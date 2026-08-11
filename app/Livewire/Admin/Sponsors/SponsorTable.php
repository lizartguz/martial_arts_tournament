<?php

namespace App\Livewire\Admin\Sponsors;

use App\Models\Event;
use App\Models\Sponsor;
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

class SponsorTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $eventId = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public ?TemporaryUploadedFile $logoImage = null;
    public ?string $currentLogoPath = null;
    public array $selectedEventIds = [];
    public array $form = [
        'name' => '',
        'slug' => '',
        'website_url' => '',
        'contact_email' => '',
        'description' => '',
        'display_order' => 0,
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('sponsors.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEventId(): void
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
        Gate::authorize('sponsors.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('sponsors.update');

        $sponsor = Sponsor::query()->findOrFail($id);

        $this->editingId = $sponsor->id;
        $this->slugTouched = true;
        $this->currentLogoPath = $sponsor->logo_path;
        $this->selectedEventIds = $sponsor->events()
            ->pluck('events.id')
            ->map(fn ($id) => (string) $id)
            ->all();
        $this->form = [
            'name' => $sponsor->name,
            'slug' => $sponsor->slug,
            'website_url' => $sponsor->website_url ?? '',
            'contact_email' => $sponsor->contact_email ?? '',
            'description' => $sponsor->description ?? '',
            'display_order' => (int) $sponsor->display_order,
            'status' => (int) $sponsor->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'sponsors.update' : 'sponsors.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $sponsor = $this->editingId
            ? Sponsor::query()->findOrFail($this->editingId)
            : new Sponsor();

        $slugs = app(SlugGeneratorService::class);
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(Sponsor::class, $validated['name'], 'slug', $this->editingId);

        $this->normalizeNullableFields($validated);
        $this->storeLogo($images, $validated, $sponsor);

        $sponsor->fill($validated);
        $sponsor->save();
        $this->syncEvents($sponsor);

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.sponsors.messages.updated')
                : __('mma.admin.sponsors.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('sponsors.delete');

        $sponsor = Sponsor::query()->findOrFail($id);

        $this->pendingDeleteId = $sponsor->id;
        $this->deleteName = $sponsor->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('sponsors.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $sponsor = Sponsor::query()
            ->withCount('events')
            ->findOrFail($this->pendingDeleteId);

        if ($sponsor->events_count > 0) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', ['failed' => __('mma.admin.sponsors.messages.delete_blocked')]);

            return;
        }

        $this->deleteStoredImage($sponsor->logo_path);
        $sponsor->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.sponsors.messages.deleted')]);
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
        $this->reset(['search', 'eventId', 'status']);
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
        $sponsors = Sponsor::query()
            ->withCount('events')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('website_url', 'like', "%{$this->search}%")
                        ->orWhere('contact_email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->eventId !== '', fn ($query) => $query->whereHas(
                'events',
                fn ($eventQuery) => $eventQuery->where('events.id', (int) $this->eventId)
            ))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        $events = Event::query()
            ->select(['id', 'name', 'starts_at'])
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.admin.sponsors.sponsor-table', compact('sponsors', 'events'));
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
                Rule::unique('sponsors', 'slug')->ignore($this->editingId),
            ],
            'form.website_url' => ['nullable', 'url', 'max:500'],
            'form.contact_email' => ['nullable', 'email', 'max:150'],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.status' => ['required', Rule::in([0, 1])],
            'selectedEventIds' => ['array'],
            'selectedEventIds.*' => ['integer', 'exists:events,id'],
            'logoImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.sponsors.form.name'),
            'form.slug' => __('mma.admin.sponsors.form.slug'),
            'form.website_url' => __('mma.admin.sponsors.form.website_url'),
            'form.contact_email' => __('mma.admin.sponsors.form.contact_email'),
            'form.description' => __('mma.admin.sponsors.form.description'),
            'form.display_order' => __('mma.admin.sponsors.form.display_order'),
            'form.status' => __('mma.admin.sponsors.form.status'),
            'selectedEventIds' => __('mma.admin.sponsors.form.events'),
            'logoImage' => __('mma.admin.sponsors.form.logo_path'),
        ];
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->logoImage = null;
        $this->currentLogoPath = null;
        $this->selectedEventIds = [];
        $this->form = [
            'name' => '',
            'slug' => '',
            'website_url' => '',
            'contact_email' => '',
            'description' => '',
            'display_order' => 0,
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['website_url', 'contact_email', 'description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['display_order'] = (int) $validated['display_order'];
        $validated['status'] = (int) $validated['status'];
    }

    protected function storeLogo(ImageUploadOptimizer $images, array &$validated, Sponsor $sponsor): void
    {
        if (! $this->logoImage) {
            return;
        }

        $config = config('uploads.public_images');
        $path = $images->store(
            $this->logoImage,
            rtrim((string) $config['directory'], '/').'/sponsors',
            'sponsor-logo',
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        $validated['logo_path'] = 'storage/'.$path;
        $this->deleteStoredImage($sponsor->logo_path);
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }

    protected function syncEvents(Sponsor $sponsor): void
    {
        $currentPivot = $sponsor->events()
            ->get()
            ->keyBy('id')
            ->map(fn ($event) => [
                'placement' => $event->pivot->placement,
                'display_order' => (int) $event->pivot->display_order,
            ]);

        $syncPayload = collect($this->selectedEventIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($id) => [
                $id => $currentPivot->get($id, ['placement' => null, 'display_order' => 0]),
            ])
            ->all();

        $sponsor->events()->sync($syncPayload);
    }
}
