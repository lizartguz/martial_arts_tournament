<?php

namespace App\Livewire\Admin\EventMedia;

use App\Models\Event;
use App\Models\EventMedia;
use App\Services\ImageUploadOptimizer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EventMediaTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $eventId = '';
    public string $fileType = '';
    public string $category = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public ?TemporaryUploadedFile $mediaImage = null;
    public ?string $currentFilePath = null;
    public array $form = [
        'event_id' => null,
        'file_type' => 'image',
        'file_path' => '',
        'category' => 'gallery',
        'title' => '',
        'description' => '',
        'is_featured' => false,
        'display_order' => 0,
        'status' => 1,
    ];

    public function mount(): void
    {
        Gate::authorize('event_media.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEventId(): void
    {
        $this->resetPage();
    }

    public function updatingFileType(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
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

    public function updatedFormFileType(string $value): void
    {
        $this->mediaImage = null;

        if ($value === 'image' && $this->editingId) {
            $this->form['file_path'] = $this->currentFilePath ?? '';
        }
    }

    public function create(): void
    {
        Gate::authorize('event_media.create');

        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('event_media.update');

        $media = EventMedia::query()->findOrFail($id);

        $this->editingId = $media->id;
        $this->currentFilePath = $media->file_type === 'image' ? $media->file_path : null;
        $this->form = [
            'event_id' => $media->event_id,
            'file_type' => $media->file_type,
            'file_path' => $media->file_path,
            'category' => $media->category ?? 'gallery',
            'title' => $media->title ?? '',
            'description' => $media->description ?? '',
            'is_featured' => (bool) $media->is_featured,
            'display_order' => (int) $media->display_order,
            'status' => (int) $media->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'event_media.update' : 'event_media.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $media = $this->editingId
            ? EventMedia::query()->findOrFail($this->editingId)
            : new EventMedia(['created_by' => auth()->id()]);

        $this->normalizeNullableFields($validated);

        if (! $this->prepareFilePath($images, $validated, $media)) {
            return;
        }

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $media->fill($validated);
        $media->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.event_media.messages.updated')
                : __('mma.admin.event_media.messages.created'),
        ]);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('event_media.delete');

        $media = EventMedia::query()->findOrFail($id);

        $this->pendingDeleteId = $media->id;
        $this->deleteName = $media->title ?: $this->fileTypeLabel($media->file_type);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Gate::authorize('event_media.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $media = EventMedia::query()->findOrFail($this->pendingDeleteId);
        $this->deleteStoredImage($media->file_path);
        $media->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.event_media.messages.deleted')]);
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
        $this->reset(['search', 'eventId', 'fileType', 'category', 'status']);
        $this->resetPage();
    }

    public function fileTypeOptions(): array
    {
        return [
            'image' => __('mma.admin.event_media.file_types.image'),
            'video' => __('mma.admin.event_media.file_types.video'),
        ];
    }

    public function categoryOptions(): array
    {
        return [
            'gallery' => __('mma.admin.event_media.categories.gallery'),
            'weigh_in' => __('mma.admin.event_media.categories.weigh_in'),
            'faceoff' => __('mma.admin.event_media.categories.faceoff'),
            'backstage' => __('mma.admin.event_media.categories.backstage'),
            'highlight' => __('mma.admin.event_media.categories.highlight'),
            'sponsor' => __('mma.admin.event_media.categories.sponsor'),
            'other' => __('mma.admin.event_media.categories.other'),
        ];
    }

    public function fileTypeLabel(?string $fileType): string
    {
        return $this->fileTypeOptions()[$fileType] ?? __('mma.admin.common.not_available');
    }

    public function categoryLabel(?string $category): string
    {
        return $category ? ($this->categoryOptions()[$category] ?? $category) : __('mma.admin.common.not_available');
    }

    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    public function render()
    {
        $mediaItems = EventMedia::query()
            ->with(['event:id,name,starts_at'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhereHas('event', fn ($eventQuery) => $eventQuery->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->eventId !== '', fn ($query) => $query->where('event_id', (int) $this->eventId))
            ->when($this->fileType !== '', fn ($query) => $query->where('file_type', $this->fileType))
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $events = Event::query()
            ->select(['id', 'name', 'starts_at'])
            ->orderByDesc('starts_at')
            ->get();

        return view('livewire.admin.event-media.event-media-table', compact('mediaItems', 'events'));
    }

    protected function rules(): array
    {
        return [
            'form.event_id' => ['required', 'integer', 'exists:events,id'],
            'form.file_type' => ['required', Rule::in(array_keys($this->fileTypeOptions()))],
            'form.file_path' => $this->filePathRules(),
            'form.category' => ['nullable', Rule::in(array_keys($this->categoryOptions()))],
            'form.title' => ['nullable', 'string', 'max:180'],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.is_featured' => ['boolean'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.status' => ['required', Rule::in([0, 1])],
            'mediaImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.event_id' => __('mma.admin.event_media.form.event_id'),
            'form.file_type' => __('mma.admin.event_media.form.file_type'),
            'form.file_path' => __('mma.admin.event_media.form.file_path'),
            'form.category' => __('mma.admin.event_media.form.category'),
            'form.title' => __('mma.admin.event_media.form.title'),
            'form.description' => __('mma.admin.event_media.form.description'),
            'form.is_featured' => __('mma.admin.event_media.form.is_featured'),
            'form.display_order' => __('mma.admin.event_media.form.display_order'),
            'form.status' => __('mma.admin.event_media.form.status'),
            'mediaImage' => __('mma.admin.event_media.form.media_image'),
        ];
    }

    protected function filePathRules(): array
    {
        $rules = [$this->form['file_type'] === 'video' ? 'required' : 'nullable', 'string', 'max:255'];

        if ($this->form['file_type'] === 'video') {
            $rules[] = 'url';
        }

        return $rules;
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->pendingDeleteId = null;
        $this->mediaImage = null;
        $this->currentFilePath = null;
        $this->form = [
            'event_id' => null,
            'file_type' => 'image',
            'file_path' => '',
            'category' => 'gallery',
            'title' => '',
            'description' => '',
            'is_featured' => false,
            'display_order' => 0,
            'status' => 1,
        ];
        $this->resetErrorBag();
    }

    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['file_path', 'category', 'title', 'description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['display_order'] = (int) $validated['display_order'];
        $validated['status'] = (int) $validated['status'];
    }

    protected function prepareFilePath(ImageUploadOptimizer $images, array &$validated, EventMedia $media): bool
    {
        if ($validated['file_type'] === 'video') {
            if ($media->exists && $media->file_type === 'image' && $media->file_path !== $validated['file_path']) {
                $this->deleteStoredImage($media->file_path);
            }

            return true;
        }

        if ($this->mediaImage) {
            $validated['file_path'] = $this->storePublicImage($images, $this->mediaImage);
            $this->deleteStoredImage($media->file_path);

            return true;
        }

        if ($media->exists && $media->file_type === 'image' && $media->file_path) {
            $validated['file_path'] = $media->file_path;

            return true;
        }

        $this->addError('mediaImage', __('mma.admin.event_media.validation.image_required'));

        return false;
    }

    protected function storePublicImage(ImageUploadOptimizer $images, TemporaryUploadedFile $image): string
    {
        $config = config('uploads.public_images');
        $path = $images->store(
            $image,
            rtrim((string) $config['directory'], '/').'/event-media',
            'event-media',
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
