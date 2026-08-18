<?php

namespace App\Livewire\Admin\FighterMedia;

use App\Models\Fighter;
use App\Models\FighterMedia;
use App\Services\ImageUploadOptimizer;
use App\Services\PublicMediaService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FighterMediaTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;

    public string $search = '';

    public string $fighterId = '';

    public string $fileType = '';

    public string $status = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingId = null;

    public ?int $pendingDeleteId = null;

    public string $deleteName = '';

    public ?TemporaryUploadedFile $mediaImage = null;

    public ?string $currentFilePath = null;

    public array $form = [
        'fighter_id' => null,
        'file_type' => 'image',
        'file_path' => '',
        'title' => '',
        'description' => '',
        'is_featured' => false,
        'display_order' => 0,
        'status' => 1,
    ];

    /**
     * Inicializa el componente de fighter media.
     */
    public function mount(): void
    {
        Gate::authorize('fighter_media.view');
    }

    /**
     * Reinicia la paginación al cambiar la búsqueda.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el peleador.
     */
    public function updatingFighterId(): void
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación al cambiar el tipo de archivo.
     */
    public function updatingFileType(): void
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
     * Sincroniza el formulario al cambiar el tipo de archivo.
     */
    public function updatedFormFileType(string $value): void
    {
        $this->mediaImage = null;

        if ($value === 'image' && $this->editingId) {
            $this->form['file_path'] = $this->currentFilePath ?? '';
        }
    }

    /**
     * Abre el formulario para crear un registro.
     */
    public function create(): void
    {
        Gate::authorize('fighter_media.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('fighter_media.update');

        $media = FighterMedia::query()->findOrFail($id);

        $this->editingId = $media->id;
        $this->currentFilePath = $media->file_type === 'image' ? $media->file_path : null;
        $this->form = [
            'fighter_id' => $media->fighter_id,
            'file_type' => $media->file_type,
            'file_path' => $media->file_path,
            'title' => $media->title ?? '',
            'description' => $media->description ?? '',
            'is_featured' => (bool) $media->is_featured,
            'display_order' => (int) $media->display_order,
            'status' => (int) $media->status,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'fighter_media.update' : 'fighter_media.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $media = $this->editingId
            ? FighterMedia::query()->findOrFail($this->editingId)
            : new FighterMedia(['created_by' => auth()->id()]);

        $this->normalizeNullableFields($validated);

        if (! $this->prepareFilePath($images, $validated, $media)) {
            return;
        }

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $media->fill($validated);
        $media->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.fighter_media.messages.updated')
                : __('mma.admin.fighter_media.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('fighter_media.delete');

        $media = FighterMedia::query()->findOrFail($id);

        $this->pendingDeleteId = $media->id;
        $this->deleteName = $media->title ?: $this->fileTypeLabel($media->file_type);
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('fighter_media.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $media = FighterMedia::query()->findOrFail($this->pendingDeleteId);
        $this->deleteStoredImage($media->file_path);
        $media->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.fighter_media.messages.deleted')]);
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
        $this->reset(['search', 'fighterId', 'fileType', 'status']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones disponibles de file type.
     */
    public function fileTypeOptions(): array
    {
        return [
            'image' => __('mma.admin.fighter_media.file_types.image'),
            'video' => __('mma.admin.fighter_media.file_types.video'),
        ];
    }

    /**
     * Devuelve la etiqueta visible de file type.
     */
    public function fileTypeLabel(?string $fileType): string
    {
        return $this->fileTypeOptions()[$fileType] ?? __('mma.admin.common.not_available');
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
     * Gestiona fighter name dentro de la tabla de fighter media.
     */
    public function fighterName(?Fighter $fighter): string
    {
        if (! $fighter) {
            return __('mma.admin.common.not_available');
        }

        return trim($fighter->first_name.' '.$fighter->last_name) ?: ($fighter->nickname ?? __('mma.admin.common.not_available'));
    }

    /**
     * Renderiza la tabla de fighter media con filtros activos.
     */
    public function render()
    {
        $mediaItems = FighterMedia::query()
            ->with(['fighter:id,first_name,last_name,nickname,slug'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        /**
                         * Agrega condiciones sobre la relacion consultada.
                         */
                        ->orWhereHas('fighter', function ($fighterQuery) {
                            $fighterQuery->where('first_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%")
                                ->orWhere('nickname', 'like', "%{$this->search}%")
                                ->orWhere('slug', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->fighterId !== '', fn ($query) => $query->where('fighter_id', (int) $this->fighterId))
            ->when($this->fileType !== '', fn ($query) => $query->where('file_type', $this->fileType))
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $fighters = Fighter::query()
            ->select(['id', 'first_name', 'last_name', 'nickname', 'slug'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('livewire.admin.fighter-media.fighter-media-table', compact('mediaItems', 'fighters'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.fighter_id' => ['required', 'integer', 'exists:fighters,id'],
            'form.file_type' => ['required', Rule::in(array_keys($this->fileTypeOptions()))],
            'form.file_path' => $this->filePathRules(),
            'form.title' => ['nullable', 'string', 'max:180'],
            'form.description' => ['nullable', 'string', 'max:3000'],
            'form.is_featured' => ['boolean'],
            'form.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.status' => ['required', Rule::in([0, 1])],
            'mediaImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.fighter_id' => __('mma.admin.fighter_media.form.fighter_id'),
            'form.file_type' => __('mma.admin.fighter_media.form.file_type'),
            'form.file_path' => __('mma.admin.fighter_media.form.file_path'),
            'form.title' => __('mma.admin.fighter_media.form.title'),
            'form.description' => __('mma.admin.fighter_media.form.description'),
            'form.is_featured' => __('mma.admin.fighter_media.form.is_featured'),
            'form.display_order' => __('mma.admin.fighter_media.form.display_order'),
            'form.status' => __('mma.admin.fighter_media.form.status'),
            'mediaImage' => __('mma.admin.fighter_media.form.media_image'),
        ];
    }

    /**
     * Gestiona file path rules dentro de la tabla de fighter media.
     */
    protected function filePathRules(): array
    {
        $rules = [$this->form['file_type'] === 'video' ? 'required' : 'nullable', 'string', 'max:255'];

        if ($this->form['file_type'] === 'video') {
            $rules[] = 'url';
        }

        return $rules;
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->pendingDeleteId = null;
        $this->mediaImage = null;
        $this->currentFilePath = null;
        $this->form = [
            'fighter_id' => null,
            'file_type' => 'image',
            'file_path' => '',
            'title' => '',
            'description' => '',
            'is_featured' => false,
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
        foreach (['file_path', 'title', 'description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['display_order'] = (int) $validated['display_order'];
        $validated['status'] = (int) $validated['status'];
    }

    /**
     * Gestiona prepare file path dentro de la tabla de fighter media.
     */
    protected function prepareFilePath(ImageUploadOptimizer $images, array &$validated, FighterMedia $media): bool
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

        $this->addError('mediaImage', __('mma.admin.fighter_media.validation.image_required'));

        return false;
    }

    /**
     * Guarda una imagen pública optimizada.
     */
    protected function storePublicImage(ImageUploadOptimizer $images, TemporaryUploadedFile $image): string
    {
        return app(PublicMediaService::class)->store($image, 'fighter-media', 'fighter-media');
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        app(PublicMediaService::class)->delete($path);
    }
}
