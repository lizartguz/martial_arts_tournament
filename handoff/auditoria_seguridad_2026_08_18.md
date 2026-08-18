# Auditoria de seguridad - Martial Arts Tournament

Fecha: 2026-08-18  
Proyecto: `C:\xampp\htdocs\martial_arts_tournament`  
Entorno revisado: Laravel 12.35.1, PHP 8.3.31, Composer 2.9.5, `APP_ENV=local`, `APP_DEBUG=true`

## Resumen ejecutivo

La revision encontro fallos reales de exposicion y superficie publica, especialmente archivos PHP heredados ejecutables dentro de `public/`, Debugbar accesible y dependencias PHP vulnerables. No se confirmaron inyecciones SQL en las consultas raw revisadas, y las rutas Laravel probadas mantienen CSRF activo.

Prioridad inmediata:

1. Eliminar o bloquear los PHP heredados dentro de `public/`, especialmente `public/diagnostic.php` y `public/frontend/reusableforms`.
2. Desactivar `APP_DEBUG` y Debugbar fuera de uso estrictamente local.
3. Actualizar dependencias Composer vulnerables.
4. Rotar y proteger secretos locales si este entorno fue compartido, respaldado o copiado.
5. Agregar headers de seguridad globales y corregir middleware legado que podria desactivar CSRF si se registra por error.

## Alcance y metodologia

Se revisaron rutas, controladores, componentes Blade/Livewire, servicios de archivos, configuracion, dependencias y archivos publicos. Las pruebas se hicieron en local con `php artisan serve` temporal y peticiones HTTP contra endpoints concretos.

Comandos usados como evidencia:

```bash
php artisan about --only=environment,cache,drivers
php artisan route:list -v
php artisan route:list --path=_debugbar -v
php artisan route:list --path=contacto -v
php artisan route:list --path=livewire/upload-file -v
composer audit --format=summary
npm.cmd audit --audit-level=moderate
rg -n -- "selectRaw|whereRaw|orderByRaw|DB::raw|DB::statement|DB::select" app
rg -n -- "{!!|x-html|innerHTML|outerHTML|document.write|eval\\(|new Function" resources app
rg -n -- "Content-Security-Policy|X-Frame-Options|Strict-Transport-Security|Referrer-Policy|Permissions-Policy|X-Content-Type-Options" app config bootstrap routes resources
```

Pruebas HTTP verificadas:

```text
GET  /diagnostic.php                                                    -> 200
GET  /frontend/reusableforms/handler.php                                -> 200, muestra warnings con rutas locales
GET  /frontend/reusableforms/vendor/phpmailer/phpmailer/get_oauth_token.php -> 200, muestra warning/fatal con rutas locales
GET  /_debugbar/assets/javascript                                       -> 200
GET  /_debugbar/open                                                    -> 200, devuelve metadata de solicitudes recientes
POST /contacto sin token CSRF                                           -> 419
```

## Hallazgos confirmados

### SEC-01 - Critico - PHP heredado ejecutable bajo `public/`

Archivos afectados:

- `public/diagnostic.php`
- `public/frontend/reusableforms/handler.php`
- `public/frontend/reusableforms/vendor/phpmailer/phpmailer/get_oauth_token.php`
- Arbol completo `public/frontend/reusableforms/vendor/**`

Evidencia:

- `GET /diagnostic.php` responde `200` y expone informacion de diagnostico del proyecto, rutas publicas, existencia de archivos, tamanos y fechas de modificacion.
- `GET /frontend/reusableforms/handler.php` responde `200` y muestra warnings/deprecations con rutas absolutas locales.
- `GET /frontend/reusableforms/vendor/phpmailer/phpmailer/get_oauth_token.php` responde `200` y muestra warning/fatal con rutas absolutas y stack trace.
- `public/frontend/reusableforms/vendor/phpmailer/phpmailer/VERSION` indica PHPMailer `5.2.25`.
- `handler.php` ejecuta fuera del ciclo Laravel, no pasa por middleware web, autenticacion, CSRF, throttle ni logging uniforme.
- `handler.php` habilita errores en pantalla con `ini_set('display_errors', 1); error_reporting(E_ALL);`.

Impacto:

- Divulgacion de rutas absolutas y detalles internos.
- Bypass de protecciones Laravel para formularios publicos.
- Superficie de spam o abuso del formulario heredado.
- Riesgo aumentado por vendor antiguo ejecutable en webroot.

Recomendacion:

- Eliminar `public/diagnostic.php`.
- Retirar `public/frontend/reusableforms` del webroot. Si aun se necesita el formulario, migrarlo a `LandingController@submitContact` o a una ruta Laravel con CSRF, throttle y validacion.
- Eliminar vendors/demo scripts bajo `public/`.
- Bloquear por servidor web cualquier `*.php` dentro de subdirectorios estaticos como `public/frontend`.
- Desactivar `display_errors` en runtime web.

### SEC-02 - Critico en produccion - Debugbar y modo debug accesibles

Archivos/configuracion relacionados:

