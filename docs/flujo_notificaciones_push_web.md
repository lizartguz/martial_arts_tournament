# Flujo de notificaciones Push Web

Documento actualizado: 11 de junio de 2026.

## Objetivo

Este documento explica el recorrido completo de una notificación Push Web en el proyecto Environmental:

1. El usuario inicia sesión.
2. El navegador solicita permiso.
3. Firebase genera el token y Laravel lo guarda.
4. El administrador crea el aviso.
5. Laravel identifica los usuarios destinatarios.
6. Laravel obtiene sus tokens activos.
7. Laravel envía el mensaje a Firebase Cloud Messaging.
8. Firebase entrega el mensaje y el navegador lo muestra.

Cada paso indica el archivo responsable, un fragmento reducido y la función que cumple.

## Resumen visual

```text
Usuario inicia sesión
        |
        v
Se carga push-notifications.js
        |
        v
Navegador solicita permiso
        |
        v
Firebase genera token FCM
        |
        v
Laravel guarda el token en fcm_tokens
        |
        v
Administrador crea un aviso
        |
        v
Laravel obtiene destinatarios y tokens
        |
        v
Laravel envía el mensaje a FCM HTTP v1
        |
        v
Firebase Push Service entrega el mensaje
        |
        +--> Página visible: onMessage()
        |
        +--> Segundo plano: onBackgroundMessage()
```

---

## Paso 1: el usuario inicia sesión

### Archivo

```text
app/Providers/FortifyServiceProvider.php
```

### Fragmento principal

```php
Fortify::authenticateUsing(function (Request $request) {
    // Busca y valida al usuario.
});
```

### Funcionamiento

Laravel Fortify procesa el formulario de acceso, valida:

- Correo y contraseña.
- Estado del usuario.
- Estado de pago o suscripción.
- Tipo de acceso.
- Roles permitidos.

Cuando la autenticación termina correctamente, Laravel crea la sesión y redirige al panel.

El layout autenticado incluye el identificador del usuario actual:

### Archivo

```text
resources/views/vendor/adminlte/master.blade.php
```

### Fragmento

```blade
<meta name="authenticated-user-id" content="{{ auth()->id() }}">
```

Este valor permite detectar si el navegador comenzó a utilizarse con una cuenta diferente y reasociar correctamente el token FCM.

El mismo layout carga el módulo Push Web:

```blade
@vite('resources/js/push-notifications.js')
```

---

## Paso 2: el navegador solicita permiso

### Archivo

```text
resources/js/push-notifications.js
```

### Fragmento

```js
if (
    Notification.permission === 'default'
    && !window.localStorage.getItem(askedPermissionKey)
) {
    window.localStorage.setItem(askedPermissionKey, '1');
    const permission = await Notification.requestPermission();

    if (permission !== 'granted') {
        return;
    }
}
```

### Funcionamiento

`Notification.requestPermission()` muestra la ventana nativa del navegador:

```text
¿Permitir que este sitio envíe notificaciones?
```

Los estados posibles son:

| Estado | Significado |
| --- | --- |
| `default` | El usuario todavía no respondió |
| `granted` | El usuario aceptó |
| `denied` | El usuario bloqueó las notificaciones |

La clave:

```text
environmental_web_push_permission_requested
```

se guarda en `localStorage` para no mostrar la solicitud repetidamente.

Si el usuario acepta, el flujo continúa. Si rechaza, no se genera ni registra un token.

---

## Paso 3: se registra el Service Worker

### Archivo

```text
resources/js/push-notifications.js
```

### Fragmento

```js
const registration = await navigator.serviceWorker.register(
    '/firebase-messaging-sw.js'
);
```

### Funcionamiento

El Service Worker es un proceso administrado por el navegador que puede recibir eventos Push aunque:

- La pestaña esté oculta.
- El usuario esté en otro módulo.
- La página esté cerrada.

La URL registrada es entregada por Laravel.

### Ruta

```text
routes/web.php
```

```php
Route::get(
    '/firebase-messaging-sw.js',
    [FirebaseWebPushController::class, 'serviceWorker']
);
```

### Controlador

```text
app/Http/Controllers/FirebaseWebPushController.php
```

