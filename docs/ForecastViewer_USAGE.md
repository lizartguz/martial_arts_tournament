# ForecastViewer - Componente Reutilizable

## Descripción
`ForecastViewer` es un componente Livewire reutilizable diseñado para mostrar pronósticos meteorológicos de cualquier estación de manera consistente en toda la aplicación.

## Ventajas del Componente Reutilizable

1. **Escalabilidad**: Un solo lugar donde mantener la lógica de pronósticos
2. **Mantenibilidad**: Cambios en un solo archivo se reflejan en toda la aplicación
3. **Consistencia**: Misma presentación y comportamiento en todas las vistas
4. **Reutilización**: Puede ser llamado desde cualquier parte de la aplicación

---

## Formas de Usar el Componente

### 1. Incluir Directamente en una Vista Blade

```blade
{{-- Incluir el componente directamente con un stationId --}}
<livewire:forecast-viewer :stationId="$stationId" />

{{-- O usando la sintaxis alternativa --}}
@livewire('forecast-viewer', ['stationId' => $stationId])
```

#### Ejemplo en una Vista:
```blade
{{-- resources/views/stations/show.blade.php --}}
<div class="container">
    <h1>Estación {{ $station->name }}</h1>
    
    {{-- Mostrar pronóstico de la estación --}}
    <livewire:forecast-viewer :stationId="$station->id" />
</div>
```

---

### 2. Usar desde Otro Componente Livewire (Con Eventos)

#### En el componente padre:
```php
// app/Livewire/Station/Station.php

public function openModalForecasts($stationId)
{
    // Simplemente emitir un evento al componente ForecastViewer
    $this->dispatch('loadForecasts', stationId: $stationId);
}
```

#### En la vista del componente padre:
```blade
{{-- Incluir el componente en la vista --}}
<div>
    <button wire:click="openModalForecasts({{ $stationId }})">
        Ver Pronóstico
    </button>
    
    {{-- Modal o sección donde mostrar el pronóstico --}}
    <div class="modal" id="forecastModal">
        <livewire:forecast-viewer />
    </div>
</div>
```

---

### 3. Cargar Dinámicamente con Eventos

#### Componente que emite el evento:
```php
// En cualquier componente Livewire
public function showForecast($stationId)
{
    // Emitir evento global
    $this->dispatch('loadForecasts', stationId: $stationId);
}
```

#### El ForecastViewer escucha el evento automáticamente:
```php
// Ya configurado en ForecastViewer.php
protected $listeners = ['loadForecasts'];
```

---

### 4. Uso en Modal Bootstrap

```blade
{{-- En tu vista principal --}}
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#forecastModal" 
        wire:click="prepareForecast({{ $stationId }})">
    Ver Pronóstico
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
                <livewire:forecast-viewer :stationId="$selectedStationId" />
            </div>
        </div>
    </div>
</div>
```

---

### 5. Uso en Tarjetas (Cards) de Dashboard

```blade
{{-- Dashboard con múltiples estaciones --}}
<div class="row">
    @foreach($stations as $station)
        <div class="col-md-6">
            <livewire:forecast-viewer :stationId="$station->id" :key="'forecast-'.$station->id" />
        </div>
    @endforeach
</div>
```

---

## Ejemplos Prácticos de Integración

### Ejemplo 1: Reemplazar openModalForecasts en Station.php

**ANTES (método largo con toda la lógica):**
```php
public function openModalForecasts($stationId){
    // 200+ líneas de código...
    $this->varStationId = $stationId;
    $this->viewType = "forecasts";
    // ... mucho código ...
}
```

**DESPUÉS (simplificado):**
```php
public function openModalForecasts($stationId){
    $this->selectedStationId = $stationId;
    $this->dispatch('loadForecasts', stationId: $stationId);
    $this->viewType = "forecasts";
}
```

### Ejemplo 2: Crear una Página Dedicada de Pronósticos

```php
// app/Livewire/ForecastsPage.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\StationM;

class ForecastsPage extends Component
{
    public $stations;
    public $selectedStationId;

    public function mount()
    {
        $this->stations = StationM::where('state', 1)->get();
    }

    public function selectStation($stationId)
    {
        $this->selectedStationId = $stationId;
    }

    public function render()
    {
        return view('livewire.forecasts-page');
    }
}
```

