<?php

namespace App\Livewire\Admin\Landing;

use App\Models\Event;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class LandingSettingsPanel extends Component
{
    public array $form = [
        'public_title' => 'Combate Real',
        'short_description' => '',
        'seo_title' => '',
        'seo_description' => '',
        'landing_show_rankings' => false,
    ];

    /**
     * Inicializa el componente de landing settings panel.
     */
    public function mount(): void
    {
        Gate::authorize('landing.update');

        $settings = $this->settings();
        $this->form = [
            'public_title' => $settings->public_title,
            'short_description' => $settings->short_description ?? '',
            'seo_title' => $settings->seo_title ?? '',
            'seo_description' => $settings->seo_description ?? '',
            'landing_show_rankings' => (bool) $settings->landing_show_rankings,
        ];
    }

    /**
     * Valida y guarda los datos del formulario.
     */
    public function save(): void
    {
        Gate::authorize('landing.update');

        $validated = $this->validate($this->rules(), attributes: $this->attributes())['form'];
        $this->normalizeNullableFields($validated);

        $settings = $this->settings();
        $settings->fill($validated);
        $settings->save();

        $this->dispatch('successAlert', ['success' => __('mma.admin.landing.messages.updated')]);
    }

    /**
     * Renderiza la tabla de landing settings panel con filtros activos.
     */
    public function render()
    {
        return view('livewire.admin.landing.landing-settings-panel', [
            'stats' => $this->landingStats(),
            'latestEvents' => $this->latestEvents(),
        ]);
    }

    /**
     * Define las reglas de validación del formulario.
     */
    protected function rules(): array
    {
        return [
            'form.public_title' => ['required', 'string', 'max:180'],
            'form.short_description' => ['nullable', 'string', 'max:3000'],
            'form.seo_title' => ['nullable', 'string', 'max:180'],
            'form.seo_description' => ['nullable', 'string', 'max:300'],
            'form.landing_show_rankings' => ['boolean'],
        ];
    }

    /**
     * Traduce los atributos usados por la validación.
     */
    protected function attributes(): array
    {
        return [
            'form.public_title' => __('mma.admin.landing.form.public_title'),
            'form.short_description' => __('mma.admin.landing.form.short_description'),
            'form.seo_title' => __('mma.admin.landing.form.seo_title'),
            'form.seo_description' => __('mma.admin.landing.form.seo_description'),
            'form.landing_show_rankings' => __('mma.admin.landing.form.landing_show_rankings'),
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
                'logo_path' => 'images/mma/brand/combate-real-logo.svg',
                'favicon_path' => 'images/mma/brand/combate-real-favicon.png',
                'landing_show_rankings' => false,
            ]
        );
    }

    /**
     * Convierte campos vacíos en valores nulos persistibles.
     */
    protected function normalizeNullableFields(array &$validated): void
    {
        foreach (['short_description', 'seo_title', 'seo_description'] as $field) {
            if (($validated[$field] ?? null) === '') {
                $validated[$field] = null;
            }
        }

        $validated['landing_show_rankings'] = (bool) $validated['landing_show_rankings'];
    }

    /**
     * Gestiona landing stats dentro de la tabla de landing settings panel.
     */
    protected function landingStats(): array
    {
        return [
            'published' => Event::query()->published()->count(),
            'featured' => Event::query()->published()->where('is_featured', true)->count(),
            'drafts' => Event::query()->where('status', 0)->count(),
        ];
    }

    /**
     * Gestiona latest events dentro de la tabla de landing settings panel.
     */
    protected function latestEvents()
    {
        return Event::query()
            ->with(['venue:id,name'])
            ->select(['id', 'venue_id', 'name', 'slug', 'starts_at', 'is_featured', 'status'])
            ->published()
            ->orderByDesc('is_featured')
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('starts_at')
            ->limit(6)
            ->get();
    }
}
