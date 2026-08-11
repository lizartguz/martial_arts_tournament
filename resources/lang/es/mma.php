<?php

$en = require dirname(__DIR__).'/en/mma.php';

$en['authorization'] = ['protected' => 'Protegido'];
$en['roles']['names'] = ['super_manager' => 'Root', 'admin' => 'Administrador', 'manager' => 'Gestor', 'publisher' => 'Publicador', 'sales' => 'Ventas', 'checkin' => 'Control de ingreso', 'support' => 'Soporte', 'subscriber' => 'Suscriptor'];
$en['permissions']['actions'] = ['access' => 'Acceder a', 'view' => 'Ver', 'create' => 'Crear', 'update' => 'Editar', 'delete' => 'Eliminar', 'publish' => 'Publicar', 'upload_proof' => 'Cargar comprobante de', 'confirm' => 'Confirmar', 'cancel' => 'Cancelar', 'assign' => 'Asignar', 'assign_roles' => 'Asignar roles de', 'close' => 'Cerrar', 'send' => 'Enviar', 'download' => 'Descargar', 'export' => 'Exportar'];
$en['permissions']['modules'] = ['dashboard' => 'panel principal', 'authorization' => 'roles y permisos', 'system_settings' => 'configuración del sistema', 'roles' => 'roles', 'users' => 'usuarios', 'venues' => 'sedes', 'events' => 'eventos', 'fights' => 'combates', 'fight_results' => 'resultados de combates', 'rankings' => 'rankings', 'fighters' => 'peleadores', 'fighter_teams' => 'equipos y gimnasios', 'weight_classes' => 'categorías de peso', 'event_media' => 'multimedia de eventos', 'fighter_media' => 'multimedia de peleadores', 'news' => 'noticias', 'landing' => 'landing pública', 'sponsors' => 'sponsors', 'subscription_plans' => 'planes de suscripción', 'subscribers' => 'suscriptores', 'user_subscriptions' => 'suscripciones de usuarios', 'subscription_payments' => 'pagos de suscripción', 'purchase_requests' => 'solicitudes de compra', 'ticket_links' => 'enlaces de tickets', 'ticket_orders' => 'órdenes de tickets', 'ticket_checkins' => 'validación de ingreso', 'notifications' => 'notificaciones', 'reports.events' => 'reportes de eventos', 'reports.subscriptions' => 'reportes de suscripciones', 'reports.sales' => 'reportes de ventas', 'reports' => 'reportes', 'logs' => 'logs', 'support_messages' => 'mensajes de soporte', 'subscriber.dashboard' => 'panel del suscriptor', 'subscriber.purchases' => 'compras del suscriptor', 'subscriber.events' => 'eventos del suscriptor', 'subscriber.subscription' => 'suscripción del usuario', 'subscriber.profile' => 'perfil del suscriptor'];
$en['menu'] = ['dashboard' => 'Inicio', 'events' => ['group' => 'Eventos', 'events' => 'Eventos', 'fights' => 'Combates', 'results' => 'Resultados', 'media' => 'Multimedia de eventos'], 'fighters' => ['group' => 'Peleadores', 'fighters' => 'Peleadores', 'teams' => 'Equipos y gimnasios', 'weight_classes' => 'Categorías de peso', 'rankings' => 'Rankings'], 'content' => ['group' => 'Contenido', 'news' => 'Noticias', 'landing' => 'Landing page', 'sponsors' => 'Sponsors'], 'commerce' => ['group' => 'Suscripciones y pagos', 'plans' => 'Planes', 'subscribers' => 'Suscriptores', 'subscriptions' => 'Suscripciones', 'payments' => 'Pagos', 'purchase_requests' => 'Solicitudes de compra'], 'tickets' => ['group' => 'Tickets', 'links' => 'Enlaces de tickets', 'orders' => 'Órdenes de tickets', 'checkins' => 'Validación de ingreso'], 'security' => ['group' => 'Usuarios y seguridad', 'users' => 'Usuarios', 'roles' => 'Roles y permisos'], 'settings' => ['group' => 'Configuración', 'system' => 'Ajustes del sistema', 'notifications' => 'Notificaciones', 'logs' => 'Logs']];
$en['admin']['common'] = ['active' => 'Activo', 'inactive' => 'Inactivo', 'empty' => 'No hay registros para mostrar.', 'developed_by' => 'Desarrollado por', 'not_available' => 'No disponible', 'yes' => 'Sí', 'no' => 'No', 'filters' => ['search' => 'Buscar', 'status' => 'Estado', 'all' => 'Todos', 'per_page' => 'Por página', 'clear' => 'Limpiar'], 'columns' => ['actions' => 'Acciones']];
$en['admin']['dashboard'] = ['page_title' => 'Panel principal', 'subtitle' => 'Resumen operativo inicial de eventos, peleadores, solicitudes y pagos.', 'events_title' => 'Eventos publicados', 'events_subtitle' => 'Ordenados por destacados y cercanía de fecha.', 'cards' => ['published_events' => 'Eventos publicados', 'active_fighters' => 'Peleadores activos', 'pending_purchase_requests' => 'Solicitudes pendientes', 'pending_payments' => 'Pagos pendientes'], 'columns' => ['event' => 'Evento', 'venue' => 'Sede', 'date' => 'Fecha', 'featured' => 'Destacado']];
$en['admin']['events'] = ['page_title' => 'Eventos', 'subtitle' => 'Gestiona eventos públicos, borradores, fechas, sedes, slugs e imágenes principales.', 'table_title' => 'Listado de eventos', 'table_subtitle' => 'Filtra, crea, edita, publica o elimina eventos sin dependencias activas.', 'create' => 'Nuevo evento', 'edit' => 'Editar evento', 'delete_title' => 'Eliminar evento', 'delete_warning' => 'Esta acción eliminará el evento:', 'search_placeholder' => 'Nombre, subtítulo o slug...', 'content_summary' => ':fights combates · :tickets enlaces', 'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.', 'filters' => ['venue' => 'Sede', 'featured' => 'Destacado', 'from' => 'Desde', 'to' => 'Hasta'], 'columns' => ['event' => 'Evento', 'venue' => 'Sede', 'date' => 'Fecha', 'content' => 'Contenido', 'status' => 'Estado', 'featured' => 'Destacado'], 'status' => ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado', 'cancelled' => 'Cancelado'], 'actions' => ['publish' => 'Publicar'], 'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'subtitle' => 'Subtítulo', 'description' => 'Descripción', 'venue_id' => 'Sede', 'starts_at' => 'Fecha y hora del evento', 'doors_open_at' => 'Apertura de puertas', 'timezone' => 'Zona horaria', 'stream_url' => 'URL de transmisión', 'ticket_url' => 'URL de tickets', 'status' => 'Estado', 'is_featured' => 'Marcar como destacado', 'poster_image' => 'Póster', 'banner_image' => 'Banner'], 'messages' => ['created' => 'Evento registrado correctamente.', 'updated' => 'Evento actualizado correctamente.', 'published' => 'Evento publicado correctamente.', 'deleted' => 'Evento eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un evento con combates, enlaces de tickets o solicitudes asociadas.']];
$en['admin']['event_media'] = ['page_title' => 'Multimedia de eventos', 'subtitle' => 'Gestiona imágenes y enlaces de video asociados a eventos.', 'table_title' => 'Listado multimedia', 'table_subtitle' => 'Filtra, sube, edita o elimina piezas multimedia públicas de eventos.', 'create' => 'Nueva pieza', 'edit' => 'Editar pieza', 'delete_title' => 'Eliminar multimedia', 'delete_warning' => 'Esta acción eliminará la pieza multimedia:', 'search_placeholder' => 'Título, descripción o evento...', 'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.', 'featured' => 'Destacado', 'untitled' => 'Sin título', 'filters' => ['event' => 'Evento', 'file_type' => 'Tipo', 'category' => 'Categoría'], 'columns' => ['media' => 'Multimedia', 'event' => 'Evento', 'category' => 'Categoría', 'order' => 'Orden', 'status' => 'Estado'], 'file_types' => ['image' => 'Imagen', 'video' => 'Video'], 'categories' => ['gallery' => 'Galería', 'weigh_in' => 'Pesaje', 'faceoff' => 'Careo', 'backstage' => 'Backstage', 'highlight' => 'Highlight', 'sponsor' => 'Sponsor', 'other' => 'Otro'], 'form' => ['event_id' => 'Evento', 'file_type' => 'Tipo de archivo', 'file_path' => 'URL del video', 'category' => 'Categoría', 'title' => 'Título', 'description' => 'Descripción', 'is_featured' => 'Marcar como destacado', 'display_order' => 'Orden', 'status' => 'Estado', 'media_image' => 'Imagen'], 'messages' => ['created' => 'Pieza multimedia registrada correctamente.', 'updated' => 'Pieza multimedia actualizada correctamente.', 'deleted' => 'Pieza multimedia eliminada correctamente.'], 'validation' => ['image_required' => 'Debe seleccionar una imagen para guardar esta pieza multimedia.']];
$en['admin']['news'] = ['page_title' => 'Noticias', 'subtitle' => 'Gestiona comunicados, novedades, entrevistas y publicaciones públicas.', 'table_title' => 'Listado de noticias', 'table_subtitle' => 'Filtra, crea, edita, publica o elimina publicaciones editoriales.', 'create' => 'Nueva noticia', 'edit' => 'Editar noticia', 'delete_title' => 'Eliminar noticia', 'delete_warning' => 'Esta acción eliminará la noticia:', 'search_placeholder' => 'Título, slug o resumen...', 'image_help' => 'La imagen de portada JPG, PNG o WebP se optimiza al guardarse. Tamaño máximo: 5 MB.', 'filters' => ['featured' => 'Destacado', 'from' => 'Desde', 'to' => 'Hasta'], 'columns' => ['post' => 'Noticia', 'author' => 'Autor', 'published_at' => 'Publicación', 'status' => 'Estado', 'featured' => 'Destacado'], 'status' => ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado'], 'actions' => ['publish' => 'Publicar'], 'form' => ['title' => 'Título', 'slug' => 'Slug', 'excerpt' => 'Resumen', 'content' => 'Contenido', 'status' => 'Estado', 'is_featured' => 'Marcar como destacado', 'published_at' => 'Fecha de publicación', 'cover_image' => 'Imagen de portada'], 'messages' => ['created' => 'Noticia registrada correctamente.', 'updated' => 'Noticia actualizada correctamente.', 'published' => 'Noticia publicada correctamente.', 'deleted' => 'Noticia eliminada correctamente.']];
$en['admin']['sponsors'] = ['page_title' => 'Sponsors', 'subtitle' => 'Gestiona marcas patrocinadoras y aliados comerciales.', 'table_title' => 'Listado de sponsors', 'table_subtitle' => 'Filtra, crea, edita o elimina sponsors sin eventos asociados.', 'create' => 'Nuevo sponsor', 'edit' => 'Editar sponsor', 'delete_title' => 'Eliminar sponsor', 'delete_warning' => 'Esta acción eliminará el sponsor:', 'search_placeholder' => 'Nombre, slug, web o correo...', 'image_help' => 'El logo JPG, PNG o WebP se optimiza al guardarse. Tamaño máximo: 5 MB.', 'events_summary' => ':count eventos', 'events_help' => 'Mantén presionada la tecla Ctrl o Cmd para seleccionar varios eventos.', 'filters' => ['event' => 'Evento'], 'columns' => ['sponsor' => 'Sponsor', 'website' => 'Web', 'email' => 'Correo', 'events' => 'Eventos', 'order' => 'Orden', 'status' => 'Estado'], 'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'website_url' => 'Sitio web', 'contact_email' => 'Correo de contacto', 'description' => 'Descripción', 'display_order' => 'Orden', 'status' => 'Estado', 'logo_path' => 'Logo', 'events' => 'Eventos asociados'], 'messages' => ['created' => 'Sponsor registrado correctamente.', 'updated' => 'Sponsor actualizado correctamente.', 'deleted' => 'Sponsor eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un sponsor asociado a eventos.']];
$en['admin']['subscription_plans'] = ['page_title' => 'Planes de suscripción', 'subtitle' => 'Gestiona paquetes para suscriptores, precios, duración y beneficios visibles.', 'table_title' => 'Listado de planes', 'table_subtitle' => 'Filtra, crea, edita o elimina planes sin uso activo.', 'create' => 'Nuevo plan', 'edit' => 'Editar plan', 'delete_title' => 'Eliminar plan', 'delete_warning' => 'Esta acción eliminará el plan:', 'search_placeholder' => 'Nombre, slug o descripción...', 'duration_summary' => ':days días', 'discount_summary' => ':discount% de descuento', 'features_summary' => ':count beneficios', 'usage_summary' => ':subscriptions suscripciones - :requests solicitudes', 'filters' => ['billing_period' => 'Periodo'], 'columns' => ['plan' => 'Plan', 'price' => 'Precio', 'period' => 'Periodo', 'usage' => 'Uso', 'order' => 'Orden', 'status' => 'Estado'], 'billing_periods' => ['monthly' => 'Mensual', 'quarterly' => 'Trimestral', 'yearly' => 'Anual', 'one_time' => 'Pago único', 'lifetime' => 'Vitalicio'], 'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'description' => 'Descripción', 'price' => 'Precio', 'currency' => 'Moneda', 'billing_period' => 'Periodo de cobro', 'duration_days' => 'Duración en días', 'discount_percentage' => 'Porcentaje de descuento', 'display_order' => 'Orden', 'status' => 'Estado'], 'features' => ['title' => 'Beneficios', 'add' => 'Agregar beneficio', 'name' => 'Beneficio', 'description' => 'Descripción', 'feature_key' => 'Clave técnica', 'value' => 'Valor', 'display_order' => 'Orden', 'status' => 'Estado', 'help' => 'Solo se guardan beneficios con nombre. Los beneficios activos también se sincronizan en el campo JSON features del plan.'], 'messages' => ['created' => 'Plan registrado correctamente.', 'updated' => 'Plan actualizado correctamente.', 'deleted' => 'Plan eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un plan con suscripciones o solicitudes de compra asociadas.']];
$en['admin']['subscribers'] = ['page_title' => 'Suscriptores', 'subtitle' => 'Revisa cuentas de suscriptores, datos de contacto, estado de acceso y su última suscripción.', 'table_title' => 'Listado de suscriptores', 'table_subtitle' => 'Filtra, revisa y actualiza datos básicos del suscriptor sin cambiar sus roles.', 'edit' => 'Editar suscriptor', 'search_placeholder' => 'Nombre, correo, teléfono o documento...', 'identity_value' => 'Documento: :value', 'last_login_value' => 'Último ingreso: :date', 'last_login_empty' => 'Sin ingreso registrado', 'activity_summary' => ':subscriptions suscripciones - :payments pagos - :requests solicitudes', 'filters' => ['subscription_status' => 'Suscripción'], 'columns' => ['subscriber' => 'Suscriptor', 'contact' => 'Contacto', 'subscription' => 'Suscripción', 'activity' => 'Actividad', 'status' => 'Estado'], 'subscription_status' => ['none' => 'Sin suscripción', 'pending' => 'Pendiente', 'active' => 'Activa', 'expired' => 'Vencida', 'cancelled' => 'Cancelada', 'suspended' => 'Suspendida'], 'form' => ['name' => 'Nombre', 'lastname' => 'Apellido', 'email' => 'Correo', 'number_phone' => 'Teléfono', 'identity_document' => 'Documento de identidad', 'state' => 'Estado de cuenta'], 'messages' => ['updated' => 'Suscriptor actualizado correctamente.']];
$en['admin']['fights'] = [
            'page_title' => 'Combates',
            'subtitle' => 'Gestiona la cartelera de cada evento, esquinas, rounds, orden y estado del combate.',
            'table_title' => 'Listado de combates',
            'table_subtitle' => 'Filtra, programa, edita o elimina combates sin resultado oficial registrado.',
            'create' => 'Nuevo combate',
            'edit' => 'Editar combate',
            'delete_title' => 'Eliminar combate',
            'delete_warning' => 'Esta acción eliminará el combate:',
            'search_placeholder' => 'Título, peleador o apodo...',
            'image_help' => 'La imagen promocional JPG, PNG o WebP se optimiza al guardarse. Tamaño máximo: 5 MB.',
            'filters' => ['event' => 'Evento', 'bout_type' => 'Tipo', 'weight_class' => 'Categoría'],
            'columns' => ['fight' => 'Combate', 'event' => 'Evento', 'weight_class' => 'Categoría', 'rounds' => 'Rounds', 'order' => 'Orden', 'status' => 'Estado'],
            'status' => ['scheduled' => 'Programado', 'live' => 'En vivo', 'finished' => 'Finalizado', 'cancelled' => 'Cancelado'],
            'bout_type' => ['regular' => 'Regular', 'main_event' => 'Estelar', 'co_main_event' => 'Coestelar', 'title_fight' => 'Por título', 'exhibition' => 'Exhibición'],
            'flags' => ['main_event' => 'Estelar', 'featured' => 'Destacado', 'has_result' => 'Con resultado'],
            'form' => ['event_id' => 'Evento', 'weight_class_id' => 'Categoría de peso', 'corner_red_fighter_id' => 'Esquina roja', 'corner_blue_fighter_id' => 'Esquina azul', 'title' => 'Título', 'bout_type' => 'Tipo de combate', 'rounds' => 'Rounds', 'display_order' => 'Orden en cartelera', 'starts_at' => 'Fecha y hora del combate', 'status' => 'Estado', 'is_main_event' => 'Combate estelar', 'is_featured' => 'Destacado', 'notes' => 'Notas internas', 'promo_image' => 'Imagen promocional'],
            'messages' => ['created' => 'Combate registrado correctamente.', 'updated' => 'Combate actualizado correctamente.', 'deleted' => 'Combate eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un combate con resultado oficial registrado.'],
        ];
$en['admin']['fight_results'] = [
            'page_title' => 'Resultados',
            'subtitle' => 'Registra y actualiza el resultado oficial de cada combate.',
            'table_title' => 'Resultados de combates',
            'table_subtitle' => 'Filtra combates, revisa resultados oficiales y registra ganador, método, round y tiempo.',
            'search_placeholder' => 'Evento, combate, peleador o método...',
            'modal_title' => 'Gestionar resultado oficial',
            'pending' => 'Pendiente',
            'no_custom_title' => 'Sin título personalizado',
            'round_time_value' => 'R: :round · T: :time',
            'filters' => ['event' => 'Evento', 'result_type' => 'Resultado', 'result_state' => 'Registro'],
            'result_state' => ['with' => 'Con resultado', 'without' => 'Sin resultado'],
            'columns' => ['fight' => 'Combate', 'event' => 'Evento', 'result' => 'Resultado', 'winner' => 'Ganador', 'round_time' => 'Round / tiempo'],
            'result_types' => ['ko_tko' => 'KO/TKO', 'submission' => 'Sumisión', 'decision' => 'Decisión', 'draw' => 'Empate', 'no_contest' => 'Sin decisión', 'disqualification' => 'Descalificación'],
            'corners' => ['red' => 'Roja: :fighter', 'blue' => 'Azul: :fighter'],
            'actions' => ['manage' => 'Gestionar resultado'],
            'form' => ['result_type' => 'Tipo de resultado', 'winner_fighter_id' => 'Ganador', 'no_winner' => 'Sin ganador', 'method' => 'Método', 'round' => 'Round', 'time' => 'Tiempo', 'official_notes' => 'Notas oficiales'],
            'messages' => ['saved' => 'Resultado oficial guardado correctamente.'],
            'validation' => ['winner_required' => 'Debe seleccionar un ganador para este tipo de resultado.', 'winner_corner' => 'El ganador debe pertenecer a la esquina roja o azul del combate.', 'round_limit' => 'El round no puede ser mayor a los :rounds rounds configurados para el combate.'],
        ];
$en['admin']['fighters'] = [
            'page_title' => 'Peleadores',
            'subtitle' => 'Gestiona perfiles deportivos, récords, categorías, equipos e imágenes de peleadores.',
            'table_title' => 'Listado de peleadores',
            'table_subtitle' => 'Filtra, crea, edita o elimina peleadores sin combates ni rankings asociados.',
            'create' => 'Nuevo peleador',
            'edit' => 'Editar peleador',
            'delete_title' => 'Eliminar peleador',
            'delete_warning' => 'Esta acción eliminará el peleador:',
            'search_placeholder' => 'Nombre, apodo o slug...',
            'record_summary' => ':wins-:losses-:draws · NC :nc',
            'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.',
            'filters' => ['gender' => 'Género', 'weight_class' => 'Categoría', 'team' => 'Equipo'],
            'columns' => ['fighter' => 'Peleador', 'team' => 'Equipo', 'weight_class' => 'Categoría', 'record' => 'Récord', 'fights' => 'Combates', 'status' => 'Estado'],
            'gender' => ['male' => 'Masculino', 'female' => 'Femenino'],
            'stance' => ['orthodox' => 'Ortodoxa', 'southpaw' => 'Zurda', 'switch' => 'Cambiante'],
            'form' => ['first_name' => 'Nombre', 'last_name' => 'Apellido', 'nickname' => 'Apodo', 'slug' => 'Slug', 'gender' => 'Género', 'country_id' => 'País', 'city_id' => 'Ciudad', 'fighter_team_id' => 'Equipo/gimnasio', 'weight_class_id' => 'Categoría de peso', 'birthdate' => 'Fecha de nacimiento', 'height_cm' => 'Estatura (cm)', 'reach_cm' => 'Alcance (cm)', 'stance' => 'Guardia', 'bio' => 'Biografía', 'wins' => 'Victorias', 'losses' => 'Derrotas', 'draws' => 'Empates', 'no_contests' => 'Sin decisión', 'status' => 'Estado', 'profile_image' => 'Imagen de perfil', 'cover_image' => 'Imagen de portada'],
            'messages' => ['created' => 'Peleador registrado correctamente.', 'updated' => 'Peleador actualizado correctamente.', 'deleted' => 'Peleador eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un peleador con combates o rankings asociados.'],
        ];
$en['admin']['rankings'] = [
            'page_title' => 'Rankings',
            'subtitle' => 'Gestiona posiciones por categoría de peso y género.',
            'table_title' => 'Listado de rankings',
            'table_subtitle' => 'Filtra, crea y ajusta posiciones oficiales por categoría y género.',
            'create' => 'Nuevo ranking',
            'edit' => 'Editar ranking',
            'search_placeholder' => 'Peleador, apodo o categoría...',
            'champion' => 'Campeón',
            'filters' => ['weight_class' => 'Categoría', 'gender' => 'Género'],
            'columns' => ['position' => 'Posición', 'fighter' => 'Peleador', 'weight_class' => 'Categoría', 'record' => 'Récord', 'movement' => 'Movimiento', 'status' => 'Estado'],
            'movement' => ['same' => 'Sin cambio', 'up' => 'Sube :places', 'down' => 'Baja :places'],
            'form' => ['weight_class_id' => 'Categoría de peso', 'gender' => 'Género', 'fighter_id' => 'Peleador', 'position' => 'Posición actual', 'previous_position' => 'Posición anterior', 'is_champion' => 'Marcar como campeón', 'ranked_at' => 'Fecha del ranking', 'status' => 'Estado'],
            'messages' => ['created' => 'Ranking registrado correctamente.', 'updated' => 'Ranking actualizado correctamente.'],
            'validation' => ['gender_mismatch' => 'El peleador seleccionado no pertenece al género del ranking.', 'weight_class_mismatch' => 'El peleador seleccionado no pertenece a la categoría de peso del ranking.', 'position_taken' => 'Ya existe un ranking activo con esa posición para la categoría y género seleccionados.'],
        ];
$en['admin']['fighter_teams'] = [
            'page_title' => 'Equipos y gimnasios',
            'subtitle' => 'Gestiona academias, clubes, gimnasios y equipos de entrenamiento.',
            'table_title' => 'Listado de equipos',
            'table_subtitle' => 'Filtra, crea, edita o elimina equipos sin peleadores asociados.',
            'create' => 'Nuevo equipo',
            'edit' => 'Editar equipo',
            'delete_title' => 'Eliminar equipo',
            'delete_warning' => 'Esta acción eliminará el equipo:',
            'search_placeholder' => 'Nombre, coach, teléfono o slug...',
            'image_help' => 'El logo JPG, PNG o WebP se optimiza al guardarse. Tamaño máximo: 5 MB.',
            'filters' => ['city' => 'Ciudad'],
            'columns' => ['team' => 'Equipo', 'city' => 'Ciudad', 'coach' => 'Coach', 'contact' => 'Contacto', 'fighters' => 'Peleadores', 'status' => 'Estado'],
            'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'city_id' => 'Ciudad', 'coach_name' => 'Coach', 'contact_phone' => 'Teléfono', 'description' => 'Descripción', 'status' => 'Estado', 'logo_path' => 'Logo'],
            'messages' => ['created' => 'Equipo registrado correctamente.', 'updated' => 'Equipo actualizado correctamente.', 'deleted' => 'Equipo eliminado correctamente.', 'delete_blocked' => 'No se puede eliminar un equipo con peleadores asociados.'],
        ];
$en['admin']['weight_classes'] = [
            'page_title' => 'Categorías de peso',
            'subtitle' => 'Gestiona divisiones por peso y género para peleadores, combates y rankings.',
            'table_title' => 'Listado de categorías',
            'table_subtitle' => 'Filtra, crea, edita o elimina categorías sin dependencias activas.',
            'create' => 'Nueva categoría',
            'edit' => 'Editar categoría',
            'delete_title' => 'Eliminar categoría',
            'delete_warning' => 'Esta acción eliminará la categoría:',
            'search_placeholder' => 'Nombre o slug...',
            'usage_summary' => ':fighters peleadores · :fights combates',
            'filters' => ['gender' => 'Género'],
            'gender' => ['male' => 'Masculino', 'female' => 'Femenino'],
            'columns' => ['name' => 'Categoría', 'gender' => 'Género', 'range' => 'Rango', 'order' => 'Orden', 'usage' => 'Uso', 'status' => 'Estado'],
            'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'gender' => 'Género', 'min_weight_kg' => 'Peso mínimo (kg)', 'max_weight_kg' => 'Peso máximo (kg)', 'max_weight_lb' => 'Peso máximo (lb)', 'display_order' => 'Orden', 'status' => 'Estado'],
            'messages' => ['created' => 'Categoría registrada correctamente.', 'updated' => 'Categoría actualizada correctamente.', 'deleted' => 'Categoría eliminada correctamente.', 'delete_blocked' => 'No se puede eliminar una categoría con peleadores, combates o rankings asociados.'],
        ];
$en['admin']['purchase_requests'] = [
            'page_title' => 'Solicitudes de compra',
            'subtitle' => 'Gestiona contactos públicos, solicitudes de tickets, suscripciones y comprobantes manuales.',
            'table_title' => 'Listado de solicitudes',
            'table_subtitle' => 'Filtra, asigna responsables y actualiza el estado de cada solicitud.',
            'search_placeholder' => 'Nombre, correo, teléfono o UUID...',
            'modal_title' => 'Gestionar solicitud',
            'delete_title' => 'Eliminar solicitud',
            'delete_warning' => 'Esta acción eliminará la solicitud de:',
            'filters' => ['request_type' => 'Tipo', 'channel' => 'Canal', 'assigned_to' => 'Asignado', 'from' => 'Desde', 'to' => 'Hasta'],
            'columns' => ['request' => 'Solicitud', 'contact' => 'Contacto', 'related_to' => 'Relacionado con', 'status' => 'Estado', 'assigned_to' => 'Asignado', 'created_at' => 'Fecha'],
            'status' => ['pending' => 'Pendiente', 'in_review' => 'En revisión', 'contacted' => 'Contactado', 'converted' => 'Convertido', 'closed' => 'Cerrado', 'rejected' => 'Rechazado'],
            'request_types' => ['general_contact' => 'Consulta general', 'event_ticket' => 'Tickets para evento', 'subscription' => 'Suscripción', 'payment_proof' => 'Comprobante de pago'],
            'channels' => ['whatsapp' => 'WhatsApp', 'phone' => 'Teléfono', 'email' => 'Correo'],
            'assignment' => ['unassigned' => 'Sin asignar', 'me' => 'Asignadas a mí'],
            'actions' => ['manage' => 'Gestionar', 'assign_to_me' => 'Asignarme', 'close' => 'Cerrar'],
            'details' => ['title' => 'Detalle de la solicitud', 'contact' => 'Contacto', 'request_type' => 'Tipo', 'email' => 'Correo', 'channel' => 'Canal', 'phone' => 'Teléfono/WhatsApp', 'related_to' => 'Relacionado con', 'message' => 'Mensaje', 'proof' => 'Comprobante'],
            'proof' => ['available' => 'Comprobante', 'open' => 'Abrir comprobante privado', 'none' => 'Sin comprobante cargado'],
            'form' => ['status' => 'Estado', 'assigned_to' => 'Responsable', 'notes' => 'Notas internas'],
            'messages' => ['updated' => 'Solicitud actualizada correctamente.', 'assigned' => 'Solicitud asignada correctamente.', 'closed' => 'Solicitud cerrada correctamente.', 'deleted' => 'Solicitud eliminada correctamente.'],
        ];
$en['landing'] = ['login' => 'Ingresar', 'hero_text' => 'Eventos, combates, rankings y suscripciones para una promotora de artes marciales.', 'view_events' => 'Ver eventos', 'contact' => 'Contactar', 'events_title' => 'Eventos activos', 'events_subtitle' => 'Eventos publicados, próximos y anteriores que siguen activos.', 'featured' => 'Destacado', 'empty_events' => 'Todavía no hay eventos publicados.', 'no_image' => 'Sin imagen disponible', 'back' => 'Volver', 'fights_title' => 'Cartelera', 'empty_fights' => 'Este evento aún no tiene combates publicados.', 'vs' => 'vs', 'nav' => ['home' => 'Inicio', 'fighters' => 'Peleadores', 'news' => 'Noticias', 'subscription' => 'Suscripción', 'contact' => 'Contacto'], 'footer' => ['quick_links' => 'Enlaces rápidos', 'contact' => 'Contacto', 'follow_us' => 'Síguenos', 'rights' => 'Todos los derechos reservados.'], 'fighters' => ['featured_title' => 'Peleadores destacados', 'featured_subtitle' => 'Conoce a algunos de los atletas de nuestro roster.', 'view_all' => 'Ver todos los peleadores', 'title' => 'Peleadores', 'subtitle' => 'Conoce al roster completo de peleadores activos.', 'empty' => 'Todavía no hay peleadores publicados.', 'wins' => 'Ganados', 'losses' => 'Perdidos', 'draws' => 'Empates', 'bio_title' => 'Biografía', 'fight_history' => 'Historial de combates', 'round' => 'Round', 'result_win' => 'Victoria', 'result_loss' => 'Derrota', 'result_pending' => 'Pendiente', 'no_fights' => 'Todavía no hay combates registrados.'], 'news' => ['section_title' => 'Últimas noticias', 'view_all' => 'Ver todas las noticias', 'title' => 'Noticias', 'subtitle' => 'Comunicados, novedades y entrevistas.', 'read_more' => 'Leer más', 'empty' => 'Todavía no hay noticias publicadas.'], 'event' => ['prev' => 'Evento anterior', 'next' => 'Evento siguiente', 'main_event' => 'Combate estelar', 'tickets_title' => 'Entradas', 'price_from' => 'Desde', 'no_tickets' => 'Los enlaces de venta se publicarán pronto.', 'contact_cta' => 'Contáctanos'], 'subscription' => ['title' => 'Suscripción', 'subtitle' => 'Elige el plan que se ajuste a ti y disfruta beneficios exclusivos.', 'empty' => 'No hay planes disponibles por el momento.', 'cta' => 'Quiero suscribirme'], 'contact_page' => ['title' => 'Contacto', 'subtitle' => 'Envíanos tu solicitud y nuestro equipo se pondrá en contacto contigo a la brevedad.', 'about_event' => 'Estás consultando sobre: :event', 'about_plan' => 'Estás consultando sobre: :plan', 'success' => 'Tu solicitud se envió correctamente. Nos pondremos en contacto contigo a la brevedad.', 'direct_title' => '¿Prefieres contactarnos directamente?', 'direct_hint' => 'También puedes comunicarte con nuestro equipo por cualquiera de estos canales.', 'form' => ['name' => 'Nombre completo', 'email' => 'Correo electrónico', 'phone' => 'Teléfono', 'whatsapp' => 'WhatsApp', 'channel' => 'Canal de contacto preferido', 'type' => 'Motivo de contacto', 'message' => 'Mensaje', 'proof' => 'Comprobante de pago', 'proof_hint' => 'Opcional. Formatos permitidos: JPG, JPEG, PNG o PDF, máximo 5 MB.', 'submit' => 'Enviar solicitud'], 'channel_options' => ['whatsapp' => 'WhatsApp', 'phone' => 'Teléfono', 'email' => 'Correo electrónico'], 'type_options' => ['general_contact' => 'Consulta general', 'event_ticket' => 'Entradas de evento', 'subscription' => 'Suscripción', 'payment_proof' => 'Comprobante de pago']]];
$en['api'] = ['purchase_requests' => ['created' => 'Solicitud registrada correctamente.', 'event_required' => 'Debe seleccionar un evento publicado para solicitar tickets.']];
$en['uploads'] = ['payment_proofs' => ['invalid_type' => 'Formato no soportado. Usa JPG, JPEG, PNG o PDF.', 'max_size' => 'El comprobante no puede pesar más de :max MB.'], 'public_images' => ['invalid_type' => 'Formato no soportado. Usa JPG, PNG, GIF o WebP.', 'max_size' => 'La imagen no puede pesar más de :max MB.', 'process_failed' => 'No se pudo procesar la imagen seleccionada.']];

$en['admin']['user_subscriptions'] = [
    'page_title' => 'Suscripciones de usuario',
    'subtitle' => 'Gestiona asignaciones manuales de planes, periodos, estados y notas internas de suscriptores.',
    'table_title' => 'Listado de suscripciones',
    'table_subtitle' => 'Filtra, crea, edita o cancela suscripciones de usuarios sin procesar pagos automáticos.',
    'create' => 'Nueva suscripción',
    'edit' => 'Editar suscripción',
    'cancel_title' => 'Cancelar suscripción',
    'cancel_warning' => 'Esta acción marcará la suscripción como cancelada:',
    'search_placeholder' => 'Suscriptor, correo, teléfono, plan o slug...',
    'period_value' => ':start - :end',
    'open_ended' => 'Sin fecha final',
    'renewal_value' => 'Renovación: :date',
    'payments_summary' => ':count pagos',
    'filters' => [
        'plan' => 'Plan',
        'from' => 'Desde',
        'to' => 'Hasta',
    ],
    'columns' => [
        'subscriber' => 'Suscriptor',
        'plan' => 'Plan',
        'period' => 'Periodo',
        'status' => 'Estado',
        'payments' => 'Pagos',
        'source' => 'Origen',
    ],
    'status' => [
        'pending' => 'Pendiente',
        'active' => 'Activa',
        'expired' => 'Vencida',
        'cancelled' => 'Cancelada',
        'suspended' => 'Suspendida',
    ],
    'sources' => [
        'manual' => 'Manual',
        'admin' => 'Administración',
        'purchase_request' => 'Solicitud de compra',
        'import' => 'Importación',
        'other' => 'Otro',
    ],
    'actions' => [
        'cancel' => 'Cancelar suscripción',
    ],
    'form' => [
        'user_id' => 'Suscriptor',
        'subscription_plan_id' => 'Plan',
        'starts_at' => 'Fecha y hora de inicio',
        'ends_at' => 'Fecha y hora de finalización',
        'trial_ends_at' => 'Fin del periodo de prueba',
        'renewal_at' => 'Fecha de renovación',
        'status' => 'Estado',
        'source' => 'Origen',
        'metadata_note' => 'Nota interna',
    ],
    'messages' => [
        'created' => 'Suscripción registrada correctamente.',
        'updated' => 'Suscripción actualizada correctamente.',
        'cancelled' => 'Suscripción cancelada correctamente.',
    ],
    'validation' => [
        'subscriber_required' => 'El usuario seleccionado debe tener el rol suscriptor.',
        'ends_at_after_start' => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
        'trial_ends_at_after_start' => 'La fecha de prueba debe ser posterior o igual a la fecha de inicio.',
        'renewal_at_after_start' => 'La fecha de renovación debe ser posterior o igual a la fecha de inicio.',
    ],
];

$en['admin']['subscription_payments'] = [
    'page_title' => 'Pagos de suscripción',
    'subtitle' => 'Gestiona pagos manuales de suscripciones, comprobantes privados y estados de pago.',
    'table_title' => 'Listado de pagos',
    'table_subtitle' => 'Filtra, registra, actualiza, confirma o anula pagos manuales.',
    'create' => 'Nuevo pago',
    'edit' => 'Editar pago',
    'confirm_title' => 'Confirmar pago',
    'confirm_warning' => 'Esta acción marcará el pago como pagado:',
    'cancel_title' => 'Anular pago',
    'cancel_warning' => 'Esta acción marcará el pago como fallido:',
    'search_placeholder' => 'Suscriptor, correo, teléfono, plan, proveedor o transacción...',
    'paid_at_value' => 'Pagado: :date',
    'not_paid' => 'Sin fecha de pago',
    'filters' => [
        'payment_method' => 'Método',
        'from' => 'Desde',
        'to' => 'Hasta',
    ],
    'columns' => [
        'subscriber' => 'Suscriptor',
        'subscription' => 'Suscripción',
        'amount' => 'Monto',
        'method' => 'Método',
        'proof' => 'Comprobante',
        'status' => 'Estado',
    ],
    'status' => [
        'pending' => 'Pendiente',
        'paid' => 'Pagado',
        'failed' => 'Fallido',
        'refunded' => 'Reembolsado',
        'expired' => 'Vencido',
    ],
    'payment_methods' => [
        'manual_transfer' => 'Transferencia manual',
        'cash' => 'Efectivo',
        'qr' => 'Pago QR',
        'whatsapp' => 'WhatsApp',
        'gateway' => 'Pasarela',
        'other' => 'Otro',
    ],
    'actions' => [
        'confirm' => 'Confirmar pago',
        'cancel' => 'Anular pago',
    ],
    'proof' => [
        'open' => 'Abrir comprobante',
        'none' => 'Sin comprobante',
        'help' => 'JPG, JPEG, PNG o PDF. Las imágenes se optimizan al guardar. Tamaño máximo: 5 MB.',
    ],
    'form' => [
        'user_id' => 'Suscriptor',
        'user_subscription_id' => 'Suscripción',
        'no_subscription' => 'Sin suscripción vinculada',
        'amount' => 'Monto',
        'currency' => 'Moneda',
        'payment_method' => 'Método de pago',
        'provider' => 'Proveedor',
        'provider_transaction_id' => 'ID de transacción',
        'payment_url' => 'URL de pago',
        'paid_at' => 'Fecha de pago',
        'expires_at' => 'Fecha de vencimiento',
        'status' => 'Estado',
        'notes' => 'Notas internas',
        'payment_proof' => 'Comprobante de pago',
    ],
    'messages' => [
        'created' => 'Pago registrado correctamente.',
        'updated' => 'Pago actualizado correctamente.',
        'confirmed' => 'Pago confirmado correctamente.',
        'cancelled' => 'Pago anulado correctamente.',
    ],
    'validation' => [
        'subscriber_required' => 'El usuario seleccionado debe tener el rol suscriptor.',
        'subscription_user_mismatch' => 'La suscripción seleccionada no pertenece al suscriptor seleccionado.',
    ],
];

$en['admin']['ticket_links'] = [
    'page_title' => 'Enlaces de tickets',
    'subtitle' => 'Gestiona enlaces externos de tickets, WhatsApp, VIP y streaming por evento.',
    'table_title' => 'Listado de enlaces de tickets',
    'table_subtitle' => 'Filtra, crea, edita o elimina enlaces públicos de venta asociados a eventos.',
    'create' => 'Nuevo enlace',
    'edit' => 'Editar enlace',
    'delete_title' => 'Eliminar enlace de tickets',
    'delete_warning' => 'Esta acción eliminará el enlace de tickets:',
    'search_placeholder' => 'Proveedor, etiqueta, URL o evento...',
    'open_start' => 'Sin límite de inicio',
    'open_end' => 'Sin límite final',
    'filters' => [
        'event' => 'Evento',
        'sale_channel' => 'Canal',
    ],
    'columns' => [
        'link' => 'Enlace',
        'event' => 'Evento',
        'channel' => 'Canal',
        'price' => 'Precio desde',
        'window' => 'Ventana de venta',
        'status' => 'Estado',
    ],
    'sale_channels' => [
        'external' => 'Plataforma externa',
        'whatsapp' => 'WhatsApp',
        'phone' => 'Teléfono',
        'streaming' => 'Streaming',
        'vip' => 'VIP',
        'other' => 'Otro',
    ],
    'form' => [
        'event_id' => 'Evento',
        'provider_name' => 'Proveedor',
        'label' => 'Etiqueta visible',
        'sale_channel' => 'Canal de venta',
        'url' => 'URL',
        'price_from' => 'Precio desde',
        'currency' => 'Moneda',
        'starts_at' => 'Inicio de venta',
        'ends_at' => 'Fin de venta',
        'display_order' => 'Orden',
        'status' => 'Estado',
    ],
    'messages' => [
        'created' => 'Enlace de tickets registrado correctamente.',
        'updated' => 'Enlace de tickets actualizado correctamente.',
        'deleted' => 'Enlace de tickets eliminado correctamente.',
    ],
    'validation' => [
        'ends_after_start' => 'La fecha de fin de venta debe ser posterior o igual a la fecha de inicio.',
    ],
];

$en['admin']['system_settings'] = [
    'page_title' => 'Ajustes del sistema',
    'subtitle' => 'Configura la marca visible, los datos de contacto, los metadatos de la landing pública y las imágenes del sistema.',
    'form_title' => 'Configuración general',
    'form_subtitle' => 'Estos valores se reflejan en el panel administrativo, el login y la landing pública.',
    'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.',
    'sections' => [
        'identity' => 'Identidad',
        'contact' => 'Contacto',
        'social' => 'Redes sociales',
        'seo' => 'Landing pública y SEO',
    ],
    'form' => [
        'product_name' => 'Nombre del producto',
        'public_title' => 'Título público',
        'contact_email' => 'Correo de contacto',
        'contact_phone' => 'Teléfono de contacto',
        'whatsapp_phone' => 'Teléfono de WhatsApp',
        'short_description' => 'Descripción corta',
        'seo_title' => 'Título SEO',
        'seo_description' => 'Descripción SEO',
        'landing_show_rankings' => 'Mostrar rankings en la landing pública',
        'logo_path' => 'Logo',
        'favicon_path' => 'Favicon',
    ],
    'social' => [
        'facebook' => 'URL de Facebook',
        'instagram' => 'URL de Instagram',
        'youtube' => 'URL de YouTube',
        'tiktok' => 'URL de TikTok',
    ],
    'actions' => [
        'save' => 'Guardar ajustes',
    ],
    'messages' => [
        'updated' => 'Ajustes del sistema actualizados correctamente.',
    ],
];

$en['menu']['events']['venues'] = 'Sedes';
$en['admin']['venues'] = [
    'page_title' => 'Sedes',
    'subtitle' => 'Gestiona arenas, coliseos y ubicaciones físicas usadas por los eventos.',
    'table_title' => 'Listado de sedes',
    'table_subtitle' => 'Filtra, crea, edita o elimina sedes sin eventos asociados.',
    'create' => 'Nueva sede',
    'edit' => 'Editar sede',
    'delete_title' => 'Eliminar sede',
    'delete_warning' => 'Esta acción eliminará la sede:',
    'search_placeholder' => 'Nombre, dirección, contacto, teléfono o slug...',
    'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.',
    'filters' => ['city' => 'Ciudad'],
    'columns' => ['venue' => 'Sede', 'location' => 'Ubicación', 'capacity' => 'Capacidad', 'contact' => 'Contacto', 'events' => 'Eventos', 'status' => 'Estado'],
    'form' => ['name' => 'Nombre', 'slug' => 'Slug', 'city_id' => 'Ciudad', 'address' => 'Dirección', 'latitude' => 'Latitud', 'longitude' => 'Longitud', 'capacity' => 'Capacidad', 'contact_name' => 'Nombre de contacto', 'contact_phone' => 'Teléfono de contacto', 'status' => 'Estado', 'image' => 'Imagen de la sede'],
    'messages' => ['created' => 'Sede registrada correctamente.', 'updated' => 'Sede actualizada correctamente.', 'deleted' => 'Sede eliminada correctamente.', 'delete_blocked' => 'No se puede eliminar una sede con eventos asociados.'],
];

$en['admin']['users'] = [
    'page_title' => 'Usuarios',
    'subtitle' => 'Gestiona cuentas de usuario, estado de acceso y roles asignados según jerarquía.',
    'table_title' => 'Listado de usuarios',
    'table_subtitle' => 'Filtra, crea, edita o elimina usuarios permitidos por la jerarquía de roles configurada.',
    'create' => 'Nuevo usuario',
    'edit' => 'Editar usuario',
    'delete_title' => 'Eliminar usuario',
    'delete_warning' => 'Esta acción eliminará el usuario:',
    'search_placeholder' => 'Nombre, correo, teléfono o documento...',
    'password_help' => 'Al editar, deja este campo vacío para conservar la contraseña actual.',
    'selected_roles' => 'roles seleccionados',
    'hierarchy_readonly' => 'Solo lectura por jerarquía',
    'filters' => ['role' => 'Rol'],
    'columns' => ['user' => 'Usuario', 'contact' => 'Contacto', 'roles' => 'Roles', 'last_login' => 'Último ingreso', 'status' => 'Estado'],
    'form' => ['name' => 'Nombre', 'lastname' => 'Apellido', 'email' => 'Correo', 'number_phone' => 'Teléfono', 'identity_document' => 'Documento de identidad', 'state' => 'Estado', 'password' => 'Contraseña', 'roles' => 'Roles'],
    'messages' => ['created' => 'Usuario registrado correctamente.', 'updated' => 'Usuario actualizado correctamente.', 'deleted' => 'Usuario eliminado correctamente.', 'self_delete_blocked' => 'No puedes eliminar tu propia cuenta de usuario.'],
    'validation' => ['roles_allowed' => 'Selecciona al menos un rol que tengas permitido asignar.'],
];

$en['menu']['fighters']['media'] = 'Multimedia de peleadores';
$en['admin']['fighter_media'] = [
    'page_title' => 'Multimedia de peleadores',
    'subtitle' => 'Gestiona imágenes y enlaces de video asociados a perfiles de peleadores.',
    'table_title' => 'Listado multimedia de peleadores',
    'table_subtitle' => 'Filtra, sube, edita o elimina piezas multimedia públicas de peleadores.',
    'create' => 'Nueva pieza',
    'edit' => 'Editar pieza',
    'delete_title' => 'Eliminar multimedia',
    'delete_warning' => 'Esta acción eliminará la pieza multimedia:',
    'search_placeholder' => 'Título, descripción, peleador o apodo...',
    'image_help' => 'Las imágenes JPG, PNG o WebP se optimizan al guardarse. Tamaño máximo: 5 MB.',
    'featured' => 'Destacado',
    'untitled' => 'Sin título',
    'filters' => ['fighter' => 'Peleador', 'file_type' => 'Tipo'],
    'columns' => ['media' => 'Multimedia', 'fighter' => 'Peleador', 'order' => 'Orden', 'status' => 'Estado'],
    'file_types' => ['image' => 'Imagen', 'video' => 'Video'],
    'form' => ['fighter_id' => 'Peleador', 'file_type' => 'Tipo de archivo', 'file_path' => 'URL del video', 'title' => 'Título', 'description' => 'Descripción', 'is_featured' => 'Marcar como destacado', 'display_order' => 'Orden', 'status' => 'Estado', 'media_image' => 'Imagen'],
    'messages' => ['created' => 'Multimedia de peleador registrada correctamente.', 'updated' => 'Multimedia de peleador actualizada correctamente.', 'deleted' => 'Multimedia de peleador eliminada correctamente.'],
    'validation' => ['image_required' => 'Debe seleccionar una imagen para guardar esta pieza multimedia de peleador.'],
];

$en['admin']['landing'] = [
    'page_title' => 'Landing pública',
    'subtitle' => 'Gestiona el título público, la descripción, los metadatos SEO y las opciones visibles de la landing.',
    'form_title' => 'Configuración de landing',
    'form_subtitle' => 'Estos valores se muestran en la página pública y deben mantenerse alineados con los eventos publicados activos.',
    'latest_events' => 'Vista previa de eventos publicados',
    'empty_events' => 'No hay eventos publicados para previsualizar.',
    'stats' => ['published' => 'Publicados', 'featured' => 'Destacados', 'drafts' => 'Borradores'],
    'actions' => ['save' => 'Guardar landing', 'open_public' => 'Abrir página pública', 'manage_events' => 'Gestionar eventos'],
    'form' => ['public_title' => 'Título público', 'short_description' => 'Descripción corta', 'seo_title' => 'Título SEO', 'seo_description' => 'Descripción SEO', 'landing_show_rankings' => 'Mostrar rankings en la landing pública'],
    'messages' => ['updated' => 'Landing pública actualizada correctamente.'],
];

$en['subscriber_portal'] = [
    'menu' => ['dashboard' => 'Inicio', 'purchases' => 'Mis compras', 'events' => 'Mis eventos', 'subscription' => 'Mi suscripción', 'profile' => 'Perfil'],
    'dashboard' => [
        'title' => 'Inicio del suscriptor',
        'subtitle' => 'Revisa tu cuenta, suscripción, compras y eventos disponibles.',
        'cards' => ['account' => 'Estado de cuenta', 'subscription' => 'Estado de suscripción', 'events' => 'Eventos disponibles'],
        'latest_purchases' => 'Últimos pagos',
        'latest_requests' => 'Últimas solicitudes',
        'next_events' => 'Eventos disponibles',
    ],
    'actions' => ['view_all' => 'Ver todo', 'save' => 'Guardar cambios', 'back_to_purchases' => 'Volver a mis compras'],
    'empty' => ['no_subscription' => 'Sin suscripción activa', 'no_purchases' => 'Sin compras registradas.', 'no_requests' => 'Sin solicitudes registradas.', 'no_events' => 'No tienes eventos disponibles.'],
    'events' => ['title' => 'Mis eventos', 'subtitle' => 'Eventos asociados a tus solicitudes, compras o beneficios de acceso.'],
    'purchases' => ['title' => 'Mis compras', 'subtitle' => 'Revisa pagos y solicitudes registradas para tu cuenta.', 'payments' => 'Pagos', 'requests' => 'Solicitudes', 'detail_title' => 'Detalle de compra', 'detail_subtitle' => 'Información completa de este pago o solicitud.', 'proof_status' => 'Estado del comprobante', 'proof_uploaded_label' => 'Comprobante cargado', 'proof_missing_label' => 'Sin comprobante cargado', 'notes' => 'Notas', 'message' => 'Mensaje', 'related_event' => 'Evento asociado', 'upload_proof_title' => 'Subir o reenviar comprobante', 'upload_proof_hint' => 'Formatos permitidos: JPG, JPEG, PNG o PDF, máximo 5 MB.', 'upload_proof_submit' => 'Subir comprobante', 'proof_field' => 'Comprobante de pago', 'proof_uploaded' => 'Comprobante cargado correctamente. Nuestro equipo lo revisará en breve.', 'proof_not_allowed' => 'Este registro ya no admite un nuevo comprobante. Comunícate con soporte.', 'contact_title' => '¿Necesitas ayuda con esta compra?', 'contact_hint' => 'Comunícate con nuestro equipo de ventas o soporte por cualquiera de estos canales.'],
    'subscription' => ['title' => 'Mi suscripción', 'subtitle' => 'Revisa tu plan actual y el historial de suscripciones.', 'current' => 'Suscripción actual', 'history' => 'Historial de suscripciones', 'benefits' => 'Beneficios del plan', 'contact_title' => '¿Quieres renovar o mejorar tu plan?', 'contact_hint' => 'Comunícate con nuestro equipo de ventas para renovar, mejorar o consultar sobre tu suscripción.'],
    'profile' => ['title' => 'Perfil', 'subtitle' => 'Actualiza tus propios datos de contacto.', 'updated' => 'Perfil actualizado correctamente.', 'form' => ['name' => 'Nombre', 'lastname' => 'Apellido', 'email' => 'Correo', 'number_phone' => 'Teléfono', 'identity_document' => 'Documento de identidad', 'current_password' => 'Contraseña actual', 'new_password' => 'Nueva contraseña', 'confirm_password' => 'Confirmar contraseña'], 'password_title' => 'Cambiar contraseña', 'password_hint' => 'Usa al menos 8 caracteres y evita reutilizar contraseñas antiguas.', 'password_updated' => 'Contraseña actualizada correctamente.', 'password_submit' => 'Actualizar contraseña'],
    'columns' => ['concept' => 'Concepto', 'amount' => 'Monto', 'method' => 'Método', 'status' => 'Estado', 'date' => 'Fecha', 'channel' => 'Canal', 'plan' => 'Plan', 'start' => 'Inicio', 'end' => 'Fin', 'period' => 'Periodo'],
];

$en['menu']['reports'] = ['group' => 'Reportes', 'events' => 'Reportes de eventos', 'subscriptions' => 'Reportes de suscripciones', 'sales' => 'Reportes de ventas'];
$en['admin']['reports'] = [
    'page_title' => 'Reportes',
    'subtitle' => 'Revisa métricas operativas de eventos, suscripciones y ventas manuales.',
    'table_title' => 'Reportes operativos',
    'table_subtitle' => 'Filtra por tipo de reporte, rango de fechas y estado sin modificar registros.',
    'types' => ['events' => 'Eventos', 'subscriptions' => 'Suscripciones', 'sales' => 'Ventas'],
    'filters' => ['type' => 'Tipo de reporte', 'from' => 'Desde', 'to' => 'Hasta'],
    'stats' => ['total' => 'Total', 'published' => 'Publicados', 'featured' => 'Destacados', 'requests' => 'Solicitudes', 'active' => 'Activas', 'pending' => 'Pendientes', 'expired' => 'Vencidas', 'paid' => 'Pagados', 'amount' => 'Monto pagado'],
    'columns' => ['event' => 'Evento', 'venue' => 'Sede', 'date' => 'Fecha', 'fights' => 'Combates', 'requests' => 'Solicitudes', 'subscriber' => 'Suscriptor', 'plan' => 'Plan', 'period' => 'Periodo', 'payments' => 'Pagos', 'concept' => 'Concepto', 'amount' => 'Monto', 'method' => 'Método'],
];

$en['admin']['logs'] = [
    'page_title' => 'Logs',
    'subtitle' => 'Revisa entradas recientes del log de Laravel sin modificar el archivo.',
    'table_title' => 'Log de la aplicación',
    'file_size' => 'Tamaño del archivo',
    'last_modified' => 'Última modificación',
    'total_entries' => 'Entradas totales',
    'filtered_entries' => 'Entradas filtradas',
    'detail_title' => 'Detalle de entrada del log',
    'context' => 'Contexto',
    'trace' => 'Traza',
    'raw' => 'Entrada original',
    'has_context' => 'Incluye contexto',
    'has_trace' => 'Incluye traza',
    'filters' => ['level' => 'Nivel', 'all_levels' => 'Todos los niveles', 'from_date' => 'Desde', 'to_date' => 'Hasta', 'search_placeholder' => 'Buscar mensaje, contexto o traza...'],
    'columns' => ['datetime' => 'Fecha y hora', 'level' => 'Nivel', 'env' => 'Entorno', 'message' => 'Mensaje'],
    'actions' => ['download' => 'Descargar log', 'view_detail' => 'Ver detalle'],
];

$en['landing']['fighters']['result_draw'] = 'Empate / Sin decisión';

return $en;
