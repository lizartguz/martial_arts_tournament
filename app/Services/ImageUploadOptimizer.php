<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageUploadOptimizer
{
    private const IMAGE_MAX_WIDTH = 1920;

    private const IMAGE_QUALITY = 80;

    /**
     * Ejecuta la operación store del servicio.
     *
     * @param  TemporaryUploadedFile|UploadedFile  $image
     */
    public function store(
        $image,
        string $directory,
        string $prefix = 'image',
        int $maxMegaBytes = 10,
        string $disk = 'public'
    ): string {
        $mimeType = strtolower((string) $image->getMimeType());
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (! $mimeType || ! in_array($mimeType, $allowedMimes, true)) {
            throw new \InvalidArgumentException(__('mma.uploads.public_images.invalid_type'));
        }

        $realPath = $image->getRealPath();
        $originalSize = $image->getSize() ?: \filesize($realPath);
        $maxBytes = $maxMegaBytes * 1024 * 1024;

        if ($originalSize > $maxBytes) {
            throw new \InvalidArgumentException(__('mma.uploads.public_images.max_size', ['max' => $maxMegaBytes]));
        }

        $optimized = $this->optimize($realPath, $mimeType);
        $fileName = $this->makeFileName($image, $prefix, $optimized['extension']);
        $relativePath = rtrim($directory, '/').'/'.$fileName;

        Storage::disk($disk)->put($relativePath, $optimized['content']);

        Log::info(
            'ImageUploadOptimizer: '.$originalSize.'B -> '.$optimized['size'].'B '.
            '('.round((1 - $optimized['size'] / max($originalSize, 1)) * 100).'% reducción) -> '.
            $disk.':'.$relativePath
        );

        return $relativePath;
    }

    /**
     * Ejecuta la operación optimize del servicio.
     */
    private function optimize(string $realPath, string $mimeType): array
    {
        if ($mimeType === 'image/gif') {
            $content = \file_get_contents($realPath);

            return [
                'content' => $content,
                'size' => \strlen($content),
                'mime' => 'image/gif',
                'extension' => 'gif',
            ];
        }

        $source = @\imagecreatefromstring(\file_get_contents($realPath));

        if (! $source) {
            throw new \RuntimeException(__('mma.uploads.public_images.process_failed'));
        }

        $sourceWidth = \imagesx($source);
        $sourceHeight = \imagesy($source);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;

        if ($sourceWidth > self::IMAGE_MAX_WIDTH) {
            $targetWidth = self::IMAGE_MAX_WIDTH;
            $targetHeight = (int) \round($sourceHeight * ($targetWidth / $sourceWidth));
        }

        $canvas = \imagecreatetruecolor($targetWidth, $targetHeight);
        $white = \imagecolorallocate($canvas, 255, 255, 255);
        \imagefill($canvas, 0, 0, $white);
        \imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );
        \imageinterlace($canvas, true);

        \ob_start();
        \imagejpeg($canvas, null, self::IMAGE_QUALITY);
        $content = (string) \ob_get_clean();

        \imagedestroy($source);
        \imagedestroy($canvas);

        return [
            'content' => $content,
            'size' => \strlen($content),
            'mime' => 'image/jpeg',
            'extension' => 'jpg',
        ];
    }

    /**
     * Ejecuta la operación make file name del servicio.
     *
     * @param  TemporaryUploadedFile|UploadedFile  $image
     */
    private function makeFileName($image, string $prefix, string $extension): string
    {
        $baseName = \pathinfo((string) $image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($baseName) ?: 'imagen';

        return Str::slug($prefix).'_'.now()->format('Ymd_His').'_'.Str::random(8).'_'.$safeBaseName.'.'.$extension;
    }
}
