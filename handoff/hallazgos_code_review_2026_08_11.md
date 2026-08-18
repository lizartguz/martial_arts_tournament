# Hallazgos de code review — 2026-08-11

Revisión con 10 agentes (ángulos A–E, Reuse, Simplification, Efficiency, Altitude, Conventions) sobre el diff de: Fase 0 (limpieza legacy), fix de login, notificaciones, landing pública, portal del suscriptor, y limpieza de RolesTable/mount() guards. 14 hallazgos verificados.

**Actualización 2026-08-17:** 13 de los 14 hallazgos (#1–#13) fueron corregidos por el commit `e5c906b` ("Corregir hallazgos reales del portal y landing MMA"), aplicado el mismo día ~50 minutos después de esta revisión. El hallazgo #14 se dejó sin cambios a propósito, tal como concluía su propia solución recomendada. Verificado contra el código actual — no quedan hallazgos abiertos de esta lista.

## Bugs de comportamiento

### 1. Empates/no-contest se muestran como "Pendiente"

**Estado:** Corregido en `e5c906b`.

**Dónde:** `resources/views/landing/fighter-detail.blade.php:73-97`

**Qué pasa:** `FightResultTable.php` pone `winner_fighter_id = null` cuando el resultado es `draw` o `no_contest` (confirmado en `winnerlessResultTypes()`). La vista del perfil público del peleador calcula `$wonFight`/`$lostFight` comparando `winner_fighter_id` con el id del peleador; si es `null`, ninguna de las dos es verdadera y cae al `@else`, que muestra la etiqueta `result_pending` ("Pendiente"). Un combate ya finalizado con empate parece, para cualquier visitante, un combate que todavía no ocurrió.

**Solución recomendada:** agregar una tercera variable `$isDraw = $fight->result && in_array($fight->result->result_type, ['draw', 'no_contest'], true)` y una rama `@elseif ($isDraw)` con una nueva clave de traducción (`mma.landing.fighters.result_draw`) antes de caer al `@else` de "Pendiente". Replicar la clave nueva en los 6 idiomas.

---

### 2. Flash `session('error')` nunca se renderiza en el portal del suscriptor

**Estado:** Corregido en `e5c906b`.

**Dónde:** `app/Http/Controllers/Subscriber/PortalController.php:76,121` (métodos `uploadPaymentProof`/`uploadRequestProof`)

**Qué pasa:** cuando el guard de estado rechaza la subida (el pago/solicitud ya no está pendiente), el controlador hace `return back()->with('error', __('mma.subscriber_portal.purchases.proof_not_allowed'));`. Pero `resources/views/layouts/subscriber.blade.php:72-82` solo tiene bloques `@if (session('success'))` y `@if ($errors->any())` — no existe ningún `@if (session('error'))`. El usuario recarga la página, el formulario de subida desaparece (porque el guard de estado también lo oculta), y no ve ningún mensaje explicando por qué.

**Solución recomendada:** agregar en `layouts/subscriber.blade.php`, junto al bloque de `session('success')` existente, un bloque equivalente para `session('error')` (mismo patrón que ya usa `layouts/admin.blade.php:361-367`).

---

### 3. `@if ($feature->value)` oculta valores "0"

**Estado:** Corregido en `e5c906b`.

**Dónde:** `resources/views/landing/subscription.blade.php:35-36` y `resources/views/subscriber/subscription.blade.php:43-44`

**Qué pasa:** `SubscriptionPlanFeature::$value` es una columna string sin cast. PHP trata el string `"0"` como falsy, así que `@if ($feature->value)` oculta el valor cuando un admin carga, por ejemplo, "Cuota de inscripción — 0" o "Días de prueba — 0": el beneficio se muestra sin su valor real.

**Solución recomendada:** cambiar la condición a `@if (filled($feature->value))` (helper de Laravel, ya usado en otras partes del proyecto) en ambos archivos.

---

### 4. Excepción sin capturar al subir comprobante → error 500

**Estado:** Corregido en `e5c906b` (el `try/catch` se agregó dentro del nuevo `FileUploadService::replacePaymentProof()`, ver #11).

**Dónde:** `app/Http/Controllers/Subscriber/PortalController.php:92,137`

**Qué pasa:** `FileUploadService::storePaymentProof()` lanza `\InvalidArgumentException` si el MIME real del archivo no coincide exactamente con la whitelist interna (`image/jpeg`, `image/png`, `application/pdf`), aun cuando el archivo pasó la regla `mimes:jpg,jpeg,png,pdf` de Laravel (que acepta un conjunto más amplio de MIME según la extensión). `LandingController::submitContact()` envuelve la misma llamada en `try/catch` y la convierte en `ValidationException` (mensaje amigable + redirect con errores). El portal del suscriptor no lo hace, así que ese mismo caso borde termina en un error 500 sin manejar.

**Solución recomendada:** envolver ambas llamadas a `$files->storePaymentProof(...)` en `try { ... } catch (\InvalidArgumentException $e) { throw ValidationException::withMessages(['proof' => [$e->getMessage()]]); }`, igual que en `LandingController.php:242-249`.

---

### 5. Plan borrado (soft-delete) rompe el historial de pagos del suscriptor

**Estado:** Corregido en `e5c906b` (opción elegida: `->withTrashed()` en ambos modelos).

**Dónde:** `app/Models/UserSubscription.php:27` (`plan()`), `app/Models/PurchaseRequest.php:27` (`plan()`)

**Qué pasa:** `SubscriptionPlan` usa `SoftDeletes`, pero ninguna de las relaciones `plan()` que apuntan a él usa `withTrashed()`. Si un admin da de baja (soft-delete) un plan antiguo, cualquier pago o solicitud histórico que referenciaba ese plan deja de poder cargarlo: `resources/views/subscriber/payment-detail.blade.php` y `request-detail.blade.php` caen al fallback `__('mma.admin.common.not_available')` ("No disponible") en vez de mostrar el nombre real del plan (ej. "Básico"). Para el suscriptor parece que se perdió información, aunque el registro histórico sigue intacto en la base de datos.

**Solución recomendada:** cambiar `belongsTo(SubscriptionPlan::class, ...)` por `belongsTo(SubscriptionPlan::class, ...)->withTrashed()` en ambas relaciones (Eloquent soporta `withTrashed()` encadenado sobre `belongsTo` desde Laravel 9+). Alternativa si se prefiere no tocar el modelo: agregar `->withTrashed()` puntualmente en los `with()` de `PortalController` donde se cargan esos pagos/solicitudes.

---

### 6. `empty()` vs `filled()` inconsistente para `event_slug`

**Estado:** Corregido en `e5c906b`.

**Dónde:** `app/Http/Controllers/Public/LandingController.php:234` (método `submitContact`)

**Qué pasa:** `if (! empty($validated['event_slug']))` trataría un slug literal `"0"` como vacío (edge case improbable — requeriría un evento cuyo nombre generara ese slug exacto — pero real). El resto del mismo archivo usa `filled()` para este tipo de chequeo (ver `contact()` unas líneas antes), así que además de ser un bug potencial es una inconsistencia de estilo dentro del mismo archivo.

**Solución recomendada:** reemplazar por `if (filled($validated['event_slug'] ?? null))`.

## Código muerto de la limpieza de Automation

### 7. `app/View/Components/LandingLayout.php` — componente huérfano y roto si se reusa

**Estado:** Corregido en `e5c906b` (archivo eliminado).

**Qué pasa:** este componente Blade (`render()` devuelve `view('layouts.landing')`) asume que la vista imprime `{{ $slot }}` en algún lado, ya que así funciona un componente Blade estándar. Pero `layouts/landing.blade.php` fue reescrito en este mismo trabajo a un layout tradicional `@extends`/`@yield('content')` sin ninguna referencia a `$slot`. Ningún archivo usa `<x-landing-layout>` hoy (grep completo sin resultados), pero si alguien lo reintrodujera más adelante, la página renderizaría con el `<main>` completamente vacío — una trampa silenciosa.

**Solución recomendada:** eliminar `app/View/Components/LandingLayout.php` (y su vista asociada si tuviera una `resources/views/components/landing-layout.blade.php` — no la tiene, usa `layouts.landing` directamente).

---

### 8. `scripts/run_automation_probe.php` — falla si se ejecuta

**Estado:** Corregido en `e5c906b` (archivo eliminado).

**Qué pasa:** este script de prueba manual llama a `app(App\Services\Automation\AutomationExecutor::class)->execute($text)`. Esa clase, junto con todo `app/Automation/`, `app/AI/` y los bindings en `AppServiceProvider`, fue eliminada en una vuelta anterior a pedido del usuario ("bórralo todo, no lo necesitamos"). Ejecutar `php scripts/run_automation_probe.php` hoy falla con `Target class [App\Services\Automation\AutomationExecutor] does not exist`.

**Solución recomendada:** eliminar el archivo `scripts/run_automation_probe.php` (y la carpeta `scripts/` si queda vacía).

---

### 9. `app/Http/Requests/Api/v1/ExecuteAutomationRequest.php` — FormRequest huérfano

**Estado:** Corregido en `e5c906b` (archivo eliminado).

**Qué pasa:** su único consumidor era `AutomationApiController` (ya eliminado) y la ruta `/api/v1/automations/execute` ya no existe en `routes/api.php`. Grep repo-completo confirma cero referencias externas.

**Solución recomendada:** eliminar el archivo.

---

### 10. Permisos `ticket_orders.*`/`ticket_checkins.*` sin módulo detrás

**Estado:** Corregido en `e5c906b` (opción elegida: (b), retirados del seeder).

**Dónde:** `database/seeders/RolesAndPermissionsSeeder.php`

**Qué pasa:** estos permisos se siguen creando y otorgando a roles (`admin`, `sales`) aunque ya no existe ninguna ruta, controller ni pantalla que los consuma — se ocultaron del menú por ser una fase futura documentada (venta propia de tickets / check-in), pero el seeder no se tocó. No es un hueco de seguridad activo (no hay nada que autorizar), pero un admin que revise "qué puede hacer el rol sales" ve permisos de un módulo que no existe todavía, lo cual confunde.

**Solución recomendada:** dos opciones válidas, a elección del usuario: (a) dejarlo como está a propósito, ya que son permisos "reservados" para cuando se construya el módulo de venta de tickets (igual que se decidió para `support_messages.*`); o (b) quitarlos del seeder ahora y volver a agregarlos cuando se construya el módulo, para que el catálogo de permisos refleje exactamente lo que existe hoy. No se tocó porque es una decisión de alcance, no un bug.

## Duplicación / eficiencia (menor prioridad, no bloqueante)

### 11. Lógica de subir comprobante triplicada

**Estado:** Corregido en `e5c906b` (`FileUploadService::replacePaymentProof()`).

**Dónde:** `PortalController::uploadPaymentProof` (líneas 70-103) y `uploadRequestProof` (líneas 115-148) son casi idénticos entre sí (validar → borrar comprobante previo → guardar nuevo → actualizar 3 columnas → redirect), y ya existe una tercera copia del mismo patrón en `SubscriptionPaymentTable.php:470-475`.

**Solución recomendada:** extraer un método `FileUploadService::replaceProof(Model $model, string $pathColumn, UploadedFile $file, string $prefix): array` que encapsule "borrar el anterior si existe + guardar el nuevo + devolver metadata", y llamarlo desde los 3 puntos en vez de repetir la lógica.

---

### 12. Filtro "noticia publicada" reimplementado 3 veces, sin scope en el modelo

**Estado:** Corregido en `e5c906b` (`NewsPost::scopePublished()` + `isPublished()`).

**Dónde:** `LandingController.php` en `index()` (línea ~42), `news()` (línea ~143) y `newsShow()` (línea ~157) — las dos primeras como query (`where('status',1)->where(fn...)`), la tercera como condición booleana con forma distinta.

**Solución recomendada:** agregar `scopePublished()` a `App\Models\NewsPost` (mismo patrón que ya existe en `Event::scopePublished()`, usado correctamente en el mismo archivo para eventos) y reemplazar las 3 implementaciones por `NewsPost::query()->published()` / `$newsPost->status === 1 && ...` → idealmente un método de instancia `isPublished()` en el modelo para el caso de `newsShow()`.

---

### 13. `SystemSettingsService` consultado 2 veces por página

**Estado:** Corregido en `e5c906b` (opción elegida: singleton en `AppServiceProvider::register()`).

**Dónde:** `resources/views/subscriber/payment-detail.blade.php:9`, `request-detail.blade.php:9`, `subscription.blade.php:9` — cada uno hace `app(\App\Services\SystemSettingsService::class)` para el bloque de contacto, pese a que `layouts/subscriber.blade.php:4` ya lo instancia. El servicio no es singleton (solo cachea con una propiedad `$loaded` de instancia), así que cada instanciación nueva vuelve a consultar `SystemSetting::query()->first()`.

**Solución recomendada:** opción simple — pasar `$settings` desde `PortalController` a cada vista (mismo patrón que ya usa `LandingController` en las 8 acciones de landing), en vez de resolverlo dentro del `@php` de cada vista. Opción más profunda — enlazar `SystemSettingsService` como singleton en `AppServiceProvider::register()`.

---

### 14. `mount() { Gate::authorize(...) }` duplica el `can:` de la ruta

**Estado:** Sin cambios, a propósito (ver "Solución recomendada" abajo). Verificado 2026-08-17: el patrón sigue presente en 19 componentes `*Table.php` bajo `app/Livewire/Admin/`.

**Dónde:** los 7 componentes Livewire donde se agregó hoy (`FighterTable`, `FighterTeamTable`, `FightTable`, `PurchaseRequestTable`, `UserTable`, `VenueTable`, `WeightClassTable`).

**Qué pasa:** cada uno de estos componentes solo se monta detrás de una ruta que ya exige el mismo permiso vía middleware `can:`, y Livewire 3 registra `Authorize::class` en su `PersistentMiddleware`, que vuelve a aplicar el middleware original de la ruta en cada actualización AJAX del componente (paginación, búsqueda, etc.). Es decir: el permiso ya se valida dos veces por la vía de Laravel/Livewire sin este `mount()`. **No es un bug** — se agregó hoy a pedido explícito para igualar el patrón que ya siguen ~20 componentes existentes en el proyecto — pero deja el mismo string de permiso hardcodeado en 3 lugares (seeder, ruta, componente) sin una fuente única de verdad.

**Solución recomendada:** ninguna acción requerida por ahora; es una decisión de consistencia ya tomada. Si en el futuro se quiere una sola fuente de verdad, se podría derivar el permiso desde el nombre de la ruta actual (`Route::currentRouteName()` → buscar su middleware `can:`) en un trait compartido, en vez de hardcodearlo en cada componente — pero es una refactorización de arquitectura, no una corrección urgente.

## Hallazgo menor descartado (no accionable)

- **Resaltado de navegación activa incompleto**: `layouts/landing.blade.php` y `layouts/subscriber.blade.php` resaltan la pestaña de nav solo comparando contra las rutas de índice (`landing.fighters.index`, `landing.news.index`); al visitar una página de detalle (`landing.fighters.show`, `landing.news.show`) ninguna pestaña queda resaltada. Puramente cosmético, sin impacto funcional — se documenta por transparencia, no requiere acción.
