<?php

namespace App\Livewire\Admin\FighterTeams;

use App\Models\City;
use App\Models\FighterTeam;
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

    /**
     * Inicializa el componente de equipos de peleadores.
     */
    public function mount(): void
    {
        Gate::authorize('fighter_teams.view');
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
     * Reinicia la paginación al cambiar la ciudad.
     */
    public function updatingCityId(): void
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
        Gate::authorize('fighter_teams.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
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

    /**
     * Valida y guarda los datos del formulario.
     */
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

    /**
     * Prepara la confirmación de eliminación del registro.
     */
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

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
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
        $this->reset(['search', 'status', 'cityId']);
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
     * Renderiza la tabla de equipos de peleadores con filtros activos.
     */
    public function render()
    {
        $fighterTeams = FighterTeam::query()
            ->with(['city:id,name,country_id', 'city.country:id,name'])
            ->withCount('fighters')
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
                $this->slugTouched ? Rule::unique('fighter_teams', 'slug')->ignore($this->editingId) : null,
            ]),
            'form.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'form.coach_name' => ['nullable', 'string', 'max:120'],
            'form.contact_phone' => ['nullable', 'string', 'max:30'],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.status' => ['required', Rule::in([0, 1])],
            'logoImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
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

    /**
     * Restaura el formulario a sus valores iniciales.
     */
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

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['city_id', 'coach_name', 'contact_phone', 'description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    /**
     * Gestiona store logo dentro de la tabla de equipos de peleadores.
     */
    protected function storeLogo(ImageUploadOptimizer $images, array &$validated, FighterTeam $team): void
    {
        if (! $this->logoImage) {
            return;
        }

        $validated['logo_path'] = app(PublicMediaService::class)
            ->store($this->logoImage, 'fighter-teams', 'fighter-team-logo');
        $this->deleteStoredImage($team->logo_path);
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        app(PublicMediaService::class)->delete($path);
    }
}
