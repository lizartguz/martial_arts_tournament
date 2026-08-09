# Estrategia de servicios reutilizables, base local y seeders MMA

## Objetivo

Este documento define reglas transversales para que la implementación MMA sea escalable, mantenible y fácil de probar desde el inicio.

La idea principal es evitar lógica duplicada en controladores, componentes Livewire o vistas. Cuando una regla pueda reutilizarse en varios módulos, debe vivir en un servicio, configuración, helper, policy, trait o clase dedicada.

## Base de datos local de trabajo

La base de datos local para migraciones, limpieza y pruebas será:

```text
artsmartialdb
```

Reglas:

- Esta base está vacía y puede usarse para ejecutar migraciones nuevas.
- En entorno local se podrá usar `php artisan migrate:fresh --seed` cuando se autorice iniciar implementación.
- Las eliminaciones de tablas heredadas se prueban primero en esta base local.
- No ejecutar comandos destructivos contra una base de producción o una base con datos reales.
- El archivo `.env` local deberá apuntar a `DB_DATABASE=artsmartialdb`.
- Antes de tocar migraciones grandes, revisar que no existan dependencias activas en rutas, modelos, vistas, Livewire, jobs o seeders.

## Centralización de archivos e imágenes

El proyecto ya cuenta con:

```text
App\Services\ImageUploadOptimizer
```

Este servicio debe ser la base para reducir y guardar imágenes en los módulos MMA. No conviene repetir `storeAs()`, validaciones de MIME, nombres de archivo o compresión dentro de cada componente.

Reglas:

- Las imágenes se validan y optimizan desde un servicio centralizado.
- Los módulos solo indican contexto: directorio, prefijo, tamaño máximo, disco y reglas del negocio.
- Si se necesita soportar PDF, debe existir una capa superior, por ejemplo `FileUploadService`, que decida:
  - imagen -> optimizar con `ImageUploadOptimizer`
  - PDF -> validar tipo/tamaño y guardar sin compresión inicial
- Los componentes Livewire no deben conocer detalles de compresión, calidad, ancho máximo o generación final de nombre.
- Los mensajes de error deben traducirse.
- Los valores repetidos deben centralizarse en configuración, por ejemplo `config/uploads.php`.

Configuración sugerida:

```php
return [
    'payment_proofs' => [
        'disk' => 'private',
        'directory' => 'payment-proofs',
        'max_mb' => 5,
        'image_mimes' => ['image/jpeg', 'image/png'],
        'document_mimes' => ['application/pdf'],
        'image_extensions' => ['jpg', 'jpeg', 'png'],
        'document_extensions' => ['pdf'],
    ],
];
```

Notas:

- Para comprobantes se aceptan `jpg`, `jpeg`, `png` y `pdf`, máximo 5 MB.
- Las imágenes se optimizan antes de guardarse.
- Los PDF se validan y guardan sin compresión inicial.
- Los comprobantes se guardan en disco privado o protegido.
- La visualización de comprobantes queda disponible solo desde sesión administrativa autorizada.
- No deben exponerse comprobantes por URL pública directa.
- Conviene guardar metadatos del archivo: ruta, MIME, tamaño final, nombre original y usuario que lo cargó.

## Servicios reutilizables recomendados

Servicios o clases transversales sugeridas:

```text
ImageUploadOptimizer
FileUploadService
SlugGeneratorService
RoleHierarchyService
PermissionCatalogService
SystemSettingsService
LocalizedLabelService
```

Responsabilidades:

- `ImageUploadOptimizer`: reducir imágenes y guardarlas con nombre seguro.
- `FileUploadService`: validar y guardar archivos por tipo de negocio.
- `SlugGeneratorService`: generar slugs únicos desde títulos o nombres.
- `RoleHierarchyService`: resolver jerarquía entre `super_manager`, `admin`, `manager`, etc.
- `PermissionCatalogService`: exponer permisos técnicos con etiquetas traducidas.
- `SystemSettingsService`: leer nombre, logo, favicon, contacto y redes desde configuración persistente.
- `LocalizedLabelService`: resolver etiquetas visibles según idioma activo.

