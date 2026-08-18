<?php

namespace Tests\Feature\Security;

use App\Http\Middleware\VerifyCsrfToken;
use FilesystemIterator;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as FrameworkVerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionMethod;
use Tests\TestCase;

/**
 * SEC-01 y SEC-05 de la auditoría.
 *
 * SEC-01: había PHP heredado ejecutable dentro de `public/` que no pasaba por
 * el ciclo de Laravel (sin CSRF, sin throttle, con `display_errors` activo).
 * SEC-05: el `VerifyCsrfToken` legado sobreescribía `handle()` devolviendo
 * `$next($request)`, así que desactivaba CSRF en silencio si alguien lo
 * registraba. Ninguno de los dos es explotable hoy; ambos vuelven a serlo con
 * un solo `git revert` distraído, por eso se fijan aquí.
 */
class LegacyPublicSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_legacy_php_is_executable_from_the_webroot(): void
    {
        $found = [];
        $root = public_path();

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($root) + 1));

            if (strtolower($file->getExtension()) === 'php' && $relative !== 'index.php') {
                $found[] = $relative;
            }
        }

        $this->assertSame(
            [],
            $found,
            'Solo public/index.php puede ser ejecutable. Sobran: '.implode(', ', $found)
        );
    }

    public function test_the_webroot_has_no_link_into_storage(): void
    {
        // El symlink `public/storage` no lo necesita nada: las imagenes
        // administradas salen por `media.public.show` desde el disco privado.
        // Se vacio `filesystems.links` para que un `storage:link` de rutina no
        // vuelva a exponer `storage/app/public` por HTTP sin que nadie lo note.
        $this->assertFileDoesNotExist(public_path('storage'));
        $this->assertSame([], config('filesystems.links'));
    }

    public function test_the_htaccess_blocks_php_outside_the_front_controller(): void
    {
        $htaccess = file_get_contents(public_path('.htaccess'));

        $this->assertMatchesRegularExpression('/<FilesMatch[^>]*php/i', $htaccess);
        $this->assertStringContainsString('Require all denied', $htaccess);
        $this->assertStringContainsString('Options -Indexes', $htaccess);
    }

    public function test_the_legacy_csrf_middleware_does_not_override_handle(): void
    {
        $this->assertTrue(
            is_subclass_of(VerifyCsrfToken::class, FrameworkVerifyCsrfToken::class),
            'El middleware legado debe extender el oficial de Laravel.'
        );

        $this->assertNotSame(
            VerifyCsrfToken::class,
            (new ReflectionMethod(VerifyCsrfToken::class, 'handle'))->getDeclaringClass()->getName(),
            'No debe declarar handle(): así fue como quedó CSRF desactivado en silencio.'
        );
    }

    public function test_the_dead_laravel_10_kernel_is_gone(): void
    {
        // `app/Http/Kernel.php` no lo resolvia nadie (el contenedor devuelve
        // Illuminate\Foundation\Http\Kernel), pero declaraba un grupo `web` con
        // EncryptCookies comentado. Ahi estaba la trampa: no en el middleware
        // legado en si, sino en el archivo muerto que lo hacia parecer vigente.
        $this->assertFileDoesNotExist(app_path('Http/Kernel.php'));

        $this->assertInstanceOf(
            \Illuminate\Foundation\Http\Kernel::class,
            $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
        );
    }

    public function test_the_legacy_csrf_middleware_rejects_a_post_without_token(): void
    {
        // El middleware se salta a si mismo cuando la app corre bajo `testing`,
        // asi que una peticion HTTP normal de la suite nunca devolveria 419 y no
        // probaria nada. Se lo invoca directo con el entorno cambiado.
        $this->app->instance('env', 'local');

        $middleware = $this->app->make(VerifyCsrfToken::class);

        $request = Request::create('/contacto', 'POST');
        $request->setLaravelSession($this->app['session']->driver());

        try {
            $middleware->handle($request, fn () => new Response);
            $this->fail('El middleware legado dejó pasar un POST sin token CSRF.');
        } catch (TokenMismatchException) {
            $this->addToAssertionCount(1);
        } finally {
            $this->app->instance('env', 'testing');
        }
    }
}
