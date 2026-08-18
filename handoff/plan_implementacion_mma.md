# Plan de implementación MMA

## Objetivo

Este documento define el orden recomendado para comenzar la implementación de la plataforma MMA con el menor nivel posible de ambiguedad. La aplicación se trabajara como una instalación para una sola promotora, no como sistema multi-tenant.

Nombre temporal del producto:

```text
Combate Real
```

El logo, favicon y login actuales pueden conservarse al inicio. Mas adelante, desde Configuración del sistema, se podrán cambiar nombre del producto, título visible, logo, favicon y textos institucionales.

## Decisiones cerradas

- La aplicación no será multi-tenant.
- Cada instalación corresponde a una promotora MMA.
- Se mantiene español como idioma por defecto.
- Se conservan todos los idiomas actuales del proyecto.
- Los textos visibles deben traducirse por completo en todos los idiomas activos.
- Los módulos heredados sin valor para MMA deben eliminarse.
- La base local de trabajo para migraciones y pruebas será `artsmartialdb`.
- Las funciones repetibles deben centralizarse en servicios reutilizables, configuración o clases dedicadas.
- El usuario `super_manager` local/demo será `sarteaga@root.dev` y su contraseña inicial local será igual al correo.
- Los pagos iniciales serán manuales o por contacto mediante WhatsApp, teléfono o formulario.
- Los pagos manuales deben guardar imagen o archivo de comprobante desde la primera etapa.
- Los comprobantes permitidos serán `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Las imágenes de comprobante deben optimizarse antes de guardarse usando `App\Services\ImageUploadOptimizer` o un servicio equivalente; los PDF se validan y guardan sin compresión inicial.
- Los comprobantes se guardarán en almacenamiento privado o protegido y solo serán visibles desde sesión administrativa autorizada.
- La pasarela de pagos queda para una fase futura.
- Los tickets de primera etapa serán enlaces externos por evento.
- Los eventos activos/publicados serán visibles al público, incluyendo eventos pasados, actuales y próximos.
- La landing ordenará eventos primero por `is_featured` y luego por fecha más cercana.
- El formulario público de contacto/compra debe crear solicitudes visibles en administración.
- Los rankings serán por categoría de peso y género.
- El administrador controla cuándo un evento deja de mostrarse cambiando su estado.
- Los eventos en borrador no se mostraran publicamente.
- El `slug` se genera automaticamente al crear desde `name` o `title`.
- El administrador puede editar manualmente el `slug`, con validación de formato y unicidad.
- El `slug` no debe cambiar automaticamente al editar el título de un registro publicado.
- Los roles definitivos son `super_manager`, `admin`, `publisher`, `sales`, `checkin`, `support` y `subscriber`. (El rol `manager`/"Gestor" se eliminó el 2026-08-17: sus permisos operativos quedaron cubiertos por `admin`.)
- `super_manager` es el rol técnico de root funcional.
- Roles y permisos se crean por código, no desde el panel.
- El panel solo asigna o quita roles/permisos existentes.
- Los seeders deben incluir datos demo para visualizar landing, tablas, filtros, estados y modales sin llenar todo manualmente.
- Las imágenes demo se tomarán de assets ya cargados en el proyecto.

## Orden recomendado de implementación

### Fase 0: preparacion y limpieza controlada

Objetivo: dejar claro que partes heredadas se conservan y cuales se eliminan.

Tareas:

1. Crear rama o punto de respaldo antes de eliminaciones grandes.
2. Listar rutas, controladores, vistas, modelos, migraciones, seeders y assets heredados.
3. Separar infraestructura Laravel reutilizable de dominio clima/agro heredado.
4. Eliminar menus heredados que ya no pertenecen a MMA.
5. Eliminar vistas y componentes heredados sin uso.
6. Eliminar controladores y Livewire heredados sin uso.
7. Eliminar traducciones heredadas no aplicables.
8. Ejecutar pruebas y revisar errores de autoload/rutas.
9. Configurar el entorno local para usar `DB_DATABASE=artsmartialdb`.

No eliminar:

```text
auth/Fortify/Jetstream/Sanctum
roles y permisos Spatie
layouts base reutilizables
notificaciones push si se reutilizaran
servicios de carga/optimizacion de imágenes
idiomas actuales
contacto/soporte si se adapta
```

### Fase 1: configuración base del sistema

Objetivo: soportar nombre, logo, favicon y datos de la promotora desde administracion.

Tareas:

1. Crear tabla o configuración persistente para ajustes del sistema.
2. Definir valores iniciales con `Combate Real`.
3. Permitir configurar nombre del producto, título, logo, favicon, contacto y enlaces sociales.
4. Reflejar esos valores en login, sidebar, topbar, favicon y landing pública.
5. Agregar permisos `settings.view` y `settings.update`.
6. Traducir todos los textos visibles.

### Fase 2: base de datos MMA

Objetivo: crear las entidades principales del dominio.

Prioridad:

1. Categorías de peso.
2. Sedes.
3. Peleadores.
4. Equipos y gimnasios.
5. Eventos.
6. Combates.
7. Multimedia.
8. Planes de suscripción.
9. Suscriptores.
10. Suscripciones.
11. Pagos manuales con comprobante.
12. Solicitudes de compra/contacto.
13. Enlaces externos de tickets.
14. Noticias/posts.
15. Sponsors.

Reglas:

- No agregar `organization_id` salvo que exista una razon funcional no multi-tenant.
- Usar `slug` en modelos con URL pública.
- Crear indices para filtros y ordenamientos principales.
- Mantener `created_by` y `updated_by` donde aporte auditoría.

### Fase 3: roles, permisos y seeders

Objetivo: registrar el catalogo técnico de roles y permisos.

Tareas:

1. Actualizar seeder de roles y permisos.
2. Registrar permisos por módulo.
3. Configurar jerarquia de roles.
4. Proteger permisos criticos.
5. Limpiar permisos heredados de `subscriber`.
6. Implementar traducciones visibles para roles y permisos.
7. Validar que no exista creador de permisos desde panel.
8. Crear seeders obligatorios para configuración base, catálogos, roles y permisos.
9. Crear `MmaDemoSeeder` para cargar datos ficticios solo en local.
10. Proteger seeders demo para que no se ejecuten en producción por accidente.
11. Crear el usuario local/demo `sarteaga@root.dev` con rol `super_manager`.

### Fase 4: esqueleto visual de módulos administrativos

Objetivo: implementar el patron visual comun.

Orden sugerido:

1. Configuración del sistema.
2. Roles.
3. Usuarios.
4. Categorías de peso.
5. Sedes.
6. Peleadores.
7. Eventos.
8. Combates.
9. Planes.
10. Suscriptores.
11. Pagos.
12. Tickets externos.

Cada módulo debe tener:

```text
filtros superiores
tabla principal
dropdown de acciones
modal crear/editar
validaciones
SweetAlert 2
traducciones completas
consultas sin N+1
permisos por ruta y acción
```

### Fase 5: landing pública

Objetivo: mostrar la promotora, eventos publicados, combates, peleadores destacados y llamadas de contacto/compra.

Tareas:

1. Crear home pública.
2. Crear detalle público de evento por `slug`.
3. Crear detalle público de peleador por `slug`.
4. Crear navegación pública entre evento anterior, evento actual/destacado y evento siguiente.
5. Mostrar eventos pasados, actuales y próximos mientras sigan activos/publicados.
6. Mostrar enlaces externos de tickets/contacto por WhatsApp, teléfono y formulario.
7. Crear formulario público de contacto/compra que registre `purchase_requests`.
8. Permitir adjuntar comprobante `jpg`, `jpeg`, `png` o `pdf`, máximo 5 MB.
9. Optimizar imágenes de comprobante antes de guardarlas.
10. Dejar ranking por categoría de peso y género preparado desde la primera versión, pero oculto/comentado hasta activarlo.
11. Mostrar solo registros publicados/activos.
12. Respetar idioma activo.

### Fase 6: portal del suscriptor

Objetivo: permitir al suscriptor consultar su cuenta sin entrar al panel administrativo.

Menú:

```text
Inicio
Mis compras
Mis eventos
Mi suscripción
Perfil
```

Primera etapa:

- Consulta de eventos comprados o asociados.
- Consulta de pagos/suscripciones registradas manualmente.
- Contacto para compra o renovacion por WhatsApp, teléfono y formulario.
- Visualización de estado de pago y compra confirmada.
- Carga de comprobantes `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Optimización de imágenes de comprobante antes de guardarlas.
- Los comprobantes cargados se almacenan como archivos privados y se revisan desde administración.
- Sin pasarela de pagos.

