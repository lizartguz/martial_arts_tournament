# Propuesta de menú lateral administrativo MMA

## Objetivo

Este documento propone una configuración inicial del menú lateral administrativo para la plataforma MMA. La idea es reutilizar el patron actual del proyecto, donde el menú se define desde `config/panel.php` y se filtra por permisos mediante `App\Support\AdminPanel`.

La propuesta no implementa rutas ni pantallas todavia. Sirve para revisar como deberian agruparse los módulos antes de renombrar, eliminar o crear nuevas opciones.

## Criterios de configuración

- El menú debe estar agrupado por flujo de trabajo, no solo por tablas.
- Las opciones mas usadas deben quedar arriba: dashboard, eventos, peleadores y contenido.
- Los módulos de configuración global deben quedar abajo.
- Cada item debe tener permiso Spatie propio para poder ocultarlo por rol.
- Los textos visibles pueden traducirse con `resources/lang`.
- Las rutas sugeridas usan prefijo `/admin` para separar claramente la administracion de la landing pública.

## Roles considerados

| Rol Spatie | Enfoque en el menú |
| --- | --- |
| `super_manager` | Ve todo el menú, configura sistema, roles, permisos y usuarios de cualquier rango. |
| `admin` | Ve y administra la promotora, eventos, usuarios internos de menor rango, suscripciones y contenido. |
| `manager` | Ve módulos operativos asignados por permisos. Puede revisar, validar o apoyar gestión. |
| `publisher` | Ve principalmente eventos, contenido, multimedia, noticias y notificaciones si se le permite. |
| `subscriber` | No deberia entrar al panel administrativo completo. Su experiencia debe ir en app/web de cliente. |

## Propuesta general de sidebar

```text
Dashboard
Configuración del sistema
Eventos
Peleadores
Contenido
Suscripciones y pagos
Tickets
Notificaciones
Usuarios y acceso
Reportes
Configuración
Soporte
```

## Detalle de módulos

### Dashboard

Resumen operativo del sistema.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Inicio | `/admin/dashboard` | Indicadores principales: eventos activos, peleadores, suscriptores, ventas/manuales, notificaciones recientes. | `super_manager`, `admin`, `manager`, `publisher` |

### Configuración del sistema

Gestión de identidad del producto, datos de la promotora, sedes y catalogos base.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Ajustes del sistema | `/admin/settings` | Nombre temporal `Combate Real`, logo, favicon, título público, contacto y redes. | `super_manager`, `admin` |
| Sedes | `/admin/venues` | Lugares donde se realizan eventos. | `super_manager`, `admin`, `manager`, `publisher` |
| Ciudades y países | `/admin/locations` | Catalogos de países y ciudades. | `super_manager`, `admin` |

### Eventos

Módulo central para planificar y publicar carteleras.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Eventos | `/admin/events` | CRUD de eventos, fechas, sede, estado de publicación, banner y poster. | `super_manager`, `admin`, `manager`, `publisher` |
| Combates | `/admin/fights` | CRUD de peleas, esquinas X/Y, categoría, rounds, orden de cartelera y estado. | `super_manager`, `admin`, `manager`, `publisher` |
| Resultados | `/admin/fight-results` | Registro de ganador, método, round, tiempo y notas oficiales. | `super_manager`, `admin`, `manager` |
| Rankings | `/admin/rankings` | Ranking por categoría de peso, género y posición. | `super_manager`, `admin`, `manager` |
| Multimedia de eventos | `/admin/event-media` | Fotos/videos de pesaje, careos, backstage, banners, posters y highlights. | `super_manager`, `admin`, `manager`, `publisher` |

### Peleadores

Gestión de atletas y datos deportivos.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Peleadores | `/admin/fighters` | CRUD de perfiles, fotos, país, equipo, categoría, bio y record. | `super_manager`, `admin`, `manager`, `publisher` |
| Equipos y gimnasios | `/admin/fighter-teams` | Academias, clubes, gimnasios o equipos de entrenamiento. | `super_manager`, `admin`, `manager` |
| Categorías de peso | `/admin/weight-classes` | Categorías base tipo UFC y categorías personalizadas para la promotora. | `super_manager`, `admin` |
| Multimedia de peleadores | `/admin/fighter-media` | Galeria de fotos/videos por peleador. | `super_manager`, `admin`, `manager`, `publisher` |

### Contenido

Contenido público distinto al CRUD deportivo principal.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Noticias / posts | `/admin/news` | Publicaciones, comunicados, notas de eventos, entrevistas o novedades. | `super_manager`, `admin`, `publisher` |
| Landing page | `/admin/landing` | Bloques destacados de la pagina pública, banners o secciones visibles. | `super_manager`, `admin`, `publisher` |
| Sponsors | `/admin/sponsors` | Marcas o auspiciadores que se muestran en eventos o landing. | `super_manager`, `admin`, `publisher` |

### Suscripciones y pagos

Gestión de planes para suscriptores/clientes.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Planes | `/admin/subscription-plans` | Planes con precio, periodo, descuentos 10/15/20% y beneficios. | `super_manager`, `admin` |
| Suscriptores | `/admin/subscribers` | Usuarios con rol `subscriber`, estado, plan activo y datos de contacto. | `super_manager`, `admin`, `manager` |
| Suscripciones | `/admin/user-subscriptions` | Vigencias, renovaciones, cancelaciones, origen web/android/manual. | `super_manager`, `admin`, `manager` |
| Pagos de suscripción | `/admin/subscription-payments` | Pagos manuales por WhatsApp, teléfono o formulario, con comprobante adjunto, y futuros pagos por pasarela. | `super_manager`, `admin`, `manager` |
| Solicitudes de compra/contacto | `/admin/purchase-requests` | Solicitudes creadas desde landing o portal, con filtros, responsable, estado y comprobante si aplica. | `super_manager`, `admin`, `manager`, `sales` |

