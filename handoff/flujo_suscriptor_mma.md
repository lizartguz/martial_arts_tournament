# Flujo del suscriptor MMA

## Objetivo

Este documento define la experiencia del usuario con rol `subscriber`. El suscriptor es el cliente final o fan que accede a la plataforma para revisar sus compras, eventos habilitados, estado de suscripción y perfil.

El suscriptor no debe entrar al panel administrativo.

## Acceso

Reglas:

- El suscriptor inicia sesión con cuenta propia.
- Debe tener rol `subscriber`.
- No debe tener roles administrativos mezclados.
- Su cuenta debe estar activa.
- Si aplica, su suscripción debe estar vigente.
- Si intenta acceder a `/admin/*`, debe recibir `403` o redireccion controlada a su portal.

## Menú del suscriptor

Menú inicial:

```text
Inicio
Mis compras
Mis eventos
Mi suscripción
Perfil
```

No debe ver:

```text
Eventos admin
Peleadores admin
Combates admin
Usuarios
Roles
Permisos
Pagos generales
Reportes globales
Configuración
Logs
```

## Inicio

Debe mostrar un resumen simple:

- Estado de cuenta.
- Estado de suscripción.
- Próximos eventos asociados.
- Ultimas compras o pagos registrados.
- Accesos rapidos a eventos y perfil.

## Mis compras

Primera etapa:

- Muestra pagos, compras o solicitudes registradas para el usuario.
- Los pagos pueden estar registrados manualmente por administracion.
- Cada pago manual debe poder guardar comprobante si existe.
- Los comprobantes permitidos son `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Las imágenes de comprobante deben optimizarse antes de guardarse usando `App\Services\ImageUploadOptimizer` o un servicio equivalente; los PDF se guardan validados sin compresión inicial.
- Los comprobantes se almacenan en privado y solo se visualizan desde sesión administrativa autorizada.
- Si no existe pasarela activa, mostrar llamada a contacto por WhatsApp, teléfono y formulario.

Estados sugeridos:

```text
Pendiente
Confirmado
Rechazado
Cancelado
Expirado
```

Acciones sugeridas:

- Ver detalle.
- Subir o reenviar comprobante si el flujo administrativo lo habilita.
- Contactar soporte o ventas.
- Ver evento asociado si corresponde.

## Mis eventos

Debe mostrar eventos asociados al suscriptor por compra, beneficio o registro.

Reglas:

- Solo mostrar eventos publicados o eventos privados asociados al usuario si el negocio lo requiere.
- No mostrar eventos en borrador.
- Resolver eventos publicos por `slug`.
- Si el usuario intenta entrar a un evento no habilitado para su cuenta, validar permiso/acceso en backend.

Datos sugeridos:

```text
Evento
Fecha
Sede
Estado
Tipo de acceso
Acción
```

## Mi suscripción

Debe mostrar:

- Plan actual.
- Fecha de inicio.
- Fecha de vencimiento.
- Estado.
- Beneficios.
- Historial breve de renovaciones.
- Acciones de contacto para renovar o consultar.

Primera etapa:

- La renovacion puede ser manual.
- El usuario puede ver instrucciones y enlaces de contacto.
- La pasarela de pagos queda para una fase futura.

## Perfil

Debe permitir:

- Ver datos personales.
- Editar datos permitidos.
- Cambiar contraseña si el flujo actual lo permite.
- Cerrar sesión.

No debe permitir:

- Cambiar su rol.
- Asignarse permisos.
- Ver usuarios internos.
- Modificar compras o suscripciones manualmente.

## Compra desde web

Primera etapa:

El suscriptor o visitante podrá iniciar interes de compra desde la web, pero la confirmacion de pago será manual.

Flujo sugerido:

```text
usuario ve evento/plan -> pulsa comprar/contactar -> se abre WhatsApp/teléfono/formulario -> envía comprobante -> administración registra o valida pago -> suscriptor ve compra confirmada
```

Canales iniciales:

- WhatsApp.
- Teléfono.
- Formulario web.

El formulario web debe permitir adjuntar comprobante de pago cuando corresponda, aplicando la validación de archivo definida para la primera etapa.

Reglas confirmadas para comprobantes:

- Formatos: `jpg`, `jpeg`, `png` y `pdf`.
- Tamaño máximo: 5 MB.
- Imágenes optimizadas antes de guardarse.
- PDF validado por tipo y tamaño, sin compresión inicial.
- Almacenamiento privado o protegido.
- Visualización del comprobante solo desde administración.

La solicitud enviada desde el formulario debe quedar registrada en administración como solicitud de compra/contacto, con estado de seguimiento y responsable asignable.

Fase futura:

```text
usuario ve evento/plan -> pasarela de pago -> confirmacion automatica -> acceso habilitado
```

## Seguridad

Validaciones obligatorias:

- `subscriber.purchases.view` para compras propias.
- `subscriber.events.view` para eventos propios o publicos permitidos.
- `subscriber.subscription.view` para suscripción propia.
- `subscriber.profile.update` para edicion de perfil.
- Comparar siempre `user_id` del recurso contra `auth()->id()` cuando sea dato propio.

Ejemplo:

```php
return $user->can('subscriber.purchases.view')
    && $purchase->user_id === $user->id;
```

## Traducciones

Todo el portal debe traducirse en todos los idiomas activos. Español será el idioma por defecto y debe escribirse con ortografia correcta, tildes, acentos y signos de apertura.

Textos incluidos:

```text
menú
labels
botones
estados
mensajes vacios
mensajes SweetAlert
validaciones
instrucciones de contacto
```

## Estados vacios

Cada pantalla debe tener textos claros:

- Sin compras registradas.
- No tienes eventos disponibles.
- No tienes una suscripción activa.
- No hay comprobantes enviados.

## Relacion con otros documentos

- `handoff/propuesta_roles_permisos_mma.md`
- `handoff/propuesta_visualizacion_modulos_mma.md`
- `handoff/plan_implementacion_mma.md`
