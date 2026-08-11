<?php

namespace App\Livewire\Admin\FighterTeams;

use App\Models\City;
use App\Models\FighterTeam;
use App\Services\ImageUploadOptimizer;
use App\Services\SlugGeneratorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FighterTeamTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $status = '';
    public string $cityId = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public ?TemporaryUploadedFile $logoImage = null;
    public ?string $currentLogoPath = null;
    public array $form = [
        'name' => '',
        'slug' => '',
        'city_id' => null,
        'coach_name' => '',
        'contact_phone' => '',
        'description' => '',
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('fighter_teams.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCityId(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFormName(string $value): void
    {
        if (! $this->slugTouched) {
            $this->form['slug'] = Str::slug($value);
        }
    }

    public function updatedFormSlug(string $value): void
    {
        $this->slugTouched = true;
        $this->form['slug'] = Str::slug($value);
    }

    public function create(): void
    {
        Gate::authorize('fighter_teams.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('fighter_teams.update');

        $team = FighterTeam::query()->findOrFail($id);

        $this->editingId = $team->id;
        $this->slugTouched = true;
        $this->currentLogoPath = $team->logo_path;
        $this->form = [
            'name' => $team->name,
            'slug' => $team->slug,
            'city_id' => $team->city_id,
            'coach_name' => $team->coach_name ?? '',
            'contact_phone' => $team->contact_phone ?? '',
            'description' => $team->description ?? '',
            'status' => (int) $team->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'fighter_teams.update' : 'fighter_teams.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $team = $this->editingId
            ? FighterTeam::query()->findOrFail($this->editingId)
            : new FighterTeam(['created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(FighterTeam::class, $validated['name'], 'slug', $this->editingId);

        $this->normalizeNullableFields($validated);
        $validated['updated_by'] = auth()->id();
        $this->storeLogo($images, $validated, $team);

        $team->fill($validated);
        $team->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.fighter_teams.messages.updated')
                : __('mma.admin.fighter_teams.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('fighter_teams.delete');

        $team = FighterTeam::query()
            ->withCount('fighters')
            ->findOrFail($id);

        if ($team->fighters_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fighter_teams.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $team->id;
        $this->deleteName = $team->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('fighter_teams.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $team = FighterTeam::query()
            ->withCount('fighters')
            ->findOrFail($this->pendingDeleteId);

        if ($team->fighters_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fighter_teams.messages.delete_blocked'),
            ]);

            return;
        }

        $this->deleteStoredImage($team->logo_path);
        $team->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.fighter_teams.messages.deleted')]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'cityId']);
        $this->resetPage();
    }

    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function render()
    {
        $fighterTeams = FighterTeam::query()
            ->with(['city:id,name,country_id', 'city.country:id,name'])
            ->withCount('fighters')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('coach_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->cityId !== '', fn ($query) => $query->where('city_id', (int) $this->cityId))
            ->orderBy('name')
            ->paginate($this->perPage);

        $cities = City::query()
            ->with('country:id,name')
            ->where('status', 1)
            ->select(['id', 'name', 'country_id'])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.fighter-teams.fighter-team-table', compact('fighterTeams', 'cities'));
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:180'],
            'form.slug' => [
                'nullable',
                'string',
                'max:220',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('fighter_teams', 'slug')->ignore($this->editingId),
            ],
            'form.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'form.coach_name' => ['nullable', 'string', 'max:120'],
            'form.contact_phone' => ['nullable', 'string', 'max:30'],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.status' => ['required', Rule::in([0, 1])],
            'logoImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.fighter_teams.form.name'),
            'form.slug' => __('mma.admin.fighter_teams.form.slug'),
            'form.city_id' => __('mma.admin.fighter_teams.form.city_id'),
            'form.coach_name' => __('mma.admin.fighter_teams.form.coach_name'),
            'form.contact_phone' => __('mma.admin.fighter_teams.form.contact_phone'),
            'form.description' => __('mma.admin.fighter_teams.form.description'),
            'form.status' => __('mma.admin.fighter_teams.form.status'),
            'logoImage' => __('mma.admin.fighter_teams.form.logo_path'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->logoImage = null;
        $this->currentLogoPath = null;
        $this->form = [
            'name' => '',
            'slug' => '',
            'city_id' => null,
            'coach_name' => '',
            'contact_phone' => '',
            'description' => '',
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['city_id', 'coach_name', 'contact_phone', 'description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    protected function storeLogo(ImageUploadOptimizer $images, array &$validated, FighterTeam $team): void
    {
        if (! $this->logoImage) {
            return;
        }

        $config = config('uploads.public_images');
        $path = $images->store(
            $this->logoImage,
            rtrim((string) $config['directory'], '/').'/fighter-teams',
            'fighter-team-logo',
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        $validated['logo_path'] = 'storage/'.$path;
        $this->deleteStoredImage($team->logo_path);
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
