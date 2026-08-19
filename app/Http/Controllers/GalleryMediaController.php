<?php

namespace App\Http\Controllers;

use App\Models\EventMedia;
use App\Models\FighterMedia;
use App\Services\PublicMediaService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class GalleryMediaController extends Controller
{
    /**
     * Mapea cada prefijo administrado a su modelo y permiso de visualización.
     *
     * @var array<string, array{model: class-string, permission: string}>
     */
    private const GALLERIES = [
        'event-media' => [
            'model' => EventMedia::class,
            'permission' => 'event_media.view',
        ],
        'fighter-media' => [
            'model' => FighterMedia::class,
            'permission' => 'fighter_media.view',
        ],
    ];

    /**
     * Sirve las galerías de eventos y peleadores respetando su estado de
     * publicación.
     *
     * A diferencia de PublicMediaController, esta ruta corre dentro del grupo
     * `web` (con sesión) porque necesita saber quién pide el archivo: un
     * admin con permiso siempre ve la verdad al día, sin caché; cualquier
     * otro visitante solo ve piezas Activas, con caché pública larga (el
     * archivo se renombra en cada cambio de estado, así que esa URL nunca
     * queda desactualizada).
     */
    public function show(string $galleryPath, PublicMediaService $media)
    {
        $normalized = $media->normalize($galleryPath);
        abort_unless($normalized, 404);

        $gallery = self::GALLERIES[explode('/', $normalized)[1] ?? ''] ?? null;
        abort_unless($gallery, 404);

        $disk = Storage::disk($media->disk());
        abort_unless($disk->exists($normalized), 404);

        if (auth()->check() && Gate::allows($gallery['permission'])) {
            return $disk->response($normalized, null, [
                'Cache-Control' => 'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $record = $gallery['model']::query()
            ->where('file_path', $normalized)
            ->where('file_type', 'image')
            ->first();

        abort_unless($record && (int) $record->status === 1, 404);

        return $disk->response($normalized, null, [
            'Cache-Control' => 'public, max-age=604800, immutable',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
