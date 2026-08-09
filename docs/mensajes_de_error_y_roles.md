# Mensajes de error y configuración de roles

Documento actualizado: 29 de junio de 2026.

## Objetivo

Este documento explica cómo el proyecto Environmental decide **qué mensaje de error se muestra al usuario** cuando ocurre una excepción inesperada en el panel web (Livewire).

La regla es simple:

- Los **roles privilegiados** (por defecto solo `super_manager`) ven el **detalle técnico crudo** de la excepción (por ejemplo `SQLSTATE[42S22]: Column not found...`), útil para depurar.
- **Cualquier otro rol** ve un **mensaje amigable y genérico**, sin filtrar detalles internos del sistema.

Toda la lógica está centralizada en una sola clase y los roles se ajustan en un único archivo de configuración.

---

## Componentes

| Pieza | Archivo | Rol |
| --- | --- | --- |
| Clase centralizadora | `app/Support/ErrorMessage.php` | Decide qué texto mostrar según el rol del usuario autenticado. |
| Configuración de roles | `config/authorization.php` → clave `error_detail_roles` | Lista de roles que SÍ ven el detalle técnico crudo. |
| Traducción genérica | `resources/lang/{es,en,pt,fr,de,ru}/messages.php` → clave `errors.unexpected` | Mensaje amigable por defecto, en los 6 idiomas. |

---

## La clase `App\Support\ErrorMessage`

Expone dos métodos estáticos:

```php
use App\Support\ErrorMessage;

// Devuelve el string a mostrar al usuario.
ErrorMessage::forUser(\Throwable|string $error, ?string $friendly = null): string

// Devuelve true si el usuario autenticado puede ver el detalle tecnico crudo.
ErrorMessage::userSeesTechnicalDetail(): bool
```

### `forUser($error, $friendly = null)`

| Parámetro | Tipo | Descripción |
| --- | --- | --- |
| `$error` | `Throwable` o `string` | La excepción capturada (o un mensaje ya extraído). Solo se muestra a roles privilegiados. |
| `$friendly` | `string` (opcional) | Mensaje amigable para el resto de roles. Si se omite, usa `messages.errors.unexpected`. |

Comportamiento:

- Si el usuario tiene un rol en `error_detail_roles` → devuelve el mensaje crudo de la excepción.
- En caso contrario → devuelve `$friendly`, o el mensaje genérico si no se pasó.
- Si no hay usuario autenticado → se trata como no privilegiado (mensaje amigable).

### `userSeesTechnicalDetail()`

Úsalo cuando el mensaje no es un simple string (por ejemplo, cuando el detalle se incrusta dentro de una plantilla de traducción con un placeholder `:error`). Permite decidir manualmente qué construir.

---

## Configuración de roles

Único lugar a modificar para cambiar quién ve el detalle técnico:

```php
// config/authorization.php
'error_detail_roles' => [
    'super_manager',
],
```

Para que **otro rol** también vea el detalle crudo, basta con agregarlo a este array. Por ejemplo, para que `admin` también lo vea:

```php
'error_detail_roles' => [
    'super_manager',
    'admin',
],
```

> Nota: esta clave es independiente de `super_roles` (que controla la ocultación de roles superiores). Se mantienen separadas a propósito para no mezclar conceptos.

Si en el servidor se usa caché de configuración, tras cambiar este archivo hay que ejecutar:

```bash
php artisan config:cache   # o, en desarrollo: php artisan config:clear
```

---

## Cómo usarla en un `catch`

### Caso típico (mensaje simple)

```php
use App\Support\ErrorMessage;

try {
    // ... lógica ...
} catch (\Throwable $th) {
    DB::rollBack();
    $this->dispatch('failedAlert', [
        'failed' => ErrorMessage::forUser($th, __('messages.operation_errors.station_update')),
    ]);
}
```

- `super_manager` ve el error real.
- El resto ve el mensaje del flujo (ej. *"No se pudo actualizar la estación. Inténtelo nuevamente."*).

También funciona con `session()->flash('error', ...)`:

```php
session()->flash('error', ErrorMessage::forUser($th, __('messages.operation_errors.subscription_save')));
```

