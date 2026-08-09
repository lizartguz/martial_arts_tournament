<?php

namespace App\Livewire\GraphDataStation;

use App\Models\StationM;
use App\Utils\Utils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Traits\AuthValidationTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\UsersSubscriptionM;
use App\Models\UserDependencyM;

class GraphDataStation extends Component
{
    
    use AuthValidationTrait;

    public $monthList = [];
    public $stationList = [];
    //definimos la lista de variables, la etiqueta del select, el tipo de grafico y la escala
    public $variableList = [];
    public $formatList = [];

    public $selectedStationList = [];
    public $selectedVariableList = [];
    public $selectedFormat = 'all';
    public $selectedFromDate = null;
    public $selectedToDate = null;
    public $selectedYear;
    public $hasData = false;

    public $maxDate;
    public $minDate;
    public $maxYear;
    public $minYear;

    public $measureSystem;
    public $tempUnit;
    public $speedUnit;
    public $lengthUnit;
    public $percUnit;
    public $pressUnit;
    public $powerUnit;
    public $typeS= null;

    protected $listeners = ['showGraph'];

    protected $datasets = [];
    protected $data = [];
    protected $labels = [];

    public function mount($typeS = null)
    {
        
        $this->typeS = $typeS;
        $user = Auth::user();
        $this->validarUsuario($user, []);
        abort_unless($user?->can('stations.charts.view'), 403);

        date_default_timezone_set('America/La_Paz');

        $this->minDate = Carbon::create('2011-01-01');
        $this->maxDate = Carbon::now();
        $this->minYear = $this->minDate->format('Y');
        $this->maxYear = $this->maxDate->format('Y');

        $this->formatList = [
            'all' => __('messages.graphs.format_all_label'),
            'daily' => __('messages.graphs.format_daily_label'),
            'annual' => __('messages.graphs.format_annual_label'),
        ];

        $this->data = [];
        $this->labels = [];
        $this->selectedFormat = 'all';
        $this->selectedYear = $this->maxDate->format('Y');
//        $this->selectedFromDate = Carbon::now()->format('Y-m-d');
//        $this->selectedToDate = Carbon::now()->format('Y-m-d');

        
        if($this->typeS===null){
            $this->variableList = [
                'tempout' => ['label' => __('messages.graphs.variables.tempout'), 'graph' => 'line', 'scale' => 'temp', 'aggregation' => 'avg'],
                'tempoutmax' =>  ['label' => __('messages.graphs.variables.tempoutmax'), 'graph' => 'line', 'scale' => 'temp', 'aggregation' => 'max' ],
                'tempoutmin' =>  ['label' => __('messages.graphs.variables.tempoutmin'), 'graph' => 'line', 'scale' => 'temp', 'aggregation' => 'min' ],
                'humout' =>  ['label' => __('messages.graphs.variables.humout'), 'graph' => 'line', 'scale' => 'perc', 'aggregation' => 'avg' ],
                'windspeed' =>  ['label' => __('messages.graphs.variables.windspeed'), 'graph' => 'line', 'scale' => 'speed', 'aggregation' => 'avg' ],
                'winddir' =>  ['label' => __('messages.graphs.variables.winddir'), 'graph' => 'scatter', 'scale' => 'dir', 'aggregation' => 'max' ],
                'windspeed2' =>  ['label' => __('messages.graphs.variables.windspeed2'), 'graph' => 'line', 'scale' => 'speed', 'aggregation' => 'avg' ],
                'winddir2' =>  ['label' => __('messages.graphs.variables.winddir2'), 'graph' => 'scatter', 'scale' => 'dir', 'aggregation' => 'max' ],
                'raintotal' =>  ['label' => __('messages.graphs.variables.raintotal'), 'graph' => 'line', 'scale' => 'length', 'aggregation' => 'max' ],
                'press' =>  ['label' => __('messages.graphs.variables.press'), 'graph' => 'line', 'scale' => 'press', 'aggregation' => 'avg' ],
                'solrad' =>  ['label' => __('messages.graphs.variables.solrad'), 'graph' => 'line', 'scale' => 'power', 'aggregation' => 'avg' ],
                'solevo' =>  ['label' => __('messages.graphs.variables.solevo'), 'graph' => 'line', 'scale' => 'length', 'aggregation' => 'sum' ],
                'leafhum1' =>  ['label' => __('messages.graphs.variables.leafhum1'), 'graph' => 'line', 'scale' => 'nro', 'aggregation' => 'avg' ],
                'soilhum1' =>  ['label' => __('messages.graphs.variables.soilhum1'), 'graph' => 'line', 'scale' => 'nro', 'aggregation' => 'avg' ],
                'leafhum2' =>  ['label' => __('messages.graphs.variables.leafhum2'), 'graph' => 'line', 'scale' => 'nro', 'aggregation' => 'avg' ],
                'soilhum2' =>  ['label' => __('messages.graphs.variables.soilhum2'), 'graph' => 'line', 'scale' => 'nro', 'aggregation' => 'avg' ],
            ];
        }else{
            //qair
            $this->variableList = [
                'tempout' => ['label' => __('messages.graphs.variables.tempout'), 'graph' => 'line', 'scale' => 'temp', 'aggregation' => 'avg'],              
                'humout' =>  ['label' => __('messages.graphs.variables.humout'), 'graph' => 'line', 'scale' => 'perc', 'aggregation' => 'avg' ],               
                'pm10' =>  ['label' => __('messages.graphs.variables.pm10'), 'graph' => 'line', 'scale' => 'qair', 'aggregation' => 'avg' ],
                'pm2_5' =>  ['label' => __('messages.graphs.variables.pm2_5'), 'graph' => 'line', 'scale' => 'qair', 'aggregation' => 'avg' ],
                'pm1' =>  ['label' => __('messages.graphs.variables.pm1'), 'graph' => 'line', 'scale' => 'qair', 'aggregation' => 'avg' ],
            ];
        }
        

        if (Auth::user()->hasAnyRole(['super_manager', 'manager', 'meteorology', 'meteorologist', 'technical'])) {
            if($this->typeS=="qair"){
                $this->stationList = StationM::where('stations.state', true)
                ->select('stations.id', 'stations.name')
                ->where('stations.state', true)
                ->orderBy('stations.name', 'asc')
                ->distinct()
                ->get();
            }else{
                $this->stationList = StationM::where('stations.state', true)                
                ->select('stations.id', 'stations.name')
                ->where('stations.state', true)
                ->orderBy('stations.name', 'asc')
                ->distinct()
                ->get();
            }
            
        }else{
            if (Auth::user()->hasAnyRole(['subscriber'])) {
                
                if($this->typeS=="qair"){
                    $this->stationList = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                    ->join('stations as s', 'us.station_id', 's.id')
                    ->where('u.id', Auth::user()->id)
                    ->whereIn('u.user_type', [1, 2])
                    ->select('s.id', 's.name')
                    ->where('s.state', true)
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }else{
                    $this->stationList = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                    ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                    ->join('stations as s', 'us.station_id', 's.id')
                    ->where('u.id', Auth::user()->id)
                    ->whereIn('u.user_type', [1, 2])
                    ->select('s.id', 's.name')                   
                    ->where('s.state', true)
                    ->orderBy('s.name', 'asc')
                    ->distinct()
                    ->get();
                }
            }
        }
        

        $this->measureSystem = session('measure', 'metric');
        $this->tempUnit = Utils::getDegreeUnit($this->measureSystem);
        $this->speedUnit = Utils::getSpeedUnit($this->measureSystem);
        $this->lengthUnit = Utils::getLengthUnit($this->measureSystem);
        $this->percUnit = '%';
        $this->pressUnit = Utils::getPressUnit($this->measureSystem);
        $this->powerUnit = Utils::getPowerUnit($this->measureSystem);
    }

