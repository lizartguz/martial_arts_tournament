<?php

namespace App\Livewire\Viewer;

use Livewire\Component;
use App\Models\StationM;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MoreDataModal extends Component
{
    public $stationId = null;
    public $dataForSteelseries = [];
    public $showContent = false;
    
    // Filtros para gráficos
    public $selectedFromDate;
    public $selectedToDate;
    public $formatType = 'all';
    public $variable = 'tempout';
    public $chartData = [];

    protected $listeners = ['openMoreDataModal', 'closeMoreDataModal'];

    public function openMoreDataModal($stationId)
    {
        if (!$stationId) {
            return;
        }
        
        // Resetear estado
        $this->showContent = false;
        $this->stationId = $stationId;
        $this->dataForSteelseries = [];
        
        // Inicializar fechas
        date_default_timezone_set('America/La_Paz');
        $this->selectedFromDate = date('Y-m-d');
        $this->selectedToDate = date('Y-m-d');
        $this->formatType = 'all';
        $this->variable = 'tempout';
        
        // Cargar datos
        $this->loadStationData($stationId);
        
        // Cargar gráfico inicial
        $this->viewGraph();
        
        // Mostrar contenido
        $this->showContent = true;
    }
    
    public function closeMoreDataModal()
    {
        $this->showContent = false;
    }
    
    private function loadStationData($stationId)
    {
        $data = StationM::select(
            'stations.id as stationId', 'stations.name', 'stations.location', 'stations.latitude', 
            'stations.longitude', 'stations.state as stateStation', 'dd.tempout','dd.humout', 
            'dd.windspeed', 'dd.raintotal','dd.tempoutmin','dd.tempoutmax','dd.dewptout','dd.press',
            'dd.winddir','dd.windspeed2','dd.winddir2','dd.windspeedhi','dd.windspeedhi2',
            'dd.rainrate','dd.uvindex','dd.solevo','dd.soiltemp1','dd.soiltemp2',
            'dd.soilhum1','dd.soilhum2','dd.leaftemp1','dd.leaftemp2',
            'dd.leafhum1','dd.leafhum2','dd.pm10','dd.pm2_5','dd.pm1','dd.gas_ppm','dd.receipt_date'
        )
        ->join('current_data as dd', 'dd.station_id', 'stations.id')
        ->where('stations.id', $stationId)
        ->first();
        
        // Convertir a array y reemplazar nulls con 'NA'
        $this->dataForSteelseries = collect($data ? $data->toArray() : [])->map(function ($value) {
            return $value ?? 0;
        })->toArray();
    }
    
    public function handleClick($variable)
    {
        $this->variable = $variable;
        $this->viewGraph();
    }
    
    public function viewGraph()
    {
        if (empty($this->selectedFromDate) || empty($this->selectedToDate)) {
            $this->dispatch('failedAlert', ['failed' => __('messages.viewer.more_data.alerts.invalid_dates')]);
            return;
        }    
       
        if ($this->selectedFromDate > $this->selectedToDate) {
            $this->dispatch('failedAlert', ['failed' => __('messages.viewer.more_data.alerts.start_after_end')]);
            return;
        }
        
        $fromDate = Carbon::parse($this->selectedFromDate)->startOfDay();
        $toDate = Carbon::parse($this->selectedToDate)->endOfDay();
    
        $monthsDifference = $fromDate->diffInMonths($toDate);
        if ($monthsDifference > 3) {
            $this->dispatch('failedAlert', ['failed' => __('messages.viewer.more_data.alerts.range_exceeded')]);
            return;
        }
       
        if($this->formatType == "all"){        
            $this->getAllData();
        } else if($this->formatType == "daily"){
            $this->getDailyData();
        }
    }
    
    public function getAllData()
    {
        date_default_timezone_set('America/La_Paz');
        $fromDate = Carbon::parse($this->selectedFromDate)->startOfDay();
        $toDate = Carbon::parse($this->selectedToDate)->endOfDay();
        $year = $fromDate->format('Y');
        
        if (!config("database.connections.senvatec_db_{$year}")) {
            $this->dispatch('failedAlert', ['failed' => __('messages.viewer.more_data.alerts.missing_year_data', ['year' => $year])]);
            return;
        }

        $records = DB::connection('senvatec_db_' . $year)
            ->table('senva_data')
            ->select('station_id', 'receipt_date', $this->variable)
            ->where('station_id', $this->stationId)
            ->whereBetween('receipt_date', [$fromDate, $toDate])
            ->orderBy('receipt_date', 'asc')
            ->get();        
        
        $titleVariable = $this->getTitleVariable();
        
        $this->chartData = [
            'title' => $titleVariable,
            'labels' => $records->pluck('receipt_date')->unique()->values()->toArray(),
            'data' => $records->pluck($this->variable)->toArray()
        ]; 

        $this->dispatch('updateChart', $this->chartData);
    }
    
    public function getDailyData()
    {
        $fromDate = Carbon::parse($this->selectedFromDate);
        $toDate = Carbon::parse($this->selectedToDate);
        $year = $fromDate->format('Y');
        
        if (!config("database.connections.senvatec_db_{$year}")) {
            $this->dispatch('failedAlert', ['failed' => __('messages.viewer.more_data.alerts.missing_year_data', ['year' => $year])]);
            return;
        }
        
        $records = DB::connection('senvatec_db_' . $year)
            ->table('senva_data')
            ->select(
                DB::raw('DATE(receipt_date) as receipt_date'),
                DB::raw("AVG({$this->variable}) as {$this->variable}")
            )
            ->where('station_id', $this->stationId)
            ->whereBetween('receipt_date', [$fromDate, $toDate])
            ->groupBy(DB::raw('DATE(receipt_date)'))
            ->orderBy('receipt_date', 'asc')
            ->get();
        
        $titleVariable = $this->getTitleVariable();
        
        $this->chartData = [
            'title' => $titleVariable . __('messages.viewer.more_data.chart.daily_suffix'),
            'labels' => $records->pluck('receipt_date')->toArray(),
            'data' => $records->pluck($this->variable)->toArray()
        ];
        
        $this->dispatch('updateChart', $this->chartData);
    }
    
    private function getTitleVariable()
    {
        return match($this->variable) {
            'tempout' => __('messages.viewer.more_data.variables.tempout'),
            'humout' => __('messages.viewer.more_data.variables.humout'),
            'raintotal' => __('messages.viewer.more_data.variables.raintotal'),
            'dewptout' => __('messages.viewer.more_data.variables.dewptout'),
            'winddir' => __('messages.viewer.more_data.variables.winddir'),
            'windspeed' => __('messages.viewer.more_data.variables.windspeed'),
            'uvindex' => __('messages.viewer.more_data.variables.uvindex'),
            'press' => __('messages.viewer.more_data.variables.press'),
            'solevo' => __('messages.viewer.more_data.variables.solevo'),
            'soiltemp1' => __('messages.viewer.more_data.variables.soiltemp1'),
            'soilhum1' => __('messages.viewer.more_data.variables.soilhum1'),
            'leafhum1' => __('messages.viewer.more_data.variables.leafhum1'),
            default => __('messages.viewer.more_data.variables.default'),
        };
    }

    public function render()
    {
        return view('livewire.viewer.more-data-modal');
    }
}

