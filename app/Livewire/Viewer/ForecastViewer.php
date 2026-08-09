<?php

namespace App\Livewire\Viewer;

use DateTime;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\StationM;
use App\Models\ForecastM;
use Illuminate\Support\Facades\DB;

class ForecastViewer extends Component
{
    // Propiedades públicas para el pronóstico
    public $stationId;
    public $tempoutF;
    public $tempout_max_dayF;
    public $tempout_min_dayF;
    public $windspeedF;
    public $humoutF;
    public $dewptoutF;
    public $windspeedhiF;
    public $raintotalF;
    public $pressF;
    public $receipt_dateF;
    public $locationF;
    public $nameF;
    public $icon_imageF;
    public $dataForecastsCurrent = [];
    public $dataForecasts = [];

    protected $listeners = ['loadForecasts'];

    public function mount($stationId = null)
    {
        // NO cargar datos automáticamente en mount
        // Solo cuando se reciba el evento loadForecasts
        $this->stationId = $stationId;
        if ($this->stationId) {
            $this->loadForecasts($this->stationId);
        }
    }

    public function loadForecasts($stationId)
    {
        // Validar que el stationId no sea nulo
        if (!$stationId) {
            return;
        }
        
        date_default_timezone_set('America/La_Paz');
        $this->stationId = $stationId;
        $this->resetForecastState();

        $stationInfo = StationM::select('id', 'name', 'location')
            ->where('id', $this->stationId)
            ->first();

        if ($stationInfo) {
            $this->nameF = $stationInfo->name;
            $this->locationF = $stationInfo->location;
        }

        $stationDataF = StationM::select(
            'stations.id as stationId', 'stations.name', 'stations.location', 'stations.latitude', 
            'stations.longitude', 'stations.state as stateStation', 'dd.tempout','dd.humout', 
            'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press',
            'dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2',
            'dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2',
            'dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2',
            'dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date',DB::raw('null as tempout_max_day'),
            DB::raw('null as tempout_min_day'),
            DB::raw('null as icon_image')
        )
        ->join('current_data as dd', 'dd.station_id', 'stations.id')
        ->where('stations.id', $this->stationId)
        ->orderBy('dd.id','desc')
        ->first();      

        if ($stationDataF){
            if ($stationDataF && Carbon::parse($stationDataF->receipt_date)->isToday()) {
            
                $fecha = null;    
                $dataAux = ForecastM::where('station_id', $this->stationId)            
                ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, reg_date, ?))', [Carbon::now()])
                ->first();

                $max = null;
                $min = null;

                if ($stationDataF) {
                    $fecha = date('Y-m-d', strtotime($stationDataF->receipt_date));   
                    $year = date('Y');
                    
                    if (config("database.connections.senvatec_db_{$year}")) {
                        
                        $tempStats = DB::connection('senvatec_db_' . $year)
                            ->table('senva_data')
                            ->where('station_id', $this->stationId)
                            ->whereDate('receipt_date', $fecha)
                            ->selectRaw('MAX(tempout) as tempout_max, MIN(tempout) as tempout_min')
                            ->first(); 
                        
                        if($dataAux && $tempStats != null){
                            $stationDataF->tempout_max_day = $tempStats->tempout_max;
                            $stationDataF->tempout_min_day = $tempStats->tempout_min;
                            $stationDataF->icon_image = $dataAux != null ? strrev($dataAux->icon_image) : null;
                            $max = $tempStats->tempout_max;
                            $min = $tempStats->tempout_min;
                        } else {
                            $stationDataF->tempout_max_day = null;
                            $stationDataF->tempout_min_day = null;
                            $stationDataF->icon_image = $dataAux != null ? strrev($dataAux->icon_image) : null;
                        }
                    } else {
                        $stationDataF->tempout_max_day = null;
                        $stationDataF->tempout_min_day = null;
                        $stationDataF->icon_image = $dataAux != null ? strrev($dataAux->icon_image) : null;
                    }

                    if($dataAux){
                        if($dataAux->tmax && $dataAux->tmin){
                            $stationDataF->tempout_max_day = $dataAux->tmax > $max ? $dataAux->tmax : $max;
                            $stationDataF->tempout_min_day = $dataAux->tmin < $min ? $dataAux->tmin : $min;
                        }
                    }
                }

                $dateStation = new DateTime(date($fecha));
                $now = new DateTime();
                $forecastDataF = null;
                
                if (($dateStation->diff($now)->h > 3) && ($dateStation->diff($now)->d >= 1)) {        
                    
                    $stationDataF = ForecastM::where('station_id', $this->stationId)
                        ->select(
                            'id', 
                            'reg_date as receipt_date', 
                            'v10m as windspeed', 
                            'v10m_dir as winddir', 
                            'prec as raintotal', 
                            't2m as tempout', 
                            'hr2m as humout', 
                            'baro as press', 
                            'dp as dewptout',
                            'tmax as tempout_max_day',
                            'tmin as tempout_min_day',
                            'icon_image'
                        )
                        ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, reg_date, ?))', [Carbon::now()])
                        ->first();

