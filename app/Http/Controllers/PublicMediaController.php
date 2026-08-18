<?php

namespace App\Http\Controllers;

use App\Services\PublicMediaService;
use Illuminate\Support\Facades\Storage;

class PublicMediaController extends Controller
{
    /**
     * Sirve imágenes administradas sin revelar la ubicación real del archivo.
     */
    public function show(string $path, PublicMediaService $media)
    {
        $normalized = $media->normalize($path);
        abort_unless($normalized, 404);

        $disk = Storage::disk($media->disk());
        abort_unless($disk->exists($normalized), 404);

        return $disk->response($normalized, null, [
            'Cache-Control' => 'public, max-age=604800, immutable',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