```php
return response()
    ->view('firebase.messaging-sw')
    ->header('Content-Type', 'application/javascript; charset=UTF-8')
    ->header('Service-Worker-Allowed', '/');
```

Laravel renderiza como JavaScript el archivo:

```text
resources/views/firebase/messaging-sw.blade.php
```

---

## Paso 4: Firebase genera el token y Laravel lo guarda

Este paso combina la generación, envío y persistencia del token.

### 4.1 Firebase genera u obtiene el token

### Archivo

```text
resources/js/push-notifications.js
```

### Fragmento

```js
const messaging = getMessaging(firebaseApp);

const token = await getToken(messaging, {
    vapidKey,
    serviceWorkerRegistration: registration,
});
```

### Funcionamiento

`getToken()` solicita a Firebase un token que identifica la suscripción Push de ese navegador.

El token está vinculado con:

- La aplicación Firebase.
- El navegador y su perfil.
- El dominio.
- La clave VAPID.
- El Service Worker registrado.

No identifica únicamente a una persona. Por eso Laravel debe asociarlo con el usuario autenticado.

### 4.2 Se comprueba la cuenta autenticada

### Archivo

```text
resources/js/push-notifications.js
```

### Fragmento

```js
const previousToken = localStorage.getItem(savedTokenKey);
const previousUserId = localStorage.getItem(savedUserIdKey);

if (previousToken !== token || previousUserId !== authenticatedUserId) {
    await persistWebToken(token);
}
```

### Funcionamiento

El token se vuelve a sincronizar cuando:

- Firebase entrega un token diferente.
- Otro usuario inicia sesión en el mismo navegador.

Esto evita que el token continúe asociado a la cuenta anterior.

Las claves locales utilizadas son:

```text
environmental_web_push_token
environmental_web_push_user_id
```

### 4.3 El navegador envía el token a Laravel

### Archivo

```text
resources/js/push-notifications.js
```

### Fragmento

```js
await fetch('/firebase/web-push/token', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify({ token }),
});
```

La ruta está protegida por autenticación:

```text
routes/web.php
```

```php
Route::post(
    '/firebase/web-push/token',
    [FirebaseWebPushController::class, 'storeToken']
);
```

### 4.4 El controlador asocia el token con el usuario

### Archivo

```text
app/Http/Controllers/FirebaseWebPushController.php
```

### Fragmento

```php
$this->fcmTokenService->registerWebTokenForUser(
    $request->user(),
    $validated['token'],
    $context
);
```

`$request->user()` representa al usuario de la sesión actual.

### 4.5 El servicio guarda o reactiva el token

### Archivo

```text
app/Services/FcmTokenService.php
```

### Fragmento

```php
return FcmToken::query()->updateOrCreate(
    ['token' => $token],
    [
        'user_id' => $userId,
        'platform' => 'web',
        'last_seen_at' => $now,
        'invalidated_at' => null,
        'is_active' => true,
    ]
);
```

### Funcionamiento

`updateOrCreate()`:

- Crea el registro si el token es nuevo.
- Actualiza el registro si ya existe.
- Reasigna el token al usuario actual.
- Reactiva un token que volvió a ser válido.
- Actualiza su última fecha de actividad.

El token queda almacenado en:

```text
fcm_tokens
```

---

## Paso 5: el administrador crea un aviso

### Archivos

```text
app/Livewire/NotificationsPush/NotificationsPush.php
resources/views/livewire/notifications-push/notifications-push.blade.php
```

### Fragmento principal

```php
$notification->fill([
    'title' => trim($this->titleAU),
    'description' => trim($this->descriptionAU),
    'scheduled_at' => $this->scheduledAtAU ?: null,
    'delivery_platform' => $this->deliveryPlatformAU,
    'metadata' => [
        'target_user_ids' => $targetUserIds,
    ],
]);
```

### Funcionamiento

Desde `/notificationsp`, el administrador define:

- Título y descripción.
- Enlace.
- Imágenes.
- Fecha programada.
- Fecha de expiración.
- Plataforma `web`, `mobile` o `all`.
- Usuarios individuales o todos los usuarios.

Los usuarios seleccionados se guardan dentro de:

```json
{
  "target_user_ids": [12, 45, 98]
}
```

Si el arreglo queda vacío, el aviso se dirige a todos los usuarios que tengan tokens activos para la plataforma elegida.

