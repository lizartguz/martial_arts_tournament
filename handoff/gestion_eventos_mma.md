# Gestión administrativa de eventos

## Alcance inicial

Los eventos se administran desde:

```text
/admin/events
```

La pantalla mantiene el patrón operativo definido para los módulos del panel:

- filtros superiores;
- tabla con resumen del evento;
- acciones por dropdown;
- modal para crear o editar;
- mensajes mediante eventos Livewire compatibles con SweetAlert2.

## Permisos aplicados

El módulo usa permisos técnicos registrados desde código y seeders:

```text
events.view
events.create
events.update
events.delete
events.publish
```

Un usuario con `events.update` puede editar información general del evento, pero publicar requiere `events.publish`. Si se intenta guardar un evento con estado publicado sin ese permiso, la autorización debe rechazar la acción.

## Slug

El slug se genera automáticamente desde el nombre cuando el usuario no lo modifica.

Si el administrador edita el slug manualmente, se valida el formato:

```text
solo minúsculas, números y guiones medios
```

El slug debe ser único en la tabla `events` porque se usa como identificador público en la URL:

```text
/eventos/{slug}
```

## Estados

Estados vigentes:

| Valor | Estado |
|---:|---|
| 0 | Borrador |
| 1 | Publicado |
| 2 | Archivado |
| 3 | Cancelado |

La landing y API pública solo muestran eventos publicados.

## Imágenes

El evento puede guardar:

- póster;
- banner.

Las imágenes se procesan con el servicio reutilizable `ImageUploadOptimizer` y se guardan en el disco público usando la configuración `uploads.public_images`.

El flujo debe eliminar la imagen anterior cuando se reemplaza por una nueva imagen optimizada.

## Eliminación

La eliminación se bloquea cuando el evento tiene dependencias activas:

- combates;
- enlaces de tickets;
- solicitudes de compra.

Esto evita romper la cartelera, ventas manuales o trazabilidad de solicitudes asociadas.

## Rendimiento

La tabla carga relaciones y conteos de forma anticipada:

```text
venue
fights_count
ticket_links_count
```

Si se agregan más columnas relacionadas, deben incorporarse con `with(...)` o `withCount(...)` antes de imprimirlas en la vista.
