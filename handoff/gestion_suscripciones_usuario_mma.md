# Gestión de suscripciones de usuario MMA

## Objetivo

Implementar la vista administrativa para crear, editar, filtrar y cancelar suscripciones asignadas a usuarios con rol `subscriber`.

## Alcance funcional

- Ruta administrativa: `/admin/user-subscriptions`.
- Permiso de acceso: `user_subscriptions.view`.
- Permisos de operación:
  - `user_subscriptions.create` para crear suscripciones manuales.
  - `user_subscriptions.update` para editar datos y cancelar una suscripción.
- La cancelación no elimina el registro: cambia el estado a `cancelled` y registra `cancelled_at`.
- El módulo no procesa pagos automáticos ni pasarelas; solo administra la relación manual entre suscriptor y plan.
- Los pagos asociados se muestran como conteo para contexto operativo.

## Vista

La pantalla mantiene el patrón visual de los módulos administrativos:

- Filtros superiores por búsqueda, plan, estado, fecha inicial, fecha final y cantidad por página.
- Tabla compacta con columnas de suscriptor, plan, periodo, estado, pagos, origen y acciones.
- Última columna con menú de acciones.
- Modal Livewire para creación y edición.
- Modal de confirmación para cancelación.
- Mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Validaciones

- El usuario seleccionado debe existir y tener rol `subscriber`.
- El plan seleccionado debe existir.
- `starts_at` es obligatorio.
- `ends_at`, `trial_ends_at` y `renewal_at`, cuando existan, deben ser posteriores o iguales a `starts_at`.
- El estado debe pertenecer al catálogo técnico:
  - `0`: pendiente.
  - `1`: activa.
  - `2`: vencida.
  - `3`: cancelada.
  - `4`: suspendida.
- El origen debe pertenecer al catálogo técnico:
  - `manual`.
  - `admin`.
  - `purchase_request`.
  - `import`.
  - `other`.

## Rendimiento

La consulta principal carga relaciones necesarias con `with()` y conteos con `withCount()` para evitar consultas N+1:

- `user`.
- `plan`.
- `payments_count`.

Los filtros por suscriptor y plan usan `whereHas()` sobre relaciones ya definidas en modelos Eloquent.

## Traducciones

Todos los textos visibles del módulo se definen en `resources/lang/{locale}/mma.php` bajo la clave:

```text
mma.admin.user_subscriptions
```

Los labels, botones, estados, mensajes y validaciones deben mantenerse traducidos en todos los idiomas activos.

## Restricciones

- Un usuario sin `user_subscriptions.view` no debe acceder a la ruta aunque fuerce la URL.
- Las acciones de creación, edición y cancelación deben validarse también desde el componente Livewire con `Gate::authorize()`.
- No se permite crear roles ni permisos desde el panel; este módulo solo consume permisos ya registrados por código y base de datos.
