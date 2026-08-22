# Auditoria de seguridad reforzada - Martial Arts Tournament

Fecha: 2026-08-22  
Proyecto: `C:\xampp\htdocs\martial_arts_tournament`  
Entorno auditado: Laravel 12.66.0, PHP 8.3.31, MySQL, `APP_ENV=local`, `APP_DEBUG=true`  
Objetivo: reforzar la auditoria anterior y buscar hallazgos adicionales validando falsos positivos con pruebas locales.

## Resumen ejecutivo

No se confirmaron bypasses directos de path traversal, XSS almacenado/reflejado, inyeccion SQL, CSRF ni exposicion directa de archivos privados por rutas publicas. Las pruebas automatizadas de seguridad, autorizacion, CSP en navegador, dependencias Composer y dependencias NPM pasaron correctamente.

El hallazgo mas importante estaba en el componente Livewire de notificaciones: una cuenta con permiso solo de visualizacion (`notifications.view`) podia ejecutar acciones de creacion, edicion, eliminacion, activacion y reenvio porque los metodos publicos no revalidaban permisos granulares. El hallazgo quedo corregido con validaciones `Gate::authorize()` por accion, permisos nuevos en el seeder y pruebas regresivas. Debugbar queda reclasificado como recomendacion de configuracion local, y los puntos de token Web Push y ruta firmada del disco local quedaron corregidos en la misma fecha de esta auditoria.

## Metodologia y pruebas ejecutadas

- Inventario de rutas con `php artisan route:list -v`.
- Revision de middleware, controladores, componentes Livewire, servicios y seeders de permisos.
- Pruebas de dependencias con `composer audit --format=summary` y `npm audit --audit-level=moderate`.
- Pruebas automatizadas con `php artisan test tests/Feature/Security --stop-on-failure`.
- Pruebas automatizadas con `php artisan test tests/Feature/Authorization --stop-on-failure`.
- Pruebas de navegador/CSP con `npm run test:browser`.
- Pruebas HTTP manuales contra servidor local en `127.0.0.1:8010`.
- Prueba dinamica Livewire con usuario temporal que tenia unicamente `notifications.view`.

## Hallazgos confirmados

### SEC-RF-01 - Corregido - Escalamiento funcional en modulo de notificaciones Livewire

**Archivos afectados**

- `app/Livewire/NotificationsPush/NotificationsPush.php`
- `database/seeders/RolesAndPermissionsSeeder.php`
- `routes/web.php`

**Evidencia**

La ruta `GET admin/notifications` esta protegida con `can:notifications.view`, y el componente `NotificationsPush` tambien valida en `mount()` que el usuario pueda ver notificaciones. Sin embargo, los metodos publicos del componente ejecutan operaciones de escritura/envio sin una autorizacion granular adicional:

- `saveNotification()` crea o actualiza notificaciones y puede disparar envio inmediato.
- `deleteNotif()` elimina notificaciones.
- `changeStatus()` cambia estado y puede disparar envio.
- `resendPush()` reenvia notificaciones push.

El seeder definia permisos separados (`notifications.view`, `notifications.create`, `notifications.send`), pero faltaba separar actualizacion y eliminacion. La prueba dinamica creo un usuario temporal con solo `notifications.view`, renderizo el componente y ejecuto `saveNotification()`. Resultado validado antes de la correccion:

```text
before=0; after=1; user_id=14
```

El registro de auditoria fue creado por un usuario sin `notifications.create` ni `notifications.send`. Luego se eliminaron el registro y el usuario temporal.

**Impacto**

Una cuenta con permiso de solo lectura sobre notificaciones puede crear, editar, eliminar, activar o reenviar notificaciones si logra interactuar con el componente Livewire. Esto rompe el principio de minimo privilegio y puede convertirse en envio no autorizado de mensajes push o manipulacion de contenido administrativo.

**Correccion aplicada**

