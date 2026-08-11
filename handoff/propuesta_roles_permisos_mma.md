# Propuesta de roles y permisos MMA

## Objetivo

Este documento propone una base inicial de roles, permisos y restricciones de acceso para la plataforma MMA. La propuesta sirve como punto de partida para implementar la autorización con Spatie Permission, filtrar el menú lateral y proteger rutas, componentes, acciones Livewire y APIs.

La matriz puede ajustarse durante la implementación. El criterio principal es partir de permisos claros por módulo, asignar permisos por defecto a cada rol y permitir permisos puntuales solo cuando no rompan la jerarquia de seguridad.

## Principios de autorización

- El menú lateral solo mejora la experiencia de usuario; no debe considerarse una barrera de seguridad.
- Cada ruta sensible debe tener middleware `can` o `permission`.
- Cada componente Livewire sensible debe validar permisos al cargar y antes de ejecutar acciones.
- Las acciones sobre modelos deben validar permiso y alcance de datos, por ejemplo jerarquia de usuario, recurso propio o compra propia.
- Los permisos criticos deben quedar protegidos para evitar que un usuario sin jerarquia suficiente los asigne o modifique.
- El rol `subscriber` debe usar una experiencia cliente separada del panel administrativo.
- Las APIs deben validar usuario autenticado, cuenta activa, suscripción vigente y permiso funcional cuando corresponda.
- La creacion de roles y permisos no debe estar disponible desde el panel. El catalogo se define por código, seeders, migraciones o scripts controlados por el programador.
- El panel solo debe permitir asignar o quitar roles y permisos existentes, respetando la jerarquia del usuario que realiza el cambio.

## Roles base sugeridos

| Rol Spatie | Enfoque |
| --- | --- |
| `super_manager` | Administracion técnica/global. Puede ver y administrar todo el sistema. |
| `admin` | Administrador de la promotora. Gestiona la operacion completa permitida dentro de la instalación. |
| `manager` | Operacion diaria. Gestiona eventos, peleadores, suscriptores, pagos y reportes operativos. |
| `publisher` | Publicación y contenido. Gestiona noticias, multimedia, landing, sponsors y publicación de eventos. |
| `sales` | Gestión comercial. Atiende suscriptores, pagos, renovaciones, compras y soporte comercial. |
| `checkin` | Validación de ingreso. Revisa eventos y confirma tickets/check-ins. |
| `support` | Soporte al usuario. Puede consultar datos y actualizar solicitudes de soporte limitadas. |
| `subscriber` | Cliente final. Solo ve su cuenta, compras, eventos comprados, suscripción y perfil. |

## Jerarquia para gestión de usuarios y roles

La gestión de usuarios debe respetar una regla de jerarquia estricta. Tener permisos como `users.view`, `users.update`, `users.delete` o `roles.update` no autoriza por si solo a modificar cualquier usuario del sistema.

Reglas sugeridas:

- `super_manager` funciona como rol root y puede visualizar, modificar, eliminar y reasignar roles de cualquier usuario, incluyendo otros administradores.
- Un usuario con rol `admin` puede visualizar usuarios con el mismo nivel de rol, pero no puede editar, cambiar roles, desactivar ni eliminar a otro `admin`.
- Un usuario con rol `admin` puede modificar, cambiar roles o eliminar usuarios con roles inferiores, siempre que también tenga el permiso especifico requerido.
- Los roles operativos solo pueden gestionar usuarios de menor jerarquia si el flujo lo permite expresamente.
- Ningun usuario puede elevarse a si mismo, quitarse restricciones criticas ni asignarse permisos protegidos.
- Ningun usuario puede modificar a otro usuario con rango igual o superior, excepto `super_manager`.

Ejemplo de rangos:

```text
super_manager 100
admin          80
manager        60
publisher      50
sales          40
checkin        30
support        30
subscriber     10
```

Comportamiento esperado:

| Actor | Usuario objetivo | Puede ver | Puede modificar/eliminar |
| --- | --- | --- | --- |
| `super_manager` | cualquier rol | Si | Si |
| `admin` | `admin` | Si | No |
| `admin` | `manager`, `publisher`, `sales`, `checkin`, `support`, `subscriber` | Si | Si, con permiso y alcance valido |
| `manager` | `admin` | Según permiso | No |
| `manager` | `manager` | Según permiso | No |
| `manager` | roles inferiores | Según permiso | Solo si el flujo lo autoriza |
| `subscriber` | cualquier otro usuario | No | No |

## Roles heredados

El sistema actual conserva roles heredados como `meteorology`, `meteorologist`, `technical` y `marketing`. Durante la transicion MMA conviene decidir si:

- se migran a los nuevos roles sugeridos;
- se mantienen temporalmente por compatibilidad;
- se eliminan cuando ya no existan módulos agro/meteorologicos activos.

El rol `subscriber` requiere revision especial porque actualmente puede tener permisos heredados del panel. En la version MMA debe quedar limitado a permisos `subscriber.*`.

## Convencion de nombres

Los permisos deberian seguir el formato:

```text
módulo.acción
```

Acciones base:

```text
view
create
update
delete
publish
export
confirm
cancel
block
resolve
send
download
access
```

Ejemplo:

```text
events.view
events.create
events.update
events.delete
events.publish
```

## Catalogo administrado por código

Los roles y permisos son parte del contrato técnico de la aplicación. No se crean desde el panel administrativo porque cada nuevo permiso requiere código asociado: rutas, middleware, validaciones Livewire, policies, pruebas y textos traducidos.

Flujo permitido:

```text
programador define permiso -> seeder/migracion lo registra -> código lo valida -> panel permite asignarlo si corresponde
```

Flujo no permitido:

```text
administrador crea permiso desde panel -> permiso queda sin validaciones reales
```

Reglas sugeridas:

- La tabla `permissions` conserva nombres tecnicos como `events.view`.
- La tabla `roles` conserva nombres tecnicos como `admin`, `manager` o `subscriber`.
- Los administradores no pueden crear, renombrar ni eliminar permisos.
- Los administradores no pueden crear permisos funcionales nuevos desde formularios.
- La gestión desde panel se limita a asignar o quitar roles existentes a usuarios, y asignar o quitar permisos existentes a roles autorizados.
- Los permisos disponibles para asignacion se filtran por jerarquia, alcance y permisos protegidos.
- Despues de agregar un permiso por código, también deben agregarse sus traducciones visibles.

## Nombres visibles y traducciones

Los nombres tecnicos no deben mostrarse como texto principal al usuario final. La interfaz debe resolver una etiqueta traducida según el idioma activo de la aplicación.

Ejemplos:

| Valor técnico | Etiqueta visible sugerida |
| --- | --- |
| `events.view` | Ver eventos |
| `events.create` | Crear eventos |
| `events.publish` | Publicar eventos |
| `subscription_payments.upload_proof` | Cargar comprobante de pago |
| `subscription_payments.confirm` | Confirmar pagos de suscripción |
| `purchase_requests.view` | Ver solicitudes de compra |
| `subscriber.purchases.view` | Ver mis compras |

Fuente recomendada:

```text
resources/lang/{locale}/authorization.php
```

Estructura sugerida:

```php
return [
    'roles' => [
        'admin' => 'Administrador',
        'manager' => 'Gestor operativo',
        'publisher' => 'Editor de contenido',
        'subscriber' => 'Suscriptor',
    ],

    'permissions' => [
        'events.view' => [
            'label' => 'Ver eventos',
            'description' => 'Permite consultar eventos dentro del panel.',
        ],
        'events.publish' => [
            'label' => 'Publicar eventos',
            'description' => 'Permite hacer visible un evento al público.',
        ],
    ],
];
```

Regla de fallback:

- Si existe traducción, mostrar `label` y opcionalmente `description`.
- Si no existe traducción, mostrar una version legible generada desde el nombre técnico.
- El nombre técnico puede mostrarse solo como ayuda secundaria para usuarios tecnicos autorizados, no como etiqueta principal.