    public function render()
    {
        return view('livewire.graph-data-station.graph-data-station');
    }

    public function showGraph($stationList, $variableList, $format, $fromDate = null, $toDate = null, $year = null) {
        set_time_limit(1800);  //ampliamos el limite de espera
        $this->selectedStationList = $stationList;
        $this->selectedVariableList = $variableList;
        if ($fromDate != null) {
            $this->selectedFromDate = $fromDate;
        }
        if ($toDate != null) {
            $this->selectedToDate = $toDate;
        }
        if ($year != null) {
            $this->selectedYear = $year;
        }
        $this->selectedFormat = $format;
        if ($this->selectedFormat == 'all') { //todos los registros
            $this->getAllData();
        } else if ($this->selectedFormat == 'daily') { //resumen diario
            $this->getDailyData();
        } else if ($this->selectedFormat == 'annual') { //resumen anual
            $this->getAnnualData();
        } else { //no hace nada
        }
        $this->updateGraph();
    }

    protected function convertData($key, $value) {
        if ($key === 'tempout' || $key === 'tempoutmax' || $key === 'tempoutmin') {
            return round(Utils::convertTemp($value, $this->measureSystem), 1);
        } else if ($key === 'windspeed' || $key === 'windspeed2') {
            return round(Utils::convertSpeed($value, $this->measureSystem), 1);
        } else if ($key === 'raintotal' || $key === 'solevo') {
            return round(Utils::convertLength($value, $this->measureSystem), 3);
        } else if ($key === 'press') {
            return round(Utils::convertPress($value, $this->measureSystem), 3);
        } else if ($key === 'solrad') {
            return round(Utils::convertPower($value, $this->measureSystem), 3);
        /*} else if ($key === 'pm1' || $key === 'pm10' || $key === 'pm2_5') {
            return round(Utils::convertPower($value, $this->measureSystem), 3);*/
        }
        else {
            return $value;
        }
    }

