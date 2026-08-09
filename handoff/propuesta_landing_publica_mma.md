# Propuesta de landing pública MMA

## Objetivo

Este documento propone una primera version de la landing pública para la plataforma MMA. El nombre temporal del producto será:

```text
Combate Real
```

El nombre, logo, favicon, título y datos visibles deben ser configurables desde el panel administrativo. Por ahora pueden conservarse los recursos visuales actuales hasta que se cargue la identidad final de la promotora.

## Enfoque

La landing debe funcionar como vitrina pública de la promotora:

- Mostrar eventos activos/publicados, incluyendo eventos actuales, próximos y finalizados que el administrador decida mantener visibles.
- Mostrar combates destacados.
- Mostrar peleadores destacados.
- Mostrar información de la promotora.
- Mostrar enlaces de tickets o contacto por WhatsApp, teléfono y formulario.
- Convertir visitantes en interesados, compradores o suscriptores.

No debe parecer una pagina generica de software. Debe sentirse como sitio público de una promotora de MMA.

## Estructura sugerida

### Header público

Elementos:

```text
Logo/nombre Combate Real
Eventos
Peleadores
Noticias
Suscripción
Contacto
Iniciar sesión
Selector de idioma
```

Reglas:

- El logo y nombre salen de Configuración del sistema.
- El selector de idioma debe afectar toda la landing.
- El boton de login lleva al acceso correspondiente.

### Hero principal

Debe mostrar el evento publicado más importante, el evento actual o el próximo evento destacado.

Contenido:

```text
imagen/banner del evento
nombre del evento
fecha y hora
sede/ciudad
estado público
boton Ver cartelera
boton Comprar/Contactar
```

Reglas:

- Solo mostrar eventos activos/publicados.
- Priorizar `is_featured = true`.
- Si no hay evento destacado, mostrar el evento activo más cercano a la fecha actual.
- La URL del evento debe usar `slug`.

### Navegación de eventos activos

La landing debe permitir navegar de forma intuitiva entre todos los eventos activos/publicados:

```text
evento anterior
evento actual o destacado
evento siguiente
línea de tiempo o carrusel de eventos
```

Reglas:

- Incluir eventos próximos.
- Incluir eventos finalizados si siguen activos/publicados.
- No ocultar automáticamente un evento por fecha pasada.
- El administrador controla la visibilidad cambiando el estado del evento.
- Ordenar primero por `is_featured` y luego por la fecha del evento más cercana a la fecha actual.

### Eventos activos

Listado de cards compactas con:

```text
poster/banner
nombre
fecha
sede
cantidad de combates
boton Ver evento
boton Entradas/Contacto
```

Estados:

- Publicado.
- Próximo.
- Actual.
- Finalizado, si continúa activo/publicado.

No mostrar:

- Borradores.
- Eventos cancelados salvo que se decida comunicar cancelacion.

### Cartelera destacada

Para el evento principal:

```text
combate principal
coestelar
resto de peleas destacadas
categoría de peso
rounds
peleador esquina roja/azul o A/B
```

Cada peleador puede enlazar a su perfil público por `slug`.

### Peleadores destacados

Mostrar perfiles activos y destacados:

```text
foto
nombre
alias
país
record
categoría
equipo/gimnasio
```

URL:

```text
/peleadores/{fighter:slug}
```

### Rankings

La primera versión debe dejar preparada la sección de rankings.

Regla práctica:

- La consulta, estructura de datos y componente pueden quedar listos.
- Los rankings se organizan por categoría de peso y género.
- La sección visual puede quedar comentada, oculta por configuración o detrás de una bandera como `landing_show_rankings = false`.
- Cuando se active, debe respetar idioma, estado público y consultas sin N+1.

### Noticias y contenido

Mostrar publicaciones activas:

```text
título
imagen
resumen
fecha
boton Leer mas
```

URL:

```text
/noticias/{post:slug}
```

### Suscripción

Seccion para explicar beneficios del suscriptor:

```text
acceso a contenido o eventos habilitados
avisos de eventos
beneficios/promociones si existen
contacto para compra o renovacion
```

