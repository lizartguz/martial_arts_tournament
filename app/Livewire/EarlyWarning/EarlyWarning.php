<?php

namespace App\Livewire\EarlyWarning;

use App\Models\StationM;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class EarlyWarning extends Component
{
    use WithPagination;

    public $stationList = [];
    public $cropList = [];
    public $cropConditionList = [];
    public $leafStationList = [];

    public $selectedStation;
    public $selectedFromDate;
    public $selectedToDate;
    public $selectedCrop;
    public $selectedCropCondition;
    public $selectedLeafStation;
    public $showResults = false;
    public $hasData = false;
    public $canExecQuery = false;

    public $maxDate;
    public $minDate;
    public $columnsName = [];

    protected $paginationTheme = 'tailwind';
    protected $listeners = ['processParams'];
    protected $perPage = 20;

    public function mount()
    {
//        $this->selectedStation = 341;
//        $this->selectedFromDate = '2024-01-01';
//        $this->selectedToDate = '2024-01-30';
//        $this->selectedCrop = 15;
//        $this->selectedCropCondition = 1;
//        $this->selectedLeafStation = 1;

        $this->minDate = Carbon::create('2011-01-01');
        $this->maxDate = Carbon::now();

        $this->stationList = StationM::select('stations.id', 'stations.name')
            ->where('stations.state', true)
            ->orderBy('name', 'asc')
            ->get();

        $this->cropList = [
            '1' => ['label' => __('messages.early_warning.crops.rice'), 'conditions' => []],
            '2' => ['label' => __('messages.early_warning.crops.acai'), 'conditions' => []],
            '3' => ['label' => __('messages.early_warning.crops.banana'), 'conditions' => []],
            '4' => ['label' => __('messages.early_warning.crops.cocoa'), 'conditions' => []],
            '5' => ['label' => __('messages.early_warning.crops.coffee'), 'conditions' => []],
            '6' => ['label' => __('messages.early_warning.crops.sugarcane'), 'conditions' => [
                '1' => ['label' => __('messages.early_warning.crop_conditions.sugarcane_1')],
            ]],
            '7' => ['label' => __('messages.early_warning.crops.chestnut'), 'conditions' => []],
            '8' => ['label' => __('messages.early_warning.crops.onion'), 'conditions' => []],
            '9' => ['label' => __('messages.early_warning.crops.cupuacu'), 'conditions' => []],
            '10' => ['label' => __('messages.early_warning.crops.majo'), 'conditions' => []],
            '11' => ['label' => __('messages.early_warning.crops.corn'), 'conditions' => []],
            '12' => ['label' => __('messages.early_warning.crops.potato'), 'conditions' => []],
            '13' => ['label' => __('messages.early_warning.crops.pineapple'), 'conditions' => []],
            '14' => ['label' => __('messages.early_warning.crops.quinoa'), 'conditions' => []],
            '15' => ['label' => __('messages.early_warning.crops.soybean'), 'conditions' => [
                '1' => ['label' => __('messages.early_warning.crop_conditions.soybean_1')],
                '2' => ['label' => __('messages.early_warning.crop_conditions.soybean_2')],
                '3' => ['label' => __('messages.early_warning.crop_conditions.soybean_3')],
                '4' => ['label' => __('messages.early_warning.crop_conditions.soybean_4')],
                '5' => ['label' => __('messages.early_warning.crop_conditions.soybean_5')],
            ]],
            '16' => ['label' => __('messages.early_warning.crops.tomato'), 'conditions' => []],
            '17' => ['label' => __('messages.early_warning.crops.wheat'), 'conditions' => []],
            '18' => ['label' => __('messages.early_warning.crops.grape'), 'conditions' => []],
        ];

        $this->leafStationList = [
            '1' => __('messages.early_warning.leaf_sensor_1_label'),
            '2' => __('messages.early_warning.leaf_sensor_2_label'),
        ];
    }

    public function processParams($selectedStation, $selectedFromDate, $selectedToDate, $selectedCrop, $selectedCropCondition, $selectedLeafStation) {
        $this->selectedStation = $selectedStation;
        $this->selectedFromDate = $selectedFromDate;
        $this->selectedToDate = $selectedToDate;
        $this->selectedCrop = $selectedCrop;
        $this->selectedCropCondition = $selectedCropCondition;
        $this->selectedLeafStation = $selectedLeafStation;
        switch ($this->selectedCrop) {
            case '6':
                switch ($this->selectedCropCondition) {
                    case '1':
                        $this->columnsName = $this->getSugarCane1Columns();
                        break;
                }
                break;
            case '15':
                switch ($this->selectedCropCondition) {
                    case '1':
                        $this->columnsName = $this->getSoyBean1Columns();
                        break;
                    case '2':
                        $this->columnsName = $this->getSoyBean2Columns();
                        break;
                    case '3':
                        $this->columnsName = $this->getSoyBean3Columns();
                        break;
                    case '4':
                        $this->columnsName = $this->getSoyBean4Columns();
                        break;
                    case '5':
                        $this->columnsName = $this->getSoyBean5Columns();
                        break;
                }
                break;
        }

        $this->resetPage();
        $this->canExecQuery = true;
        $this->showResults = true;
    }

    protected function getData() {
        $year = Carbon::create($this->selectedFromDate)->format('Y');
        $data = null;
        $this->hasData = false;
        if ($this->canExecQuery) {
            $measureSystem = session('measure', 'metric');
            $leafTemp = 'leaftemp' . $this->selectedLeafStation;
            $leafHum = 'leafhum' . $this->selectedLeafStation;
            $totalLeafHum = 'total_leafhum' . $this->selectedLeafStation;
            
            // Conexión antigua (comentado)
            // if (config("database.connections.db_guest_h$year")) {
            //     $this->hasData = DB::connection('db_guest_h' . $year)
            //         ->table('daily_data')
            
            // Nueva conexión
            if (config("database.connections.senvatec_db_{$year}")) { //si existe config DB para ese año
                $this->hasData = DB::connection('senvatec_db_' . $year)
                    ->table('senva_daily_data')
                    ->where('daily_data.station_id', $this->selectedStation)
                    ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                    ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                    ->exists();

                if ($this->hasData) {
                    $measureSystem = session('measure', 'metric');
                    if ($this->selectedCrop == '6') { //caña
                        if ($this->selectedCropCondition == '1') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'humout',
                                    $leafHum . ' AS leafhum',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.{$leafHum} > 0
                                        ) as records_leafhum
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.humout >= 90
                                        ) as records_hum_90
                                    "),
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->conditions = '';

                                $item->conditions = __('messages.early_warning.conditions.no');
                                $item->conditions_sw = 0;
                                $item->infection_hours = (($item->records_leafhum * $item->processed_records)/144)/6;
                                $item->hours_hum_90 = (($item->records_hum_90*$item->humout)/144)/6; //confirmar si es humout
                                if (($item->infection_hours>=8) && (($item->hours_hum_90>=90) && ($item->tempout>=17) && ($item->tempout<=23))) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.yes');
                                }
                                $item->infection_hours = $item->infection_hours > 8 ? $item->infection_hours - 8 : 0;
                                $item->infection_hours = number_format($item->infection_hours, 1);
                                $item->hours_hum_90 = number_format($item->hours_hum_90, 1);

                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        }
                    } else if ($this->selectedCrop == '15') { //soya
                        if ($this->selectedCropCondition == '1') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'tempoutmax',
                                    'tempoutmin',
                                    'humout',
                                    $leafTemp . ' AS leaftemp',
                                    $leafHum . ' AS leafhum',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.{$leafHum} > 0
                                        ) as records_leafhum
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.humout >= 90
                                        ) as records_hum_90
                                    "),
                                    'windspeed',
                                    'windspeedhi',
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->rating = '';

                                $item->rating = __('messages.early_warning.conditions.low');
                                $item->conditions_sw = 0;
                                $item->wet_hours = (($item->records_leafhum * $item->processed_records)/144)/6;
                                $item->wet_temp = number_format($item->leaftemp, 1);
                                $item->hours_hum_90 = number_format((($item->records_hum_90*$item->processed_records)/144)/6, 1);
                                if ((($item->wet_hours>=6) && ($item->wet_hours<=12)) && (($item->leaftemp>=19) && ($item->leaftemp<=24))) {
                                    $item->conditions_sw = 1;
                                    $item->rating = __('messages.early_warning.conditions.high');
                                }
                                if (($item->wet_hours>12) && (($item->leaftemp>=11) && ($item->leaftemp<=28))) {
                                    $item->conditions_sw = 1;
                                    $item->rating = __('messages.early_warning.conditions.high');
                                }
                                if ((($item->wet_hours>=6) && ($item->wet_hours<=12)) && (($item->leaftemp>=11) && ($item->leaftemp<19))) {
                                    $item->conditions_sw = 2;
                                    $item->rating = __('messages.early_warning.conditions.moderate');
                                }
                                if ((($item->wet_hours>=6) && ($item->wet_hours<=12)) && (($item->leaftemp>24) && ($item->leaftemp<=28))) {
                                    $item->conditions_sw = 2;
                                    $item->rating = __('messages.early_warning.conditions.moderate');
                                }
                                if (($item->wet_hours>6) && (($item->leaftemp<11) || ($item->leaftemp>28))) {
                                    $item->conditions_sw = 3;
                                    $item->rating = __('messages.early_warning.conditions.light');
                                }
                                $item->wet_hours = number_format($item->wet_hours, 1);

                                $item->tempoutmax = number_format(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                                $item->tempoutmin = number_format(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        } else if ($this->selectedCropCondition == '2') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'tempoutmax',
                                    'tempoutmin',
                                    'humout',
                                    $leafTemp . ' AS leaftemp',
                                    $leafHum . ' AS leafhum',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.{$leafHum} > 0
                                        ) as records_leafhum
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.humout >= 90
                                        ) as records_hum_90
                                    "),
                                    'windspeed',
                                    'windspeedhi',
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->conditions = '';

                                $item->conditions_sw = 0;
                                $item->wet_hours = number_format((($item->records_leafhum * $item->processed_records)/144)/6, 1);
                                $item->wet_temp = number_format($item->leaftemp, 1);
                                $item->hours_hum_90 = number_format((($item->records_hum_90*$item->processed_records)/144)/6, 1);
                                if (($item->wet_hours>=6) && (($item->leaftemp>=15) && ($item->leaftemp<=30))) {
                                    $item->conditions_sw = 2;
                                    $item->conditions = __('messages.early_warning.conditions.progress');
                                }
                                if (($item->windspeed>=10) && ($item->raintotal>=5)) {
                                    $item->conditions_sw = 2;
                                    $item->conditions = __('messages.early_warning.conditions.dispersion');
                                }
                                if ((($item->wet_hours>=6) && (($item->leaftemp>=15) && ($item->leaftemp<=30))) && ($item->windspeed>=10) && ($item->raintotal>=5)) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.pro_dis');
                                }

                                $item->tempoutmax = number_format(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                                $item->tempoutmin = number_format(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->windspeed = number_format(Utils::convertSpeed($item->windspeed, $measureSystem), 1);
                                $item->windspeedhi = number_format(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        } else if ($this->selectedCropCondition == '3') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'tempoutmax',
                                    'tempoutmin',
                                    'humout',
                                    $leafTemp . ' AS leaftemp',
                                    $leafHum . ' AS leafhum',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.{$leafHum} > 0
                                        ) as records_leafhum
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.humout >= 70
                                        ) as records_hum_70
                                    "),
                                    'windspeed',
                                    'windspeedhi',
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->conditions = '';

                                $item->conditions = __('messages.early_warning.conditions.no');
                                $item->conditions_sw = 0;
                                $item->infection_hours = (($item->records_hum_70*$item->processed_records)/144)/6;
                                $item->germination_hours = (($item->records_leafhum*$item->processed_records)/144)/6;
                                if (($item->infection_hours>=6) && (($item->tempout>=24) && ($item->tempout<=32))) {
                                    $item->conditions_sw = 3;
                                    $item->conditions = __('messages.early_warning.conditions.infection');
                                }
                                if ($item->germination_hours>=6) {
                                    $item->conditions_sw = 2;
                                    $item->conditions = __('messages.early_warning.conditions.germination');
                                }
                                if (($item->germination_hours>=6) && (($item->windspeed>=15) || ($item->raintotal>=5))) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.germination_dispersion');
                                }
                                if (($item->windspeed>=15) && ($item->raintotal>=5)) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.dispersion');
                                }
                                $item->infection_hours = $item->infection_hours > 6 ? $item->infection_hours - 6 : 0;
                                $item->infection_hours = number_format($item->infection_hours, 1);
                                $item->germination_hours = $item->germination_hours > 6 ? $item->germination_hours - 6 : 0;
                                $item->germination_hours = number_format($item->germination_hours, 1);

                                $item->tempoutmax = number_format(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                                $item->tempoutmin = number_format(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->windspeed = number_format(Utils::convertSpeed($item->windspeed, $measureSystem), 1);
                                $item->windspeedhi = number_format(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        } else if ($this->selectedCropCondition == '4') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'tempoutmax',
                                    'tempoutmin',
                                    'humout',
                                    $leafTemp . ' AS leaftemp',
                                    $leafHum . ' AS leafhum',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.humout >= 80
                                        ) as records_hum_80
                                    "),
                                    'windspeed',
                                    'windspeedhi',
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->conditions = '';

                                $item->conditions = __('messages.early_warning.conditions.no');
                                $item->conditions_sw = 0;
                                $item->infection_hours = (($item->records_hum_80*$item->processed_records)/144)/6;
                                if (($item->infection_hours>=24) && (($item->tempout>=18) && ($item->tempout<=21))) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.infection');
                                }
                                $item->infection_hours = number_format($item->infection_hours, 1);

                                $item->tempoutmax = number_format(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                                $item->tempoutmin = number_format(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->windspeed = number_format(Utils::convertSpeed($item->windspeed, $measureSystem), 1);
                                $item->windspeedhi = number_format(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        } else if ($this->selectedCropCondition == '5') {
                            // Conexión antigua (comentado)
                            // $data = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $data = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->select(
                                    'registration_date AS date',
                                    'tempout',
                                    'tempoutmax',
                                    'tempoutmin',
                                    'humout',
                                    $leafTemp . ' AS leaftemp',
                                    $leafHum . ' AS leafhum',
                                    'total_rainrate',
                                    'total_' . $leafHum . ' AS processed_records',
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.{$leafHum} > 0
                                        ) as records_leafhum
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.windspeed > 0
                                        ) as records_windspeed_0
                                    "),
                                    DB::raw("
                                        (SELECT COUNT(*) FROM data
                                        WHERE data.station_id = daily_data.station_id
                                        AND DATE(data.receipt_date) = daily_data.registration_date
                                        AND data.rainrate > 0
                                        ) as records_rainrate_0
                                    "),
                                    'windspeed',
                                    'windspeedhi',
                                    'raintotal'
                                )
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $this->selectedFromDate)
                                ->whereDate('daily_data.registration_date', '<=', $this->selectedToDate)
                                ->orderBy('registration_date', 'desc')
                                ->paginate($this->perPage);
                            $data->getCollection()->transform(function ($item) use($measureSystem) {
                                $item->date = Carbon::parse($item->date)->format('d/m/Y');
                                $item->conditions = '';

                                $item->conditions = __('messages.early_warning.conditions.no');
                                $item->conditions_sw = 0;
                                $item->infection_hours = (($item->records_leafhum*$item->processed_records)/144)/6;
                                $item->wind_hours = (($item->records_windspeed_0*$item->processed_records)/144)/6;;
                                $item->rain_hours = (($item->records_rainrate_0*$item->total_rainrate)/144)/6;;
                                if ((($item->wind_hours>=6) && ($item->rain_hours>=6)) || (($item->tempout>=20) && ($item->tempout<=26))) {
                                    $item->conditions_sw = 1;
                                    $item->conditions = __('messages.early_warning.conditions.yes');
                                }
                                $item->infection_hours = $item->infection_hours > 6 ? $item->infection_hours - 6 : 0;
                                $item->infection_hours = number_format($item->infection_hours, 1);

                                $item->tempoutmax = number_format(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                                $item->tempoutmin = number_format(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                                $item->tempout = number_format(Utils::convertTemp($item->tempout, $measureSystem), 1);
                                $item->humout = number_format($item->humout, 1);
                                $item->windspeed = number_format(Utils::convertSpeed($item->windspeed, $measureSystem), 1);
                                $item->windspeedhi = number_format(Utils::convertSpeed($item->windspeedhi, $measureSystem), 1);
                                $item->raintotal = number_format(Utils::convertLength($item->raintotal, $measureSystem), 2);
                                return $item;
                            });
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function render()
    {
        $data = $this->getData();
        if ($this->canExecQuery)
            $this->dispatch('showData');
        return view('livewire.early-warning.early-warning', ['data' => $data]);
    }

    public function getSugarCane1Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.date'),
                'unit' => '',
            ],
            'infection_hours' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.infection_hours'),
                'unit' => '',
            ],
            'tempout' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.avg_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'humout' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.avg_hum'),
                'unit' => '(%)',
            ],
            'hours_hum_90' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.hours_hum_90'),
                'unit' => '(%)',
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'conditions' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.conditions'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.sugarcane_1.processed_records'),
                'unit' => '',
            ]
        ];
    }

    public function getSoyBean1Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.date'),
                'unit' => '',
            ],
            'tempoutmax' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.max_temp'),
                'unit' =>  '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'tempoutmin' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.min_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'wet_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.wet_hours'),
                'unit' => '',
            ],
            'wet_temp' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.wet_temp'),
                'unit' =>  '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'humout' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.avg_hum'),
                'unit' => '(%)'
            ],
            'hours_hum_90' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.hours_hum_90'),
                'unit' => ''
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'rating' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.rating'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.soybean_1.processed_records'),
                'unit' => '',
            ],
        ];
    }

    public function getSoyBean2Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.date'),
                'unit' => '',
            ],
            'tempoutmax' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.max_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'tempoutmin' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.min_temp') ,
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'wet_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.wet_hours'),
                'unit' => '',
            ],
            'wet_temp' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.wet_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'humout' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.avg_hum'),
                'unit' => '(%)',
            ],
            'hours_hum_90' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.hours_hum_90'),
                'unit' => ''
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'windspeed' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.avg_wind'),
                'unit' => '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'windspeedhi' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.gust'),
                'unit' => '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'conditions' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.conditions'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.soybean_2.processed_records'),
                'unit' => '',
            ],
        ];
    }

    public function getSoyBean3Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.date'),
                'unit' => '',
            ],
            'infection_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.infection_hours'),
                'unit' => '',
            ],
            'germination_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.germination_hours'),
                'unit' => '',
            ],
            'windspeed' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.avg_wind'),
                'unit' =>  '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'windspeedhi' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.gust'),
                'unit' =>  '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'conditions' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.conditions'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.soybean_3.processed_records'),
                'unit' => '',
            ],
        ];
    }

    public function getSoyBean4Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.date'),
                'unit' => '',
            ],
            'infection_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.infection_hours'),
                'unit' => '',
            ],
            'tempout' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.avg_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'humout' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.avg_hum'),
                'unit' => '(%)',
            ],
            'windspeed' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.avg_wind'),
                'unit' => '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'windspeedhi' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.gust'),
                'unit' => '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'conditions' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.conditions'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.soybean_4.processed_records'),
                'unit' => '',
            ],
        ];
    }

    public function getSoyBean5Columns() {
        $measureSystem = session('measure', 'metric');
        return [
            'date' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.date'),
                'unit' => '',
            ],
            'infection_hours' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.infection_hours'),
                'unit' => '',
            ],
            'tempout' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.avg_temp'),
                'unit' => '(°' . Utils::getDegreeUnit($measureSystem) . ')',
            ],
            'humout' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.avg_hum'),
                'unit' => '(%)',
            ],
            'windspeed' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.avg_wind'),
                'unit' =>  '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'windspeedhi' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.gust'),
                'unit' => '('. Utils::getSpeedUnit($measureSystem) .')',
            ],
            'raintotal' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.rain'),
                'unit' => '('. Utils::getLengthUnit($measureSystem) .')',
            ],
            'conditions' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.conditions'),
                'unit' => '',
            ],
            'processed_records' => [
                'label' => __('messages.early_warning.data_columns.soybean_5.processed_records'),
                'unit' => '',
            ],
        ];
    }

}
