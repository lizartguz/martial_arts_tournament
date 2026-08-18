<?php

namespace Tests\Feature\Security;

use App\Services\PublicMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Imágenes de landing/admin servidas por ruta controlada.
 *
 * Las imágenes administradas viven en el disco privado
 * (`storage/app/private/mma/...`) y `/media/{path}` es su único canal de
 * lectura, así que la estructura real del storage no viaja en el HTML.
 *
 * No hay compatibilidad con el esquema anterior: un solo disco, y las rutas
 * con prefijo `storage/` se rechazan igual que cualquier otra ruta inválida.
 * Lo que se fija aquí es ese borde.
 */
class PublicMediaRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_it_serves_an_image_stored_on_the_private_disk(): void
    {
        Storage::disk('local')->put('mma/events/poster.webp', 'contenido-binario');

        $response = $this->get(route('media.public.show', ['path' => 'mma/events/poster.webp']));

        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame('contenido-binario', $response->streamedContent());
    }

    public function test_the_managed_disk_is_private_and_outside_the_webroot(): void
    {
        $this->assertSame('local', app(PublicMediaService::class)->disk());
        $this->assertStringNotContainsString(
            'app'.DIRECTORY_SEPARATOR.'public',
            (string) config('filesystems.disks.local.root')
        );
    }

    public function test_the_generated_url_never_exposes_the_storage_path(): void
    {
        $url = app(PublicMediaService::class)->url('mma/fighters/perfil.webp');

        $this->assertStringContainsString('/media/mma/fighters/perfil.webp', $url);
        $this->assertStringNotContainsString('/storage/', $url);
    }

    public function test_absolute_urls_and_static_assets_are_left_untouched(): void
    {
        $service = app(PublicMediaService::class);

        $this->assertSame('https://cdn.example.test/a.png', $service->url('https://cdn.example.test/a.png'));
        $this->assertStringContainsString('images/mma/generated/event-card.webp', $service->url(null, 'images/mma/generated/event-card.webp'));
        $this->assertNull($service->url(null));
    }

    public static function rejectedPathsProvider(): array
    {
        return [
            'traversal' => ['mma/../../.env'],
            'traversal codificado' => ['mma/%2e%2e/%2e%2e/.env'],
            'prefijo storage del esquema anterior' => ['storage/mma/events/poster.webp'],
            'fuera del directorio administrado' => ['payment-proofs/comprobante.jpg'],
            'extension no permitida' => ['mma/events/shell.php'],
            'sin extension' => ['mma/events/poster'],
            'segmento vacio' => ['mma//poster.webp'],
        ];
    }

    #[DataProvider('rejectedPathsProvider')]
    public function test_it_rejects_paths_outside_the_managed_image_directory(string $path): void
    {
        Storage::disk('local')->put('mma/events/poster.webp', 'no-servir-por-ruta-invalida');
        Storage::disk('local')->put('mma/events/shell.php', 'no-servir');
        Storage::disk('local')->put('payment-proofs/comprobante.jpg', 'privado');

        $this->assertNull(app(PublicMediaService::class)->normalize($path));

        $this->get('/media/'.ltrim($path, '/'))->assertNotFound();
    }

    public function test_a_missing_file_is_a_404_and_not_a_storage_error(): void
    {
        $this->get(route('media.public.show', ['path' => 'mma/events/no-existe.webp']))
            ->assertNotFound();
    }

    public function test_deleting_only_ever_touches_the_managed_directory(): void
    {
        $service = app(PublicMediaService::class);

        Storage::disk('local')->put('mma/news/cover.webp', 'administrada');
        Storage::disk('local')->put('payment-proofs/comprobante.jpg', 'privado');

        $service->delete('payment-proofs/comprobante.jpg');
        $service->delete('mma/../payment-proofs/comprobante.jpg');
        Storage::disk('local')->assertExists('payment-proofs/comprobante.jpg');

        $service->delete('mma/news/cover.webp');
        Storage::disk('local')->assertMissing('mma/news/cover.webp');
    }
}
