<?php

namespace App\Support;

use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class AdminPanel
{
    public static function brandName(): string
    {
        return app(SystemSettingsService::class)->productName();
    }

    public static function brandLogo(): string
    {
        return app(SystemSettingsService::class)->logoUrl();
    }

    public static function favicon(): string
    {
        return app(SystemSettingsService::class)->faviconUrl();
    }

    public static function homeUrl(): string
    {
        return url((string) config('panel.brand.home_url', '/dashboard'));
    }

    public static function pageTitle(?string $title = null): string
    {
        $baseTitle = self::brandName();

        return $title ? "{$title} | {$baseTitle}" : $baseTitle;
    }

    public static function menu(): array
    {
        return self::filterVisible((array) config('panel.menu', []));
    }

    public static function label(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $translated = __($value);

        if ($translated !== $value) {
            return $translated;
        }

        $menuKey = "menu.{$value}";

        if (Lang::has($menuKey)) {
            return __($menuKey);
        }

        return $value;
    }

    public static function url(array $item): string
    {
        $url = (string) ($item['url'] ?? '#');

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (Str::startsWith($url, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $url;
        }

        return url($url);
    }

    public static function isActive(array $item): bool
    {
        foreach ((array) ($item['submenu'] ?? []) as $child) {
            if (self::isActive($child)) {
                return true;
            }
        }

        foreach (self::activePatterns($item) as $pattern) {
            if (request()->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected static function filterVisible(array $items): array
    {
        $visible = [];

        foreach ($items as $item) {
            if (!self::canView($item)) {
                continue;
            }

            if (!empty($item['submenu'])) {
                $item['submenu'] = self::filterVisible((array) $item['submenu']);

                if ($item['submenu'] === []) {
                    continue;
                }
            }

            $visible[] = $item;
        }

        return $visible;
    }

    protected static function canView(array $item): bool
    {
        $permissions = (array) ($item['can'] ?? []);

        if ($permissions === []) {
            return true;
        }

        $user = Auth::user();

        return $user !== null && $user->canAny($permissions);
    }

    protected static function activePatterns(array $item): array
    {
        if (!empty($item['active'])) {
            return (array) $item['active'];
        }

        $url = trim((string) ($item['url'] ?? ''), '/');

        if ($url === '' || $url === '#') {
            return [];
        }

        return [$url, "{$url}/*"];
    }
}