```blade
{{-- resources/views/livewire/forecasts-page.blade.php --}}
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h4>Estaciones</h4>
            <div class="list-group">
                @foreach($stations as $station)
                    <button type="button" 
                            class="list-group-item list-group-item-action"
                            wire:click="selectStation({{ $station->id }})">
                        {{ $station->name }}
                    </button>
                @endforeach
            </div>
        </div>
        
        <div class="col-md-9">
            @if($selectedStationId)
                <livewire:forecast-viewer :stationId="$selectedStationId" 
                                          :key="'forecast-'.$selectedStationId" />
            @else
                <div class="alert alert-info">
                    Seleccione una estación para ver el pronóstico
                </div>
            @endif
        </div>
    </div>
</div>
```

---

## API del Componente

### Propiedades Públicas

| Propiedad | Tipo | Descripción |
|-----------|------|-------------|
| `$stationId` | int | ID de la estación |
| `$tempoutF` | float | Temperatura actual |
| `$tempout_max_dayF` | float | Temperatura máxima del día |
| `$tempout_min_dayF` | float | Temperatura mínima del día |
| `$windspeedF` | float | Velocidad del viento |
| `$humoutF` | float | Humedad |
| `$raintotalF` | float | Precipitación total |
| `$icon_imageF` | string | Icono del clima |
| `$dataForecastsCurrent` | Collection | Pronósticos del día actual |
| `$dataForecasts` | Collection | Todos los pronósticos |

### Métodos Públicos

| Método | Parámetros | Descripción |
|--------|-----------|-------------|
| `loadForecasts()` | `$stationId` | Carga los pronósticos de una estación |
| `pPrec()` | `$prec` | Calcula porcentaje de precipitación |
| `pPrecTXT()` | `$prec` | Obtiene texto descriptivo de precipitación |
| `getPeriod()` | `$dateTime` | Obtiene período del día |
| `getPeriodRange()` | `$dateTime` | Obtiene rango horario del período |

### Eventos

| Evento | Parámetros | Descripción |
|--------|-----------|-------------|
| `loadForecasts` | `stationId` | Carga pronósticos de una estación |

---

## Personalización

### Personalizar la Vista
Puedes personalizar la vista publicándola:

```bash
php artisan vendor:publish --tag=livewire-views
```

Luego edita: `resources/views/livewire/forecast-viewer.blade.php`

### Extender el Componente
Puedes crear un componente hijo que extienda ForecastViewer:

```php
namespace App\Livewire;

use App\Livewire\ForecastViewer;

class CustomForecastViewer extends ForecastViewer
{
    // Añadir funcionalidad personalizada
    public function exportToPDF()
    {
        // Tu código aquí
    }
}
```

---

## Mejores Prácticas

1. **Usar Key en Loops**: Siempre usa `:key` cuando uses el componente en loops
   ```blade
   @foreach($stations as $station)
       <livewire:forecast-viewer :stationId="$station->id" :key="'forecast-'.$station->id" />
   @endforeach
   ```

2. **Lazy Loading**: Para mejor rendimiento en páginas con múltiples pronósticos
   ```blade
   <livewire:forecast-viewer :stationId="$stationId" lazy />
   ```

3. **Polling Automático**: Para actualizar pronósticos automáticamente
   ```blade
   <livewire:forecast-viewer :stationId="$stationId" wire:poll.60s />
   ```

---

## Troubleshooting

### El componente no se actualiza
- Asegúrate de usar `:key` con valores únicos en loops
- Verifica que el evento se esté emitiendo correctamente

### Error "Station not found"
- Verifica que el `stationId` sea válido
- Asegúrate de que la estación exista en la base de datos

### Los iconos no se muestran
- Verifica que las imágenes existan en `public/images/icons/`
- Revisa que los nombres de archivos coincidan con los valores en la BD

---

## Ventajas sobre el Método Original

| Aspecto | Método Original | ForecastViewer |
|---------|----------------|----------------|
| Líneas de código | ~200+ por uso | ~5 líneas |
| Mantenibilidad | Difícil (código duplicado) | Fácil (un solo lugar) |
| Reutilización | No | Sí |
| Consistencia | Variable | Garantizada |
| Testing | Complejo | Simple |
| Escalabilidad | Limitada | Excelente |

---

## Conclusión

El componente `ForecastViewer` transforma tu aplicación en un sistema más escalable y mantenible, siguiendo las mejores prácticas de desarrollo de software como DRY (Don't Repeat Yourself) y SoC (Separation of Concerns).

