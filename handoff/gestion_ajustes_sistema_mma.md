# Gestión de ajustes del sistema MMA

## Objetivo

Implementar una vista administrativa para configurar la identidad visible de la aplicación, los datos de contacto, la información básica de SEO y las imágenes principales del sistema.

## Alcance funcional

- Ruta administrativa: `/admin/settings`.
- Permiso de acceso: `system_settings.view`.
- Permiso de actualización: `system_settings.update`.
- La configuración se guarda en la tabla `system_settings`.
- El registro principal se mantiene como una configuración única del sistema, no como configuración multi-organización.
- El nombre por defecto del producto es `Combate Real`.

## Campos gestionados

- Nombre del producto.
- Título público para la landing.
- Correo de contacto.
- Teléfono de contacto.
- Teléfono de WhatsApp.
- Descripción corta.
- Título SEO.
- Descripción SEO.
- Indicador para mostrar rankings en la landing pública.
- Logo.
- Favicon.
- URLs de redes sociales: Facebook, Instagram, YouTube y TikTok.

## Identidad visual vigente

Los valores por defecto de la marca usan assets propios de MMA:

- Logo: `images/mma/brand/combate-real-logo.svg`.
- Favicon: `images/mma/brand/combate-real-favicon.png`.
- Imagen por defecto: `images/mma/generated/landing-hero.webp`.

Los datos demo usan imagenes fotorealistas bajo `images/mma/generated/` para eventos, peleadores, noticias y fondos publicos.

## Vista

La pantalla mantiene el patrón visual administrativo:

- Encabezado con título, subtítulo y botón de guardado.
- Secciones agrupadas por identidad, contacto, redes sociales, SEO e imágenes.
- Inputs y textarea con validaciones Livewire.
- Carga de logo y favicon con previsualización.
- Mensaje de éxito mediante evento Livewire compatible con SweetAlert2.

## Imágenes

- Se aceptan imágenes JPG, JPEG, PNG y WebP.
- Tamaño máximo: 5 MB.
- Las imágenes se guardan usando el servicio centralizado `ImageUploadOptimizer`.
- El almacenamiento queda en disco público bajo la carpeta configurada para imágenes MMA.
- Si se reemplaza una imagen subida previamente por el sistema, se elimina el archivo anterior del disco.
- Las imágenes heredadas incluidas en el proyecto se conservan como fallback.

## Consumo en la aplicación

Los ajustes configurados se reflejan en:

- Menú lateral administrativo.
- Favicon del panel.
- Login.
- Landing pública de eventos.
- Detalle público de evento.

Para centralizar este consumo se usa `SystemSettingsService`, evitando duplicar consultas y fallbacks en las vistas.

## Landing pública

La landing usa:

- `seo_title` como título de la página.
- `seo_description` como metadescripción cuando exista.
- `logo_path` como logo visible.
- `favicon_path` como favicon.
- `default_image` como imagen visual de respaldo para el hero publico.
- `public_title` como título principal.
- `short_description` como texto principal cuando esté configurado.
- Teléfono de contacto o WhatsApp como enlace inicial de contacto.

El campo para rankings queda disponible para activar su visualización cuando se implemente la sección pública correspondiente.

## Validaciones

- El nombre del producto y el título público son obligatorios.
- El correo debe tener formato válido cuando se informe.
- Las URLs de redes sociales deben tener formato válido cuando se informen.
- Los textos tienen límites máximos para evitar contenido excesivo.
- Logo y favicon deben ser imágenes válidas y respetar el límite de tamaño.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.system_settings
```

Los labels, secciones, ayudas y mensajes deben mantenerse traducidos en todos los idiomas activos.

## Restricciones

- Un usuario sin `system_settings.view` no debe acceder a la pantalla aunque fuerce la URL.
- Guardar cambios debe validar `system_settings.update` desde el componente Livewire.
- Los administradores no crean nuevos permisos desde el panel; solo consumen los permisos definidos por código y base de datos.
