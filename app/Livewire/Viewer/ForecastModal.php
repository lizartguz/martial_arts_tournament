<?php

namespace App\Livewire\Viewer;

use Livewire\Component;
use App\Livewire\Viewer\ForecastViewer;

class ForecastModal extends Component
{
    public $stationId = null;
    public $stationName = null;
    public $modalKey = 0;
    public $show = false;

    protected $listeners = ['openForecastModal', 'resetForecastModal'];

    public function openForecastModal(array $data)
    {
        $stationId   = $data['stationId'] ?? null;
        $stationName = $data['stationName'] ?? null;

        // Incrementar key para forzar nuevo componente
        $this->modalKey++;
        $this->stationId   = $stationId;
        $this->stationName = $stationName;
        $this->show        = true;

        // Emitir evento al ForecastViewer para que cargue los datos
        $this->dispatch('loadForecasts', ['stationId' => $stationId])->to(ForecastViewer::class);
    }

    public function closeForecastModal()
    {
        $this->show = false;
    }

    public function resetForecastModal()
    {
        // No resetear a null para evitar errores de hidratación
        // Solo limpiar los nombres
        $this->stationName = null;
    }

    public function render()
    {
        return view('livewire.viewer.forecast-modal');
    }
}

