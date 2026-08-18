# Flujo de inicio de sesión (login)

Documento actualizado: 18 de agosto de 2026.

## Objetivo

Este documento explica toda la lógica de autenticación del proyecto Environmental: cómo se valida un intento de login y qué reglas debe cumplir un usuario según sea **interno** (personal de la organización) o **externo** (cliente con suscripción).

Toda la lógica vive en un único archivo:

```text
app/Providers/FortifyServiceProvider.php
```

El proyecto usa **Laravel Fortify**. La autenticación por defecto de Fortify se reemplaza con un callback propio registrado en `Fortify::authenticateUsing()`, dentro del método `boot()`.

---

## Conceptos clave

Antes del flujo, conviene entender los campos del usuario que deciden el resultado:

| Campo / concepto | Significado | Valores |
| --- | --- | --- |
| `access_type` | Tipo de acceso. Define la rama de validación. | `0` = interno, `1` = externo (suscriptor) |
| `state` | Cuenta habilitada o desactivada manualmente. | `1` = activa, otro = inactiva |
| `payment_state` | Pago habilitado (solo externos). | `1` = pagado/habilitado |
| `own_state` | Estado propio de la suscripción (solo externos). | `1` = válido |
| `date_expiry` | Fecha de expiración de la suscripción (tipo `date`). | `YYYY-MM-DD` o `null` |
| `temporary_extension_end` | Fin de la prórroga temporal posterior a la expiración. | `YYYY-MM-DD` o `null` |
| `type_subscriptions` | Nombre del tipo de suscripción. | p. ej. `Personal Demo` |
| Roles internos | `super_manager`, `admin`, `manager`, `meteorology`, `meteorologist`, `technical`, `sales`, `marketing` | — |
| Rol externo | `subscriber` (el externo debe tener **solo** este rol) | — |

Constantes que representan estos valores en el código:

```php
private const INTERNAL_ACCESS_TYPE = 0;
private const SUBSCRIBER_ACCESS_TYPE = 1;
private const INTERNAL_ROLE_NAMES = [
    'super_manager', 'admin', 'manager', 'meteorology',
    'meteorologist', 'technical', 'sales', 'marketing',
];
private const EXTERNAL_ROLE_NAME = 'subscriber';
```

---

## Resumen visual

```text
POST /login  (email, password)
        |
        v
Fortify::authenticateUsing()  -> zona horaria America/La_Paz
        |
        v
Validar formato: email requerido + password requerido
        |
        v
findUserForLogin(email)  -> busca usuario por email
        |
        v
¿Existe usuario y Hash::check(password)?  --NO--> denyLogin('auth.failed')
        | SI
        v
ensureUserCanAuthenticate(user, hoy)
        |
        +-- access_type == 0 (INTERNO) ----> ensureInternalUserCanAuthenticate()
        |                                       - state == 1
        |                                       - tiene algún rol interno
        |
        +-- access_type == 1 (EXTERNO) ----> loadExternalSubscription()
        |                                    ensureExternalUserCanAuthenticate()
        |                                       - state == 1
        |                                       - payment_state == 1
        |                                       - suscripción vigente
        |                                       - own_state == 1
        |                                       - type != 'Personal Demo'
        |                                       - solo rol 'subscriber'
        |
        +-- otro valor -------------------> denyLogin('No tienes los permisos...')
        |
        v
logAuthenticationOutcome()  -> registra éxito y retorna el usuario (login OK)
```

Cualquier validación fallida llama a `denyLogin()`, que lanza una `ValidationException` y corta el flujo. El intento se registra en el log antes de propagar el error.

---

## Punto de entrada: `boot()`

```php
Fortify::authenticateUsing(function (Request $request) {
    try {
        date_default_timezone_set('America/La_Paz');

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = $this->findUserForLogin($request->input('email'));

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $this->denyLogin(__('auth.failed'));
        }

        $this->ensureUserCanAuthenticate($user, date('Y-m-d'));

        return $this->logAuthenticationOutcome($request, $user);
    } catch (ValidationException $exception) {
        $this->logFailedLoginAttempt($request, $exception);
        throw $exception;
    }
});
```

Función que cumple:

1. Fija la zona horaria a `America/La_Paz` para que `date('Y-m-d')` represente la fecha local boliviana.
2. Valida que lleguen `email` (con formato) y `password`.
3. Busca el usuario por correo (`findUserForLogin`).
4. Si no existe o la contraseña no coincide, deniega con el mensaje genérico `auth.failed` (no revela si el correo existe).
5. Si las credenciales son correctas, delega las reglas de negocio a `ensureUserCanAuthenticate`, pasando la fecha de hoy.
6. Registra el resultado y, si todo pasó, **retorna el usuario** (Fortify lo interpreta como login exitoso).
7. Si en cualquier punto se lanzó una `ValidationException`, registra el intento fallido y la vuelve a lanzar.

> Nota: `boot()` también define la vista de login (`auth.login`), las acciones de registro/actualización de perfil/contraseña de Fortify, y los **rate limiters** (ver sección final).

La vista de login se renderiza con el componente `x-guest-layout`, que usa `resources/views/layouts/guest.blade.php`. Ese layout debe mantener las directivas Blade compilables (`@vite`, `csrf_token()`, `asset()`, Livewire) para que los estilos de autenticación se carguen desde Vite y no queden impresos como texto crudo en el HTML.

---

## Eliminación lógica de cuentas

El flujo vigente de eliminación de cuenta desde `/user/profile` conserva el registro en `users` y marca `deleted_at`. El modelo `App\Models\User` usa `SoftDeletes`, por lo que las consultas normales de Eloquent y el login de Fortify excluyen automáticamente esas cuentas.

La acción `App\Actions\Jetstream\DeleteUser` revoca tokens y elimina la foto de perfil por seguridad y privacidad, pero la fila del usuario permanece disponible para auditoría y trazabilidad histórica. Si se requiere liberar el correo para un nuevo registro, debe definirse una política adicional de anonimización o reemplazo controlado de identificadores únicos.

---

## Paso 1: búsqueda del usuario — `findUserForLogin()`

```php
private function findUserForLogin(string $email): ?User
{
    return User::query()
        ->where('email', $email)
        ->select(
            'id', 'name', 'lastname', 'password', 'email', 'number_phone',
            'image', 'device_identifier', 'state', 'access_type', 'user_type',
            'is_update', 'payment_state', 'own_state',
        )
        ->first();
}
```

Función que cumple: trae solo las columnas necesarias para validar credenciales y aplicar las reglas (estado, tipo de acceso, estado de pago, etc.). Devuelve `null` si no hay usuario con ese correo.

---

## Paso 2: bifurcación por tipo de acceso — `ensureUserCanAuthenticate()`

```php
private function ensureUserCanAuthenticate(User $user, string $currentDate): void
{
    if ((int) $user->access_type === self::INTERNAL_ACCESS_TYPE) {       // 0
        $this->ensureInternalUserCanAuthenticate($user);
        return;
    }

    if ((int) $user->access_type === self::SUBSCRIBER_ACCESS_TYPE) {     // 1
        $this->loadExternalSubscription($user);
        $this->ensureExternalUserCanAuthenticate($user, $currentDate);
        return;
    }

    $this->denyLogin('No tienes los permisos necesarios para acceder.');
}
```

Función que cumple: según `access_type`, deriva a la validación de usuario interno o externo. Cualquier otro valor se rechaza por seguridad.

---

## Rama A: usuario interno — `ensureInternalUserCanAuthenticate()`

```php
private function ensureInternalUserCanAuthenticate(User $user): void
{
    if (! $this->isEnabledUser($user)) {
        $this->denyLogin('Tu cuenta no está activa.');
    }

    if (! $user->hasAnyRole(self::INTERNAL_ROLE_NAMES)) {
        $this->denyLogin('No tienes los permisos necesarios para acceder.');
    }
}
```

Un usuario interno entra solo si cumple **ambas**:

1. **Cuenta activa** — `state == 1` (`isEnabledUser`).
2. **Rol interno** — tiene al menos uno de los roles de `INTERNAL_ROLE_NAMES` (`hasAnyRole`, vía spatie/laravel-permission).

Los internos **no** pasan por validación de suscripción, pago ni vigencia.

---

## Rama B: usuario externo (suscriptor)

Tiene dos partes: primero se **carga** la suscripción asociada y luego se **valida**.

### B.1 Cargar la suscripción — `loadExternalSubscription()`

