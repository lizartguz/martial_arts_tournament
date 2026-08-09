<div>
    @if($showContent && !empty($dataForSteelseries))
        <x-tw-modal close="closeMoreDataModal" max-width="7xl"
                    header-class="bg-indigo-600 text-white"
                    icon="fas fa-chart-line"
                    :title="(!empty($dataForSteelseries['name']) ? $dataForSteelseries['name'].' - ' : '') . __('messages.viewer.more_data.modal_title')">

            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                {{-- Columna izquierda: Steelseries gauges --}}
                <div class="flex flex-col items-center lg:col-span-3" style="max-height: 70vh; overflow-y: auto;">

                    {{-- Temperatura --}}
                    <a href="javascript:void(0);" wire:click="handleClick('tempout')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.temperature') }}</span>
                        <steelseries-linear width="90" height="185" value="{{ $dataForSteelseries['tempout'] ?? 0 }}"
                            gaugeType="TYPE2" titleString="{{ __('messages.viewer.more_data.gauges.temperature') }}" unitString="°C"
                            frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                        <span>{{ __('messages.viewer.more_data.gauges.actual') }} {{ $dataForSteelseries['tempout'] ?? 0 }} °C</span>
                        <span>{{ __('messages.viewer.more_data.gauges.max') }} {{ $dataForSteelseries['tempoutmax'] ?? 0 }} °C</span>
                        <span>{{ __('messages.viewer.more_data.gauges.min') }} {{ $dataForSteelseries['tempoutmin'] ?? 0 }} °C</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Humedad --}}
                    <a href="javascript:void(0);" wire:click="handleClick('humout')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.humidity') }}</span>
                        <steelseries-linear width="90" height="185" value="{{ $dataForSteelseries['humout'] ?? 0 }}"
                            gaugeType="TYPE2" titleString="{{ __('messages.viewer.more_data.gauges.humidity') }}" unitString="%"
                            frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                        <span class="weather-text">{{ $dataForSteelseries['humout'] ?? 0 }} %</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Precipitación --}}
                    <a href="javascript:void(0);" wire:click="handleClick('raintotal')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.rain') }}</span>
                        <steelseries-linear width="90" height="185" value="{{ $dataForSteelseries['raintotal'] ?? 0 }}"
                            gaugeType="TYPE2" titleString="{{ __('messages.viewer.more_data.gauges.rain') }}" unitString="mm"
                            frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                        <span class="weather-text">{{ $dataForSteelseries['raintotal'] ?? 0 }} mm</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Punto de Rocío --}}
                    <a href="javascript:void(0);" wire:click="handleClick('dewptout')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.dew_point') }}</span>
                        <steelseries-linear width="90" height="185" value="{{ $dataForSteelseries['dewptout'] ?? 0 }}"
                            gaugeType="TYPE2" titleString="{{ __('messages.viewer.more_data.gauges.dew_point') }}" unitString="°C"
                            frameDesign="ANTHRACITE" backgroundColor="WHITE" lcdColor="WHITE" lcdDecimals="1"></steelseries-linear>
                        <span class="weather-text">{{ $dataForSteelseries['dewptout'] ?? 0 }} °C</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Dirección del Viento --}}
                    <a href="javascript:void(0);" wire:click="handleClick('winddir')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.wind_direction') }}</span>
                        <steelseries-compass size="150" value="{{ $dataForSteelseries['winddir'] ?? 0 }}"
                            frameDesign="TILTED_BLACK" backgroundColor="WHITE" pointerType="TYPE2" pointerColor="RED" knobStyle="SILVER"></steelseries-compass>
                        <span class="weather-text">{{ $dataForSteelseries['winddir'] ?? 0 }} °</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Velocidad del Viento --}}
                    <a href="javascript:void(0);" wire:click="handleClick('windspeed')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.wind_speed') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['windspeed'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="Km/h" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['windspeed'] ?? 0 }} Km/h</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Rayos UV --}}
                    <a href="javascript:void(0);" wire:click="handleClick('uvindex')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.uv_index') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['uvindex'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['uvindex'] ?? 0 }}</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Presión Barométrica --}}
                    <a href="javascript:void(0);" wire:click="handleClick('press')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.pressure') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['press'] ?? 0 }}"
                            threshold="1000" gaugeType="TYPE3" unitString="mb" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['press'] ?? 0 }} mb</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Evapotranspiración --}}
                    <a href="javascript:void(0);" wire:click="handleClick('solevo')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.evapotranspiration') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['solevo'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="mm" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['solevo'] ?? 0 }} mm</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Temperatura del Suelo --}}
                    <a href="javascript:void(0);" wire:click="handleClick('soiltemp1')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.soil_temperature') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['soiltemp1'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="°C" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['soiltemp1'] ?? 0 }} °C</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Humedad del Suelo --}}
                    <a href="javascript:void(0);" wire:click="handleClick('soilhum1')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.soil_moisture') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['soilhum1'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="cb" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['soilhum1'] ?? 0 }} cb</span>
                    </a>
                    <hr class="my-2 w-full border-gray-200">

                    {{-- Humedad Foliar --}}
                    <a href="javascript:void(0);" wire:click="handleClick('leafhum1')" class="flex w-full flex-col items-center px-2 py-2">
                        <span>{{ __('messages.viewer.more_data.gauges.leaf_moisture') }}</span>
                        <steelseries-radial size="180" value="{{ $dataForSteelseries['leafhum1'] ?? 0 }}"
                            threshold="150" gaugeType="TYPE3" unitString="" frameDesign="ANTHRACITE" backgroundColor="LIGHT_GRAY"
                            pointerType="TYPE6" knobStyle="SILVER" lcdColor="WHITE" lcdDecimals="1" foregroundType="TYPE2" tickLabelOrientation="NORMAL"></steelseries-radial>
                        <span class="weather-text">{{ $dataForSteelseries['leafhum1'] ?? 0 }}</span>
                    </a>
                </div>

                {{-- Columna derecha: Filtros y Gráfico --}}
                <div class="lg:col-span-9">
                    {{-- Filtros --}}
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex w-full flex-col sm:w-auto sm:flex-1">
                            <label class="tw-label text-center">{{ __('messages.viewer.more_data.filters.date_range') }}</label>
                            <div class="flex gap-2">
                                <input type="date" wire:model="selectedFromDate" class="tw-input tw-input-sm">
                                <input type="date" wire:model="selectedToDate" class="tw-input tw-input-sm">
                            </div>
                        </div>
                        <div class="w-full sm:w-40">
                            <label class="tw-label">{{ __('messages.viewer.more_data.filters.format') }}</label>
                            <select wire:model="formatType" class="tw-input tw-input-sm">
                                <option value="all">{{ __('messages.viewer.more_data.filters.format_all') }}</option>
                                <option value="daily">{{ __('messages.viewer.more_data.filters.format_daily') }}</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <div wire:loading><div class="tw-spinner tw-spinner-sm"></div></div>
                            <button class="tw-btn-xs tw-btn-emerald" wire:click='viewGraph'>{{ __('messages.viewer.more_data.filters.view') }}</button>
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button class="tw-btn-xs tw-btn-gray" type="button" @click="open = !open">
                                    {{ __('messages.viewer.more_data.filters.actions') }} <i class="fas fa-caret-down ml-1"></i>
                                </button>
                                <div x-show="open" x-cloak class="absolute right-0 z-50 mt-1 w-48 rounded border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-600 dark:bg-gray-700">
                                    <button class="block w-full px-4 py-1.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600" type="button" onclick="resetZoom()" @click="open=false">{{ __('messages.viewer.more_data.filters.reset_zoom') }}</button>
                                    <div class="my-1 border-t border-gray-200 dark:border-gray-600"></div>
                                    <button class="block w-full px-4 py-1.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600" type="button" onclick="downloadImage('png')" @click="open=false">{{ __('messages.viewer.more_data.filters.download_png') }}</button>
                                    <button class="block w-full px-4 py-1.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600" type="button" onclick="downloadImage('jpeg')" @click="open=false">{{ __('messages.viewer.more_data.filters.download_jpeg') }}</button>
                                    <button class="block w-full px-4 py-1.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600" type="button" onclick="downloadPDF()" @click="open=false">{{ __('messages.viewer.more_data.filters.download_pdf') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico --}}
                    <div style="position: relative; height:60vh; width:100%; margin-top: 20px;">
                        <canvas id="graphMoreData" height="60vh" width="250"></canvas>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <button type="button" wire:click="closeMoreDataModal" class="tw-btn tw-btn-gray">
                    <i class="fas fa-times mr-1"></i> {{ __('messages.actions.close') }}
                </button>
            </x-slot:footer>
        </x-tw-modal>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            // Actualización del gráfico
            Livewire.on("updateChart", (chartData) => {
                setTimeout(() => {
                    let canvas = document.getElementById('graphMoreData');
                    if (canvas) {
                        let ctx = canvas.getContext('2d');
                        if (ctx) {
                            if (window.myChartMoreData) {
                                window.myChartMoreData.destroy();
                            }
                            window.myChartMoreData = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: chartData[0].labels,
                                    datasets: [{
                                        label: chartData[0].title,
                                        data: chartData[0].data,
                                        borderColor: "#667eea",
                                        backgroundColor: "rgba(102, 126, 234, 0.1)",
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: {
                                            type: 'category',
                                            ticks: {
                                                autoSkip: true,
                                                maxTicksLimit: 10
                                            }
                                        },
                                        y: {
                                            beginAtZero: false
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        },
                                        zoom: {
                                            zoom: {
                                                wheel: {
                                                    enabled: true,
                                                },
                                                pinch: {
                                                    enabled: true
                                                },
                                                mode: 'xy',
                                            },
                                            pan: {
                                                enabled: true,
                                                mode: 'xy',
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                }, 500);
            });
        });

        // Funciones para descargas
        function resetZoom() {
            if (window.myChartMoreData) {
                window.myChartMoreData.resetZoom();
            }
        }

        function downloadImage(format) {
            const canvas = document.getElementById('graphMoreData');
            const tempCanvas = document.createElement('canvas');
            const ctx = tempCanvas.getContext('2d');

            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            ctx.drawImage(canvas, 0, 0);

            const link = document.createElement('a');
            link.href = tempCanvas.toDataURL(`image/${format}`);
            link.download = `chart.${format}`;
            link.click();
        }

        function downloadPDF() {
            const canvas = document.getElementById('graphMoreData');
            const tempCanvas = document.createElement('canvas');
            const ctx = tempCanvas.getContext('2d');

            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            ctx.drawImage(canvas, 0, 0);

            const canvasImage = tempCanvas.toDataURL('image/jpeg', 1.0);

            let pdf = new jspdf.jsPDF({ orientation: 'landscape' });
            pdf.setFontSize(20);
            pdf.addImage(canvasImage, 'jpeg', 15, 15, 260, 140);
            pdf.save('chart.pdf');
        }
    </script>
</div>
