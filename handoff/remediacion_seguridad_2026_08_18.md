# Remediacion de la auditoria de seguridad - Martial Arts Tournament

Fecha: 2026-08-18
Documento base: `handoff/auditoria_seguridad_2026_08_18.md`
Alcance: aplicar los hallazgos confirmados y separar "visibilidad publica" de
"archivo fisicamente publico" en las imagenes administradas.

## Estado por hallazgo

| ID | Hallazgo | Estado |
| --- | --- | --- |
| SEC-01 | PHP heredado ejecutable bajo `public/` | Corregido |
| SEC-02 | Debugbar y `APP_DEBUG` accesibles | Protegido + documentado (local intacto) |
| SEC-03 | Dependencias Composer vulnerables | Corregido (`composer audit` limpio) |
| SEC-04 | Secretos locales | Sin cambio de codigo; defensa extra en `.htaccess` |
| SEC-05 | Middleware legado `VerifyCsrfToken` + `Kernel.php` muerto | Corregido |
| SEC-06 | Sin headers de seguridad globales | Corregido |
| Extra | `tw-select` reinyectaba HTML con `x-html` | Corregido |
| Extra | Imagenes administradas servidas desde `public/storage` | Migrado a ruta controlada |

## SEC-01 - Superficie publica

Eliminado del webroot:

- `public/diagnostic.php`
- `public/frontend/reusableforms/**` (incluido el `vendor/` heredado con PHPMailer 5.2.25)
- `public/frontend/header.php`, `footer.php`, `menu.php` (restos de la app meteorologica;
  hacian `require_once` de rutas inexistentes, o sea fatal con ruta absoluta en pantalla)
- `public/frontend/error_log`
- `public/color-preview.html`, `public/test-table-theme.html`, `public/.DS_Store`

Hoy `find public -name "*.php"` devuelve unicamente `public/index.php`.

Ademas `public/.htaccess` deniega, como defensa en profundidad:

- cualquier `.php` / `.phtml` / `.phar` / `.inc` que no sea el front controller,
  incluidos los que aparezcan bajo `public/storage`;
- dotfiles y extensiones de configuracion, logs y backups (`.env`, `.log`,
  `.ini`, `.sql`, `.bak`, `.json`, `.lock`, `.yml`...);
- el listado de directorios, aunque `mod_negotiation` no este activo.

## SEC-02 - Debugbar y APP_DEBUG

El `.env` local no se toco: sigue en `APP_ENV=local` y `APP_DEBUG=true`.

Lo que si cambio, sin estorbar el desarrollo local:

- `config/debugbar.php` pasa de `env('DEBUGBAR_ENABLED', null)` a
  `env('DEBUGBAR_ENABLED', env('APP_ENV', 'production') === 'local' ? null : false)`.
  En local el comportamiento es identico (Debugbar sigue a `APP_DEBUG`); fuera de
  local queda apagado aunque alguien despliegue con `APP_DEBUG=true` por error.
- `.env.example` documenta los valores de staging/produccion
  (`APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`,
  `LOG_LEVEL=warning`) y el `php artisan optimize:clear` posterior.

## SEC-03 - Dependencias Composer

El lockfile ya venia actualizado de la sesion anterior (por ejemplo
`laravel/framework` v12.35.1 -> v12.66.0, `league/commonmark` 2.7.1 -> 2.10.0),
pero el `vendor/` habia quedado a medio instalar: faltaba
`vendor/google/apiclient-services/autoload.php` y `installed.json` seguia
reflejando las versiones viejas. Con ese estado la aplicacion no arrancaba ni
por CLI.

Tras reparar el `vendor/`, `composer audit` responde
**"No security vulnerability advisories found"**: los 58 advisories sobre 17
paquetes del informe original quedan en cero.

Nota de entorno: `ext-sockets` esta disponible en `C:\php83\ext` pero comentado
en `php.ini`, y `php-amqplib/php-amqplib` lo exige. Mientras siga deshabilitado,
composer necesita el flag:

```bash
composer install --ignore-platform-req=ext-sockets
```

