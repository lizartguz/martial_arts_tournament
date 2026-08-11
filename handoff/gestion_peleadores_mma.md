# Gestión administrativa de peleadores

## Alcance inicial

Los peleadores se administran desde:

```text
/admin/fighters
```

La pantalla sigue el patrón operativo del panel:

- filtros superiores;
- tabla de resultados;
- acciones por dropdown;
- modal para crear o editar;
- mensajes mediante eventos Livewire compatibles con SweetAlert2.

## Permisos aplicados

El módulo usa permisos técnicos registrados desde código y seeders:

```text
fighters.view
fighters.create
fighters.update
fighters.delete
```

Los permisos no se crean desde el panel. La administración solo puede asignarlos o retirarlos desde roles.

## Datos gestionados

El formulario inicial permite administrar:

- nombre, apellido y apodo;
- slug;
- género;
- país y ciudad;
- equipo/gimnasio;
- categoría de peso;
- fecha de nacimiento;
- estatura, alcance y guardia;
- biografía;
- récord principal;
- imagen de perfil y portada;
- estado activo/inactivo.

Las estadísticas avanzadas de `fighter_stats` quedan separadas para una siguiente etapa, porque pueden requerir reglas propias por resultado de combate.

## Slug

El slug se genera automáticamente desde el nombre y apellido cuando el usuario no lo modifica.

Si el administrador edita el slug manualmente, se valida:

```text
solo minúsculas, números y guiones medios
```

El slug debe ser único en `fighters` porque servirá como identificador público del perfil del peleador.

## Imágenes

El peleador puede guardar:

- imagen de perfil;
- imagen de portada.

Las imágenes se procesan con `ImageUploadOptimizer` y se guardan en el disco público usando `uploads.public_images`.

Cuando una imagen se reemplaza, el archivo anterior se elimina si pertenece al disco público del sistema.

## Eliminación

La eliminación se bloquea si el peleador tiene dependencias:

- combates como esquina roja;
- combates como esquina azul;
- rankings asociados.

Esto evita romper carteleras o posiciones públicas.

## Rendimiento

La tabla carga relaciones y conteos anticipados:

```text
country
city
team
weightClass
redCornerFights_count
blueCornerFights_count
```

Si se agregan columnas relacionadas, deben resolverse con `with(...)` o `withCount(...)` antes de mostrarlas en Blade.
