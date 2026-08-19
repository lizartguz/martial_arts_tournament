# Content-Security-Policy: qué se aplicó y qué hay que saber para trabajar en el proyecto

Documento actualizado: 19 de agosto de 2026.

Antecedente: `handoff/remediacion_seguridad_2026_08_18.md` (SEC-06, cabeceras
de seguridad globales).

## Qué es y por qué existe

`Content-Security-Policy` es una cabecera HTTP que le dice al navegador de qué
orígenes puede cargar y ejecutar scripts y estilos. Es la última línea de
defensa contra XSS: aunque algún día se cuele una inyección en un formulario,
si la política no permite ejecutar ese script inyectado, el navegador se
niega a correrlo.

Vive en `app/Http/Middleware/SecurityHeaders.php`, middleware global
registrado en `bootstrap/app.php`.

## La política actual, explicada

```
frame-ancestors 'self'; base-uri 'self'; object-src 'none';
script-src 'self' 'unsafe-eval' 'unsafe-inline' 'nonce-{aleatorio por request}' https://www.googletagmanager.com;
script-src-attr 'unsafe-inline';
style-src 'self' 'unsafe-inline' https://fonts.bunny.net
```

| Pieza | Qué hace | Por qué está |
| --- | --- | --- |
| `script-src 'self'` | Solo scripts del propio dominio, salvo lo listado explícito | Bloquea `<script src="https://dominio-ajeno.com/x.js">`, el vector clásico de robo de sesión/backdoor |
| `'unsafe-eval'` | Permite `eval()`/`Function()` en scripts ya permitidos | Alpine.js (empaquetado con Livewire) evalúa `x-data`, `@click`, etc. así por dentro |
| `'nonce-...'` | Token aleatorio, distinto en cada respuesta | Solo los `<script>` que lo llevan impreso se ejecutan; bloquea cualquier `<script>` inyectado que no lo conozca |
| `'unsafe-inline'` (en `script-src`) | Respaldo para navegadores viejos sin soporte de nonce | Inerte en navegadores modernos en cuanto detectan el nonce presente; no reabre nada real |
| `https://www.googletagmanager.com` | Dominio permitido explícito | El SDK de Firebase Analytics (`resources/js/analytics.js`) inyecta `gtag.js` desde ahí en tiempo de ejecución |
| `script-src-attr 'unsafe-inline'` | Directiva aparte para atributos (`onclick=`, `onerror=`...) | Cubre los 4 handlers nativos que quedan sin convertir a Alpine, sin que el nonce de arriba los afecte |
| `style-src 'self' 'unsafe-inline' https://fonts.bunny.net` | Estilos del propio dominio + inline + la fuente Figtree | No se endureció (ver "Lo que queda pendiente") |

## El nonce: cómo funciona

`App\Support\CspNonce` es un singleton memoizado por request (registrado en
`AppServiceProvider::register()`). Genera un valor aleatorio la primera vez
que alguien lo pide en ese request, y devuelve siempre el mismo después —
sin importar si lo pide el middleware (para la cabecera) o una vista (para
imprimirlo en un `<script>`).

**No se usa `View::share()`** porque `Livewire::test()` (en los tests) invoca
los componentes directo, sin pasar por el middleware — con `View::share()`,
esas vistas rendían con `$cspNonce` indefinido. El singleton resuelve esto:
cualquiera que lo pida, en cualquier orden, obtiene el mismo valor.

Uso en una vista:

```blade
<script nonce="{{ \App\Support\CspNonce::value() }}">
    // ...
</script>
```

`@vite(...)` y `@livewireScripts` reciben el nonce automáticamente vía
`Vite::useCspNonce($nonce)` y `@livewireScripts(['nonce' => ...])` en el
middleware — no hace falta pasarlo a mano en cada `@vite()`.

## Guía práctica: qué hacer y qué no, de acá en adelante

### No hace falta tocar nada para:

- Cualquier `.js` cargado vía `@vite(...)` o `asset(...)` (mismo origen).
- Directivas de Alpine (`x-data`, `@click`, `x-show`...): el navegador no las
  reconoce como scripts nativos, las lee Alpine por su cuenta.
- Imágenes, `fetch`/AJAX, iframes: sin restricción, no se tocó nada de eso.
- Estilos inline (`style="..."`, `<style>`): dejado abierto a propósito.

### Si agregás un `<script>` inline nuevo (sin `src=`):

Tiene que llevar `nonce="{{ \App\Support\CspNonce::value() }}"` o el
navegador lo bloquea. Preferible: si el código no es trivial, ponerlo en un
archivo `.js` real bajo `resources/js/` y cargarlo con `@vite()` — ahí no
hace falta nonce ni acordarse de nada.

