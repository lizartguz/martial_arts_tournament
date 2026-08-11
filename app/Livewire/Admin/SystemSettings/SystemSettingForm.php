<?php

namespace App\Livewire\Admin\SystemSettings;

use App\Models\SystemSetting;
use App\Services\ImageUploadOptimizer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SystemSettingForm extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $logoImage = null;
    public ?TemporaryUploadedFile $faviconImage = null;
    public ?string $currentLogoPath = null;
    public ?string $currentFaviconPath = null;
    public array $form = [
        'product_name' => 'Combate Real',
        'public_title' => 'Combate Real',
        'contact_email' => '',
        'contact_phone' => '',
        'whatsapp_phone' => '',
        'short_description' => '',
        'seo_title' => '',
        'seo_description' => '',
        'landing_show_rankings' => false,
        'social_links' => [
            'facebook' => '',
            'instagram' => '',
            'youtube' => '',
            'tiktok' => '',
        ],
    ];

    /**
     * Inicializa el componente de system setting form.
     */
    public function mount(): void
    {
        Gate::authorize('system_settings.view');

        $settings = $this->settings();
        $this->currentLogoPath = $settings->logo_path;
        $this->currentFaviconPath = $settings->favicon_path;
        $this->form = [
            'product_name' => $settings->product_name,
            'public_title' => $settings->public_title,
            'contact_email' => $settings->contact_email ?? '',
            'contact_phone' => $settings->contact_phone ?? '',
            'whatsapp_phone' => $settings->whatsapp_phone ?? '',
            'short_description' => $settings->short_description ?? '',
            'seo_title' => $settings->seo_title ?? '',
            'seo_description' => $settings->seo_description ?? '',
            'landing_show_rankings' => (bool) $settings->landing_show_rankings,
            'social_links' => array_merge([
                'facebook' => '',
                'instagram' => '',
                'youtube' => '',
                'tiktok' => '',
            ], (array) $settings->social_links),
        ];
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(ImageUploadOptimizer $images): void
    {
        Gate::authorize('system_settings.update');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $settings = $this->settings();

        $this->normalizeNullableFields($validated);
        $this->storeImages($images, $validated, $settings);

        $settings->fill($validated);
        $settings->save();

        $this->currentLogoPath = $settings->logo_path;
        $this->currentFaviconPath = $settings->favicon_path;
        $this->logoImage = null;
        $this->faviconImage = null;

        $this->dispatch('successAlert', ['success' => __('mma.admin.system_settings.messages.updated')]);
    }

    /**
     * Renderiza la tabla de system setting form con filtros activos.
     */
    public function render()
    {
        return view('livewire.admin.system-settings.system-setting-form');
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.product_name' => ['required', 'string', 'max:150'],
            'form.public_title' => ['required', 'string', 'max:180'],
            'form.contact_email' => ['nullable', 'email', 'max:150'],
            'form.contact_phone' => ['nullable', 'string', 'max:30'],
            'form.whatsapp_phone' => ['nullable', 'string', 'max:30'],
            'form.short_description' => ['nullable', 'string', 'max:3000'],
            'form.seo_title' => ['nullable', 'string', 'max:180'],
            'form.seo_description' => ['nullable', 'string', 'max:300'],
            'form.landing_show_rankings' => ['boolean'],
            'form.social_links.facebook' => ['nullable', 'url', 'max:500'],
            'form.social_links.instagram' => ['nullable', 'url', 'max:500'],
            'form.social_links.youtube' => ['nullable', 'url', 'max:500'],
            'form.social_links.tiktok' => ['nullable', 'url', 'max:500'],
            'logoImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'faviconImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.product_name' => __('mma.admin.system_settings.form.product_name'),
            'form.public_title' => __('mma.admin.system_settings.form.public_title'),
            'form.contact_email' => __('mma.admin.system_settings.form.contact_email'),
            'form.contact_phone' => __('mma.admin.system_settings.form.contact_phone'),
            'form.whatsapp_phone' => __('mma.admin.system_settings.form.whatsapp_phone'),
            'form.short_description' => __('mma.admin.system_settings.form.short_description'),
            'form.seo_title' => __('mma.admin.system_settings.form.seo_title'),
            'form.seo_description' => __('mma.admin.system_settings.form.seo_description'),
            'form.landing_show_rankings' => __('mma.admin.system_settings.form.landing_show_rankings'),
            'form.social_links.facebook' => __('mma.admin.system_settings.social.facebook'),
            'form.social_links.instagram' => __('mma.admin.system_settings.social.instagram'),
            'form.social_links.youtube' => __('mma.admin.system_settings.social.youtube'),
            'form.social_links.tiktok' => __('mma.admin.system_settings.social.tiktok'),
            'logoImage' => __('mma.admin.system_settings.form.logo_path'),
            'faviconImage' => __('mma.admin.system_settings.form.favicon_path'),
        ];
    }

    /**
     * Carga la configuración general del sistema.
     */
    protected function settings(): SystemSetting
    {
        return SystemSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'product_name' => 'Combate Real',
                'public_title' => 'Combate Real',
                'logo_path' => 'images/logo_circle.png',
                'favicon_path' => 'frontend/images/logo_with_text.png',
                'landing_show_rankings' => false,
            ]
        );
    }

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['contact_email', 'contact_phone', 'whatsapp_phone', 'short_description', 'seo_title', 'seo_description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        foreach ($validated['social_links'] as $key => $value) {
            if ($value === '') {
                $validated['social_links'][$key] = null;
            }
        }

        $validated['landing_show_rankings'] = (bool) $validated['landing_show_rankings'];
    }

    /**
     * Guarda las imágenes enviadas desde el formulario.
     */
    protected function storeImages(ImageUploadOptimizer $images, array &$validated, SystemSetting $settings): void
    {
        if ($this->logoImage) {
            $validated['logo_path'] = $this->storeImage($images, $this->logoImage, 'system-logo');
            $this->deleteStoredImage($settings->logo_path);
        }

        if ($this->faviconImage) {
            $validated['favicon_path'] = $this->storeImage($images, $this->faviconImage, 'system-favicon');
            $this->deleteStoredImage($settings->favicon_path);
        }
    }

    /**
     * Gestiona store image dentro de la tabla de system setting form.
     */
    protected function storeImage(ImageUploadOptimizer $images, TemporaryUploadedFile $image, string $prefix): string
    {
        $config = config('uploads.public_images');
        $path = $images->store(
            $image,
            rtrim((string) $config['directory'], '/').'/system',
            $prefix,
            (int) $config['max_mb'],
            (string) $config['disk']
        );

        return 'storage/'.$path;
    }

    /**
     * Elimina un archivo almacenado si existe.
     */
    protected function deleteStoredImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(str($path)->after('storage/')->toString());
    }
}