> **Importante:** el segundo parámetro debe ser un **mensaje específico del flujo/funcionalidad**, no genérico. Cada `catch` describe qué operación falló (registrar estación, guardar calibración, etc.). Solo se omite el parámetro (cayendo a `messages.errors.unexpected`) cuando no aplica un mensaje contextual.

### Catálogo de mensajes por flujo: `messages.operation_errors`

Los mensajes amigables específicos viven centralizados en `messages.operation_errors` (en los 6 idiomas), una clave por operación. Así se reutilizan y se mantienen en un solo lugar:

| Clave | Flujo |
| --- | --- |
| `station_create` / `station_update` / `station_delete` | Registrar / actualizar / eliminar estación |
| `maintenance_create` / `maintenance_update` | Registrar / actualizar plan de mantenimiento |
| `calibration_save` / `calibration_update` | Guardar / actualizar calibración |
| `subscription_save` | Guardar suscripción |
| `notice_validate` / `notice_save` | Validar / guardar aviso |

Al agregar un flujo nuevo, añade su clave en `resources/lang/<idioma>/messages.php → operation_errors` (6 idiomas) y pásala con `__('messages.operation_errors.<clave>')`.

### Caso avanzado (detalle dentro de una plantilla de traducción)

Cuando el mensaje técnico se incrusta en una cadena con placeholder, usa `userSeesTechnicalDetail()`:

```php
$this->dispatch('failedAlert', ['failed' => ErrorMessage::userSeesTechnicalDetail()
    ? __('messages.notices.alerts.save_failed', ['error' => $th->getMessage()])
    : __('messages.operation_errors.notice_save')]);
```

### Mensajes de validación de negocio

Los mensajes de **validación de negocio** (código de estación duplicado, nombre repetido, campos obligatorios, una `InvalidArgumentException` de dominio, etc.) **no** deben pasar por `ErrorMessage`: son informativos y deben verse para **todos** los roles. `ErrorMessage` es solo para **errores técnicos inesperados** atrapados en un `catch`.

---

## Alcance y exclusiones

La regla de roles aplica **solo a mensajes visibles en el panel web (Livewire)**.

**NO** se debe envolver con `ErrorMessage` (siempre conservan el detalle crudo):

- Registros de log: `Log::error(...)`, `$th->getFile()`, `$th->getLine()`, `$th->getTraceAsString()`, escritura a archivos de log.
- Controladores de API (`app/Http/Controllers/Api/**`) — audiencia distinta (app móvil).
- Comandos de consola (`app/Console/Commands/**`), Jobs (`app/Jobs/**`), Imports (`app/Imports/**`), Services (`app/Services/**`) — no hay usuario autenticado del panel.

---

## Sitios ya migrados

| Archivo | Notas |
| --- | --- |
| `app/Livewire/Station/Station.php` | 7 `catch` de agregar/modificar/eliminar estación, plan de mantenimiento y calibraciones. |
| `app/Livewire/UsersSubscriptions/UsersSubscriptions.php` | Cambio de vinculación y registro de suscripción. |
| `app/Livewire/UserManagement/UserManagement.php` | Cambio de vinculación. |
| `app/Livewire/Subscriptions/Subscriptions.php` | Actualización de suscripción. |
| `app/Livewire/ForecastsEdit/ForecastsEdit.php` | Recolección de pronósticos (conserva prefijo de contexto para rol técnico). |
| `app/Livewire/NotificationsPush/NotificationsPush.php` | Validar y guardar aviso. Conserva el mensaje de `InvalidArgumentException` para todos. |

---

## Mensaje genérico (traducción)

La clave `errors.unexpected` existe en los 6 idiomas. Para personalizar el texto por defecto, edítala en cada archivo `resources/lang/<idioma>/messages.php`:

```php
'errors' => [
    'unexpected' => 'Ocurrió un error al procesar la solicitud. Por favor, inténtelo nuevamente.',
    // ...
],
```

---

## Verificación rápida

```bash
# 1. La config carga los roles correctos
php artisan config:clear
php artisan tinker --execute="print_r(config('authorization.error_detail_roles'));"
# Debe imprimir: Array ( [0] => super_manager )
```

Para probar la lógica con usuarios reales (rol privilegiado vs. otro rol), iniciar sesión con cada uno y forzar un error en un guardado: `super_manager` debe ver el detalle técnico y el resto el mensaje amigable.
