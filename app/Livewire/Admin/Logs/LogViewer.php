<?php

namespace App\Livewire\Admin\Logs;

use App\Services\LogReaderService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class LogViewer extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $level = '';
    public string $fromDate = '';
    public string $toDate = '';
    public int $perPage = 25;
    public ?array $selectedEntry = null;

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

    /**
     * Inicializa el componente de log viewer.
     */
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);
    }

    /**
     * Sincroniza el estado al cambiar .
     */
    public function updated($property): void
    {
        if (in_array($property, ['search', 'level', 'fromDate', 'toDate', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'level', 'fromDate', 'toDate']);
        $this->perPage = 25;
        $this->resetPage();
    }

    /**
     * Gestiona open entry dentro de la tabla de log viewer.
     */
    public function openEntry(string $entryId): void
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);

        $this->selectedEntry = collect($this->filteredEntries())->firstWhere('id', $entryId);
    }

    /**
     * Gestiona close entry dentro de la tabla de log viewer.
     */
    public function closeEntry(): void
    {
        $this->selectedEntry = null;
    }

    /**
     * Renderiza la tabla de log viewer con filtros activos.
     */
    public function render(LogReaderService $reader)
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);

        $entries = $reader->entries();
        $filtered = $this->filterEntries($entries);

        return view('livewire.admin.logs.log-viewer', [
            'metadata' => $reader->metadata(),
            'totalEntries' => count($entries),
            'filteredEntries' => count($filtered),
            'logs' => $this->paginate($filtered),
        ]);
    }

    /**
     * Gestiona filter entries dentro de la tabla de log viewer.
     */
    protected function filterEntries(array $entries): array
    {
        $search = mb_strtolower(trim($this->search));

        /**
         * Filtra los registros segun los criterios activos.
         */
        return array_values(array_filter($entries, function (array $entry) use ($search) {
            if ($this->level !== '' && $entry['level'] !== $this->level) {
                return false;
            }

            if ($this->fromDate !== '' && $entry['datetime'] < $this->fromDate.' 00:00:00') {
                return false;
            }

            if ($this->toDate !== '' && $entry['datetime'] > $this->toDate.' 23:59:59') {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $entry['datetime'] ?? '',
                $entry['level'] ?? '',
                $entry['env'] ?? '',
                $entry['message'] ?? '',
                $entry['context_json'] ?? '',
                implode("\n", (array) ($entry['trace'] ?? [])),
                implode("\n", (array) ($entry['raw'] ?? [])),
            ]));

            return str_contains($haystack, $search);
        }));
    }

    /**
     * Gestiona paginate dentro de la tabla de log viewer.
     */
    protected function paginate(array $entries): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = collect($entries);

        return new LengthAwarePaginator(
            $items->forPage($page, $this->perPage)->values(),
            $items->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