- `.env`: `APP_DEBUG=true`
- `config/debugbar.php`
- Rutas `_debugbar/*`

Evidencia:

- `php artisan about` reporta `Debug Mode ENABLED`.
- `php artisan route:list --path=_debugbar -v` lista 6 rutas Debugbar.
- `GET /_debugbar/assets/javascript` responde `200`.
- `GET /_debugbar/open` responde `200` y devuelve metadata de solicitudes recientes.

Impacto:

- En un entorno expuesto, Debugbar puede filtrar rutas, cabeceras, consultas, excepciones, datos de request y trazas internas.
- Puede revelar tokens o datos personales si pasan por requests o excepciones.

Recomendacion:

- Usar `APP_DEBUG=false` y `DEBUGBAR_ENABLED=false` en cualquier entorno compartido, staging o produccion.
- Evitar instalar/activar Debugbar en produccion.
- Limpiar caches despues del cambio:

```bash
php artisan optimize:clear
```

### SEC-03 - Alto/Critico - Dependencias Composer vulnerables

Evidencia:

- `composer audit --format=summary` devuelve: `Found 58 security vulnerability advisories affecting 17 packages.`
- Paquetes relevantes en el lockfile:
  - `laravel/framework` 12.35.1: advisories de CRLF injection en regla `email` y confusion en temporary signed URL.
  - `phpoffice/phpspreadsheet` 1.30.1: advisories criticos y altos, incluyendo SSRF/RCE/DoS/XSS segun `composer audit`.
  - `guzzlehttp/guzzle` 7.10.0 y `guzzlehttp/psr7` 2.8.0: advisories de host/cookie/proxy/CRLF.
  - `dompdf/dompdf` 3.1.3: advisories de lectura local/DoS via SVG/BMP/font-face.
  - `symfony/http-foundation`, `symfony/mime`, `symfony/mailer`, `symfony/routing`, `symfony/process`: advisories activos.

Impacto:

- La explotabilidad exacta depende del uso de cada paquete, pero la evidencia del lockfile es real y suficiente para tratarlo como deuda critica de supply chain.
- `phpoffice/phpspreadsheet` es especialmente sensible si existe importacion o procesamiento de hojas de calculo cargadas por usuarios.
- `dompdf` es sensible si genera PDF con HTML o imagenes controladas por usuarios.

Recomendacion:

- Ejecutar una actualizacion controlada de Composer en rama separada:

```bash
composer update laravel/framework guzzlehttp/guzzle guzzlehttp/psr7 phpoffice/phpspreadsheet dompdf/dompdf symfony/* --with-all-dependencies
composer audit
php artisan test
```

- Si `maatwebsite/excel` bloquea `phpoffice/phpspreadsheet`, evaluar actualizar `maatwebsite/excel` o aislar/deshabilitar importaciones de archivos de usuario hasta resolver.
- Agregar `composer audit` al pipeline antes de despliegues.

### SEC-04 - Alto operacional - Secretos locales y credenciales sensibles

Archivos revisados:

- `.env`
- `storage/app/firebase/*.json`

Evidencia:

- `.env` no esta trackeado por Git, pero contiene credenciales sensibles reales o similares a reales: `MAIL_PASSWORD`, `TWILIO_AUTH_TOKEN`, `OPENAI_API_KEY`, `GOOGLE_API_KEY`, claves Firebase y configuracion de servicios externos.
- `storage/app/firebase` contiene archivos JSON de Firebase Admin SDK. No estan trackeados por Git.

Impacto:

- Si este entorno local se comparte, respalda, sube a hosting o se copia completo, esas credenciales permiten acceso a servicios externos.
- Las claves Firebase publicas tipo `VITE_FIREBASE_*` pueden ser publicas por diseno, pero el JSON Admin SDK, Twilio, OpenAI, mail y tokens privados no deben salir del entorno seguro.

Recomendacion:

- Mantener `.env` y `storage/app/firebase/*.json` fuera de Git.
- Rotar credenciales si el proyecto fue compartido, enviado por zip o respaldado en ubicaciones no controladas.
- Usar secretos por entorno y no copiar credenciales productivas a entornos locales.
- Validar que el servidor web nunca exponga `storage/app` completo; solo debe exponerse `storage/app/public` mediante `public/storage`.

### SEC-05 - Medio - Middleware legado podria desactivar CSRF si se registra

Archivos afectados:

- `app/Http/Kernel.php`
- `app/Http/Middleware/VerifyCsrfToken.php`

Evidencia:

- `VerifyCsrfToken::handle()` retorna directamente `$next($request)`, lo que desactiva CSRF si esa clase se registra como middleware activo.
- En el runtime actual de Laravel 12, `POST /contacto` sin token devuelve `419`, por lo que CSRF si esta activo en las rutas Laravel actuales.

Impacto:

- No es un exploit confirmado hoy.
- Es una trampa de mantenimiento: si alguien registra ese middleware legado o vuelve a usar `Kernel.php`, se puede desactivar CSRF de forma silenciosa.

Recomendacion:

