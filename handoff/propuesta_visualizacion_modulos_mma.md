# Propuesta de visualizacion y gestión de módulos MMA

## Objetivo

Este documento define una convención inicial para implementar las vistas administrativas y de gestión de la plataforma MMA. La idea es que todos los módulos mantengan el mismo flujo, estructura de archivos, estilo visual, validaciones, mensajes y criterios de rendimiento.

El patrón base de implementación es:

```text
ruta web -> controlador administrativo delgado -> vista Blade wrapper -> componente Livewire -> tabla, filtros, modales y acciones
```

Este patrón debe adaptarse al layout actual `resources/views/layouts/admin.blade.php`, a los componentes Tailwind existentes y a las reglas de permisos documentadas en `handoff/propuesta_roles_permisos_mma.md`.

## Principios generales

- Todas las vistas de gestión deben compartir estructura, color, espaciado, botones, tablas, modales y mensajes.
- El primer nivel visible de cada módulo debe ser la experiencia de trabajo, no una pagina explicativa.
- Cada módulo debe tener filtros superiores, tabla principal, paginación y una ultima columna de acciones.
- Las acciones por fila deben ir dentro de un dropdown o menú compacto.
- Crear, editar y acciones complejas deben abrir modal.
- Las validaciones deben mostrarse junto al campo correspondiente dentro del modal.
- Los mensajes de exito, advertencia, error o confirmacion deben mostrarse con SweetAlert 2.
- Los textos visibles deben resolverse por traducciones con `__('...')`.
- Las rutas, controladores, componentes y acciones deben validar permisos.
- Las consultas deben evitar N+1 usando eager loading, conteos agregados y paginación.

## Estructura de archivos por módulo

Para cada módulo administrativo se recomienda esta estructura:

```text
routes/web.php
app/Http/Controllers/Admin/{Module}Controller.php
app/Livewire/Admin/{Module}/{Module}Table.php
resources/views/admin/{module}/index.blade.php
resources/views/livewire/admin/{module}/{module}-table.blade.php
resources/js/livewire/admin/{module}.js
resources/lang/{locale}/messages.php
```

Ejemplo para eventos:

```text
app/Http/Controllers/Admin/EventController.php
app/Livewire/Admin/Events/EventTable.php
resources/views/admin/events/index.blade.php
resources/views/livewire/admin/events/event-table.blade.php
resources/js/livewire/admin/events.js
```

Regla:

- El controlador devuelve la vista y valida el permiso base del módulo.
- La vista Blade solo carga título, encabezado y componente Livewire.
- El componente Livewire contiene estado, filtros, reglas, acciones, consulta y render.
- El Blade Livewire contiene filtros, tabla, dropdown de acciones y modales.
- El JS del módulo solo coordina eventos del navegador, Bootstrap/Tailwind modal si aplica, SweetAlert y detalles que no pertenecen al backend.

## Rutas

Las rutas administrativas deben agruparse por prefijo y permiso:

```php
Route::prefix('admin/events')
    ->name('admin.events.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/', [EventController::class, 'index'])
            ->middleware('can:events.view')
            ->name('index');
    });
```

Las acciones CRUD que se ejecutan dentro de Livewire también deben validar permisos dentro del componente. El middleware de ruta solo protege la entrada inicial.

## Slugs y URLs visibles

Los módulos con presencia pública o enlaces compartibles deben usar `slug` en la URL visible. El `id` puede seguir existiendo como clave interna de base de datos, pero no debe ser la primera opción para URLs públicas.

Ejemplos:

```text
/eventos/mma-night-santa-cruz-2026
/peleadores/juan-el-tigre-perez
/noticias/pesaje-oficial-del-evento
```

Reglas para vistas públicas:

- Usar `slug` en rutas públicas de eventos, peleadores, sedes, noticias y páginas similares.
- Resolver el registro por `slug` mediante route model binding o consulta controlada.
- Validar que el registro esté publicado, activo y dentro del alcance visible antes de mostrarlo.
- No mostrar contenido privado solo porque el `slug` exista.
- Si el registro no está publicado o el usuario no tiene acceso, devolver `404` o `403` según el caso.

Ejemplo:

```php
Route::get('/eventos/{event:slug}', [PublicEventController::class, 'show'])
    ->name('public.events.show');
```

Reglas para vistas administrativas:

- Las rutas administrativas pueden usar `id` o `uuid` internamente si facilita seguridad y mantenimiento.
- En tablas administrativas se puede mostrar el `slug` como dato secundario o usarlo en una acción "Ver página pública".
- Si el módulo permite editar `slug`, debe hacerse con validación de unicidad y advertencia cuando el registro esté publicado.
- No regenerar automáticamente el `slug` al editar el nombre de un registro publicado, para no romper URLs compartidas.

## Controladores

Los controladores administrativos deben ser delgados. Su responsabilidad principal es autorizar la entrada y devolver la vista.

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        $this->authorize('events.view');

        return view('admin.events.index');
    }
}
```

No se recomienda colocar consultas de tabla, filtros ni lógica de modales en el controlador si la pantalla se gestiona con Livewire.

## Vistas wrapper

Cada vista wrapper debe usar `layouts.admin`:

```blade
@extends('layouts.admin')

@section('title', __('messages.events.page_title'))

@section('content_header')
    <h5>{{ __('messages.events.page_title') }}</h5>
@stop

@section('content')
    <livewire:admin.events.event-table />
@stop
```

Reglas:

- No duplicar headers grandes dentro del componente si ya existe `content_header`.
- No envolver todo el componente en cards decorativas anidadas.
- Mantener el contenido compacto y operativo.

## Layout visual estandar

Cada módulo debe seguir este orden:

```text
1. Barra superior de filtros y acciones
2. Resumen minimo opcional
3. Tabla principal
4. Paginación
5. Modales
6. Scripts especificos del módulo
```

### Filtros superiores

La seccion de filtros debe quedar arriba de la tabla. En escritorio/tablet debe mantenerse en una sola fila siempre que el espacio lo permita.

Elementos comunes:

```text
busqueda textual
selector de estado
selector de categoría/tipo
rango de fechas
selector perPage
boton limpiar filtros
boton nuevo registro
```

Reglas visuales:

- Usar columnas estables y comparables.
- Alinear labels, inputs, selects y botones por la parte inferior.
- Evitar que un filtro ocupe toda la fila si no es necesario.
- En móvil se permite apilar.
- En escritorio, usar grillas tipo `grid-cols-*` o `minmax(0, 1fr)`.
- Los botones deben tener icono FontAwesome cuando exista.

Ejemplo conceptual:

```blade
<div class="grid gap-3 md:grid-cols-6">
    <div class="md:col-span-2">
        <label class="tw-label">{{ __('messages.filters.search') }}</label>
        <input type="text" class="tw-input-sm" wire:model.live.debounce.400ms="search">
    </div>

    <div>
        <label class="tw-label">{{ __('messages.filters.status') }}</label>
        <select class="tw-input-sm" wire:model.live="status">
            <option value="">{{ __('messages.filters.all') }}</option>
        </select>
    </div>

    <div>
        <label class="tw-label">{{ __('messages.filters.per_page') }}</label>
        <select class="tw-input-sm" wire:model.live="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    <div class="flex items-end">
        <button type="button" class="tw-btn tw-btn-gray w-full" wire:click="clearFilters">
            <i class="fas fa-times text-xs"></i>{{ __('messages.actions.clear') }}
        </button>
    </div>

    <div class="flex items-end">
        @can('events.create')
            <button type="button" class="tw-btn tw-btn-emerald w-full" wire:click="create">
                <i class="fas fa-plus text-xs"></i>{{ __('messages.actions.create') }}
            </button>
        @endcan
    </div>
