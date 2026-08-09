# Configuracion de Firebase en Environmental

Documento actualizado: 11 de junio de 2026.

## 1. Objetivo

El proyecto integra Firebase para dos funciones principales:

1. **Google Analytics para Firebase**: registra navegacion y eventos de uso en el panel web.
2. **Firebase Cloud Messaging (FCM)**: envia notificaciones push a navegadores web y dispositivos moviles.

La integracion esta dividida en dos entornos:

- **Frontend web**: usa el SDK JavaScript de Firebase instalado con NPM.
- **Backend Laravel**: usa una cuenta de servicio y la API HTTP v1 de FCM para enviar mensajes.

Firebase Hosting no es necesario porque Laravel continua alojado en el servidor actual. Firebase se usa como servicio de Analytics y mensajeria, no como proveedor de hosting.

## 2. Arquitectura general

```text
Firebase / Google Analytics
          ^
          |
resources/js/analytics.js
          ^
          |
Layout AdminLTE -> Navegador del usuario


Firebase Cloud Messaging
          ^
          | API HTTP v1 + OAuth 2.0
          |
PushNotificationService (Laravel)
          ^
          |
notifications + fcm_tokens
          ^
          |
Marketing / Avisos


Navegador web
    |
    +-- push-notifications.js
    +-- token FCM
    +-- FirebaseWebPushController
    +-- tabla fcm_tokens
    +-- firebase-messaging-sw.js
```

## 3. Dependencias instaladas

### Frontend

En `package.json`:

```json
"firebase": "^12.14.0"
```

El SDK se instala con:

```bash
npm install firebase
```

Los puntos de entrada de Vite estan declarados en `vite.config.js`:

```text
resources/js/analytics.js
resources/js/push-notifications.js
```

Despues de cambiar archivos JavaScript o variables `VITE_*`, se debe generar nuevamente el bundle:

```bash
npm run build
```

En Windows, si PowerShell bloquea `npm.ps1`, se puede usar:

```bash
npm.cmd run build
```

### Backend

En `composer.json` se usa:

```json
"google/apiclient": "^2.0"
```

Esta libreria genera el token OAuth 2.0 de corta duracion usando la cuenta de servicio.

No se instalo un SDK de Firebase completo con Composer porque el backend envia directamente mediante la API HTTP v1 de FCM. Laravel utiliza:

- `google/apiclient` para autenticacion OAuth.
- `Http` de Laravel para realizar la peticion a Firebase.
- Servicios propios para gestionar tokens, errores y destinatarios.

## 4. Variables de entorno

Las plantillas estan documentadas en `.env.example`. Los valores reales se guardan solamente en `.env`.

### Variables publicas del frontend

```dotenv
VITE_FIREBASE_API_KEY=
VITE_FIREBASE_AUTH_DOMAIN=
VITE_FIREBASE_PROJECT_ID=
VITE_FIREBASE_STORAGE_BUCKET=
VITE_FIREBASE_MESSAGING_SENDER_ID=
VITE_FIREBASE_APP_ID=
VITE_FIREBASE_MEASUREMENT_ID=
VITE_FIREBASE_VAPID_KEY=
```

Estas variables se incluyen en el bundle JavaScript del navegador. No deben considerarse secretos:

- La configuracion web identifica la aplicacion Firebase.
- `VITE_FIREBASE_MEASUREMENT_ID` identifica el flujo web de Google Analytics.
- `VITE_FIREBASE_VAPID_KEY` es la clave publica usada para Web Push.

Aunque sean publicas, se recomienda restringir la API Key desde Google Cloud cuando corresponda y controlar los dominios autorizados.

### Variables privadas del backend

```dotenv
FIREBASE_SDK=app/firebase/nombre-del-service-account.json
FIREBASE_ADDSCOPE=https://www.googleapis.com/auth/firebase.messaging
FIREBASE_PETITION=https://fcm.googleapis.com/v1/projects/ID_PROYECTO/messages:send
```

Estas variables se leen desde `config/services.php`:

