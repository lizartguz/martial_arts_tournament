<?php

namespace App\Livewire\Admin\Rankings;

use App\Models\Fighter;
use App\Models\FighterRanking;
use App\Models\WeightClass;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class RankingTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $gender = '';
    public string $weightClassId = '';
    public string $status = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public array $form = [
        'weight_class_id' => null,
        'gender' => 'male',
        'fighter_id' => null,
        'position' => 1,
        'previous_position' => null,
        'is_champion' => false,
        'ranked_at' => '',
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('rankings.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGender(): void
    {
        $this->resetPage();
    }

    public function updatingWeightClassId(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFormWeightClassId($value): void
    {
        $weightClass = $value ? WeightClass::query()->find((int) $value) : null;

        if ($weightClass) {
            $this->form['gender'] = $weightClass->gender;
        }

        $this->form['fighter_id'] = null;
    }

    public function updatedFormGender($value): void
    {
        $this->form['fighter_id'] = null;
    }

    public function create(): void
    {
        Gate::authorize('rankings.update');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('rankings.update');

        $ranking = FighterRanking::query()->findOrFail($id);

        $this->editingId = $ranking->id;
        $this->form = [
            'weight_class_id' => $ranking->weight_class_id,
            'gender' => $ranking->gender,
            'fighter_id' => $ranking->fighter_id,
            'position' => $ranking->position,
            'previous_position' => $ranking->previous_position,
            'is_champion' => (bool) $ranking->is_champion,
            'ranked_at' => $ranking->ranked_at?->format('Y-m-d') ?? '',
            'status' => (int) $ranking->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        Gate::authorize('rankings.update');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $this->normalizeNullableFields($validated);

        if (! $this->validateBusinessRules($validated)) {
            return;
        }

        $ranking = $this->editingId
            ? FighterRanking::query()->findOrFail($this->editingId)
            : new FighterRanking(['created_by' => auth()->id()]);

        $ranking->fill($validated);
        $ranking->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.rankings.messages.updated')
                : __('mma.admin.rankings.messages.created'),
        ]);

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'gender', 'weightClassId', 'status']);
        $this->resetPage();
    }

    public function genderLabel(string $gender): string
    {
        return __("mma.admin.weight_classes.gender.{$gender}");
    }

    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function movementLabel(?int $previousPosition, int $position): string
    {
        if (! $previousPosition || $previousPosition === $position) {
            return __('mma.admin.rankings.movement.same');
        }

        return $previousPosition > $position
            ? __('mma.admin.rankings.movement.up', ['places' => $previousPosition - $position])
            : __('mma.admin.rankings.movement.down', ['places' => $position - $previousPosition]);
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

    public function fighterRecord(?Fighter $fighter): string
    {
        if (! $fighter) {
            return __('mma.admin.common.not_available');
        }

        return __('mma.admin.fighters.record_summary', [
            'wins' => $fighter->wins,
            'losses' => $fighter->losses,
            'draws' => $fighter->draws,
            'nc' => $fighter->no_contests,
        ]);
    }

    public function render()
    {
        $rankings = FighterRanking::query()
            ->with([
                'weightClass:id,name,gender',
                'fighter:id,first_name,last_name,nickname,gender,weight_class_id,fighter_team_id,wins,losses,draws,no_contests,status',
                'fighter.team:id,name',
            ])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('fighter', fn ($fighterQuery) => $this->applyFighterSearch($fighterQuery))
                        ->orWhereHas('weightClass', fn ($classQuery) => $classQuery->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->gender !== '', fn ($query) => $query->where('gender', $this->gender))
            ->when($this->weightClassId !== '', fn ($query) => $query->where('weight_class_id', (int) $this->weightClassId))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderBy('weight_class_id')
            ->orderBy('gender')
            ->orderBy('position')
            ->paginate($this->perPage);

        $weightClasses = WeightClass::query()
            ->where('status', 1)
            ->select(['id', 'name', 'gender'])
            ->orderBy('gender')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $fighters = Fighter::query()
            ->with(['weightClass:id,name,gender'])
            ->where('status', 1)
            ->when(($this->form['gender'] ?? '') !== '', fn ($query) => $query->where('gender', $this->form['gender']))
            ->when(($this->form['weight_class_id'] ?? '') !== null && ($this->form['weight_class_id'] ?? '') !== '', fn ($query) => $query->where('weight_class_id', (int) $this->form['weight_class_id']))
            ->select(['id', 'first_name', 'last_name', 'nickname', 'gender', 'weight_class_id', 'wins', 'losses', 'draws', 'no_contests'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('livewire.admin.rankings.ranking-table', compact('rankings', 'weightClasses', 'fighters'));
    }

    protected function rules(): array
    {
        return [
            'form.weight_class_id' => ['required', 'integer', 'exists:weight_classes,id'],
            'form.gender' => ['required', Rule::in(['male', 'female'])],
            'form.fighter_id' => [
                'required',
                'integer',
                'exists:fighters,id',
                Rule::unique('fighter_rankings', 'fighter_id')
                    ->where(fn ($query) => $query
                        ->where('weight_class_id', $this->form['weight_class_id'])
                        ->where('gender', $this->form['gender']))
                    ->ignore($this->editingId),
            ],
            'form.position' => ['required', 'integer', 'min:1', 'max:999'],
            'form.previous_position' => ['nullable', 'integer', 'min:1', 'max:999'],
            'form.is_champion' => ['boolean'],
            'form.ranked_at' => ['nullable', 'date'],
            'form.status' => ['required', Rule::in([0, 1])],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.weight_class_id' => __('mma.admin.rankings.form.weight_class_id'),
            'form.gender' => __('mma.admin.rankings.form.gender'),
            'form.fighter_id' => __('mma.admin.rankings.form.fighter_id'),
            'form.position' => __('mma.admin.rankings.form.position'),
            'form.previous_position' => __('mma.admin.rankings.form.previous_position'),
            'form.is_champion' => __('mma.admin.rankings.form.is_champion'),
            'form.ranked_at' => __('mma.admin.rankings.form.ranked_at'),
            'form.status' => __('mma.admin.rankings.form.status'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'weight_class_id' => null,
            'gender' => 'male',
            'fighter_id' => null,
            'position' => 1,
            'previous_position' => null,
            'is_champion' => false,
            'ranked_at' => now()->toDateString(),
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['previous_position', 'ranked_at'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        foreach (['weight_class_id', 'fighter_id', 'position', 'previous_position', 'status'] as $field) {
            if ($validated[$field] !== null) {
                $validated[$field] = (int) $validated[$field];
            }
        }

        $validated['is_champion'] = (bool) ($validated['is_champion'] ?? false);
    }

    protected function validateBusinessRules(array $validated): bool
    {
        $fighter = Fighter::query()->findOrFail($validated['fighter_id']);

        if ($fighter->gender !== $validated['gender']) {
            $this->addError('form.fighter_id', __('mma.admin.rankings.validation.gender_mismatch'));

            return false;
        }

        if ((int) $fighter->weight_class_id !== $validated['weight_class_id']) {
            $this->addError('form.fighter_id', __('mma.admin.rankings.validation.weight_class_mismatch'));

            return false;
        }

        // Solo se bloquean posiciones duplicadas cuando ambos registros están activos.
        if ($validated['status'] === 1 && $this->activePositionIsTaken($validated)) {
            $this->addError('form.position', __('mma.admin.rankings.validation.position_taken'));

            return false;
        }

        return true;
    }

    protected function activePositionIsTaken(array $validated): bool
    {
        return FighterRanking::query()
            ->where('weight_class_id', $validated['weight_class_id'])
            ->where('gender', $validated['gender'])
            ->where('position', $validated['position'])
            ->where('status', 1)
            ->when($this->editingId, fn ($query) => $query->whereKeyNot($this->editingId))
            ->exists();
    }

    protected function applyFighterSearch($query): void
    {
        $query->where('first_name', 'like', "%{$this->search}%")
            ->orWhere('last_name', 'like', "%{$this->search}%")
            ->orWhere('nickname', 'like', "%{$this->search}%");
    }
}
