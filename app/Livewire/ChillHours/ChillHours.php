<?php

namespace App\Livewire\ChillHours;

use App\Models\StationM;
use App\Utils\Utils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ChillHours extends Component
{
    use WithPagination;

    public $monthList = [];
    public $stationList = [];
    public $rangeList = [];

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
    public $totalCH;
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
            'daily' => __('messages.chill_hours.range_daily_label'),
            'monthly' => __('messages.chill_hours.range_monthly_label'),
            'annual' => __('messages.chill_hours.range_annual_label'),
        ];

        $measureSystem = session('measure', 'metric');
        $this->columnsName = [
            'registration_date' => [
                'label' => __('messages.chill_hours.data_columns.date'),
                'unit' => '',
            ],
            'tempout' => [
                'label' => __('messages.chill_hours.data_columns.avg_temp'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
            ],
            'tempoutmax' => [
                'label' => __('messages.chill_hours.data_columns.max_temp'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
            ],
            'tempoutmin' => [
                'label' => __('messages.chill_hours.data_columns.min_temp'),
                'unit' => '(°'.Utils::getDegreeUnit($measureSystem).')',
            ],
            'chillhours' => [
                'label' => __('messages.chill_hours.data_columns.chill_hours'),
                'unit' => '',
            ],
        ];

        $this->stationList = StationM::select('stations.id', 'stations.name')
            ->where('stations.state', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function processParams($selectedStation, $selectedRange, $selectedFromDate, $selectedToDate, $selectedYear, $selectedMonth) {
        $this->selectedStation = $selectedStation;
        $this->selectedRange = $selectedRange;
        $this->selectedFromDate = $selectedFromDate;
        $this->selectedToDate = $selectedToDate;
        $this->selectedYear = $selectedYear;
        $this->selectedMonth = $selectedMonth;

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
            //     $this->totalCH = DB::connection('db_guest_h' . $year)
            //         ->table('data')
            
            // Nueva conexión
            if (config("database.connections.senvatec_db_{$year}")) { //si existe config DB para ese año
                if ($this->runOnce === true) {
                    $this->hasData = false;
                    $this->totalCH = DB::connection('senvatec_db_' . $year)
                        ->table('senva_data')
                        ->where('station_id', $this->selectedStation)
                        ->where('tempout', '<=', 7.2)
                        ->whereDate('receipt_date', '>=', $fromDate)
                        ->whereDate('receipt_date', '<=', $toDate)
                        ->select(DB::raw('COUNT(DISTINCT CONCAT(DATE(receipt_date), HOUR(receipt_date))) AS total'))
                        ->value('total');
                    $this->hasData = ($this->totalCH > 0);
                    $this->runOnce = false;
                    $this->totalCH = number_format($this->totalCH, 1);
                };
                if ($this->hasData) {
                    $measureSystem = session('measure', 'metric');
                    
                    // Conexión antigua (comentado)
                    // $data = DB::connection('db_guest_h' . $year)
                    //     ->table('daily_data')
                    //     ->joinSub(
                    //         DB::connection('db_guest_h' . $year)
                    //             ->table('data')
                    //             ...
                    //             ->groupBy(DB::connection('db_guest_h' . $year)->raw('DATE(receipt_date)')),
                    
                    // Nueva conexión
                    $data = DB::connection('senvatec_db_' . $year)
                        ->table('senva_daily_data')
                        ->joinSub(
                            DB::connection('senvatec_db_' . $year)
                                ->table('senva_data')
                                ->selectRaw('DATE(receipt_date) AS F_DATE, COUNT(DISTINCT HOUR(receipt_date)) AS chillhours')
                                ->where('station_id', $this->selectedStation)
                                ->where('tempout', '<=', 7.2)
                                ->whereDate('receipt_date', '>=', $fromDate)
                                ->whereDate('receipt_date', '<=', $toDate)
                                ->groupBy(DB::connection('senvatec_db_' . $year)->raw('DATE(receipt_date)')),
                            'V_CHILLHOURS',
                            function ($join) use($year) {
                                $join->on(DB::connection('senvatec_db_' . $year)->raw('DATE(daily_data.registration_date)'), '=', 'V_CHILLHOURS.F_DATE');
                            }
                        )
                        ->select('daily_data.registration_date', 'daily_data.tempout', 'daily_data.tempoutmax', 'daily_data.tempoutmin', 'V_CHILLHOURS.chillhours')
                        ->where('daily_data.station_id', $this->selectedStation)
                        ->whereDate('daily_data.registration_date', '>=', $fromDate)
                        ->whereDate('daily_data.registration_date', '<=', $toDate)
                        ->orderBy('daily_data.registration_date', 'ASC')
                        ->paginate($this->perPage);

                    $data->getCollection()->transform(function ($item) use($measureSystem) {
                        $item->registration_date = Carbon::parse($item->registration_date)->format('d/m/Y');
                        $item->tempout = round(Utils::convertTemp($item->tempout, $measureSystem), 1);
                        $item->tempoutmax = round(Utils::convertTemp($item->tempoutmax, $measureSystem), 1);
                        $item->tempoutmin = round(Utils::convertTemp($item->tempoutmin, $measureSystem), 1);
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

        return view('livewire.chill-hours.chill-hours', ['data' => $data]);
    }
}
