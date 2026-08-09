<?php

namespace App\Livewire\FundecorDashboard;

use App\Models\CurrentDataM;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ForecastM;
use App\Models\AlertM;
use App\Models\StationM;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;
use Forecast;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FundecorDashboard extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $headers;
    protected $paginationTheme = 'tailwind';
    public $stateChange = 'all';
    public $perPage = 10;
    public $currentData = [];
    public $forecast = [];
    public $chillingHours = []; 
    public $alerts = [];  
    public $monthText= null;

    /**
     * Activa el pulso/onda tipo radar en las estaciones ACTIVAS del dashboard.
     * Opcional: ponlo en false para desactivar la animación en las activas. Las
     * estaciones no funcionales (sin datos recientes) conservan su pulso siempre.
     */
    public bool $enablePulseEffect = true;

    protected $listeners = ['delete','updateForecast'];
    
    public function mount($boardId = null){
        //dd($boardId);
        $this->getData();
    }

    private function calculateIconDate($value,$regDate) {
		$ico = '';        
        if (($value>=0)&&($value<=1)) { $ico = '1';}
		if (($value>=2)&&($value<4)) { $ico = '2';}
		if (($value>=4)&&($value<6)) { $ico = '3';}
		if (($value>=6)&&($value<8)) { $ico = '4';}
		if (($value>=8)&&($value<10)) { $ico = '5';}
		if (($value>=10)&&($value<12)) { $ico = '7';}
		if (($value>=12)&&($value<14)) { $ico = '6';}
		if (($value>=14)&&($value<16)) { $ico = '10';}
		if (($value>=16)&&($value<18)) { $ico = '8';}
		if (($value>=18)&&($value<20)) { $ico = '9';}
		if (($value>=20)&&($value<247.5)) { $ico = '11';}//granizo

        $dt = new DateTime(date($regDate));
		if( ($dt->format('H') >= 6) && ($dt->format('H') < 18) ){
			$ico = $ico."a";
		} else {
			$ico = $ico."b";
		}
		return $ico;
	}

    public function getData(){
        date_default_timezone_set('America/La_Paz'); 
        session([
            'dashboard_autorizado' => true,
            'dashboard_expira_en' => now()->setTime(23, 59, 59),
        ]);
        Carbon::setLocale('es');
        $this->currentData = [];
        $this->forecast = [];
        $this->chillingHours = [];
        $this->alerts = [];
        $stationIds = [441, 498, 326, 356];

        $this->monthText = ucfirst(Carbon::now()->translatedFormat('F'));
      
        //CurrentData
        $this->currentData = CurrentDataM::whereIn('station_id', $stationIds)
        ->join('stations as s', 'current_data.station_id', 's.id')
        ->select(
            's.id',
            'current_data.receipt_date',
            's.name',
            's.location',
            's.latitude',
            's.longitude',
            'current_data.tempout',
            'current_data.press',
            'current_data.dewptout',
            'current_data.humout',
            'current_data.raintotal',
            'current_data.windspeed',
            'current_data.winddir'
        )
        ->orderByRaw('FIELD(current_data.station_id, ' . implode(',', $stationIds) . ')')
        ->get();
        $this->currentData->each(function ($item) {

            
            $carbonDate = Carbon::parse($item->receipt_date);
            $item->dia_mes_hora_min = $carbonDate->format('d/m H:i');
            if (!is_null($item->dewptout) && !is_null($item->tempout) && !is_null($item->press)) {
                $dew = $item->dewptout;
                $temp = $item->tempout;
                $press = $item->press;
                
                $e = 6.11 * pow(10, (7.5 * $dew) / (237.7 + $dew));
                $gamma = 4098 * $e / pow(($dew + 237.7), 2);
                $denominator = (0.00066 * $press) + $gamma;
                $numerator = (0.00066 * $press * $temp) + ($gamma * $dew);
                $bulboHumedo = $numerator / $denominator;
                $item->deltat = (string) round($temp - $bulboHumedo, 3);
            } else {
                $item->deltat = null;
            }
                       
        });
        $this->currentData =$this->currentData->toArray();       
        
        
        //Forecast
        $fecha = Carbon::now()->addDays(4)->endOfDay();
        $endDate = $fecha->format('Y-m-d H:i:s');

        for ($i = 0; $i < count($stationIds); $i++) {

            $maxIconsByDate = ForecastM::where('station_id', $stationIds[$i])
                ->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
                ->where('forecasts.reg_date', '<=', $endDate)
                ->select(
                    DB::raw('DATE(forecasts.reg_date) AS reg_date'),
                    DB::raw('MAX(forecasts.icon) AS max_icon')
                )
                ->groupBy(DB::raw('DATE(forecasts.reg_date)'))
                ->get();

            $frequentIconsByDate = ForecastM::where('station_id', $stationIds[$i])
                ->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
                ->where('forecasts.reg_date', '<=', $endDate)
                ->select(
                    DB::raw('DATE(forecasts.reg_date) AS reg_date'),
                    'forecasts.icon',
                    DB::raw('COUNT(forecasts.icon) AS frequency')
                )
                ->groupBy(DB::raw('DATE(forecasts.reg_date)'), 'forecasts.icon')
                ->orderBy(DB::raw('DATE(forecasts.reg_date)'), 'asc')
                ->orderBy('frequency', 'desc')
                ->get()
                ->groupBy('reg_date')
                ->map(function ($icons) {
                    $mostFrequent = $icons->first(); 
                    return [
                        'reg_date' => $mostFrequent->reg_date,
                        'most_frequent_icon' => $mostFrequent->icon,
                        'frequency' => $mostFrequent->frequency,
                    ];
                });

            $iconForDate = $maxIconsByDate->map(function ($maxIcon) use ($frequentIconsByDate) {
                $regDate = $maxIcon->reg_date;
                $frequentIcon = $frequentIconsByDate->get($regDate);					
                
                return [
                    'reg_date' =>$regDate,
                    'icon_date' => $maxIcon->max_icon >= 6
                        ? $this->calculateIconDate($maxIcon->max_icon,$regDate)
                        : ($this->calculateIconDate($frequentIcon['most_frequent_icon'],$regDate) ?? null),
                ];
            });
        
            $result = ForecastM::where('station_id', $stationIds[$i])
                ->join('stations as s', 's.id', 'forecasts.station_id')
                ->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
                ->where('forecasts.reg_date', '<=', $endDate)
                ->orderBy('reg_date', 'asc')
                ->select(
                    's.name',
                    DB::raw('DATE(forecasts.reg_date) AS reg_date'),
                    'forecasts.v10m_max',
                    'forecasts.tmax',
                    'forecasts.tmin',
                    'forecasts.hr2m_max',
                    'forecasts.hr2m_min',
                    'forecasts.prec_total'
                )
                ->distinct()
                ->get()
                ->map(function ($item) use ($iconForDate){
                    $carbonDate = Carbon::parse($item->reg_date);
                    $icon = collect($iconForDate)->firstWhere('reg_date', $item->reg_date)['icon_date'] ?? null;

                    // Formato 01/06
                    $item->dia_mes = $carbonDate->format('d/m');

                    // Día literal
                    $hoy = Carbon::today();
                    if ($carbonDate->isSameDay($hoy)) {
                        $item->dia_literal = 'Hoy';                    
                    } else {
                        $item->dia_literal = ucfirst($carbonDate->translatedFormat('l')); // Ej: Sábado
                    }
                    $item->icon = $icon;

                    return $item;
                })
                ->toArray();

            $this->forecast[] = $result;
        }

        //dd($this->forecast);

        //ChillingHours
        $year = date('Y');
        
        // Conexión antigua (comentado)
        // if (config("database.connections.db_guest_h$year")) {
        //     $valueHF = DB::connection('db_guest_h' . $year)
        //         ->table('data')
        
        // Nueva conexión
        if (config("database.connections.senvatec_db_{$year}")) {            
            $month = date('m');
            $startDate = date('Y-m-01 00:00:00');
            $endDate = date('Y-m-t 23:59:59');
            for ($i=0; $i < count($stationIds); $i++) {
                $valueHF = DB::connection('senvatec_db_' . $year)
                    ->table('senva_data')
                    ->where('station_id',$stationIds[$i])
                    ->where('tempout', '<=', 7.2)
                    ->whereDate('receipt_date', '>=', $startDate)
                    ->whereDate('receipt_date', '<=', $endDate)
                    ->select(DB::raw('COUNT(DISTINCT CONCAT(DATE(receipt_date), HOUR(receipt_date))) AS total'))
                    ->value('total');               
                $this->chillingHours[] = $valueHF!==null? number_format($valueHF, 1):null;
            }
        }
        
        //Alerts
        for ($i=0; $i < count($stationIds); $i++) {
            $this->alerts[] = AlertM::where('station_id', $stationIds[$i])->OrderBy('reg_date','asc')->limit(5)->get()->toArray();
        }

        //dd($this->alerts);

        $stationList = StationM::join('current_data as dd', 'dd.station_id', 'stations.id')
        ->select(
            'stations.id as stationId',
            'stations.name',
            'stations.location',
            'stations.latitude',
            'stations.longitude',
            'dd.tempout',
            'dd.humout',
            'dd.windspeed',
            'dd.raintotal',
            'dd.receipt_date'
        )
        ->whereIn('stations.id', $stationIds)
        ->orderByRaw('FIELD(stations.id, ' . implode(',', $stationIds) . ')')
        ->distinct()
        ->get();
        //dd($stationList);
        $this->dispatch('getMapStation', [
            'data' => $stationList,
            'weather_type' => 'temperature',
            'enablePulseEffect' => $this->enablePulseEffect,
        ]);
        // dd($this->currentData,$this->forecast,$this->chillingHours,$this->alerts);
    }
    
    public function render(){
        return view('livewire.fundecor-dashboard.fundecor-dashboard');
    }
}