### Fase 7: pruebas, hardening y cierre de migracion

Objetivo: asegurar seguridad y rendimiento.

Tareas:

1. Probar acceso forzado por URL.
2. Probar jerarquia de usuarios.
3. Probar que `admin` no modifica otro `admin`.
4. Probar que `subscriber` no entra a `/admin`.
5. Probar slugs duplicados y slugs invalidos.
6. Probar traducciones por idioma.
7. Revisar consultas N+1.
8. Eliminar archivos heredados restantes.

## Primer bloque implementable recomendado

Cuando se autorice iniciar código, empezar por:

```text
1. Configurar entorno local con DB_DATABASE=artsmartialdb.
2. Limpieza de menú/rutas heredadas no usadas.
3. Configuración base de producto Combate Real.
4. Servicios reutilizables base: archivos/imágenes, slugs, jerarquía de roles.
5. Roles, permisos y jerarquia.
6. Migraciones base MMA.
7. Seeders obligatorios y demo.
8. Primer CRUD: categorías de peso o sedes.
```

La razon para iniciar con Configuración, Roles y un CRUD simple es validar temprano el patron visual, permisos, traducciones, modales, SweetAlert y consultas sin N+1 antes de repetirlo en módulos mas grandes como eventos o peleadores.

## Documentos relacionados

- `handoff/estrategia_servicios_reutilizables_seeders_mma.md`