</div>
```

## Tabla principal

La tabla debe ser el centro de la gestión.

Reglas:

- Usar `tw-table-wrap`, `tw-table` y `themed-thead` cuando esten disponibles.
- Mantener columnas legibles y ordenadas por importancia.
- Usar badges para estados.
- Usar formato consistente para fechas, moneda y estados.
- No ejecutar consultas dentro del Blade.
- No acceder a relaciones no cargadas previamente.
- La ultima columna debe reservarse para acciones.
- La columna de acciones debe tener ancho estable y alineacion consistente.

Columnas habituales:

```text
identificador o código
nombre/título
categoría/tipo
estado
fecha principal
usuario responsable
acciones
```

## Dropdown de acciones por fila

La ultima columna debe mostrar un dropdown compacto con acciones disponibles para el usuario autenticado.

Acciones comunes:

```text
Ver detalle
Editar
Publicar / despublicar
Activar / desactivar
Duplicar
Exportar
Eliminar
```

Reglas:

- Cada acción debe estar protegida visualmente con `@can`.
- Cada método Livewire debe volver a validar el permiso.
- Las acciones destructivas deben ir separadas visualmente y usar color de peligro.
- Si no hay acciones disponibles, mostrar un guion o no renderizar el boton.

Ejemplo conceptual:

```blade
<td class="w-28 text-right">
    <div class="relative inline-block text-left" x-data="{ open: false }">
        <button type="button" class="tw-btn-xs tw-btn-outline-gray" @click="open = !open">
            <i class="fas fa-ellipsis-v"></i>
        </button>

        <div x-show="open" @click.outside="open = false" class="absolute right-0 z-20 mt-1 w-44 rounded-md border bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            @can('events.update')
                <button type="button" class="block w-full px-3 py-2 text-left text-sm" wire:click="edit({{ $event->id }})">
                    <i class="fas fa-pencil-alt mr-2"></i>{{ __('messages.actions.edit') }}
                </button>
            @endcan

            @can('events.delete')
                <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600" wire:click="confirmDelete({{ $event->id }})">
                    <i class="fas fa-trash mr-2"></i>{{ __('messages.actions.delete') }}
                </button>
            @endcan
        </div>
    </div>
