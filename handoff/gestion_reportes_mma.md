# Gestión de reportes MMA

## Alcance implementado

- Se agregó el módulo administrativo `/admin/reports`.
- El módulo no crea tablas nuevas; consume datos existentes de eventos, suscripciones, pagos y solicitudes.
- La pantalla usa un componente Livewire único con selector de tipo de reporte:
  - Eventos.
  - Suscripciones.
  - Ventas.
- Cada reporte permite filtrar por:
  - Tipo de reporte.
  - Fecha desde.
  - Fecha hasta.
  - Estado.
  - Cantidad por página.

## Permisos

| Reporte | Permiso |
| --- | --- |
| Eventos | `reports.events.view` |
| Suscripciones | `reports.subscriptions.view` |
| Ventas | `reports.sales.view` |

La ruta permite acceso si el usuario tiene al menos uno de esos permisos. Dentro del componente, cada tipo de reporte vuelve a validar el permiso correspondiente.

## Menú lateral

- Se agregó el grupo `Reportes`.
- Las opciones del menú apuntan a `/admin/reports?type=...`.
- Cada opción se muestra únicamente si el rol tiene el permiso correspondiente.

## Datos y rendimiento

- Reporte de eventos:
  - Cuenta eventos, publicados, destacados y solicitudes asociadas.
  - Lista eventos con sede, cantidad de combates, solicitudes y enlaces de tickets.
  - Usa `with()` y `withCount()` para evitar N+1.
- Reporte de suscripciones:
  - Cuenta total, activas, pendientes y vencidas.
  - Lista suscripciones con usuario, plan y cantidad de pagos.
  - Usa `with()` y `withCount()`.
- Reporte de ventas:
  - Cuenta pagos totales, pagados, pendientes y monto pagado.
  - Lista pagos con usuario, suscripción y plan.
  - Usa `with()` para evitar N+1.

## Restricciones actuales

- La exportación `reports.export` queda documentada, pero no se implementa todavía.
- No se incluyen gráficos avanzados en esta etapa.
- No se agregan reportes de tickets internos porque las tablas `ticket_orders` y `ticket_checkins` aún no están implementadas.
- No se modifica información desde los reportes; son vistas operativas de lectura.

## Idiomas

- Todos los textos visibles usan claves `mma.admin.reports.*` y `mma.menu.reports.*`.
- Se agregaron traducciones para español, inglés, portugués, francés, alemán y ruso.
