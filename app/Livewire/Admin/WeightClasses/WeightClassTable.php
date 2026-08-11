<?php

namespace App\Livewire\Admin\WeightClasses;

use App\Models\WeightClass;
use App\Services\SlugGeneratorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class WeightClassTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $gender = '';
    public string $status = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public array $form = [
        'name' => '',
        'slug' => '',
        'gender' => 'male',
        'min_weight_kg' => null,
        'max_weight_kg' => null,
        'max_weight_lb' => null,
        'display_order' => 1,
        'status' => 1,
    ];

    /**
     * Inicializa el componente de categorías de peso.
     */
    public function mount(): void
    {
        Gate::authorize('weight_classes.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el género.
     */
    public function updatingGender(): void
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
        Gate::authorize('weight_classes.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('weight_classes.update');

        $weightClass = WeightClass::query()->findOrFail($id);

        $this->editingId = $weightClass->id;
        $this->slugTouched = true;
        $this->form = [
            'name' => $weightClass->name,
            'slug' => $weightClass->slug,
            'gender' => $weightClass->gender,
            'min_weight_kg' => $weightClass->min_weight_kg,
            'max_weight_kg' => $weightClass->max_weight_kg,
            'max_weight_lb' => $weightClass->max_weight_lb,
            'display_order' => $weightClass->display_order,
            'status' => $weightClass->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize($this->editingId ? 'weight_classes.update' : 'weight_classes.create');

        $slugs = app(SlugGeneratorService::class);
        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(WeightClass::class, $validated['name'], 'slug', $this->editingId);

        $weightClass = $this->editingId
            ? WeightClass::query()->findOrFail($this->editingId)
            : new WeightClass();

        $weightClass->fill($validated);
        $weightClass->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.weight_classes.messages.updated')
                : __('mma.admin.weight_classes.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('weight_classes.delete');

        $weightClass = WeightClass::query()
            ->withCount(['fighters', 'fights', 'rankings'])
            ->findOrFail($id);

        if ($weightClass->fighters_count || $weightClass->fights_count || $weightClass->rankings_count) {
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.weight_classes.messages.delete_blocked'),
            ]);

            return;
        }

        $this->pendingDeleteId = $weightClass->id;
        $this->deleteName = $weightClass->name;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('weight_classes.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $weightClass = WeightClass::query()
            ->withCount(['fighters', 'fights', 'rankings'])
            ->findOrFail($this->pendingDeleteId);

        if ($weightClass->fighters_count || $weightClass->fights_count || $weightClass->rankings_count) {
            $this->closeDeleteModal();
            $this->dispatch('failedAlert', [
                'failed' => __('mma.admin.weight_classes.messages.delete_blocked'),
            ]);

            return;
        }

        $weightClass->delete();
        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.weight_classes.messages.deleted')]);
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
        $this->reset(['search', 'gender', 'status']);
        $this->resetPage();
    }

    /**
     * Traduce el género del registro.
     */
    public function genderLabel(string $gender): string
    {
        return __("mma.admin.weight_classes.gender.{$gender}");
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
     * Renderiza la tabla de categorías de peso con filtros activos.
     */
    public function render()
    {
        $weightClasses = WeightClass::query()
            ->withCount(['fighters', 'fights'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->when($this->gender !== '', fn ($query) => $query->where('gender', $this->gender))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.weight-classes.weight-class-table', compact('weightClasses'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:120'],
            'form.slug' => [
                'nullable',
                'string',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('weight_classes', 'slug')->ignore($this->editingId),
            ],
            'form.gender' => ['required', Rule::in(['male', 'female'])],
            'form.min_weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'form.max_weight_kg' => ['required', 'numeric', 'gt:form.min_weight_kg', 'max:999.99'],
            'form.max_weight_lb' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'form.display_order' => ['required', 'integer', 'min:1', 'max:999'],
            'form.status' => ['required', Rule::in([0, 1])],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.name' => __('mma.admin.weight_classes.form.name'),
            'form.slug' => __('mma.admin.weight_classes.form.slug'),
            'form.gender' => __('mma.admin.weight_classes.form.gender'),
            'form.min_weight_kg' => __('mma.admin.weight_classes.form.min_weight_kg'),
            'form.max_weight_kg' => __('mma.admin.weight_classes.form.max_weight_kg'),
            'form.max_weight_lb' => __('mma.admin.weight_classes.form.max_weight_lb'),
            'form.display_order' => __('mma.admin.weight_classes.form.display_order'),
            'form.status' => __('mma.admin.weight_classes.form.status'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->form = [
            'name' => '',
            'slug' => '',
            'gender' => 'male',
            'min_weight_kg' => null,
            'max_weight_kg' => null,
            'max_weight_lb' => null,
            'display_order' => 1,
            'status' => 1,
        ];
        $this->resetErrorBag();
    }
}
