# Propuesta de estructura de base de datos MMA

## Objetivo del documento

Este handoff propone una arquitectura inicial de base de datos para transformar el proyecto Laravel heredado en una plataforma de artes marciales mixtas. La propuesta no es una implementación final: sirve como base de revision para ajustar nombres, campos, relaciones y prioridades antes de crear migraciones.

## Criterio general

El proyecto actual ya tiene piezas utiles que conviene conservar o adaptar:

- Autenticacion con `users`, Fortify, Jetstream y Sanctum.
- Roles y permisos con Spatie.
- Sesiones, jobs, cache y tokens personales de Laravel.
- Notificaciones genericas, lecturas de notificaciones y tokens FCM.
- Flujo de contacto/soporte, registro de IP y eliminación de cuenta.
- Estructura de imágenes y estados activos/inactivos que puede servir como patron.

Las tablas muy amarradas al dominio anterior de clima/estaciones deben eliminarse cuando se confirme que no son necesarias para MMA.

## Decision vigente: una sola promotora

La aplicación no será multi-tenant. Cada instalación corresponde a una sola promotora MMA.

Reglas vigentes:

- No se usara `organization_id` como separador de clientes.
- Los roles pertenecen a la misma instalación.
- `super_manager` es el rol técnico/root de la instalación.
- `admin` administra la promotora dentro de esa instalación, pero no gestiona otros administradores.
- La información institucional se guardara en una configuración del sistema o perfil de promotora, no en multiples organizaciones.
- El nombre temporal del producto será `Combate Real`.
- Logo, favicon, nombre del producto y título público serán configurables desde el panel.

## Resumen de decision por tablas actuales

| Tabla actual | Decision propuesta | Motivo |
| --- | --- | --- |
| `users` | Adaptar | Base de login/admin. Conviene limpiar campos de app/clima y agregar perfil administrativo. |
| `password_reset_tokens` | Conservar | Laravel/Fortify la usa para recuperacion de acceso. |
| `sessions` | Conservar | Necesaria si se mantiene session driver database. |
| `cache`, `cache_locks` | Conservar | Infraestructura Laravel. |
| `jobs`, `job_batches`, `failed_jobs` | Conservar | Util para notificaciones, emails, procesamiento de imágenes y tareas programadas. |
| `personal_access_tokens` | Conservar | Sanctum puede servir para API futura. |
| `roles`, `permissions`, pivots Spatie | Conservar | Base para administradores, editores y permisos de gestión. |
| `notifications` | Adaptar | Es generica y sirve para avisos de eventos, combates y novedades. |
| `notification_reads` | Conservar | Sirve para marcar avisos vistos por usuario. |
| `fcm_tokens` | Conservar | Sirve para Web Push y para la futura app móvil Android. |
| `app_update` | Adaptar | Puede servir para controlar version minima, version actual y bloqueo de la futura app Android. |
| `customer_service` | Adaptar a `contact_messages` | La lógica sirve, pero el nombre debe alinearse al nuevo dominio. |
| `account_deletion_requests` | Conservar/adaptar | Util si habra usuarios registrados no solo administradores. |
| `ip_registrations` | Conservar | Puede apoyar seguridad, rate limit o auditoría de accesos. |
| `departments`, `provinces`, `municipalities` | Reemplazar | Sirven al dominio anterior. Para MMA conviene una estructura simple `countries`/`cities`/`venues`. |
| `translations` | Eliminar | No era multidioma; registraba traslados de estaciones. Para idiomas se usaran archivos `resources/lang`. |
| `subscriptions`, `details_subscriptions`, `users_subscriptions` | Eliminar | La lógica heredada responde a otro modelo de negocio. Conviene crear una estructura nueva de planes, suscripciones y pagos. |
| Tablas de `stations`, sensores, clima, alertas climaticas, forecast, mantenimiento | Eliminar gradualmente | Son del dominio anterior y no aplican al nucleo MMA. |

## Módulos propuestos

1. Seguridad y administracion.
2. Configuración del sistema y perfil de promotora.
3. Contenido público y SEO.
4. Peleadores.
5. Categorías de peso.
6. Eventos.
7. Combates.
8. Resultados, records y rankings.
9. Multimedia.
10. Suscripciones, membresias y beneficios.
11. Tickets externos y futura venta interna.
12. Notificaciones.
13. Contacto y auditoría.

## Decisiones validadas

- La tabla de combates se llamara `fights`.
- La tabla para academias, gimnasios, clubes o equipos se llamara `fighter_teams`.
- Los roles base de Spatie se manejaran en ingles: `super_manager`, `admin`, `manager`, `publisher`, `sales`, `checkin`, `support`, `subscriber`.
- `super_manager` representa al programador, encargado técnico o root funcional del sistema.
- `admin` representa al administrador principal de la promotora que compra/usa el producto.
- `manager` recibe funciones operativas asignadas por `super_manager` o `admin`.
- `publisher` se encarga de publicaciones, eventos y contenido.
- `sales` atiende suscriptores, ventas manuales, renovaciones y pagos.
- `checkin` valida accesos o tickets cuando el módulo se active.
- `support` atiende consultas y soporte limitado.
- `subscriber` representa al cliente/fan que paga un plan o membresia.
- La plataforma tendrá login administrativo y login para suscriptores/clientes en web y Android.
- Los idiomas iniciales serán los que ya existen en `resources/lang`.
- Las categorías de peso serán configurables para la instalación, con registros iniciales basados en categorías tipo UFC.
- La estructura de rankings por categoría de peso y género debe existir desde la primera etapa, aunque su visualización pública pueda quedar oculta o comentada hasta activarse.
- Al inicio se usarán pagos manuales por WhatsApp, teléfono o formulario; más adelante se integrará pasarela de pagos.
- Los pagos manuales deben permitir guardar comprobante de pago desde la primera etapa.
- Los comprobantes permitidos serán `jpg`, `jpeg`, `png` y `pdf`, con tamaño máximo de 5 MB.
- Los comprobantes en imagen deben reducirse/optimizarse antes de guardarse usando el servicio existente `App\Services\ImageUploadOptimizer` o un servicio equivalente centralizado; los PDF se validan y guardan sin compresión inicial.
- Los comprobantes deben almacenarse en disco privado o protegido y solo visualizarse desde sesión administrativa autorizada.
- El formulario público de contacto/compra debe crear solicitudes visibles y gestionables desde administración.
- La base local vacía para migraciones, pruebas y seeders será `artsmartialdb`.
- Para tickets se manejaran enlaces externos desde la primera etapa y se deja prevista una arquitectura futura para venta interna.

## Tablas base a conservar o adaptar

### `users`

Tabla base para login administrativo y suscriptores/clientes. Los permisos concretos se controlan con Spatie mediante roles en ingles.