La alternativa limpia es descomentar `extension=sockets` en `C:\php83\php.ini`
y dejar de usar el flag. Es un cambio de entorno, no del proyecto.

## SEC-05 - Middleware CSRF legado

`app/Http/Middleware/VerifyCsrfToken` ya no sobreescribe `handle()` devolviendo
`$next($request)`. Ahora extiende
`Illuminate\Foundation\Http\Middleware\VerifyCsrfToken` y solo declara `$except`,
asi que registrarlo por error ya no puede desactivar CSRF en silencio.

Ademas se elimino `app/Http/Kernel.php`, que era donde estaba la trampa de
verdad. Sobrevivia de Laravel 10 y no lo resolvia nadie: el contenedor devuelve
`Illuminate\Foundation\Http\Kernel`, verificado en runtime. Pero declaraba un
grupo `web` con `EncryptCookies` **comentado**, ademas del `VerifyCsrfToken`
permisivo. Quien lo hubiera vuelto a conectar habria perdido cifrado de cookies
y CSRF de una sola vez, con un archivo que a simple vista parecia vigente.

`App\Http\Middleware\VerifyCsrfToken` queda ahora sin referencias y sin riesgo.
Su `$except` apunta a `outofservice/webhook`, ruta que tampoco existe, asi que
se puede borrar tambien; se conservo porque el encargo fue corregirlo, no
eliminarlo. En Laravel 12 las exclusiones de CSRF se declaran con
`$middleware->validateCsrfTokens(except: [...])` en `bootstrap/app.php`.

## SEC-06 - Headers de seguridad globales

`app/Http/Middleware/SecurityHeaders`, registrado como middleware global en
`bootstrap/app.php`, aplica a toda respuesta:

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- `Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'; object-src 'none'`
- `Strict-Transport-Security` solo cuando la peticion es HTTPS real

Se aplica con `setIfMissing`, para no pisar respuestas que ya definen sus
propias cabeceras (adjuntos privados de notificaciones, imagenes servidas).

La CSP arranca deliberadamente acotada a `frame-ancestors` / `base-uri` /
`object-src`. Endurecerla con `script-src` / `style-src` exige antes revisar los
inline de Alpine, Livewire y las vistas heredadas; queda como trabajo siguiente,
idealmente empezando en `Content-Security-Policy-Report-Only`.

## Imagenes administradas: ruta publica controlada

Antes, cada imagen se guardaba en el disco `public` y la URL era
`asset('storage/mma/...')`: el HTML publicaba la estructura real del storage y
el archivo era alcanzable directo por `public/storage`.

Ahora:

- `config/uploads.php` -> `public_images.disk` pasa de `public` a `local`, o sea
  `storage/app/private/mma/...`, fuera del webroot.
- `App\Services\PublicMediaService` centraliza guardar, borrar, normalizar y
  resolver URL. `normalize()` es la frontera de confianza: decodifica, rechaza
  `..`, segmentos vacios, bytes nulos y rutas fuera de `mma/`, y solo acepta las
  extensiones de `uploads.public_images.image_extensions`.
- `App\Support\PublicMedia::url()` es el punto unico de uso en modelos y vistas.
  Devuelve la URL externa tal cual si ya es absoluta, la ruta controlada si el
  archivo es administrado, o `asset()` si es un estatico del repo.
- En base de datos se guarda la ruta relativa al disco (`mma/events/...`), sin
  prefijo alguno. La URL solo la arma `PublicMedia::url()`.
- `App\Http\Controllers\PublicMediaController` sirve el archivo con
  `Content-Security-Policy: default-src 'none'; sandbox`, `nosniff` y cache larga.
- La ruta vive en `routes/media.php` y se registra via `then:` en
  `bootstrap/app.php`, **fuera del grupo `web`**: sin sesion ni CSRF. Dentro de
  `web` cada imagen habria costado una lectura/escritura de sesion en base de
  datos, y una respuesta que lleva cookie de sesion no puede declararse
  `Cache-Control: public`.

Migrados a este canal: 10 componentes Livewire de admin, los modelos `Event`,
`EventMedia`, `Fight`, `Fighter`, `FighterMedia`, `FighterTeam`, `NewsPost`,
`Sponsor`, `Venue`, el `SystemSettingsService` y las vistas de landing y admin.

