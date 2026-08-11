# Gestión de multimedia de peleadores MMA

## Objetivo

Implementar la vista administrativa para gestionar imágenes y enlaces de video asociados a perfiles de peleadores.

## Alcance funcional

- Ruta administrativa: `/admin/fighter-media`.
- Permiso de acceso: `fighter_media.view`.
- Permisos de operación:
  - `fighter_media.create` para crear piezas multimedia.
  - `fighter_media.update` para editar piezas multimedia.
  - `fighter_media.delete` para eliminar piezas multimedia.
- Cada pieza pertenece a un peleador.
- Se soportan imágenes optimizadas y videos por URL externa.

## Vista

La pantalla mantiene el patrón visual administrativo:

- Filtros superiores por búsqueda, peleador, tipo, estado y cantidad por página.
- Tabla compacta con multimedia, peleador, orden, estado y acciones.
- Última columna con menú de acciones.
- Modal Livewire para creación y edición.
- Modal de confirmación para eliminación.
- Mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Validaciones

- El peleador seleccionado debe existir.
- El tipo de archivo debe ser `image` o `video`.
- Para imágenes se debe cargar un archivo JPG, JPEG, PNG o WebP.
- Para videos se debe informar una URL válida.
- El título y la descripción son opcionales.
- El orden debe ser un entero entre 0 y 9999.
- El estado debe ser `activo` o `inactivo`.

## Imágenes

- Las imágenes se guardan usando el servicio centralizado `ImageUploadOptimizer`.
- Tamaño máximo: 5 MB.
- Si una imagen subida previamente por el sistema se reemplaza o elimina, se borra el archivo anterior del disco.
- Las URLs de video no se descargan ni se procesan; solo se validan y almacenan.

## Rendimiento

La consulta principal usa:

- `with('fighter')` para evitar consultas N+1 al mostrar datos del peleador.
- `whereHas('fighter')` para búsqueda por nombre, apellido, apodo o slug del peleador.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.fighter_media
```

El acceso desde menú usa:

```text
mma.menu.fighters.media
```
