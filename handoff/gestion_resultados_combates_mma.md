# Gestión de resultados de combates MMA

## Objetivo

El módulo de resultados permite registrar y actualizar el resultado oficial de un combate ya programado. La gestión se realiza desde `/admin/fight-results` y trabaja sobre los registros de `fights`, mostrando combates con y sin resultado para que el operador pueda completar la información oficial desde una misma vista.

## Permisos

```text
fight_results.view
fight_results.update
```

- `fight_results.view`: permite acceder al listado de resultados.
- `fight_results.update`: permite abrir el modal y registrar o actualizar el resultado oficial.
- No existe permiso de creación o eliminación separado para resultados. El resultado se crea o actualiza desde la acción de gestión del combate.

La ruta aplica middleware `can:fight_results.view`, el controlador vuelve a validar el acceso con `Gate::authorize('fight_results.view')` y el componente Livewire valida `fight_results.update` antes de guardar.

## Flujo de vista

La vista mantiene el patrón operativo definido para los módulos administrativos:

- filtros superiores por búsqueda, evento, tipo de resultado, estado de registro y cantidad por página;
- tabla compacta con combate, evento, resultado, ganador, round/tiempo y acciones;
- menú de acciones en la última columna;
- modal de edición con validaciones de campo;
- mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Reglas de negocio

- Cada combate puede tener un solo resultado oficial (`fight_results.fight_id` es único).
- La tabla lista combates con resultado y sin resultado para facilitar el registro pendiente.
- Los tipos `draw` y `no_contest` no requieren ganador y limpian `winner_fighter_id`.
- Los tipos `ko_tko`, `submission`, `decision` y `disqualification` requieren ganador.
- El ganador debe pertenecer a la esquina roja o azul del combate.
- El round informado no puede superar los rounds configurados para el combate.
- El tiempo se registra en formato `M:SS` o `MM:SS`.
- Al guardar un resultado oficial, el combate queda marcado operativamente como finalizado (`fights.status = 2`).

## Rendimiento

El listado debe evitar consultas N+1. La consulta principal carga de forma anticipada:

```text
event
cornerRed
cornerBlue
result
result.winner
```

Los filtros usan `whereHas` para buscar por evento, peleadores y método sin recorrer colecciones en memoria.

## Traducciones

Todos los textos visibles deben usar claves de traducción bajo:

```text
mma.admin.fight_results
```

Las claves están disponibles en español, inglés, portugués, francés, alemán y ruso. El español es el idioma por defecto y debe conservar ortografía completa con tildes y acentos.

## Relación con rankings y récords

El registro de resultados es la base para los módulos posteriores de rankings y actualización de récords. En esta etapa solo se guarda el resultado oficial; la sincronización automática de récords o rankings debe implementarse después mediante una regla centralizada para evitar duplicar cálculos en varias vistas.