```php
private function loadExternalSubscription(User $user): void
{
    $subscription = $this->findSubscriptionFromDependency($user)
        ?? $this->findDirectSubscription($user);

    if (! $subscription) {
        return;
    }

    $user->date_expiry = $subscription->date_expiry;
    $user->temporary_extension_end = $subscription->temporary_extension_end;
    $user->type_subscriptions = $subscription->type_subscriptions;
}
```

Función que cumple: localiza la suscripción que aplica al usuario externo y copia sus fechas y tipo al objeto `$user` para que las validaciones posteriores las puedan leer. La prioridad es:

1. **Como dependiente** (`findSubscriptionFromDependency`): un usuario que accede gracias a la suscripción de un titular (plan con usuarios adicionales), enlazado vía la tabla `user_dependency`.
2. Si no es dependiente, **suscripción propia** (`findDirectSubscription`): el titular que tiene su propio registro en `users_subscriptions`.

Si no encuentra ninguna, no asigna fechas (la validación de vigencia fallará después).

#### Selección de la suscripción correcta

Ambos métodos comparten el mismo criterio de ordenamiento:

```php
->orderByRaw("GREATEST(COALESCE(us.temporary_extension_end, '1000-01-01'), COALESCE(us.date_expiry, '1000-01-01')) DESC")
->orderByDesc('us.id')
->first();
```

Función que cumple, de adentro hacia afuera:

- `COALESCE(columna, '1000-01-01')` — si la fecha es `NULL`, la reemplaza por `1000-01-01`, que es el **valor mínimo válido** del tipo `DATE` en MySQL. Así un `NULL` siempre "pierde".
- `GREATEST(extension_end, date_expiry)` — toma la **mayor** de las dos fechas: la *fecha de vigencia efectiva* de esa suscripción.
- `DESC` — ordena de la vigencia más lejana a la más cercana.
- `orderByDesc('us.id')` — desempate determinista: si dos suscripciones tienen la misma vigencia efectiva, gana la de `id` más alto (la más reciente).
- `first()` — devuelve la suscripción con vigencia más lejana (la más conveniente para el usuario).

### B.2 Validar — `ensureExternalUserCanAuthenticate()`

```php
private function ensureExternalUserCanAuthenticate(User $user, string $currentDate): void
{
    if (! $this->isEnabledUser($user)) {
        $this->denyLogin('Tu cuenta no está activa.');
    }

    if (! $this->isPaidUser($user)) {
        $this->denyLogin('Tu cuenta no está activa.');
    }

    if (! $this->hasValidSubscription($user, $currentDate)) {
        $this->denyLogin('Tu suscripción ha expirado.');
    }

    if ((int) $user->own_state !== 1) {
        $this->denyLogin('Tu suscripción ha expirado.');
    }

    if ($user->type_subscriptions === 'Personal Demo') {
        $this->denyLogin('No tienes los permisos necesarios para acceder.');
    }

    if (! $this->hasOnlySubscriberRole($user)) {
        $this->denyLogin('No tienes los permisos necesarios para acceder.');
    }
}
```

Un externo entra solo si cumple **todas** estas condiciones, en orden:

| # | Condición | Helper | Mensaje si falla |
| --- | --- | --- | --- |
| 1 | Cuenta activa (`state == 1`) | `isEnabledUser` | Tu cuenta no está activa. |
| 2 | Pago habilitado (`payment_state == 1`) | `isPaidUser` | Tu cuenta no está activa. |
| 3 | Suscripción vigente | `hasValidSubscription` | Tu suscripción ha expirado. |
| 4 | `own_state == 1` | — | Tu suscripción ha expirado. |
| 5 | El tipo **no** es `Personal Demo` | — | No tienes los permisos necesarios para acceder. |
| 6 | Tiene **solo** el rol `subscriber` | `hasOnlySubscriberRole` | No tienes los permisos necesarios para acceder. |

---

## Vigencia de la suscripción — `hasValidSubscription()` / `effectiveSubscriptionEnd()`

```php
private function hasValidSubscription(User $user, string $currentDate): bool
{
    $effectiveEnd = $this->effectiveSubscriptionEnd($user);

    return $effectiveEnd !== null && $effectiveEnd >= $currentDate;
}

private function effectiveSubscriptionEnd(User $user): ?string
{
    $dates = array_filter([
        $user->date_expiry,
        $user->temporary_extension_end,
    ]);

    return empty($dates) ? null : max($dates);
}
```

