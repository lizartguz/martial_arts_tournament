# tests/Playwright

Pruebas de navegador real (Playwright), separadas de la suite de PHPUnit
(`tests/Feature`, `tests/Unit`). Sirven para lo que un test de servidor no
puede ver: cosas que solo el navegador aplica, como violaciones de
Content-Security-Policy, o que un botón realmente haga algo al hacer clic.

Contexto completo del primer caso de uso (CSP):
`docs/politica_seguridad_csp.md`.

## Requisitos antes de correrlas

1. **Dependencias instaladas**, con el navegador headless ya descargado:

   ```bash
   npm install
   npx playwright install chromium
   ```

2. **Base de datos real migrada y sembrada** (no la sqlite en memoria de
   PHPUnit). Los tests que usan `adminPage` (ver `fixtures.js`) inician
   sesión con el usuario que crea `database/seeders/SystemSettingsSeeder.php`:

   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Un servidor sirviendo la app** en `http://127.0.0.1:8000`. No hace falta
   levantarlo a mano: `playwright.config.js` reutiliza uno si ya está
   corriendo (por ejemplo, XAMPP/Apache en ese puerto), y si no, levanta
   `php artisan serve` solo para la corrida y lo cierra al terminar.

## Cómo correrlas

```bash
npm run test:browser
```

Corre toda la suite en modo headless (sin ventana visible) y tira un resumen
por consola.

Para debug interactivo, con la interfaz de Playwright (ver cada paso, pausar,
inspeccionar el DOM):

```bash
npm run test:browser:ui
```

Para correr un solo archivo o filtrar por nombre:

```bash
npx playwright test tests/Playwright/security/csp.spec.js
npx playwright test --grep "panel admin"
```

## Si un test falla

- El reporte queda en `test-results/` **en la raíz del proyecto** (no dentro
  de esta carpeta) — es el `outputDir` por defecto de Playwright, relativo a
  `playwright.config.js`, que vive en la raíz. Está en `.gitignore`. Ahí
  queda la captura de pantalla del momento del fallo y, si hubo reintento,
  una traza que se puede abrir con
  `npx playwright show-trace test-results/.../trace.zip`.
- Si el fallo es justo por una violación de CSP, el mensaje de consola
  capturado trae la URL y línea exacta de origen (ver
  `collectCspViolations()` en `security/csp.spec.js` como ejemplo de cómo se
  arma ese detalle) — mucho más preciso que buscar a mano en el navegador.

## Estructura

```
tests/Playwright/
  fixtures.js             <- helpers reutilizables (login como admin, etc.)
  security/
    csp.spec.js            <- primer test: violaciones de CSP en login y panel admin
```

## Agregar un test nuevo

Importar `test`/`expect` desde `../fixtures` (no directo de
`@playwright/test`) para tener acceso a `adminPage` cuando el test necesite
sesión iniciada:

```js
import { test, expect } from '../fixtures';

test('algo que requiere estar logueado', async ({ adminPage: page }) => {
    await page.goto('/admin/events');
    // ...
});
```

Para un test que no necesita sesión, `{ page }` normal alcanza, igual que en
cualquier test de Playwright.

Si el nuevo test necesita otro usuario/rol, agregar un fixture nuevo en
`fixtures.js` siguiendo el mismo patrón que `adminPage`, en vez de repetir el
flujo de login en cada archivo.
