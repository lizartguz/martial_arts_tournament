<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileUploadService
{
    public function __construct(
        private readonly ImageUploadOptimizer $imageUploadOptimizer
    ) {
    }

    /**
     * Guarda un comprobante privado, optimizando imágenes y validando PDF.
     *
     * @param  TemporaryUploadedFile|UploadedFile  $file
     */
    public function storePaymentProof($file, string $prefix = 'payment-proof'): array
    {
        $config = config('uploads.payment_proofs');
        $mimeType = strtolower((string) $file->getMimeType());

        if (in_array($mimeType, (array) $config['image_mimes'], true)) {
            $path = $this->imageUploadOptimizer->store(
                $file,
                (string) $config['directory'],
                $prefix,
                (int) $config['max_mb'],
                (string) $config['disk']
            );

            return $this->metadata($file, $path, 'image/jpeg');
        }

        if (in_array($mimeType, (array) $config['document_mimes'], true)) {
            $this->ensureAllowedSize($file, (int) $config['max_mb']);
            $path = $this->storeDocument($file, (string) $config['directory'], $prefix, (string) $config['disk']);

            return $this->metadata($file, $path, $mimeType);
        }

        throw new \InvalidArgumentException(__('mma.uploads.payment_proofs.invalid_type'));
    }

    /**
     * Reemplaza un comprobante privado conservando el anterior si el nuevo archivo falla.
     *
     * @param  TemporaryUploadedFile|UploadedFile  $file
     */
    public function replacePaymentProof(Model $model, $file, string $prefix = 'payment-proof', string $pathColumn = 'payment_proof_path'): array
    {
        $previousPath = $model->getAttribute($pathColumn);
        $stored = $this->storePaymentProof($file, $prefix);

        if ($model->exists && filled($previousPath)) {
            Storage::disk((string) config('uploads.payment_proofs.disk', 'local'))->delete($previousPath);
        }

        return $stored;
    }

    /**
     * @param  TemporaryUploadedFile|UploadedFile  $file
     */
    private function ensureAllowedSize($file, int $maxMegaBytes): void
    {
        $size = (int) ($file->getSize() ?: filesize($file->getRealPath()));
        $maxBytes = $maxMegaBytes * 1024 * 1024;

        if ($size > $maxBytes) {
            throw new \InvalidArgumentException(__('mma.uploads.payment_proofs.max_size', ['max' => $maxMegaBytes]));
        }
    }

    /**
     * @param  TemporaryUploadedFile|UploadedFile  $file
     */
    private function storeDocument($file, string $directory, string $prefix, string $disk): string
    {
        $extension = strtolower((string) $file->getClientOriginalExtension()) ?: 'pdf';
        $baseName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($baseName) ?: 'documento';
        $fileName = Str::slug($prefix).'_'.now()->format('Ymd_His').'_'.Str::random(8).'_'.$safeBaseName.'.'.$extension;
        $relativePath = rtrim($directory, '/').'/'.$fileName;

        Storage::disk($disk)->put($relativePath, file_get_contents($file->getRealPath()));

        return $relativePath;
    }

    /**
     * @param  TemporaryUploadedFile|UploadedFile  $file
     */
    private function metadata($file, string $path, string $mimeType): array
    {
        $disk = (string) config('uploads.payment_proofs.disk');
        $size = Storage::disk($disk)->size($path);

        return [
            'path' => $path,
            'mime' => $mimeType,
            'size' => $size,
            'original_name' => $file->getClientOriginalName(),
            'disk' => $disk,
        ];
    }
}
