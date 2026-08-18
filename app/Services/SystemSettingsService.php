<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Support\PublicMedia;
use Illuminate\Support\Facades\Schema;

class SystemSettingsService
{
    private ?SystemSetting $settings = null;
    private bool $loaded = false;

    /**
     * Carga la configuración general del sistema.
     */
    public function settings(): ?SystemSetting
    {
        if ($this->loaded) {
            return $this->settings;
        }

        $this->loaded = true;

        try {
            if (! Schema::hasTable('system_settings')) {
                return null;
            }

            $this->settings = SystemSetting::query()->first();
        } catch (\Throwable) {
            $this->settings = null;
        }

        return $this->settings;
    }

    /**
     * Ejecuta la operación product name del servicio.
     */
    public function productName(): string
    {
        return $this->filledSetting('product_name', (string) config('panel.brand.name', config('app.name', 'Combate Real')));
    }

    /**
     * Ejecuta la operación public title del servicio.
     */
    public function publicTitle(): string
    {
        return $this->filledSetting('public_title', $this->productName());
    }

    /**
     * Ejecuta la operación logo path del servicio.
     */
    public function logoPath(): string
    {
        return $this->filledSetting('logo_path', (string) config('panel.brand.logo', 'images/mma/brand/combate-real-logo.svg'));
    }

    /**
     * Ejecuta la operación favicon path del servicio.
     */
    public function faviconPath(): string
    {
        return $this->filledSetting('favicon_path', (string) config('panel.brand.favicon', 'images/mma/brand/combate-real-favicon.png'));
    }

    /**
     * Ejecuta la operación default image path del servicio.
     */
    public function defaultImagePath(): ?string
    {
        return $this->settings()?->default_image;
    }

    /**
     * Devuelve la URL pública del logo.
     */
    public function logoUrl(): string
    {
        return PublicMedia::url($this->logoPath()) ?? asset($this->logoPath());
    }

    /**
     * Devuelve la URL pública de favicon.
     */
    public function faviconUrl(): string
    {
        return PublicMedia::url($this->faviconPath()) ?? asset($this->faviconPath());
    }

    /**
     * Ejecuta la operación contact email del servicio.
     */
    public function contactEmail(): ?string
    {
        return $this->settings()?->contact_email;
    }

    /**
     * Ejecuta la operación contact phone del servicio.
     */
    public function contactPhone(): ?string
    {
        return $this->settings()?->contact_phone;
    }

    /**
     * Ejecuta la operación whatsapp phone del servicio.
     */
    public function whatsappPhone(): ?string
    {
        return $this->settings()?->whatsapp_phone;
    }

    /**
     * Ejecuta la operación short description del servicio.
     */
    public function shortDescription(): ?string
    {
        return $this->settings()?->short_description;
    }

    /**
     * Ejecuta la operación seo title del servicio.
     */
    public function seoTitle(): string
    {
        return $this->filledSetting('seo_title', $this->publicTitle());
    }

    /**
     * Ejecuta la operación seo description del servicio.
     */
    public function seoDescription(): ?string
    {
        return $this->settings()?->seo_description;
    }

    /**
     * Indica si la landing debe mostrar rankings.
     */
    public function landingShowRankings(): bool
    {
        return (bool) ($this->settings()?->landing_show_rankings ?? false);
    }

    /**
     * Devuelve los enlaces sociales visibles.
     */
    public function socialLinks(): array
    {
        return (array) ($this->settings()?->social_links ?? []);
    }

    /**
     * Devuelve un ajuste solo cuando tiene contenido útil.
     */
    private function filledSetting(string $field, string $fallback): string
    {
        $value = $this->settings()?->{$field};

        return filled($value) ? (string) $value : $fallback;
    }
}
