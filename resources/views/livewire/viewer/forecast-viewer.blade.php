<div>
    @if($stationId)
        <div class="forecast-container">
            {{-- Informacion principal del pronostico --}}
            @if($nameF)
                <div class="forecast-header mb-3">
                    <h4 class="text-xl font-semibold">{{ $nameF }}</h4>
                    @if($locationF)
                        <p class="text-gray-500">{{ $locationF }}</p>
                    @endif
                </div>
            @endif

            {{-- Datos actuales del pronostico --}}
            @if($tempoutF || $icon_imageF)
                <div class="current-forecast mb-3 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-4">
                        <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-2">
                            <div>
                                <div class="temperature-display">
                                    @if($tempoutF)
                                        <h2 class="text-4xl font-bold">{{ number_format($tempoutF, 1) }}&deg;C</h2>
                                    @endif

                                    @if($tempout_max_dayF || $tempout_min_dayF)
                                        <p class="text-gray-500">
                                            <i class="fas fa-arrow-up text-red-600"></i> {{ number_format($tempout_max_dayF, 1) }}&deg;C
                                            <i class="fas fa-arrow-down text-blue-600 ml-2"></i> {{ number_format($tempout_min_dayF, 1) }}&deg;C
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                @if($icon_imageF)
                                    <img src="{{ asset('images/forecast/'.$icon_imageF.'.gif') }}"
                                         alt="Weather Icon"
                                         class="weather-icon ml-auto"
                                         style="width: 100px; height: 100px;">
                                @endif
                            </div>
                        </div>

                        {{-- Datos adicionales --}}
                        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                            @if($humoutF)
                                <div>
                                    <div class="forecast-detail">
                                        <i class="fas fa-tint"></i>
                                        <span class="ml-2">{{ __('messages.viewer.forecast.current.humidity') }}: {{ number_format($humoutF, 0) }}%</span>
                                    </div>
                                </div>
                            @endif

                            @if($windspeedF)
                                <div>
                                    <div class="forecast-detail">
                                        <i class="fas fa-wind"></i>
                                        <span class="ml-2">{{ __('messages.viewer.forecast.current.wind') }}: {{ number_format($windspeedF, 1) }} km/h</span>
                                    </div>
                                </div>
                            @endif

                            @if($raintotalF)
                                <div>
                                    <div class="forecast-detail">
                                        <i class="fas fa-cloud-rain"></i>
                                        <span class="ml-2">{{ __('messages.viewer.forecast.current.rain') }}: {{ number_format($raintotalF, 1) }} mm</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($receipt_dateF)
                            <div class="mt-2">
                                <small class="text-xs text-gray-500">
                                    <i class="far fa-clock"></i>
                                    {{ __('messages.viewer.forecast.current.updated') }} {{ \Carbon\Carbon::parse($receipt_dateF)->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Pronosticos por horas (dia actual) --}}
            @if(count($dataForecastsCurrent) > 0)
                <div class="hourly-forecast mb-3 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-700">
                        <h5 class="m-0 text-lg font-semibold">{{ __('messages.viewer.forecast.hourly_title') }}</h5>
                    </div>
                    <div class="p-4">
                        @foreach($dataForecastsCurrent as $dayData)
                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                @foreach($dayData['records'] as $record)
                                    <div>
                                        <div class="forecast-hour-card rounded border border-gray-200 p-2">
                                            <div class="text-center">
                                                <p class="mb-1"><strong>{{ $record['formatted_time'] }}</strong></p>
                                                <p class="mb-1 text-xs text-gray-500">{{ $record['period'] }}</p>
                                                @if($record['icon_image'])
                                                    <img src="{{ asset('images/forecast/'.$record['icon_image'].'.gif') }}"
                                                         alt="Weather Icon"
                                                         class="mx-auto"
                                                         style="width: 50px; height: 50px;">
                                                @endif
                                                <p class="mb-1"><strong>{{ number_format($record['t2m'], 1) }}&deg;C</strong></p>
                                                <p class="mb-0 text-xs">
                                                    <i class="fas fa-tint"></i> {{ number_format($record['hr2m'], 0) }}%
                                                </p>
                                                <p class="mb-0 text-xs text-sky-600">{{ $record['percentage'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pronostico extendido (todos los dias) --}}
            @if(count($dataForecasts) > 0)
                <div class="extended-forecast rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-2 dark:border-gray-700">
                        <h5 class="m-0 text-lg font-semibold">{{ __('messages.viewer.forecast.extended_title') }}</h5>
                    </div>
                    <div class="p-4">
                        @foreach($dataForecasts as $dayData)
                            <div class="day-forecast mb-4">
                                <h6 class="border-b border-gray-200 pb-2 font-semibold">{{ $dayData['date'] }}</h6>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-6">
                                    @foreach($dayData['records'] as $record)
                                        <div>
                                            <div class="forecast-item rounded border border-gray-200 p-2 text-center">
                                                <p class="mb-1 text-xs"><strong>{{ $record['formatted_time'] }}</strong></p>
                                                @if($record['icon_image'])
                                                    <img src="{{ asset('images/forecast/'.$record['icon_image'].'.gif') }}"
                                                         alt="Weather Icon"
                                                         class="mx-auto"
                                                         style="width: 40px; height: 40px;">
                                                @endif
                                                <p class="mb-0"><strong>{{ number_format($record['t2m'], 1) }}&deg;C</strong></p>
                                                <p class="mb-0 text-xs text-gray-500">
                                                    <i class="fas fa-tint"></i> {{ number_format($record['hr2m'], 0) }}%
                                                </p>
                                                <p class="mb-0 text-xs text-sky-600">{{ $record['percentage'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Mensaje si no hay datos --}}
            @if(!$tempoutF && !$icon_imageF && count($dataForecasts) == 0)
                <div class="tw-alert tw-alert-blue">
                    <i class="fas fa-info-circle"></i> {{ __('messages.viewer.forecast.no_data') }}
                </div>
            @endif
        </div>
    @else
        <div class="tw-alert tw-alert-amber">
            <i class="fas fa-exclamation-triangle"></i> {{ __('messages.viewer.forecast.no_station') }}
        </div>
    @endif

    <style>
        .forecast-container {
            padding: 15px;
        }

        .weather-icon {
            max-width: 100%;
            height: auto;
        }

        .forecast-detail {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .forecast-hour-card {
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .forecast-hour-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .forecast-item {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .forecast-item:hover {
            background-color: #e9ecef;
            transform: scale(1.05);
        }

        .temperature-display h2 {
            margin: 0;
            font-weight: bold;
        }
    </style>
</div>
