# Gestión de sedes MMA

## Objetivo

Implementar la vista administrativa para crear, editar, filtrar y eliminar sedes físicas donde se realizan los eventos.

## Alcance funcional

- Ruta administrativa: `/admin/venues`.
- Permiso de acceso: `venues.view`.
- Permisos de operación:
  - `venues.create` para crear sedes.
  - `venues.update` para editar sedes.
  - `venues.delete` para eliminar sedes.
- Cada sede puede relacionarse con una ciudad.
- Cada sede puede tener dirección, coordenadas, capacidad, contacto e imagen.
- Las sedes son usadas por eventos, por lo que no deben eliminarse cuando existan eventos asociados.

## Vista

La pantalla mantiene el patrón visual administrativo:

- Filtros superiores por búsqueda, ciudad, estado y cantidad por página.
- Tabla compacta con columnas de sede, ubicación, capacidad, contacto, eventos, estado y acciones.
- Última columna con menú de acciones.
- Modal Livewire para creación y edición.
- Modal de confirmación para eliminación.
- Mensajes de éxito o bloqueo mediante eventos Livewire compatibles con SweetAlert2.

## Validaciones

- El nombre de la sede es obligatorio.
- El slug se genera automáticamente desde el nombre cuando el usuario no lo modifica manualmente.
- El slug puede editarse manualmente, pero debe respetar el formato técnico permitido.
- La ciudad es opcional, pero si se informa debe existir.
- Latitud y longitud son opcionales, pero deben estar dentro de rangos válidos.
- La capacidad es opcional y debe ser un número entero mayor o igual a cero.
- El estado debe ser `activo` o `inactivo`.
- La imagen debe ser JPG, JPEG, PNG o WebP y no superar 5 MB.

## Imágenes

- Las imágenes se guardan usando el servicio centralizado `ImageUploadOptimizer`.
- Se almacenan bajo la carpeta pública configurada para imágenes MMA.
- Si una imagen subida previamente por el sistema se reemplaza, se elimina el archivo anterior.
- Las imágenes heredadas usadas por seeders se conservan como fallback y no se eliminan automáticamente.

## Rendimiento

La consulta principal usa:

- `with(['city', 'city.country'])` para evitar consultas N+1 al mostrar ciudad y país.
- `withCount('events')` para mostrar dependencias sin cargar toda la relación.

## Restricciones

- Un usuario sin `venues.view` no debe acceder a la pantalla aunque fuerce la URL.
- Crear, editar o eliminar valida permisos desde el componente Livewire.
- Una sede con eventos asociados no puede eliminarse.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.venues
```

El acceso desde menú usa:

```text
mma.menu.events.venues
```
