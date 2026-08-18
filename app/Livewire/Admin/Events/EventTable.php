<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use App\Models\Venue;
use App\Services\ImageUploadOptimizer;
use App\Services\PublicMediaService;
use App\Services\SlugGeneratorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EventTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;

    public string $search = '';

    public string $status = '';

    public string $venueId = '';

    public string $featured = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingId = null;

    public ?int $pendingDeleteId = null;

    public string $deleteName = '';

    public bool $slugTouched = false;

    public ?TemporaryUploadedFile $posterImage = null;

    public ?TemporaryUploadedFile $bannerImage = null;

    public ?string $currentPosterImage = null;

    public ?string $currentBannerImage = null;

    public array $form = [
        'name' => '',
        'slug' => '',
        'subtitle' => '',
        'description' => '',
        'venue_id' => null,
        'starts_at' => '',
        'doors_open_at' => '',
        'timezone' => '',
        'stream_url' => '',
        'ticket_url' => '',
        'status' => 0,
        'is_featured' => false,
    ];

    /**
     * Inicializa el componente de eventos.
     */
    public function mount(): void
    {
        $this->form['timezone'] = (string) config('app.timezone', 'UTC');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el estado.
     */
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la sede.
     */
    public function updatingVenueId(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el destacado.
     */
    public function updatingFeatured(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la fecha inicial.
     */
    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la fecha final.
     */
    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el tamaño de página.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sincroniza el formulario al cambiar el nombre.
     */
    public function updatedFormName(string $value): void
    {
        if (! $this->slugTouched) {
            $this->form['slug'] = Str::slug($value);
        }
    }

    /**
     * Sincroniza el formulario al cambiar el slug.
     */
    public function updatedFormSlug(string $value): void
    {
        $this->slugTouched = true;
        $this->form['slug'] = Str::slug($value);
    }

    /**
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        Gate::authorize('events.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('events.update');

        $event = Event::query()->findOrFail($id);

        $this->editingId = $event->id;
        $this->slugTouched = true;
        $this->currentPosterImage = $event->poster_image;
        $this->currentBannerImage = $event->banner_image;
        $this->form = [
            'name' => $event->name,
            'slug' => $event->slug,
            'subtitle' => $event->subtitle ?? '',
            'description' => $event->description ?? '',
            'venue_id' => $event->venue_id,
            'starts_at' => $event->starts_at?->format('Y-m-d\TH:i') ?? '',
            'doors_open_at' => $event->doors_open_at?->format('Y-m-d\TH:i') ?? '',
            'timezone' => $event->timezone ?: (string) config('app.timezone', 'UTC'),
            'stream_url' => $event->stream_url ?? '',
            'ticket_url' => $event->ticket_url ?? '',
            'status' => (int) $event->status,
            'is_featured' => (bool) $event->is_featured,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'events.update' : 'events.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $requestedStatus = (int) $validated['status'];

        if ($requestedStatus === 1) {
            Gate::authorize('events.publish');
        }

        $event = $this->editingId
            ? Event::query()->findOrFail($this->editingId)
            : new Event(['uuid' => (string) Str::uuid(), 'created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(Event::class, $validated['name'], 'slug', $this->editingId);

        $validated['venue_id'] = filled($validated['venue_id'] ?? null) ? (int) $validated['venue_id'] : null;
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['updated_by'] = auth()->id();
        $validated['published_at'] = $requestedStatus === 1 ? ($event->published_at ?: now()) : null;

        $this->storeImages($images, $validated, $event);

        $event->fill($validated);
        $event->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.events.messages.updated')
                : __('mma.admin.events.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Publica el registro seleccionado.
     */
    public function publish(int $id): void
    {
        Gate::authorize('events.publish');

        Event::query()->findOrFail($id)->update([
            'status' => 1,
            'published_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('successAlert', ['success' => __('mma.admin.events.messages.published')]);
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('events.delete');

        $event = Event::query()
            ->withCount(['fights', 'ticketLinks', 'purchaseRequests'])
            ->findOrFail($id);

        if ($event->fights_count || $event->ticket_links_count || $event->purchase_requests_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.events.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $event->id;
        $this->deleteName = $event->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('events.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $event = Event::query()
            ->withCount(['fights', 'ticketLinks', 'purchaseRequests'])
            ->findOrFail($this->pendingDeleteId);

        if ($event->fights_count || $event->ticket_links_count || $event->purchase_requests_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.events.messages.delete_blocked'),
            ]);

            return;
        }

        $this->deleteStoredImage($event->poster_image);
        $this->deleteStoredImage($event->banner_image);
        $event->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.events.messages.deleted')]);
    }

    /**
     * Cierra el modal principal y limpia su estado.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Cierra el modal de eliminación.
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->pendingDeleteId = null;
        $this->deleteName = '';
    }

    /**
     * Limpia filtros y reinicia la paginación.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'venueId', 'featured', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.events.status.draft'),
            1 => __('mma.admin.events.status.published'),
            2 => __('mma.admin.events.status.archived'),
            3 => __('mma.admin.events.status.cancelled'),
        ];
    }

    /**
     * Traduce el estado del registro.
     */
    public function statusLabel(int $status): string
    {
        return $this->statusOptions()[$status] ?? (string) $status;
    }

    /**
     * Define la clase visual según el estado.
     */
    public function statusBadge(int $status): string
    {
        return [
            0 => 'tw-badge-gray',
            1 => 'tw-badge-green',
            2 => 'tw-badge-blue',
            3 => 'tw-badge-red',
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Renderiza la tabla de eventos con filtros activos.
     */
    public function render()
    {
        $events = Event::query()
            ->with(['venue:id,name,city_id'])
            ->withCount(['fights', 'ticketLinks'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('subtitle', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->venueId !== '', fn ($query) => $query->where('venue_id', (int) $this->venueId))
            ->when($this->featured !== '', fn ($query) => $query->where('is_featured', (int) $this->featured))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('starts_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('starts_at', '<=', $this->dateTo))
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);

        $venues = Venue::query()
            ->where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.events.event-table', compact('events', 'venues'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:180'],
            'form.slug' => array_filter([
                'nullable',
                'string',
                'max:220',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $this->slugTouched ? Rule::unique('events', 'slug')->ignore($this->editingId) : null,
            ]),
            'form.subtitle' => ['nullable', 'string', 'max:200'],
            'form.description' => ['nullable', 'string', 'max:8000'],
            'form.venue_id' => ['nullable', 'integer', 'exists:venues,id'],
            'form.starts_at' => ['required', 'date'],
            'form.doors_open_at' => ['nullable', 'date', 'before_or_equal:form.starts_at'],
            'form.timezone' => ['nullable', 'string', 'max:60'],
            'form.stream_url' => ['nullable', 'url', 'max:500'],
            'form.ticket_url' => ['nullable', 'url', 'max:500'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.is_featured' => ['boolean'],
            'posterImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'bannerImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.events.form.name'),
            'form.slug' => __('mma.admin.events.form.slug'),
            'form.subtitle' => __('mma.admin.events.form.subtitle'),
            'form.description' => __('mma.admin.events.form.description'),
            'form.venue_id' => __('mma.admin.events.form.venue_id'),
            'form.starts_at' => __('mma.admin.events.form.starts_at'),
            'form.doors_open_at' => __('mma.admin.events.form.doors_open_at'),
            'form.timezone' => __('mma.admin.events.form.timezone'),
            'form.stream_url' => __('mma.admin.events.form.stream_url'),
            'form.ticket_url' => __('mma.admin.events.form.ticket_url'),
            'form.status' => __('mma.admin.events.form.status'),
            'form.is_featured' => __('mma.admin.events.form.is_featured'),
            'posterImage' => __('mma.admin.events.form.poster_image'),
            'bannerImage' => __('mma.admin.events.form.banner_image'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->posterImage = null;
        $this->bannerImage = null;
        $this->currentPosterImage = null;
        $this->currentBannerImage = null;
        $this->form = [
            'name' => '',
            'slug' => '',
            'subtitle' => '',
            'description' => '',
            'venue_id' => null,
            'starts_at' => '',
            'doors_open_at' => '',
            'timezone' => (string) config('app.timezone', 'UTC'),
            'stream_url' => '',
            'ticket_url' => '',
            'status' => 0,
            'is_featured' => false,
        ];
        $this->resetErrorBag();
    }

    /**
     * Guarda las imágenes enviadas desde el formulario.
     */
    protected function storeImages(ImageUploadOptimizer $images, array &$validated, Event $event): void
    {
        if ($this->posterImage) {
            $validated['poster_image'] = $this->storePublicImage($images, $this->posterImage, 'event-poster');
            $this->deleteStoredImage($event->poster_image);
        }

        if ($this->bannerImage) {
            $validated['banner_image'] = $this->storePublicImage($images, $this->bannerImage, 'event-banner');
            $this->deleteStoredImage($event->banner_image);
        }
    }

    /**
     * Guarda una imagen pública optimizada.
     */
    protected function storePublicImage(ImageUploadOptimizer $images, TemporaryUploadedFile $image, string $prefix): string
    {
        return app(PublicMediaService::class)->store($image, 'events', $prefix);
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        app(PublicMediaService::class)->delete($path);
    }
}