## Catalogo inicial de permisos

### Dashboard

```text
dashboard.view
```

### Organizacion

```text
system_settings.view
system_settings.update
venues.view
venues.create
venues.update
venues.delete
```

### Eventos y combates

```text
events.view
events.create
events.update
events.delete
events.publish
fights.view
fights.create
fights.update
fights.delete
fight_results.view
fight_results.update
rankings.view
rankings.update
```

### Peleadores

```text
fighters.view
fighters.create
fighters.update
fighters.delete
fighter_teams.view
fighter_teams.create
fighter_teams.update
fighter_teams.delete
weight_classes.view
weight_classes.create
weight_classes.update
weight_classes.delete
```

### Multimedia

```text
event_media.view
event_media.create
event_media.update
event_media.delete
fighter_media.view
fighter_media.create
fighter_media.update
fighter_media.delete
```

### Contenido público

```text
news.view
news.create
news.update
news.delete
news.publish
landing.update
sponsors.view
sponsors.create
sponsors.update
sponsors.delete
```

### Suscripciones y pagos

```text
subscription_plans.view
subscription_plans.create
subscription_plans.update
subscription_plans.delete
subscribers.view
subscribers.update
user_subscriptions.view
user_subscriptions.create
user_subscriptions.update
subscription_payments.view
subscription_payments.upload_proof
subscription_payments.confirm
subscription_payments.cancel
purchase_requests.view
purchase_requests.update
purchase_requests.assign
purchase_requests.close
purchase_requests.delete
```

### Tickets

```text
ticket_links.view
ticket_links.create
ticket_links.update
ticket_links.delete
```

Los permisos para venta interna y check-in (`ticket_orders.*`, `ticket_checkins.*`) no forman parte del catálogo vigente. Deben agregarse junto con las rutas, pantallas y tablas del módulo correspondiente.

### Notificaciones

```text
notifications.view
notifications.create
notifications.send
notifications.delete
fcm_tokens.view
```

### Usuarios y acceso

```text
users.view
users.create
users.update
users.delete
roles.view
roles.create
roles.update
roles.delete
permissions.view
permissions.create
permissions.update
permissions.delete
authorization.access
devices.view
devices.block
account_deletions.view
account_deletions.resolve
```

### Reportes

```text
reports.events.view
reports.subscriptions.view
reports.sales.view
reports.app.view
reports.export
```

### Configuración

```text
settings.view
settings.update
app_versions.view
app_versions.update
logs.view
logs.download
```

### Soporte

```text
support_messages.view
support_messages.update
```

### Portal del suscriptor

```text
subscriber.dashboard.view
subscriber.purchases.view
subscriber.events.view
subscriber.subscription.view
subscriber.profile.view
subscriber.profile.update
```

## Permisos por defecto por rol

### `super_manager`

Debe recibir todos los permisos disponibles.

### `admin`

Permisos sugeridos:

```text
dashboard.view
system_settings.view
system_settings.update
venues.view
venues.create
venues.update
venues.delete
events.view
events.create
events.update
events.delete
events.publish
fights.view
fights.create
fights.update
fights.delete
fight_results.view
fight_results.update
rankings.view
rankings.update
fighters.view
fighters.create
fighters.update
fighters.delete
fighter_teams.view
fighter_teams.create
fighter_teams.update
fighter_teams.delete
weight_classes.view
weight_classes.create
weight_classes.update
weight_classes.delete
event_media.view
event_media.create
event_media.update
event_media.delete
fighter_media.view
fighter_media.create
fighter_media.update
fighter_media.delete
news.view
news.create
news.update
news.delete
news.publish
landing.update
sponsors.view
sponsors.create
sponsors.update
sponsors.delete
subscription_plans.view
subscription_plans.create
subscription_plans.update
subscription_plans.delete
subscribers.view
subscribers.update
user_subscriptions.view
user_subscriptions.create
user_subscriptions.update
subscription_payments.view
subscription_payments.upload_proof
subscription_payments.confirm
subscription_payments.cancel
purchase_requests.view
purchase_requests.update
purchase_requests.assign
purchase_requests.close
ticket_links.view
ticket_links.create
ticket_links.update
ticket_links.delete
notifications.view
notifications.create
notifications.send
notifications.delete
fcm_tokens.view
users.view
users.create
users.update
devices.view
devices.block
account_deletions.view
account_deletions.resolve
reports.events.view
reports.subscriptions.view
reports.sales.view
reports.app.view
reports.export
settings.view
settings.update
app_versions.view
app_versions.update
support_messages.view
support_messages.update
```