Regla práctica:

Si una regla se usa en dos módulos o más, debe extraerse antes de duplicarse.

## Seeders base y datos ficticios

Los seeders deben separarse por intención:

```text
seeders obligatorios
seeders demo
seeders de pruebas automatizadas
```

### Seeders obligatorios

Se ejecutan en cualquier instalación:

- Roles y permisos definitivos.
- Configuración inicial del sistema con `Combate Real`.
- Categorías de peso base.
- Estados o catálogos mínimos si se implementan como tablas.
- Usuario inicial `super_manager` local/demo:
  - correo: `sarteaga@root.dev`
  - contraseña inicial local: igual al correo
  - esta credencial es solo para desarrollo y debe reemplazarse en producción.

### Seeders demo

Se usan solo para desarrollo local y demostración visual:

- Sedes ficticias.
- Equipos/gimnasios ficticios.
- Peleadores ficticios con género, país, récord y categoría.
- Eventos pasados, actuales y próximos publicados.
- Eventos en borrador para probar visibilidad.
- Combates con cartelera principal, coestelar y preliminares.
- Rankings por categoría de peso y género.
- Planes de suscripción.
- Suscriptores ficticios.
- Pagos manuales con estados variados.
- Solicitudes de compra/contacto creadas desde landing.
- Enlaces externos de tickets.

Reglas:

- Los seeders demo deben estar protegidos para no ejecutarse en producción por accidente.
- El seeder demo principal puede llamarse `MmaDemoSeeder`.
- Los datos deben permitir probar filtros, tablas, estados, dropdowns, modales, permisos y landing.
- Los textos demo deben estar bien escritos en español.
- Los slugs deben generarse con el mismo servicio real que usará la aplicación.
- Las consultas de vistas demo deben seguir evitando N+1.

Cantidad inicial sugerida:

```text
3 sedes
6 equipos/gimnasios
24 peleadores
4 categorías de peso por género
5 eventos publicados: pasados, actual y próximos
2 eventos en borrador
30 combates
3 planes de suscripción
12 suscriptores
12 pagos manuales
15 solicitudes de compra/contacto
8 enlaces externos de tickets
rankings masculinos y femeninos por categoría
```

## Datos visuales demo

Para acelerar revisión visual:

- Usar imágenes ya cargadas en el proyecto como base visual inicial para eventos y peleadores.
- No depender de URLs externas para que la demo funcione sin internet.
- Mantener tamaños razonables y pasar imágenes por el servicio central de optimización.
- Evitar archivos pesados dentro del repositorio si no son necesarios.

## Flujo recomendado para pruebas locales

Cuando se autorice iniciar implementación:

```text
1. Configurar .env con DB_DATABASE=artsmartialdb.
2. Ejecutar migraciones nuevas.
3. Ejecutar seeders obligatorios.
4. Ejecutar seeders demo solo en local.
5. Revisar login, menú, permisos y landing.
6. Validar CRUDs con datos ya cargados.
7. Revisar filtros y consultas sin N+1.
```

## Pruebas mínimas esperadas

- La base `artsmartialdb` migra desde cero sin errores.
- Los seeders obligatorios pueden repetirse sin duplicar permisos o roles.
- Los seeders demo no se ejecutan en producción.
- Las imágenes se optimizan desde un único servicio.
- Los PDF se almacenan sin pasar por optimización de imagen.
- Los comprobantes mayores a 5 MB son rechazados.
- Los comprobantes solo se visualizan desde administración.
- Las rutas protegidas validan permisos aunque el usuario fuerce la URL.
- La landing muestra eventos destacados y cercanos con datos demo.

## Decisiones cerradas para implementación inicial

- La implementación todavía no inicia hasta autorización explícita.
- El usuario inicial local/demo será `sarteaga@root.dev`.
- La contraseña local/demo inicial será igual al correo.
- Los comprobantes se guardarán en almacenamiento privado o protegido.
- Los comprobantes serán visibles solo desde sesión administrativa autorizada.
- Las imágenes demo usarán assets ya cargados en el proyecto.
