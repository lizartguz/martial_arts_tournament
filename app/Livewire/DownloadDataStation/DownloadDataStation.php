<?php

namespace App\Livewire\DownloadDataStation;

use Carbon\Carbon;
use App\Utils\Utils;
use Livewire\Component;
use App\Models\StationM;
use App\Models\UserDependencyM;
use App\Exports\DataStationExport;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDownloadColumnPreferenceM;
use App\Services\DownloadDataStationPdfService;

class DownloadDataStation extends Component
{
    use AuthValidationTrait;

    public $monthList = [];
    public $stationList = [];
    public $formatList = [];
    public $selectedStationId = null;
    public $selectedFormat = 'daily';
    public $selectedFromDate;
    public $selectedToDate;
    public $selectedYear;
    public $selectedMonth;
    public $lastDayMonth;

    public $maxDate;
    public $minDate;
    public $maxYear;
    public $minYear;

    public $hasDataView; //para controlar si existe o no registros de visualizacion
    public $hasDataDownload; //para controlar si existe o no registros de descarga
    public $showResultsView; //para mostrar el resultado de Mostrar
    public $showResultsDownload; //para mostrar el resultado de Mostrar
    public $data = [];
    public $heads = [];
    public $headsDaily;
    public $headsMonthly = [];
    public $typeS = null;
    public bool $freezeDateColumn = true;
    public array $availableColumnOptions = [];
    public array $selectedColumns = [];
    public bool $showColumnsModal = false;

    protected $listeners = ['showData', 'downloadData', 'downloadPdfData', 'openColumnsModal'];

    /**
     * Inicializa filtros, columnas disponibles y estaciones permitidas.
     */
    public function mount($typeS = null) {
        $this->typeS = $typeS;
        $user = Auth::user();
        $this->validarUsuario($user, []);
        abort_unless($user?->can('stations.downloads.view'), 403);

        date_default_timezone_set('America/La_Paz');
        set_time_limit(2800);  //ampliamos el limite de espera

        $measureSystem = session('measure', 'metric');
        
        if($this->typeS == "qair"){
            $this->headsDaily = [
                'receipt_date' => [
                    'label' => __('messages.downloads.data_daily_columns.date'),
                    'unit' => '',
                ],
                'tempout' => [
                    'label' => __('messages.downloads.data_daily_columns.tempout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'humout' => [
                    'label' => __('messages.downloads.data_daily_columns.humout'),
                    'unit'=> '(%)',
                ],
                'pm10' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm10'),
                    'unit' => '(µg/m³)',
                ],
                'pm2_5' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm2_5'),
                    'unit' => '(µg/m³)',
                ],
                'pm1' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm1'),
                    'unit' => '(µg/m³)',
                ],
            ];

