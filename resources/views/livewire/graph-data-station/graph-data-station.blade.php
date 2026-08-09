<div
    x-data="{
        selectedFormat: @entangle('selectedFormat'),
    }"
>
    <h5 class="mb-4 w-full text-center text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.graphs.page') }}</h5>
    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" x-cloak>
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex w-full flex-col gap-4 md:flex-row">
                <div class="w-full" wire:ignore>
                    <label class="tw-label">{{ __('messages.graphs.station_label') }}</label>
                    <x-adminlte-select2
                        class=""
                        multiple
                        id="station-select"
                        name="station-select"
                        data-placeholder="{{ __('messages.graphs.station_placeholder') }}"
                        :config="[
                            'maximumSelectionLength' => 5,
                            'allowClear' => true,
                        ]"
                    >
                        @foreach ($stationList as $station)
                            <option value="{{ $station->id }}">{{$station->name}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="w-full" wire:ignore>
                    <label class="tw-label">{{ __('messages.graphs.variables_label') }}</label>
                    <x-adminlte-select2
                        class=""
                        multiple
                        id="variable-select"
                        name="variable-select"
                        data-placeholder="{{ __('messages.graphs.station_placeholder') }}"
                        wire:loading.attr="disabled"
                        :config="[
                            'maximumSelectionLength' => 5,
                            'allowClear' => true,
                        ]"
                    >
                        @foreach ($variableList as $key => $variable)
                            <option value="{{ $key }}">{{ $variable['label'] }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="mb-3 w-full md:max-w-44">
                    <label class="tw-label">{{ __('messages.graphs.format_label') }}</label>
                    <select id="format-select" class="tw-input">
                        @foreach ($formatList as $key => $item)
                            <option value="{{ $key }}" {{ $selectedFormat==$key ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 w-full" id="pnlDate" x-show="selectedFormat==null || selectedFormat!='annual'">
                    <label class="tw-label">{{ __('messages.graphs.date_label') }}</label>
                    <div class="flex items-center gap-4">
                        <input
                            wire:model='selectedFromDate'
                            class="tw-input"
                            id="from-date-input"
                            name="from-date-input"
                            type="date"
                            @blur="blurFromDateGraphs"
                        />
                        <input
                            wire:model='selectedToDate'
                            class="tw-input"
                            id="to-date-input"
                            name="to-date-input"
                            type="date"
                            @blur="blurToDateGraphs"
                        />
                    </div>
                </div>
                <div class="mb-3 flex w-full gap-4 md:max-w-28" id="pnlYear" x-show="selectedFormat=='annual' && selectedFormat != null">
                    <div class="w-full">
                        <label class="tw-label">{{ __('messages.graphs.year_date_label') }}</label>
                        <select id="year-select" class="tw-input">
                            @for ($yearIndex=$minYear ; $yearIndex<=$maxYear ; $yearIndex++ )
                                <option value="{{ $yearIndex }}" {{$yearIndex == $selectedYear ? 'selected' : '' }}>{{ $yearIndex }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="flex justify-end md:mt-8">
                    <div class="flex items-center gap-2">
                        <button class="tw-btn tw-btn-blue mb-3" wire:loading.attr="disabled" onclick="showGraph()">
                            <div wire:loading.remove>{{ __('messages.graphs.show_button') }}</div>
                            <div wire:loading>{{ __('messages.graphs.processing_label') }}</div>
                        </button>
                        <div wire:loading><span class="tw-spinner ml-2"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4" id="pnlResults" style="display: none;">
            <div id="nodata" class="w-full text-center text-gray-500 dark:text-gray-400">No se encontraron datos</div>
            <div class="flex w-full flex-col gap-4" id="pnlGraph">
                <div id="graphButtons" class="flex items-center justify-between">
                    <button type="button" class="tw-btn-xs tw-btn-outline-blue flex items-center gap-1" onclick="resetZoom()">
                        <i class="fas fa-search-minus"></i>
                        {{ __('messages.graphs.reset_button') }}
                    </button>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" class="tw-btn-xs tw-btn-emerald flex items-center gap-1" @click="open = !open">
                            <i class="fas fa-download"></i>
                            Descargar gráfico
                            <i class="fas fa-caret-down ml-1"></i>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 z-50 mt-1 min-w-[200px] rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-600 dark:bg-gray-700">
                            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <i class="fas fa-file-image mr-1"></i> Formato de imagen
                            </div>
                            <button class="flex w-full items-center gap-2 px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" onclick="downloadImage('png'); $event.stopPropagation()">
                                <i class="fas fa-file-image text-sky-500"></i>
                                {{ __('messages.graphs.png_button') }}
                            </button>
                            <button class="flex w-full items-center gap-2 px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" onclick="downloadImage('jpeg'); $event.stopPropagation()">
                                <i class="fas fa-file-image text-amber-500"></i>
                                {{ __('messages.graphs.jpeg_button') }}
                            </button>
                            <hr class="my-1 border-gray-200 dark:border-gray-600">
                            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <i class="fas fa-file-pdf mr-1"></i> Formato de documento
                            </div>
                            <button class="flex w-full items-center gap-2 px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-600" onclick="downloadPDF(); $event.stopPropagation()">
                                <i class="fas fa-file-pdf text-red-500"></i>
                                {{ __('messages.graphs.pdf_button') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height:80vh; width:100%">
                    <canvas id="graph"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
    <style>
        .select2-search__field {
            box-shadow: 0 0 #0000;
        }
        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border: 1px solid #80bdff;
            box-shadow: 0 0 #0000;
        }
    </style>
@stop

@section('js')
    <script>
        let maxDate = new Date(@js($maxDate));
        let minDate = new Date(@js($minDate));
        let locale = localStorage.getItem('locale');
        let chart;

        const plugin = {
            id: 'custom_canvas_background_color',
            beforeDraw: (chart) => {
                const {ctx} = chart;
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        };

        document.addEventListener('livewire:initialized', () => {
            $('#format-select').on('change', function (e) {
                $('#pnlYear').hide();
                $('#pnlMonth').hide();
                $('#pnlDate').hide();
                $('#pnlGraph').hide();
                formatValue = $(this).val();
                if (formatValue == 'all') { $('#pnlDate').show(); }
                if (formatValue == 'daily') { $('#pnlDate').show(); }
                if (formatValue == 'annual') { $('#pnlYear').show(); $('#pnlMonth').hide(); }
            });

            chart = new Chart(document.getElementById('graph').getContext('2d'), {
                type: 'bar',
                plugins: [plugin],
                data: {},
                options: {
                    locale: locale,
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                title: function(tooltipItems) {
                                    const date = new Date(tooltipItems[0].raw.x);
                                    return new Intl.DateTimeFormat('es-ES', {
                                        day: '2-digit', month: '2-digit', year: 'numeric',
                                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                                    }).format(date);
                                },
                                label: function(tooltipItem) {
                                    let dataset = tooltipItem.dataset;
                                    let value = tooltipItem.parsed.y;
                                    let unit = '';
                                    if (dataset.yAxisID === 'dir') { unit = ' °'; }
                                    else if (dataset.yAxisID === 'length') { unit = ' ' + @js($lengthUnit); }
                                    else if (dataset.yAxisID === 'perc') { unit = ' ' + @js($percUnit); }
                                    else if (dataset.yAxisID === 'speed') { unit = ' ' + @js($speedUnit); }
                                    else if (dataset.yAxisID === 'temp') { unit = ' °' + @js($tempUnit); }
                                    else if (dataset.yAxisID === 'press') { unit = ' ' + @js($pressUnit); }
                                    else if (dataset.yAxisID === 'power') { unit = ' ' + @js($powerUnit); }
                                    else if (dataset.yAxisID === 'nro') { unit = ''; }
                                    else if (dataset.yAxisID === 'qair') { unit = ' µg/m³'; }
                                    return dataset.label + ': ' + value + unit;
                                },
                            },
                        },
                        zoom: {
                            zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        },
                    },
                    scales: {
                        x: { stacked: true, type: 'time', time: { parser: 'yyyy-MM-dd HH:mm:ss', unit: 'hour', displayFormats: { hour: 'dd/MM/yyyy HH:mm' } } },
                        xDaily: { stacked: true, type: 'time', time: { parser: 'yyyy-MM-dd HH:mm:ss', unit: 'day', displayFormats: { day: 'dd/MM/yyyy' } } },
                        xAnnual: { stacked: true, type: 'time', time: { parser: 'yyyy-MM-dd HH:mm:ss', unit: 'month', displayFormats: { month: 'MMM' } } },
                        dir: { type: 'linear', min: 0, max: 360, position: 'left', title: { display: true, text: '(°)' }, ticks: { callback: function (value) { const labels = { 0: @js(__('messages.wind_dir.n')), 90: @js(__('messages.wind_dir.e')), 180: @js(__('messages.wind_dir.s')), 270: @js(__('messages.wind_dir.w')), 360: @js(__('messages.wind_dir.n')) }; return labels[value] || ''; }, stepSize: 90 }, display: true },
                        length: { type: 'linear', position: 'left', title: { display: true, text: '(' + @js($lengthUnit) + ')' }, display: true },
                        perc: { type: 'linear', position: 'left', title: { display: true, text: '(%)' }, display: true },
                        speed: { type: 'linear', position: 'left', title: { display: true, text: '(' + @js($speedUnit) + ')' }, display: true },
                        temp: { type: 'linear', position: 'left', title: { display: true, text: '(°' + @js($tempUnit) + ')' }, display: true },
                        press: { type: 'linear', position: 'left', title: { display: true, text: '(' + @js($pressUnit) + ')' }, display: true },
                        power: { type: 'linear', position: 'left', title: { display: true, text: '(' + @js($powerUnit) + ')' }, display: true },
                        nro: { type: 'linear', position: 'left', title: { display: true }, display: true },
                        qair: { type: 'linear', position: 'left', title: { display: true, text: '(µg/m³)' }, display: true },
                    }
                }
            });
        });

        Livewire.on('updateChart', data => {
            $("#station-select").prop("disabled", false);
            $("#variable-select").prop("disabled", false);
            document.getElementById('format-select').disabled = false;
            document.getElementById('from-date-input').disabled = false;
            document.getElementById('to-date-input').disabled = false;
            document.getElementById('year-select').disabled = false;
            let formatValue = $('#format-select').val();

            chart.data = [];
            chart.resetZoom();
            $('#pnlGraph').show();
            if (data[0].datasets.length == 0) {
                chart.update('none');
                setTimeout(() => { $('#pnlResults').show(); $('#pnlGraph').hide(); $('#nodata').show(); }, 100);
            } else {
                const dirVisible = data[0].datasets.some(d => d.yAxisID === 'dir');
                const lengthVisible = data[0].datasets.some(d => d.yAxisID === 'length');
                const percVisible = data[0].datasets.some(d => d.yAxisID === 'perc');
                const speedVisible = data[0].datasets.some(d => d.yAxisID === 'speed');
                const tempVisible = data[0].datasets.some(d => d.yAxisID === 'temp');
                const pressVisible = data[0].datasets.some(d => d.yAxisID === 'press');
                const powerVisible = data[0].datasets.some(d => d.yAxisID === 'power');
                const nroVisible = data[0].datasets.some(d => d.yAxisID === 'nro');
                const qairVisible = data[0].datasets.some(d => d.yAxisID === 'qair');
                chart.data = data[0];
                chart.options.scales.dir.display = dirVisible;
                chart.options.scales.length.display = lengthVisible;
                chart.options.scales.perc.display = percVisible;
                chart.options.scales.speed.display = speedVisible;
                chart.options.scales.temp.display = tempVisible;
                chart.options.scales.press.display = pressVisible;
                chart.options.scales.power.display = powerVisible;
                chart.options.scales.nro.display = nroVisible;
                chart.options.scales.qair.display = qairVisible;
                chart.options.scales.x.display = (formatValue == 'all');
                chart.options.scales.xDaily.display = (formatValue == 'daily');
                chart.options.scales.xAnnual.display = (formatValue == 'annual');
                chart.update('none');
                setTimeout(() => { $('#pnlResults').show(); $('#pnlGraph').show(); $('#nodata').hide(); }, 100);
            }
        });

        function isValidDate(date) {
            return !isNaN(new Date(date).getTime());
        }

        function blurFromDateGraphs() {
            let val = document.getElementById('from-date-input').value;
            let d = new Date(val);
            if (!isValidDate(val)) { Swal.fire({ text: @js(__('messages.graphs.errors.select_from_date')) }); return; }
            if (d > maxDate) { Swal.fire({ text: @js(__('messages.graphs.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
            if (d < minDate) { Swal.fire({ text: @js(__('messages.graphs.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
        }

        function blurToDateGraphs() {
            let fromVal = document.getElementById('from-date-input').value;
            let toVal = document.getElementById('to-date-input').value;
            let fromDate = new Date(fromVal), toDate = new Date(toVal);
            if (!isValidDate(toVal)) { Swal.fire({ text: @js(__('messages.graphs.errors.select_to_date')) }); return; }
            if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.graphs.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
            if (toDate < minDate) { Swal.fire({ text: @js(__('messages.graphs.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
            if (isValidDate(fromVal)) {
                if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.graphs.errors.select_to_date_less_than_from_date')) }); return; }
                if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.graphs.errors.select_year_to_date_equal_than_year_from_date')) }); return; }
                if ((toDate - fromDate) / (1000 * 60 * 60 * 24 * 30.44) > 3) { Swal.fire({ text: @js(__('messages.graphs.errors.max_3_months')) }); return; }
            }
        }

        function checkParams() {
            let selectedStationVal = $('#station-select').val().filter(i => i !== "");
            let variableListValue = $('#variable-select').val().filter(i => i !== "");
            let selectedRangeVal = document.getElementById('format-select').value;
            let selectedFromDateVal = document.getElementById('from-date-input').value;
            let selectedToDateVal = document.getElementById('to-date-input').value;
            let fromDate = new Date(selectedFromDateVal), toDate = new Date(selectedToDateVal);

            if (!selectedStationVal || selectedStationVal.length < 1) { Swal.fire({ text: @js(__('messages.graphs.errors.select_station')) }); return false; }
            if (!variableListValue || variableListValue.length < 1) { Swal.fire({ text: @js(__('messages.graphs.errors.select_variable')) }); return false; }

            if (selectedRangeVal != 'annual') {
                if (!isValidDate(selectedFromDateVal)) { Swal.fire({ text: @js(__('messages.downloads.errors.select_from_date')) }); return false; }
                if (fromDate > maxDate) { Swal.fire({ text: @js(__('messages.downloads.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return false; }
                if (fromDate < minDate) { Swal.fire({ text: @js(__('messages.downloads.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return false; }
                if (!isValidDate(selectedToDateVal)) { Swal.fire({ text: @js(__('messages.downloads.errors.select_to_date')) }); return false; }
                if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.downloads.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return false; }
                if (toDate < minDate) { Swal.fire({ text: @js(__('messages.downloads.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return false; }
                if (isValidDate(selectedFromDateVal)) {
                    if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.downloads.errors.select_to_date_less_than_from_date')) }); return false; }
                    if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.downloads.errors.select_year_to_date_equal_than_year_from_date')) }); return false; }
                    if ((toDate - fromDate) / (1000 * 60 * 60 * 24 * 30.44) > 3) { Swal.fire({ text: @js(__('messages.graphs.errors.max_3_months')) }); return false; }
                }
            }
            return true;
        }

        function showGraph() {
            if (checkParams()) {
                let selectedStationVal = $('#station-select').val().filter(i => i !== "");
                let variableListValue = $('#variable-select').val().filter(i => i !== "");
                let selectedFormatVal = document.getElementById('format-select').value;
                let selectedFromDateVal = document.getElementById('from-date-input').value;
                let selectedToDateVal = document.getElementById('to-date-input').value;
                let selectedYearVal = document.getElementById('year-select').value;

                $("#station-select").prop("disabled", true);
                $("#variable-select").prop("disabled", true);
                document.getElementById('format-select').disabled = true;
                document.getElementById('from-date-input').disabled = true;
                document.getElementById('to-date-input').disabled = true;
                document.getElementById('year-select').disabled = true;
                $('#pnlResults').hide();
                Livewire.dispatch('showGraph', {stationList: selectedStationVal, variableList: variableListValue, format: selectedFormatVal, fromDate: selectedFromDateVal, toDate: selectedToDateVal, year: selectedYearVal});
            }
        }

        function resetZoom() { chart.resetZoom(); }

        function downloadImage(format) {
            const canvas = document.getElementById('graph');
            const link = document.createElement('a');
            link.href = canvas.toDataURL(`image/${format}`);
            link.download = `chart.${format}`;
            link.click();
        }

        function downloadPDF() {
            const canvas = document.getElementById('graph');
            const canvasImage = canvas.toDataURL('image/jpeg', 1.0);
            let pdf = new jspdf.jsPDF({ orientation: 'landscape' });
            pdf.setFontSize(20);
            pdf.addImage(canvasImage, 'jpeg', 15, 15, 260, 140);
            pdf.save('chart.pdf');
        }
    </script>
@stop
