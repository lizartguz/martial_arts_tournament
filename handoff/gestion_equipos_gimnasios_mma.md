# Gestión administrativa de equipos y gimnasios

## Alcance inicial

Los equipos, academias, clubes o gimnasios se administran desde:

```text
/admin/fighter-teams
```

La vista sigue el patrón operativo del panel:

- filtros superiores;
- tabla de resultados;
- acciones por dropdown;
- modal para crear o editar;
- mensajes mediante eventos Livewire compatibles con SweetAlert2.

## Permisos aplicados

El módulo usa permisos técnicos registrados desde código y seeders:

```text
fighter_teams.view
fighter_teams.create
fighter_teams.update
fighter_teams.delete
```

Los permisos no se crean desde el panel. La administración solo puede asignarlos o retirarlos desde roles.

## Datos gestionados

El formulario inicial permite administrar:

- nombre;
- slug;
- ciudad;
- coach principal;
- teléfono de contacto;
- descripción;
- logo;
- estado activo/inactivo.

## Slug

El slug se genera automáticamente desde el nombre cuando el usuario no lo modifica.

Si el administrador edita el slug manualmente, se valida:

```text
solo minúsculas, números y guiones medios
```

El slug debe ser único en `fighter_teams`.

## Logo

El logo se procesa con `ImageUploadOptimizer` y se guarda en el disco público usando `uploads.public_images`.

Cuando el logo se reemplaza, el archivo anterior se elimina si pertenece al disco público del sistema.

## Eliminación

La eliminación se bloquea si el equipo tiene peleadores asociados.

Esto evita dejar peleadores con relaciones de equipo inconsistentes y protege la información pública de perfiles.

## Rendimiento

La tabla carga relaciones y conteos anticipados:

```text
city
city.country
fighters_count
```

Si se agregan columnas relacionadas, deben resolverse con `with(...)` o `withCount(...)` antes de mostrarlas en Blade.
