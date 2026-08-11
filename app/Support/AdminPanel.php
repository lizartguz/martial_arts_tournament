<?php

namespace App\Support;

use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class AdminPanel
{
    /**
     * Obtiene el nombre visible de la marca del panel.
     */
    public static function brandName(): string
    {
        return app(SystemSettingsService::class)->productName();
    }

    /**
     * Obtiene la URL del logotipo configurado para el panel.
     */
    public static function brandLogo(): string
    {
        return app(SystemSettingsService::class)->logoUrl();
    }

    /**
     * Obtiene la URL del favicon configurado para el panel.
     */
    public static function favicon(): string
    {
        return app(SystemSettingsService::class)->faviconUrl();
    }

    /**
     * Construye la URL de inicio configurada para el panel.
     */
    public static function homeUrl(): string
    {
        return url((string) config('panel.brand.home_url', '/dashboard'));
    }

    /**
     * Compone el titulo de pagina con el nombre de marca.
     */
    public static function pageTitle(?string $title = null): string
    {
        $baseTitle = self::brandName();

        return $title ? "{$title} | {$baseTitle}" : $baseTitle;
    }

    /**
     * Devuelve el menu visible segun los permisos del usuario.
     */
    public static function menu(): array
    {
        return self::filterVisible((array) config('panel.menu', []));
    }

    /**
     * Traduce una etiqueta de menu cuando existe una clave disponible.
     */
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

    /**
     * Normaliza una URL de menu como absoluta o local.
     */
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

    /**
     * Indica si un item de menu coincide con la ruta actual.
     */
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

    /**
     * Filtra los items de menu permitidos para el usuario actual.
     */
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

    /**
     * Verifica si el usuario puede ver un item de menu.
     */
    protected static function canView(array $item): bool
    {
        $permissions = (array) ($item['can'] ?? []);

        if ($permissions === []) {
            return true;
        }

        $user = Auth::user();

        return $user !== null && $user->canAny($permissions);
    }

    /**
     * Resuelve los patrones usados para marcar un menu como activo.
     */
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