Si el aviso está activo y su fecha ya corresponde, se intenta enviar inmediatamente:

```php
app(PushNotificationService::class)
    ->sendNotificationRecord($notification->fresh());
```

---

## Paso 6: Laravel obtiene los tokens destinatarios

### Archivo

```text
app/Services/PushNotificationService.php
```

### Fragmento

```php
$tokens = $this->fcmTokenService->getActiveTokensForUsers(
    $userIds,
    $deliveryPlatform
);
```

La consulta se realiza en:

```text
app/Services/FcmTokenService.php
```

```php
return FcmToken::query()
    ->when(!empty($userIds), function ($query) use ($userIds) {
        $query->whereIn('user_id', $userIds);
    })
    ->where('is_active', true)
    ->whereNull('invalidated_at')
    ->when($deliveryPlatform !== 'all', function ($query) use ($deliveryPlatform) {
        $query->where('platform', $deliveryPlatform);
    })
    ->get();
```

### Funcionamiento

Laravel selecciona únicamente tokens que:

- Pertenecen a los usuarios destinatarios.
- Están activos.
- No fueron invalidados.
- Coinciden con la plataforma solicitada.

Ejemplos:

| Configuración | Tokens seleccionados |
| --- | --- |
| `web` | Solo navegadores |
| `mobile` | Solo dispositivos móviles |
| `all` | Navegadores y móviles |

Un usuario puede producir varios tokens porque puede utilizar varios navegadores o dispositivos.

---

## Paso 7: Laravel envía el mensaje a Firebase

### Archivo

```text
app/Services/PushNotificationService.php
```

### 7.1 Construcción del mensaje

```php
$payload = $this->buildPayload(
    $tokenRecord->token,
    $title,
    $body,
    $link,
    $image,
    $data
);
```

El payload contiene:

- Token destinatario.
- Título.
- Descripción.
- Imagen.
- Enlace.
- Datos complementarios.
- Configuración web y Android.

### 7.2 Envío a FCM HTTP v1

```php
$response = Http::withToken($accessToken)
    ->acceptJson()
    ->post($url, $payload);
```

### Funcionamiento

Laravel no se conecta directamente con el navegador del usuario.

El recorrido es:

```text
Laravel -> Firebase Cloud Messaging -> Servicio Push del navegador -> Navegador
```

`$accessToken` se obtiene usando la cuenta de servicio privada de Firebase.

`$url` corresponde al endpoint:

```text
https://fcm.googleapis.com/v1/projects/PROYECTO/messages:send
```

Si Firebase acepta el mensaje, Laravel actualiza:

```text
fcm_tokens.last_sent_at
notifications.push_sent_at
```

---

## Paso 8: Firebase entrega la notificación

Firebase entrega un mensaje, no un archivo. La recepción cambia según el estado de la plataforma.

## 8.1 Página abierta y visible

### Archivo

```text
resources/js/push-notifications.js
```

### Escuchador

```js
onMessage(messaging, (payload) => {
    showForegroundNotification(payload);
});
```

### Funcionamiento

`onMessage()` permanece registrado durante la vida de la página.

Cuando FCM entrega un mensaje con la plataforma visible:

1. Firebase SDK recibe el payload.
2. Ejecuta la función registrada en `onMessage()`.
3. `showForegroundNotification()` crea la notificación nativa.

```js
const browserNotification = new Notification(title, {
    body,
    icon,
    image,
});
```

Cuando el usuario hace clic:

```js
browserNotification.onclick = () => {
    window.focus();
    window.location.href = link;
    browserNotification.close();
};
```

La ventana se enfoca y navega al enlace configurado.

## 8.2 Página cerrada, oculta o en segundo plano

### Archivo

```text
resources/views/firebase/messaging-sw.blade.php
```

### Escuchador

```js
messaging.onBackgroundMessage((payload) => {
    self.registration.showNotification(title, options);
});
```

### Funcionamiento

El Service Worker está registrado en el navegador, separado de la página visible.

Cuando llega un mensaje:

1. El servicio Push del navegador recibe el mensaje de Firebase.
2. El navegador despierta el Service Worker.
3. Firebase Messaging ejecuta `onBackgroundMessage()`.
4. `showNotification()` solicita al sistema operativo mostrar el aviso.