</td>
```

## Modales

Los formularios de creacion y edicion deben abrirse en modal.

Reglas:

- Usar un solo modal para crear/editar cuando el formulario sea similar.
- Usar modales separados para confirmaciones destructivas si mejora claridad.
- Los campos obligatorios deben indicarse visualmente.
- Las validaciones Livewire deben mostrarse debajo del campo.
- El modal debe tener boton Cancelar y boton Guardar.
- El boton Guardar debe deshabilitarse con `wire:loading.attr="disabled"`.
- El modal debe limpiar errores y estado temporal al cerrar.
- No guardar datos si la validación falla.

Ejemplo conceptual:

```blade
@if ($showModal)
    <x-tw-modal close="closeModal" max-width="3xl" :title="$editingId ? __('messages.events.edit') : __('messages.events.create')" icon="fas fa-calendar">
        <div class="grid gap-3 md:grid-cols-2">
            <div>
                <label class="tw-label">{{ __('messages.events.fields.name') }}</label>
                <input type="text" class="tw-input @error('form.name') border-red-500 @enderror" wire:model.defer="form.name">
                @error('form.name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <x-slot:footer>
            <button type="button" class="tw-btn tw-btn-gray" wire:click="closeModal">{{ __('messages.actions.cancel') }}</button>
            <button type="button" class="tw-btn tw-btn-emerald" wire:click="save" wire:loading.attr="disabled">
                <i class="fas fa-save text-xs"></i>{{ __('messages.actions.save') }}
            </button>
        </x-slot:footer>
    </x-tw-modal>
@endif
```

## SweetAlert 2

SweetAlert 2 debe usarse para mensajes de exito, error, advertencia y confirmaciones relevantes.

Convencion recomendada de eventos Livewire:

```php
$this->dispatch('successAlert', [
    'success' => __('messages.events.messages.created'),
]);

$this->dispatch('failedAlert', [
    'failed' => __('messages.events.messages.save_failed'),
]);
```

Convencion visual:

```text
successAlert -> icon success
failedAlert  -> icon error
warningAlert -> icon warning
confirmAlert -> confirmacion antes de acción destructiva
```

Reglas:

- Los mensajes deben salir de archivos de traducción.
- El backend debe enviar mensajes claros y seguros.
- No mostrar trazas tecnicas al usuario final.
- Para errores internos, usar `App\Support\ErrorMessage` cuando aplique.
- Las acciones destructivas deben requerir confirmacion.

## Componente Livewire base

Cada componente de tabla debe incluir:

```text
WithPagination
perPage
filtros publicos
queryString para filtros utiles
rules()
messages()
clearFilters()
create()
edit()
save()
confirmDelete()
delete()
closeModal()
render()
propiedad computada o método para consulta paginada
```

Ejemplo de consulta:

```php
public function getRowsProperty()
{
    return Event::query()
        ->with([
            'venue:id,name',
        ])
        ->withCount('fights')
        ->when($this->search !== '', function ($query) {
            $search = "%{$this->search}%";

            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', $search)
                    ->orWhere('slug', 'like', $search);
            });
        })
        ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
        ->orderByDesc('starts_at')
        ->paginate($this->perPage);
}
```

## Rendimiento y consultas sin N+1

Todas las consultas de tabla deben evitar N+1.

Reglas obligatorias:

- Usar `with()` para relaciones que se imprimen en la tabla.
- Usar `withCount()` para conteos.
- Usar `select()` cuando la tabla no necesita todas las columnas.
- Usar `when()` para filtros opcionales.
- Usar `paginate()` o `simplePaginate()`, no `get()` para listados grandes.
- No ejecutar queries dentro de loops Blade.
- No llamar relaciones no cargadas dentro de cada fila.
- No calcular totales fila por fila si pueden agregarse en la consulta.
- Agregar indices a columnas filtradas u ordenadas cuando el módulo lo requiera.
- Para exportaciones, usar chunking o queries especificas de exportacion.

Checklist de revision:

```text
[ ] La tabla pagina resultados.
[ ] Las relaciones visibles estan en with().
[ ] Los conteos usan withCount().
[ ] No hay consultas en Blade.
[ ] Los filtros usan columnas indexables cuando aplica.
[ ] La consulta respeta jerarquia, visibilidad o propiedad cuando aplica.
[ ] Las acciones Livewire vuelven a validar permisos.
```

## Permisos y seguridad

Cada módulo debe validar en tres niveles:

```text
ruta        -> middleware can/permission
controlador -> authorize() si aplica
Livewire    -> abort_unless/authorize antes de cargar y antes de accionar
policy      -> permiso + alcance + jerarquia si hay usuario objetivo
```

Ejemplo:

```php
public function edit(int $id): void
{
    $this->authorize('events.update');

    $event = Event::query()
        ->findOrFail($id);

    $this->editingId = $event->id;
}
```

Para gestión de usuarios y roles, aplicar la jerarquia documentada en `handoff/propuesta_roles_permisos_mma.md`.

## Traducciones

Todo texto visible debe usar traducciones:

```text
títulos
labels
placeholders
botones
acciones del dropdown
mensajes SweetAlert
validaciones personalizadas
estados
textos vacíos
ayudas
tooltips
encabezados de tabla
opciones de filtros
confirmaciones
mensajes de permisos
```

El idioma por defecto de la aplicación será mayormente español. Por eso, los textos en español deben escribirse con ortografía correcta, tildes, acentos, signos de apertura cuando corresponda y redacción natural para el usuario final.

Ejemplos esperados:

```text
Gestión de eventos
Categoría
Descripción
Acciones
¿Eliminar registro?
El evento se creó correctamente.
No se pudo guardar la información.
Tu suscripción ha expirado.
```

Reglas obligatorias:

- No dejar textos visibles hardcodeados en Blade, Livewire, controladores o JavaScript.
- No mostrar claves técnicas como `events.view`, `subscription_payments.confirm` o `subscriber.purchases.view` como etiqueta principal para usuarios.
- No mezclar idiomas en la misma vista.
- Al cambiar el idioma activo, toda la vista debe cambiar: labels, placeholders, botones, acciones, tooltips, estados, errores, confirmaciones, mensajes SweetAlert y textos vacíos.
- Cada texto agregado en español debe tener su equivalente en todos los idiomas activos del sistema.
- Las traducciones deben guardarse en UTF-8 y validarse para evitar caracteres corruptos.
- Las traducciones de roles y permisos deben usar etiquetas amigables, no nombres técnicos.

Convencion sugerida:

```text
messages.events.page_title
messages.events.filters.search
messages.events.fields.name
messages.events.messages.created
messages.events.messages.updated
messages.events.messages.deleted
messages.actions.save
messages.actions.cancel
messages.actions.delete
```

Checklist de traducciones por módulo:

```text
[ ] Título de página traducido.
[ ] Filtros traducidos.
[ ] Labels de formularios traducidos.
[ ] Placeholders traducidos.
[ ] Botones traducidos.
[ ] Acciones del dropdown traducidas.
[ ] Estados/badges traducidos.
[ ] Mensajes de validación traducidos.
[ ] Mensajes SweetAlert traducidos.
[ ] Textos vacíos traducidos.
[ ] Tooltips y aria-labels traducidos.
[ ] Español revisado con tildes y acentos correctos.
[ ] Todos los idiomas activos tienen las mismas claves.
```

## Estados vacios y errores

Cada tabla debe contemplar:

- Estado sin registros.
- Estado sin resultados por filtros.
- Error de autorización con `403`.
- Error de guardado con SweetAlert.
- Botones con `wire:loading`.
- Campos con errores de validación.

## Auditoría

Las acciones relevantes deben registrar auditoría:

```text
crear
editar
eliminar
publicar
confirmar pago
asignar permiso
cambiar rol
bloquear usuario
```

El registro debe incluir usuario actor, entidad, id de entidad, valores importantes y acción ejecutada.

## Pagos con comprobante

El módulo administrativo de pagos de suscripción debe permitir revisar, registrar y confirmar pagos manuales recibidos por WhatsApp, teléfono o formulario.

Reglas visuales y funcionales:

- La tabla debe mostrar estado, usuario, plan/suscripción, monto, método, fecha, comprobante y acción.
- El modal de creación/edición debe permitir adjuntar o reemplazar el comprobante de pago.
- El comprobante debe aceptar `jpg`, `jpeg`, `png` y `pdf`, con tamaño máximo de 5 MB.
- Los comprobantes en imagen deben optimizarse antes de guardarse usando `App\Services\ImageUploadOptimizer` o un servicio centralizado equivalente, con validación previa para limitar este flujo a `jpg`, `jpeg` y `png`.
- Los comprobantes PDF se validan por tipo/tamaño y se guardan sin compresión inicial.
- Los comprobantes deben servirse desde almacenamiento privado o protegido, solo para usuarios administrativos autorizados.
- El comprobante debe validarse por tipo de archivo, tamaño máximo y obligatoriedad según el estado del pago.
- La acción de confirmar pago debe usar SweetAlert 2 y registrar auditoría.
- Todos los labels, errores de carga y mensajes de éxito/error deben traducirse.

## Solicitudes de compra/contacto

El formulario público de contacto o compra debe crear una solicitud visible en administración. Esta gestión debe tener vista propia, independiente de soporte general.

Estructura visual:

```text
filtros superiores
tabla de solicitudes
dropdown de acciones
modal de detalle/edición
historial de atención
SweetAlert 2 para cambios de estado
```

Filtros mínimos:

- Estado.
- Tipo de solicitud.
- Evento.
- Plan.
- Responsable.
- Rango de fechas.
- Búsqueda por nombre, teléfono, WhatsApp o correo.

La tabla debe mostrar interesado, canal preferido, evento/plan asociado, estado, responsable, fecha de creación y última acción. Las consultas deben usar `with()` para evento, plan, usuario y responsable, evitando N+1.

## Módulos a implementar con este patron

Primera etapa administrativa:

```text
Eventos
Combates
Peleadores
Categorías de peso
Equipos y gimnasios
Multimedia de eventos
Noticias / posts
Planes de suscripción
Suscriptores
Suscripciones
Pagos de suscripción
Solicitudes de compra/contacto
Enlaces de tickets
Notificaciones
Usuarios
Roles
Version app
Soporte
```

Portal suscriptor:

```text
Mis compras
Mis eventos
Mi suscripción
Perfil
```

El portal suscriptor puede usar el mismo lenguaje visual, pero debe ser mas simple y no debe mostrar acciones administrativas.

## Ajustes propios de MMA

Cada módulo MMA debe aplicar estos criterios:

- Usar los componentes visuales actuales del proyecto MMA (`tw-*`, `x-tw-modal`, `layouts.admin`).
- Evitar pantalla independiente para crear permisos.
- Gestionar permisos desde roles, con checkboxes sobre el catalogo existente.
- Usar nombres visibles traducidos para roles y permisos.
- Aplicar jerarquia estricta para gestión de usuarios.
- Mantener rutas MMA con prefijo administrativo consistente.
- Priorizar consultas sin N+1 desde el inicio.

## Checklist antes de cerrar cada módulo

```text
[ ] Ruta creada con middleware de permiso.
[ ] Controlador administrativo delgado creado.
[ ] Vista wrapper creada con layout admin.
[ ] Componente Livewire creado.
[ ] Filtros superiores implementados.
[ ] Tabla principal implementada.
[ ] Ultima columna de acciones con dropdown.
[ ] Acciones protegidas con @can y validación backend.
[ ] Modal de crear/editar con validaciones.
[ ] Modal o SweetAlert de confirmacion para eliminar.
[ ] Mensajes SweetAlert de exito/error.
[ ] Textos visibles traducidos.
[ ] Slug generado, validado y usado en URLs públicas cuando el módulo lo requiera.
[ ] Consulta paginada sin N+1.
[ ] Jerarquia, visibilidad o propiedad aplicada cuando corresponda.
[ ] Auditoría aplicada en acciones relevantes.
[ ] Pruebas o verificación manual del flujo principal.
```
