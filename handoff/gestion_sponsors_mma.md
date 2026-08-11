# Gestión de sponsors MMA

## Objetivo

El módulo de sponsors permite administrar marcas patrocinadoras y aliados comerciales desde `/admin/sponsors`. Su información se reutilizará en eventos, landing pública y futuras piezas comerciales.

## Acceso y permisos

Ruta administrativa:

```text
/admin/sponsors
```

Permisos técnicos:

```text
sponsors.view
sponsors.create
sponsors.update
sponsors.delete
```

La ruta debe estar protegida por `can:sponsors.view` y cada acción del componente Livewire debe validar su permiso específico. Esto evita que un usuario fuerce acciones desde la URL o desde llamadas directas al componente.

## Flujo visual

La vista mantiene el patrón operativo del panel:

- Filtros superiores por búsqueda, evento asociado, estado y cantidad por página.
- Tabla con logo, nombre, slug, web, correo, cantidad de eventos, orden, estado y columna final de acciones.
- Menú de acciones por fila para editar o eliminar.
- Modal para crear/editar con validaciones por campo y selección múltiple de eventos asociados.
- Modal de confirmación para eliminación.
- Mensajes de éxito o error mediante los eventos SweetAlert2 ya usados por el panel.

## Reglas de datos

- `name` es obligatorio y tiene máximo 180 caracteres.
- `slug` se genera automáticamente desde el nombre cuando el usuario no lo modifica manualmente.
- `slug` puede editarse manualmente, pero debe cumplir el formato `^[a-z0-9]+(?:-[a-z0-9]+)*$` y ser único.
- `website_url` es opcional, pero si se informa debe ser una URL válida.
- `contact_email` es opcional, pero si se informa debe ser un correo válido.
- `display_order` controla el orden de aparición.
- `status` permite activar o desactivar el sponsor sin eliminarlo.
- Los eventos asociados se guardan en `event_sponsor`. Al editar, el sistema conserva los valores existentes de `placement` y `display_order` del pivote para no perder configuraciones futuras.

## Imágenes

El logo acepta `jpg`, `jpeg`, `png` y `webp`, con tamaño máximo de 5 MB. Al guardarse debe pasar por el servicio centralizado `ImageUploadOptimizer`, usando el almacenamiento público configurado para imágenes optimizadas.

La eliminación de un sponsor también debe eliminar su logo anterior cuando aplique.

## Restricciones de eliminación

Un sponsor no debe eliminarse si tiene eventos asociados en `event_sponsor`. En ese caso el sistema muestra un mensaje de error y conserva el registro.

## Rendimiento

La tabla debe usar:

- `withCount('events')` para mostrar la cantidad de eventos sin generar consultas N+1.
- `whereHas('events')` para filtrar por evento.
- Selección acotada de eventos (`id`, `name`, `starts_at`) para el selector de filtro.

## Internacionalización

Todas las etiquetas, filtros, columnas, mensajes y validaciones visibles deben resolverse mediante `resources/lang/{locale}/mma.php`. Español es el idioma por defecto, pero el módulo debe mantenerse traducido en todos los idiomas activos del sistema: `es`, `en`, `pt`, `fr`, `de` y `ru`.