### Un solo esquema, sin compatibilidad

No hay ruta de compatibilidad con el esquema anterior, por decision explicita:
el proyecto todavia se puede regenerar en produccion y no vale la pena arrastrar
muletas. En concreto se quitaron:

- el segundo disco: `diskFor()` desaparecio y el controlador lee del disco que
  declara `uploads.public_images.disk`, nada mas;
- la tolerancia al prefijo `storage/` en `normalize()`, que ahora se rechaza
  igual que cualquier otra ruta invalida;
- la tolerancia al prefijo `media/`, que tampoco usaba nadie;
- el borrado en dos discos.

En `storage/app/public/mma/` habia **un unico archivo**, una prueba de subida
del dia anterior. Se elimino. Los seeders no generan rutas de storage: usan
estaticos del repo (`images/mma/...`), que `PublicMedia::url()` resuelve con
`asset()` sin pasar por la ruta de medios.

`public/storage` ya no sirve nada que produzca la aplicacion: `Features::profilePhotos()`
esta comentado en `config/jetstream.php`, asi que el disco `public` solo conserva
`imgstation/**`, restos de la app meteorologica heredada, ajenos a este trabajo.

### Default peligroso corregido

`ImageUploadOptimizer::store()` tenia `string $disk = 'public'`. Los tres
llamadores reales siempre pasan el disco explicito, asi que el default estaba
muerto, pero era una trampa: cualquier llamada futura que lo omitiera escribia
en el webroot en silencio. Ahora el default es `local`, o sea privado salvo que
se pida lo contrario.

## Limpieza de restos del proyecto heredado

Todo lo de aqui se verifico sin referencias antes de borrarlo.

Del webroot:

- `public/storage`: symlink eliminado, y `filesystems.links` quedo vacio para que
  un `php artisan storage:link` de rutina no vuelva a exponer
  `storage/app/public` por HTTP sin que nadie lo note. Ninguna imagen del
  proyecto se sirve ya desde el webroot.
- `public/vendor/**`: 14 librerias huerfanas (Chart.js, bootstrap,
  bootstrap-select, chartjs-adapter-luxon, chartjs-plugin-zoom, hammer.js,
  handsontable, jspdf, luxon, overlayScrollbars, popper, select2,
  select2-bootstrap4-theme, sweetalert2). De 13 MB a 4 MB. Solo quedan
  `fontawesome-free` y `jquery`, que si se cargan desde blades. sweetalert2 y
  luxon ya venian por Vite desde `resources/js/app.js`, o sea eran copias
  redundantes.
- `public/imgpf/**` (3 imagenes) y `public/frontend/css/` (solo lo usaba
  `infowindow2.php`).
- `public/frontend/images/senvatec-*.png` (6 imagenes de marketing agro/clima).
  Se conservo `logo_with_text.png`, que si se usa como icono de push.

Del storage y del repo:

- `storage/app/public/imgstation/**` (10 imagenes de estaciones meteorologicas).
  `storage/app/public` quedo con su `.gitignore` y nada mas.
- `resources/views/infowindow2.php`, vista de la app meteorologica sin ninguna
  referencia.
- Cinco documentos de raiz del componente ForecastViewer
  (`DONDE_ESTA_EL_COMPONENTE.md`, `IMPLEMENTACION_ForecastViewer.md`,
  `INDICE_DOCUMENTACION.md`, `README_FORECAST_COMPONENT.md`,
  `RESUMEN_CAMBIOS_REALIZADOS.md`) y el archivo `@php` de 0 bytes.

### Restos que quedan y necesitan decision

No se tocaron porque no son basura huerfana: o estan vivos, o son de otro modulo.

- **Marca heredada en vistas vivas**: el logo `logo_artguz_clima.png` aparece en
  el componente `<x-welcome/>` del dashboard y en la cabecera de todos los
  correos; hay enlaces a `artguz.com` en el pie del panel admin y en
  `notificationspush`; y el icono de las notificaciones push sigue siendo
  `logo_with_text.png`, de SenvaTec. Borrar esos archivos rompe paginas: es un
  cambio de marca, no una limpieza.
