<?php

namespace App\Livewire\Viewer;

use DateTime;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\StationM;
use App\Models\ForecastM;
use App\Models\AlertM;
use Illuminate\Support\Facades\DB;

class StationInfoModal extends Component
{
    public $stationId = null;
    public $stationData = null;
    public $showContent = false;
    
    // Datos de pronóstico
    public $tempoutF = null;
    public $tempout_max_dayF = null;
    public $tempout_min_dayF = null;
    public $icon_imageF = null;
    public $dataForecastsCurrent = [];
    public $dataForecasts = [];
    
    // Datos de alertas
    public $alerts = [];

    protected $listeners = ['openStationInfoModal', 'closeModal'];

    public function openStationInfoModal($stationId)
    {
        if (!$stationId) {
            return;
        }
        
        // Resetear completamente el estado
        $this->showContent = false;
        $this->stationId = $stationId;
        $this->stationData = null;
        $this->tempoutF = null;
        $this->tempout_max_dayF = null;
        $this->tempout_min_dayF = null;
        $this->icon_imageF = null;
        $this->dataForecastsCurrent = [];
        $this->dataForecasts = [];
        $this->alerts = [];
        
        // Cargar datos frescos
        $this->loadStationData($stationId);
        $this->loadForecasts($stationId);
        $this->loadAlerts($stationId);
        
        // Mostrar contenido
        $this->showContent = true;
    }
    
    public function closeModal()
    {
        $this->showContent = false;
    }
    
    private function loadStationData($stationId)
    {
        $data = StationM::select(
            'stations.id as stationId', 
            'stations.name', 
            'stations.location', 
            'stations.latitude', 
            'stations.longitude', 
            'dd.tempout',
            'dd.humout', 
            'dd.windspeed', 
            'dd.raintotal',
            'dd.dewptout',
            'dd.press',
            'dd.winddir',
            'dd.uvindex',
            'dd.receipt_date'
        )
        ->join('current_data as dd', 'dd.station_id', 'stations.id')
        ->where('stations.id', $stationId)
        ->first();
        
        // Convertir a array para evitar problemas de hidratación
        $this->stationData = $data ? (object) $data->toArray() : null;
    }
    
    private function loadForecasts($stationId)
    {
        $dataAux = ForecastM::where('station_id', $stationId)            
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, reg_date, ?))', [Carbon::now()])
            ->first();
        
        if ($dataAux) {
            $this->tempoutF = $dataAux->t2m;
            $this->tempout_max_dayF = $dataAux->tmax;
            $this->tempout_min_dayF = $dataAux->tmin;
            $this->icon_imageF = strrev($dataAux->icon_image);
        }
        
        // Pronósticos del día actual - CONVERTIR A ARRAY
        $this->dataForecastsCurrent = ForecastM::where('station_id', $stationId)
            ->select('id', 'reg_date', 't2m', 'hr2m', 'prec', 'icon_image', 'v10m', 'v10m_dir', 'v10m_max', 'baro', 'cloud', 'dp', 'tmax', 'tmin')
            ->whereBetween('reg_date', [Carbon::now(), Carbon::now()->endOfDay()])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reg_date' => $item->reg_date,
                    't2m' => $item->t2m,
                    'hr2m' => $item->hr2m,
                    'prec' => $item->prec,
                    'icon_image' => strrev($item->icon_image),
                    'v10m' => $item->v10m,
                    'v10m_dir' => $item->v10m_dir,
                    'v10m_max' => $item->v10m_max,
                    'baro' => $item->baro,
                    'cloud' => $item->cloud,
                    'dp' => $item->dp,
                    'tmax' => $item->tmax,
                    'tmin' => $item->tmin,
                    'formatted_time' => Carbon::parse($item->reg_date)->format('H:i')
                ];
            })
            ->toArray();
        
        // Pronósticos extendidos - CONVERTIR A ARRAY
        $this->dataForecasts = ForecastM::where('station_id', $stationId)
            ->select('id', 'reg_date', 't2m', 'hr2m', 'prec', 'icon_image', 'v10m', 'v10m_dir', 'v10m_max', 'baro', 'cloud', 'dp', 'tmax', 'tmin')
            ->whereDate('reg_date', '>', Carbon::today())
            ->take(20)
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->reg_date)->format('d-m');
            })
            ->map(function ($itemsByDate, $date) {
                return [
                    'date' => $date,
                    'records' => $itemsByDate->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'reg_date' => $item->reg_date,
                            't2m' => $item->t2m,
                            'hr2m' => $item->hr2m,
                            'prec' => $item->prec,
                            'icon_image' => strrev($item->icon_image),
                            'v10m' => $item->v10m,
                            'v10m_dir' => $item->v10m_dir,
                            'v10m_max' => $item->v10m_max,
                            'baro' => $item->baro,
                            'cloud' => $item->cloud,
                            'dp' => $item->dp,
                            'tmax' => $item->tmax,
                            'tmin' => $item->tmin,
                            'formatted_time' => Carbon::parse($item->reg_date)->format('H:i')
                        ];
                    })->toArray()
                ];
            })
            ->toArray();
    }
    
    private function loadAlerts($stationId)
    {
        // CONVERTIR A ARRAY para evitar problemas de hidratación
        $this->alerts = AlertM::where('station_id', $stationId)
            ->orderBy('reg_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'station_id' => $item->station_id,
                    'reg_date' => $item->reg_date,
                    'message' => $item->message,
                    'type' => $item->type ?? 'info',
                    'status' => $item->status ?? 'active'
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.viewer.station-info-modal');
    }
}
