<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SEC-06 de la auditoría: el middleware global de cabeceras debe aplicarse a
 * toda respuesta web. Se prueba sobre /contacto porque GET / ordena con
 * TIMESTAMPDIFF, funcion de MySQL que no existe en la sqlite de esta suite.
 * El modo de falla real es que alguien desregistre el middleware de
 * `bootstrap/app.php` y nadie lo note hasta que aparezca un XSS o clickjacking.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_carry_the_global_security_headers(): void
    {
        $response = $this->get('/contacto');

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        // Bloquea cargar un <script src="..."> desde un dominio ajeno, sin
        // romper Alpine/Livewire (unsafe-eval), los ~11 <script> inline con
        // nonce, ni googletagmanager.com (Firebase Analytics lo inyecta en
        // runtime). script-src-attr aparte cubre los onclick=/onerror= que
        // quedan sin convertir, sin que el nonce de script-src los afecte.
        $this->assertMatchesRegularExpression(
            "/script-src 'self' 'unsafe-eval' 'unsafe-inline' 'nonce-[A-Za-z0-9+\/=]+' https:\/\/www\.googletagmanager\.com/",
            $csp
        );
        $this->assertStringContainsString("script-src-attr 'unsafe-inline'", $csp);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline' https://fonts.bunny.net", $csp);
        $this->assertStringContainsString(
            'camera=()',
            (string) $response->headers->get('Permissions-Policy')
        );
    }

    /**
     * El caso de falla real: si el nonce que viaja en la cabecera no coincide
     * exactamente con el que quedó impreso en cada <script nonce="...">, el
     * navegador bloquea todos los scripts inline legítimos de la página.
     */
    public function test_the_csp_nonce_matches_the_ones_printed_in_the_page(): void
    {
        $response = $this->get('/login');

        preg_match("/'nonce-([A-Za-z0-9+\/=]+)'/", (string) $response->headers->get('Content-Security-Policy'), $match);
        $this->assertNotEmpty($match, 'La cabecera CSP no trae nonce.');

        $html = $response->getContent();
        $scriptNonces = [];
        preg_match_all('/<script[^>]*\bnonce="([^"]+)"/', $html, $scriptNonces);

        $this->assertNotEmpty($scriptNonces[1], 'La vista no imprimió ningún <script nonce="...">.');

        foreach ($scriptNonces[1] as $printedNonce) {
            $this->assertSame($match[1], $printedNonce);
        }
    }

    public function test_hsts_is_only_sent_over_https(): void
    {
        $this->get('/contacto')->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/contacto')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
