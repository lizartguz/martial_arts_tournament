<?php

namespace App\Livewire\Viewer;

use Livewire\Component;
use App\Models\StationM;
use App\Livewire\Viewer\AlertsViewer;

class AlertsModal extends Component
{
    public $stationId = null;
    public $stationName = null;
    public $modalKey = 0;
    public $show = false;

    protected $listeners = ['openAlertModal', 'resetAlertModal'];

    public function openAlertModal($stationId, $stationName = null)
    {
        if (!$stationId) {
            return;
        }

        $this->modalKey++;
        $this->stationId = $stationId;
        $this->stationName = $stationName ?? StationM::where('id', $stationId)->value('name');
        $this->show = true;

        $this->dispatch('loadAlerts', ['stationId' => $stationId])->to(AlertsViewer::class);
    }

    public function closeAlertModal()
    {
        $this->show = false;
    }

    public function resetAlertModal()
    {
        $this->stationName = null;
    }

    public function render()
    {
        return view('livewire.viewer.alerts-modal');
    }
}


