<?php

namespace App\Support;

use App\Services\PublicMediaService;

class PublicMedia
{
    /**
     * Resuelve imágenes administradas mediante rutas públicas controladas.
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        return app(PublicMediaService::class)->url($path, $fallback);
    }
}