```php
'firebase' => [
    'sdk' => env('FIREBASE_SDK'),
    'petition' => env('FIREBASE_PETITION'),
    'scope' => env('FIREBASE_ADDSCOPE'),
],
```

`FIREBASE_SDK` es una ruta relativa a `storage/`. Actualmente el archivo activo se encuentra en:

```text
storage/app/firebase/
```

El JSON contiene una clave privada. Nunca se debe:

- Publicar en Git.
- Guardar en `.env.example`.
- Enviar al frontend.
- Exponer en capturas o logs.

El directorio `storage/app/` esta ignorado por Git mediante `storage/app/.gitignore`.

## 5. Configuracion compartida del frontend

Archivo:

```text
resources/js/firebase-app.js
```

Responsabilidades:

- Lee las variables `VITE_FIREBASE_*`.
- Construye `firebaseConfig`.
- Comprueba que existan las variables obligatorias.
- Inicializa una sola instancia con `initializeApp()`.
- Comparte esa instancia entre Analytics y Messaging.

Esto evita inicializar Firebase varias veces en la misma pagina.

El layout global de AdminLTE carga Analytics y Push al final:

```text
resources/views/vendor/adminlte/master.blade.php
```

Los scripts se cargan despues de Livewire y de los scripts propios de cada modulo para reducir conflictos con codigo heredado basado en jQuery.

## 6. Google Analytics

### Archivo principal

```text
resources/js/analytics.js
```

El flujo actual es:

1. Espera hasta que la pagina termine de cargar.
2. Verifica que la configuracion y `measurementId` existan.
3. Ejecuta `isSupported()` para comprobar compatibilidad.
4. Inicializa Analytics con `getAnalytics(firebaseApp)`.
5. Expone la instancia como `window.appAnalytics`.
6. Registra eventos personalizados.

### Eventos implementados

#### `app_open`

Se envia al inicializar Analytics en cada carga del layout AdminLTE.

> Nota: `app_open` es un nombre asociado normalmente a aplicaciones moviles. Para una medicion web mas clara, el evento automatico principal es `page_view`.

#### `sidebar_menu_click`

Se envia al hacer clic en una opcion del menu lateral.

Parametros:

| Parametro | Descripcion |
| --- | --- |
| `menu_label` | Texto visible de la opcion |
| `menu_href` | URL o destino del enlace |
| `parent_label` | Menu padre cuando corresponde |
| `interaction_type` | `navigation` o `toggle` |

Este evento permite saber que menus o modulos usan mas los usuarios.

Para usar estos parametros como dimensiones visibles en reportes de GA4 se deben crear dimensiones personalizadas en:

```text
Google Analytics > Administrar > Definiciones personalizadas
```

### Eventos web automaticos

Cuando Analytics esta correctamente inicializado, GA4 puede registrar eventos web automaticos o de medicion mejorada, entre ellos:

- `page_view`
- `session_start`
- `first_visit`
- `user_engagement`
- `scroll`
- clics salientes, busquedas, formularios o descargas cuando la medicion mejorada esta habilitada

La disponibilidad exacta depende de la configuracion del flujo web en Google Analytics.

### Donde se visualizan

En Firebase:

```text
Firebase Console > Analytics > Dashboard
```

En GA4:

```text
Google Analytics > Informes > En tiempo real
Google Analytics > Administrar > DebugView
Google Analytics > Informes > Interaccion > Eventos
```

Los informes normales pueden tardar entre 24 y 48 horas en procesarse. Para comprobar la instalacion se debe usar primero `En tiempo real` o `DebugView`.

### Local y produccion

Analytics puede enviar datos desde local y produccion si:

- El navegador puede conectarse a los dominios de Google.
- No existe un bloqueador de anuncios o privacidad que bloquee Analytics.
- El bundle fue reconstruido despues de modificar las variables `VITE_*`.
- La pagina utiliza el layout AdminLTE que incluye `analytics.js`.

Con la configuracion actual, local y produccion alimentan el mismo flujo de datos. Por ello, el trafico local puede mezclarse con el trafico real.