    //todos los registros
    public function getAllData() {
        $fromDate = Carbon::parse($this->selectedFromDate . '00:00:00');
        $toDate = Carbon::parse($this->selectedToDate . '23:59:59');
        $year = $fromDate->format('Y');

        $this->data = [];
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return ;
        // }
        // $records = DB::connection('db_guest_h'.$year)
        //     ->table('data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return ;
        }

        $records = DB::connection('senvatec_db_'.$year)
            ->table('senva_data')
            ->select('station_id', 'receipt_date', ...$this->selectedVariableList)
            ->whereIn('station_id', $this->selectedStationList)
            ->where('receipt_date', '>=', $fromDate)
            ->where('receipt_date', '<=', $toDate)
            ->orderBy('receipt_date', 'asc')
            ->get();

        //creamos las etiquetas del ejeX
        $this->labels = $records->pluck('receipt_date')->unique()->values()->toArray();
        foreach ($records as $entry) {
            $stationId = $entry->station_id;
            $receiptDate = $entry->receipt_date;

            // Inicializa la estación si no existe en el resultado
            if (!isset($this->data[$stationId])) {
                $this->data[$stationId] = [];
            }

            foreach ($entry as $key => $value) {
                if ($key === 'station_id' || $key === 'receipt_date') { //no tomar en cuenta estos campos
                    continue;
                }
                // Inicializa la clave si no existe
                if (!isset($this->data[$stationId][$key])) {
                    $this->data[$stationId][$key] = [];
                };

                // Agrega el dato a la estructura correspondiente
                $this->data[$stationId][$key][] = [
                    "x" => $receiptDate,
                    'y' => $this->convertData($key, $value),
                ];
            }
        }
    }

    public function getDailyData() {
        $fromDate = Carbon::parse($this->selectedFromDate);
        $toDate = Carbon::parse($this->selectedToDate);
        $year = $fromDate->format('Y');

        $this->data = [];
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$year}")) {
        //     return ;
        // }
        // $records = DB::connection('db_guest_h'.$year)
        //     ->table('daily_data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$year}")) {
            return ;
        }

        $records = DB::connection('senvatec_db_'.$year)
            ->table('senva_daily_data')
            ->select('station_id', 'registration_date', ...$this->selectedVariableList)
            ->whereIn('station_id', $this->selectedStationList)
            ->where('registration_date', '>=', $fromDate)
            ->where('registration_date', '<=', $toDate)
            ->orderBy('registration_date', 'asc')
            ->get();
        $this->labels = $records->pluck('registration_date')->unique()->values()->toArray();
        foreach ($records as $entry) {
            $stationId = $entry->station_id;
            $receiptDate = $entry->registration_date;

            // Inicializa la estación si no existe en el resultado
            if (!isset($this->data[$stationId])) {
                $this->data[$stationId] = [];
            }

            foreach ($entry as $key => $value) {
                if ($key === 'station_id' || $key === 'registration_date') { //no tomar en cuenta estos campos
                    continue;
                }
                // Inicializa la clave si no existe
                if (!isset($this->data[$stationId][$key])) {
                    $this->data[$stationId][$key] = [];
                };

                // Agrega el dato a la estructura correspondiente
                $this->data[$stationId][$key][] = [
                    "x" => $receiptDate, //(new DateTime($receiptDate))->format('Y-m-d\TH:i:s\Z'),
                    'y' => $this->convertData($key, $value),
                ];
            }
        }
    }

    public function getAnnualData() {
        $filteredVariables = array_intersect_key($this->variableList, array_flip($this->selectedVariableList));
        $selects = array_map(function ($variable, $config) {
            $aggregation = strtoupper($config['aggregation'] ?? 'AVG');
            return DB::raw("$aggregation($variable) as $variable");
        }, array_keys($filteredVariables), $filteredVariables);

        $this->data = [];
        
        // Conexión antigua (comentado)
        // if (!config("database.connections.db_guest_h{$this->selectedYear}")) {
        //     return ;
        // }
        // $records = DB::connection('db_guest_h'.$this->selectedYear)
        //     ->table('daily_data')
        
        // Nueva conexión
        if (!config("database.connections.senvatec_db_{$this->selectedYear}")) {
            return ;
        }

        $records = DB::connection('senvatec_db_'.$this->selectedYear)
            ->table('senva_daily_data')
            ->select('station_id', 'f_year', 'f_month')
            ->addSelect($selects)
            ->whereIn('station_id', $this->selectedStationList)
            ->where('f_year', $this->selectedYear)
            ->groupBy('station_id', 'f_year', 'f_month')
            ->get();

        $this->labels = $records->map(function ($item) {
            return $item->f_year . '-' . sprintf('%02d', $item->f_month) . '-01 00:00:00';
        })->unique()->values()->toArray();
        foreach ($records as $entry) {
            $stationId = $entry->station_id;
            $receiptDate = $entry->f_year . '-' . sprintf('%02d', $entry->f_month) . '-01 00:00:00';

            // Inicializa la estación si no existe en el resultado
            if (!isset($this->data[$stationId])) {
                $this->data[$stationId] = [];
            }

            foreach ($entry as $key => $value) {
                if ($key === 'station_id' || $key === 'f_year' || $key === 'f_month') { //no tomar en cuenta estos campos al generar las series
                    continue;
                }
                // Inicializa la clave si no existe
                if (!isset($this->data[$stationId][$key])) {
                    $this->data[$stationId][$key] = [];
                };

                // Agrega el dato a la estructura correspondiente
                $this->data[$stationId][$key][] = [
                    "x" => $receiptDate,
                    'y' => $this->convertData($key, $value),
                ];
            }
        }
    }

    public function updateGraph() {
        set_time_limit(1800);
        $colors = ["#38b99a", "#36a2eb", "#ff6384", "#9966ff", "#ff9f40", "#4bc0c0", "#ff6347", "#90ee90", "#87cefa"];
        $this->datasets = [];
        $index = 0;
        if (count($this->data)) {
            foreach($this->selectedStationList as $stationId) {
                $stationModel = StationM::find($stationId);
                foreach($this->selectedVariableList as $variable) {
                    $backgroundColor = $colors[$index % count($colors)];
                    $borderColor = $backgroundColor;
                    $dataset = [
                        "label" => $stationModel->name . ' (' . $this->variableList[$variable]['label'] . ')',
                        "data" => isset($this->data[$stationId]) ? $this->data[$stationId][$variable] : [],
                        "backgroundColor" => $backgroundColor,
                        "borderColor" => $borderColor,
                        "type" => $this->variableList[$variable]['graph'],
                        "spanGaps" => true,
                        "pointRadius" => $this->variableList[$variable]['graph'] == 'scatter' ? 3 : 2,
                        "borderSkipped" => false,
                        "fill" => $variable == 'raintotal',
                        "yAxisID" => $this->variableList[$variable]['scale'],
                    ];
                    $this->datasets[] = $dataset;
                    $index++;
                }
            }
        }

        //dd( $this->datasets);
        $this->dispatch('updateChart', [
            'datasets' => $this->datasets,
            'labels' => $this->labels,
        ]);
    }
}
