<?php

namespace Tests\Feature\Services;

use App\Services\ImageUploadOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Protege el comportamiento de optimización de imágenes administradas.
 *
 * El disco por defecto del optimizador es `local` (privado, fuera del webroot):
 * omitir el argumento nunca debe terminar escribiendo en `public`.
 *
 * El caso crítico es la transparencia: logos, favicons y sponsors se suben en
 * PNG/WebP con canal alfa, y aplanarlos a JPEG les deja un rectángulo blanco
 * de fondo que se ve roto sobre el sidebar oscuro y el login.
 */
class ImageUploadOptimizerTest extends TestCase
{
    public function test_png_with_transparency_keeps_its_format_and_alpha_channel(): void
    {
        Storage::fake('local');

        $stored = app(ImageUploadOptimizer::class)->store(
            $this->transparentPngUpload(),
            'test',
            'logo'
        );

        $this->assertStringEndsWith('.png', $stored, 'Un PNG no debe convertirse a otro formato.');

        $result = @imagecreatefromstring(Storage::disk('local')->get($stored));
        $this->assertNotFalse($result);

        // Esquina superior izquierda: era 100% transparente en el original.
        $corner = imagecolorat($result, 2, 2);
        $alpha = ($corner >> 24) & 0x7F;
        $this->assertSame(127, $alpha, 'La transparencia del PNG debe conservarse (alpha 127 = totalmente transparente).');

        // Centro: era rojo opaco, debe seguir siéndolo.
        $center = imagecolorsforindex($result, imagecolorat($result, 50, 50));
        $this->assertSame(0, $center['alpha'], 'El área opaca debe seguir opaca.');
        $this->assertSame(255, $center['red']);
    }

    public function test_jpeg_upload_still_produces_an_optimized_jpeg(): void
    {
        Storage::fake('local');

        $stored = app(ImageUploadOptimizer::class)->store(
            UploadedFile::fake()->image('foto.jpg', 300, 300),
            'test',
            'foto'
        );

        $this->assertStringEndsWith('.jpg', $stored);
        $this->assertTrue(Storage::disk('local')->exists($stored));
    }

    public function test_oversized_image_is_resized_to_the_max_width(): void
    {
        Storage::fake('local');

        $stored = app(ImageUploadOptimizer::class)->store(
            UploadedFile::fake()->image('grande.jpg', 3000, 1500),
            'test',
            'grande'
        );

        $result = @imagecreatefromstring(Storage::disk('local')->get($stored));

        $this->assertSame(1920, imagesx($result), 'Las imágenes muy anchas deben redimensionarse a 1920px.');
        $this->assertSame(960, imagesy($result), 'La proporción original debe mantenerse.');
    }

    public function test_non_image_mime_is_rejected(): void
    {
        Storage::fake('local');

        $this->expectException(\InvalidArgumentException::class);

        app(ImageUploadOptimizer::class)->store(
            UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
            'test',
            'documento'
        );
    }

    /**
     * Construye un PNG real de 100x100 con fondo transparente y centro rojo.
     */
    private function transparentPngUpload(): UploadedFile
    {
        $image = imagecreatetruecolor(100, 100);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefilledrectangle($image, 0, 0, 99, 99, imagecolorallocatealpha($image, 0, 0, 0, 127));

        imagealphablending($image, true);
        imagefilledrectangle($image, 25, 25, 74, 74, imagecolorallocate($image, 255, 0, 0));

        ob_start();
        imagepng($image);
        $contents = (string) ob_get_clean();
        imagedestroy($image);

        $path = tempnam(sys_get_temp_dir(), 'optimizer').'.png';
        file_put_contents($path, $contents);

        return new UploadedFile($path, 'logo-transparente.png', 'image/png', null, true);
    }
}
