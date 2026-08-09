<?php

namespace App\Livewire\Viewer;

use Livewire\Component;
use App\Models\AlertM;

class AlertsViewer extends Component
{
    public $stationId;
    public $alerts = [];

    protected $listeners = ['loadAlerts'];

    public function mount($stationId = null)
    {
        // NO cargar datos automáticamente en mount
        // Solo cuando se reciba el evento loadAlerts
        $this->stationId = $stationId;
    }

    public function loadAlerts($stationId)
    {
        // Validar que el stationId no sea nulo
        if (!$stationId) {
            return;
        }
        
        $this->stationId = $stationId;
        $this->alerts = AlertM::where('station_id', $stationId)
            ->orderBy('reg_date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reg_date' => $item->reg_date,
                    'description' => $item->description ?? $item->message,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.viewer.alerts-viewer');
    }
}
