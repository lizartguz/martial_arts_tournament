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

        $this->assertStringContainsString(
            "frame-ancestors 'self'",
            (string) $response->headers->get('Content-Security-Policy')
        );
        $this->assertStringContainsString(
            'camera=()',
            (string) $response->headers->get('Permissions-Policy')
        );
    }

    public function test_hsts_is_only_sent_over_https(): void
    {
        $this->get('/contacto')->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/contacto')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