- `database/seeders/RolesAndPermissionsSeeder.php` incluye `notifications.update` y `notifications.delete`.
- `NotificationsPush` valida con `Gate::authorize()` cada metodo sensible: crear, editar, eliminar, cambiar estado y reenviar.
- El envio inmediato requiere `notifications.send`; crear un aviso inactivo solo requiere `notifications.create`.
- La vista oculta botones de crear, reenviar, cambiar estado y eliminar cuando el usuario no tiene permisos.
- La base local fue sincronizada con `php artisan db:seed --class=RolesAndPermissionsSeeder`.
- Se agregaron pruebas regresivas: un usuario con solo `notifications.view` puede ver, pero no puede crear ni mutar avisos; un usuario con `notifications.create` pero sin `notifications.send` solo puede guardar avisos inactivos.

**Recomendacion residual**

- Agregar autorizaciones dentro de cada metodo publico sensible, no solo en `mount()`.
- Cubrir con pruebas regresivas: un usuario con solo `notifications.view` debe poder abrir la vista, pero debe recibir `403` o error de autorizacion al llamar `saveNotification`, `deleteNotif`, `changeStatus` y `resendPush`.
- Como defensa adicional, normalizar/validar columnas y direccion de ordenamiento antes de construir consultas.

### SEC-RF-02 - Recomendacion - Debugbar expone metadatos HTTP si el entorno local se comparte o publica

**Archivos/rutas relacionados**

- `.env`
- `config/debugbar.php`
- Ruta `/_debugbar/open`

**Evidencia**

En el entorno local auditado, `/_debugbar/open` respondio `200 OK` y devolvio JSON con metadatos de solicitudes recientes, incluyendo rutas, tiempos e IPs. Esto coincide con `APP_ENV=local` y `APP_DEBUG=true`.

**Impacto**

En una maquina local no expuesta, el riesgo es bajo y esperado. Si este servidor de desarrollo se publica por LAN, VPN, tunel, proxy o staging mal configurado, Debugbar puede revelar informacion operativa y detalles de solicitudes que ayuden a ampliar otros ataques.

**Estado**

Revisado el 2026-08-22. La configuracion ya apaga Debugbar fuera de `local` aunque `APP_DEBUG` quede activo por error. No requiere cambio de codigo adicional; se mantiene como recomendacion operativa.

**Recomendacion**

- Mantener `DEBUGBAR_ENABLED=false` en cualquier entorno compartido, staging o demo.
- No exponer `php artisan serve`, Apache/XAMPP o tuneles con Debugbar activo.
- Limpiar almacenamiento de Debugbar si se manipularon datos sensibles durante pruebas.
- Conservar la regla ya documentada en la auditoria anterior: Debugbar solo local y nunca productivo.

### SEC-RF-03 - Corregido - Baja de token Web Push no valida propietario del token

**Archivos afectados**

- `app/Http/Controllers/FirebaseWebPushController.php`
- `app/Services/FcmTokenService.php`

**Evidencia**

`FirebaseWebPushController::destroyToken()` valida que exista un `token` en la solicitud autenticada y llama a `FcmTokenService::deactivateTokenByValue($token, ...)`. El servicio busca solo por valor de token:

```php
FcmToken::where('token', $token)->first()
```

No se filtra por `user_id` del usuario autenticado, aunque otros metodos del servicio, como `isActiveWebTokenForUser()`, si comprueban usuario y plataforma.

**Impacto**

Si un token Web Push de otro usuario se filtra o se obtiene desde un cliente comprometido, cualquier usuario autenticado podria desactivarlo. No se confirmo exposicion publica de tokens, y el token es de alta entropia, por lo que el riesgo es limitado, pero la validacion de propiedad deberia ser consistente.

**Correccion aplicada**

`FirebaseWebPushController::destroyToken()` usa `FcmTokenService::deactivateWebTokenForUser()`, que filtra por usuario autenticado, token, `platform = web` y `delivery_platform = web`. Se agrego una prueba regresiva que confirma que un usuario no puede desactivar el token Web Push de otro usuario.

**Recomendacion residual**

