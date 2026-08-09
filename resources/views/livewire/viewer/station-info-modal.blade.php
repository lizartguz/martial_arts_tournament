<div>
    @if($showContent)
        <x-tw-modal close="closeModal" max-width="6xl"
                    header-class="bg-blue-600 text-white"
                    icon="fas fa-broadcast-tower"
                    :title="$stationData ? $stationData->name : __('messages.viewer.station_info.modal_title')">

            @if($stationData)
                {{-- DATOS ACTUALES --}}
                <div class="mb-3 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-sky-500 px-4 py-2 text-white">
                        <h5 class="m-0 text-sm font-semibold"><i class="fas fa-info-circle mr-1"></i> {{ __('messages.viewer.station_info.sections.current_data') }}</h5>
                    </div>
                    <div class="p-4">
                        <div class="mb-2">
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                <strong>{{ __('messages.viewer.station_info.fields.location') }}:</strong> {{ $stationData->location }}
                            </p>
                            <small class="text-gray-500">
                                <i class="fas fa-map-pin"></i>
                                {{ __('messages.viewer.station_info.fields.latitude') }}: {{ number_format($stationData->latitude, 6) }} |
                                {{ __('messages.viewer.station_info.fields.longitude') }}: {{ number_format($stationData->longitude, 6) }}
                            </small>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3 md:grid-cols-4">
                            <div class="rounded border border-gray-200 p-2 dark:border-gray-600">
                                <small class="block text-gray-500">{{ __('messages.viewer.station_info.fields.temperature') }}</small>
                                <h5 class="m-0 text-lg font-semibold">{{ number_format($stationData->tempout ?? 0, 1) }} °C</h5>
                            </div>
                            <div class="rounded border border-gray-200 p-2 dark:border-gray-600">
                                <small class="block text-gray-500">{{ __('messages.viewer.station_info.fields.humidity') }}</small>
                                <h5 class="m-0 text-lg font-semibold">{{ number_format($stationData->humout ?? 0, 0) }} %</h5>
                            </div>
                            <div class="rounded border border-gray-200 p-2 dark:border-gray-600">
                                <small class="block text-gray-500">{{ __('messages.viewer.station_info.fields.wind') }}</small>
                                <h5 class="m-0 text-lg font-semibold">{{ number_format($stationData->windspeed ?? 0, 1) }} km/h</h5>
                            </div>
                            <div class="rounded border border-gray-200 p-2 dark:border-gray-600">
                                <small class="block text-gray-500">{{ __('messages.viewer.station_info.fields.rain') }}</small>
                                <h5 class="m-0 text-lg font-semibold">{{ number_format($stationData->raintotal ?? 0, 1) }} mm</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PRONÓSTICOS --}}
                <div class="mb-3 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-emerald-600 px-4 py-2 text-white">
                        <h5 class="m-0 text-sm font-semibold"><i class="fas fa-cloud-sun mr-1"></i> {{ __('messages.viewer.station_info.sections.forecast') }}</h5>
                    </div>
                    <div class="p-4">
                        @if($tempoutF || $icon_imageF)
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ number_format($tempoutF ?? 0, 1) }}°C</h3>
                                    @if($tempout_max_dayF || $tempout_min_dayF)
                                        <p class="text-gray-500">
                                            <i class="fas fa-arrow-up text-red-600"></i> {{ number_format($tempout_max_dayF ?? 0, 1) }}°C
                                            <i class="fas fa-arrow-down text-blue-600 ml-2"></i> {{ number_format($tempout_min_dayF ?? 0, 1) }}°C
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($icon_imageF)
                                        <img src="{{ asset('images/forecast/'.$icon_imageF.'.gif') }}"
                                             alt="Weather Icon"
                                             style="width: 80px; height: 80px;">
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Pronósticos por hora (hoy) --}}
                        @if(count($dataForecastsCurrent) > 0)
                            <h6 class="mt-3 border-b pb-2 font-semibold">{{ __('messages.viewer.station_info.fields.forecast_today') }}</h6>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                                @foreach($dataForecastsCurrent as $record)
                                    <div>
                                        <div class="forecast-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); color: white;">
                                            <div class="mb-2 text-center">
                                                <h5 class="mb-1 text-lg"><strong>{{ $record['formatted_time'] }}</strong></h5>
                                                @if($record['icon_image'])
                                                    <img src="{{ asset('images/forecast/'.$record['icon_image'].'.gif') }}"
                                                         style="width: 60px; height: 60px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                                                @endif
                                            </div>

                                            <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; backdrop-filter: blur(10px);">
                                                {{-- Temperatura --}}
                                                <div class="mb-2 flex items-center justify-between border-b pb-2" style="border-color: rgba(255,255,255,0.2) !important;">
                                                    <span><i class="fas fa-thermometer-half"></i> {{ __('messages.viewer.station_info.fields.temperature') }}</span>
                                                    <strong>{{ number_format($record['t2m'], 1) }}°C</strong>
                                                </div>

                                                {{-- Humedad --}}
                                                <div class="mb-2 flex items-center justify-between border-b pb-2" style="border-color: rgba(255,255,255,0.2) !important;">
                                                    <span><i class="fas fa-tint"></i> {{ __('messages.viewer.station_info.fields.humidity') }}</span>
                                                    <strong>{{ number_format($record['hr2m'], 0) }}%</strong>
                                                </div>

                                                {{-- Precipitación --}}
                                                <div class="mb-2 flex items-center justify-between border-b pb-2" style="border-color: rgba(255,255,255,0.2) !important;">
                                                    <span><i class="fas fa-cloud-rain"></i> {{ __('messages.viewer.station_info.fields.rain') }}</span>
                                                    <strong>{{ number_format($record['prec'] ?? 0, 1) }} mm</strong>
                                                </div>

                                                {{-- Viento --}}
                                                <div class="mb-2 flex items-center justify-between border-b pb-2" style="border-color: rgba(255,255,255,0.2) !important;">
                                                    <span><i class="fas fa-wind"></i> {{ __('messages.viewer.station_info.fields.wind') }}</span>
                                                    <strong>{{ number_format($record['v10m'] ?? 0, 1) }} km/h</strong>
                                                </div>

                                                {{-- Ráfaga --}}
                                                <div class="mb-2 flex items-center justify-between border-b pb-2" style="border-color: rgba(255,255,255,0.2) !important;">
                                                    <span><i class="fas fa-tornado"></i> {{ __('messages.viewer.station_info.fields.gust') }}</span>
                                                    <strong>{{ number_format($record['v10m_max'] ?? 0, 1) }} km/h</strong>
                                                </div>

                                                {{-- Presión --}}
                                                <div class="flex items-center justify-between">
                                                    <span><i class="fas fa-tachometer-alt"></i> {{ __('messages.viewer.station_info.fields.pressure') }}</span>
                                                    <strong>{{ number_format($record['baro'] ?? 0, 0) }} mb</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Pronósticos extendidos --}}
                        @if(count($dataForecasts) > 0)
                            <h6 class="mt-4 border-b pb-2 font-semibold">{{ __('messages.viewer.station_info.fields.forecast_upcoming') }}</h6>
                            @foreach($dataForecasts as $dayData)
                                <div class="mb-4">
                                    <div class="mb-3 flex items-center">
                                        <div style="width: 4px; height: 25px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 2px; margin-right: 10px;"></div>
                                        <h6 class="m-0 font-semibold"><strong>{{ $dayData['date'] }}</strong></h6>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                                        @foreach($dayData['records'] as $record)
                                            <div>
                                                <div class="forecast-card-extended" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.15); color: white; transition: all 0.3s ease;">
                                                    <div class="mb-2 text-center">
                                                        <small class="mb-1 block"><strong>{{ $record['formatted_time'] }}</strong></small>
                                                        @if($record['icon_image'])
                                                            <img src="{{ asset('images/forecast/'.$record['icon_image'].'.gif') }}"
                                                                 style="width: 45px; height: 45px; filter: drop-shadow(0 2px 3px rgba(0,0,0,0.3));">
                                                        @endif
                                                    </div>

                                                    <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 8px; backdrop-filter: blur(10px);">
                                                        {{-- Temperatura --}}
                                                        <div class="mb-1 flex items-center justify-between">
                                                            <small><i class="fas fa-thermometer-half"></i></small>
                                                            <small><strong>{{ number_format($record['t2m'], 1) }}°C</strong></small>
                                                        </div>

                                                        {{-- Humedad --}}
                                                        <div class="mb-1 flex items-center justify-between">
                                                            <small><i class="fas fa-tint"></i></small>
                                                            <small><strong>{{ number_format($record['hr2m'], 0) }}%</strong></small>
                                                        </div>

                                                        {{-- Precipitación --}}
                                                        <div class="mb-1 flex items-center justify-between">
                                                            <small><i class="fas fa-cloud-rain"></i></small>
                                                            <small><strong>{{ number_format($record['prec'] ?? 0, 1) }} mm</strong></small>
                                                        </div>

                                                        {{-- Viento --}}
                                                        <div class="flex items-center justify-between">
                                                            <small><i class="fas fa-wind"></i></small>
                                                            <small><strong>{{ number_format($record['v10m'] ?? 0, 1) }} km/h</strong></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <style>
                                .forecast-card-extended:hover {
                                    transform: translateY(-5px);
                                    box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
                                }
                            </style>
                        @else
                            <p class="text-gray-500">{{ __('messages.viewer.station_info.fields.no_forecast') }}</p>
                        @endif
                    </div>
                </div>

                {{-- ALERTAS --}}
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-amber-400 px-4 py-2 text-gray-900">
                        <h5 class="m-0 text-sm font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i> {{ __('messages.viewer.station_info.sections.alerts') }}</h5>
                    </div>
                    <div class="bg-gray-50 p-4 dark:bg-gray-900/40">
                        @if(count($alerts) > 0)
                            <div class="alerts-container">
                                @foreach($alerts as $alert)
                                    <div class="alert-item mb-3" style="background: white; border-left: 4px solid #ffc107; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                                        <div class="flex items-start">
                                            <div class="mr-3 flex-shrink-0">
                                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-bell text-white" style="font-size: 20px;"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="mb-2 flex items-start justify-between">
                                                    <h6 class="m-0 font-bold text-gray-900">{{ __('messages.viewer.station_info.fields.station_alert') }}</h6>
                                                    <span class="tw-badge tw-badge-amber">
                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($alert['reg_date'])->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                                <p class="mb-1 text-gray-600" style="line-height: 1.6;">
                                                    {{ $alert['message'] ?? $alert['description'] ?? __('messages.viewer.alerts.no_description') }}
                                                </p>
                                                <div class="mt-2">
                                                    <small class="text-gray-500">
                                                        <i class="far fa-calendar-alt"></i>
                                                        {{ \Carbon\Carbon::parse($alert['reg_date'])->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <style>
                                .alert-item:hover {
                                    transform: translateX(5px);
                                    box-shadow: 0 4px 8px rgba(0,0,0,0.12) !important;
                                }
                            </style>
                        @else
                            <div class="py-4 text-center">
                                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                    <i class="fas fa-check-circle text-blue-600" style="font-size: 40px;"></i>
                                </div>
                                <h6 class="m-0 text-gray-500">{{ __('messages.viewer.station_info.fields.alerts_empty_title') }}</h6>
                                <small class="text-gray-500">{{ __('messages.viewer.station_info.fields.alerts_empty_subtitle') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="tw-alert tw-alert-blue">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('messages.viewer.station_info.select_station') }}
                </div>
            @endif

            <x-slot:footer>
                <button type="button" wire:click="closeModal" class="tw-btn tw-btn-gray">
                    <i class="fas fa-times mr-1"></i> {{ __('messages.actions.close') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif
</div>