Restriccion sugerida: no asignar por defecto `roles.*`, `permissions.*`, `authorization.access`, `logs.download` ni `users.delete` si el administrador no debe modificar seguridad global.

### `manager`

Permisos sugeridos:

```text
dashboard.view
system_settings.view
venues.view
venues.create
venues.update
events.view
events.create
events.update
fights.view
fights.create
fights.update
fight_results.view
fight_results.update
rankings.view
rankings.update
fighters.view
fighters.create
fighters.update
fighter_teams.view
fighter_teams.create
fighter_teams.update
weight_classes.view
event_media.view
event_media.create
event_media.update
fighter_media.view
fighter_media.create
fighter_media.update
subscribers.view
subscribers.update
user_subscriptions.view
user_subscriptions.update
subscription_payments.view
subscription_payments.upload_proof
subscription_payments.confirm
purchase_requests.view
purchase_requests.update
purchase_requests.assign
purchase_requests.close
ticket_links.view
ticket_links.create
ticket_links.update
notifications.view
reports.events.view
reports.subscriptions.view
reports.sales.view
support_messages.view
support_messages.update
```

### `publisher`

Permisos sugeridos:

```text
dashboard.view
events.view
events.update
events.publish
fights.view
fighters.view
event_media.view
event_media.create
event_media.update
event_media.delete
fighter_media.view
fighter_media.create
fighter_media.update
fighter_media.delete
news.view
news.create
news.update
news.delete
news.publish
landing.update
sponsors.view
sponsors.create
sponsors.update
sponsors.delete
ticket_links.view
ticket_links.create
ticket_links.update
notifications.view
notifications.create
notifications.send
```

### `sales`

Permisos sugeridos:

```text
dashboard.view
events.view
ticket_links.view
subscription_plans.view
subscribers.view
subscribers.update
user_subscriptions.view
user_subscriptions.create
user_subscriptions.update
subscription_payments.view
subscription_payments.upload_proof
subscription_payments.confirm
subscription_payments.cancel
purchase_requests.view
purchase_requests.update
purchase_requests.close
reports.subscriptions.view
reports.sales.view
reports.export
support_messages.view
support_messages.update
```

### `checkin`

Permisos sugeridos:

```text
dashboard.view
events.view
```

### `support`

Permisos sugeridos:

```text
dashboard.view
events.view
subscribers.view
user_subscriptions.view
subscription_payments.view
support_messages.view
support_messages.update
```

### `subscriber`

Permisos sugeridos:

```text
subscriber.dashboard.view
subscriber.purchases.view
subscriber.events.view
subscriber.subscription.view
subscriber.profile.view
subscriber.profile.update
```

El suscriptor no debe recibir permisos administrativos como `users.view`, `subscribers.view`, `events.update`, `ticket_links.update` o `subscription_payments.view`.

## Permisos asignables manualmente

Estos permisos pueden asignarse a usuarios puntuales cuando el flujo operativo lo justifique:

```text
events.publish
reports.export
notifications.send
subscription_payments.confirm
subscription_payments.upload_proof
subscription_payments.cancel
purchase_requests.assign
purchase_requests.close
logs.view
```

La asignacion puntual debe respetar la jerarquia del actor que concede el permiso.

## Gestión visual de roles

La interfaz administrativa no debe exponer una pantalla libre para listar, crear o editar permisos como entidades independientes. La experiencia recomendada es gestionar permisos desde el detalle de cada rol.