                    if($stationDataF){

                        $stationDataF->icon_image = strrev($stationDataF->icon_image);
                    
                        if($max !== null && $stationDataF->tempout_max_day !== null){
                            $stationDataF->tempout_max_day = $stationDataF->tempout_max_day > $max ? $stationDataF->tempout_max_day : $max;
                        }
                        if($max !== null && $stationDataF->tempout_min_day !== null){
                            $stationDataF->tempout_min_day = $stationDataF->tempout_min_day < $min ? $stationDataF->tempout_min_day : $min;
                        }
                    }
                }

                if($stationDataF){
                    $this->tempoutF = $stationDataF->tempout;
                    $this->tempout_max_dayF = $stationDataF->tempout_max_day;
                    $this->tempout_min_dayF = $stationDataF->tempout_min_day;
                    $this->windspeedF = $stationDataF->windspeed;
                    $this->humoutF = $stationDataF->humout;
                    $this->dewptoutF = $stationDataF->dewptout;
                    $this->windspeedhiF = $stationDataF->windspeedhi ?? null;
                    $this->raintotalF = $stationDataF->raintotal;
                    $this->pressF = $stationDataF->press;
                    $this->receipt_dateF = $stationDataF->receipt_date;
                    $this->locationF = $this->locationF ?? ($stationDataF->location ?? null);
                    $this->nameF = $this->nameF ?? ($stationDataF->name ?? null);
                    $this->icon_imageF = $stationDataF->icon_image;
                }

        
            } else {
                $stationDataF = ForecastM::where('station_id', $this->stationId)
                ->select(
                    'id', 
                    'reg_date as receipt_date', 
                    'v10m as windspeed', 
                    'v10m_dir as winddir', 
                    'prec as raintotal', 
                    't2m as tempout', 
                    'hr2m as humout', 
                    'baro as press', 
                    'dp as dewptout',
                    'tmax as tempout_max_day',
                    'tmin as tempout_min_day',
                    'icon_image'
                )
                ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, reg_date, ?))', [Carbon::now()])
                ->first();

                if($stationDataF){
                    $stationDataF->icon_image = strrev($stationDataF->icon_image);
               
                    $this->tempoutF = $stationDataF->tempout;
                    $this->tempout_max_dayF = $stationDataF->tempout_max_day;
                    $this->tempout_min_dayF = $stationDataF->tempout_min_day;
                    $this->windspeedF = $stationDataF->windspeed;
                    $this->humoutF = $stationDataF->humout;
                    $this->dewptoutF = $stationDataF->dewptout;
                    $this->windspeedhiF = $stationDataF->windspeedhi ?? null;
                    $this->raintotalF = $stationDataF->raintotal;
                    $this->pressF = $stationDataF->press;
                    $this->receipt_dateF = $stationDataF->receipt_date;
                    $this->locationF = $this->locationF ?? null;
                    $this->nameF = $this->nameF ?? null;
                    $this->icon_imageF = $stationDataF->icon_image;
                }
            }
        } else {
            $stationDataF = ForecastM::where('station_id', $this->stationId)
                ->select(
                    'id', 
                    'reg_date as receipt_date', 
                    'v10m as windspeed', 
                    'v10m_dir as winddir', 
                    'prec as raintotal', 
                    't2m as tempout', 
                    'hr2m as humout', 
                    'baro as press', 
                    'dp as dewptout',
                    'tmax as tempout_max_day',
                    'tmin as tempout_min_day',
                    'icon_image'
                )
                ->orderByDesc('reg_date')
                ->first();

            if ($stationDataF) {
                $stationDataF->icon_image = strrev($stationDataF->icon_image);

                $this->tempoutF = $stationDataF->tempout;
                $this->tempout_max_dayF = $stationDataF->tempout_max_day;
                $this->tempout_min_dayF = $stationDataF->tempout_min_day;
                $this->windspeedF = $stationDataF->windspeed;
                $this->humoutF = $stationDataF->humout;
                $this->dewptoutF = $stationDataF->dewptout;
                $this->windspeedhiF = null;
                $this->raintotalF = $stationDataF->raintotal;
                $this->pressF = $stationDataF->press;
                $this->receipt_dateF = $stationDataF->receipt_date;
                $this->icon_imageF = $stationDataF->icon_image;
            } else {
                $stationDataF = null;
            }
        }

        // Obtener pronósticos del día actual
        $this->dataForecastsCurrent = $this->groupForecastsByDay(
            ForecastM::where('station_id', $this->stationId)
                ->select('id', 'reg_date', 'v10m', 'v10m_dir', 'v10m_max', 'v850', 'v850_dir', 'prec', 'cape', 'li', 'dam', 'a850', 'a500', 't2m', 'hr2m', 'hr2m_max', 'hr2m_min', 't850', 't500', 'baro', 'cloud', 'snow', 'dp', 'tmax', 'tmin', 'sh_ts', 'icon', 'icon_image', 'min_t2m', 'min_dp', 'state')
                ->whereBetween('reg_date', [Carbon::now(), Carbon::now()->endOfDay()])
                ->get()
        );

        // Obtener todos los pronósticos
        $this->dataForecasts = $this->groupForecastsByDay(
            ForecastM::where('station_id', $this->stationId)
                ->select('id', 'reg_date', 'v10m', 'v10m_dir', 'v10m_max', 'v850', 'v850_dir', 'prec', 'cape', 'li', 'dam', 'a850', 'a500', 't2m', 'hr2m', 'hr2m_max', 'hr2m_min', 't850', 't500', 'baro', 'cloud', 'snow', 'dp', 'tmax', 'tmin', 'sh_ts', 'icon', 'icon_image', 'min_t2m', 'min_dp', 'state')
                ->get()
        );
    }

    private function resetForecastState(): void
    {
        $this->tempoutF = null;
        $this->tempout_max_dayF = null;
        $this->tempout_min_dayF = null;
        $this->windspeedF = null;
        $this->humoutF = null;
        $this->dewptoutF = null;
        $this->windspeedhiF = null;
        $this->raintotalF = null;
        $this->pressF = null;
        $this->receipt_dateF = null;
        $this->icon_imageF = null;
        $this->dataForecastsCurrent = [];
        $this->dataForecasts = [];
    }

    private function groupForecastsByDay($forecastCollection): array
    {
        return $forecastCollection
            ->groupBy(function ($item) {
                return Carbon::parse($item->reg_date)->format('d-m');
            })
            ->map(function ($itemsByDate, $date) {
                return [
                    'date' => $date,
                    'records' => $itemsByDate->map(function ($item) {
                        return $this->mapForecastRecord($item);
                    })->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function mapForecastRecord($item): array
    {
        return [
            'id' => $item->id,
            'reg_date' => $item->reg_date,
            'formatted_date' => Carbon::parse($item->reg_date)->format('d/m'),
            'formatted_time' => Carbon::parse($item->reg_date)->format('H:i'),
            'period' => $this->getPeriod($item->reg_date),
            'periodRange' => $this->getPeriodRange($item->reg_date),
            'percentage' => $this->pPrec($item->prec) . ' ' . $this->pPrecTXT($item->prec),
            'icon_image' => $item->icon_image ? strrev($item->icon_image) : null,
            't2m' => $item->t2m,
            'hr2m' => $item->hr2m,
            'prec' => $item->prec,
            'v10m' => $item->v10m,
            'v10m_dir' => $item->v10m_dir,
            'v10m_max' => $item->v10m_max,
            'baro' => $item->baro,
            'cloud' => $item->cloud,
            'dp' => $item->dp,
            'tmax' => $item->tmax,
            'tmin' => $item->tmin,
        ];
    }

    /**
     * Calcula el porcentaje de precipitación
     */
    public function pPrec($prec) 
    {
        $cadena = "";
        if (($prec <= 0.55)) { $cadena = $cadena . '0%';}
        if (($prec > 0.55) && ($prec <= 1.5)) { $cadena = $cadena . '70%';}
        if (($prec > 1.5) && ($prec <= 3.1)) { $cadena = $cadena . '80%';}
        if (($prec > 3.1) && ($prec <= 5.1)) { $cadena = $cadena . '90%';}
        if (($prec > 5.1) && ($prec <= 8.1)) { $cadena = $cadena . '95%';}
        if (($prec > 8.1)) { $cadena = $cadena . '99%';}
        return $cadena;
    }
    
    /**
     * Obtiene el texto descriptivo de precipitación
     */
    public function pPrecTXT($prec) 
    {
        if (($prec <= 0.55)) {
            return __('messages.viewer.forecast.precipitation_text.none');
        }
        if (($prec > 0.55) && ($prec <= 1.5)) {
            return __('messages.viewer.forecast.precipitation_text.drizzle');
        }
        if (($prec > 1.5) && ($prec <= 3.1)) {
            return __('messages.viewer.forecast.precipitation_text.light');
        }
        if (($prec > 3.1) && ($prec <= 5.1)) {
            return __('messages.viewer.forecast.precipitation_text.moderate');
        }
        if (($prec > 5.1) && ($prec <= 8.1)) {
            return __('messages.viewer.forecast.precipitation_text.heavy');
        }
        if (($prec > 8.1)) {
            return __('messages.viewer.forecast.precipitation_text.torrential');
        }

        return '';
    }

    /**
     * Obtiene el rango de período del día
     */
    public function getPeriodRange($dateTime)
    {
        date_default_timezone_set('America/La_Paz');

        $time = Carbon::parse($dateTime)->format('H');
        if ($time >= 0 && $time <= 2) {
            return '00:00 - 02:00';
        } elseif ($time >= 3 && $time <= 5) {
            return '03:00 - 05:00';
        } elseif ($time >= 6 && $time <= 8) {
            return '06:00 - 08:00';
        } elseif ($time >= 9 && $time <= 11) {
            return '09:00 - 11:00';
        } elseif ($time >= 12 && $time <= 14) {
            return '12:00 - 14:00';
        } elseif ($time >= 15 && $time <= 17) {
            return '15:00 - 17:00';
        } elseif ($time >= 18 && $time <= 20) {
            return '18:00 - 20:00';
        } elseif ($time >= 21 && $time <= 23) {
            return '21:00 - 23:00';
        } else {
            return '';
        }
    }

    /**
     * Obtiene el período del día
     */
    public function getPeriod($dateTime)
    {
        date_default_timezone_set('America/La_Paz');
        $time = Carbon::parse($dateTime)->format('H');

        if ($time >= 0 && $time < 6) {
            return __('messages.viewer.forecast.periods.dawn');
        } elseif ($time >= 6 && $time <= 11) {
            return __('messages.viewer.forecast.periods.morning');
        } elseif ($time >= 12 && $time < 13) {
            return __('messages.viewer.forecast.periods.noon');
        } elseif ($time >= 13 && $time <= 18) {
            return __('messages.viewer.forecast.periods.afternoon');
        } elseif ($time >= 19 && $time <= 23) {
            return __('messages.viewer.forecast.periods.night');
        }

        return '';
    }

    public function render()
    {
        return view('livewire.viewer.forecast-viewer');
    }
}

