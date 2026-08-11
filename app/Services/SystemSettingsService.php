<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

class SystemSettingsService
{
    private ?SystemSetting $settings = null;
    private bool $loaded = false;

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

    public function productName(): string
    {
        return $this->filledSetting('product_name', (string) config('panel.brand.name', config('app.name', 'Combate Real')));
    }

    public function publicTitle(): string
    {
        return $this->filledSetting('public_title', $this->productName());
    }

    public function logoPath(): string
    {
        return $this->filledSetting('logo_path', (string) config('panel.brand.logo', 'images/logo_circle.png'));
    }

    public function faviconPath(): string
    {
        return $this->filledSetting('favicon_path', (string) config('panel.brand.favicon', 'frontend/images/logo_with_text.png'));
    }

    public function defaultImagePath(): ?string
    {
        return $this->settings()?->default_image;
    }

    public function logoUrl(): string
    {
        return asset($this->logoPath());
    }

    public function faviconUrl(): string
    {
        return asset($this->faviconPath());
    }

    public function contactEmail(): ?string
    {
        return $this->settings()?->contact_email;
    }

    public function contactPhone(): ?string
    {
        return $this->settings()?->contact_phone;
    }

    public function whatsappPhone(): ?string
    {
        return $this->settings()?->whatsapp_phone;
    }

    public function shortDescription(): ?string
    {
        return $this->settings()?->short_description;
    }

    public function seoTitle(): string
    {
        return $this->filledSetting('seo_title', $this->publicTitle());
    }

    public function seoDescription(): ?string
    {
        return $this->settings()?->seo_description;
    }

    public function landingShowRankings(): bool
    {
        return (bool) ($this->settings()?->landing_show_rankings ?? false);
    }

    public function socialLinks(): array
    {
        return (array) ($this->settings()?->social_links ?? []);
    }

    private function filledSetting(string $field, string $fallback): string
    {
        $value = $this->settings()?->{$field};

        return filled($value) ? (string) $value : $fallback;
    }
}
