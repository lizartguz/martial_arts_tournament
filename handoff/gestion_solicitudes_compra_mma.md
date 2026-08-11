# Gestión administrativa de solicitudes de compra

## Alcance inicial

Las solicitudes creadas desde la landing pública o desde la API se gestionan desde el panel administrativo en:

```text
/admin/purchase-requests
```

La pantalla debe mantener el mismo patrón visual del resto de módulos administrativos:

- filtros superiores;
- tabla con información resumida;
- columna final de acciones mediante dropdown;
- modal para gestionar la solicitud;
- mensajes de éxito o error con SweetAlert2 mediante los eventos Livewire existentes.

## Permisos aplicados

La vista y sus acciones se protegen con permisos técnicos:

```text
purchase_requests.view
purchase_requests.update
purchase_requests.assign
purchase_requests.close
purchase_requests.delete
```

Estos permisos no se crean desde el panel. Se registran desde código y seeders, y luego administración solo podrá asignarlos o retirarlos desde la gestión de roles.

## Flujo de gestión

Cada solicitud puede filtrarse por búsqueda, estado, tipo de solicitud, canal preferido, responsable y rango de fechas.

Desde la tabla se podrá:

- gestionar la solicitud en un modal;
- asignarla al usuario autenticado;
- cerrarla;
- eliminarla si el rol tiene permiso.

El modal permite modificar:

- estado;
- responsable asignado;
- notas internas.

Cuando una solicitud pasa a un estado atendido, cerrado, convertido o rechazado, se registran `handled_by` y `handled_at`.

## Comprobantes privados

Los comprobantes de pago se guardan en disco privado y no se exponen por URL pública directa.

El panel administrativo usa una ruta protegida:

```text
/admin/purchase-requests/{purchaseRequest}/proof
```

Esta ruta requiere sesión administrativa y permiso `purchase_requests.view`. Si el archivo no existe o la solicitud no tiene comprobante, la respuesta será `404`.

## Rendimiento

La tabla debe evitar consultas N+1. Por ello, la consulta del componente carga relaciones con `with(...)`:

```text
event
plan
assignedUser
user
```

Cuando se agreguen nuevas columnas relacionadas, se debe ampliar esta carga anticipada antes de imprimir información adicional en la tabla.

## Traducciones

Todos los labels, estados, acciones, mensajes, filtros y textos visibles deben usar archivos de idioma. La aplicación mantiene español como idioma por defecto, pero la vista debe funcionar con todos los idiomas activos.
