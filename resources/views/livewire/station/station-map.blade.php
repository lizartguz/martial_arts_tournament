<div wire:init="loadMarkers">
    <style>
        .map-weather-controls {
            position: absolute;
            top: 18px;
            right: 60px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .map-processing-overlay {
            position: absolute;
            inset: 0;
            z-index: 1200;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(2px);
        }

        .dark .map-processing-overlay {
            background-color: rgba(17, 24, 39, 0.85);
        }

        @media (max-width: 767.98px) {
            .map-weather-controls {
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                flex-direction: column;
                align-items: stretch;
            }
        }

    </style>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
        <div class="p-0">
            <div style="position: relative; height: 75vh; width: 100%;">

                {{-- Indicador mientras se cargan las estaciones (carga diferida con wire:init). --}}
                <div wire:loading.flex wire:target="loadMarkers"
                     class="map-processing-overlay">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ __('messages.stations.map.loading') }}
                    </span>
                </div>

                {{-- Indicadores de acciones que recargan o enfocan el mapa. --}}
                <div wire:loading.flex wire:target="setGoMarkerZoom"
                     class="map-processing-overlay">
                    <i class="fas fa-crosshairs fa-spin text-3xl text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ __('messages.stations.map.locating_station') }}
                    </span>
                </div>

                <div wire:loading.flex wire:target="setweatherType"
                     class="map-processing-overlay">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ __('messages.stations.map.updating_map') }}
                    </span>
                </div>

                <x-station.map-search-panel :data="$data" />

                <!-- Botones dentro del mapa (derecha superior, debajo de la barra de colores) -->
                <div class="map-weather-controls">
                    <button class="tw-btn-xs tw-btn-red" wire:click="setweatherType('temperature')">
                        <i class="fas fa-thermometer-half mr-1"></i>{{ __('messages.stations.map.controls.temperature') }}
                    </button>
                    <button class="tw-btn-xs tw-btn-gray" wire:click="setweatherType('wind')">
                        <i class="fas fa-wind mr-1"></i>{{ __('messages.stations.map.controls.wind') }}
                    </button>
                    <button class="tw-btn-xs tw-btn-blue" wire:click="setweatherType('rain')">
                        <i class="fas fa-cloud-rain mr-1"></i>{{ __('messages.stations.map.controls.rain') }}
                    </button>
                </div>

                <!-- Leyenda meteorologica responsive -->
                <x-station.weather-legend :weather-type="$weather_type" />

                @if(!is_null($stationId))
                <div style="position: absolute; top: 10px; right: 10px; z-index: 1000;">
                    <span id="distance" class="tw-badge tw-badge-blue"></span>
                </div>
                @endif

                <!-- Mapa ocupa todo el espacio -->
                <div wire:ignore id="map" style="height: 100%; width: 100%;"></div>

                <!-- Canvas para efecto de lluvia -->
                <canvas id="rain-canvas" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    {{-- Modal Unificado con Datos, Pronósticos y Alertas --}}
    <livewire:viewer.station-info-modal />

    {{-- Modal de Más Datos Meteorológicos (Gráficos Steelseries) --}}
    <livewire:viewer.more-data-modal />
</div>
