# 🚀 Guía Rápida: ForecastViewer

## ¿Qué es ForecastViewer?

Un componente Livewire reutilizable que muestra pronósticos meteorológicos. Convierte 200+ líneas de código en solo 5 líneas.

---

## 📦 Instalación (Ya está listo!)

Los archivos ya están creados:
- ✅ `app/Livewire/ForecastViewer.php` - Componente Livewire
- ✅ `resources/views/livewire/forecast-viewer.blade.php` - Vista

---

## 🎯 Uso Básico (3 formas)

### 1️⃣ En cualquier vista Blade

```blade
{{-- La forma más simple --}}
<livewire:forecast-viewer :stationId="123" />
```

### 2️⃣ Desde otro componente Livewire

```php
// En tu componente
public $selectedStationId = 123;
```

```blade
{{-- En tu vista --}}
<livewire:forecast-viewer :stationId="$selectedStationId" />
```

### 3️⃣ Con eventos dinámicos

```php
// En tu componente padre
public function showForecast($stationId)
{
    $this->dispatch('loadForecasts', stationId: $stationId);
}
```

```blade
{{-- En tu vista --}}
<livewire:forecast-viewer />
```

---

## 💡 Ejemplos Prácticos

### Ejemplo 1: Botón que abre pronóstico

```blade
<button wire:click="$set('selectedStation', {{ $station->id }})">
    Ver Pronóstico
</button>

<livewire:forecast-viewer :stationId="$selectedStation" />
```

### Ejemplo 2: Modal con pronóstico

```blade
<button data-toggle="modal" data-target="#forecastModal">
    Pronóstico
</button>

<div class="modal" id="forecastModal">
    <div class="modal-body">
        <livewire:forecast-viewer :stationId="{{ $stationId }}" />
    </div>
</div>
```

### Ejemplo 3: Lista de estaciones

```blade
<div class="row">
    @foreach($stations as $station)
        <div class="col-md-6">
            <livewire:forecast-viewer 
                :stationId="$station->id" 
                :key="'forecast-'.$station->id" />
        </div>
    @endforeach
</div>
```

---

## 🔧 Cómo Reemplazar openModalForecasts

### Paso 1: Modificar Station.php

**Encuentra esto:**
```php
public function openModalForecasts($stationId){
    // 200+ líneas de código...
}
```

**Reemplaza con:**
```php
public $selectedStationId;

public function openModalForecasts($stationId)
{
    $this->selectedStationId = $stationId;
    $this->viewType = "forecasts";
}
```

### Paso 2: Modificar la vista

**Encuentra esto en `station.blade.php`:**
```blade
@elseif($viewType=='forecasts')
    <div class="container-fluid">
        <!-- 500+ líneas de HTML -->
    </div>
@endif
```

**Reemplaza con:**
```blade
@elseif($viewType=='forecasts')
    <div class="container-fluid">
        <livewire:forecast-viewer 
            :stationId="$selectedStationId" 
            :key="'forecast-'.$selectedStationId" />
    </div>
@endif
```

### Paso 3: Limpiar caché

```bash
php artisan view:clear
php artisan cache:clear
```

---

## ⚙️ Opciones Avanzadas

### Auto-actualización cada 60 segundos
```blade
<livewire:forecast-viewer :stationId="$id" wire:poll.60s />
```

### Carga diferida (mejor rendimiento)
```blade
<livewire:forecast-viewer :stationId="$id" lazy />
```

### Con placeholder mientras carga
```blade
<livewire:forecast-viewer :stationId="$id" lazy>
    <div>Cargando pronóstico...</div>
</livewire:forecast-viewer>
```

---

## 🎨 Personalización

### Cambiar estilos

Edita: `resources/views/livewire/forecast-viewer.blade.php`

```blade
<style>
    .forecast-container {
        background: linear-gradient(to bottom, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
    }
</style>
```

### Agregar funcionalidad

Edita: `app/Livewire/ForecastViewer.php`

```php
public function exportToPDF()
{
    // Tu código aquí
}
```

---

## 📊 Comparación

| Aspecto | Antes | Después |
|---------|-------|---------|
| Líneas de código | 200+ | ~5 |
| Reutilizable | ❌ No | ✅ Sí |
| Mantenible | ❌ Difícil | ✅ Fácil |
| Escalable | ❌ No | ✅ Sí |

---

## ❓ Problemas Comunes

### No se muestra nada
- Verifica que `$stationId` tenga un valor válido
- Revisa que la estación exista en la BD

### Error "Station not found"
- Asegúrate de pasar el ID correcto: `:stationId="$variable"`

### Iconos no aparecen
- Verifica que existan en: `public/images/icons/`

### No se actualiza al cambiar stación
- Usa `:key` único: `:key="'forecast-'.$stationId"`

---

## 🎓 Recursos

- 📖 [Documentación Completa](./ForecastViewer_USAGE.md)
- 🔧 [Ejemplo de Refactorización](./REFACTORING_EXAMPLE.md)
- 🌐 [Livewire Docs](https://livewire.laravel.com)

---

## ✅ Checklist de Implementación

- [ ] Componente creado (ya está ✅)
- [ ] Vista creada (ya está ✅)
- [ ] Modificar `Station.php`
- [ ] Modificar `station.blade.php`
- [ ] Limpiar caché
- [ ] Probar en navegador
- [ ] Celebrar 🎉

---

## 🚀 Resultado Final

```blade
{{-- De esto... --}}
@elseif($viewType=='forecasts')
    <div class="container-fluid">
        <!-- 500+ líneas de HTML complejo -->
        @if($stationDataF)
            <!-- Temperatura -->
        @endif
        @foreach($dataForecastsCurrent as $forecast)
            @foreach($forecast['records'] as $record)
                <!-- Cada registro -->
            @endforeach
        @endforeach
        <!-- ...más código... -->
    </div>
@endif

{{-- ...a esto! ✨ --}}
@elseif($viewType=='forecasts')
    <livewire:forecast-viewer :stationId="$selectedStationId" />
@endif
```

**¡De 500+ líneas a 1 línea! 🎉**

---

## 💪 Ventajas Inmediatas

1. ✅ Código 95% más corto
2. ✅ Fácil de mantener
3. ✅ Reutilizable en toda la app
4. ✅ Más fácil de testear
5. ✅ Mejor rendimiento
6. ✅ Arquitectura escalable

---

## 🎯 Próximos Pasos

1. Implementa `ForecastViewer` en `Station.php`
2. Prueba en tu aplicación
3. Úsalo en otros componentes donde necesites pronósticos
4. ¡Disfruta de código más limpio! 😎

---

**¿Preguntas?** Revisa la documentación completa en `ForecastViewer_USAGE.md`

**¡Happy coding! 🚀**