Campos propuestos:

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `username` | string(100), nullable, unique | Alias interno para login o identificacion administrativa. |
| `name` | string(100) | Nombre del usuario. |
| `lastname` | string(100), nullable | Apellido del usuario. |
| `email` | string(150), unique | Correo de acceso. |
| `number_phone` | string(20), nullable | Teléfono de contacto. |
| `password` | string | Hash de acceso. |
| `image` | string(255), nullable | Foto de perfil del administrador o usuario. |
| `email_verified_at` | timestamp, nullable | Verificación de correo. |
| `two_factor_secret` | text, nullable | Campo Fortify para 2FA. |
| `two_factor_recovery_codes` | text, nullable | Codigos de recuperacion 2FA. |
| `two_factor_confirmed_at` | timestamp, nullable | Confirmacion de 2FA. |
| `device_identifier` | string(300), nullable | Identificador del dispositivo principal autorizado para la app móvil. Ayuda a restringir multiples inicios de sesión cuando el usuario paga por un solo acceso. |
| `version_app` | string(30), nullable | Version de la app móvil usada por el usuario. Sirve para metricas, campanas, compatibilidad y notificaciones segmentadas. |
| `state` | tinyInteger default 1 | 0 bloqueado/inactivo, 1 activo. Controla si el usuario puede acceder al sistema. |
| `delete` | tinyInteger default 0 | 0 visible/activo en sistema, 1 eliminado logicamente. Permite ocultar el usuario sin borrar su historial. |
| `last_login_at` | timestamp, nullable | Ultimo acceso registrado. |
| `remember_token` | string, nullable | Token Laravel. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Reglas sugeridas para acceso móvil:

- Si `device_identifier` ya existe para el usuario, la app Android debe validar que el inicio de sesión venga desde el mismo dispositivo o requerir autorización administrativa para reemplazarlo.
- `version_app` debe actualizarse cuando el cliente móvil inicie sesión o reporte actividad.
- `state = 0` bloquea el acceso por decision administrativa o regla de negocio.
- `delete = 1` oculta al usuario de operaciones normales y evita login, manteniendo trazabilidad histórica.

Campos actuales candidatos a eliminar de `users`:

- `occupation`
- `ci`
- `pin`
- `is_update`
- `user_dependency_id`
- `access_type`
- `user_type`
- `payment_state`
- `own_state`

### Tablas Spatie

Se conservan porque el paquete `spatie/laravel-permission` ya esta instalado.

#### `roles`

| Campo | Uso |
| --- | --- |
| `id` | Identificador del rol. |
| `name` | Nombre del rol Spatie en ingles: `super_manager`, `admin`, `manager`, `publisher`, `sales`, `checkin`, `support`, `subscriber`. |
| `guard_name` | Guard de Laravel, normalmente `web`. |
| `created_at`, `updated_at` | Auditoría. |

Roles base validados:

| Rol | Descripción |
| --- | --- |
| `super_manager` | Encargado global del sistema, programador o equipo técnico con control total. Puede administrar configuración global y todos los usuarios. |
| `admin` | Administrador principal de la promotora. Gestiona eventos, usuarios internos, suscripciones y configuraciones permitidas. |
| `manager` | Usuario operativo con funciones asignadas por `super_manager` o `admin`. Puede revisar, validar o administrar partes del sistema según permisos. |
| `publisher` | Encargado de publicaciones, eventos, contenido, imágenes, noticias y material visible al público. |
| `sales` | Usuario enfocado en suscriptores, ventas manuales, renovaciones y pagos. |
| `checkin` | Usuario enfocado en validar accesos o tickets cuando el módulo se active. |
| `support` | Usuario enfocado en soporte y consultas limitadas. |
| `subscriber` | Cliente/fan que paga una membresia o plan. Accede a beneficios, descuentos, contenido o funciones según su suscripción. |

#### `permissions`

| Campo | Uso |
| --- | --- |
| `id` | Identificador del permiso. |
| `name` | Permiso granular: `events.view`, `events.create`, `fighters.update`, etc. |
| `guard_name` | Guard de Laravel. |
| `created_at`, `updated_at` | Auditoría. |

#### `model_has_roles`

| Campo | Uso |
| --- | --- |
| `role_id` | Rol asignado. |
| `model_type` | Modelo relacionado, normalmente `App\Models\User`. |
| `model_id` | ID del usuario. |

#### `model_has_permissions`

| Campo | Uso |
| --- | --- |
| `permission_id` | Permiso asignado directamente. |
| `model_type` | Modelo relacionado. |
| `model_id` | ID del usuario. |

#### `role_has_permissions`

| Campo | Uso |
| --- | --- |
| `permission_id` | Permiso incluido en el rol. |
| `role_id` | Rol que recibe el permiso. |

## Nuevas tablas del dominio MMA

### `system_settings`

Tabla de configuración general de la instalación. Como la aplicación no será multi-tenant, estos datos representan la identidad del producto y de la promotora que usa la plataforma.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `product_name` | string(150) | Nombre del producto visible en login, sidebar y landing. Valor inicial: `Combate Real`. |
| `public_title` | string(180), nullable | Título público o nombre comercial de la promotora. |
| `slug` | string(180), unique | Texto único y limpio para URL o identificacion interna; ejemplo: `combate-real`. |
| `logo` | string(255), nullable | Logo visible del producto/promotora. |
| `favicon` | string(255), nullable | Favicon configurable. |
| `default_image` | string(255), nullable | Imagen por defecto para contenido sin imagen propia. |
| `contact_email` | string(150), nullable | Correo de contacto administrativo. |
| `contact_phone` | string(25), nullable | Teléfono de contacto administrativo. |
| `whatsapp_phone` | string(25), nullable | Numero usado para contacto o ventas manuales. |
| `facebook_url` | string(500), nullable | Red social de la promotora. |
| `instagram_url` | string(500), nullable | Red social de la promotora. |
| `youtube_url` | string(500), nullable | Canal de videos o streaming. |
| `seo_title` | string(180), nullable | Título SEO por defecto. |
| `seo_description` | string(300), nullable | Descripción SEO por defecto. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Reglas:

- Debe existir un único registro activo de configuración general.
- El panel de Configuración del sistema actualiza estos valores.
- El login, sidebar, topbar, favicon, landing y footer deben leer estos valores.
- Si no hay valores personalizados, se usan los valores actuales del proyecto y `Combate Real` como nombre temporal.

## Reglas para campos `slug`

El `slug` es el identificador legible que puede formar parte de una URL pública o de una ruta interna cuando convenga mostrar una dirección limpia. El usuario ve el nombre normal del registro, pero la URL puede usar el `slug`.

Ejemplo:

```text
Nombre del evento: MMA Night Santa Cruz 2026
Slug generado: mma-night-santa-cruz-2026
URL pública: /eventos/mma-night-santa-cruz-2026
```

Reglas sugeridas:

- El `slug` debe generarse al crear el registro desde el campo principal, por ejemplo `name` o `title`.
- El `slug` debe normalizar texto: minúsculas, sin tildes, sin caracteres especiales, con palabras separadas por guion.
- El `slug` debe ser único dentro del alcance definido para el módulo.
- Si el `slug` generado ya existe, se debe agregar un sufijo incremental: `mma-night`, `mma-night-2`, `mma-night-3`.
- Si el usuario edita el nombre visible, el `slug` no debe cambiar automáticamente cuando el registro ya está publicado o ya pudo compartirse públicamente.
- Si se permite editar el `slug`, debe hacerse desde una acción controlada, con validación de unicidad y advertencia de impacto en enlaces compartidos.
- Los slugs deben validarse en backend, no solo en frontend.
- El sistema debe resolver registros públicos por `slug` y no por `id` cuando la URL sea visible para visitantes o suscriptores.

Alcance recomendado de unicidad:

| Módulo | Alcance sugerido |
| --- | --- |
| `system_settings.slug` | Global para la instalación. |
| `events.slug` | Global para la instalación. |
| `fighters.slug` | Global para perfiles públicos. |
| `venues.slug` | Global o por ciudad. |
| `fighter_teams.slug` | Global para la instalación. |
| `weight_classes.slug` | Global para la instalación. |
| `news.slug` | Global para publicaciones públicas. |
| `subscription_plans.slug` | Global para lógica interna o URL administrativa. |

Recomendación técnica:

```php
use Illuminate\Support\Str;

$baseSlug = Str::slug($name);
```

Luego debe aplicarse una rutina de unicidad por módulo antes de guardar.

### `countries`

Catalogo simple para nacionalidad de peleadores y países asociados a ciudades.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(100) | Nombre del país. |
| `iso_code` | string(3), nullable | Código ISO opcional. |
| `flag_image` | string(255), nullable | Ruta de bandera si se usa visualmente. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `cities`

Catalogo de ciudades para ubicar sedes/eventos. La ciudad contiene la relacion con país, por lo que `venues` no necesita guardar `country_id` directamente.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `country_id` | foreignId | País al que pertenece la ciudad. |
| `name` | string(120) | Nombre de la ciudad. |
| `slug` | string(150), nullable | Texto único y limpio para URL pública si se exponen paginas por ciudad; ejemplo: `santa-cruz-de-la-sierra`. |
| `state_region` | string(120), nullable | Departamento, estado o region si aplica. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `weight_classes`

Categorías de peso para organizar peleadores y combates. Se cargaran categorías iniciales basadas en UFC como registros por defecto, y la promotora podrá personalizarlas, desactivarlas o crear nuevas según su reglamento.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(100) | Nombre visible: Peso Ligero, Peso Welter, etc. |
| `slug` | string(120), unique | Texto único y limpio para URL pública generado desde el nombre; ejemplo: `peso-ligero`. |
| `gender` | enum/string | `male`, `female`, `mixed` si aplica. |
| `min_weight_kg` | decimal(6,2), nullable | Peso minimo en kg. |
| `max_weight_kg` | decimal(6,2), nullable | Peso maximo en kg. |
| `source` | string(40) default `custom` | Origen de la categoría: `ufc_default`, `system_default`, `custom`. |
| `is_default` | boolean default false | Indica si es una categoría cargada como base inicial. |
| `display_order` | integer default 0 | Orden en listados. |
| `description` | text, nullable | Descripción pública. |
| `status` | tinyInteger default 1 | Estado activo/inactivo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `fighter_teams`

Catalogo de equipos, academias, gimnasios o clubes donde entrenan los peleadores. Se propone este nombre en vez de `teams` para evitar confusion con posibles equipos de Jetstream.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(150) | Nombre del equipo, academia o gimnasio. |
| `slug` | string(180), unique | Texto único y limpio para URL pública generado desde el nombre del equipo; ejemplo: `academia-titan-mma`. |
| `type` | string(40) | Tipo: `academy`, `gym`, `club`, `team`, `independent`, `other`. |
| `country_id` | foreignId nullable | País principal del equipo. |
| `city_id` | foreignId nullable | Ciudad principal del equipo. |
| `address` | string(255), nullable | Dirección si se desea mostrar o administrar. |
| `logo` | string(255), nullable | Logo del equipo/gimnasio. |
| `contact_name` | string(120), nullable | Persona de contacto. |
| `contact_phone` | string(25), nullable | Teléfono de contacto. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

### `fighters`

Ficha principal de cada peleador.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `uuid` | uuid, unique | Identificador público estable. |
| `first_name` | string(100) | Nombre. |
| `last_name` | string(100), nullable | Apellido. |
| `nickname` | string(100), nullable | Alias de pelea. |
| `slug` | string(160), unique | Texto único y limpio para URL pública del perfil; ejemplo: `juan-el-tigre-perez`. |
| `country_id` | foreignId nullable | Nacionalidad principal. |
| `weight_class_id` | foreignId nullable | Categoría principal actual. |
| `fighter_team_id` | foreignId nullable | Equipo, academia, gimnasio o club al que pertenece. |
| `birthdate` | date, nullable | Fecha de nacimiento. |
| `height_cm` | decimal(5,2), nullable | Estatura. |
| `reach_cm` | decimal(5,2), nullable | Alcance. |
| `stance` | string(50), nullable | Guardia: orthodox, southpaw, switch, etc. |
| `bio` | text, nullable | Biografia pública. |
| `profile_image` | string(255), nullable | Foto principal. |
| `cover_image` | string(255), nullable | Imagen de portada. |
| `wins` | unsignedInteger default 0 | Victorias acumuladas. |
| `losses` | unsignedInteger default 0 | Derrotas acumuladas. |
| `draws` | unsignedInteger default 0 | Empates acumulados. |
| `no_contests` | unsignedInteger default 0 | Sin resultado. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo, 2 retirado. |
| `created_by` | foreignId nullable | Usuario administrador que creo el registro. |
| `updated_by` | foreignId nullable | Usuario administrador que modifico el registro. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

### `fighter_stats`

Tabla separada para estadisticas que pueden crecer sin cargar demasiado `fighters`.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `fighter_id` | foreignId | Peleador relacionado. |
| `knockout_wins` | unsignedInteger default 0 | Victorias por KO/TKO. |
| `submission_wins` | unsignedInteger default 0 | Victorias por sumision. |
| `decision_wins` | unsignedInteger default 0 | Victorias por decision. |
| `knockout_losses` | unsignedInteger default 0 | Derrotas por KO/TKO. |
| `submission_losses` | unsignedInteger default 0 | Derrotas por sumision. |
| `decision_losses` | unsignedInteger default 0 | Derrotas por decision. |
| `current_streak` | integer default 0 | Racha actual positiva o negativa. |
| `ranking_position` | unsignedInteger, nullable | Ranking dentro de su categoría. |
| `metadata` | json, nullable | Datos extra futuros. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `fighter_rankings`

