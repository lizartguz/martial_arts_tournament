<?php

namespace App\Livewire\Admin\FightResults;

use App\Models\Event;
use App\Models\Fight;
use App\Models\FightResult;
use App\Models\Fighter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class FightResultTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $eventId = '';
    public string $resultType = '';
    public string $resultState = '';
    public bool $showModal = false;
    public ?int $selectedFightId = null;
    public string $selectedFightLabel = '';
    public string $selectedEventLabel = '';
    public array $winnerOptions = [];
    public array $form = [
        'result_type' => 'decision',
        'winner_fighter_id' => null,
        'method' => '',
        'round' => null,
        'time' => '',
        'official_notes' => '',
    ];

    public function mount(): void
    {
        Gate::authorize('fight_results.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEventId(): void
    {
        $this->resetPage();
    }

    public function updatingResultType(): void
    {
        $this->resetPage();
    }

    public function updatingResultState(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFormResultType(string $value): void
    {
        if (in_array($value, $this->winnerlessResultTypes(), true)) {
            $this->form['winner_fighter_id'] = null;
        }
    }

    public function manage(int $fightId): void
    {
        Gate::authorize('fight_results.update');

        $fight = $this->findFightForModal($fightId);

        $this->selectedFightId = $fight->id;
        $this->selectedFightLabel = $this->fightName($fight);
        $this->selectedEventLabel = $fight->event?->name ?? __('mma.admin.common.not_available');
        $this->winnerOptions = $this->buildWinnerOptions($fight);
        $this->form = [
            'result_type' => $fight->result?->result_type ?? 'decision',
            'winner_fighter_id' => $fight->result?->winner_fighter_id,
            'method' => $fight->result?->method ?? '',
            'round' => $fight->result?->round,
            'time' => $fight->result?->time ?? '',
            'official_notes' => $fight->result?->official_notes ?? '',
        ];

        if (in_array($this->form['result_type'], $this->winnerlessResultTypes(), true)) {
            $this->form['winner_fighter_id'] = null;
        }

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        Gate::authorize('fight_results.update');

        if (! $this->selectedFightId) {
            return;
        }

        $fight = $this->findFightForModal($this->selectedFightId);
        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $this->normalizeNullableFields($validated);

        if (! $this->validateBusinessRules($fight, $validated)) {
            return;
        }

        $result = FightResult::query()->firstOrNew(['fight_id' => $fight->id]);

        if (! $result->exists) {
            $result->created_by = auth()->id();
        }

        $result->fill([
            'winner_fighter_id' => $validated['winner_fighter_id'],
            'result_type' => $validated['result_type'],
            'method' => $validated['method'],
            'round' => $validated['round'],
            'time' => $validated['time'],
            'official_notes' => $validated['official_notes'],
            'updated_by' => auth()->id(),
        ]);
        $result->save();

        // Registrar un resultado oficial cierra operativamente el combate.
        $fight->forceFill([
            'status' => 2,
            'updated_by' => auth()->id(),
        ])->save();

        $this->dispatch('successAlert', ['success' => __('mma.admin.fight_results.messages.saved')]);
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'eventId', 'resultType', 'resultState']);
        $this->resetPage();
    }

    public function resultTypeOptions(): array
    {
        return [
            'ko_tko' => __('mma.admin.fight_results.result_types.ko_tko'),
            'submission' => __('mma.admin.fight_results.result_types.submission'),
            'decision' => __('mma.admin.fight_results.result_types.decision'),
            'draw' => __('mma.admin.fight_results.result_types.draw'),
            'no_contest' => __('mma.admin.fight_results.result_types.no_contest'),
            'disqualification' => __('mma.admin.fight_results.result_types.disqualification'),
        ];
    }

    public function resultStateOptions(): array
    {
        return [
            'with' => __('mma.admin.fight_results.result_state.with'),
            'without' => __('mma.admin.fight_results.result_state.without'),
        ];
    }

    public function resultTypeLabel(?string $resultType): string
    {
        return $resultType ? ($this->resultTypeOptions()[$resultType] ?? $resultType) : __('mma.admin.fight_results.pending');
    }

    public function resultBadge(?string $resultType): string
    {
        return [
            'ko_tko' => 'tw-badge-red',
            'submission' => 'tw-badge-blue',
            'decision' => 'tw-badge-green',
            'draw' => 'tw-badge-gray',
            'no_contest' => 'tw-badge-gray',
            'disqualification' => 'tw-badge-red',
        ][$resultType] ?? 'tw-badge-gray';
    }

    public function fighterName(?Fighter $fighter): string
    {
        if (! $fighter) {
            return __('mma.admin.common.not_available');
        }

        $name = trim($fighter->first_name.' '.$fighter->last_name);

        return $fighter->nickname
            ? $name.' "'.$fighter->nickname.'"'
            : $name;
    }

    public function render()
    {
        $fights = Fight::query()
            ->with([
                'event:id,name,starts_at',
                'cornerRed:id,first_name,last_name,nickname',
                'cornerBlue:id,first_name,last_name,nickname',
                'result:id,fight_id,winner_fighter_id,result_type,method,round,time',
                'result.winner:id,first_name,last_name,nickname',
            ])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('event', fn ($eventQuery) => $eventQuery->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('cornerRed', fn ($fighterQuery) => $this->applyFighterSearch($fighterQuery))
                        ->orWhereHas('cornerBlue', fn ($fighterQuery) => $this->applyFighterSearch($fighterQuery))
                        ->orWhereHas('result', fn ($resultQuery) => $resultQuery->where('method', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->eventId !== '', fn ($query) => $query->where('event_id', (int) $this->eventId))
            ->when($this->resultType !== '', fn ($query) => $query->whereHas('result', fn ($resultQuery) => $resultQuery->where('result_type', $this->resultType)))
            ->when($this->resultState === 'with', fn ($query) => $query->has('result'))
            ->when($this->resultState === 'without', fn ($query) => $query->doesntHave('result'))
            ->orderByDesc('event_id')
            ->orderBy('display_order')
            ->paginate($this->perPage);

        $events = Event::query()
            ->select(['id', 'name', 'starts_at'])
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.admin.fight-results.fight-result-table', compact('fights', 'events'));
    }

    protected function rules(): array
    {
        return [
            'form.result_type' => ['required', Rule::in(array_keys($this->resultTypeOptions()))],
            'form.winner_fighter_id' => ['nullable', 'integer', 'exists:fighters,id'],
            'form.method' => ['nullable', 'string', 'max:80'],
            'form.round' => ['nullable', 'integer', 'min:1', 'max:12'],
            'form.time' => ['nullable', 'string', 'max:10', 'regex:/^\d{1,2}:\d{2}$/'],
            'form.official_notes' => ['nullable', 'string', 'max:3000'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.result_type' => __('mma.admin.fight_results.form.result_type'),
            'form.winner_fighter_id' => __('mma.admin.fight_results.form.winner_fighter_id'),
            'form.method' => __('mma.admin.fight_results.form.method'),
            'form.round' => __('mma.admin.fight_results.form.round'),
            'form.time' => __('mma.admin.fight_results.form.time'),
            'form.official_notes' => __('mma.admin.fight_results.form.official_notes'),
        ];
    }

    protected function resetForm(): void
    {
        $this->selectedFightId = null;
        $this->selectedFightLabel = '';
        $this->selectedEventLabel = '';
        $this->winnerOptions = [];
        $this->form = [
            'result_type' => 'decision',
            'winner_fighter_id' => null,
            'method' => '',
            'round' => null,
            'time' => '',
            'official_notes' => '',
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['winner_fighter_id', 'method', 'round', 'time', 'official_notes'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        if ($validated['round'] !== null) {
            $validated['round'] = (int) $validated['round'];
        }

        if ($validated['winner_fighter_id'] !== null) {
            $validated['winner_fighter_id'] = (int) $validated['winner_fighter_id'];
        }
    }

    protected function validateBusinessRules(Fight $fight, array &$validated): bool
    {
        if (in_array($validated['result_type'], $this->winnerlessResultTypes(), true)) {
            $validated['winner_fighter_id'] = null;

            return $this->validateRoundLimit($fight, $validated);
        }

        if (! $validated['winner_fighter_id']) {
            $this->addError('form.winner_fighter_id', __('mma.admin.fight_results.validation.winner_required'));

            return false;
        }

        $cornerIds = array_filter([
            $fight->corner_red_fighter_id,
            $fight->corner_blue_fighter_id,
        ]);

        if (! in_array($validated['winner_fighter_id'], $cornerIds, true)) {
            $this->addError('form.winner_fighter_id', __('mma.admin.fight_results.validation.winner_corner'));

            return false;
        }

        return $this->validateRoundLimit($fight, $validated);
    }

    protected function validateRoundLimit(Fight $fight, array $validated): bool
    {
        if ($validated['round'] !== null && $validated['round'] > $fight->rounds) {
            $this->addError('form.round', __('mma.admin.fight_results.validation.round_limit', ['rounds' => $fight->rounds]));

            return false;
        }

        return true;
    }

    protected function winnerlessResultTypes(): array
    {
        return ['draw', 'no_contest'];
    }

    protected function findFightForModal(int $fightId): Fight
    {
        return Fight::query()
            ->with([
                'event:id,name',
                'cornerRed:id,first_name,last_name,nickname',
                'cornerBlue:id,first_name,last_name,nickname',
                'result:id,fight_id,winner_fighter_id,result_type,method,round,time,official_notes',
            ])
            ->findOrFail($fightId);
    }

    protected function buildWinnerOptions(Fight $fight): array
    {
        return collect([
            ['id' => $fight->corner_red_fighter_id, 'label' => __('mma.admin.fight_results.corners.red', ['fighter' => $this->fighterName($fight->cornerRed)])],
            ['id' => $fight->corner_blue_fighter_id, 'label' => __('mma.admin.fight_results.corners.blue', ['fighter' => $this->fighterName($fight->cornerBlue)])],
        ])->filter(fn (array $option) => $option['id'] !== null)->values()->all();
    }

    protected function applyFighterSearch($query): void
    {
        $query->where('first_name', 'like', "%{$this->search}%")
            ->orWhere('last_name', 'like', "%{$this->search}%")
            ->orWhere('nickname', 'like', "%{$this->search}%");
    }

    protected function fightName(Fight $fight): string
    {
        return $fight->title ?: $this->fighterName($fight->cornerRed).' vs '.$this->fighterName($fight->cornerBlue);
    }
}
