# Gestión de logs MMA

## Alcance implementado

- Se agregó el módulo administrativo `/admin/logs`.
- La pantalla muestra entradas recientes de `storage/logs/laravel.log`.
- El visor permite:
  - Buscar por mensaje, contexto o traza.
  - Filtrar por nivel.
  - Filtrar por rango de fechas.
  - Cambiar cantidad por página.
  - Ver detalle de una entrada en modal.
  - Descargar el archivo completo si el usuario tiene permiso.

## Permisos

| Acción | Permiso |
| --- | --- |
| Ver logs | `logs.view` |
| Descargar log | `logs.download` |

El permiso `logs.download` no se asigna por defecto a todos los administradores. Debe reservarse para `super_manager` o usuarios puntuales autorizados.

## Seguridad

- La ruta `/admin/logs` usa `can:logs.view`.
- La ruta `/admin/logs/download` usa `can:logs.download`.
- El controlador y el componente Livewire vuelven a validar permisos.
- No se permite borrar entradas ni limpiar el archivo desde esta primera versión.

## Rendimiento

- El servicio lee las últimas líneas del log usando `LogReaderService::DEFAULT_MAX_LINES`.
- El filtrado y paginado se aplican sobre las entradas recientes ya parseadas.
- Esta primera versión evita cargar logs históricos completos demasiado grandes.

## Idiomas

- Los textos visibles usan `mma.admin.logs.*`.
- Hay traducciones para español, inglés, portugués, francés, alemán y ruso.
