# Gestión de rankings MMA

## Objetivo

El módulo de rankings permite administrar posiciones oficiales de peleadores por categoría de peso y género desde `/admin/rankings`. La primera etapa mantiene la gestión manual para que administración pueda publicar o ajustar posiciones sin depender todavía de una automatización completa de récords.

## Permisos

```text
rankings.view
rankings.update
```

- `rankings.view`: permite acceder al listado de rankings.
- `rankings.update`: permite crear, editar, activar o inactivar posiciones.
- No se define permiso `rankings.delete`; la baja operativa se maneja cambiando el estado a inactivo.

La ruta usa `can:rankings.view`, el controlador valida con `Gate::authorize('rankings.view')` y el componente Livewire valida `rankings.view` en `mount()` y `rankings.update` antes de modificar datos.

## Flujo de vista

La vista sigue el patrón administrativo común:

- filtros superiores por búsqueda, categoría, género, estado y cantidad por página;
- tabla con posición, peleador, categoría, récord, movimiento, estado y acciones;
- menú de acciones en la última columna;
- modal de creación/edición con validaciones por campo;
- mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Reglas de negocio

- El ranking se organiza por `weight_class_id` y `gender`.
- Un peleador solo puede aparecer una vez en la misma combinación de categoría y género.
- El peleador seleccionado debe coincidir con el género del ranking.
- El peleador seleccionado debe pertenecer a la categoría de peso del ranking.
- No se permite duplicar una posición activa dentro de la misma categoría y género.
- Para retirar a un peleador del ranking se cambia `status` a inactivo.
- `is_champion` marca visualmente al campeón; la posición sigue siendo un número entero positivo.
- `previous_position` permite mostrar movimiento simple: sube, baja o sin cambio.

## Rendimiento

El listado evita consultas N+1 cargando anticipadamente:

```text
weightClass
fighter
fighter.team
```

Los filtros de búsqueda usan `whereHas` para buscar por datos del peleador o nombre de categoría sin recorrer colecciones en memoria.

## Relación con resultados

Los resultados oficiales son la fuente futura para automatizar récords y rankings. En esta etapa la vista de rankings no recalcula posiciones automáticamente. Cuando se implemente la sincronización, debe centralizarse en un servicio de dominio para evitar duplicar lógica entre resultados, rankings, reportes y API pública.

## Traducciones

Todos los textos visibles usan claves bajo:

```text
mma.admin.rankings
```

Las claves están disponibles en español, inglés, portugués, francés, alemán y ruso. El español conserva tildes y acentos como idioma por defecto.