- Mantener la prueba regresiva para evitar que futuras refactorizaciones vuelvan a desactivar tokens por valor global desde endpoints autenticados.

### SEC-RF-04 - Corregido - Ruta firmada `storage/{path}` activa para el disco local

**Archivo relacionado**

- `config/filesystems.php`

**Evidencia**

Al momento de la auditoria, el disco local tenia `serve => true`, lo que registraba la ruta `storage/{path}` con firma. La prueba dinamica confirmo:

- URL firmada hacia `storage/audit-private.txt`: `200 OK`.
- GET sin firma al mismo archivo: `403`.
- PUT sin firma: `403` y no creo archivo.

No se confirmo bypass sin firma ni escritura no autorizada.

**Impacto**

No es una vulnerabilidad explotable directamente en el estado actual. El riesgo es de superficie: cualquier codigo que genere una URL temporal sobre el disco local podria exponer archivos bajo `storage/app/private`. La aplicacion ya tiene rutas controladas para multimedia (`/media`), por lo que conviene confirmar si esta capacidad generica es necesaria.

**Correccion aplicada**

El disco `local` quedo con `serve => false` porque la aplicacion sirve imagenes administradas mediante rutas propias y controladores especificos. No se encontro uso de `Storage::temporaryUrl()` sobre el disco local; los previews encontrados corresponden a archivos temporales Livewire en formularios.

**Recomendacion residual**

- Mantener `serve => false` mientras las imagenes administradas sigan saliendo por rutas propias.
- Si en el futuro se necesita `Storage::temporaryUrl()` sobre el disco local, centralizar su uso y limitarlo a rutas permitidas.

## Riesgos revisados sin hallazgo confirmado

### Path traversal

Pruebas contra `/media/mma/..%2F..%2F.env`, `/storage/../../.env` y rutas de multimedia no filtraron `.env` ni archivos fuera de directorios permitidos. Las pruebas `GalleryMediaRouteTest`, `PublicMediaRouteTest` y `LegacyPublicSurfaceTest` pasaron correctamente.

### XSS

No se confirmo XSS almacenado ni reflejado. Se revisaron vistas Blade, componentes Livewire y usos sensibles de `x-html`. El componente `tw-select` usa `x-html`, pero la funcion `labelHtml()` escapa el texto antes de devolver HTML. En notificaciones, la descripcion se renderiza con escaping antes de aplicar saltos de linea. Las pruebas Playwright de CSP pasaron sin violaciones.

### Inyeccion SQL

No se encontro concatenacion directa de entradas de usuario en SQL crudo para los flujos revisados. Las busquedas y filtros principales usan Query Builder/Eloquent con parametros enlazados. Se observo que algunos estados de ordenamiento en Livewire son publicos; no se confirmo inyeccion SQL con la implementacion actual de Laravel, pero se recomienda normalizar columnas/direccion como defensa.

### CSRF

Un `POST /contacto` sin token CSRF devolvio `419`, confirmando que el middleware esta activo. La configuracion corregida en la auditoria anterior sigue funcionando.

### Cabeceras y CSP

Las pruebas automatizadas de seguridad y navegador confirmaron presencia de cabeceras y ausencia de violaciones CSP en los flujos cubiertos. En local, Debugbar puede alterar/relajar el comportamiento de depuracion, por lo que la validacion productiva debe hacerse con Debugbar deshabilitado.

### Dependencias

`composer audit --format=summary` no reporto advisories. `npm audit --audit-level=moderate` reporto `0 vulnerabilities`.

### IDOR/autorizacion en portal de suscriptor

Se revisaron flujos de detalle de pagos, solicitudes de compra y subida de comprobantes. Los controladores validan propiedad con `user_id` antes de devolver o modificar recursos. No se confirmo IDOR en esos puntos.

## Prioridad sugerida de correccion

1. Mantener pruebas regresivas de permisos de notificaciones en cada cambio del modulo.
2. Mantener Debugbar deshabilitado en cualquier entorno compartido o expuesto.
3. Mantener `serve => false` en el disco local mientras no se use para URLs temporales controladas.
