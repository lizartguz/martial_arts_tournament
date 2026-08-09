# Ejemplo de Refactorización: Station.php con ForecastViewer

Este documento muestra cómo simplificar el componente `Station.php` usando el nuevo componente reutilizable `ForecastViewer`.

---

## ANTES: Método openModalForecasts (~200 líneas)

```php
// app/Livewire/Station/Station.php (ANTES)

public function openModalForecasts($stationId){
    date_default_timezone_set('America/La_Paz');        
    $this->varStationId = $stationId;
    $this->viewType = "forecasts";       
    
    $stationDataF = StationM::select(
        'stations.id as stationId', 'stations.name', 'stations.location', 
        // ... muchos campos ...
    )
    ->join('current_data as dd', 'dd.station_id', 'stations.id')
    ->where('stations.id',$this->varStationId)
    ->orderBy('dd.id','desc')
    ->first();      

    if ($stationDataF){
        // ... 150+ líneas de lógica compleja ...
        
        $this->tempoutF = $stationDataF->tempout;
        $this->tempout_max_dayF = $stationDataF->tempout_max_day;
        // ... más asignaciones ...
    }

    $this->dataForecastsCurrent = ForecastM::where('station_id', $this->varStationId)
    // ... más lógica ...
    ->get()
    ->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->reg_date)->format('d-m');
    })
    ->map(function ($itemsByDate, $date) {
        // ... más transformaciones ...
    });

    $this->dataForecasts = ForecastM::where('station_id', $this->varStationId)
    // ... aún más lógica ...
    ->get()
    ->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->reg_date)->format('d-m');
    })
    ->map(function ($itemsByDate, $date) {
        // ... más transformaciones ...
    });
}
```

**Problemas:**
- ❌ 200+ líneas de código difíciles de mantener
- ❌ Lógica duplicada si necesitas pronósticos en otro lugar
- ❌ Difícil de testear
- ❌ Mezcla lógica de negocio con presentación
- ❌ No reutilizable

---

## DESPUÉS: Método simplificado (~10 líneas)

```php
// app/Livewire/Station/Station.php (DESPUÉS)

public $selectedStationId;
public $showForecastModal = false;

public function openModalForecasts($stationId)
{
    $this->selectedStationId = $stationId;
    $this->viewType = "forecasts";
    
    // Opcionalmente, puedes emitir un evento para actualizar el componente hijo
    $this->dispatch('loadForecasts', stationId: $stationId);
}
```

**Ventajas:**
- ✅ Solo ~10 líneas de código
- ✅ Fácil de entender y mantener
- ✅ Lógica encapsulada en ForecastViewer
- ✅ Componente reutilizable en toda la app
- ✅ Más fácil de testear

---

## Modificación de la Vista

### ANTES: Vista con HTML repetitivo

```blade
<!-- resources/views/livewire/station/station.blade.php (ANTES) -->

@elseif($viewType=='forecasts')
    <div class="container-fluid">
        <div class="card px-2 pt-3" style="background-color:#e5e7e9;">
            <div class="row">
                <!-- Botón de refresh -->
                <button wire:click='openModalForecasts({{$varStationId}})'>
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            <!-- 500+ líneas de HTML para mostrar pronósticos -->
            <div class="row">
                @if($tempoutF)
                    <!-- Temperatura -->
                @endif
                @if($icon_imageF)
                    <!-- Icono -->
                @endif
                <!-- ... mucho más HTML ... -->
            </div>
            
            @foreach ($dataForecastsCurrent as $forecast)
                <!-- Pronósticos por hora -->
                @foreach ($forecast['records'] as $record)
                    <!-- Cada registro -->
                @endforeach
            @endforeach
            
            @foreach ($dataForecasts as $forecast)
                <!-- Pronósticos extendidos -->
                @foreach ($forecast['records'] as $record)
                    <!-- Cada registro -->
                @endforeach
            @endforeach
        </div>
    </div>
@endif
```

### DESPUÉS: Vista simplificada

```blade
<!-- resources/views/livewire/station/station.blade.php (DESPUÉS) -->

@elseif($viewType=='forecasts')
    <div class="container-fluid">
        {{-- Simplemente incluir el componente reutilizable --}}
        <livewire:forecast-viewer 
            :stationId="$selectedStationId" 
            :key="'forecast-'.$selectedStationId" />
    </div>
@endif
```

**Reducción:** De ~500 líneas de HTML a ~5 líneas! 🎉

---

## Pasos para la Refactorización

### Paso 1: Backup del código original
```bash
# Hacer una copia de seguridad
cp app/Livewire/Station/Station.php app/Livewire/Station/Station.php.backup
```

### Paso 2: Modificar Station.php

Reemplazar el método `openModalForecasts` completo por:

```php
public $selectedStationId;

public function openModalForecasts($stationId)
{
    $this->selectedStationId = $stationId;
    $this->viewType = "forecasts";
}
```

### Paso 3: Modificar la vista station.blade.php

