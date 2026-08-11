<?php

namespace App\Livewire\Admin\Venues;

use App\Models\City;
use App\Models\Venue;
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

class VenueTable extends Component
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
    public ?TemporaryUploadedFile $venueImage = null;
    public ?string $currentImagePath = null;
    public array $form = [
        'name' => '',
        'slug' => '',
        'city_id' => null,
        'address' => '',
        'latitude' => null,
        'longitude' => null,
        'capacity' => null,
        'contact_name' => '',
        'contact_phone' => '',
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('venues.view');
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
        Gate::authorize('venues.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('venues.update');

        $venue = Venue::query()->findOrFail($id);

        $this->editingId = $venue->id;
        $this->slugTouched = true;
        $this->currentImagePath = $venue->image;
        $this->form = [
            'name' => $venue->name,
            'slug' => $venue->slug,
            'city_id' => $venue->city_id,
            'address' => $venue->address ?? '',
            'latitude' => $venue->latitude,
            'longitude' => $venue->longitude,
            'capacity' => $venue->capacity,
            'contact_name' => $venue->contact_name ?? '',
            'contact_phone' => $venue->contact_phone ?? '',
            'status' => (int) $venue->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'venues.update' : 'venues.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $venue = $this->editingId
            ? Venue::query()->findOrFail($this->editingId)
            : new Venue(['created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(Venue::class, $validated['name'], 'slug', $this->editingId);

        $this->normalizeNullableFields($validated);
        $validated['updated_by'] = auth()->id();
        $this->storeImage($images, $validated, $venue);

        $venue->fill($validated);
        $venue->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.venues.messages.updated')
                : __('mma.admin.venues.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('venues.delete');

        $venue = Venue::query()
            ->withCount('events')
            ->findOrFail($id);

        if ($venue->events_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.venues.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $venue->id;
        $this->deleteName = $venue->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('venues.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $venue = Venue::query()
            ->withCount('events')
            ->findOrFail($this->pendingDeleteId);

        if ($venue->events_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.venues.messages.delete_blocked'),
            ]);

            return;
        }

        $this->deleteStoredImage($venue->image);
        $venue->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.venues.messages.deleted')]);
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
        $venues = Venue::query()
            ->with(['city:id,name,country_id', 'city.country:id,name'])
            ->withCount('events')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%")
                        ->orWhere('contact_name', 'like', "%{$this->search}%")
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

        return view('livewire.admin.venues.venue-table', compact('venues', 'cities'));
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
                Rule::unique('venues', 'slug')->ignore($this->editingId),
            ],
            'form.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'form.address' => ['nullable', 'string', 'max:255'],
            'form.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'form.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'form.capacity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'form.contact_name' => ['nullable', 'string', 'max:120'],
            'form.contact_phone' => ['nullable', 'string', 'max:30'],
            'form.status' => ['required', Rule::in([0, 1])],
            'venueImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.venues.form.name'),
            'form.slug' => __('mma.admin.venues.form.slug'),
            'form.city_id' => __('mma.admin.venues.form.city_id'),
            'form.address' => __('mma.admin.venues.form.address'),
            'form.latitude' => __('mma.admin.venues.form.latitude'),
            'form.longitude' => __('mma.admin.venues.form.longitude'),
            'form.capacity' => __('mma.admin.venues.form.capacity'),
            'form.contact_name' => __('mma.admin.venues.form.contact_name'),
            'form.contact_phone' => __('mma.admin.venues.form.contact_phone'),
            'form.status' => __('mma.admin.venues.form.status'),
            'venueImage' => __('mma.admin.venues.form.image'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->venueImage = null;
        $this->currentImagePath = null;
        $this->form = [
            'name' => '',
            'slug' => '',
            'city_id' => null,
            'address' => '',
            'latitude' => null,
            'longitude' => null,
            'capacity' => null,
            'contact_name' => '',
            'contact_phone' => '',
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['city_id', 'address', 'latitude', 'longitude', 'capacity', 'contact_name', 'contact_phone'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    protected function storeImage(ImageUploadOptimizer $images, array &$validated, Venue $venue): void
    {
        if (! $this->venueImage) {
            return;
        }

        $config = config('uploads.public_images');
        $path = $images->store(
            $this->venueImage,
            rtrim((string) $config['directory'], '/').'/venues',
            'venue-image',
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        $validated['image'] = 'storage/'.$path;
        $this->deleteStoredImage($venue->image);
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