Función que cumple: calcula la **fecha de vigencia efectiva** como el máximo entre `date_expiry` y `temporary_extension_end` (ignorando los `null`), y la compara contra la fecha de hoy. La suscripción está vigente si esa fecha efectiva es **hoy o futura**.

> **Importante:** este criterio (`max` de ambas fechas) es **el mismo** que usa el `ORDER BY ... GREATEST(...)` al seleccionar la suscripción. Mantenerlos alineados evita el caso borde en que se elegía una suscripción por su vigencia más lejana pero luego se la rechazaba mirando solo la prórroga. Las columnas son tipo `date` y no están casteadas a `Carbon`, por lo que llegan como strings `YYYY-MM-DD` y la comparación lexicográfica con `max()` y `>=` es correcta.

---

## Helpers de apoyo

```php
private function isEnabledUser(User $user): bool
{
    return (int) $user->state === 1;
}

private function isPaidUser(User $user): bool
{
    return (int) $user->payment_state === 1;
}

private function hasOnlySubscriberRole(User $user): bool
{
    $user->loadMissing('roles');
    $roleNames = $user->roles->pluck('name');

    return $roleNames->count() === 1 && $roleNames->contains(self::EXTERNAL_ROLE_NAME);
}
```

- `isEnabledUser` — cuenta no desactivada manualmente.
- `isPaidUser` — pago habilitado (solo externos).
- `hasOnlySubscriberRole` — el externo debe tener **exactamente un** rol y que sea `subscriber`. Si tiene roles internos además, se rechaza.

---

## Manejo de rechazos — `denyLogin()`

```php
private function denyLogin(string $message): void
{
    throw ValidationException::withMessages([
        'email' => $message,
    ]);
}
```

Función que cumple: corta el flujo lanzando una `ValidationException` asociada al campo `email`, de modo que el mensaje aparece junto al formulario de login. Todos los rechazos del flujo pasan por aquí.

---

## Registro (logging) de intentos

Todo se registra en el canal `stack`:

- **`Fortify::loginView`** — registra cada visita a `/login` (`Auth page visited: login`).
- **`logAuthenticationOutcome`** — al final del flujo: `Auth login succeeded` (con `user_id`, `user_email`, IP, user agent, referer) cuando hay usuario; `Auth login failed` si no.
- **`logFailedLoginAttempt`** — cuando se lanza una `ValidationException`: registra `Auth login failed` con el motivo extraído del error (`errors['email'][0]`), el correo enviado, IP, etc.

Esto permite auditar tanto los logins exitosos como los rechazados y su razón.

---

## Rate limiting (límite de intentos)

Definidos en `boot()`:

```php
RateLimiter::for('login', function (Request $request) {
    $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
    return Limit::perMinute(5)->by($throttleKey);
});

RateLimiter::for('two-factor', function (Request $request) {
    return Limit::perMinute(5)->by($request->session()->get('login.id'));
});
```

Función que cumple:

- **`login`**: máximo **5 intentos por minuto** por combinación de correo + IP (la clave se normaliza en minúsculas y sin acentos). Mitiga ataques de fuerza bruta.
- **`two-factor`**: máximo **5 intentos por minuto** para la verificación de dos factores, identificados por la sesión del login en curso.

---

## Resumen de reglas por tipo de usuario

| Validación | Interno (`access_type=0`) | Externo (`access_type=1`) |
| --- | :---: | :---: |
| Credenciales correctas (email + hash) | ✅ | ✅ |
| Cuenta activa (`state == 1`) | ✅ | ✅ |
| Rol interno (`hasAnyRole`) | ✅ | — |
| Pago habilitado (`payment_state == 1`) | — | ✅ |
| Suscripción vigente (fecha efectiva ≥ hoy) | — | ✅ |
| `own_state == 1` | — | ✅ |
| Tipo distinto de `Personal Demo` | — | ✅ |
| Solo rol `subscriber` | — | ✅ |

Si **cualquier** regla aplicable falla, el login se rechaza con `denyLogin()` y el intento queda registrado en el log.