Ranking de peleadores por categoría de peso y género. La estructura queda lista desde la primera etapa, aunque la visualización pública del ranking pueda quedar oculta o comentada hasta activarse.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `weight_class_id` | foreignId | Categoría de peso rankeada. |
| `gender` | string(20) | Género del ranking: `male`, `female`, `mixed` u otro valor definido por catálogo. |
| `fighter_id` | foreignId | Peleador rankeado. |
| `position` | unsignedInteger | Posicion dentro del ranking. |
| `previous_position` | unsignedInteger nullable | Posicion anterior para mostrar subidas/bajadas. |
| `is_champion` | boolean default false | Marca si el peleador es campeon de la categoría. |
| `ranked_at` | date nullable | Fecha oficial del ranking. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_by` | foreignId nullable | Usuario que registro o actualizo el ranking. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Reglas:

- La combinación `weight_class_id + gender + position + status activo` no debe duplicarse.
- La vista pública debe filtrar rankings por categoría de peso y género.
- Si la categoría de peso ya guarda género, el campo `gender` puede validarse contra esa categoría para evitar inconsistencias.

Regla sugerida:

- Una categoría no deberia tener dos peleadores activos con la misma `position`.

### `venues`

Lugares donde se realizaran eventos.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(150) | Nombre del lugar. |
| `slug` | string(180), unique | Texto único y limpio para URL pública de la sede; ejemplo: `coliseo-julio-borelli`. |
| `city_id` | foreignId nullable | Ciudad donde se ubica la sede. El país se obtiene desde `cities.country_id`. |
| `address` | string(255), nullable | Dirección. |
| `latitude` | decimal(10,7), nullable | Latitud para mapa. |
| `longitude` | decimal(10,7), nullable | Longitud para mapa. |
| `capacity` | unsignedInteger, nullable | Capacidad aproximada. |
| `image` | string(255), nullable | Imagen del lugar. |
| `status` | tinyInteger default 1 | Estado activo/inactivo. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

### `events`

Cartelera o evento principal visible en la landing. Los eventos pasados, actuales y próximos pueden seguir visibles mientras estén publicados; la fecha no debe ocultarlos automáticamente. El administrador controla la visibilidad cambiando el estado. Los textos Próximo, Actual o Finalizado se calculan para la vista pública desde `starts_at` y la fecha actual, pero no reemplazan el estado técnico.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `uuid` | uuid, unique | Identificador público estable. |
| `name` | string(180) | Nombre del evento. |
| `slug` | string(200), unique | Texto único y limpio para URL pública del evento; ejemplo: `mma-night-santa-cruz-2026`. |
| `subtitle` | string(200), nullable | Texto corto de apoyo. |
| `description` | text, nullable | Descripción pública del evento. |
| `venue_id` | foreignId nullable | Lugar del evento. |
| `starts_at` | datetime | Fecha y hora de inicio. |
| `doors_open_at` | datetime, nullable | Apertura de puertas si aplica. |
| `timezone` | string(60), nullable | Zona horaria del evento. |
| `poster_image` | string(255), nullable | Poster principal. |
| `banner_image` | string(255), nullable | Banner para landing. |
| `stream_url` | string(500), nullable | Link de transmision si aplica. |
| `ticket_url` | string(500), nullable | Link de entradas si aplica. |
| `status` | tinyInteger default 0 | 0 borrador, 1 publicado, 2 archivado, 3 cancelado. |
| `is_featured` | boolean default false | Marca para destacar en la home. |
| `published_at` | timestamp, nullable | Fecha de publicación. |
| `created_by` | foreignId nullable | Usuario creador. |
| `updated_by` | foreignId nullable | Ultimo usuario editor. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

### `fights`

Combate programado dentro de un evento.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `uuid` | uuid, unique | Identificador público estable. |
| `event_id` | foreignId | Evento al que pertenece. |
| `weight_class_id` | foreignId nullable | Categoría de peso. |
| `corner_x_fighter_id` | foreignId | Peleador asignado a la esquina X. |
| `corner_y_fighter_id` | foreignId | Peleador asignado a la esquina Y. |
| `title` | string(180), nullable | Nombre opcional del combate. |
| `fight_order` | unsignedInteger default 0 | Orden dentro de la cartelera. |
| `card_type` | enum/string | `main`, `prelim`, `early_prelim`. |
| `scheduled_rounds` | unsignedTinyInteger default 3 | Rounds pactados. |
| `round_duration_seconds` | unsignedSmallInteger default 300 | Duracion de cada round. |
| `is_title_fight` | boolean default false | Indica si es pelea por título. |
| `status` | tinyInteger default 0 | 0 programada, 1 en vivo, 2 finalizada, 3 cancelada. |
| `starts_at` | datetime, nullable | Hora estimada del combate. |
| `promo_image` | string(255), nullable | Imagen promocional del combate. |
| `description` | text, nullable | Resumen o contexto de la pelea. |
| `created_by` | foreignId nullable | Usuario creador. |
| `updated_by` | foreignId nullable | Ultimo usuario editor. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

Regla sugerida:

- `corner_x_fighter_id` y `corner_y_fighter_id` no deben apuntar al mismo peleador.
- Un combate debe pertenecer siempre a un evento.

### `fight_results`

Resultado oficial de un combate. Se separa de `fights` para permitir registrar o corregir resultado sin ensuciar la programacion.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `fight_id` | foreignId unique | Combate relacionado. |
| `winner_fighter_id` | foreignId nullable | Ganador si existe. |
| `result_type` | enum/string | `ko_tko`, `submission`, `decision`, `draw`, `no_contest`, `disqualification`. |
| `method_detail` | string(180), nullable | Detalle: rear naked choke, unanimous decision, etc. |
| `ending_round` | unsignedTinyInteger nullable | Round en el que termino. |
| `ending_time` | string(10), nullable | Tiempo dentro del round, formato `mm:ss`. |
| `judges_score` | json, nullable | Tarjetas de jueces si aplica. |
| `notes` | text, nullable | Observaciones administrativas. |
| `official_at` | timestamp, nullable | Fecha de oficializacion. |
| `created_by` | foreignId nullable | Usuario que registro el resultado. |
| `updated_by` | foreignId nullable | Ultimo editor. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `fighter_media`

Galeria de imágenes o videos asociada a peleadores.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `fighter_id` | foreignId | Peleador relacionado. |
| `type` | string(30) | `image`, `video`, `document`. |
| `path` | string(500) | Ruta del archivo o URL. |
| `thumbnail_path` | string(500), nullable | Miniatura si aplica. |
| `title` | string(180), nullable | Título interno o público. |
| `alt_text` | string(180), nullable | Texto alternativo para accesibilidad/SEO. |
| `display_order` | integer default 0 | Orden en galeria. |
| `is_featured` | boolean default false | Marca como imagen destacada. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `event_media`

Galeria de imágenes, videos o piezas visuales asociadas a un evento. Esta tabla puede crecer mucho porque un solo evento puede tener muchos registros: posters, banners, fotos de pesaje, careos, conferencias, backstage, llegada de peleadores, highlights y material promocional. Cada registro representa un archivo individual, por lo que la relacion esperada es `events 1 -> N event_media`.

Esta tabla debe pensarse para consultas paginadas y filtradas por evento, categoría, tipo de archivo y estado de publicación. No conviene cargar toda la galeria de un evento sin paginación si el evento tiene cientos o miles de archivos.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `event_id` | foreignId | Evento relacionado. |
| `type` | string(30) | `image`, `video`, `poster`, `banner`. |
| `category` | string(40) | Contexto del archivo: `poster`, `banner`, `weigh_in`, `faceoff`, `press_conference`, `backstage`, `fight_week`, `highlight`, `gallery`, `other`. |
| `path` | string(500) | Ruta o URL del archivo. |
| `thumbnail_path` | string(500), nullable | Miniatura. |
| `title` | string(180), nullable | Título. |
| `alt_text` | string(180), nullable | Texto alternativo. |
| `caption` | string(300), nullable | Texto corto para mostrar debajo de la imagen o video. |
| `file_size` | unsignedBigInteger nullable | Peso del archivo en bytes, util para auditoría y optimizacion. |
| `mime_type` | string(100), nullable | Tipo MIME: `image/jpeg`, `video/mp4`, etc. |
| `taken_at` | datetime, nullable | Fecha en la que se capturo el material si aplica. |
| `display_order` | integer default 0 | Orden de visualizacion. |
| `is_featured` | boolean default false | Imagen destacada. |
| `status` | tinyInteger default 1 | Estado activo/inactivo. |
| `created_by` | foreignId nullable | Usuario administrador que subio o registro el archivo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Consultas esperadas:

- Ultimos archivos activos de un evento.
- Galeria de pesaje de un evento: `event_id + category = weigh_in`.
- Galeria de careos de un evento: `event_id + category = faceoff`.
- Imágenes destacadas para portada de evento: `event_id + is_featured`.
- Paginación administrativa por evento, tipo, categoría y estado.

### `news_posts`

Módulo opcional para novedades, comunicados y contenido editorial.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `title` | string(200) | Título de la noticia. |
| `slug` | string(220), unique | Texto único y limpio para URL pública del post, generado desde el título; ejemplo: `pesaje-oficial-del-evento`. |
| `excerpt` | string(300), nullable | Resumen corto. |
| `body` | longText | Contenido principal. |
| `cover_image` | string(255), nullable | Imagen principal. |
| `status` | tinyInteger default 0 | 0 borrador, 1 publicado, 2 archivado. |
| `published_at` | timestamp, nullable | Fecha de publicación. |
| `created_by` | foreignId nullable | Autor administrativo. |
| `updated_by` | foreignId nullable | Ultimo editor. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

## Tablas de comunicacion y contenido generico

### `notifications`

Se puede conservar con ajustes menores para MMA.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `title` | string(255), nullable | Título del aviso. |
| `description` | text, nullable | Mensaje principal. |
| `image` | string(255), nullable | Imagen principal. |
| `link` | string(500), nullable | Link interno o externo. |
| `target_type` | string(30), nullable | `all`, `admins`, `fans`, `individual`, `role`. Reemplaza `type_user`. |
| `channel_type` | string(20), nullable | `push`, `normal`, `both`. Reemplaza o mantiene `type`. |
| `delivery_platform` | string(20) default `all` | `web`, `mobile`, `all`. |
| `metadata` | json, nullable | Relacion opcional con evento, pelea o noticia. |
| `scheduled_at` | datetime, nullable | Fecha desde la cual se muestra/envia. |
| `deadline` | date, nullable | Fecha de expiracion. |
| `push_sent_at` | datetime, nullable | Ultimo envio push exitoso. |
| `push_last_error_at` | datetime, nullable | Ultimo fallo de envio. |
| `push_last_error_message` | string(500), nullable | Mensaje del ultimo error. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `creator_user_id` | foreignId nullable | Usuario creador. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `notification_reads`

Se conserva igual.

| Campo | Uso |
| --- | --- |
| `id` | Identificador único. |
| `notification_id` | Notificacion leida. |
| `user_id` | Usuario que la leyo. |
| `read_at` | Fecha de lectura. |
| `created_at`, `updated_at` | Auditoría. |

### `fcm_tokens`

Se conserva practicamente igual porque servira tanto para Web Push como para la futura app Android.

| Campo | Uso |
| --- | --- |
| `id` | Identificador único. |
| `user_id` | Usuario propietario. |
| `token` | Token Firebase único. |
| `platform` | `mobile` o `web`. |
| `device_identifier` | Identificador reportado por cliente. |
| `device_name` | Nombre legible del dispositivo. |
| `browser` | Navegador si aplica Web Push. |
| `delivery_platform` | `android`, `ios`, `web`, `unknown`. |
| `last_seen_at` | Ultima vigencia reportada. |
| `last_sent_at` | Ultimo intento de envio. |
| `last_error_at` | Ultimo error de Firebase. |
| `last_error_message` | Detalle del error. |
| `invalidated_at` | Fecha en que se invalido el token. |
| `is_active` | Habilitado para envios futuros. |
| `created_at`, `updated_at` | Auditoría. |

### `app_update`

Se adapta para la futura app Android. La tabla actual tiene campos para Android e iOS; en la primera etapa se puede simplificar a Android y dejar iOS solo si realmente se planifica.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `platform` | string(20) | `android`, `ios`, `web`. En primera etapa: `android`. |
| `current_version` | string(30), nullable | Version actual recomendada. |
| `minimum_version` | string(30), nullable | Version minima permitida. |
| `new_version` | string(30), nullable | Version disponible para actualizar. |
| `update_message` | string(255), nullable | Mensaje mostrado al usuario. |
| `download_url` | string(500), nullable | Link de descarga o Play Store. |
| `is_required` | boolean default false | Indica si la actualizacion es obligatoria. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. |
| `created_by` | foreignId nullable | Usuario administrador que registro la version. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `contact_messages`

Reemplazo sugerido para `customer_service`.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(100), nullable | Nombre de quien escribe. |
| `lastname` | string(100), nullable | Apellido. |
| `email` | string(150), nullable | Correo de contacto. |
| `number_phone` | string(25), nullable | Teléfono. |
| `company` | string(120), nullable | Empresa, academia o promotora. |
| `country` | string(80), nullable | País. |
| `city` | string(120), nullable | Ciudad. |
| `subject` | string(200), nullable | Asunto. |
| `message` | text | Mensaje recibido. |
| `type` | string(40), nullable | `contact`, `support`, `partnership`, `fighter_application`. |
| `status` | tinyInteger default 0 | 0 pendiente, 1 revisado, 2 cerrado. |
| `assigned_user_id` | foreignId nullable | Administrador responsable. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### Idiomas con `resources/lang`

La tabla actual `translations` debe eliminarse porque no corresponde a traducciones de idioma: registraba traslados de estaciones del proyecto anterior. Para esta nueva app, la primera decision es mantener textos de interfaz mediante archivos `resources/lang`.

Uso recomendado:

| Recurso | Descripción |
| --- | --- |
| `resources/lang/es/*.php` y `resources/lang/es.json` | Textos principales en español. |
| `resources/lang/en/*.php` y `resources/lang/en.json` | Textos en ingles si se mantiene soporte multidioma. |
| Claves de idioma por módulo | Ejemplo: `events.title`, `fighters.nickname`, `fights.main_card`. |

Nota de arquitectura:

- No crear tabla de traducciones de contenido en la primera etapa.
- Si mas adelante se necesita traducir descripciones de eventos, peleadores o noticias desde el panel, se evaluara una tabla nueva y generica desde cero.

## Nuevo módulo de suscripciones y pagos

Las tablas actuales `subscriptions`, `details_subscriptions` y `users_subscriptions` deben eliminarse cuando ya no tengan dependencias activas. La nueva app necesita un modelo mas claro, separado en planes, suscripciones contratadas y pagos.

### `subscription_plans`

Planes disponibles para usuarios `subscriber`. En la primera etapa los planes pueden dar descuentos en entradas y beneficios de contenido/experiencia. Los descuentos iniciales sugeridos son 10%, 15% y 20%, según el nivel del plan.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `name` | string(120) | Nombre del plan: Basico, Premium, Promotora, etc. |
| `slug` | string(140), unique | Texto único y limpio para identificar el plan en URLs o lógica interna; ejemplo: `plan-premium-mensual`. |
| `description` | text, nullable | Descripción comercial o administrativa. |
| `price` | decimal(10,2) | Precio base. |
| `currency` | string(3) default `BOB` | Moneda del cobro. |
| `billing_period` | string(20) | `monthly`, `quarterly`, `yearly`, `one_time`. |
| `trial_days` | unsignedSmallInteger default 0 | Dias de prueba si aplica. |
| `ticket_discount_percentage` | decimal(5,2) default 0 | Porcentaje de descuento en entradas: ejemplo 10.00, 15.00 o 20.00. |
| `features` | json, nullable | Lista de beneficios del plan. |
| `limits` | json, nullable | Limites: eventos, usuarios, anuncios, etc. |
| `is_public` | boolean default true | Indica si se muestra al público. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo, 2 archivado. |
| `created_at`, `updated_at`, `deleted_at` | timestamps/soft delete | Auditoría y eliminación lógica. |

Ejemplo inicial de planes:

| Plan | Descuento entradas | Beneficios sugeridos |
| --- | --- | --- |
| Basico | 10% | Preventa, notificaciones de eventos, historial de compras/reservas. |
| Plus | 15% | Acceso temprano, contenido exclusivo, sorteos o promociones. |
| Premium | 20% | Mayor descuento, prioridad en entradas, promociones VIP y beneficios especiales. |

### `user_subscriptions`

Suscripción activa o histórica de un usuario a un plan.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `user_id` | foreignId | Usuario suscrito. |
| `subscription_plan_id` | foreignId | Plan contratado. |
| `starts_at` | datetime | Inicio de vigencia. |
| `ends_at` | datetime, nullable | Fin de vigencia. |
| `trial_ends_at` | datetime, nullable | Fin de prueba si aplica. |
| `cancelled_at` | datetime, nullable | Fecha de cancelacion. |
| `renewal_at` | datetime, nullable | Próxima renovacion esperada. |
| `status` | tinyInteger default 0 | 0 pendiente, 1 activa, 2 vencida, 3 cancelada, 4 suspendida. |
| `source` | string(40), nullable | `web`, `android`, `manual`, `admin`. |
| `metadata` | json, nullable | Datos extra del proveedor o reglas aplicadas. |
| `created_by` | foreignId nullable | Administrador que creo la suscripción manualmente. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

### `subscription_payments`

Pagos asociados a una suscripción. En la primera etapa se registrarán manualmente luego de coordinar por WhatsApp, teléfono o formulario. Más adelante esta misma tabla puede almacenar pagos por pasarela web, QR, tarjeta o compras desde Android.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `user_subscription_id` | foreignId nullable | Suscripción relacionada. |
| `user_id` | foreignId | Usuario pagador. |
| `amount` | decimal(10,2) | Monto cobrado. |
| `currency` | string(3) default `BOB` | Moneda. |
| `payment_method` | string(40) | `cash`, `qr`, `card`, `bank_transfer`, `play_store`, `courtesy`. |
| `provider` | string(80), nullable | Pasarela o proveedor externo. |
| `provider_transaction_id` | string(180), nullable | ID de transaccion externa. |
| `payment_url` | string(500), nullable | Link de pago si aplica. |
| `payment_proof_path` | string(255), nullable | Ruta del comprobante de pago cargado por el usuario o por administración. |
| `payment_proof_mime` | string(100), nullable | Tipo MIME del comprobante guardado. |
| `payment_proof_size` | unsignedInteger nullable | Tamaño final del archivo guardado en bytes. |
| `paid_at` | datetime, nullable | Fecha de pago confirmado. |
| `expires_at` | datetime, nullable | Vencimiento del link/intento de pago. |
| `status` | tinyInteger default 0 | 0 pendiente, 1 pagado, 2 fallido, 3 reembolsado, 4 vencido. |
| `notes` | text, nullable | Observaciones administrativas. |
| `metadata` | json, nullable | Respuesta del proveedor o datos técnicos adicionales. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Reglas de comprobante:

- Aceptar solo `jpg`, `jpeg`, `png` y `pdf`.
- Tamaño máximo: 5 MB.
- Las imágenes deben optimizarse antes de guardarse mediante `App\Services\ImageUploadOptimizer` o servicio equivalente centralizado, aplicando antes una validación que limite los formatos a `jpg`, `jpeg` y `png` para este flujo.
- Los PDF se validan por tipo/tamaño y se guardan sin compresión inicial.
- Guardar los comprobantes en un directorio privado o protegido.
- Servir comprobantes solo mediante controlador autenticado con permisos administrativos.
- No exponer comprobantes por URL pública directa.

### `purchase_requests`

Solicitudes creadas desde el formulario público de contacto/compra. Este módulo permite que administración gestione interesados en eventos, planes, reservas o compras manuales sin mezclar estas solicitudes con soporte general.

Relaciones posibles:

```text
events 1 -> N purchase_requests
subscription_plans 1 -> N purchase_requests
users 1 -> N purchase_requests
```

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `uuid` | uuid, unique | Identificador público estable. |
| `user_id` | foreignId nullable | Usuario relacionado si inició sesión. |
| `event_id` | foreignId nullable | Evento relacionado si la solicitud nace desde una página de evento. |
| `subscription_plan_id` | foreignId nullable | Plan relacionado si la solicitud nace desde suscripción. |
| `contact_name` | string(150) | Nombre de la persona interesada. |
| `contact_email` | string(150), nullable | Correo de contacto. |
| `contact_phone` | string(30), nullable | Teléfono de contacto. |
| `contact_whatsapp` | string(30), nullable | Número de WhatsApp si difiere del teléfono. |
| `preferred_channel` | string(30) | Canal preferido: `whatsapp`, `phone`, `email`, `form`. |
| `request_type` | string(40) | Tipo: `event_ticket`, `subscription_plan`, `general_contact`, `other`. |
| `message` | text, nullable | Mensaje enviado por el visitante o suscriptor. |
| `payment_proof_path` | string(255), nullable | Comprobante adjunto cuando el visitante ya realizó un pago manual. |
| `payment_proof_mime` | string(100), nullable | Tipo MIME del comprobante. |
| `payment_proof_size` | unsignedInteger nullable | Tamaño final del archivo guardado en bytes. |
| `status` | tinyInteger default 0 | 0 pendiente, 1 en revisión, 2 contactado, 3 convertido, 4 cerrado, 5 rechazado. |
| `assigned_to` | foreignId nullable | Usuario interno responsable de atender la solicitud. |
| `handled_by` | foreignId nullable | Usuario que cerró o convirtió la solicitud. |
| `handled_at` | datetime, nullable | Fecha de cierre, conversión o atención final. |
| `notes` | text, nullable | Observaciones internas. |
| `metadata` | json, nullable | Datos técnicos del origen, IP, UTM o página desde donde se envió. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Reglas:

- El formulario público debe crear registros en esta tabla.
- Administración debe gestionarlos en una vista propia con filtros, tabla, acciones, modal de detalle y SweetAlert 2.
- Si la solicitud se convierte en pago o suscripción, debe enlazarse o dejar trazabilidad en `metadata` o en una relación futura.
- Los comprobantes adjuntos siguen las mismas reglas que `subscription_payments`: imágenes optimizadas y PDF sin compresión inicial.
- Los comprobantes de solicitudes también se almacenan en privado y se revisan desde administración.

### `subscription_plan_features`

Tabla opcional si se prefiere administrar beneficios como registros en vez de JSON.

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único. |
| `subscription_plan_id` | foreignId | Plan relacionado. |
| `name` | string(120) | Nombre del beneficio. |
| `description` | string(255), nullable | Detalle del beneficio. |
| `feature_key` | string(100), nullable | Clave técnica: `premium_events`, `exclusive_notifications`. |
| `value` | string(120), nullable | Valor del limite o beneficio. |
| `display_order` | integer default 0 | Orden visual. |
| `status` | tinyInteger default 1 | Estado activo/inactivo. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

## Tablas opcionales futuras

### `ticket_links`

Tabla opcional para administrar varios enlaces de compra, reserva o acceso asociados a un mismo evento. Se usa cuando la landing pública debe mostrar botones como "Comprar entrada general", "Comprar VIP", "Reservar por WhatsApp" o "Ver streaming", pero la transaccion ocurre fuera del sistema.

Cada registro representa un canal o enlace independiente para el evento. Por ejemplo, un mismo evento puede tener un enlace para entrada general, otro para VIP, otro para reservas por WhatsApp y otro para streaming. Esto evita limitar el evento a un solo campo `ticket_url`.

Esta tabla no registra pagos internos ni genera entradas propias. Solo guarda enlaces externos o canales de venta/reserva. Si mas adelante el sistema vende tickets directamente, se deberia crear otro módulo con ordenes, tipos de tickets, pagos, QR y control de ingreso.

Relacion esperada:

```text
events 1 -> N ticket_links
```

| Campo | Tipo sugerido | Descripción |
| --- | --- | --- |
| `id` | big integer | Identificador único del enlace. |
| `event_id` | foreignId | Evento al que pertenece el enlace. |
| `provider_name` | string(120) | Nombre del proveedor o canal: Ticketeg, Passline, WhatsApp, boleteria, streaming, etc. |
| `label` | string(120) | Texto visible del boton o enlace: Entrada general, VIP, Reservar por WhatsApp, Ver streaming. |
| `sale_channel` | string(40) | Tipo de canal: `external_platform`, `whatsapp`, `physical_box_office`, `streaming`, `other`. |
| `url` | string(500) | Link de compra, reserva, información o streaming. |
| `price_from` | decimal(10,2), nullable | Precio minimo mostrado al público si aplica. |
| `currency` | string(3), nullable | Moneda del precio: `BOB`, `USD`, etc. |
| `starts_at` | datetime, nullable | Fecha desde la cual el enlace debe mostrarse. |
| `ends_at` | datetime, nullable | Fecha hasta la cual el enlace debe mostrarse. |
| `display_order` | integer default 0 | Orden de aparicion de los botones en la landing del evento. |
| `status` | tinyInteger default 1 | 0 inactivo, 1 activo. Solo los activos se muestran al público. |
| `created_at`, `updated_at` | timestamps | Auditoría base. |

Ejemplo de registros:

| `event_id` | `provider_name` | `label` | `sale_channel` | `url` | `price_from` | `currency` | `status` |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 10 | Ticketeg | Entrada general | external_platform | `https://ticketeg.com/evento-x` | 50.00 | BOB | activo |
| 10 | Ticketeg | VIP | external_platform | `https://ticketeg.com/evento-x-vip` | 150.00 | BOB | activo |
| 10 | WhatsApp | Reservas por WhatsApp | whatsapp | `https://wa.me/59100000000` | null | BOB | activo |
| 10 | Streaming | Ver online | streaming | `https://stream.com/evento-x` | 30.00 | BOB | activo |

Reglas sugeridas:

- Un evento puede tener cero, uno o muchos `ticket_links`.
- En la landing solo se muestran enlaces con `status = 1`.
- Si `starts_at` y `ends_at` tienen valor, el enlace solo se muestra dentro de ese rango.
- Los botones se ordenan por `display_order`.

### Venta interna de tickets

Módulo futuro para vender entradas dentro del sistema. No reemplaza `ticket_links`: ambos pueden coexistir. `ticket_links` cubre canales externos; la venta interna cubrira compras, pagos, entradas generadas y validación de ingreso.

Tablas sugeridas para una etapa posterior:

| Tabla | Uso |
| --- | --- |
| `ticket_types` | Tipos de entrada por evento: General, VIP, Graderia, Mesa, Streaming, etc. |
| `ticket_orders` | Orden o compra realizada por un usuario/suscriptor. |
| `ticket_order_items` | Detalle de entradas compradas dentro de una orden. |
| `ticket_payments` | Pago asociado a la compra interna de tickets. |
| `tickets` | Entrada individual generada, idealmente con código único o QR. |
| `ticket_checkins` | Registro de validación de ingreso en puerta o acceso digital. |

Regla de etapa:

- Primera etapa: usar `ticket_links` y pagos manuales para suscripciones.
- Etapa posterior: disenar e implementar venta interna de tickets con pagos, QR y validación.

### `sponsors`

Solo si se mostraran auspiciadores en eventos o landing.

| Campo | Uso |
| --- | --- |
| `id` | Identificador único. |
| `name` | Nombre del sponsor. |
| `logo` | Logo. |
| `website_url` | Sitio web. |
| `status` | Estado activo/inactivo. |
| `created_at`, `updated_at` | Auditoría. |

### `event_sponsor`

Tabla pivote entre eventos y auspiciadores.

| Campo | Uso |
| --- | --- |
| `event_id` | Evento. |
| `sponsor_id` | Sponsor. |
| `display_order` | Orden visual. |

## Indices y restricciones sugeridas

- `users.email` único.
- `system_settings.slug` único.
- `countries.iso_code` index.
- `cities.country_id` index.
- `cities.name`, `country_id` index compuesto.
- `weight_classes.slug` único.
- `weight_classes.is_default` index.
- `fighter_teams.slug` único.
- `fighter_teams.type` index.
- `fighter_teams.city_id` index.
- `fighters.slug` único.
- `fighters.uuid` único.
- `fighters.country_id` index.
- `fighters.weight_class_id` index.
- `fighters.fighter_team_id` index.
- `fighter_rankings.fighter_id`, `weight_class_id` index compuesto.
- `fighter_rankings.weight_class_id`, `gender`, `status`, `position` index compuesto.
- `venues.city_id` index.
- `events.slug` único.
- `events.uuid` único.
- `events.starts_at` index.
- `events.status` index.
- `events.is_featured` index.
- `event_media.event_id` index.
- `event_media.event_id`, `category`, `status` index compuesto.
- `event_media.event_id`, `type`, `status` index compuesto.
- `event_media.event_id`, `is_featured`, `status` index compuesto.
- `event_media.event_id`, `display_order` index compuesto.
- `event_media.event_id`, `created_at` index compuesto para paginación cronologica.
- `fights.event_id` index.
- `fights.weight_class_id` index.
- `fights.corner_x_fighter_id` index.
- `fights.corner_y_fighter_id` index.
- `fights.status` index.
- `fights.starts_at` index.
- `fight_results.fight_id` único.
- `notifications.scheduled_at` index.
- `notifications.status` index.
- `fcm_tokens.token` único.
- `fcm_tokens.user_id`, `platform`, `is_active` index compuesto.
- `subscription_plans.slug` único.
- `subscription_plans.status` index.
- `user_subscriptions.user_id` index.
- `user_subscriptions.subscription_plan_id` index.
- `user_subscriptions.status` index.
- `user_subscriptions.ends_at` index.
- `subscription_payments.user_subscription_id` index.
- `subscription_payments.user_id` index.
- `subscription_payments.provider_transaction_id` index.
- `subscription_payments.status` index.
- `purchase_requests.status` index.
- `purchase_requests.request_type` index.
- `purchase_requests.event_id` index.
- `purchase_requests.subscription_plan_id` index.
- `purchase_requests.assigned_to` index.
- `purchase_requests.created_at` index.

## Estados recomendados

Para evitar confusiones con varios campos `state`, conviene estandarizar:

- `status` para estado de registros.
- `state` solo se conserva en `users` para bloqueo o habilitacion de acceso.
- `is_featured` para destacado.
- `published_at` para publicación real.
- `delete` se conserva en `users` para baja lógica según regla de negocio.
- `deleted_at` para eliminación lógica en entidades del dominio cuando se use SoftDeletes.

Estados sugeridos:

| Contexto | Valores |
| --- | --- |
| Registros simples | 0 inactivo, 1 activo |
| Eventos | 0 borrador, 1 publicado, 2 archivado, 3 cancelado |
| Combates | 0 programado, 1 en vivo, 2 finalizado, 3 cancelado |
| Solicitudes/contacto general | 0 pendiente, 1 revisado, 2 cerrado |
| Solicitudes de compra | 0 pendiente, 1 en revisión, 2 contactado, 3 convertido, 4 cerrado, 5 rechazado |
| Usuarios `state` | 0 bloqueado/inactivo, 1 activo |
| Usuarios `delete` | 0 visible, 1 eliminado logicamente |
| Suscripciones | 0 pendiente, 1 activa, 2 vencida, 3 cancelada, 4 suspendida |
| Pagos | 0 pendiente, 1 pagado, 2 fallido, 3 reembolsado, 4 vencido |

## Orden sugerido de migracion

1. Mantener tablas Laravel base, Spatie, usuarios y notificaciones.
2. Definir y seedear roles Spatie en ingles: `super_manager`, `admin`, `manager`, `publisher`, `sales`, `checkin`, `support`, `subscriber`.
3. Crear `countries` y `cities`.
4. Crear `system_settings` con valores iniciales de `Combate Real`.
5. Limpiar/adaptar `users` para login administrativo y login de suscriptores.
6. Crear `weight_classes` con categorías base tipo UFC y soporte de personalizacion para la promotora.
7. Crear `fighter_teams`.
8. Crear `fighters`, `fighter_stats` y `fighter_rankings`.
9. Crear `venues`.
10. Crear `events`.
11. Crear `fights`.
12. Crear `fight_results`.
13. Crear `fighter_media` y `event_media`.
14. Adaptar `notifications`, `notification_reads`, `fcm_tokens` y `app_update` pensando en web y Android.
15. Crear la nueva estructura de `subscription_plans`, `user_subscriptions`, `subscription_payments` y `purchase_requests`.
16. Mantener `ticket_links` para enlaces externos y documentar venta interna de tickets como etapa posterior.
17. Adaptar `customer_service` a `contact_messages`.
18. Eliminar `translations` y usar `resources/lang` para textos de interfaz.
19. Eliminar tablas heredadas del dominio clima, incluyendo `stations` y tablas dependientes, cuando ya no tengan dependencias activas.

## Pendientes para próxima revision

- Definir permisos exactos por rol Spatie.
- Definir si `subscriber` tendrá beneficios globales, por plan o por evento.
- Definir el flujo de registro de suscriptores en Android y web.
- Definir el modelo futuro de venta interna de tickets antes de implementarlo.
