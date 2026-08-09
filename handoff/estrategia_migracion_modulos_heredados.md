# Estrategia de migracion y eliminación de módulos heredados

## Objetivo

Este documento define como retirar del proyecto todo módulo heredado que no aporte al dominio MMA. La decision vigente es eliminar absolutamente todo lo que ya no se necesite, pero hacerlo de forma controlada para no romper autenticacion, permisos, layouts, idiomas, notificaciones reutilizables o infraestructura Laravel.

## Regla principal

Todo archivo, ruta, tabla, permiso, traducción, vista, componente, controlador, modelo, seeder o asset del dominio anterior debe eliminarse cuando no sea necesario para MMA.

La eliminación debe ser verificable:

```text
identificar -> confirmar dependencias -> retirar menú/ruta -> retirar código -> retirar traducciones/assets -> probar
```

## Conservar o adaptar

Piezas que pueden conservarse o adaptarse:

```text
users
auth/Fortify/Jetstream/Sanctum
roles y permisos Spatie
layouts administrativos compatibles
componentes UI reutilizables
selector de idioma
resources/lang actuales
notificaciones push si se reutilizan para eventos
FCM tokens
contacto/soporte si se adapta
optimizacion/carga de imágenes
logs y auditoría si aplican
jobs/cache/sessions
```

Regla de reutilización:

- Si una funcionalidad sirve para varios módulos MMA, debe conservarse o extraerse a un servicio central.
- No duplicar lógica de carga de archivos, optimización de imágenes, generación de slugs, permisos, jerarquía de roles o etiquetas traducidas dentro de cada componente.
- El servicio existente `App\Services\ImageUploadOptimizer` debe considerarse reutilizable para imágenes de comprobantes, eventos, peleadores, banners, logos y multimedia.
- Si un flujo acepta imágenes y PDF, crear una capa superior de archivos que use el optimizador solo para imágenes y guarde PDF validado sin compresión inicial.

## Eliminar

Piezas candidatas a eliminación:

```text
estaciones
sensores
clima
forecast/pronosticos
agro
horas frio
GDD
alertas climaticas
mantenimiento de estaciones
importacion global de datos climaticos
organizacion de usuarios por estaciones
tablas de traslados heredadas
assets de navegacion heredados
traducciones de módulos heredados
permisos heredados del dominio anterior
menus heredados del panel
```

## Orden recomendado

### Paso 1: inventario

Listar:

- Rutas heredadas.
- Items de `config/panel.php`.
- Controladores heredados.
- Componentes Livewire heredados.
- Vistas heredadas.
- Modelos heredados.
- Migraciones/tablas heredadas.
- Seeders heredados.
- Permisos heredados.
- Traducciones heredadas.
- Assets heredados.

### Paso 2: retirar exposicion visual

Primero ocultar o quitar del menú administrativo los módulos heredados que no se usaran.

Esto reduce riesgo de que usuarios entren a pantallas que pronto se eliminaran.

### Paso 3: retirar rutas

Eliminar rutas heredadas o moverlas temporalmente a una zona de revision si aún hay dependencias tecnicas.

Toda ruta eliminada debe tener confirmacion de que no hay enlaces activos en menú, vistas o scripts.

### Paso 4: retirar código

Eliminar controladores, Livewire, vistas, modelos y servicios del dominio anterior cuando ya no tengan referencias.

Antes de borrar:

```text
rg "NombreClase"
rg "ruta.heredada"
rg "permiso.heredado"
composer dump-autoload
php artisan route:list
```

### Paso 5: retirar base de datos

Crear migraciones de eliminación o limpieza solo cuando el código ya no dependa de esas tablas.

Regla:

- No borrar tabla si todavia existe modelo, ruta, reporte, vista o job que la consulta.
- Si hay datos que deban conservarse por auditoría, exportarlos antes.
- Si la tabla pertenece completamente al dominio anterior y no tiene valor para MMA, eliminarla.

### Paso 6: retirar permisos y traducciones

Eliminar permisos heredados de:

```text
permissions
role_has_permissions
model_has_permissions
config/authorization.php
database/seeders
resources/lang/*/messages.php
resources/lang/*/menú.php
```

El rol `subscriber` debe quedar sin permisos administrativos heredados.

### Paso 7: validar

Validaciones minimas:

```text
php artisan route:list
php artisan config:clear
php artisan view:clear
composer dump-autoload
php artisan test
```

Si no hay pruebas suficientes, validar manualmente:

- Login.
- Panel administrativo.
- Menú lateral.
- Selector de idioma.
- Roles/permisos.
- Landing pública.
- Portal suscriptor.

### Paso 8: seeders demo en base local

Usar la base local vacía `artsmartialdb` para validar migraciones y datos ficticios.

Reglas:

- Ejecutar seeders demo solo en entorno local.
- Cargar datos suficientes para revisar landing, filtros, tablas, estados, permisos y modales.
- No depender de datos reales ni de carga manual para validar las primeras pantallas.
- No ejecutar `migrate:fresh --seed` contra bases con datos reales.

## Permisos heredados a reemplazar

Los permisos de estaciones, clima, agro, forecast e importaciones deben reemplazarse por permisos MMA.

Ejemplos:

```text
stations.table.view           -> eliminar
stations.map.view             -> eliminar
agro.chill_hours.view         -> eliminar
agro.gdd.view                 -> eliminar
administration.forecasts_edit -> eliminar
events.view                   -> conservar/crear
fighters.view                 -> conservar/crear
fights.view                   -> conservar/crear
```

## Criterio de finalizacion

La limpieza heredada se considera completa cuando:

```text
[ ] No hay rutas del dominio anterior.
[ ] No hay items de menú del dominio anterior.
[ ] No hay permisos heredados asignados.
[ ] No hay vistas heredadas accesibles.
[ ] No hay Livewire heredado registrado en rutas activas.
[ ] No hay controladores heredados usados.
[ ] No hay traducciones visibles del dominio anterior.
[ ] No hay assets heredados referenciados.
[ ] Las tablas heredadas no necesarias fueron eliminadas.
[ ] La aplicación inicia, compila y permite login.
```

## Relacion con otros documentos

- `handoff/plan_implementacion_mma.md`
- `handoff/propuesta_roles_permisos_mma.md`
- `handoff/propuesta_visualizacion_modulos_mma.md`
