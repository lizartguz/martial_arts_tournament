# Guia de transicion del proyecto MMA

## Proposito

Este documento registra el contexto principal del proyecto mientras se transforma una base Laravel existente en una plataforma enfocada en artes marciales mixtas. La guia es temporal: sirve para orientar decisiones durante la migracion progresiva y podrá eliminarse cuando el sistema ya este completamente adaptado al nuevo dominio.

## Vision del proyecto

El proyecto apunta a construir una pagina pública y un panel administrativo para eventos de artes marciales mixtas, con una experiencia similar en concepto a plataformas de promocion de combates como UFC, pero adaptada a las necesidades propias del sistema.

La parte pública debe permitir consultar eventos, peleas programadas, peleadores, categorías de peso, fechas, fotografias y descripciones. La parte administrativa debe permitir iniciar sesión y gestionar todo el contenido que alimenta esa experiencia pública.

## Estrategia de trabajo

El proyecto parte de una copia funcional de otro sistema. No se busca reconstruir todo desde cero, sino reutilizar la mayor cantidad posible de estructura, paquetes, configuraciones y logicas existentes para ganar avance inicial.

La migracion se hara de forma gradual, pero con limpieza definitiva del dominio heredado que no aporte a MMA:

- Reemplazar textos, nombres, rutas, controladores, modelos y vistas heredadas por conceptos del nuevo dominio.
- Mantener temporalmente solo infraestructura reutilizable, como autenticacion, permisos, idiomas, layouts, notificaciones o carga de imágenes.
- Eliminar módulos heredados del dominio anterior cuando no sean necesarios para MMA.
- Reutilizar configuraciones, autenticacion, idiomas, notificaciones push, carga de imágenes, estructura de layouts y paquetes instalados cuando sean compatibles.
- Centralizar reglas reutilizables, como carga/optimización de imágenes, slugs, permisos, jerarquía de roles y configuración del sistema, para evitar duplicación en controladores o componentes Livewire.
- Documentar decisiones relevantes para evitar perder contexto durante el cambio progresivo.

## Alcance funcional esperado

La plataforma deberá cubrir, de forma progresiva, estos módulos principales:

- Landing page pública para mostrar eventos y combates.
- Login para el acceso administrativo.
- Gestión CRUD de eventos.
- Gestión CRUD de peleadores.
- Gestión CRUD de combates.
- Gestión de categorías de peso.
- Carga y administracion de fotografias de peleadores, eventos y combates.
- Descripciones, estadisticas basicas y caracteristicas de peleadores.
- Programacion de fechas y horarios de eventos.
- Publicación o despublicacion de eventos y peleas.
- Soporte multidioma aprovechando la estructura existente, con español como idioma por defecto y todos los idiomas actuales conservados.
- Posible uso de notificaciones push para avisos de eventos, combates o novedades.

## Entidades iniciales del dominio

Estas entidades serviran como punto de partida para pensar migraciones, modelos y pantallas:

- Evento: cartelera o funcion completa con nombre, descripción, fecha, lugar, imagen principal y estado de publicación.
- Peleador: persona registrada para competir, con nombre, alias, fotografia, biografia, país, equipo, record y datos fisicos.
- Combate: pelea programada entre dos peleadores dentro de un evento.
- Categoría de peso: division o rango competitivo donde se agrupan peleadores y combates.
- Imagen o multimedia: archivos asociados a eventos, peleadores o combates.
- Usuario administrador: usuario autenticado que gestiona la información del sistema.

## Reutilizacion recomendada

Durante la transicion conviene revisar y reutilizar primero:

- Autenticacion, roles y permisos existentes.
- Layouts administrativos y componentes Livewire reutilizables.
- Configuración de idiomas y archivos de traducción.
- Flujo de notificaciones push web.
- Servicios de carga, almacenamiento y optimizacion de imágenes.
- Servicios reutilizables para slugs, archivos, permisos, jerarquía de roles, configuración del sistema y etiquetas traducidas.
- Estructura de rutas, controladores, vistas y modelos como referencia para nuevos CRUD.
- Pruebas existentes que puedan adaptarse a los nuevos flujos.

## Limpieza progresiva

La limpieza debe hacerse con cuidado para no romper partes utiles del sistema. Cada módulo heredado deberia clasificarse antes de eliminarlo:

- Reutilizar: el módulo sirve casi completo y solo requiere renombrado o ajuste de dominio.
- Adaptar: el módulo tiene lógica util, pero necesita cambios fuertes de nombres, validaciones o relaciones.
- Reemplazar: el módulo solo sirve como referencia y se creara una estructura nueva.
- Eliminar: el módulo no aporta al proyecto MMA y no tiene dependencias necesarias. Esta es la decision final para estaciones, clima, agro, forecast, sensores y pantallas relacionadas.

## Convenciones de nombres esperadas

El nuevo dominio deberia ir migrando hacia nombres claros y consistentes:

- `fighters` para peleadores.
- `events` para eventos.
- `fights` para combates.
- `weight_categories` para categorías de peso.
- `admin` para rutas internas de administracion.

Cuando se renombre un módulo, también deben revisarse rutas, permisos, traducciones, vistas, pruebas, seeders y documentacion relacionada.

## Documentacion relacionada existente

Estos documentos pueden servir como referencia durante la migracion:

- `docs/flujo_login_usuarios.md`
- `docs/flujo_notificaciones_push_web.md`
- `docs/firebase_configuracion.md`
- `docs/mensajes_de_error_y_roles.md`
- `docs/COLOR_THEME_GUIDE.md`
- `handoff/estrategia_servicios_reutilizables_seeders_mma.md`

## Criterios para futuras decisiones

- Priorizar avances que permitan ver pronto una landing pública funcional.
- Mantener el login administrativo y adaptarlo antes de crear nuevas capas de seguridad.
- Crear CRUDs simples y estables antes de agregar estadisticas avanzadas.
- Eliminar código heredado que no aporte a MMA despues de verificar dependencias.
- Preferir cambios pequenos y verificables para reducir riesgo durante la migracion.
- Mantener este documento actualizado mientras siga siendo la guia de transicion.

## Pendientes iniciales sugeridos

- Definir nombres finales de las entidades principales.
- Revisar el menú administrativo actual y decidir que opciones se reutilizan, adaptan o eliminan.
- Crear la primera version del modelo de datos para eventos, peleadores, combates y categorías.
- Decidir la estructura visual inicial de la landing pública.
- Identificar módulos heredados que pueden reemplazarse por CRUDs del nuevo dominio.
- Revisar la configuración de imágenes para confirmar donde se almacenaran fotografias y banners.
- Configurar la base local `artsmartialdb` para migraciones, limpieza y seeders demo.
- Crear seeders obligatorios y seeders demo para visualizar eventos, peleadores, rankings, pagos y solicitudes sin carga manual inicial.