- Eliminar el middleware legado si ya no aplica a Laravel 12.
- O corregirlo para extender/usar el middleware oficial de Laravel sin sobreescribir `handle()` de forma permisiva.
- Agregar una prueba de regresion que confirme que rutas `POST` web sin token devuelven `419`.

### SEC-06 - Medio - No hay headers de seguridad globales

Evidencia:

- Busqueda de headers de seguridad solo encontro `X-Content-Type-Options: nosniff` en `NotificationAttachmentService`.
- No se encontro middleware global para `Content-Security-Policy`, `X-Frame-Options`/`frame-ancestors`, `Strict-Transport-Security`, `Referrer-Policy` o `Permissions-Policy`.

Impacto:

- Aumenta el impacto de XSS si aparece una inyeccion.
- Permite clickjacking si las vistas se pueden embeber en iframes.
- Falta endurecimiento basico para navegadores.

Recomendacion:

- Agregar middleware global de headers, ajustado por entorno:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN` o CSP `frame-ancestors 'self'`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy` restrictivo
  - `Strict-Transport-Security` solo cuando se sirva por HTTPS real
  - CSP gradual, empezando en `report-only` si hay scripts inline o librerias heredadas.

## Riesgos revisados sin bug explotable confirmado

### CSRF en rutas Laravel

`POST /contacto` sin token devuelve `419`, asi que el stack Laravel actual mantiene CSRF activo. La excepcion es el PHP heredado en `public/frontend/reusableforms/handler.php`, que no pasa por Laravel y por eso queda cubierto en SEC-01.

### SQL injection en consultas raw

Se revisaron usos de `selectRaw`, `whereRaw`, `orderByRaw`, `DB::raw`, `DB::statement` y `DB::select`. Los usos encontrados son expresiones constantes, por ejemplo `count(*)`, `CASE WHEN ...` o `ABS(TIMESTAMPDIFF(...))`. No se encontro concatenacion directa de input del usuario en SQL raw durante esta revision.

### Traversal en ruta `storage/{path}`

La ruta `storage/{path}` existe por el disk local con `serve => true`, pero `Illuminate\Filesystem\ServeFile` valida firma para discos privados y captura `PathTraversalDetected`. No se marca como fallo confirmado.

### Ruta `/admin/reports`

Aunque la ruta aparece sin middleware `can:*` explicito en `route:list`, `Admin\ReportController` valida `canAny(['reports.events.view','reports.subscriptions.view','reports.sales.view'])` antes de renderizar. No se marca como bypass confirmado.

### XSS en Blade/Livewire

Se revisaron salidas `{!! !!}`, `x-html` e inserciones `innerHTML`. Los casos confirmados como seguros usan `e()` antes de `nl2br` o datos de configuracion/aplicacion confiables.  
Observacion de endurecimiento: `resources/views/components/tw-select.blade.php` reinyecta `option.textContent` con `x-html`. No se encontro uso activo del componente en vistas actuales, pero si se reutiliza con nombres de base de datos, debe escapar `opt.html` o usar `x-text` salvo cuando exista HTML controlado por el sistema.

### Auditoria NPM

`npm.cmd audit --audit-level=moderate` devuelve `found 0 vulnerabilities` despues del `npm audit fix` ya aplicado. No quedan vulnerabilidades NPM reportadas por audit en este lockfile.

## Recomendaciones priorizadas

### Inmediato

1. Eliminar `public/diagnostic.php`.
2. Eliminar o mover fuera de `public/` todo `public/frontend/reusableforms`.
3. Desactivar Debugbar y `APP_DEBUG` en cualquier entorno que no sea local privado.
4. Planificar update de Composer hasta que `composer audit` quede limpio o con excepciones justificadas.

### Corto plazo

1. Agregar middleware global de headers.
2. Corregir o eliminar middleware legado `VerifyCsrfToken`.
3. Agregar pruebas de regresion para CSRF, acceso a adjuntos privados y rutas publicas prohibidas.
4. Revisar usos de Excel/PDF y bloquear archivos controlados por usuario hasta actualizar `phpoffice/phpspreadsheet` y `dompdf`.

### Medio plazo

1. Agregar auditoria automatica en CI: `composer audit`, `npm audit --audit-level=moderate`, `php artisan test`.
2. Documentar manejo de secretos por entorno.
3. Revisar politicas de autorizacion de reportes, pagos y adjuntos con pruebas por rol.
4. Endurecer componentes reutilizables que usen `x-html`.

## Comandos de validacion recomendados despues de corregir

```bash
composer audit --format=summary
npm.cmd audit --audit-level=moderate
php artisan optimize:clear
php artisan route:list --path=_debugbar -v
php artisan test
```

Checks HTTP esperados despues de remediar:

```text
GET /diagnostic.php                                                     -> 404
GET /frontend/reusableforms/handler.php                                 -> 404
GET /frontend/reusableforms/vendor/phpmailer/phpmailer/get_oauth_token.php -> 404
GET /_debugbar/open                                                     -> 404 o 403 fuera de local autorizado
POST /contacto sin token CSRF                                           -> 419
```