Una mejora futura recomendable es excluir trafico interno en GA4 o utilizar un flujo/proyecto diferente para desarrollo.

## 7. Firebase Cloud Messaging

### Componentes principales

| Archivo | Responsabilidad |
| --- | --- |
| `resources/js/push-notifications.js` | Solicita permiso, obtiene el token web y recibe mensajes en primer plano |
| `resources/views/firebase/messaging-sw.blade.php` | Service worker para mensajes en segundo plano |
| `app/Http/Controllers/FirebaseWebPushController.php` | Entrega el service worker y registra/desactiva tokens web |
| `app/Services/FcmTokenService.php` | Gestiona tokens web y moviles |
| `app/Services/PushNotificationService.php` | Envia mensajes por FCM HTTP v1 |
| `app/Models/FcmToken.php` | Modelo de tokens |
| `app/Models/NotificationM.php` | Modelo de avisos |
| `app/Livewire/NotificationsPush/NotificationsPush.php` | Formulario de marketing y avisos |

## 8. Registro de tokens Web Push

El flujo del navegador es:

1. `push-notifications.js` comprueba soporte de Service Worker y Notifications.
2. Solicita permiso al usuario una sola vez.
3. Registra `/firebase-messaging-sw.js`.
4. Solicita a Firebase un token usando la clave VAPID publica.
5. Envia el token a Laravel.
6. Laravel lo guarda o reactiva en `fcm_tokens`.

Rutas:

```http
GET /firebase-messaging-sw.js
POST /firebase/web-push/token
DELETE /firebase/web-push/token
```

Las rutas para registrar y eliminar tokens requieren un usuario autenticado.

Claves usadas en `localStorage`:

```text
environmental_web_push_permission_requested
environmental_web_push_token
```

La primera evita pedir permiso repetidamente. La segunda permite comparar y desactivar el token conocido.

## 9. Funcionamiento de las notificaciones en la web

### Web abierta y visible

Cuando la pestaña esta abierta:

1. Firebase recibe el mensaje.
2. `onMessage()` ejecuta `showForegroundNotification()`.
3. Se crea una notificacion mediante la API `Notification`.
4. Al hacer clic, se enfoca la ventana y se navega al enlace recibido.

### Web cerrada o en segundo plano

El navegador utiliza el service worker:

```text
resources/views/firebase/messaging-sw.blade.php
```

El service worker:

- Recibe el mensaje con `onBackgroundMessage()`.
- Muestra titulo, descripcion, icono e imagen.
- Guarda el enlace en `notification.data`.
- Al hacer clic, enfoca una ventana existente o abre una nueva.

### Requisito HTTPS

FCM Web Push y los Service Workers requieren un contexto seguro:

- Produccion debe usar HTTPS.
- `http://localhost` tiene una excepcion para desarrollo.
- Un dominio local personalizado servido solo por HTTP, por ejemplo `http://senvatec.test:8000`, puede no ser aceptado como contexto seguro.

Para probar Web Push con un dominio local personalizado se recomienda configurar HTTPS local.

## 10. Registro de tokens moviles

Las aplicaciones moviles envian el token en el campo:

```text
fcmToken
```

Opcionalmente pueden enviar:

```text
deviceIdentifier
device_identifier
identifier
deviceName
device_name
versionApp
version_app
```

El metodo central es:

```text
app/Http/Controllers/Controller.php
```

Metodo:

```php
syncMobileFcmToken()
```

Varios controladores API lo llaman cuando reciben actividad de la aplicacion. Finalmente, `FcmTokenService::registerMobileTokenForUser()` registra o actualiza el token.

## 11. Tabla `fcm_tokens`

Migracion:

```text
database/migrations/2026_06_10_000001_create_fcm_tokens_table.php
```

La tabla permite que un usuario tenga varios dispositivos y navegadores.

Campos principales:

| Campo | Funcion |
| --- | --- |
| `user_id` | Propietario del token |
| `token` | Token unico generado por FCM |
| `platform` | `mobile` o `web` |
| `delivery_platform` | `android`, `ios`, `web` o `unknown` |
| `device_identifier` | Identificador del dispositivo |
| `device_name` | Nombre legible |
| `browser` | Navegador para tokens web |
| `last_seen_at` | Ultimo registro o renovacion |
| `last_sent_at` | Ultimo envio exitoso |
| `last_error_at` | Fecha del ultimo error |
| `last_error_message` | Detalle del ultimo error |
| `invalidated_at` | Fecha en que dejo de ser util |
| `is_active` | Habilita o deshabilita futuros envios |

Los tokens no se duplican porque `token` tiene un indice unico. Si el mismo token vuelve a registrarse:

- Se actualiza su usuario y contexto.
- Se reactiva.
- Se limpia la invalidacion y el error anterior.

Cuando Firebase responde `UNREGISTERED`, `NOT_FOUND` o determinados errores de argumento, el token se invalida y deja de usarse.

## 12. Creacion y envio de avisos

La interfaz se encuentra en:

```text
/notificationsp
```

El formulario permite:

- Titulo y descripcion.
- Enlace opcional.
- Fecha programada.
- Fecha de expiracion.
- Estado activo o inactivo.
- Plataforma: movil y web, solo movil o solo web.
- Todos los usuarios o usuarios individuales.
- Busqueda de usuarios y filtro por rol.
- Hasta tres imagenes.

Los usuarios seleccionados se guardan en:

```json
{
  "target_user_ids": [1, 5, 20]
}
```

dentro de `notifications.metadata`.

Si `target_user_ids` esta vacio, el aviso se considera dirigido a todos los usuarios que tengan tokens activos para la plataforma seleccionada.

## 13. Envio inmediato y programado

### Envio inmediato

Al guardar un aviso activo:

- Si no tiene fecha futura, se envia inmediatamente.
- Si fue enviado correctamente, se registra `push_sent_at`.

### Envio programado

El comando se define en:

```text
routes/console.php
```

Comando:

```bash
php artisan app:notification-dispatch-push
```

Programacion:

```text
Cada cinco minutos
Zona horaria: America/La_Paz
```

Para que funcione en produccion, el servidor debe ejecutar el scheduler de Laravel. Una configuracion comun de cron es:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

El comando busca avisos:

- Activos.
- No enviados.
- Sin error previo.
- No expirados.
- Con fecha programada vencida o sin fecha programada.

## 14. Envio desde Laravel a Firebase

`PushNotificationService` realiza este proceso:

1. Consulta tokens activos mediante `FcmTokenService`.
2. Lee el JSON de cuenta de servicio.
3. Solicita un access token OAuth 2.0.
4. Guarda el access token en cache durante 45 minutos.
5. Construye un payload compatible con Android y Web Push.
6. Envia una peticion por cada token a FCM HTTP v1.
7. Actualiza fechas y errores de tokens y avisos.

El payload puede incluir:

- Titulo.
- Descripcion.
- Imagen.
- Enlace.
- ID del aviso.
- Plataforma de entrega.

## 15. Configuracion en Firebase Console

### Aplicacion web

En:

```text
Firebase Console > Configuracion del proyecto > General
```

Debe existir una aplicacion web. De esa aplicacion salen las variables `VITE_FIREBASE_*`.

### Google Analytics

Debe estar vinculado al proyecto Firebase y existir un flujo web con el mismo `measurementId` configurado en:

```dotenv
VITE_FIREBASE_MEASUREMENT_ID=
```

### Web Push

En:

```text
Configuracion del proyecto > Cloud Messaging > Web Push certificates
```

Debe existir un par de claves VAPID. La clave publica se guarda en:

```dotenv
VITE_FIREBASE_VAPID_KEY=
```

### Cuenta de servicio

En:

```text
Configuracion del proyecto > Cuentas de servicio
```

Se genera la clave privada JSON usada por Laravel. Tambien debe estar habilitada la API de Firebase Cloud Messaging.

## 16. Despliegue

En cada ambiente:

1. Configurar las variables en `.env`.
2. Colocar el JSON privado dentro de `storage/app/firebase/`.
3. Verificar permisos de lectura para el usuario que ejecuta PHP.
4. Ejecutar migraciones.
5. Instalar dependencias y compilar assets.
6. Limpiar o reconstruir cache de Laravel.
7. Configurar el scheduler.
8. Confirmar que produccion usa HTTPS.

Comandos habituales:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

Despues del despliegue se debe comprobar que `/firebase-messaging-sw.js` devuelve JavaScript con la configuracion esperada.

> Observacion tecnica: el service worker actual lee las variables con `env()` desde una vista Blade. Cuando se utiliza `config:cache`, Laravel recomienda acceder a variables mediante archivos `config/*.php`. Conviene validar este endpoint despues de cachear configuracion.

## 17. Diagnostico

### Analytics no muestra datos

Comprobar:

- El `measurementId` pertenece al flujo web correcto.
- Se ejecuto `npm run build` despues de cambiar `.env`.
- La pagina usa el layout AdminLTE.
- No hay bloqueadores de anuncios.
- La consola no muestra `Firebase Analytics could not be initialized`.
- Los eventos aparecen en En tiempo real o DebugView.
- Se espero el tiempo de procesamiento para informes normales.

### No aparece la solicitud de permiso Web Push

Comprobar:

- HTTPS o `localhost`.
- Permiso del navegador no bloqueado.
- Existencia de `VITE_FIREBASE_VAPID_KEY`.
- El navegador soporta Notifications, Push API y Service Workers.
- El valor `environmental_web_push_permission_requested` en `localStorage`.

Si se necesita volver a probar la solicitud:

1. Restablecer el permiso de notificaciones del sitio desde el navegador.
2. Eliminar las claves `environmental_web_push_permission_requested` y `environmental_web_push_token` de `localStorage`.
3. Recargar la pagina.

### El token web no se guarda

Comprobar:

- Usuario autenticado.
- CSRF valido.
- Respuesta de `POST /firebase/web-push/token`.
- Tabla `fcm_tokens` migrada.
- Logs de Laravel.

### El backend no envia

Comprobar:

- Ruta correcta en `FIREBASE_SDK`.
- JSON valido y perteneciente al proyecto correcto.
- `FIREBASE_ADDSCOPE` correcto.
- Endpoint HTTP v1 correcto.
- Cloud Messaging API habilitada.
- Acceso saliente del servidor a Google.
- Logs en `storage/logs/laravel.log`.
- Campos `push_last_error_at` y `push_last_error_message`.

## 18. Archivos relacionados

```text
.env
.env.example
config/services.php
package.json
composer.json
vite.config.js

resources/js/firebase-app.js
resources/js/analytics.js
resources/js/push-notifications.js
resources/views/firebase/messaging-sw.blade.php
resources/views/vendor/adminlte/master.blade.php
resources/views/livewire/notifications-push/notifications-push.blade.php

app/Http/Controllers/Controller.php
app/Http/Controllers/FirebaseWebPushController.php
app/Livewire/NotificationsPush/NotificationsPush.php
app/Models/FcmToken.php
app/Models/NotificationM.php
app/Models/User.php
app/Services/FcmTokenService.php
app/Services/PushNotificationService.php

database/migrations/2024_07_05_005402_create_notifications_table.php
database/migrations/2026_06_10_000001_create_fcm_tokens_table.php

routes/web.php
routes/console.php
```

## 19. Referencias oficiales

- Firebase Web: https://firebase.google.com/docs/web/setup
- Google Analytics para Firebase: https://firebase.google.com/docs/analytics/get-started
- Eventos automaticos de GA4: https://support.google.com/analytics/answer/9234069
- Confirmar recoleccion de datos: https://support.google.com/analytics/answer/9333790
- FCM Web Push: https://firebase.google.com/docs/cloud-messaging/web/get-started
- Recepcion de mensajes web: https://firebase.google.com/docs/cloud-messaging/web/receive-messages
- FCM HTTP v1: https://firebase.google.com/docs/cloud-messaging/send/v1-api