Buscar la sección que comienza con `@elseif($viewType=='forecasts')` y reemplazar todo el bloque por:

```blade
@elseif($viewType=='forecasts')
    <div class="container-fluid">
        <div class="card px-2 pt-3" style="background-color:#e5e7e9;">
            <livewire:forecast-viewer 
                :stationId="$selectedStationId" 
                :key="'forecast-'.$selectedStationId" />
        </div>
    </div>
@endif
```

### Paso 4: Eliminar propiedades no utilizadas

Si ya no las necesitas en Station.php, puedes eliminar:

```php
// ELIMINAR (ahora están en ForecastViewer)
public $tempoutF;
public $tempout_max_dayF;
public $tempout_min_dayF;
public $windspeedF;
public $humoutF;
public $dewptoutF;
public $windspeedhiF;
public $raintotalF;
public $pressF;
public $receipt_dateF;
public $locationF;
public $nameF;
public $icon_imageF;
public $dataForecastsCurrent;
public $dataForecasts;
```

### Paso 5: Eliminar métodos helpers de Station.php

También puedes eliminar estos métodos si no se usan en otros lugares:

```php
// ELIMINAR (ahora están en ForecastViewer)
public function pPrec($prec) { ... }
public function pPrecTXT($prec) { ... }
public function getPeriod($dateTime) { ... }
public function getPeriodRange($dateTime) { ... }
```

### Paso 6: Probar

```bash
# Limpiar caché
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Probar la aplicación
```

---

## Comparación de Líneas de Código

| Archivo | Antes | Después | Reducción |
|---------|-------|---------|-----------|
| `Station.php` | ~3620 líneas | ~3200 líneas | ~420 líneas ✅ |
| `station.blade.php` | ~1000 líneas | ~500 líneas | ~500 líneas ✅ |
| **Total reducido** | | | **~920 líneas** 🎉 |

---

## Otros Lugares donde Usar ForecastViewer

### 1. En un Dashboard

```php
// app/Livewire/Dashboard.php
public function render()
{
    $stations = StationM::where('state', 1)->limit(4)->get();
    return view('livewire.dashboard', compact('stations'));
}
```

```blade
<!-- resources/views/livewire/dashboard.blade.php -->
<div class="row">
    @foreach($stations as $station)
        <div class="col-md-6 mb-3">
            <livewire:forecast-viewer 
                :stationId="$station->id" 
                :key="'forecast-'.$station->id" />
        </div>
    @endforeach
</div>
```

### 2. En un Modal Reutilizable

```blade
<!-- Botón para abrir modal -->
<button type="button" 
        class="btn btn-primary" 
        data-toggle="modal" 
        data-target="#forecastModal"
        wire:click="setStation({{ $stationId }})">
    <i class="fas fa-cloud-sun"></i> Ver Pronóstico
</button>

<!-- Modal -->
<div class="modal fade" id="forecastModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pronóstico del Tiempo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($selectedStationId)
                    <livewire:forecast-viewer 
                        :stationId="$selectedStationId" 
                        :key="'modal-forecast-'.$selectedStationId" />
                @endif
            </div>
        </div>
    </div>
</div>
```

### 3. En una API o Ruta Dedicada

```php
// routes/web.php
Route::get('/forecasts/{stationId}', function($stationId) {
    return view('forecasts.show', ['stationId' => $stationId]);
});
```

```blade
<!-- resources/views/forecasts/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Pronóstico del Tiempo</h1>
        <livewire:forecast-viewer :stationId="$stationId" />
    </div>
@endsection
```

---

## Testing Simplificado

### Antes: Difícil de testear
```php
// Tenías que testear toda la lógica dentro de Station
public function test_openModalForecasts()
{
    // Difícil: 200+ líneas de lógica para testear
}
```

### Después: Fácil de testear
```php
// tests/Feature/ForecastViewerTest.php
use Livewire\Livewire;
use App\Livewire\ForecastViewer;

public function test_forecast_viewer_loads_data()
{
    $station = StationM::factory()->create();
    
    Livewire::test(ForecastViewer::class, ['stationId' => $station->id])
        ->assertSet('stationId', $station->id)
        ->assertSee($station->name);
}

public function test_forecast_viewer_calculates_precipitation()
{
    $component = new ForecastViewer();
    
    $this->assertEquals('0%', $component->pPrec(0.3));
    $this->assertEquals('70%', $component->pPrec(1.0));
    $this->assertEquals('99%', $component->pPrec(10.0));
}
```

---

## Conclusión

La refactorización a `ForecastViewer` te proporciona:

1. ✅ **Código más limpio**: ~920 líneas menos
2. ✅ **Mejor mantenibilidad**: Un solo lugar para cambios
3. ✅ **Reutilización**: Usa el componente en cualquier lugar
4. ✅ **Testing más fácil**: Componente aislado
5. ✅ **Escalabilidad**: Arquitectura más sólida
6. ✅ **Consistencia**: Misma UI en toda la app

**¡Tu sistema ahora es mucho más escalable! 🚀**

