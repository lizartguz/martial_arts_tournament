# Gestión de enlaces de tickets MMA

## Objetivo

Implementar la vista administrativa para crear, editar, filtrar y eliminar enlaces externos de venta o contacto asociados a eventos.

## Alcance funcional

- Ruta administrativa: `/admin/ticket-links`.
- Permiso de acceso: `ticket_links.view`.
- Permisos de operación:
  - `ticket_links.create` para crear enlaces.
  - `ticket_links.update` para editar enlaces.
  - `ticket_links.delete` para eliminar enlaces.
- Cada enlace pertenece a un evento.
- Los enlaces pueden representar plataformas externas, WhatsApp, teléfono, streaming, VIP u otros canales.
- La venta interna de tickets, órdenes y validación QR no forman parte de esta etapa.

## Vista

La pantalla mantiene el patrón visual administrativo:

- Filtros superiores por búsqueda, evento, canal, estado y cantidad por página.
- Tabla compacta con columnas de enlace, evento, canal, precio desde, ventana de venta, estado y acciones.
- Última columna con menú de acciones.
- Modal Livewire para creación y edición.
- Modal de confirmación para eliminación.
- Mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Validaciones

- El evento seleccionado debe existir.
- El proveedor, la etiqueta visible y la URL son obligatorios.
- La URL debe tener formato válido.
- El canal de venta debe pertenecer al catálogo permitido.
- El precio desde es opcional, pero si se informa debe ser mayor o igual a cero.
- La fecha final de venta debe ser posterior o igual a la fecha inicial.
- El estado debe ser `activo` o `inactivo`.

## Catálogo de canales

Valores técnicos iniciales:

- `external`.
- `whatsapp`.
- `phone`.
- `streaming`.
- `vip`.
- `other`.

Estos valores pueden mostrarse con etiquetas traducidas mediante `mma.admin.ticket_links.sale_channels`.

## Rendimiento

La consulta principal usa `with('event')` para evitar consultas N+1 al listar enlaces con datos del evento.

Los filtros de búsqueda por evento usan `whereHas()` sobre la relación `event`.

## Relación pública

Los enlaces activos pueden ser consumidos por la landing pública y la API pública de eventos para mostrar canales de compra o contacto.

La visibilidad pública depende de:

- Estado del evento.
- Estado del enlace.
- Ventana de venta, cuando se use en el consumo público.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.ticket_links
```

Los labels, filtros, columnas, canales, mensajes y validaciones deben mantenerse traducidos en todos los idiomas activos.

## Restricciones

- Un usuario sin `ticket_links.view` no debe acceder a la pantalla aunque fuerce la URL.
- Crear, editar o eliminar debe validar permisos desde ruta y componente Livewire.
- El módulo no crea órdenes internas ni entradas digitales.
