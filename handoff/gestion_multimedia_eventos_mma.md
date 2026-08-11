# Gestión de multimedia de eventos MMA

## Objetivo

El módulo de multimedia de eventos permite administrar imágenes y enlaces de video asociados a eventos desde `/admin/event-media`. Sirve para galerías, pesajes, careos, backstage, highlights, piezas de sponsors y material visual público.

## Permisos

```text
event_media.view
event_media.create
event_media.update
event_media.delete
```

- `event_media.view`: permite acceder al listado multimedia.
- `event_media.create`: permite registrar nuevas piezas.
- `event_media.update`: permite editar metadatos, estado, orden, destacado y archivo/URL.
- `event_media.delete`: permite eliminar la pieza y, si corresponde, el archivo público asociado.

La ruta usa `can:event_media.view`, el controlador valida con `Gate::authorize('event_media.view')` y el componente Livewire valida `event_media.view` en `mount()` y permisos específicos antes de crear, editar o eliminar.

## Flujo de vista

La vista sigue el patrón administrativo uniforme:

- filtros superiores por búsqueda, evento, tipo, categoría, estado y cantidad por página;
- tabla con previsualización, evento, categoría, orden, estado y acciones;
- menú de acciones en la última columna;
- modal de creación/edición con validaciones por campo;
- confirmación modal antes de eliminar;
- mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Reglas de archivo

- Las imágenes se suben desde el panel y se procesan con `App\Services\ImageUploadOptimizer`.
- Las imágenes se guardan en el disco público usando `uploads.public_images`.
- Los formatos permitidos para imagen son JPG, JPEG, PNG y WebP, con máximo 5 MB.
- Los videos se registran como URL externa en `file_path`; no se suben videos pesados al servidor en esta etapa.
- Al reemplazar o eliminar una imagen almacenada bajo `storage/`, el archivo anterior se borra del disco público.
- Al cambiar una pieza de imagen a video, se elimina la imagen pública anterior si la URL nueva reemplaza el archivo local.

## Categorías

Las categorías operativas iniciales son:

```text
gallery
weigh_in
faceoff
backstage
highlight
sponsor
other
```

Estas categorías son valores técnicos controlados por código y traducidos en la vista. No se crean desde el panel.

## Rendimiento

El listado evita consultas N+1 cargando anticipadamente:

```text
event
```

Los filtros usan `whereHas` para buscar por nombre del evento y campos propios de la pieza multimedia.

## Traducciones

Todos los textos visibles usan claves bajo:

```text
mma.admin.event_media
```

Las claves están disponibles en español, inglés, portugués, francés, alemán y ruso. El español conserva tildes y acentos como idioma por defecto.
