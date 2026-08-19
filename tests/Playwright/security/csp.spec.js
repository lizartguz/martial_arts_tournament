import { test, expect } from '../fixtures';

/**
 * Verifica en un navegador real lo que un test de PHPUnit no puede ver: si
 * la Content-Security-Policy (app/Http/Middleware/SecurityHeaders.php)
 * bloquea algo que el sitio necesita. Un test de servidor puede confirmar
 * que la cabecera se envía; solo el navegador aplica la política de verdad.
 *
 * Con Laravel Debugbar activo (típico en local, ver config/debugbar.php) la
 * respuesta no lleva nonce a propósito: el bundle de Debugbar inyecta
 * contenido dinámico interno que setCspNonce() no cubre, y con un nonce
 * presente los navegadores dejan de honrar 'unsafe-inline' para nada no
 * nonceado, esos widgets incluidos. Por eso el chequeo de nonce es
 * condicional; el de cero violaciones en consola vale siempre.
 */

/** Junta violaciones de CSP vistas en consola durante el test. */
function collectCspViolations(page) {
    const violations = [];

    page.on('console', (msg) => {
        const text = msg.text();
        if (/content security policy|refused to/i.test(text)) {
            const loc = msg.location();
            violations.push(`${text} (${loc.url}:${loc.lineNumber})`);
        }
    });

    return violations;
}

/** Nonce de la cabecera CSP de esta respuesta, si trae uno. */
function nonceFromCsp(cspHeader) {
    return cspHeader?.match(/'nonce-([^']+)'/)?.[1];
}

/** Si hay nonce en la cabecera, todo <script nonce> impreso debe coincidir. */
async function assertNoncesAreConsistent(page, cspHeader) {
    const nonce = nonceFromCsp(cspHeader);
    if (!nonce) {
        return;
    }

    const scriptNonces = await page.locator('script[nonce]').evaluateAll(
        (scripts) => scripts.map((s) => s.nonce)
    );
    expect(scriptNonces.length).toBeGreaterThan(0);
    for (const printed of scriptNonces) {
        expect(printed).toBe(nonce);
    }
}

test.describe('Content-Security-Policy', () => {
    test('el login carga sin violaciones y el nonce coincide con el de la pagina', async ({ page }) => {
        const violations = collectCspViolations(page);

        const response = await page.goto('/login');
        await assertNoncesAreConsistent(page, response.headers()['content-security-policy']);

        expect(violations, `violaciones de CSP en /login:\n${violations.join('\n')}`).toEqual([]);
    });

    test('el panel admin carga sin violaciones y el toggle de tema oscuro funciona', async ({ adminPage: page }) => {
        const violations = collectCspViolations(page);

        // adminPage ya navegó y esperó /admin en el fixture; re-armamos el
        // listener de consola desde cero, así que recargamos para capturar
        // el ciclo de carga completo bajo escucha.
        const response = await page.reload();
        await assertNoncesAreConsistent(page, response.headers()['content-security-policy']);

        // El toggle usa onclick= nativo (script-src-attr), no @click de Alpine.
        await page.locator('.dark-mode-toggle').click();
        await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');

        expect(violations, `violaciones de CSP en el panel admin:\n${violations.join('\n')}`).toEqual([]);
    });
});
