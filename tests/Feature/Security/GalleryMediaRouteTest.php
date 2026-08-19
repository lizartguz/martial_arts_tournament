<?php

namespace Tests\Feature\Security;

use App\Http\Controllers\PublicMediaController;
use App\Models\Event;
use App\Models\EventMedia;
use App\Models\Fighter;
use App\Models\FighterMedia;
use App\Services\PublicMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

/**
 * Galerías de eventos y peleadores (event-media/fighter-media).
 *
 * A diferencia del resto de las carpetas administradas, estas dos tienen un
 * estado de publicación (Activo/Inactivo) que puede cambiar sin que cambie el
 * nombre del archivo. `/media/mma/event-media/...` y
 * `/media/mma/fighter-media/...` van por `GalleryMediaController`, que sí
 * conoce la sesión, en vez del `PublicMediaController` sin sesión que sirve
 * las otras 8 carpetas.
 */
class GalleryMediaRouteTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
        Storage::fake('local');

        // Livewire deja un flag estatico prendido (no lo resetea entre tests)
        // en cuanto CUALQUIER componente de pagina completa se renderiza en
        // el proceso de PHPUnit. Con el prendido, su middleware global fuerza
        // Cache-Control: no-store en TODA respuesta del grupo web, sin
        // importar que controlador la genere. No es un bug de esta suite: es
        // un efecto de que PHPUnit corre muchos tests en un solo proceso.
        \Livewire\Features\SupportDisablingBackButtonCache\SupportDisablingBackButtonCache::$disableBackButtonCache = false;
    }

    public static function galleriesProvider(): array
    {
        return [
            'event-media' => ['event-media', 'event_media.view'],
            'fighter-media' => ['fighter-media', 'fighter_media.view'],
        ];
    }

    #[DataProvider('galleriesProvider')]
    public function test_a_guest_can_view_an_active_piece_with_a_long_public_cache(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/pieza-activa.webp";
        Storage::disk('local')->put($path, 'contenido-activo');
        $this->createMediaRecord($prefix, $path, status: 1);

        $response = $this->get('/media/'.$path);

        $response->assertOk();
        $this->assertSame('contenido-activo', $response->streamedContent());
        $this->assertStringContainsString('public', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('immutable', (string) $response->headers->get('Cache-Control'));
    }

    #[DataProvider('galleriesProvider')]
    public function test_a_guest_cannot_view_an_inactive_piece(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/pieza-inactiva.webp";
        Storage::disk('local')->put($path, 'contenido-inactivo');
        $this->createMediaRecord($prefix, $path, status: 0);

        $this->get('/media/'.$path)->assertNotFound();
    }

    #[DataProvider('galleriesProvider')]
    public function test_a_guest_gets_404_for_an_orphan_file_without_a_database_record(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/huerfano.webp";
        Storage::disk('local')->put($path, 'sin-registro');

        $this->get('/media/'.$path)->assertNotFound();
    }

    #[DataProvider('galleriesProvider')]
    public function test_an_authorized_viewer_sees_inactive_pieces_without_any_caching(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/pieza-inactiva.webp";
        Storage::disk('local')->put($path, 'contenido-inactivo');
        $this->createMediaRecord($prefix, $path, status: 0);

        $this->actingAsRole('admin');

        $response = $this->get('/media/'.$path);

        $response->assertOk();
        $this->assertSame('contenido-inactivo', $response->streamedContent());
        $this->assertPrivateNoStore($response);
    }

    #[DataProvider('galleriesProvider')]
    public function test_an_authorized_viewer_never_gets_a_cached_response_even_for_active_pieces(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/pieza-activa.webp";
        Storage::disk('local')->put($path, 'contenido-activo');
        $this->createMediaRecord($prefix, $path, status: 1);

        $this->actingAsRole('admin');

        $this->assertPrivateNoStore($this->get('/media/'.$path)->assertOk());
    }

    #[DataProvider('galleriesProvider')]
    public function test_a_logged_in_user_without_the_permission_is_treated_like_a_guest(string $prefix, string $permission): void
    {
        $path = "mma/{$prefix}/pieza-inactiva.webp";
        Storage::disk('local')->put($path, 'contenido-inactivo');
        $this->createMediaRecord($prefix, $path, status: 0);

        // El rol sales no tiene event_media.view ni fighter_media.view.
        $this->actingAsRole('sales');

        $this->get('/media/'.$path)->assertNotFound();
    }

    public function test_the_sessionless_controller_refuses_to_serve_gallery_paths_directly(): void
    {
        // Defensa en profundidad: la ruta /media/{galleryPath}, mas
        // especifica, gana siempre que exista, asi que esto no se puede
        // reproducir enviando una peticion HTTP normal (el enrutador la
        // resolveria igual por patron, no por el nombre de ruta usado para
        // generar la URL). Se invoca el controlador directo para probar que,
        // si de algun modo una peticion para una galeria llegara aqui, la
        // rechaza en vez de servirla sin chequear el estado.
        $path = 'mma/event-media/pieza-activa.webp';
        Storage::disk('local')->put($path, 'contenido-activo');
        $this->createMediaRecord('event-media', $path, status: 1);

        $this->expectException(NotFoundHttpException::class);

        app(PublicMediaController::class)->show($path, app(PublicMediaService::class));
    }

    private function assertPrivateNoStore(TestResponse $response): void
    {
        $cacheControl = (string) $response->headers->get('Cache-Control');

        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
    }

    private function createMediaRecord(string $prefix, string $filePath, int $status): EventMedia|FighterMedia
    {
        if ($prefix === 'event-media') {
            return EventMedia::query()->create([
                'event_id' => Event::factory()->create()->id,
                'file_path' => $filePath,
                'file_type' => 'image',
                'status' => $status,
            ]);
        }

        return FighterMedia::query()->create([
            'fighter_id' => Fighter::factory()->create()->id,
            'file_path' => $filePath,
            'file_type' => 'image',
            'status' => $status,
        ]);
    }
}
