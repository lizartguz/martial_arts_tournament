<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Punto único para guardar, borrar y resolver las imágenes administradas.
 *
 * Viven en un solo disco, el privado configurado en `uploads.public_images`.
 * Se leen por `media.public.show` (sin sesión) salvo las galerías con estado
 * de publicación (event-media/fighter-media), que van por `media.gallery.show`
 * porque esa ruta necesita saber quién pide el archivo. No hay segundo disco
 * ni rutas con prefijo `storage/`: lo que no encaje en `normalize()` es un
 * 404, no un caso a tolerar.
 */
class PublicMediaService
{
    /**
     * Disco donde viven las imágenes administradas.
     */
    public function disk(): string
    {
        return (string) config('uploads.public_images.disk', 'local');
    }

    /**
     * Genera una URL pública sin exponer la ubicación física del archivo.
     */
    public function url(?string $path, ?string $fallback = null): ?string
    {
        $candidate = filled($path) ? (string) $path : $fallback;

        if (! filled($candidate)) {
            return null;
        }

        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $candidate;
        }

        $normalized = $this->normalize($candidate);

        if ($normalized) {
            return $this->isGalleryPath($normalized)
                ? route('media.gallery.show', ['galleryPath' => $normalized])
                : route('media.public.show', ['path' => $normalized]);
        }

        // Estáticos del repo (`images/mma/brand/...`), que no pasan por storage.
        return asset(ltrim($candidate, '/'));
    }

    /**
     * Guarda una imagen administrada en el disco privado.
     *
     * @param  TemporaryUploadedFile|UploadedFile  $image
     */
    public function store($image, string $directory, string $prefix): string
    {
        $config = config('uploads.public_images');

        return app(ImageUploadOptimizer::class)->store(
            $image,
            rtrim((string) $config['directory'], '/').'/'.trim($directory, '/'),
            $prefix,
            (int) $config['max_mb'],
            $this->disk()
        );
    }

    /**
     * Borra una imagen administrada.
     */
    public function delete(?string $path): void
    {
        $normalized = $this->normalize($path);

        if (! $normalized) {
            return;
        }

        Storage::disk($this->disk())->delete($normalized);
    }

    /**
     * Renombra un archivo administrado sin reprocesarlo.
     *
     * Se usa cuando cambia la visibilidad de una pieza (Activo/Inactivo) sin
     * que llegue un archivo nuevo: la URL anterior queda huérfana de
     * inmediato en vez de depender de que expire un caché.
     */
    public function rename(?string $path): ?string
    {
        $normalized = $this->normalize($path);

        if (! $normalized) {
            return null;
        }

        $disk = Storage::disk($this->disk());

        if (! $disk->exists($normalized)) {
            return null;
        }

        $directory = trim((string) pathinfo($normalized, PATHINFO_DIRNAME), '/');
        $extension = strtolower((string) pathinfo($normalized, PATHINFO_EXTENSION));
        $newPath = $directory.'/'.Str::random(32).'.'.$extension;

        if (! $disk->copy($normalized, $newPath)) {
            return null;
        }

        $disk->delete($normalized);

        return $newPath;
    }

    /**
     * Indica si la ruta pertenece a una galeria con estado de publicacion
     * (event-media/fighter-media), servida por media.gallery.show en vez de
     * media.public.show porque necesita saber quien la pide.
     */
    public function isGalleryPath(string $normalized): bool
    {
        return in_array(explode('/', $normalized)[1] ?? '', ['event-media', 'fighter-media'], true);
    }

    /**
     * Frontera de confianza: valida la ruta antes de tocar el disco.
     *
     * Devuelve null ante traversal, segmentos vacíos, bytes nulos, cualquier
     * ruta fuera del directorio administrado y cualquier extensión que no esté
     * en la lista blanca de imágenes.
     */
    public function normalize(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = ltrim(rawurldecode(str_replace('\\', '/', trim((string) $path))), '/');
        $segments = explode('/', $path);

        if ($path === '' || str_contains($path, "\0") || in_array('', $segments, true) || in_array('..', $segments, true)) {
            return null;
        }

        $root = trim((string) config('uploads.public_images.directory', 'mma'), '/');

        if (! Str::startsWith($path, $root.'/')) {
            return null;
        }

        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $allowedExtensions = (array) config('uploads.public_images.image_extensions', ['jpg', 'jpeg', 'png', 'webp']);

        if (! in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        return $path;
    }
}
