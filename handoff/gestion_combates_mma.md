# Gestión administrativa de combates

## Alcance inicial

Los combates se administran desde:

```text
/admin/fights
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
fights.view
fights.create
fights.update
fights.delete
```

Los resultados oficiales se gestionan en un módulo separado con:

```text
fight_results.view
fight_results.update
```

## Datos gestionados

El formulario inicial permite administrar:

- evento;
- categoría de peso;
- esquina roja;
- esquina azul;
- título opcional;
- tipo de combate;
- cantidad de rounds;
- orden en cartelera;
- fecha y hora específica del combate;
- estado;
- si es combate estelar;
- si es destacado;
- notas internas;
- imagen promocional.

## Validaciones

La esquina roja y la esquina azul deben ser peleadores distintos.

El combate debe pertenecer a un evento. La categoría de peso puede quedar vacía si aún no está definida, pero se recomienda completarla antes de publicar la cartelera.

## Estados

Estados vigentes:

| Valor | Estado |
|---:|---|
| 0 | Programado |
| 1 | En vivo |
| 2 | Finalizado |
| 3 | Cancelado |

## Tipos de combate

Tipos vigentes:

```text
regular
main_event
co_main_event
title_fight
exhibition
```

## Imagen promocional

La imagen promocional se procesa con `ImageUploadOptimizer` y se guarda en el disco público usando `uploads.public_images`.

Cuando la imagen se reemplaza, el archivo anterior se elimina si pertenece al disco público del sistema.

## Eliminación

La eliminación se bloquea si el combate tiene un resultado oficial registrado en `fight_results`.

Esto protege la trazabilidad de resultados, rankings futuros y reportes.

## Rendimiento

La tabla carga relaciones y conteos anticipados:

```text
event
weightClass
cornerRed
cornerBlue
result_count
```

Si se agregan columnas relacionadas, deben resolverse con `with(...)` o `withCount(...)` antes de mostrarlas en Blade.
