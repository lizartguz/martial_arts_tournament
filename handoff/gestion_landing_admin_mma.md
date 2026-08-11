# Gestión administrativa de landing pública MMA

## Alcance implementado

- Se agregó el módulo administrativo `/admin/landing` para configurar información pública de la landing.
- El acceso queda protegido por el permiso técnico `landing.update`.
- La pantalla reutiliza la tabla `system_settings`; no se crea una tabla nueva porque los campos ya pertenecen a la configuración pública del sistema.
- La gestión se limita a:
  - Título público.
  - Descripción corta.
  - Título SEO.
  - Descripción SEO.
  - Activación visual de rankings en la landing pública.
- La pantalla muestra un resumen de eventos publicados, destacados y borradores.
- La vista previa de eventos publicados carga la sede con `with(['venue:id,name'])` para evitar consultas N+1.

## Validaciones y seguridad

- La ruta usa middleware `can:landing.update`.
- El controlador también ejecuta `Gate::authorize('landing.update')`.
- El componente Livewire valida permisos en `mount()` y en `save()`.
- Los campos editables tienen validaciones de longitud y obligatoriedad según su uso público.
- Los campos opcionales se guardan como `null` cuando quedan vacíos.

## Idiomas

- Todos los textos visibles usan claves `__('mma...')`.
- Se agregaron traducciones para español, inglés, portugués, francés, alemán y ruso.
- El idioma español mantiene acentos y tildes en los textos de interfaz.

## Relación con ajustes del sistema

- El módulo de landing es una vista focalizada para contenido público.
- Los ajustes de marca como logo, favicon, nombre del producto, contactos y redes sociales siguen centralizados en Ajustes del sistema.
- Ambos módulos escriben sobre `system_settings`, pero cada uno cubre una responsabilidad visual distinta.

## Pendiente relacionado

- Cuando se active visualmente el bloque de rankings en la landing pública, se deberá conectar el flag `landing_show_rankings` con la sección comentada o preparada para rankings.
- Si se decide agregar textos publicitarios adicionales para la landing, primero se debe documentar qué campos serán editables antes de crear migraciones nuevas.