            $this->headsMonthly = [
                'receipt_date' => [
                    'label' => __('messages.downloads.data_monthly_columns.date'),
                    'unit' => '',
                ],
                'tempout' => [
                    'label' => __('messages.downloads.data_monthly_columns.tempout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'humout' => [
                    'label' => __('messages.downloads.data_monthly_columns.humout'),
                    'unit'=> '(%)',
                ],
                'pm10' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm10'),
                    'unit' => '(µg/m³)',
                ],
                'pm2_5' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm2_5'),
                    'unit' => '(µg/m³)',
                ],
                'pm1' => [
                    'label' => __('messages.downloads.data_monthly_columns.pm1'),
                    'unit' => '(µg/m³)',
                ],
                
            ];

        }else{
            $this->headsDaily = [
                'receipt_date' => [
                    'label' => __('messages.downloads.data_daily_columns.date'),
                    'unit' => '',
                ],
                'tempout' => [
                    'label' => __('messages.downloads.data_daily_columns.tempout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'tempoutmax' => [
                    'label' => __('messages.downloads.data_daily_columns.tempoutmax'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'tempoutmin' => [
                    'label' => __('messages.downloads.data_daily_columns.tempoutmin'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'dewptout' => [
                    'label' => __('messages.downloads.data_daily_columns.dewptout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'humout' => [
                    'label' => __('messages.downloads.data_daily_columns.humout'),
                    'unit'=> '(%)',
                ],
                'winddir' => [
                    'label' => __('messages.downloads.data_daily_columns.winddir'),
                    'unit' => '(0º N)',
                ],
                'winddirstr' => [
                    'label' => __('messages.downloads.data_daily_columns.winddirstr'),
                    'unit' => '',
                ],
                'windspeed' => [
                    'label' => __('messages.downloads.data_daily_columns.windspeed'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'windspeedhi' => [
                    'label' => __('messages.downloads.data_daily_columns.windspeedhi'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'winddir2' => [
                    'label' => __('messages.downloads.data_daily_columns.winddir2'),
                    'unit' => '(0º N)',
                ],
                'winddirstr2' => [
                    'label' => __('messages.downloads.data_daily_columns.winddirstr2'),
                    'unit' => '',
                ],
                'windspeed2' => [
                    'label' => __('messages.downloads.data_daily_columns.windspeed2'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'windspeedhi2' => [
                    'label' => __('messages.downloads.data_daily_columns.windspeedhi2'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'raintotal' => [
                    'label' => __('messages.downloads.data_daily_columns.raintotal'),
                    'unit' => '('.Utils::getLengthUnit($measureSystem).')',
                ],
                'rainrate' => [
                    'label' =>  __('messages.downloads.data_daily_columns.rainrate'),
                    'unit' => '('.Utils::getRainfallUnit($measureSystem).')',
                ],
                'press' => [
                    'label' => __('messages.downloads.data_daily_columns.pressure'),
                    'unit' => '('.Utils::getPressUnit($measureSystem).')',
                ],
                'wetbulb' => [
                    'label' => __('messages.downloads.data_daily_columns.wetbulb'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'heatindexout' => [
                    'label' => __('messages.downloads.data_daily_columns.heatindex'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'uvindex' => [
                    'label' => __('messages.downloads.data_daily_columns.uvindex'),
                    'unit' => '',
                ],
                'solrad' => [
                    'label' => __('messages.downloads.data_daily_columns.solrad'),
                    'unit' => '('.Utils::getPowerUnit($measureSystem).')',
                ],
                'solevo' => [
                    'label' => __('messages.downloads.data_daily_columns.solevo'),
                    'unit' => '('.Utils::getLengthUnit($measureSystem).')',
                ],
                'soiltemp1' => [
                    'label' => __('messages.downloads.data_daily_columns.soiltemp'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'soilhum1' => [
                    'label' => __('messages.downloads.data_daily_columns.soilhum'),
                    'unit' => '(cbar)',
                ],
                'leaftemp1' => [
                    'label' => __('messages.downloads.data_daily_columns.leaftemp'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'leafhum1' => [
                    'label' => __('messages.downloads.data_daily_columns.leafhum'),
                    'unit' => '(wet)',
                ],
            ];

            $this->headsMonthly = [
                'receipt_date' => [
                    'label' => __('messages.downloads.data_monthly_columns.date'),
                    'unit' => '',
                ],
                'tempout' => [
                    'label' => __('messages.downloads.data_monthly_columns.tempout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'tempoutmax' => [
                    'label' => __('messages.downloads.data_monthly_columns.tempoutmax'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'tempoutmin' => [
                    'label' => __('messages.downloads.data_monthly_columns.tempoutmin'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'dewptout' => [
                    'label' => __('messages.downloads.data_monthly_columns.dewptout'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'humout' => [
                    'label' => __('messages.downloads.data_monthly_columns.humout'),
                    'unit'=> '(%)',
                ],
                'winddirstr' => [
                    'label' => __('messages.downloads.data_monthly_columns.winddirstr'),
                    'unit' => '',
                ],
                'windspeed' => [
                    'label' => __('messages.downloads.data_monthly_columns.windspeed'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'windspeedhi' => [
                    'label' => __('messages.downloads.data_monthly_columns.windspeedhi'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'winddirstr2' => [
                    'label' => __('messages.downloads.data_monthly_columns.winddirstr2'),
                    'unit' => '',
                ],
                'windspeed2' => [
                    'label' => __('messages.downloads.data_monthly_columns.windspeed2'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'windspeedhi2' => [
                    'label' => __('messages.downloads.data_monthly_columns.windspeedhi2'),
                    'unit' => '('.Utils::getSpeedUnit($measureSystem).')',
                ],
                'raintotal' => [
                    'label' => __('messages.downloads.data_monthly_columns.raintotal'),
                    'unit' => '('.Utils::getLengthUnit($measureSystem).')',
                ],
                'press' => [
                    'label' => __('messages.downloads.data_monthly_columns.pressure'),
                    'unit' => '('.Utils::getPressUnit($measureSystem).')',
                ],
                'wetbulb' => [
                    'label' => __('messages.downloads.data_monthly_columns.wetbulb'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'heatindexout' => [
                    'label' => __('messages.downloads.data_monthly_columns.heatindex'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'uvindex' => [
                    'label' => __('messages.downloads.data_monthly_columns.uvindex'),
                    'unit' => '',
                ],
                'solrad' => [
                    'label' => __('messages.downloads.data_monthly_columns.solrad'),
                    'unit' => '('.Utils::getPowerUnit($measureSystem).')',
                ],
                'solevo' => [
                    'label' => __('messages.downloads.data_monthly_columns.solevo'),
                    'unit' => '('.Utils::getLengthUnit($measureSystem).')',
                ],
                'soiltemp1' => [
                    'label' => __('messages.downloads.data_monthly_columns.soiltemp'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'soilhum1' => [
                    'label' => __('messages.downloads.data_monthly_columns.soilhum'),
                    'unit' => '(cbar)',
                ],
                'leaftemp1' => [
                    'label' => __('messages.downloads.data_monthly_columns.leaftemp'),
                    'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
                ],
                'leafhum1' => [
                    'label' => __('messages.downloads.data_monthly_columns.leafhum'),
                    'unit' => '(wet)',
                ],
            ];
        }

        if ($this->typeS !== 'qair') {
            $this->loadColumnPreferences($user);
        }

        $this->minDate = Carbon::create('2011-01-01');
        $this->maxDate = Carbon::now();
        $today = $this->maxDate->format('Y-m-d');
        $this->minYear = $this->minDate->format('Y');
        $this->maxYear = $this->maxDate->format('Y');

//        $this->selectedStationId = 22;
//        $this->selectedFromDate = '2024-01-01';
//        $this->selectedToDate = '2024-01-10';
        $this->selectedFromDate = $today;
        $this->selectedToDate = $today;
        $this->selectedYear = $this->maxDate->format('Y');
        $this->selectedMonth = $this->maxDate->format('m');

        $this->data = [];
        $this->heads = [''];
        $this->hasDataView = false;
        $this->hasDataDownload = false;
        $this->showResultsView = false;
        $this->showResultsDownload = false;

        $this->monthList = [
            1 => __('messages.months.january'),
            2 => __('messages.months.february'),
            3 => __('messages.months.march'),
            4 => __('messages.months.april'),
            5 => __('messages.months.may'),
            6 => __('messages.months.june'),
            7 => __('messages.months.july'),
            8 => __('messages.months.august'),
            9 => __('messages.months.september'),
            10 => __('messages.months.october'),
            11 => __('messages.months.november'),
            12 => __('messages.months.december'),
        ];

        if($this->typeS=="qair"){
            $this->formatList = [
                'daily' => __('messages.downloads.format_daily_label'),                
            ];
        }else{
            $this->formatList = [
                'daily' => __('messages.downloads.format_daily_label'),
                'monthly' => __('messages.downloads.format_monthly_label'),
                'annual' => __('messages.downloads.format_annual_label'),
            ];
        }


        
    }

    /**
     * Renderiza la vista principal de descargas.
     */
    public function render()
    {
        return view('livewire.download-data-station.download-data-station');
    }

    /**
     * Limpia los datos visibles antes de una nueva consulta.
     */
    public function clearDataView() {
        $this->data = [];
    }

    /**
     * Consulta los datos y aplica las columnas seleccionadas por el usuario.
     */
    public function showData($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null) {
        $this->clearDataView();
        $this->hasDataView = false;
        $this->selectedStationId = $stationId;
        $this->selectedFormat = $format;
        $this->heads = $this->getVisibleHeads($this->selectedFormat == 'daily' ? $this->headsDaily : $this->headsMonthly);
        if ($fromDate != null) {
            $this->selectedFromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->selectedToDate = $toDate;
        }
        if ($year != null) {
            $this->selectedYear = $year;
        }
        if ($month != null) {
            $this->selectedMonth = $month;
        }
        $this->lastDayMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->day;

        if ($this->selectedFormat == 'daily') { //todos los registros
            $this->data = $this->getDailyData(200);
        }
        if ($this->selectedFormat == 'monthly') {
            $this->data = $this->getMonthlyData(200);
        }
        if ($this->selectedFormat == 'annual') {
            $this->data = $this->getAnnualData(200);
        }
        $this->data = $this->filterDataRowsByHeads($this->data, $this->heads);
        $this->hasDataView = count($this->data) > 0;
        $this->showResultsView = true;
        $this->dispatch('showResults');
    }

    /**
     * Obtiene registros diarios para mostrar en pantalla.
     */
    public function getDailyData($limit = 0) {
        $fromDate = Carbon::parse($this->selectedFromDate . '00:00:00');
        $toDate = Carbon::parse($this->selectedToDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        $arrayData = [];

        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return $arrayData;
        // }
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return $arrayData;
        }

        $measureSystem = session('measure', 'metric');
        
        if($this->typeS=="qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h' . $year)
            //     ->table('data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_' . $year)
                ->table('senva_data')
                ->select('receipt_date', 'tempout','humout','pm10','pm2_5','pm1')
                ->where('station_id', $this->selectedStationId)
                ->where('receipt_date', '>=', $fromDate)
                ->where('receipt_date', '<=', $toDate)
                ->orderBy('receipt_date', 'asc');
            if ($limit > 0) {
                $query->limit($limit);
            };
            foreach ($query->cursor() as $item) {
                $arrayData[] = [
                    'receipt_date' => Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
                    'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',                   
                    'humout' => $item->humout ? round($item->humout, 0) : '',                   
                    'pm1' => $item->pm1 ? round($item->pm1, 1) : '',
                    'pm2_5' => $item->pm2_5 ? round($item->pm2_5, 1) : '',
                    'pm10' => $item->pm10 ? round($item->pm10, 1) : '',
                ];
            };
        }else{
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h' . $year)
            //     ->table('data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_' . $year)
                ->table('senva_data')
                ->select('receipt_date', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout', 'humout',
                    'winddir', 'winddirstr', 'windspeed', 'windspeedhi',
                    'winddir2', 'windspeed2', 'windspeedhi2',
                    'raintotal', 'rainrate', 'press', 'heatindexout',
                    'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1','pm10','pm2_5','pm1')
                ->where('station_id', $this->selectedStationId)
                ->where('receipt_date', '>=', $fromDate)
                ->where('receipt_date', '<=', $toDate)
                ->orderBy('receipt_date', 'asc');
            if ($limit > 0) {
                $query->limit($limit);
            };
            foreach ($query->cursor() as $item) {
                $bulbo = null;
                try{
                    $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $measureSystem), 1)))));
                    $resultado = (((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2));
                
                $bulbo = round($resultado,2);
                } catch (\Exception $e) {}
                    

                $arrayData[] = [
                    'receipt_date' => Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
                    'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',
                    'tempoutmax' => $item->tempoutmax ? round(Utils::convertTemp($item->tempoutmax, $measureSystem), 1) : '',
                    'tempoutmin' => $item->tempoutmin ? round(Utils::convertTemp($item->tempoutmin, $measureSystem), 1) : '',
                    'dewptout' => $item->dewptout ? round(Utils::convertTemp($item->dewptout, $measureSystem), 1) : '',
                    'humout' => $item->humout ? round($item->humout, 0) : '',
                    'winddir' => $item->winddir ? round($item->winddir, 1) : '',
                    'winddirstr' => Utils::windDirToStr($item->winddir, $item->windspeed),
                    'windspeed' => $item->windspeed ? round(Utils::convertSpeed($item->windspeed, $measureSystem), 0) : '',
                    'windspeedhi' => $item->windspeedhi ? round(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1) : '',
                    'winddir2' => $item->winddir2 ? round($item->winddir2, 1) : '',
                    'winddirstr2' => Utils::windDirToStr($item->winddir2, $item->windspeed2),
                    'windspeed2' => $item->windspeed2 ? round(Utils::convertSpeed($item->windspeed2, $measureSystem), 0) : '',
                    'windspeedhi2' => $item->windspeedhi2 ? round(Utils::convertSpeed($item->windspeedhi2, $measureSystem), 1) : '',
                    'raintotal' => $item->raintotal ? round(Utils::convertLength($item->raintotal, $measureSystem), 2) : '',
                    'rainrate' => $item->rainrate ? round(Utils::convertRainfall($item->rainrate, $measureSystem), 2) : '',
                    'press' => $item->press ? round(Utils::convertPress($item->press, $measureSystem), 1) : '',
                    'wetbulb' => $bulbo!=null?(string)$bulbo:'',
                    'heatindexout' => $item->heatindexout ? round(Utils::convertTemp($item->heatindexout, $measureSystem), 1) : '',
                    'uvindex' => $item->uvindex ? round($item->uvindex, 1) : '',
                    'solrad' => $item->solrad ? round(Utils::convertPower($item->solrad, $measureSystem), 2) : '',
                    'solevo' => $item->solevo ? round(Utils::convertLength($item->solevo, $measureSystem), 3) : '',
                    'soiltemp1' => $item->soiltemp1 ? round(Utils::convertLength($item->soiltemp1, $measureSystem), 1) : '',
                    'soilhum1' => $item->soilhum1 ? round($item->soilhum1, 1) : '',
                    'leaftemp1' => $item->leaftemp1 ? round(Utils::convertLength($item->leaftemp1, $measureSystem), 1) : '',
                    'leafhum1' => $item->leafhum1 ? round($item->leafhum1, 1) : '',
                ];
            };
        }
        return $arrayData;
    }

    /**
     * Obtiene registros mensuales para mostrar en pantalla.
     */
    public function getMonthlyData($limit = 0) {
        $dataTemp = [];
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$this->selectedYear}")) {
        //     return $dataTemp;
        // }
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$this->selectedYear}")) {
            return $dataTemp;
        }

        $measureSystem = session('measure', 'metric');

        if($this->typeS=="qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->selectedYear)
            // ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->selectedYear)
            ->table('senva_daily_data')
            ->select('f_day', 'tempout', 'humout','pm1', 'pm2_5', 'pm10',)
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->selectedYear)
            ->where('f_month', $this->selectedMonth)
            ->orderBy('f_day', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }
        $dataTemp = collect($query->get()
            ->map(function ($item) use($measureSystem) {
                return [
                    'receipt_date' => $item->f_day,
                    'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',
                    'humout' => $item->humout ? round($item->humout, 1) : '',
                    'pm1' => $item->pm1 ? round($item->pm1, 1) : '',
                    'pm2_5' => $item->pm2_5 ? round($item->pm2_5, 1) : '',
                    'pm10' => $item->pm10 ? round($item->pm10, 1) : '',
                ];
            })
            ->toArray())->keyBy('receipt_date');
        }else{
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->selectedYear)
            // ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->selectedYear)
            ->table('senva_daily_data')
            ->select('f_day', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout', 'humout',
                'winddirstr', 'windspeed', 'windspeedhi',
                'winddirstr2', 'windspeed2', 'windspeedhi2',
                'raintotal', 'press', 'heatindexout',
                'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1')
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->selectedYear)
            ->where('f_month', $this->selectedMonth)
            ->orderBy('f_day', 'asc');
            if ($limit > 0) {
                $query->limit($limit);
            }
            $dataTemp = collect($query->get()
            ->map(function ($item) use($measureSystem) {

                $bulbo = null;
                try{
                    $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $measureSystem), 1)))));
                    $resultado = (((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2));
                
                $bulbo = round($resultado,2);
                } catch (\Exception $e) {}

                return [
                    'receipt_date' => $item->f_day,
                    'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',
                    'tempoutmax' => $item->tempoutmax ? round(Utils::convertTemp($item->tempoutmax, $measureSystem), 1) : '',
                    'tempoutmin' => $item->tempoutmin ? round(Utils::convertTemp($item->tempoutmin, $measureSystem), 1) : '',
                    'dewptout' => $item->dewptout ? round(Utils::convertTemp($item->dewptout, $measureSystem), 1) : '',
                    'humout' => $item->humout ? round($item->humout, 1) : '',
                    'winddirstr' => $item->winddirstr,
                    'windspeed' => $item->windspeed ? round(Utils::convertSpeed($item->windspeed, $measureSystem), 0) : '',
                    'windspeedhi' => $item->windspeedhi ? round(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1) : '',
                    'winddirstr2' => $item->winddirstr2,
                    'windspeed2' => $item->windspeed2 ? round(Utils::convertSpeed($item->windspeed2, $measureSystem), 0) : '',
                    'windspeedhi2' => $item->windspeedhi2 ? round(Utils::convertSpeed($item->windspeedhi2, $measureSystem), 1) : '',
                    'raintotal' => $item->raintotal ? round(Utils::convertLength($item->raintotal, $measureSystem), 2) : '',
                    'press' => $item->press ? round(Utils::convertPress($item->press, $measureSystem), 1) : '',
                    'wetbulb' => $bulbo!=null?(string)$bulbo:'',
                    'heatindexout' => $item->heatindexout ? round(Utils::convertTemp($item->heatindexout, $measureSystem), 1) : '',
                    'uvindex' => $item->uvindex ? round($item->uvindex, 1) : '',
                    'solrad' => $item->solrad ? round(Utils::convertPower($item->solrad, $measureSystem), 2) : '',
                    'solevo' => $item->solevo ? round(Utils::convertLength($item->solevo, $measureSystem), 3) : '',
                    'soiltemp1' => $item->soiltemp1 ? round(Utils::convertLength($item->soiltemp1, $measureSystem), 1) : '',
                    'soilhum1' => $item->soilhum1 ? round($item->soilhum1, 1) : '',
                    'leaftemp1' => $item->leaftemp1 ? round(Utils::convertLength($item->leaftemp1, $measureSystem), 1) : '',
                    'leafhum1' => $item->leafhum1 ? round($item->leafhum1, 1) : '',
                ];
            })
            ->toArray())->keyBy('receipt_date');
        }
        
    
        return $dataTemp;
    }

    /**
     * Obtiene registros anuales para mostrar en pantalla.
     */
    public function getAnnualData($limit = 0) {
        $dataTemp = [];
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h$this->selectedYear")) {
        //     return $dataTemp;
        // }
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$this->selectedYear}")) {
            return $dataTemp;
        }

        $measureSystem = session('measure', 'metric');

        if($this->typeS=="qair"){
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->selectedYear)
            // ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->selectedYear)
            ->table('senva_daily_data')
            ->selectRaw('
                f_month,
                avg(tempout) as tempout,
                avg(humout) as humout,
                avg(pm1) as pm1,
                avg(pm2_5) as pm2_5,
                avg(pm10) as pm10
            ')
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->selectedYear)
            ->groupBy('f_month')
            ->orderBy('f_month', 'asc');
            if ($limit > 0) {
                $query->limit($limit);
            }
            $dataTemp = collect($query->get()
                ->map(function ($item) use($measureSystem) {
                    return [
                        'receipt_date' => $this->monthList[$item->f_month],
                        'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 3) : '',                       
                        'humout' => $item->humout ? round($item->humout, 3) : '',                       
                        'pm1' => $item->pm1 ? round($item->pm1, 1) : '',
                        'pm2_5' => $item->pm2_5 ? round($item->pm2_5, 1) : '',
                        'pm10' => $item->pm10 ? round($item->pm10, 1) : '',
                    ];
                })
                ->toArray())->keyBy('receipt_date');
        }else{
            // Conexión antigua (comentado)
            // $query = DB::connection('db_guest_h'.$this->selectedYear)
            // ->table('daily_data')
            
            // Nueva conexión
            $query = DB::connection('senvatec_db_'.$this->selectedYear)
            ->table('senva_daily_data')
            ->selectRaw('
                f_month,
                avg(tempout) as tempout,
                max(tempoutmax) as tempoutmax,
                min(tempoutmin) as tempoutmin,
                avg(dewptout) as dewptout,
                avg(humout) as humout,
                max(winddirstr) as winddirstr,
                avg(windspeed) as windspeed,
                max(windspeedhi) as windspeedhi,
                max(winddirstr2) as winddirstr2,
                avg(windspeed2) as windspeed2,
                max(windspeedhi2) as windspeedhi2,
                sum(raintotal) as raintotal,
                avg(press) as press,
                avg(heatindexout) as heatindexout,
                avg(uvindex) as uvindex,
                avg(solrad) as solrad,
                sum(solevo) as solevo,
                avg(soiltemp1) as soiltemp1,
                avg(soilhum1) as soilhum1,
                avg(leaftemp1) as leaftemp1,
                avg(leafhum1) as leafhum1
            ')
            ->where('station_id', $this->selectedStationId)
            ->where('f_year', $this->selectedYear)
            ->groupBy('f_month')
            ->orderBy('f_month', 'asc');
            if ($limit > 0) {
                $query->limit($limit);
            }
            $dataTemp = collect($query->get()
                ->map(function ($item) use($measureSystem) {
                    $bulbo = null;
                    try{
                        $e= (6.11 * pow(10,(7.5 *round(Utils::convertTemp($item->dewptout, $measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $measureSystem), 1)))));
                        $resultado = (((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1)) *  round(Utils::convertTemp($item->tempout, $measureSystem), 1)) + ((4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2) * round(Utils::convertTemp($item->dewptout, $measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1) ) + (4098 *  $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7) , 2));
                    
                    $bulbo = round($resultado,2);
                    } catch (\Exception $e) {}
                    return [
                        'receipt_date' => $this->monthList[$item->f_month],
                        'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 3) : '',
                        'tempoutmax' => $item->tempoutmax ? round(Utils::convertTemp($item->tempoutmax, $measureSystem), 3) : '',
                        'tempoutmin' => $item->tempoutmin ? round(Utils::convertTemp($item->tempoutmin, $measureSystem), 3) : '',
                        'dewptout' => $item->dewptout ? round(Utils::convertTemp($item->dewptout, $measureSystem), 3) : '',
                        'humout' => $item->humout ? round($item->humout, 3) : '',
                        'winddirstr' => $item->winddirstr,
                        'windspeed' => $item->windspeed ? round(Utils::convertSpeed($item->windspeed, $measureSystem), 0) : '',
                        'windspeedhi' => $item->windspeedhi ? round(Utils::convertSpeed($item->windspeedhi, $measureSystem), 3) : '',
                        'winddirstr2' => $item->winddirstr2,
                        'windspeed2' => $item->windspeed2 ? round(Utils::convertSpeed($item->windspeed2, $measureSystem), 3) : '',
                        'windspeedhi2' => $item->windspeedhi2 ? round(Utils::convertSpeed($item->windspeedhi2, $measureSystem), 3) : '',
                        'raintotal' => $item->raintotal ? round(Utils::convertLength($item->raintotal, $measureSystem), 3) : '',
                        'press' => $item->press ? round(Utils::convertPress($item->press, $measureSystem), 2) : '',
                        'wetbulb' => $bulbo!=null?(string)$bulbo:'',
                        'heatindexout' => $item->heatindexout ? round(Utils::convertTemp($item->heatindexout, $measureSystem), 3) : '',
                        'uvindex' => $item->uvindex ? round($item->uvindex, 4) : '',
                        'solrad' => $item->solrad ? round(Utils::convertPower($item->solrad, $measureSystem), 2) : '',
                        'solevo' => $item->solevo ? round(Utils::convertLength($item->solevo, $measureSystem), 2) : '',
                        'soiltemp1' => $item->soiltemp1 ? round(Utils::convertLength($item->soiltemp1, $measureSystem), 3) : '',
                        'soilhum1' => $item->soilhum1 ? round($item->soilhum1, 3) : '',
                        'leaftemp1' => $item->leaftemp1 ? round(Utils::convertLength($item->leaftemp1, $measureSystem), 3) : '',
                        'leafhum1' => $item->leafhum1 ? round($item->leafhum1, 3) : '',
                    ];
                })
                ->toArray())->keyBy('receipt_date');
        }
            
        
        return $dataTemp;
    }

    /**
     * Descarga el Excel usando las columnas seleccionadas por el usuario.
     */
    public function downloadData($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null) {
        //$this->clearData();
        $this->hasDataDownload = false;
        $this->selectedStationId = $stationId;
        $this->selectedFormat = $format;
        $this->heads = $this->getVisibleHeads($this->selectedFormat == 'daily' ? $this->headsDaily : $this->headsMonthly);
        if ($fromDate != null) {
            $this->selectedFromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->selectedToDate = $toDate;
        }
        if ($year != null) {
            $this->selectedYear = $year;
        }
        if ($month != null) {
            $this->selectedMonth = $month;
        }
        $this->lastDayMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->day;

        //verificamos que existan registros antes de efectuar la descarga
        if ($this->selectedFormat == 'daily') { //todos los registros
            $this->hasDataDownload = $this->checkDailyData();
        }
        if ($this->selectedFormat == 'monthly') {
            $this->hasDataDownload = $this->checkMonthlyData();
        }
        if ($this->selectedFormat == 'annual') {
            $this->hasDataDownload = $this->checkAnnualData();
        }
        $this->showResultsDownload = true;
        if ($this->hasDataDownload) {  //si existen registros realizamos la descarga, caso contrario mostramos No se encontraron registros"
            $measureSystem = session('measure', 'metric');
            $heads = [];
            foreach($this->heads as $key => $head) {
                $heads[] = $head['label'] . ' ' . $head['unit'];
            }
            $export = new DataStationExport($this->buildDownloadFilename(), $this->selectedStationId, $this->selectedFormat,  $this->selectedFromDate, $this->selectedToDate, $this->selectedYear, $this->selectedMonth, $heads, $measureSystem, $this->typeS, array_keys($this->heads));
            return $export->download();
        }
    }

    /**
     * Descarga el PDF usando las columnas seleccionadas por el usuario.
     */
    public function downloadPdfData($stationId, $format, $fromDate = null, $toDate = null, $year = null, $month = null)
    {
        $this->hasDataDownload = false;
        $this->selectedStationId = $stationId;
        $this->selectedFormat = $format;
        $this->heads = $this->getVisibleHeads($this->selectedFormat == 'daily' ? $this->headsDaily : $this->headsMonthly);

        if ($fromDate != null) {
            $this->selectedFromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->selectedToDate = $toDate;
        }
        if ($year != null) {
            $this->selectedYear = $year;
        }
        if ($month != null) {
            $this->selectedMonth = $month;
        }

        $this->lastDayMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->day;

        if ($this->selectedFormat == 'daily') {
            $this->hasDataDownload = $this->checkDailyData();
        }
        if ($this->selectedFormat == 'monthly') {
            $this->hasDataDownload = $this->checkMonthlyData();
        }
        if ($this->selectedFormat == 'annual') {
            $this->hasDataDownload = $this->checkAnnualData();
        }

        $this->showResultsDownload = true;

        if (!$this->hasDataDownload) {
            return null;
        }

        $filename = $this->buildDownloadFilename('pdf');
        $path = $this->buildDownloadTempPath($filename);
        $paperSize = $this->getPdfPaperSize();

        app(DownloadDataStationPdfService::class)->generate(
            $path,
            $this->heads,
            $this->getPdfDataRows(),
            $this->getPdfMetadata(),
            $paperSize
        );

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Genera el nombre del archivo con fecha y hora de descarga.
     */
    protected function buildDownloadFilename(string $extension = 'xlsx'): string
    {
        return 'datos_' . Carbon::now('America/La_Paz')->format('Ymd_His') . '.' . $extension;
    }

    /**
     * Construye la ruta temporal donde se generara el PDF.
     */
    protected function buildDownloadTempPath(string $filename): string
    {
        return storage_path('app/temp-downloads/' . $filename);
    }

    /**
     * Obtiene los registros del PDF aplicando las columnas visibles.
     */
    protected function getPdfDataRows(): iterable
    {
        if ($this->selectedFormat == 'daily') {
            return $this->filterPdfRowsByHeads($this->getDailyPdfDataRows(), $this->heads);
        }

        $data = $this->selectedFormat == 'monthly'
            ? $this->getMonthlyData()
            : $this->getAnnualData();

        return $this->filterPdfRowsByHeads($data, $this->heads);
    }

    /**
     * Define el tamano de hoja del PDF segun la cantidad de variables visibles.
     */
    protected function getPdfPaperSize(): string
    {
        $visibleVariables = max(count($this->heads) - 1, 0);
        $totalVariables = $this->typeS === 'qair'
            ? max(count($this->headsDaily) - 1, 1)
            : max(count($this->availableColumnOptions), 1);

        return $visibleVariables <= floor($totalVariables / 2) ? 'letter' : 'a4';
    }

    /**
     * Devuelve la etiqueta traducida del formato seleccionado.
     */
    protected function getSelectedFormatLabel(): string
    {
        return $this->formatList[$this->selectedFormat] ?? $this->selectedFormat;
    }

    /**
     * Prepara los metadatos visibles en el encabezado del PDF.
     */
    protected function getPdfMetadata(): array
    {
        $station = StationM::find($this->selectedStationId);

        return [
            'title' => __('messages.downloads.page'),
            'station' => __('messages.downloads.file_report_station') . ': ' . ($station->name ?? ''),
            'period' => $this->getPdfPeriodLabel(),
            'generatedAt' => __('messages.downloads.file_report_date_emit') . Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Devuelve el texto del periodo usado en el encabezado del PDF.
     */
    protected function getPdfPeriodLabel(): string
    {
        if ($this->selectedFormat === 'daily') {
            return $this->getSelectedFormatLabel() . ' | ' . __('messages.downloads.file_report_date_from') . $this->selectedFromDate . ' | ' . __('messages.downloads.file_report_date_to') . $this->selectedToDate;
        }

        if ($this->selectedFormat === 'monthly') {
            return $this->getSelectedFormatLabel() . ' | ' . __('messages.downloads.year_date_label') . ': ' . $this->selectedYear . ' | ' . __('messages.downloads.month_date_label') . ': ' . ($this->monthList[$this->selectedMonth] ?? $this->selectedMonth);
        }

        return $this->getSelectedFormatLabel() . ' | ' . __('messages.downloads.year_date_label') . ': ' . $this->selectedYear;
    }

    /**
     * Recorre registros diarios del PDF usando cursor para no acumularlos en memoria.
     */
    protected function getDailyPdfDataRows(): iterable
    {
        $fromDate = Carbon::parse($this->selectedFromDate . '00:00:00');
        $toDate = Carbon::parse($this->selectedToDate . '23:59:59');
        $year = $fromDate->format('Y');

        if (!config("database.connections.senvatec_db_{$year}")) {
            return;
        }

        $measureSystem = session('measure', 'metric');
        $query = $this->getDailyPdfQuery($year, $fromDate, $toDate);

        foreach ($query->cursor() as $item) {
            yield $this->typeS === 'qair'
                ? $this->mapDailyQairRow($item, $measureSystem)
                : $this->mapDailyStationRow($item, $measureSystem);
        }
    }

    /**
     * Construye la consulta diaria del PDF segun el tipo de estacion.
     */
    protected function getDailyPdfQuery(string $year, Carbon $fromDate, Carbon $toDate)
    {
        $query = DB::connection('senvatec_db_' . $year)
            ->table('senva_data')
            ->where('station_id', $this->selectedStationId)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->orderBy('receipt_date', 'asc');

        if ($this->typeS === 'qair') {
            return $query->select('receipt_date', 'tempout', 'humout', 'pm10', 'pm2_5', 'pm1');
        }

        return $query->select('receipt_date', 'tempout', 'tempoutmax', 'tempoutmin', 'dewptout', 'humout',
            'winddir', 'winddirstr', 'windspeed', 'windspeedhi',
            'winddir2', 'windspeed2', 'windspeedhi2',
            'raintotal', 'rainrate', 'press', 'heatindexout',
            'uvindex', 'solrad', 'solevo', 'soiltemp1', 'soilhum1', 'leaftemp1', 'leafhum1', 'pm10', 'pm2_5', 'pm1');
    }

    /**
     * Formatea una fila diaria de calidad de aire para PDF.
     */
    protected function mapDailyQairRow($item, string $measureSystem): array
    {
        return [
            'receipt_date' => Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
            'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',
            'humout' => $item->humout ? round($item->humout, 0) : '',
            'pm1' => $item->pm1 ? round($item->pm1, 1) : '',
            'pm2_5' => $item->pm2_5 ? round($item->pm2_5, 1) : '',
            'pm10' => $item->pm10 ? round($item->pm10, 1) : '',
        ];
    }

    /**
     * Formatea una fila diaria meteorologica para PDF.
     */
    protected function mapDailyStationRow($item, string $measureSystem): array
    {
        $bulbo = null;

        try {
            $e = (6.11 * pow(10, (7.5 * round(Utils::convertTemp($item->dewptout, $measureSystem), 1) / (237.7 + round(Utils::convertTemp($item->dewptout, $measureSystem), 1)))));
            $resultado = (((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1)) * round(Utils::convertTemp($item->tempout, $measureSystem), 1)) + ((4098 * $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7), 2) * round(Utils::convertTemp($item->dewptout, $measureSystem), 1))) / ((0.00066 * round(Utils::convertPress($item->press, $measureSystem), 1)) + (4098 * $e) / pow((round(Utils::convertTemp($item->dewptout, $measureSystem), 1) + 237.7), 2));
            $bulbo = round($resultado, 2);
        } catch (\Exception $e) {
        }

        return [
            'receipt_date' => Carbon::parse($item->receipt_date)->format('d/m/Y H:i:s'),
            'tempout' => $item->tempout ? round(Utils::convertTemp($item->tempout, $measureSystem), 1) : '',
            'tempoutmax' => $item->tempoutmax ? round(Utils::convertTemp($item->tempoutmax, $measureSystem), 1) : '',
            'tempoutmin' => $item->tempoutmin ? round(Utils::convertTemp($item->tempoutmin, $measureSystem), 1) : '',
            'dewptout' => $item->dewptout ? round(Utils::convertTemp($item->dewptout, $measureSystem), 1) : '',
            'humout' => $item->humout ? round($item->humout, 0) : '',
            'winddir' => $item->winddir ? round($item->winddir, 1) : '',
            'winddirstr' => Utils::windDirToStr($item->winddir, $item->windspeed),
            'windspeed' => $item->windspeed ? round(Utils::convertSpeed($item->windspeed, $measureSystem), 0) : '',
            'windspeedhi' => $item->windspeedhi ? round(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1) : '',
            'winddir2' => $item->winddir2 ? round($item->winddir2, 1) : '',
            'winddirstr2' => Utils::windDirToStr($item->winddir2, $item->windspeed2),
            'windspeed2' => $item->windspeed2 ? round(Utils::convertSpeed($item->windspeed2, $measureSystem), 0) : '',
            'windspeedhi2' => $item->windspeedhi2 ? round(Utils::convertSpeed($item->windspeedhi2, $measureSystem), 1) : '',
            'raintotal' => $item->raintotal ? round(Utils::convertLength($item->raintotal, $measureSystem), 2) : '',
            'rainrate' => $item->rainrate ? round(Utils::convertRainfall($item->rainrate, $measureSystem), 2) : '',
            'press' => $item->press ? round(Utils::convertPress($item->press, $measureSystem), 1) : '',
            'wetbulb' => $bulbo != null ? (string) $bulbo : '',
            'heatindexout' => $item->heatindexout ? round(Utils::convertTemp($item->heatindexout, $measureSystem), 1) : '',
            'uvindex' => $item->uvindex ? round($item->uvindex, 1) : '',
            'solrad' => $item->solrad ? round(Utils::convertPower($item->solrad, $measureSystem), 2) : '',
            'solevo' => $item->solevo ? round(Utils::convertLength($item->solevo, $measureSystem), 3) : '',
            'soiltemp1' => $item->soiltemp1 ? round(Utils::convertLength($item->soiltemp1, $measureSystem), 1) : '',
            'soilhum1' => $item->soilhum1 ? round($item->soilhum1, 1) : '',
            'leaftemp1' => $item->leaftemp1 ? round(Utils::convertLength($item->leaftemp1, $measureSystem), 1) : '',
            'leafhum1' => $item->leafhum1 ? round($item->leafhum1, 1) : '',
        ];
    }

    /**
     * Filtra filas iterables del PDF segun los encabezados visibles.
     */
    protected function filterPdfRowsByHeads(iterable $rows, array $heads): iterable
    {
        if ($this->typeS === 'qair') {
            yield from $rows;
            return;
        }

        $visibleKeys = array_keys($heads);

        foreach ($rows as $row) {
            yield $this->filterRowByKeys($row, $visibleKeys);
        }
    }

    /**
     * Verifica si existen datos diarios para descargar.
     */
    public function checkDailyData() {
        $fromDate = Carbon::parse($this->selectedFromDate . '00:00:00');
        $toDate = Carbon::parse($this->selectedToDate . '23:59:59'); //colocamos la fecha final correcta
        $year = $fromDate->format('Y');
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return false;
        // }
        // return DB::connection('db_guest_h'.$year)
        //     ->table('data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return false;
        }

        return DB::connection('senvatec_db_'.$year)
            ->table('senva_data')
            ->where('station_id', $this->selectedStationId)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->exists();
    }

    /**
     * Verifica si existen datos mensuales para descargar.
     */
    public function checkMonthlyData() {
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$this->selectedYear}")) {
        //     return false;
        // }
        // return DB::connection('db_guest_h'.$this->selectedYear)
        //         ->table('daily_data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$this->selectedYear}")) {
            return false;
        }

        return DB::connection('senvatec_db_'.$this->selectedYear)
                ->table('senva_daily_data')
                ->where('station_id', $this->selectedStationId)
                ->where('f_year', $this->selectedYear)
                ->where('f_month', $this->selectedMonth)
                ->exists();
    }

    /**
     * Verifica si existen datos anuales para descargar.
     */
    public function checkAnnualData() {
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$this->selectedYear}")) {
        //     return false;
        // }
        // return DB::connection('db_guest_h'.$this->selectedYear)
        //         ->table('daily_data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$this->selectedYear}")) {
            return false;
        }

        return DB::connection('senvatec_db_'.$this->selectedYear)
                ->table('senva_daily_data')
                ->where('station_id', $this->selectedStationId)
                ->where('f_year', $this->selectedYear)
                ->exists();
    }

    /**
     * Abre el modal de configuracion de columnas.
     */
    public function openColumnsModal(): void
    {
        if ($this->typeS === 'qair') {
            return;
        }

        $this->showColumnsModal = true;
    }

    /**
     * Cierra el modal de configuracion de columnas.
     */
    public function closeColumnsModal(): void
    {
        $this->showColumnsModal = false;
    }

    /**
     * Guarda o actualiza las columnas visibles recibidas desde el modal.
     */
    public function saveColumnConfiguration(array $columns = []): void
    {
        if ($this->typeS === 'qair') {
            return;
        }

        $this->selectedColumns = $this->sanitizeSelectedColumns($columns);

        UserDownloadColumnPreferenceM::updateOrCreate(
            ['user_id' => Auth::id()],
            ['selected_columns' => array_values($this->selectedColumns)]
        );

        $this->showColumnsModal = false;
        session()->flash('columns_success', __('messages.downloads.columns_config_saved'));

        if ($this->showResultsView && $this->selectedStationId) {
            $this->showData(
                $this->selectedStationId,
                $this->selectedFormat,
                $this->selectedFromDate,
                $this->selectedToDate,
                $this->selectedYear,
                $this->selectedMonth
            );
        }
    }

    /**
     * Carga la configuracion guardada o selecciona todas las columnas por defecto.
     */
    protected function loadColumnPreferences($user): void
    {
        $this->availableColumnOptions = $this->getAvailableColumnOptions();
        $preference = UserDownloadColumnPreferenceM::where('user_id', $user->id)->first();

        $this->selectedColumns = $preference
            ? $this->sanitizeSelectedColumns($preference->selected_columns ?? [])
            : array_keys($this->availableColumnOptions);
    }

    /**
     * Devuelve las variables configurables excluyendo la fecha.
     */
    protected function getAvailableColumnOptions(): array
    {
        return array_filter(
            $this->headsDaily,
            fn ($key) => $key !== 'receipt_date',
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Limpia columnas invalidas manteniendo solo variables permitidas.
     */
    protected function sanitizeSelectedColumns(array $columns): array
    {
        $allowedColumns = array_keys($this->availableColumnOptions);

        return array_values(array_intersect($columns, $allowedColumns));
    }

    /**
     * Filtra encabezados manteniendo siempre la fecha visible.
     */
    protected function getVisibleHeads(array $heads): array
    {
        if ($this->typeS === 'qair') {
            return $heads;
        }

        $visibleKeys = array_merge(['receipt_date'], $this->sanitizeSelectedColumns($this->selectedColumns));

        return array_filter(
            $heads,
            fn ($key) => in_array($key, $visibleKeys, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Filtra cada fila segun los encabezados visibles.
     */
    protected function filterDataRowsByHeads($rows, array $heads)
    {
        if ($this->typeS === 'qair') {
            return $rows;
        }

        $visibleKeys = array_keys($heads);

        if ($rows instanceof \Illuminate\Support\Collection) {
            return $rows->map(fn ($row) => $this->filterRowByKeys($row, $visibleKeys));
        }

        return array_map(fn ($row) => $this->filterRowByKeys($row, $visibleKeys), $rows);
    }

    /**
     * Ordena y reduce una fila usando la lista de columnas visibles.
     */
    protected function filterRowByKeys(array $row, array $visibleKeys): array
    {
        $filteredRow = [];

        foreach ($visibleKeys as $key) {
            $filteredRow[$key] = $row[$key] ?? '';
        }

        return $filteredRow;
    }
}