- **`MigrateNotificationImagesToPrivate`** y el `migratePublicFile()` que usa:
  comando de migracion puntual del modulo de notificaciones. Ya no tiene nada
  que migrar, porque `storage/app/public` esta vacio.
- **`scripts/copy-vendor-assets.mjs`**: su `vendorAssets` es un array vacio, o
  sea corre en cada `npm run dev`/`build` sin copiar nada.
- **`.codex-translation-cache.json`**: 175 KB de cache de herramienta,
  trackeado en git y sin ningun script que lo lea.

## Endurecimiento de `tw-select`

`resources/views/components/tw-select.blade.php` reinyectaba `option.textContent`
con `x-html`, o sea texto ya escapado por Blade que volvia a interpretarse como
HTML. Ahora usa `x-text` en la lista y escapa el valor en el disparador.

## Pruebas de regresion agregadas

`tests/Feature/Security/`:

- `SecurityHeadersTest` - cabeceras globales presentes; HSTS solo bajo HTTPS.
  Usa `/contacto` y no `/`, porque la home ordena con `TIMESTAMPDIFF`, funcion
  de MySQL que no existe en la sqlite de la suite.
- `LegacyPublicSurfaceTest` - solo `public/index.php` es ejecutable; el
  `.htaccess` mantiene el bloqueo; el `VerifyCsrfToken` legado no declara
  `handle()`; `POST /contacto` sin token responde 419.
- `PublicMediaRouteTest` - sirve desde el disco privado, mantiene el legado del
  disco publico, la URL no filtra `storage/`, y rechaza traversal (plano y
  percent-encoded), rutas fuera de `mma/`, extensiones no permitidas, rutas sin
  extension y segmentos vacios.

Detalle que vale documentar: Laravel se salta `VerifyCsrfToken` cuando la app
corre bajo `testing`, asi que un `assertStatus(419)` desde la suite pasa sin
probar nada. Por eso el test invoca el middleware directo con el entorno
cambiado, en vez de hacer un POST.

### Test existente corregido

`tests/Feature/Admin/SystemSettingsFormTest` afirmaba que el logo quedaba en el
disco `public` con prefijo `storage/`. Ya no es cierto. De paso tenia un fallo
propio: solo falseaba el disco `public`, asi que cada corrida escribia archivos
**reales** en `storage/app/private/mma/system/`. Ahora falsea tambien `local`.

## Verificacion

```bash
composer install --ignore-platform-req=ext-sockets
composer audit --format=summary
php artisan optimize:clear
php artisan test --filter=Security
php artisan test
```

Resultado en esta maquina:

- `composer audit` -> sin advisories.
- `php artisan test tests/Feature/Security` -> 18 passed.
- `php artisan test` -> 106 passed, 8 skipped, 13 failed.

Los 13 fallos son previos e independientes de este trabajo; se confirmo
neutralizando el middleware de cabeceras y la ruta de medios y volviendo a
correrlos: fallan igual. Son de tres tipos:

- `ExampleTest` pide `GET /`, que ordena con `TIMESTAMPDIFF` (MySQL) y revienta
  en la sqlite de la suite. Mismo motivo ya documentado en
  `QueryCountRegressionTest`.
- `AuthenticationTest`, `PasswordResetTest`, `RegistrationTest`: pruebas de
  scaffolding de Jetstream que no se actualizaron al login propio del proyecto.
- `NotificationPrivateImagesTest`: quedo desfasado del esquema y las rutas
  actuales (`users.deleted_at` no existe en su `createMinimalSchema()`, y pide
  una ruta `notifications.preview` que ya no esta).

Checks HTTP esperados:

```text
GET  /diagnostic.php                                -> 404
GET  /frontend/reusableforms/handler.php            -> 404
GET  /frontend/header.php                           -> 404
GET  /media/mma/events/<archivo>.webp               -> 200
GET  /media/mma/../../.env                          -> 404
GET  /media/mma/events/shell.php                    -> 404
POST /contacto sin token CSRF                       -> 419
```
