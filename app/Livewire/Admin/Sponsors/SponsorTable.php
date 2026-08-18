<?php

namespace App\Livewire\Admin\Sponsors;

use App\Models\Event;
use App\Models\Sponsor;
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

    /**
     * Inicializa el componente de sponsors.
     */
    public function mount(): void
    {
        Gate::authorize('sponsors.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el evento.
     */
    public function updatingEventId(): void
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
        Gate::authorize('sponsors.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
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

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'sponsors.update' : 'sponsors.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $sponsor = $this->editingId
            ? Sponsor::query()->findOrFail($this->editingId)
            : new Sponsor;

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

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('sponsors.delete');

        $sponsor = Sponsor::query()->findOrFail($id);

        $this->pendingDeleteId = $sponsor->id;
        $this->deleteName = $sponsor->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
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
        $this->reset(['search', 'eventId', 'status']);
        $this->resetPage();
    }

    /**
     * Traduce el estado del registro.
     */
    public function statusLabel(int $status): string
    {
        return $status === 1
            ? __('mma.admin.common.active')
            : __('mma.admin.common.inactive');
    }

    /**
     * Renderiza la tabla de sponsors con filtros activos.
     */
    public function render()
    {
        $sponsors = Sponsor::query()
            ->withCount('events')
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
                $this->slugTouched ? Rule::unique('sponsors', 'slug')->ignore($this->editingId) : null,
            ]),
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

    /**
     * Traduce los atributos usados por la validación.
     */
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

    /**
     * Restaura el formulario a sus valores iniciales.
     */
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

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
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

    /**
     * Gestiona store logo dentro de la tabla de sponsors.
     */
    protected function storeLogo(ImageUploadOptimizer $images, array &$validated, Sponsor $sponsor): void
    {
        if (! $this->logoImage) {
            return;
        }

        $validated['logo_path'] = app(PublicMediaService::class)
            ->store($this->logoImage, 'sponsors', 'sponsor-logo');
        $this->deleteStoredImage($sponsor->logo_path);
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        app(PublicMediaService::class)->delete($path);
    }

    /**
     * Gestiona sync events dentro de la tabla de sponsors.
     */
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
