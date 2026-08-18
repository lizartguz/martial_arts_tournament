<?php

namespace App\Livewire\Admin\Fights;

use App\Models\Event;
use App\Models\Fight;
use App\Models\Fighter;
use App\Models\WeightClass;
use App\Services\ImageUploadOptimizer;
use App\Services\PublicMediaService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FightTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;

    public string $search = '';

    public string $eventId = '';

    public string $status = '';

    public string $boutType = '';

    public string $weightClassId = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingId = null;

    public ?int $pendingDeleteId = null;

    public string $deleteName = '';

    public ?TemporaryUploadedFile $promoImage = null;

    public ?string $currentPromoImage = null;

    public array $form = [
        'event_id' => null,
        'weight_class_id' => null,
        'corner_red_fighter_id' => null,
        'corner_blue_fighter_id' => null,
        'title' => '',
        'bout_type' => 'regular',
        'rounds' => 3,
        'display_order' => 1,
        'starts_at' => '',
        'status' => 0,
        'is_main_event' => false,
        'is_featured' => false,
        'notes' => '',
    ];

    /**
     * Inicializa el componente de combates.
     */
    public function mount(): void
    {
        Gate::authorize('fights.view');
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
     * Reinicia la paginación al cambiar el tipo de combate.
     */
    public function updatingBoutType(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar la categoría de peso.
     */
    public function updatingWeightClassId(): void
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
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        Gate::authorize('fights.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('fights.update');

        $fight = Fight::query()->findOrFail($id);

        $this->editingId = $fight->id;
        $this->currentPromoImage = $fight->promo_image;
        $this->form = [
            'event_id' => $fight->event_id,
            'weight_class_id' => $fight->weight_class_id,
            'corner_red_fighter_id' => $fight->corner_red_fighter_id,
            'corner_blue_fighter_id' => $fight->corner_blue_fighter_id,
            'title' => $fight->title ?? '',
            'bout_type' => $fight->bout_type,
            'rounds' => (int) $fight->rounds,
            'display_order' => (int) $fight->display_order,
            'starts_at' => $fight->starts_at?->format('Y-m-d\TH:i') ?? '',
            'status' => (int) $fight->status,
            'is_main_event' => (bool) $fight->is_main_event,
            'is_featured' => (bool) $fight->is_featured,
            'notes' => $fight->notes ?? '',
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'fights.update' : 'fights.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $fight = $this->editingId
            ? Fight::query()->findOrFail($this->editingId)
            : new Fight(['created_by' => auth()->id()]);

        $this->normalizeNullableFields($validated);
        $validated['updated_by'] = auth()->id();
        $validated['is_main_event'] = (bool) ($validated['is_main_event'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $this->storePromoImage($images, $validated, $fight);

        $fight->fill($validated);
        $fight->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.fights.messages.updated')
                : __('mma.admin.fights.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('fights.delete');

        $fight = Fight::query()
            ->with(['cornerRed:id,first_name,last_name,nickname', 'cornerBlue:id,first_name,last_name,nickname'])
            ->withCount('result')
            ->findOrFail($id);

        if ($fight->result_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fights.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $fight->id;
        $this->deleteName = $this->fightName($fight);
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('fights.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $fight = Fight::query()
            ->withCount('result')
            ->findOrFail($this->pendingDeleteId);

        if ($fight->result_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.fights.messages.delete_blocked'),
            ]);

            return;
        }

        $this->deleteStoredImage($fight->promo_image);
        $fight->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.fights.messages.deleted')]);
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
        $this->reset(['search', 'eventId', 'status', 'boutType', 'weightClassId']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.fights.status.scheduled'),
            1 => __('mma.admin.fights.status.live'),
            2 => __('mma.admin.fights.status.finished'),
            3 => __('mma.admin.fights.status.cancelled'),
        ];
    }

    /**
     * Devuelve las opciones disponibles de bout type.
     */
    public function boutTypeOptions(): array
    {
        return [
            'regular' => __('mma.admin.fights.bout_type.regular'),
            'main_event' => __('mma.admin.fights.bout_type.main_event'),
            'co_main_event' => __('mma.admin.fights.bout_type.co_main_event'),
            'title_fight' => __('mma.admin.fights.bout_type.title_fight'),
            'exhibition' => __('mma.admin.fights.bout_type.exhibition'),
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
            1 => 'tw-badge-blue',
            2 => 'tw-badge-green',
            3 => 'tw-badge-red',
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Devuelve la etiqueta visible de bout type.
     */
    public function boutTypeLabel(?string $boutType): string
    {
        return $this->boutTypeOptions()[$boutType] ?? __('mma.admin.common.not_available');
    }

    /**
     * Gestiona fighter name dentro de la tabla de combates.
     */
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

    /**
     * Renderiza la tabla de combates con filtros activos.
     */
    public function render()
    {
        $fights = Fight::query()
            ->with([
                'event:id,name,starts_at',
                'weightClass:id,name,gender',
                'cornerRed:id,first_name,last_name,nickname,slug',
                'cornerBlue:id,first_name,last_name,nickname,slug',
            ])
            ->withCount('result')
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('cornerRed', fn ($fighterQuery) => $this->applyFighterSearch($fighterQuery))
                        ->orWhereHas('cornerBlue', fn ($fighterQuery) => $this->applyFighterSearch($fighterQuery));
                });
            })
            ->when($this->eventId !== '', fn ($query) => $query->where('event_id', (int) $this->eventId))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->boutType !== '', fn ($query) => $query->where('bout_type', $this->boutType))
            ->when($this->weightClassId !== '', fn ($query) => $query->where('weight_class_id', (int) $this->weightClassId))
            ->orderByDesc('event_id')
            ->orderBy('display_order')
            ->paginate($this->perPage);

        $events = Event::query()
            ->select(['id', 'name', 'starts_at'])
            ->orderByDesc('starts_at')
            ->get();
        $fighters = Fighter::query()
            ->where('status', 1)
            ->select(['id', 'first_name', 'last_name', 'nickname'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $weightClasses = WeightClass::query()
            ->where('status', 1)
            ->select(['id', 'name', 'gender'])
            ->orderBy('gender')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.fights.fight-table', compact('fights', 'events', 'fighters', 'weightClasses'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.event_id' => ['required', 'integer', 'exists:events,id'],
            'form.weight_class_id' => ['nullable', 'integer', 'exists:weight_classes,id'],
            'form.corner_red_fighter_id' => ['required', 'integer', 'exists:fighters,id', 'different:form.corner_blue_fighter_id'],
            'form.corner_blue_fighter_id' => ['required', 'integer', 'exists:fighters,id', 'different:form.corner_red_fighter_id'],
            'form.title' => ['nullable', 'string', 'max:160'],
            'form.bout_type' => ['required', Rule::in(array_keys($this->boutTypeOptions()))],
            'form.rounds' => ['required', 'integer', 'min:1', 'max:12'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:999'],
            'form.starts_at' => ['nullable', 'date'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.is_main_event' => ['boolean'],
            'form.is_featured' => ['boolean'],
            'form.notes' => ['nullable', 'string', 'max:3000'],
            'promoImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.event_id' => __('mma.admin.fights.form.event_id'),
            'form.weight_class_id' => __('mma.admin.fights.form.weight_class_id'),
            'form.corner_red_fighter_id' => __('mma.admin.fights.form.corner_red_fighter_id'),
            'form.corner_blue_fighter_id' => __('mma.admin.fights.form.corner_blue_fighter_id'),
            'form.title' => __('mma.admin.fights.form.title'),
            'form.bout_type' => __('mma.admin.fights.form.bout_type'),
            'form.rounds' => __('mma.admin.fights.form.rounds'),
            'form.display_order' => __('mma.admin.fights.form.display_order'),
            'form.starts_at' => __('mma.admin.fights.form.starts_at'),
            'form.status' => __('mma.admin.fights.form.status'),
            'form.is_main_event' => __('mma.admin.fights.form.is_main_event'),
            'form.is_featured' => __('mma.admin.fights.form.is_featured'),
            'form.notes' => __('mma.admin.fights.form.notes'),
            'promoImage' => __('mma.admin.fights.form.promo_image'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->promoImage = null;
        $this->currentPromoImage = null;
        $this->form = [
            'event_id' => null,
            'weight_class_id' => null,
            'corner_red_fighter_id' => null,
            'corner_blue_fighter_id' => null,
            'title' => '',
            'bout_type' => 'regular',
            'rounds' => 3,
            'display_order' => 1,
            'starts_at' => '',
            'status' => 0,
            'is_main_event' => false,
            'is_featured' => false,
            'notes' => '',
        ];
        $this->resetErrorBag();
    }

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['weight_class_id', 'title', 'starts_at', 'notes'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    /**
     * Gestiona store promo image dentro de la tabla de combates.
     */
    protected function storePromoImage(ImageUploadOptimizer $images, array &$validated, Fight $fight): void
    {
        if (! $this->promoImage) {
            return;
        }

        $validated['promo_image'] = app(PublicMediaService::class)
            ->store($this->promoImage, 'fights', 'fight-promo');
        $this->deleteStoredImage($fight->promo_image);
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        app(PublicMediaService::class)->delete($path);
    }

    /**
     * Gestiona apply fighter search dentro de la tabla de combates.
     */
    protected function applyFighterSearch($query): void
    {
        $query->where('first_name', 'like', "%{$this->search}%")
            ->orWhere('last_name', 'like', "%{$this->search}%")
            ->orWhere('nickname', 'like', "%{$this->search}%");
    }

    /**
     * Gestiona fight name dentro de la tabla de combates.
     */
    protected function fightName(Fight $fight): string
    {
        return $fight->title ?: $this->fighterName($fight->cornerRed).' vs '.$this->fighterName($fight->cornerBlue);
    }
}
