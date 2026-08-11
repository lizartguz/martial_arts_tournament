# Gestión del portal del suscriptor MMA

## Alcance implementado

- Se agregó un portal propio para usuarios con rol `subscriber`, separado del panel administrativo.
- Las rutas viven bajo `/subscriber/*`.
- El portal incluye:
  - Inicio.
  - Mis compras.
  - Mis eventos.
  - Mi suscripción.
  - Perfil.
- El layout del suscriptor no muestra menú administrativo, roles, permisos, usuarios, configuración ni reportes globales.

## Rutas y permisos

| Ruta | Permiso |
| --- | --- |
| `/subscriber/dashboard` | `subscriber.dashboard.view` |
| `/subscriber/purchases` | `subscriber.purchases.view` |
| `/subscriber/events` | `subscriber.events.view` |
| `/subscriber/subscription` | `subscriber.subscription.view` |
| `/subscriber/profile` | `subscriber.profile.view` |
| `PATCH /subscriber/profile` | `subscriber.profile.update` |

La ruta `/dashboard` redirige al panel administrativo si el usuario tiene `dashboard.view`; si no lo tiene y cuenta con `subscriber.dashboard.view`, redirige al portal del suscriptor.

## Datos propios

- Compras: se muestran pagos de suscripción y solicitudes de compra vinculadas al `user_id` autenticado.
- Eventos: se muestran eventos publicados asociados a solicitudes de compra del usuario.
- Suscripción: se muestra la suscripción más reciente y el historial del usuario autenticado.
- Perfil: permite actualizar datos básicos propios, sin modificar roles ni permisos.

## Seguridad

- Todas las rutas están bajo autenticación, sesión Jetstream y verificación.
- Cada ruta aplica middleware `can:*`.
- El controlador valida permisos con `Gate::authorize()`.
- El perfil solo actualiza el usuario autenticado.
- Las consultas filtran por `user_id` del usuario autenticado para evitar acceso cruzado a datos de otros suscriptores.

## Rendimiento

- Las vistas cargan relaciones con `with()` para evitar consultas N+1:
  - Pagos con suscripción y plan.
  - Solicitudes con evento y plan.
  - Suscripciones con plan.
  - Eventos con sede.
- Las listas principales usan paginación cuando pueden crecer.

## Idiomas

- Los textos visibles del portal usan claves `mma.subscriber_portal.*`.
- Hay traducciones para español, inglés, portugués, francés, alemán y ruso.
- El español usa tildes y acentos correctamente en etiquetas y mensajes visibles.
