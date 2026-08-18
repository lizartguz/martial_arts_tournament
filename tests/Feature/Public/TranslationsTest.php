<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Fase 7, punto 6: traducciones por idioma.
 *
 * `en/mma.php` es el array base; los otros 5 locales hacen
 * `$en = require .../en/mma.php` y sobreescriben claves puntuales sobre esa
 * misma variable (no hay merge recursivo automático de Laravel de por medio).
 * El modo de falla real no es "clave faltante" sino que un locale pierda una
 * sección completa al sobreescribir $en, o que el archivo tenga un error de
 * sintaxis silencioso.
 */
class TranslationsTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['en', 'es', 'pt', 'fr', 'de', 'ru'];

    public static function localesProvider(): array
    {
        return array_combine(self::LOCALES, array_map(fn (string $locale) => [$locale], self::LOCALES));
    }

    #[DataProvider('localesProvider')]
    public function test_every_locale_file_loads_as_a_non_empty_array_with_matching_top_level_keys(string $locale): void
    {
        $en = require lang_path('en/mma.php');
        $translations = require lang_path("{$locale}/mma.php");

        $this->assertNotEmpty($translations);
        $this->assertSame(array_keys($en), array_keys($translations));
    }

    #[DataProvider('localesProvider')]
    public function test_sample_keys_resolve_to_real_strings_in_every_locale(string $locale): void
    {
        app()->setLocale($locale);

        $keys = [
            'mma.landing.news.title',
            'mma.landing.fighters.title',
            'mma.landing.subscription.title',
            'mma.landing.nav.home',
            'mma.landing.event.next',
            'mma.landing.contact',
            'mma.roles.names.admin',
            'mma.authorization.protected',
            'mma.permissions.actions.view',
            'mma.admin.dashboard.page_title',
        ];

        foreach ($keys as $key) {
            $value = __($key);
            $this->assertNotSame($key, $value, "La clave [{$key}] no resolvió a texto real en el locale [{$locale}].");
            $this->assertNotEmpty($value, "La clave [{$key}] resolvió a un valor vacío en el locale [{$locale}].");
        }
    }

    public function test_locale_cookie_changes_rendered_landing_text(): void
    {
        $this->withCookie('locale', 'en')
            ->get('/noticias')
            ->assertOk()
            ->assertSee('News');

        $this->withCookie('locale', 'es')
            ->get('/noticias')
            ->assertOk()
            ->assertSee('Noticias');
    }

    public function test_locale_switch_route_sets_cookie_only_for_supported_locales(): void
    {
        $this->get('/idioma/fr')->assertCookie('locale', 'fr');

        $response = $this->get('/idioma/xx');
        $cookie = $response->headers->getCookies();
        $localeCookie = collect($cookie)->first(fn ($c) => $c->getName() === 'locale');

        $this->assertTrue(
            $localeCookie === null || $localeCookie->getValue() !== 'xx',
            'Un locale no soportado no debería quedar guardado en la cookie.'
        );
    }
}
