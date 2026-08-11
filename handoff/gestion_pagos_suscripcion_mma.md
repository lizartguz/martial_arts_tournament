# Gestión de pagos de suscripción MMA

## Objetivo

Implementar la vista administrativa para registrar, editar, confirmar y anular pagos manuales asociados a suscriptores y, opcionalmente, a una suscripción activa o histórica.

## Alcance funcional

- Ruta administrativa: `/admin/subscription-payments`.
- Ruta privada de comprobante: `/admin/subscription-payments/{subscriptionPayment}/proof`.
- Permiso de acceso: `subscription_payments.view`.
- Permisos de operación:
  - `subscription_payments.upload_proof` para registrar o actualizar pagos manuales y comprobantes.
  - `subscription_payments.confirm` para marcar un pago como pagado.
  - `subscription_payments.cancel` para marcar un pago como fallido/anulado.
- El módulo no integra pasarelas de pago todavía; mantiene campos preparados para proveedor, transacción y URL de pago futura.

## Vista

La pantalla sigue el patrón administrativo definido para los módulos MMA:

- Filtros superiores por búsqueda, estado, método de pago, fecha inicial, fecha final y cantidad por página.
- Tabla compacta con columnas de suscriptor, suscripción, monto, método, comprobante, estado y acciones.
- Última columna con menú de acciones.
- Modal Livewire para registrar o editar pagos.
- Modal de confirmación para marcar como pagado.
- Modal de anulación para marcar como fallido.
- Mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Comprobantes

- Los comprobantes se guardan en disco privado según `config/uploads.php`.
- Formatos permitidos: `jpg`, `jpeg`, `png` y `pdf`.
- Tamaño máximo inicial: 5 MB.
- Las imágenes se optimizan al guardar mediante el servicio centralizado `FileUploadService` y `ImageUploadOptimizer`.
- Los PDF se almacenan sin compresión inicial.
- La visualización del comprobante exige sesión administrativa y permiso `subscription_payments.view`.

## Estados técnicos

La columna `status` mantiene el catálogo documentado:

- `0`: pendiente.
- `1`: pagado.
- `2`: fallido/anulado.
- `3`: reembolsado.
- `4`: vencido.

## Validaciones

- El usuario seleccionado debe existir y tener rol `subscriber`.
- Si se selecciona una suscripción, debe pertenecer al mismo suscriptor.
- El monto debe ser mayor a cero.
- La moneda debe pertenecer al catálogo permitido.
- El método de pago debe pertenecer al catálogo permitido.
- La URL de pago, si se informa, debe ser válida.
- El comprobante debe cumplir formato y tamaño configurado.

## Rendimiento

La consulta principal carga relaciones con `with()` para evitar consultas N+1:

- `user`.
- `subscription`.
- `subscription.plan`.

Los filtros de búsqueda por suscriptor y plan usan `whereHas()` sobre relaciones Eloquent.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.subscription_payments
```

Los labels, botones, estados, métodos de pago, mensajes y validaciones deben mantenerse traducidos en todos los idiomas activos.

## Restricciones

- Un usuario sin `subscription_payments.view` no debe acceder a la pantalla ni a comprobantes privados aunque fuerce la URL.
- Registrar o editar pagos requiere `subscription_payments.upload_proof`.
- Confirmar pagos requiere `subscription_payments.confirm`.
- Anular pagos requiere `subscription_payments.cancel`.
- Este módulo no elimina pagos; los cambios operativos se reflejan mediante estados.