El mismo mecanismo aplica para todos los roles. Al abrir cualquier rol, el sistema debe cargar el catalogo completo de permisos existentes y marcar como seleccionados los permisos que ese rol ya tiene asignados, incluyendo los permisos configurados por defecto desde el seeder. Los permisos no seleccionados quedan disponibles para agregarse si la jerarquia y las reglas de seguridad lo permiten.

Flujo sugerido:

1. El administrador entra a Gestión de roles.
2. El sistema muestra los roles que el administrador puede ver o gestionar.
3. El administrador selecciona un rol, por ejemplo `admin`.
4. El sistema despliega todos los permisos existentes agrupados por módulo.
5. Cada permiso aparece con checkbox, etiqueta traducida y descripción breve.
6. Los permisos que el rol ya tiene aparecen seleccionados.
7. Los permisos que el rol no tiene aparecen sin seleccionar.
8. Los permisos asignables pueden marcarse para agregarse o desmarcarse para quitarse.
9. Los permisos protegidos o fuera de jerarquia aparecen deshabilitados u ocultos, según la politica de seguridad definida.
10. Al guardar, el backend vuelve a validar que el actor puede modificar ese rol y esos permisos.
11. El guardado sincroniza los permisos autorizados del rol; no crea permisos nuevos.

Ejemplo visual conceptual:

```text
Rol: Administrador

Eventos
[x] Ver eventos
[x] Crear eventos
[x] Editar eventos
[x] Publicar eventos
[ ] Eliminar eventos

Suscripciones y pagos
[x] Ver suscriptores
[x] Ver pagos
[ ] Confirmar pagos

Seguridad
[-] Gestionar permisos       bloqueado por jerarquia
[-] Eliminar usuarios        bloqueado por jerarquia
```

La pantalla debe guardar usando los nombres tecnicos, pero renderizar usando traducciones. Por ejemplo, el checkbox muestra `Ver eventos`, pero el valor enviado y validado sigue siendo `events.view`.

La matriz de permisos por defecto por rol funciona como estado inicial. Despues, un rol puede quedar con permisos agregados o quitados desde esta pantalla, siempre que esos permisos ya existan en el catalogo técnico y el usuario que guarda el cambio tenga autorización para hacerlo.

## Permisos protegidos

Estos permisos deben quedar protegidos contra asignacion, edicion o eliminación por usuarios sin jerarquia global:

```text
authorization.access
roles.view
roles.create
roles.update
roles.delete
permissions.view
permissions.create
permissions.update
permissions.delete
users.delete
system_settings.update
settings.update
logs.download
```

Regla sugerida:

- `super_manager` puede gestionar permisos protegidos.
- `admin` puede gestionar roles y usuarios internos solo si no modifica permisos protegidos ni usuarios de rango igual o superior.
- Ningun rol operativo debe poder asignarse permisos que no posee o que superan su jerarquia.
- Ningun rol puede modificar usuarios o roles de rango igual o superior, excepto `super_manager`.

## Restricciones contra acceso forzado por URL

### Rutas web

Cada ruta debe declarar el permiso requerido:

```php
Route::get('/admin/events', [EventController::class, 'index'])
    ->middleware('can:events.view')
    ->name('admin.events.index');

Route::post('/admin/events', [EventController::class, 'store'])
    ->middleware('can:events.create')
    ->name('admin.events.store');

Route::put('/admin/events/{event}', [EventController::class, 'update'])
    ->middleware('can:events.update')
    ->name('admin.events.update');
```

Para el módulo de autorización se puede mantener `permission:authorization.access` si se prefiere usar el middleware de Spatie directamente.

### Componentes Livewire

Cada componente debe validar al montar y antes de ejecutar acciones sensibles:

```php
public function mount(): void
{
    abort_unless(auth()->user()?->can('events.view'), 403);
}

public function save(): void
{
    abort_unless(auth()->user()?->can('events.update'), 403);

    // Guardar cambios.
}
```

### Policies y alcance de datos

Los permisos de acción deben combinarse con reglas de propiedad:

```php
public function update(User $user, Event $event): bool
{
    return $user->can('events.update');
}
```

Para gestión de usuarios, las policies deben validar permiso, alcance y jerarquia:

```php
public function update(User $actor, User $target): bool
{
    return $actor->can('users.update')
        && $this->sameOrganization($actor, $target)
        && $this->canManageTargetUser($actor, $target);
}

private function canManageTargetUser(User $actor, User $target): bool
{
    if ($actor->hasRole('super_manager')) {
        return true;
    }

    return $this->highestRoleRank($actor) > $this->highestRoleRank($target);
}
```

La comparacion debe ser estrictamente mayor (`>`), no mayor o igual (`>=`). Asi, un `admin` no puede modificar a otro `admin`, pero si puede gestionar usuarios de rangos inferiores cuando también cumple permiso y alcance.

Para suscriptores, la validación debe asegurar que solo consulten compras, eventos y suscripciones propias:

```php
public function viewPurchase(User $user, Purchase $purchase): bool
{
    return $user->can('subscriber.purchases.view')
        && $purchase->user_id === $user->id;
}
```

### Separacion de panel administrativo y portal cliente

Rutas sugeridas:

```text
/admin/*       panel administrativo interno
/subscriber/*  portal del suscriptor
/account/*     perfil, seguridad y datos propios
```

Regla sugerida:

- `/admin/*` requiere usuario autenticado, rol interno y permiso especifico.
- `/subscriber/*` requiere usuario autenticado, rol `subscriber`, cuenta activa y suscripción valida cuando aplique.
- Si un `subscriber` intenta entrar a `/admin/*`, debe recibir `403` o redireccion controlada a su inicio cliente.

### APIs

Las APIs deben validar:

- token o sesión autenticada;
- usuario activo;
- suscripción vigente si es usuario externo;
- permiso funcional cuando la acción no sea pública;
- propiedad del recurso consultado o modificado.

No se debe confiar en que la aplicación web o móvil oculte botones.

### Auditoría

Conviene registrar intentos denegados en acciones criticas:

```text
authorization.access
roles.update
permissions.update
users.delete
events.publish
subscription_payments.confirm
subscription_payments.upload_proof
purchase_requests.close
settings.update
logs.download
```

El registro debe incluir usuario, rol, permiso requerido, ruta, método HTTP, recurso objetivo si existe y fecha/hora.

## Recomendaciones para implementación

1. Crear o actualizar el seeder de roles y permisos con esta matriz inicial.
2. Agregar traducciones visibles para cada rol y permiso base.
3. Actualizar `config/authorization.php` con roles, jerarquia y permisos protegidos.
4. Ajustar `config/panel.php` para que cada item del menú use permisos MMA.
5. Separar las rutas administrativas de las rutas del suscriptor.
6. Agregar middleware por permiso en rutas.
7. Agregar validaciones `abort_unless` en Livewire.
8. Crear policies para modelos con jerarquia, propiedad o reglas de visibilidad cuando corresponda.
9. Implementar una validación de jerarquia para que solo `super_manager` gestione usuarios de igual o mayor rango.
10. Limpiar permisos heredados del rol `subscriber`.
11. Implementar Gestión de roles como una pantalla de checkboxes sobre permisos existentes, no como creador libre de permisos.
12. Agregar pruebas para confirmar que un usuario sin permiso recibe `403` y que un usuario no puede modificar otro de rango igual o superior.

## Primera etapa sugerida

Para una primera version funcional, priorizar:

```text
dashboard.view
events.*
fights.*
fighters.*
event_media.*
subscription_plans.*
subscribers.*
user_subscriptions.*
subscription_payments.*
purchase_requests.*
ticket_links.*
notifications.*
users.view
users.create
users.update
roles.view
roles.update
permissions.view
authorization.access
subscriber.*
```

Los permisos de tickets internos, check-in, reportes avanzados, logs, eliminación de cuentas y configuración global pueden quedar documentados pero ocultos hasta que existan pantallas y flujos definidos.