### Si cargás un script o una hoja de estilos de un dominio externo nuevo:

Agregar ese dominio en `app/Http/Middleware/SecurityHeaders.php`: a
`script-src` si es JS, a `style-src` si es CSS. Sin eso, se bloquea en
silencio — así se encontraron `fonts.bunny.net` y `googletagmanager.com`, con
verificación real en navegador, no por grep.

### Si usás `onclick=`/`onerror=`/etc. nativos:

Siguen funcionando (`script-src-attr 'unsafe-inline'`). Para código nuevo,
mejor usar `@click` de Alpine, que ya es el patrón dominante del proyecto y
no depende de esa apertura.

### La trampa real: Debugbar enmascara el problema en local

Con Laravel Debugbar activo (normal en `APP_ENV=local`), la política se sirve
**sin nonce** a propósito — ver la sección siguiente. Eso significa que un
`<script>` inline sin nonce puede parecer que funciona perfecto en tu
máquina, y bloquearse en silencio en staging o producción, donde Debugbar
nunca corre y la política vuelve a ser estricta. No hay ningún error de
servidor que avise de esto: se ve solo en la consola del navegador.

**Antes de confiar en que algo funciona:** o revisás la consola del navegador
con Debugbar apagado (`DEBUGBAR_ENABLED=false` en `.env`), o corrés
`npm run test:browser` (ver más abajo), que revisa la consola por vos.

## Por qué Debugbar rompe con el nonce, y cómo se resolvió

Laravel Debugbar no solo emite los `<script>` que arma su propio renderer
(bootstrap, `jQuery.noConflict`, el visor de VarDumper, el panel): también
inyecta contenido dinámico interno por cada widget, que **no** pasa por
`setCspNonce()`. En cuanto hay un solo nonce presente en `script-src`, los
navegadores modernos dejan de honrar `'unsafe-inline'` para todo lo no
nonceado de esa respuesta — ese contenido interno de Debugbar incluido.

La solución, en `SecurityHeaders::handle()`: mientras Debugbar esté activo
para ese request (`app('debugbar')->isEnabled()`), la respuesta se sirve
**sin nonce**, con `'unsafe-inline'` funcionando de la forma clásica. Como
Debugbar nunca corre fuera de `local` (ver `config/debugbar.php`), esto no
afecta ni staging ni producción — ahí el nonce siempre está presente y es
estricto.

## Verificación: Playwright

`tests/Playwright/` (estructura a la Laravel Dusk, driver Playwright). Corre
contra un servidor real, algo que PHPUnit no puede hacer: un test de servidor
confirma que la cabecera se manda, pero solo el navegador aplica la política
de verdad.

```bash
npm run test:browser       # corre toda la suite headless
npm run test:browser:ui    # modo interactivo, para debug
```

`tests/Playwright/security/csp.spec.js` revisa, en `/login` y en el panel admin
autenticado:

- Que no aparezca ninguna violación de CSP en la consola.
- Que, si la respuesta trae nonce, coincida exactamente con el impreso en
  cada `<script nonce="...">` de la página (el chequeo es condicional porque
  con Debugbar activo no hay nonce, a propósito).

`tests/Playwright/fixtures.js` expone `adminPage`, una page ya logueada con el
usuario sembrado por `database/seeders/SystemSettingsSeeder.php`
(`sarteaga@root.dev`, rol `super_manager`) — reutilizable para tests futuros
que necesiten sesión.

## Lo que queda pendiente

- **`style-src` sigue abierto** (`'unsafe-inline'`, sin nonce). Hay ~54
  atributos `style=""` inline repartidos en el proyecto; endurecerlo es
  trabajo aparte, de menor prioridad porque una inyección de CSS es mucho
  menos grave que una de JS.
- **4 atributos `onclick=`/`onerror=` nativos** sin convertir a Alpine
  (`layouts/admin.blade.php` x2, `layouts/guest.blade.php`,
  `livewire/components/language-select.blade.php`). Quedan cubiertos por
  `script-src-attr 'unsafe-inline'`, no por nonce. No se convirtieron a
  ciegas por no poder probar cada uno en el navegador en ese momento; con
  `tests/Playwright/` ya armado, convertirlos y confirmarlos ahí es más seguro
  ahora que antes.
- Si en algún momento se agrega un widget o script de terceros nuevo
  (chat, pagos, mapas, otro pixel de analítica), el primer síntoma de que
  falta agregarlo a la política va a ser silencio total del lado servidor y
  algo que "no hace nada" en el navegador. Revisar la consola primero.
