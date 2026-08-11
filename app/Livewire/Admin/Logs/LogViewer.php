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

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'level', 'fromDate', 'toDate', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'level', 'fromDate', 'toDate']);
        $this->perPage = 25;
        $this->resetPage();
    }

    public function openEntry(string $entryId): void
    {
        abort_unless(auth()->user()?->can('logs.view'), 403);

        $this->selectedEntry = collect($this->filteredEntries())->firstWhere('id', $entryId);
    }

    public function closeEntry(): void
    {
        $this->selectedEntry = null;
    }

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

    protected function filterEntries(array $entries): array
    {
        $search = mb_strtolower(trim($this->search));

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
