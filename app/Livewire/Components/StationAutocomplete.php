<?php

namespace App\Livewire\Components;

use App\Models\StationM;
use App\Models\UserDependencyM;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Autocomplete de estaciones con búsqueda server-side (escalable a miles).
 *
 * Componente aislado: al escribir solo se re-renderiza este componente (payload
 * mínimo), sin tocar el componente padre ni sus filtros. Mantiene un <input
 * type="hidden" id="{inputId}"> para que el JS existente que lee por DOM
 * (p. ej. checkParams() en /downloads) siga funcionando sin cambios.
 */
class StationAutocomplete extends Component
{
    /** id del <input> oculto que el JS del padre lee por DOM. */
    public string $inputId = 'station-select';
    public string $placeholder = '';

    public ?int $selectedId = null;
    public ?string $selectedName = null;
    public string $search = '';
    public array $results = [];

    public function mount($selectedId = null, $selectedName = null, string $inputId = 'station-select', string $placeholder = ''): void
    {
        $this->inputId = $inputId;
        $this->placeholder = $placeholder;
        $this->selectedId = ($selectedId !== null && $selectedId !== '') ? (int) $selectedId : null;

        if ($this->selectedId) {
            $this->selectedName = $selectedName ?: optional(StationM::find($this->selectedId))->name;
            $this->search = (string) $this->selectedName;
        }
    }

    /**
     * Hook de Livewire al actualizar el buscador (con debounce desde el front).
     * Mínimo 2 caracteres + LIMIT 25 → payload mínimo, una sola query.
     */
    public function updatedSearch(): void
    {
        $q = trim($this->search);

        if (mb_strlen($q) < 2) {
            $this->results = [];
            return;
        }

        $this->results = $this->accessibleStationsQuery()
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name', 'asc')
            ->limit(25)
            ->get(['id', 'name'])
            ->toArray();
    }

    /**
     * Fija la estación elegida. Solo recibe el id (el nombre se resuelve aquí
     * para evitar problemas de comillas al pasar strings por el atributo).
     */
    public function selectStation($id): void
    {
        $id = (int) $id;
        $name = collect($this->results)->firstWhere('id', $id)['name']
            ?? optional(StationM::find($id))->name;

        $this->selectedId = $id;
        $this->selectedName = $name;
        $this->search = (string) $name;
        $this->results = [];

        // Para reutilización: avisar a quien lo necesite (opcional, sin listener obligatorio).
        $this->dispatch('stationSelected', id: $id, name: $name);
    }

    /**
     * Query base de estaciones accesibles según el rol del usuario.
     */
    private function accessibleStationsQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['super_manager', 'manager', 'meteorology', 'meteorologist', 'technical'])) {
            return StationM::query()->where('state', true);
        }

        if ($user->hasAnyRole(['subscriber'])) {
            $stationIds = UserDependencyM::join('users_subscriptions as us', 'us.id', 'user_dependency.user_subscription_id')
                ->join('users as u', 'user_dependency.dependent_user_id', 'u.id')
                ->where('u.id', $user->id)
                ->whereIn('u.user_type', [1, 2])
                ->pluck('us.station_id');

            return StationM::query()->where('state', true)->whereIn('id', $stationIds);
        }

        // Sin acceso a estaciones.
        return StationM::query()->whereRaw('1 = 0');
    }

    public function render()
    {
        return view('livewire.components.station-autocomplete');
    }
}
