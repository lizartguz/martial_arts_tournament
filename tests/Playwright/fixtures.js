import { test as base, expect } from '@playwright/test';

/**
 * Usuario sembrado por database/seeders/SystemSettingsSeeder.php
 * (seedRootUser), rol super_manager. No es un secreto a proteger: la
 * contraseña ya vive en texto plano en ese seeder, versionado en git.
 * Requiere haber corrido las migraciones + seeders contra la base real
 * (no aplica a la suite de PHPUnit, que usa sqlite en memoria aparte).
 */
export const ADMIN_EMAIL = 'sarteaga@root.dev';
export const ADMIN_PASSWORD = 'sarteaga@root.dev';

/**
 * Extiende el test base de Playwright con una `page` ya logueada como admin,
 * para no repetir el flujo de login en cada archivo de test.
 *
 * Uso: import { test, expect } from '../fixtures'; luego pedir `adminPage`
 * en vez de `page` en la firma del test.
 */
export const test = base.extend({
    adminPage: async ({ page }, use) => {
        await page.goto('/login');
        await page.locator('#email').fill(ADMIN_EMAIL);
        await page.locator('#password').fill(ADMIN_PASSWORD);
        await page.locator('.login-submit').click();
        await page.waitForURL(/\/admin/);

        await use(page);
    },
});

export { expect };
