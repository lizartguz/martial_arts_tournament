<?php

namespace App\Services;

use App\Models\NotificationM;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationAttachmentService
{
    public const SLOT_COLUMNS = [
        'image' => 'image',
        'image_1' => 'image_1',
        'image_2' => 'image_2',
    ];

    /**
     * Inyecta las dependencias requeridas por la clase.
     */
    public function __construct(
        private readonly ImageUploadOptimizer $imageUploadOptimizer,
    ) {}

    /**
     * Ejecuta la operación store del servicio.
     */
    public function store($image, string $prefix): string
    {
        return $this->imageUploadOptimizer->store(
            $image,
            'notifications',
            $prefix,
            10,
            'local'
        );
    }

    /**
     * Elimina el registro seleccionado cuando está permitido.
     */
    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    /**
     * Devuelve la URL pública de web.
     */
    public function webUrl(NotificationM $notification, string $slot): ?string
    {
        return $this->pathForSlot($notification, $slot)
            ? route('notifications.images.show', [$notification, $slot])
            : null;
    }

    /**
     * Devuelve la URL pública de temporary api.
     */
    public function temporaryApiUrl(NotificationM $notification, string $slot, User $user): ?string
    {
        if (! $this->pathForSlot($notification, $slot)) {
            return null;
        }

        return URL::temporarySignedRoute(
            'api.notifications.images.show',
            now()->addMinutes((int) config('notifications.image_url_ttl_minutes', 15)),
            [
                'notification' => $notification,
                'slot' => $slot,
                'user' => $user,
            ]
        );
    }

    /**
     * Ejecuta la operación response del servicio.
     */
    public function response(NotificationM $notification, string $slot): StreamedResponse|Response
    {
        $path = $this->pathForSlot($notification, $slot);
        abort_if(! $path, 404);

        $disk = $this->diskContaining($path);
        abort_if(! $disk, 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Ejecuta la operación migrate public file del servicio.
     */
    public function migratePublicFile(?string $path, bool $dryRun = false): string
    {
        if (! $path) {
            return 'skipped';
        }

        $privateDisk = Storage::disk('local');
        $publicDisk = Storage::disk('public');

        if ($privateDisk->exists($path)) {
            if (! $dryRun && $publicDisk->exists($path)) {
                $this->deletePublicCopy($publicDisk, $path);
            }

            return 'already_private';
        }

        if (! $publicDisk->exists($path)) {
            return 'missing';
        }

        if ($dryRun) {
            return 'migrated';
        }

        $stream = $publicDisk->readStream($path);

        if ($stream === false) {
            throw new \RuntimeException("No se pudo leer el adjunto publico: {$path}");
        }

        try {
            if (! $privateDisk->writeStream($path, $stream)) {
                throw new \RuntimeException("No se pudo guardar el adjunto privado: {$path}");
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! $privateDisk->exists($path)) {
            throw new \RuntimeException("No se pudo verificar el adjunto privado: {$path}");
        }

        $this->deletePublicCopy($publicDisk, $path);

        return 'migrated';
    }

    /**
     * Ejecuta la operación path for slot del servicio.
     */
    public function pathForSlot(NotificationM $notification, string $slot): ?string
    {
        $column = self::SLOT_COLUMNS[$slot] ?? null;
        abort_if(! $column, 404);

        return $notification->{$column};
    }

    /**
     * Ejecuta la operación disk containing del servicio.
     */
    private function diskContaining(string $path): ?Filesystem
    {
        foreach (['local', 'public'] as $disk) {
            $filesystem = Storage::disk($disk);

            if ($filesystem->exists($path)) {
                return $filesystem;
            }
        }

        return null;
    }

    /**
     * Ejecuta la operación delete public copy del servicio.
     */
    private function deletePublicCopy(Filesystem $publicDisk, string $path): void
    {
        if (! $publicDisk->delete($path) || $publicDisk->exists($path)) {
            throw new \RuntimeException("No se pudo eliminar la copia publica del adjunto: {$path}");
        }
    }
}
