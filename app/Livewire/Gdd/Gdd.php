<?php

namespace App\Livewire\Gdd;

use App\Models\StationM;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Gdd extends Component
{
    use WithPagination;

    public $monthList = [];
    public $stationList = [];
    public $rangeList = [];
    public $baseList = [];

    public $selectedStation;
    public $selectedRange;
    public $selectedFromDate;
    public $selectedToDate;
    public $selectedYear;
    public $selectedMonth;
    public $selectedBase;
    public $showResults = false;
    public $hasData = false;
    public $canExecQuery = false;
    public $totalGDD;
    protected $runOnce = true;

    public $maxDate;
    public $minDate;
    public $maxYear;
    public $minYear;
    public $columnsName = [];

    protected $paginationTheme = 'tailwind';
    protected $listeners = ['processParams'];
    protected $perPage = 20;

    public function mount()
    {
        $this->minDate = Carbon::create('2011-01-01');
        $this->maxDate = Carbon::now();
        $this->minYear = $this->minDate->format('Y');
        $this->maxYear = $this->maxDate->format('Y');

//        $this->selectedRange = 'anual';
        $this->selectedYear = $this->maxDate->format('Y');
        $this->selectedMonth = $this->maxDate->format('m');

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

        $this->rangeList = [
            'daily' => __('messages.gdd.range_daily_label'),
            'monthly' => __('messages.gdd.range_monthly_label'),
            'annual' => __('messages.gdd.range_annual_label'),
        ];

        $measureSystem = session('measure', 'metric');

        if ($measureSystem == 'metric') {
            $this->baseList = [
                '0' => '0',
                '5' => '5',
                '10' => '10',
                '12' => '12',
                '15' => '15',
                '18' => '18',
                '20' => '20',
            ];
        } else if ($measureSystem == 'imperial') {
            $this->baseList = [
                '32' => '32',
                '41' => '41',
                '50' => '50',
                '54' => '54',
                '59' => '59',
                '64' => '64',
                '68' => '68',
            ];
        }

        $this->columnsName = [
            'registration_date' => [
                'label' => __('messages.gdd.data_columns.date'),
                'unit' => '',
            ],
            'tempoutmax' => [
                'label' => __('messages.gdd.data_columns.max_temp'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
            ],
            'tempoutmin' => [
                'label' => __('messages.gdd.data_columns.min_temp'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
            ],
            'gdd' => [
                'label' => __('messages.gdd.data_columns.gdd'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).'/'.__('messages.day').')',
            ],
            'records' => [
                'label' => __('messages.gdd.data_columns.daily_records'),
                'unit' => '',
            ]
        ];

        $this->stationList = StationM::select('stations.id', 'stations.name')
            ->where('stations.state', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function processParams($selectedStation, $selectedRange, $selectedFromDate, $selectedToDate, $selectedYear, $selectedMonth, $selectedBase) {
        $this->selectedStation = $selectedStation;
        $this->selectedRange = $selectedRange;
        $this->selectedFromDate = $selectedFromDate;
        $this->selectedToDate = $selectedToDate;
        $this->selectedYear = $selectedYear;
        $this->selectedMonth = $selectedMonth;
        $this->selectedBase = $selectedBase;

        $this->resetPage();
        $this->runOnce = true;
        $this->canExecQuery = true;
        $this->showResults = true;
    }

    protected function getData() {
        $data = null;
        $fromDate = $this->selectedFromDate;
        $toDate = $this->selectedToDate;
        if ($this->canExecQuery) {
            if ($this->selectedRange == 'daily') {
                $year = Carbon::create($this->selectedFromDate)->format('Y');
            } else if ($this->selectedRange == 'monthly') {
                $fromDate = Carbon::parse($this->selectedYear . '-'.$this->selectedMonth.'-01');
                $year = $this->selectedYear;
                $toDate = $fromDate->copy()->endOfMonth();
            } else {
                $year = $this->selectedYear;
                $fromDate = Carbon::parse($this->selectedYear . '-01-01');
                $toDate = $fromDate->copy()->endOfYear();

            }
            
            // Conexión antigua (comentado)
            // if (config("database.connections.db_guest_h$year")) {
            //     DB::connection('db_guest_h' . $year)
            //         ->table('daily_data')
            
            // Nueva conexión
            if (config("database.connections.senvatec_db_{$year}")) { //si existe config DB para ese año
                $measureSystem = session('measure', 'metric');
                if ($this->runOnce === true) {
                    $this->hasData = false;
                    $this->hasData = DB::connection('senvatec_db_' . $year)
                        ->table('senva_daily_data')
                        ->where('daily_data.station_id', $this->selectedStation)
                        ->whereDate('daily_data.registration_date', '>=', $fromDate)
                        ->whereDate('daily_data.registration_date', '<=', $toDate)
                        ->exists();
                    if ($this->hasData) {
                        $this->totalGDD = 0;
                        if ($measureSystem == 'metric') {
                            // Conexión antigua (comentado)
                            // $totalData = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $totalData = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->selectRaw("SUM(IF(((tempoutmax + tempoutmin) / 2) - {$this->selectedBase} > 0, ((tempoutmax + tempoutmin) / 2) - {$this->selectedBase}, 0)) as total")
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $fromDate)
                                ->whereDate('daily_data.registration_date', '<=', $toDate)
                                ->value('total');
                        } else if ($measureSystem == 'imperial') {
                            // Conexión antigua (comentado)
                            // $totalData = DB::connection('db_guest_h' . $year)
                            //     ->table('daily_data')
                            
                            // Nueva conexión
                            $totalData = DB::connection('senvatec_db_' . $year)
                                ->table('senva_daily_data')
                                ->selectRaw("SUM(IF(((((tempoutmax*9/5)+32)+((tempoutmin*9/5)+32))/2) - {$this->selectedBase} > 0, ((((tempoutmax*9/5)+32)+((tempoutmin*9/5)+32))/2) - {$this->selectedBase}, 0)) as total")
                                ->where('daily_data.station_id', $this->selectedStation)
                                ->whereDate('daily_data.registration_date', '>=', $fromDate)
                                ->whereDate('daily_data.registration_date', '<=', $toDate)
                                ->value('total');

                        }
                        $this->runOnce = false;
                        $this->totalGDD = round($totalData, 1);
                    }
                };
                if ($this->hasData) {
                    // Conexión antigua (comentado)
                    // $data = DB::connection('db_guest_h' . $year)
                    //     ->table('daily_data')
                    
                    // Nueva conexión
                    $data = DB::connection('senvatec_db_' . $year)
                        ->table('senva_daily_data')
                        ->select('registration_date', 'tempout', 'tempoutmax', 'tempoutmin', 'total_tempout AS records')
                        ->where('daily_data.station_id', $this->selectedStation)
                        ->whereDate('daily_data.registration_date', '>=', $fromDate)
                        ->whereDate('daily_data.registration_date', '<=', $toDate)
                        ->orderBy('registration_date', 'desc')
                        ->paginate($this->perPage);
                    $data->getCollection()->transform(function ($item) use($measureSystem) {
                        $item->registration_date = Carbon::parse($item->registration_date)->format('Y/m/d');
                        $item->tempoutmax = round(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                        $item->tempoutmin = round(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
                        $item->gdd = round((($item->tempoutmax + $item->tempoutmin)/2) - $this->selectedBase, 1);
                        if ($item->gdd < 0) {
                            $item->gdd = 0;
                        }
                        return $item;
                    });
                }
            }
        }
        return $data;
    }

    public function render()
    {
        $data = $this->getData();
        if ($this->canExecQuery) {
            $this->dispatch('showData');
        }
        return view('livewire.gdd.gdd', ['data' => $data]);
    }
}
