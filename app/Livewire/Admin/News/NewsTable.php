<?php

namespace App\Livewire\Admin\News;

use App\Models\NewsPost;
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

class NewsTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public string $search = '';
    public string $status = '';
    public string $featured = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $pendingDeleteId = null;
    public string $deleteName = '';
    public bool $slugTouched = false;
    public ?TemporaryUploadedFile $coverImage = null;
    public ?string $currentCoverImage = null;
    public array $form = [
        'title' => '',
        'slug' => '',
        'excerpt' => '',
        'content' => '',
        'status' => 0,
        'is_featured' => false,
        'published_at' => '',
    ];

    /**
     * Inicializa el componente de noticias.
     */
    public function mount(): void
    {
        Gate::authorize('news.view');
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
     * Sincroniza el formulario al cambiar title.
     */
    public function updatedFormTitle(string $value): void
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
        Gate::authorize('news.create');

        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Carga el registro seleccionado para editarlo.
     */
    public function edit(int $id): void
    {
        Gate::authorize('news.update');

        $post = NewsPost::query()->findOrFail($id);

        $this->editingId = $post->id;
        $this->slugTouched = true;
        $this->currentCoverImage = $post->cover_image;
        $this->form = [
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt ?? '',
            'content' => $post->content ?? '',
            'status' => (int) $post->status,
            'is_featured' => (bool) $post->is_featured,
            'published_at' => $post->published_at?->format('Y-m-d\TH:i') ?? '',
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize($this->editingId ? 'news.update' : 'news.create');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $requestedStatus = (int) $validated['status'];

        if ($requestedStatus === 1) {
            Gate::authorize('news.publish');
        }

        $post = $this->editingId
            ? NewsPost::query()->findOrFail($this->editingId)
            : new NewsPost(['uuid' => (string) Str::uuid(), 'created_by' => auth()->id()]);

        $slugs = app(SlugGeneratorService::class);
        $validated['slug'] = $this->slugTouched && $validated['slug'] !== ''
            ? $validated['slug']
            : $slugs->uniqueForModel(NewsPost::class, $validated['title'], 'slug', $this->editingId);

        $this->normalizeNullableFields($validated);
        $validated['status'] = $requestedStatus;
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['updated_by'] = auth()->id();
        $validated['published_at'] = $this->resolvePublishedAt($post, $validated);

        $this->storeCoverImage($images, $validated, $post);

        $post->fill($validated);
        $post->save();

        $this->dispatch('successAlert', [
            'success' => $this->editingId
                ? __('mma.admin.news.messages.updated')
                : __('mma.admin.news.messages.created'),
        ]);

        $this->closeModal();
    }

    /**
     * Publica el registro seleccionado.
     */
    public function publish(int $id): void
    {
        Gate::authorize('news.publish');

        $post = NewsPost::query()->findOrFail($id);
        $post->forceFill([
            'status' => 1,
            'published_at' => $post->published_at ?: now(),
            'updated_by' => auth()->id(),
        ])->save();

        $this->dispatch('successAlert', ['success' => __('mma.admin.news.messages.published')]);
    }

    /**
     * Prepara la confirmación de eliminación del registro.
     */
    public function confirmDelete(int $id): void
    {
        Gate::authorize('news.delete');

        $post = NewsPost::query()->findOrFail($id);

        $this->pendingDeleteId = $post->id;
        $this->deleteName = $post->title;
        $this->showDeleteModal = true;
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(): void
    {
        Gate::authorize('news.delete');

        if (! $this->pendingDeleteId) {
            return;
        }

        $post = NewsPost::query()->findOrFail($this->pendingDeleteId);
        $this->deleteStoredImage($post->cover_image);
        $post->delete();

        $this->closeDeleteModal();
        $this->dispatch('successAlert', ['success' => __('mma.admin.news.messages.deleted')]);
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
        $this->reset(['search', 'status', 'featured', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    /**
     * Devuelve las opciones de estado permitidas.
     */
    public function statusOptions(): array
    {
        return [
            0 => __('mma.admin.news.status.draft'),
            1 => __('mma.admin.news.status.published'),
            2 => __('mma.admin.news.status.archived'),
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
        ][$status] ?? 'tw-badge-gray';
    }

    /**
     * Renderiza la tabla de noticias con filtros activos.
     */
    public function render()
    {
        $posts = NewsPost::query()
            ->with(['createdBy:id,name,email'])
            /**
             * Aplica el filtro solo cuando existe un criterio activo.
             */
            ->when($this->search !== '', function ($query) {
                /**
                 * Agrupa condiciones adicionales dentro de la consulta.
                 */
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%")
                        ->orWhere('excerpt', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', (int) $this->status))
            ->when($this->featured !== '', fn ($query) => $query->where('is_featured', (int) $this->featured === 1))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('published_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('published_at', '<=', $this->dateTo))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.news.news-table', compact('posts'));
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:200'],
            'form.slug' => array_filter([
                'nullable',
                'string',
                'max:220',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $this->slugTouched ? Rule::unique('news_posts', 'slug')->ignore($this->editingId) : null,
            ]),
            'form.excerpt' => ['nullable', 'string', 'max:300'],
            'form.content' => ['nullable', 'string'],
            'form.status' => ['required', Rule::in(array_keys($this->statusOptions()))],
            'form.is_featured' => ['boolean'],
            'form.published_at' => ['nullable', 'date'],
            'coverImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.title' => __('mma.admin.news.form.title'),
            'form.slug' => __('mma.admin.news.form.slug'),
            'form.excerpt' => __('mma.admin.news.form.excerpt'),
            'form.content' => __('mma.admin.news.form.content'),
            'form.status' => __('mma.admin.news.form.status'),
            'form.is_featured' => __('mma.admin.news.form.is_featured'),
            'form.published_at' => __('mma.admin.news.form.published_at'),
            'coverImage' => __('mma.admin.news.form.cover_image'),
        ];
    }

    /**
     * Restaura el formulario a sus valores iniciales.
     */
    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->slugTouched = false;
        $this->coverImage = null;
        $this->currentCoverImage = null;
        $this->form = [
            'title' => '',
            'slug' => '',
            'excerpt' => '',
            'content' => '',
            'status' => 0,
            'is_featured' => false,
            'published_at' => '',
        ];
        $this->resetErrorBag();
    }

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['excerpt', 'content', 'published_at'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }
    }

    /**
     * Resuelve published at desde la configuración disponible.
     */
    protected function resolvePublishedAt(NewsPost $post, array $validated)
    {
        if ((int) $validated['status'] !== 1) {
            return null;
        }

        return $validated['published_at'] ?: ($post->published_at ?: now());
    }

    /**
     * Gestiona store cover image dentro de la tabla de noticias.
     */
    protected function storeCoverImage(ImageUploadOptimizer $images, array &$validated, NewsPost $post): void
    {
        if (! $this->coverImage) {
            return;
        }

        $config = config('uploads.public_images');
        $path = $images->store(
            $this->coverImage,
            rtrim((string) $config['directory'], '/').'/news',
            'news-cover',
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        $validated['cover_image'] = 'storage/'.$path;
        $this->deleteStoredImage($post->cover_image);
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
    }
}
