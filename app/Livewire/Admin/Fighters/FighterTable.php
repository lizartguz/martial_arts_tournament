<?php

namespace App\Livewire\Admin\Fighters;

use App\Models\City;
use App\Models\Country;
use App\Models\Fighter;
use App\Models\FighterTeam;
use App\Models\WeightClass;
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

class FighterTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $gender = '';
    public string $status = '';
    public string $weightClassId = '';
    public string $teamId = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public ?TemporaryUploadedFile $profileImage = null;
    public ?TemporaryUploadedFile $coverImage = null;
    public ?string $currentProfileImage = null;
    public ?string $currentCoverImage = null;
    public array $form = [
        'first_name' => '',
        'last_name' => '',
        'nickname' => '',
        'slug' => '',
        'gender' => 'male',
        'country_id' => null,
        'city_id' => null,
        'fighter_team_id' => null,
        'weight_class_id' => null,
        'birthdate' => '',
        'height_cm' => null,
        'reach_cm' => null,
        'stance' => '',
        'bio' => '',
        'wins' => 0,
        'losses' => 0,
        'draws' => 0,
        'no_contests' => 0,
        'status' => 1,
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGender(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingWeightClassId(): void
    {
        $this->resetPage();
    }

    public function updatingTeamId(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFormFirstName(): void
    {
        $this->syncSlugFromName();
    }

    public function updatedFormLastName(): void
    {
        $this->syncSlugFromName();
    }

    public function updatedFormSlug(string $value): void
    {
        $this->slugTouched = true;
        $this->form['slug'] = Str::slug($value);
    }

    public function create(): void
    {
        Gate::authorize('fighters.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('fighters.update');

        $fighter = Fighter::query()->findOrFail($id);

        $this->editingId = $fighter->id;
        $this->slugTouched = true;
        $this->currentProfileImage = $fighter->profile_image;
        $this->currentCoverImage = $fighter->cover_image;
        $this->form = [
            'first_name' => $fighter->first_name,
            'last_name' => $fighter->last_name ?? '',
            'nickname' => $fighter->nickname ?? '',
            'slug' => $fighter->slug,
            'gender' => $fighter->gender,
            'country_id' => $fighter->country_id,
            'city_id' => $fighter->city_id,
            'fighter_team_id' => $fighter->fighter_team_id,
            'weight_class_id' => $fighter->weight_class_id,
            'birthdate' => $fighter->birthdate?->format('Y-m-d') ?? '',
            'height_cm' => $fighter->height_cm,
            'reach_cm' => $fighter->reach_cm,
            'stance' => $fighter->stance ?? '',
            'bio' => $fighter->bio ?? '',
            'wins' => (int) $fighter->wins,
            'losses' => (int) $fighter->losses,
            'draws' => (int) $fighter->draws,
            'no_contests' => (int) $fighter->no_contests,
            'status' => (int) $fighter->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'fighters.update' : 'fighters.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $fighter = $this->editingId
            ? Fighter::query()->findOrFail($this->editingId)
            : new Fighter(['uuid' => (string) Str::uuid(), 'created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $nameForSlug = trim($validated['first_name'].' '.($validated['last_name'] ?? ''));
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(Fighter::class, $nameForSlug, 'slug', $this->editingId);

        $this->normalizeNullableFields($validated);
        $validated['updated_by'] = auth()->id();
        $this->storeImages($images, $validated, $fighter);

        $fighter->fill($validated);
        $fighter->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.fighters.messages.updated')
                : __('mma.admin.fighters.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('fighters.delete');

        $fighter = Fighter::query()
            ->withCount(['redCornerFights', 'blueCornerFights', 'rankings'])
            ->findOrFail($id);

        if ($fighter->red_corner_fights_count || $fighter->blue_corner_fights_count || $fighter->rankings_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fighters.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $fighter->id;
        $this->deleteName = trim($fighter->first_name.' '.$fighter->last_name);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('fighters.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $fighter = Fighter::query()
            ->withCount(['redCornerFights', 'blueCornerFights', 'rankings'])
            ->findOrFail($this->pendingDeleteId);

        if ($fighter->red_corner_fights_count || $fighter->blue_corner_fights_count || $fighter->rankings_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fighters.messages.delete_blocked'),
            ]);

            return;
        }

        $this->deleteStoredImage($fighter->profile_image);
        $this->deleteStoredImage($fighter->cover_image);
        $fighter->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.fighters.messages.deleted')]);
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
        $this->reset(['search', 'gender', 'status', 'weightClassId', 'teamId']);
        $this->resetPage();
    }

    public function genderOptions(): array
    {
        return [
            'male' => __('mma.admin.fighters.gender.male'),
            'female' => __('mma.admin.fighters.gender.female'),
        ];
    }

    public function stanceOptions(): array
    {
        return [
            'orthodox' => __('mma.admin.fighters.stance.orthodox'),
            'southpaw' => __('mma.admin.fighters.stance.southpaw'),
            'switch' => __('mma.admin.fighters.stance.switch'),
        ];
    }

    public function genderLabel(?string $gender): string
    {
        return $this->genderOptions()[$gender] ?? __('mma.admin.common.not_available');
    }

    public function stanceLabel(?string $stance): string
    {
        return $this->stanceOptions()[$stance] ?? __('mma.admin.common.not_available');
    }

    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function render()
    {
        $fighters = Fighter::query()
            ->with([
                'country:id,name,iso2',
                'city:id,name',
                'team:id,name',
                'weightClass:id,name,gender',
            ])
            ->withCount(['redCornerFights', 'blueCornerFights'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('nickname', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->when($this->gender !== '', fn ($query) => $query->where('gender', $this->gender))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->weightClassId !== '', fn ($query) => $query->where('weight_class_id', (int) $this->weightClassId))
            ->when($this->teamId !== '', fn ($query) => $query->where('fighter_team_id', (int) $this->teamId))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate($this->perPage);

        $countries = Country::query()->where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $cities = City::query()->where('status', 1)->select(['id', 'name', 'country_id'])->orderBy('name')->get();
        $teams = FighterTeam::query()->where('status', 1)->select(['id', 'name'])->orderBy('name')->get();
        $weightClasses = WeightClass::query()
            ->where('status', 1)
            ->select(['id', 'name', 'gender'])
            ->orderBy('gender')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.fighters.fighter-table', compact('fighters', 'countries', 'cities', 'teams', 'weightClasses'));
    }

    protected function rules(): array
    {
        return [
            'form.first_name' => ['required', 'string', 'max:100'],
            'form.last_name' => ['nullable', 'string', 'max:100'],
            'form.nickname' => ['nullable', 'string', 'max:120'],
            'form.slug' => [
                'nullable',
                'string',
                'max:220',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('fighters', 'slug')->ignore($this->editingId),
            ],
            'form.gender' => ['required', Rule::in(array_keys($this->genderOptions()))],
            'form.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'form.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'form.fighter_team_id' => ['nullable', 'integer', 'exists:fighter_teams,id'],
            'form.weight_class_id' => ['nullable', 'integer', 'exists:weight_classes,id'],
            'form.birthdate' => ['nullable', 'date', 'before:today'],
            'form.height_cm' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'form.reach_cm' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'form.stance' => ['nullable', Rule::in(array_keys($this->stanceOptions()))],
            'form.bio' => ['nullable', 'string', 'max:8000'],
            'form.wins' => ['required', 'integer', 'min:0', 'max:999'],
            'form.losses' => ['required', 'integer', 'min:0', 'max:999'],
            'form.draws' => ['required', 'integer', 'min:0', 'max:999'],
            'form.no_contests' => ['required', 'integer', 'min:0', 'max:999'],
            'form.status' => ['required', Rule::in([0, 1])],
            'profileImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'coverImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.first_name' => __('mma.admin.fighters.form.first_name'),
            'form.last_name' => __('mma.admin.fighters.form.last_name'),
            'form.nickname' => __('mma.admin.fighters.form.nickname'),
            'form.slug' => __('mma.admin.fighters.form.slug'),
            'form.gender' => __('mma.admin.fighters.form.gender'),
            'form.country_id' => __('mma.admin.fighters.form.country_id'),
            'form.city_id' => __('mma.admin.fighters.form.city_id'),
            'form.fighter_team_id' => __('mma.admin.fighters.form.fighter_team_id'),
            'form.weight_class_id' => __('mma.admin.fighters.form.weight_class_id'),
            'form.birthdate' => __('mma.admin.fighters.form.birthdate'),
            'form.height_cm' => __('mma.admin.fighters.form.height_cm'),
            'form.reach_cm' => __('mma.admin.fighters.form.reach_cm'),
            'form.stance' => __('mma.admin.fighters.form.stance'),
            'form.bio' => __('mma.admin.fighters.form.bio'),
            'form.wins' => __('mma.admin.fighters.form.wins'),
            'form.losses' => __('mma.admin.fighters.form.losses'),
            'form.draws' => __('mma.admin.fighters.form.draws'),
            'form.no_contests' => __('mma.admin.fighters.form.no_contests'),
            'form.status' => __('mma.admin.fighters.form.status'),
            'profileImage' => __('mma.admin.fighters.form.profile_image'),
            'coverImage' => __('mma.admin.fighters.form.cover_image'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->profileImage = null;
        $this->coverImage = null;
        $this->currentProfileImage = null;
        $this->currentCoverImage = null;
        $this->form = [
            'first_name' => '',
            'last_name' => '',
            'nickname' => '',
            'slug' => '',
            'gender' => 'male',
            'country_id' => null,
            'city_id' => null,
            'fighter_team_id' => null,
            'weight_class_id' => null,
            'birthdate' => '',
            'height_cm' => null,
            'reach_cm' => null,
            'stance' => '',
            'bio' => '',
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'no_contests' => 0,
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function syncSlugFromName(): void
    {
        if ($this->slugTouched) {
            return;
        }

        $this->form['slug'] = Str::slug(trim($this->form['first_name'].' '.$this->form['last_name']));
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['last_name', 'nickname', 'country_id', 'city_id', 'fighter_team_id', 'weight_class_id', 'birthdate', 'height_cm', 'reach_cm', 'stance', 'bio'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    protected function storeImages(ImageUploadOptimizer $images, array &$validated, Fighter $fighter): void
    {
        if ($this->profileImage) {
            $validated['profile_image'] = $this->storePublicImage($images, $this->profileImage, 'fighter-profile');
            $this->deleteStoredImage($fighter->profile_image);
        }

        if ($this->coverImage) {
            $validated['cover_image'] = $this->storePublicImage($images, $this->coverImage, 'fighter-cover');
            $this->deleteStoredImage($fighter->cover_image);
        }
    }

    protected function storePublicImage(ImageUploadOptimizer $images, TemporaryUploadedFile $image, string $prefix): string
    {
        $config = config('uploads.public_images');
        $path = $images->store(
            $image,
            rtrim((string) $config['directory'], '/').'/fighters',
            $prefix,
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        return 'storage/'.$path;
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
