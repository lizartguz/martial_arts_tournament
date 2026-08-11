# Gestión de planes de suscripción MMA

## Objetivo

El módulo de planes de suscripción permite administrar los paquetes comerciales que se ofrecerán a suscriptores desde `/admin/subscription-plans`. Cada plan define nombre, slug, precio, moneda, periodo, duración, descuento, orden, estado y beneficios visibles.

## Acceso y permisos

Ruta administrativa:

```text
/admin/subscription-plans
```

Permisos técnicos:

```text
subscription_plans.view
subscription_plans.create
subscription_plans.update
subscription_plans.delete
```

La ruta debe validar `can:subscription_plans.view` y cada acción Livewire debe validar su permiso específico. Esto evita accesos forzados por URL o acciones directas no autorizadas.

## Flujo visual

La vista mantiene el patrón operativo del panel:

- Filtros superiores por búsqueda, periodo, estado y cantidad por página.
- Tabla con plan, precio, periodo, uso, orden, estado y columna final de acciones.
- Menú de acciones por fila para editar o eliminar.
- Modal para crear/editar datos del plan y beneficios.
- Modal de confirmación para eliminar.
- Mensajes SweetAlert2 para éxito o errores.

## Reglas de datos

- `name` es obligatorio y tiene máximo 120 caracteres.
- `slug` se genera automáticamente desde el nombre si el usuario no lo modifica.
- `slug` puede editarse manualmente, pero debe cumplir `^[a-z0-9]+(?:-[a-z0-9]+)*$` y ser único.
- `price` es obligatorio, decimal y no puede ser negativo.
- `currency` se limita inicialmente a `BOB` y `USD`.
- `billing_period` se limita a `monthly`, `quarterly`, `yearly`, `one_time` y `lifetime`.
- `duration_days` es opcional, pero si se informa debe ser un entero entre 1 y 3650.
- `discount_percentage` permite valores entre 0 y 100.
- `status` permite activar o desactivar el plan sin eliminarlo.

## Beneficios

Los beneficios se guardan en `subscription_plan_features`. El formulario permite agregar múltiples filas con nombre, descripción, clave técnica, valor, orden y estado.

Solo se guardan beneficios con nombre. Los beneficios activos también se sincronizan en el campo JSON `features` de `subscription_plans` para facilitar consumo en landing, API o vistas públicas.

## Restricciones de eliminación

Un plan no debe eliminarse si tiene:

- Suscripciones asociadas en `user_subscriptions`.
- Solicitudes de compra asociadas en `purchase_requests`.

En esos casos el sistema muestra un error y conserva el registro.

## Rendimiento

La tabla debe usar:

- `withCount('planFeatures')` para mostrar beneficios sin N+1.
- `withCount('userSubscriptions')` y `withCount('purchaseRequests')` para uso y validación de eliminación.
- Orden por `display_order` y luego `price`.

## Internacionalización

Todas las etiquetas, filtros, columnas, periodos, mensajes y validaciones visibles deben resolverse mediante `resources/lang/{locale}/mma.php`. Español es el idioma por defecto, pero el módulo debe mantenerse traducido en `es`, `en`, `pt`, `fr`, `de` y `ru`.