No existe un ciclo JavaScript consultando Firebase cada cierto tiempo. Tampoco se realiza polling.

La escucha está basada en eventos Push:

```text
Firebase envía -> navegador recibe -> navegador despierta Service Worker
```

El navegador administra internamente la conexión con su servicio Push. Por eso la notificación puede llegar aunque la página no esté ejecutándose.

## 8.3 Clic sobre una notificación en segundo plano

### Archivo

```text
resources/views/firebase/messaging-sw.blade.php
```

### Escuchador

```js
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(openOrFocusApplication());
});
```

### Funcionamiento

El Service Worker busca ventanas abiertas de la plataforma:

```js
clients.matchAll({
    type: 'window',
    includeUncontrolled: true,
});
```

Si encuentra una:

```js
client.navigate(link);
return client.focus();
```

Si no encuentra ninguna:

```js
return clients.openWindow(link);
```

Así se evita abrir ventanas innecesarias cuando la plataforma ya está abierta.

---

## Flujo de permisos

El permiso no se solicita en cada inicio de sesión.

```text
default -> se solicita permiso
granted -> continúa sin volver a preguntar
denied  -> se detiene; el usuario debe cambiarlo manualmente
```

Si el permiso está bloqueado:

```js
if (Notification.permission === 'denied') {
    await unregisterStoredToken();
    return;
}
```

Laravel recibe una petición `DELETE` y desactiva el token:

```text
DELETE /firebase/web-push/token
```

---

## Detección de tokens inválidos

Un token no se considera inválido solamente por ser antiguo.

Laravel intenta enviar y analiza la respuesta de Firebase:

### Archivo

```text
app/Services/PushNotificationService.php
```

```php
return in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'], true)
    || in_array($status, ['NOT_FOUND', 'INVALID_ARGUMENT'], true);
```

Cuando Firebase confirma que el token ya no sirve:

### Archivo

```text
app/Services/FcmTokenService.php
```

```php
$token->forceFill([
    'is_active' => false,
    'invalidated_at' => Carbon::now('America/La_Paz'),
    'last_error_at' => Carbon::now('America/La_Paz'),
    'last_error_message' => $message,
])->save();
```

Desde ese momento, el token ya no aparece en futuras consultas.

Los errores temporales se registran, pero no invalidan automáticamente el token:

```php
$token->forceFill([
    'last_error_at' => Carbon::now('America/La_Paz'),
    'last_error_message' => $message,
])->save();
```

---

## Avisos programados

### Archivo

```text
routes/console.php
```

### Fragmento

```php
Artisan::command(
    'app:notification-dispatch-push',
    function (PushNotificationService $service) {
        $service->dispatchPendingNotifications();
    }
)->everyFiveMinutes()
  ->timezone('America/La_Paz');
```

### Funcionamiento

Cada cinco minutos Laravel busca avisos:

- Activos.
- No enviados.
- No expirados.
- Sin error previo.
- Con fecha programada ya alcanzada.

En producción, el servidor debe ejecutar el scheduler de Laravel:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

El cron despierta Laravel cada minuto. Laravel decide cuándo corresponde ejecutar la revisión de cinco minutos.

---

## Requisitos de funcionamiento

1. El usuario debe estar autenticado para registrar el token.
2. El usuario debe conceder permiso.
3. Debe existir una clave VAPID válida.
4. El Service Worker debe poder registrarse.
5. Producción debe utilizar HTTPS.
6. El token debe estar activo en `fcm_tokens`.
7. Laravel debe acceder a FCM HTTP v1.
8. El scheduler debe estar activo para avisos programados.

---

## Archivos principales

```text
app/Providers/FortifyServiceProvider.php
app/Http/Controllers/FirebaseWebPushController.php
app/Livewire/NotificationsPush/NotificationsPush.php
app/Services/FcmTokenService.php
app/Services/PushNotificationService.php
app/Models/FcmToken.php
app/Models/NotificationM.php

resources/js/firebase-app.js
resources/js/push-notifications.js
resources/views/firebase/messaging-sw.blade.php
resources/views/vendor/adminlte/master.blade.php
resources/views/livewire/notifications-push/notifications-push.blade.php

routes/web.php
routes/console.php
```