Primera etapa:

- Sin pasarela.
- Botón de contacto por WhatsApp, teléfono y formulario.
- Si hay planes visibles, mostrar solo planes activos.

### Tickets y contacto

Los tickets de primera etapa son enlaces externos por evento.

El formulario público de contacto/compra debe crear una solicitud visible en administración para seguimiento comercial.

Tipos posibles:

```text
Entrada general
VIP
Reserva por WhatsApp
Streaming
Ticketera externa
```

Reglas:

- Mostrar solo enlaces activos.
- Ordenar por `display_order`.
- Abrir enlaces externos de forma segura.
- Registrar click si se implementa analitica.
- Guardar solicitudes del formulario como `purchase_requests`.
- Permitir adjuntar comprobante si el visitante ya coordinó un pago manual.
- Aceptar comprobantes `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Optimizar imágenes antes de guardarlas usando `App\Services\ImageUploadOptimizer` o un servicio equivalente; los PDF se guardan validados, sin compresión inicial.
- Guardar comprobantes en almacenamiento privado o protegido.
- Revisar comprobantes solo desde sesión administrativa autorizada.

### Footer

Contenido:

```text
logo/nombre
datos de contacto
redes sociales
enlaces legales
idioma
copyright
```

## Rutas publicas sugeridas

```text
/                              landing principal
/eventos                       listado público de eventos
/eventos/{event:slug}          detalle público de evento
/peleadores                    listado público de peleadores
/peleadores/{fighter:slug}     perfil público de peleador
/noticias                      listado público de noticias
/noticias/{post:slug}          detalle público de noticia
/suscripción                   información de planes/contacto
/contacto                      formulario o datos de contacto
```

## Estados y visibilidad

Eventos:

```text
draft      no visible
published  visible aunque la fecha ya haya pasado
archived   no visible en la landing pública
cancelled  visible solo si se decide comunicarlo
```

Los textos Próximo, Actual o Finalizado son estados visuales derivados de `starts_at` y de la fecha actual. No deben reemplazar el estado técnico de publicación.

Peleadores:

```text
active     visible
inactive   no visible o visible histórico según configuración
```

Noticias:

```text
draft      no visible
published  visible
archived   no visible en listados principales
```

## Configuración del sistema

Desde el panel administrativo debe poder configurarse:

```text
nombre del producto
título público
logo
favicon
imagen por defecto
teléfono principal
WhatsApp
correo de contacto
redes sociales
texto corto de la promotora
seo title
seo description
```

Estos valores deben reflejarse en:

```text
landing pública
login
sidebar administrativo
topbar
favicon
metadatos publicos
footer
```

## Idiomas

La landing debe traducirse por completo.

El idioma por defecto será español. Los textos en español deben usar ortografia correcta, tildes, acentos y signos de apertura.

Todo debe cambiar al idioma activo:

```text
menú
botones
títulos
subtitulos
estados
formularios
mensajes SweetAlert
validaciones
textos vacios
metadatos si se administran por idioma
```

## Rendimiento

Reglas:

- Consultar eventos con `with()` para sede, combates destacados y multimedia necesaria.
- Usar `withCount()` para cantidad de combates.
- No cargar galerias completas en la landing.
- Usar imágenes optimizadas.
- Evitar consultas N+1 en cards de eventos y peleadores.
- Cachear bloques publicos si el contenido no cambia constantemente.

## Primera version recomendada

Para iniciar rapido:

```text
1. Header público.
2. Hero con evento destacado.
3. Navegación de eventos activos.
4. Eventos activos: pasados, actuales y próximos publicados.
5. Cartelera destacada del evento principal.
6. Peleadores destacados.
7. Tickets/contacto.
8. Footer.
```

La sección de rankings debe quedar preparada desde la primera versión, pero puede permanecer oculta o comentada hasta que se decida mostrarla públicamente.

Noticias, sponsors y contenido avanzado pueden agregarse después de tener eventos y peleadores funcionando.

## Dudas pendientes

- Confirmar si la landing debe tener modo oscuro o solo respetar el tema visual público.
