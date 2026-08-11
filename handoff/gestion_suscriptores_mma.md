# Gestión de suscriptores MMA

## Objetivo

El módulo de suscriptores permite revisar y actualizar datos básicos de usuarios con rol `subscriber` desde `/admin/subscribers`. Sirve como vista operativa de clientes finales antes de gestionar suscripciones, pagos o solicitudes.

## Acceso y permisos

Ruta administrativa:

```text
/admin/subscribers
```

Permisos técnicos:

```text
subscribers.view
subscribers.update
```

La ruta debe validar `can:subscribers.view` y la actualización debe validar `subscribers.update`.

## Alcance

Este módulo no crea roles, no asigna permisos y no cambia el rol del usuario. Los permisos y roles siguen siendo gestionados por código y por el módulo de roles autorizado.

Campos editables:

- Nombre.
- Apellido.
- Correo.
- Teléfono.
- Documento de identidad.
- Estado de cuenta (`state`).

## Flujo visual

La vista mantiene el patrón operativo del panel:

- Filtros superiores por búsqueda, estado de suscripción, estado de cuenta y cantidad por página.
- Tabla con suscriptor, contacto, última suscripción, actividad, estado y acciones.
- Menú de acciones por fila para editar.
- Modal de actualización con validaciones por campo.
- Mensajes SweetAlert2 para confirmación de guardado.

## Reglas de datos

- Solo se listan usuarios con rol `subscriber`.
- `email` debe ser único en `users`.
- `state` controla si la cuenta está activa o inactiva.
- Las suscripciones y pagos no se modifican desde este módulo; tendrán módulos propios.
- Un administrador no debe poder convertir un suscriptor en otro rol desde esta pantalla.

## Rendimiento

La tabla debe evitar N+1 usando:

- `with('latestSubscription.plan')` para mostrar la suscripción más reciente y su plan.
- `withCount('userSubscriptions')` para conteo de suscripciones.
- `withCount('subscriptionPayments')` para conteo de pagos.
- `withCount('purchaseRequests')` para conteo de solicitudes.

## Internacionalización

Todas las etiquetas, filtros, columnas, estados, mensajes y textos de ayuda deben resolverse mediante `resources/lang/{locale}/mma.php`. Español es el idioma por defecto, pero el módulo debe mantenerse traducido en `es`, `en`, `pt`, `fr`, `de` y `ru`.