### Tickets

Primera etapa con enlaces externos y etapa futura con venta interna.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Enlaces de tickets | `/admin/ticket-links` | Links externos por evento: Ticketeg, WhatsApp, VIP, streaming, etc. | `super_manager`, `admin`, `publisher` |
| Tipos de ticket | `/admin/ticket-types` | Futuro módulo para definir General, VIP, Streaming, Mesa, etc. | `super_manager`, `admin` |
| Ordenes de tickets | `/admin/ticket-orders` | Futuro módulo para compras internas. | `super_manager`, `admin`, `manager` |
| Validación de ingreso | `/admin/ticket-checkins` | Futuro módulo para QR/check-in. | `super_manager`, `admin`, `manager` |

Nota:

- En primera etapa solo se implementaria `ticket_links`.
- Los otros items pueden quedar documentados, pero no visibles hasta planificar venta interna.

### Notificaciones

Avisos web/app y comunicacion con usuarios.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Notificaciones | `/admin/notifications` | Crear avisos normales, push o ambos. | `super_manager`, `admin`, `publisher` |
| Tokens FCM | `/admin/fcm-tokens` | Diagnostico de dispositivos web/android, errores y tokens activos. | `super_manager`, `admin` |
| Campanas | `/admin/campaigns` | Futuro módulo para segmentar por version de app, plan o evento. | `super_manager`, `admin` |

### Usuarios y acceso

Control de usuarios, roles, permisos y seguridad.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Usuarios | `/admin/users` | Gestión de usuarios internos y suscriptores. | `super_manager`, `admin`, `manager` |
| Roles y permisos | `/admin/authorization` | Matriz Spatie de roles y permisos. | `super_manager`, `admin` |
| Sesiones / dispositivos | `/admin/devices` | Revision de `device_identifier`, versiones de app y bloqueo de accesos multiples. | `super_manager`, `admin`, `manager` |
| Solicitudes de eliminación | `/admin/account-deletions` | Revision de solicitudes de baja de cuenta si aplica. | `super_manager`, `admin` |

### Reportes

Consultas administrativas y metricas.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Eventos y asistencia | `/admin/reports/events` | Reportes de eventos, carteleras, publicaciones y actividad. | `super_manager`, `admin`, `manager` |
| Suscripciones | `/admin/reports/subscriptions` | Planes activos, vencimientos, pagos manuales y renovaciones. | `super_manager`, `admin`, `manager` |
| App Android | `/admin/reports/android` | Versiones de app, dispositivos, tokens FCM y usuarios activos. | `super_manager`, `admin` |

### Configuración

Configuración técnica o catalogos base.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Ajustes generales | `/admin/settings` | Nombre de plataforma, logos, parametros generales. | `super_manager`, `admin` |
| Version app | `/admin/app-update` | Version minima, version actual y bloqueo para Android. | `super_manager`, `admin` |
| Idiomas | `/admin/locales` | Revision de idiomas disponibles desde `resources/lang`. | `super_manager` |
| Logs | `/admin/logs` | Logs del sistema. | `super_manager` |

### Soporte

Mensajes o solicitudes de contacto.

| Item | Ruta sugerida | Uso | Roles sugeridos |
| --- | --- | --- | --- |
| Mensajes de contacto | `/admin/contact-messages` | Contactos, soporte, alianzas o solicitudes de peleadores. | `super_manager`, `admin`, `manager` |

## Orden recomendado en `config/panel.php`

```text
Dashboard
Configuración del sistema
Eventos
Peleadores
Contenido
Suscripciones y pagos
Tickets
Notificaciones
Usuarios y acceso
Reportes
Configuración
Soporte
```

## Permisos sugeridos

Los permisos deberian seguir una convencion simple por módulo:

```text
events.view
events.create
events.update
events.delete
events.publish
fighters.view
fighters.create
fighters.update
fighters.delete
subscription_plans.view
subscription_payments.confirm
purchase_requests.view
notifications.send
authorization.access
```

Regla sugerida:

- `super_manager` puede tener todos los permisos.
- `admin` puede tener permisos completos de operacion, excepto configuración critica del sistema.
- `manager` debe recibir permisos especificos según tarea.
- `publisher` debe enfocarse en contenido, eventos, multimedia y notificaciones permitidas.
- `subscriber` no deberia tener permisos de panel administrativo.

## Módulos que conviene ocultar al inicio

Estos items pueden quedar documentados, pero no visibles hasta que existan pantallas y flujo definido:

- Venta interna de tickets: `ticket_types`, `ticket_orders`, `ticket_checkins`.
- Campanas avanzadas de notificaciones.
- Reportes complejos.
- Idiomas administrados desde panel.

## Primera etapa sugerida

Para avanzar rapido, el primer menú funcional podria quedar asi:

```text
Dashboard
Configuración del sistema
Eventos
  - Eventos
  - Combates
  - Multimedia de eventos
Peleadores
  - Peleadores
  - Equipos y gimnasios
  - Categorías de peso
Contenido
  - Noticias / posts
Suscripciones y pagos
  - Planes
  - Suscriptores
  - Pagos de suscripción
  - Solicitudes de compra/contacto
Tickets
  - Enlaces de tickets
Notificaciones
Usuarios y acceso
  - Usuarios
  - Roles y permisos
Configuración
  - Version app
Soporte
```
