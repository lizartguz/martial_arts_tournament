<?php

namespace App\Livewire\LogManager;

use App\Services\LogReaderService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class LogManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $level = '';
    public string $fromDate = '';
    public string $toDate = '';
    public int $perPage = 25;
    public string $clearConfirmation = '';
    public ?array $selectedEntry = null;
    public bool $showClearModal = false;
    public ?array $entryToDelete = null;
    public bool $showDeleteEntryModal = false;

    public array $levels = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];

    protected $paginationTheme = 'tailwind';

    /**
     * Reinicia la paginacion cuando cambian los filtros visibles.
     */
    public function updated($property): void
    {
        if (in_array($property, ['search', 'level', 'fromDate', 'toDate', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Limpia los filtros y restaura la paginacion por defecto.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'level', 'fromDate', 'toDate']);
        $this->perPage = 25;
        $this->resetPage();
    }

    /**
     * Abre el detalle de una entrada seleccionada del log.
     */
    public function openEntry(string $entryId): void
    {
        $entry = collect($this->filteredEntries())->firstWhere('id', $entryId);

        $this->selectedEntry = $entry ?: null;
    }

    /**
     * Cierra el modal de detalle de una entrada.
     */
    public function closeEntry(): void
    {
        $this->selectedEntry = null;
    }

    /**
     * Muestra la confirmacion para limpiar todo el log.
     */
    public function confirmClear(): void
    {
        $this->clearConfirmation = '';
        $this->showClearModal = true;
    }

    /**
     * Cancela la limpieza total del log.
     */
    public function cancelClear(): void
    {
        $this->clearConfirmation = '';
        $this->showClearModal = false;
    }

    /**
     * Muestra la confirmacion para eliminar una entrada especifica.
     */
    public function confirmDeleteEntry(string $entryId): void
    {
        $entry = collect($this->filteredEntries())->firstWhere('id', $entryId);

        if (! $entry) {
            session()->flash('success', __('messages.logs.entry_not_found'));
            return;
        }

        $this->entryToDelete = $entry;
        $this->showDeleteEntryModal = true;
    }

    /**
     * Cancela la eliminacion individual de una entrada.
     */
    public function cancelDeleteEntry(): void
    {
        $this->entryToDelete = null;
        $this->showDeleteEntryModal = false;
    }

    /**
     * Elimina la entrada seleccionada sin crear un nuevo registro de auditoria.
     */
    public function deleteEntry(): void
    {
        if (! auth()->user()?->can('administration.logs.view')) {
            abort(403);
        }

        if (! $this->entryToDelete) {
            return;
        }

        $deletedEntry = $this->entryToDelete;
        $wasDeleted = app(LogReaderService::class)->deleteEntry($deletedEntry['id']);

        if ($wasDeleted) {
            session()->flash('success', __('messages.logs.entry_delete_success'));
        } else {
            session()->flash('success', __('messages.logs.entry_not_found'));
        }

        $this->entryToDelete = null;
        $this->showDeleteEntryModal = false;
        $this->selectedEntry = null;
        $this->resetPage();
    }

    /**
     * Limpia todo el log y deja una entrada de auditoria general.
     */
    public function clearLog(): void
    {
        if (! auth()->user()?->can('administration.logs.view')) {
            abort(403);
        }

        if ($this->clearConfirmation !== 'LIMPIAR') {
            $this->addError('clearConfirmation', __('messages.logs.clear_confirmation_error'));
            return;
        }

        app(LogReaderService::class)->clear();

        Log::warning('[LogManager] Log cleared', [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'cleared_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->clearConfirmation = '';
        $this->showClearModal = false;
        $this->selectedEntry = null;
        $this->resetPage();

        session()->flash('success', __('messages.logs.clear_success'));
    }

    /**
     * Construye la vista paginada con los filtros activos.
     */
    public function render()
    {
        $reader = app(LogReaderService::class);
        $allEntries = $reader->entries();
        $entries = $this->filteredEntries($allEntries);
        $page = $this->getPage();
        $items = collect($entries);
        $logs = new LengthAwarePaginator(
            $items->forPage($page, $this->perPage)->values(),
            $items->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.log-manager.log-manager', [
            'logs' => $logs,
            'metadata' => $reader->metadata(),
            'totalEntries' => count($allEntries),
            'filteredEntries' => $items->count(),
        ]);
    }

    /**
     * Aplica los filtros actuales sobre las entradas disponibles.
     */
    protected function filteredEntries(?array $entries = null): array
    {
        $reader = app(LogReaderService::class);

        return $reader->filterEntries($entries ?? $reader->entries(), [
            'search' => $this->search,
            'level' => $this->level,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
        ]);
    }
}
