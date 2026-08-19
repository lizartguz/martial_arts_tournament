import { defineConfig, devices } from '@playwright/test';

/**
 * Config de Playwright para tests/Playwright (nombre a la Laravel Dusk, aunque
 * el driver acá es Playwright). Corre contra un servidor real, no contra la
 * suite de PHPUnit: sirve para cosas que un test de servidor no puede ver,
 * como violaciones de CSP que solo el navegador aplica.
 */
export default defineConfig({
    testDir: './tests/Playwright',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 2 : undefined,
    reporter: 'list',

    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
    },

    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    ],

    // Reutiliza un `php artisan serve` si ya hay uno corriendo en el puerto;
    // si no, levanta uno para la corrida y lo cierra al terminar.
    webServer: {
        command: 'php artisan serve',
        url: 'http://127.0.0.1:8000',
        reuseExistingServer: true,
        timeout: 30_000,
    },
});
