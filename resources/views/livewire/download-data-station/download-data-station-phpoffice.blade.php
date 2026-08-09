@section('plugins.Select2', true)

<div x-data="{ format: @entangle('format') }">
    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex flex-wrap gap-3">
                {{-- Estación --}}
                <div class="w-full lg:min-w-52 lg:flex-1" wire:ignore>
                    <label class="tw-label">Estación</label>
                    <x-adminlte-select2
                        wire:loading.attr="disabled"
                        id="station-select" name="station-select"
                        data-placeholder="Selecciona una estación..."
                    >
                        <option></option>
                        @foreach ($stationList as $station)
                            <option value="{{ $station->id }}">{{$station->name}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                {{-- Formato --}}
                <div class="w-full sm:w-28">
                    <label class="tw-label">Formato</label>
                    <select
                        wire:loading.attr="disabled"
                        id="format-select"
                        class="tw-input"
                    >
                        <option value="daily">Diario</option>
                        <option value="monthly">Mensual</option>
                        <option value="annual">Anual</option>
                    </select>
                </div>
                {{-- Rango de fechas --}}
                <div class="w-full lg:flex-1" id="pnlDate" x-show="format=='daily'">
                    <label class="tw-label">Rango de fechas</label>
                    <div class="flex gap-2">
                        <input wire:loading.attr="disabled" value="{{ $fromDate }}"
                               type="date" id="from-date" class="tw-input min-w-0 flex-1">
                        <input wire:loading.attr="disabled" value="{{ $toDate }}"
                               type="date" id="to-date" class="tw-input min-w-0 flex-1">
                    </div>
                </div>
                {{-- Año / Mes --}}
                <div class="flex w-full gap-3 sm:w-auto" id="pnlYear" x-show="format!='daily'">
                    <div class="flex-1 sm:w-28">
                        <label class="tw-label">Elige un año</label>
                        <select wire:loading.attr="disabled" id="year-select" class="tw-input">
                            @for ($yearIndex=2012 ; $yearIndex<=$lastYear ; $yearIndex++ )
                                <option value="{{ $yearIndex }}" {{$yearIndex == $year ? 'selected' : '' }}>{{ $yearIndex }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 sm:w-28" id="pnlMonth" x-show="format=='monthly'">
                        <label class="tw-label">Elige un mes</label>
                        <select wire:loading.attr="disabled" id="month-select" class="tw-input">
                            @for ($monthIndex=0 ; $monthIndex<=11 ; $monthIndex++ )
                                <option value="{{ $monthIndex }}" {{$monthIndex == $month-1 ? 'selected' : '' }}>{{ $months[$monthIndex] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                {{-- Botones --}}
                <div class="flex w-full flex-wrap items-end gap-2 sm:w-auto">
                    <button
                        type="button"
                        class="tw-btn-xs tw-btn-blue"
                        wire:loading.attr="disabled"
                        onclick="showData()"
                    >
                        <div wire:loading.remove>Mostrar</div>
                        <div wire:loading>Procesando</div>
                    </button>
                    <button
                        type="button"
                        class="tw-btn-xs tw-btn-emerald"
                        wire:loading.attr="disabled"
                        onclick="downloadData()"
                    >
                        <div wire:loading.remove>Descargar en Excel</div>
                        <div wire:loading>Procesando</div>
                    </button>
                </div>
            </div>
        </div>
        @if (count($data))
            <div class="overflow-x-auto p-2" wire:loading.remove>
                @if($format == 'daily')
                    <div class="tw-table-wrap" style="max-height:500px;">
                        <table class="tw-table">
                            <thead class="themed-thead sticky top-0">
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Temperatura (ºC)</th>
                                    <th class="text-center">Temp. Max (ºC)</th>
                                    <th class="text-center">Temp. Min (ºC)</th>
                                    <th class="text-center">Punto de rocio (ºC)</th>
                                    <th class="text-center">Humedad (%)</th>
                                    <th class="text-center">Dir. viento (0º N)</th>
                                    <th class="text-center">Punto Cardinal</th>
                                    <th class="text-center">Vel. viento (km/h)</th>
                                    <th class="text-center">Rafaga (km/h)</th>
                                    <th class="text-center">Lluvia acumulada (mm)</th>
                                    <th class="text-center">Intensidad (mm/h)</th>
                                    <th class="text-center">Presion atmosferica (hpa)</th>
                                    <th class="text-center">Bulbo humedo (ºC)</th>
                                    <th class="text-center">Indice de calor (ºC)</th>
                                    <th class="text-center">Indice UV</th>
                                    <th class="text-center">Rad. solar (W/m^2)</th>
                                    <th class="text-center">Evapotranspiración (mm)</th>
                                    <th class="text-center">Temp. suelo (ºC)</th>
                                    <th class="text-center">Hum. suelo (cbar)</th>
                                    <th class="text-center">Temp. Hoja (ºC)</th>
                                    <th class="text-center">Hum. hoja (wet)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <th class="text-center"><p class="m-0 text-base text-wrap">{{ $item['receipt_date'] }}</p></th>
                                        <td class="text-center">{{ $item['tempout'] }}</td>
                                        <td class="text-center">{{ $item['tempoutmax'] }}</td>
                                        <td class="text-center">{{ $item['tempoutmin'] }}</td>
                                        <td class="text-center">{{ $item['dewptout'] }}</td>
                                        <td class="text-center">{{ $item['humout'] }}</td>
                                        <td class="text-center">{{ $item['winddir'] }}</td>
                                        <td class="text-center">{{ $item['winddirstr'] }}</td>
                                        <td class="text-center">{{ $item['windspeed'] }}</td>
                                        <td class="text-center">{{ $item['windspeedhi'] }}</td>
                                        <td class="text-center">{{ $item['raintotal'] }}</td>
                                        <td class="text-center">{{ $item['rainrate'] }}</td>
                                        <td class="text-center">{{ $item['press'] }}</td>
                                        <td class="text-center">{{ $item['wetbulb'] }}</td>
                                        <td class="text-center">{{ $item['heatindexout'] }}</td>
                                        <td class="text-center">{{ $item['uvindex'] }}</td>
                                        <td class="text-center">{{ $item['solrad'] }}</td>
                                        <td class="text-center">{{ $item['solevo'] }}</td>
                                        <td class="text-center">{{ $item['soiltemp1'] }}</td>
                                        <td class="text-center">{{ $item['soilhum1'] }}</td>
                                        <td class="text-center">{{ $item['leaftemp1'] }}</td>
                                        <td class="text-center">{{ $item['leafhum1'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if($format=='monthly')
                    <div class="tw-table-wrap" style="max-height:500px;">
                        <table class="tw-table">
                            <thead class="themed-thead sticky top-0">
                                <tr>
                                    <th class="text-center">Dia</th>
                                    <th class="text-center">Temperatura (ºC)</th>
                                    <th class="text-center">Temp. Max (ºC)</th>
                                    <th class="text-center">Temp. Min (ºC)</th>
                                    <th class="text-center">Punto de rocio (ºC)</th>
                                    <th class="text-center">Humedad (%)</th>
                                    <th class="text-center">Punto Cardinal</th>
                                    <th class="text-center">Vel. viento (km/h)</th>
                                    <th class="text-center">Rafaga (km/h)</th>
                                    <th class="text-center">Lluvia acumulada (mm)</th>
                                    <th class="text-center">Presion atmosferica (hpa)</th>
                                    <th class="text-center">Bulbo humedo (ºC)</th>
                                    <th class="text-center">Indice de calor (ºC)</th>
                                    <th class="text-center">Indice UV</th>
                                    <th class="text-center">Rad. solar (W/m^2)</th>
                                    <th class="text-center">Evapotranspiración (mm)</th>
                                    <th class="text-center">Temp. suelo (ºC)</th>
                                    <th class="text-center">Hum. suelo (cbar)</th>
                                    <th class="text-center">Temp. Hoja (ºC)</th>
                                    <th class="text-center">Hum. hoja (wet)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @for ($day=1 ; $day<=$lastDayMonth ; $day++ )
                                <tr>
                                    <th class="text-center">{{ $day }}</th>
                                    @if ($data->has($day))
                                        <td class="text-center">{{ $data->get($day)['tempout'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['tempoutmax'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['tempoutmin'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['dewptout'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['humout'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['winddirstr'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['windspeed'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['windspeedhi'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['raintotal'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['press'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['wetbulb'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['heatindexout'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['uvindex'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['solrad'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['solevo'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['soiltemp1'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['soilhum1'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['leaftemp1'] }}</td>
                                        <td class="text-center">{{ $data->get($day)['leafhum1'] }}</td>
                                    @else
                                        @for ($c=0; $c<19; $c++)<td class="text-center">-</td>@endfor
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                @endif
                @if($format=='annual')
                    <div class="tw-table-wrap" style="max-height:500px;">
                        <table class="tw-table">
                            <thead class="themed-thead sticky top-0">
                                <tr>
                                    <th class="text-center">Mes</th>
                                    <th class="text-center">Temperatura (ºC)</th>
                                    <th class="text-center">Temp. Max (ºC)</th>
                                    <th class="text-center">Temp. Min (ºC)</th>
                                    <th class="text-center">Punto de rocio (ºC)</th>
                                    <th class="text-center">Humedad (%)</th>
                                    <th class="text-center">Punto Cardinal</th>
                                    <th class="text-center">Vel. viento (km/h)</th>
                                    <th class="text-center">Rafaga (km/h)</th>
                                    <th class="text-center">Lluvia acumulada (mm)</th>
                                    <th class="text-center">Presion atmosferica (hpa)</th>
                                    <th class="text-center">Bulbo humedo (ºC)</th>
                                    <th class="text-center">Indice de calor (ºC)</th>
                                    <th class="text-center">Indice UV</th>
                                    <th class="text-center">Rad. solar (W/m^2)</th>
                                    <th class="text-center">Evapotranspiración (mm)</th>
                                    <th class="text-center">Temp. suelo (ºC)</th>
                                    <th class="text-center">Hum. suelo (cbar)</th>
                                    <th class="text-center">Temp. Hoja (ºC)</th>
                                    <th class="text-center">Hum. hoja (wet)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @for ($month=1 ; $month<=12 ; $month++ )
                                <tr>
                                    <th class="text-center">{{ $months[$month-1] }}</th>
                                    @if ($data->has($month))
                                        <td class="text-center">{{ $data->get($month)['tempout'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['tempoutmax'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['tempoutmin'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['dewptout'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['humout'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['winddirstr'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['windspeed'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['windspeedhi'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['raintotal'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['press'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['wetbulb'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['heatindexout'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['uvindex'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['solrad'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['solevo'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['soiltemp1'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['soilhum1'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['leaftemp1'] }}</td>
                                        <td class="text-center">{{ $data->get($month)['leafhum1'] }}</td>
                                    @else
                                        @for ($c=0; $c<19; $c++)<td class="text-center">-</td>@endfor
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @else
            @if ($noData)
                <div class="p-4 text-center text-gray-500 dark:text-gray-400" wire:loading.remove id="nodata">
                    No se encontraron registros
                </div>
            @endif
        @endif
    </div>
</div>

@section('js')
    <script>
        let fromDateValue;
        let toDateValue;
        let stationIdValue;
        let formatValue;
        let yearValue;
        let monthValue;

        $(document).ready(function() {
            $('#nodata').hide();
            $('#pnlYear').hide();
            $('#format-select').on('change', function (e) {
                $('#pnlYear').hide();
                $('#pnlMonth').hide();
                $('#pnlDate').hide();
                formatValue = $(this).val();
                if (formatValue == 'daily') { $('#pnlDate').show(); }
                if (formatValue == 'monthly') { $('#pnlYear').show(); $('#pnlMonth').show(); }
                if (formatValue == 'annual') { $('#pnlYear').show(); $('#pnlMonth').hide(); }
            });
        });

        function isValidDate(date) {
            const parsedDate = new Date(date);
            return !isNaN(parsedDate.getTime());
        }

        function checkParams() {
            stationIdValue = $('#station-select').val();
            formatValue = $('#format-select').val();
            fromDateValue = $('#from-date').val();
            toDateValue = $('#to-date').val();
            yearValue = $('#year-select').val();
            monthValue = $('#month-select').val();

            if (stationIdValue < 1) { alert('Seleccione una estación'); return false; }
            if (!['daily', 'monthly', 'annual'].includes(formatValue)) { alert('Seleccione un formato válido'); return false; }

            if (formatValue == 'daily') {
                yearValue = null;
                monthValue = null;
                if (!isValidDate(fromDateValue)) { alert('Seleccione una fecha inicial válida'); return false; }
                if (!isValidDate(toDateValue)) { alert('Seleccione una fecha final válida'); return false; }
                const startDate = new Date(fromDateValue);
                const endDate = new Date(toDateValue);
                if (startDate > endDate) { alert("La fecha inicial no puede ser mayor que la fecha final."); return false; }
                const diffInMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());
                if (diffInMonths > 12) { alert("El rango de fechas maximo es de 12 meses."); return false; }
            }
            return true;
        }

        function showData() {
            $('#nodata').hide();
            if (checkParams()) {
                $('#station-select').prop('disabled', true);
                Livewire.dispatch('showData', {stationId: stationIdValue, format: formatValue, fromDate: fromDateValue, toDate: toDateValue, year: yearValue, month: monthValue});
            }
        }

        function downloadData() {
            $('#nodata').hide();
            if (checkParams()) {
                Livewire.dispatch('downloadData', {stationId: stationIdValue, format: formatValue, fromDate: fromDateValue, toDate: toDateValue, year: yearValue, month: monthValue});
            }
        }
    </script>
@stop
