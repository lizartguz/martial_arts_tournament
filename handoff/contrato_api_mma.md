# Contrato API MMA

## Objetivo

Este documento define la superficie API vigente para la primera etapa de Combate Real. La API ya no expone rutas heredadas de estaciones, clima, nodos, créditos ni automatizaciones.

## Base

```text
/api/v1
```

## Endpoints públicos

### Listar eventos publicados

```text
GET /api/v1/events
```

Reglas:

- Devuelve solo eventos con `status = 1`.
- Ordena primero por `is_featured` y luego por cercanía de fecha.
- Incluye sede y cantidad de combates con consultas optimizadas.
- No muestra eventos en borrador, archivados ni cancelados.

### Ver evento por slug

```text
GET /api/v1/events/{event:slug}
```

Reglas:

- Resuelve el evento por `slug`.
- Devuelve `404` si el evento no está publicado.
- Incluye cartelera con peleadores, categoría de peso y enlaces de tickets activos.
- Usa carga anticipada de relaciones para evitar consultas N+1.

### Registrar solicitud de compra/contacto

```text
POST /api/v1/purchase-requests
```

Campos principales:

```text
contact_name
contact_email
contact_phone
contact_whatsapp
preferred_channel
request_type
event_id
event_slug
subscription_plan_id
message
payment_proof
```

Reglas:

- Debe existir al menos un dato de contacto: correo, teléfono o WhatsApp.
- `preferred_channel` acepta `whatsapp`, `phone` o `email`.
- `request_type` acepta `general_contact`, `event_ticket`, `subscription` o `payment_proof`.
- Si `request_type = event_ticket`, debe existir un evento publicado por `event_id` o `event_slug`.
- `payment_proof` acepta `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Las imágenes se optimizan con el servicio centralizado antes de guardarse.
- Los comprobantes se guardan en almacenamiento privado.
- La ruta tiene limitación `throttle:10,1`.

## Endpoints autenticados

### Ver usuario autenticado

```text
GET /api/v1/me
Authorization: Bearer {token}
```

Reglas:

- Requiere `auth:sanctum`.
- Devuelve datos propios del usuario.
- Incluye suscripciones, pagos y solicitudes recientes del usuario autenticado.
- Incluye banderas de permisos para portal de suscriptor.
- No permite consultar datos de otros usuarios desde este endpoint.

## Seguridad

- Las rutas públicas solo exponen contenido publicado o generan solicitudes comerciales.
- Las rutas privadas deben comparar siempre datos propios contra `auth()->id()` cuando se agreguen nuevos endpoints.
- El panel administrativo y la API no deben reutilizar rutas heredadas del proyecto anterior.
- Los nombres técnicos de permisos no deben ser texto visible de interfaz; la API puede devolver banderas funcionales cuando una app cliente las necesite.
