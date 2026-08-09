<div x-data="{ selectedRange: @entangle('selectedRange') }">
    <h5 class="mb-4 w-full text-center text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.chill_hours.page') }}</h5>

    <div x-cloak>
        {{-- Filtros --}}
        <div class="mb-4 border-b border-gray-200 pb-4 dark:border-gray-700">
            <div class="flex w-full flex-col gap-4 md:flex-row">
                <div class="w-full" wire:ignore>
                    <label class="tw-label">{{ __('messages.chill_hours.station_label') }}</label>
                    <x-adminlte-select-bs
                        id="station-select"
                        name="station-select"
                        title="{{ __('messages.chill_hours.station_placeholder') }}"
                        data-live-search="true"
                        data-style="selectControl">
                        @foreach ($stationList as $station)
                            <option value="{{ $station['id'] }}">{{ $station['name'] }}</option>
                        @endforeach
                    </x-adminlte-select-bs>
                </div>

                <div class="w-full md:max-w-36">
                    <label class="tw-label">{{ __('messages.chill_hours.range_label') }}</label>
                    <select id="range-select" class="tw-input">
                        @foreach ($rangeList as $key => $item)
                            <option value="{{ $key }}" {{ $selectedRange == $key ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full" id="pnlDate" x-show="selectedRange == null || selectedRange == 'daily'">
                    <label class="tw-label">{{ __('messages.chill_hours.date_label') }}</label>
                    <div class="flex items-center gap-3">
                        <input wire:model='selectedFromDate' class="tw-input" id="from-date-input" name="from-date-input" type="date" @blur="handleBlurFromDate">
                        <input wire:model='selectedToDate' class="tw-input" id="to-date-input" name="to-date-input" type="date" @blur="handleBlurToDate">
                    </div>
                </div>

                <div class="flex w-full gap-3 md:max-w-64" id="pnlYear" x-show="selectedRange != 'daily' && selectedRange != null">
                    <div class="w-full">
                        <label class="tw-label">{{ __('messages.chill_hours.year_date_label') }}</label>
                        <select id="year-select" class="tw-input">
                            @for ($yearIndex = $minYear; $yearIndex <= $maxYear; $yearIndex++)
                                <option value="{{ $yearIndex }}" {{ $yearIndex == $selectedYear ? 'selected' : '' }}>{{ $yearIndex }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full" id="pnlMonth" x-show="selectedRange == 'monthly'">
                        <label class="tw-label">{{ __('messages.chill_hours.month_date_label') }}</label>
                        <select id="month-select" class="tw-input">
                            @foreach ($monthList as $key => $item)
                                <option value="{{ $key }}" {{ $key == $selectedMonth ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-end justify-end">
                    <div class="flex w-36 items-center gap-2">
                        <button class="tw-btn tw-btn-emerald w-full" wire:loading.attr="disabled" onclick="checkParams()">
                            <span wire:loading.remove>{{ __('messages.chill_hours.show_button_label') }}</span>
                            <span wire:loading>{{ __('messages.chill_hours.processing_button_label') }}</span>
                        </button>
                        <div wire:loading><span class="tw-spinner"></span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resultados --}}
        @if ($showResults)
            <div class="overflow-x-auto" id="pnlResults" name="pnlResults">
                @if ($hasData)
                    <div class="h-full w-full max-h-[60vh]">
                        <table class="tw-table overflow-auto">
                            <thead class="themed-thead sticky -top-5">
                                <tr>
                                    @foreach ($this->columnsName as $column)
                                        <th class="text-center align-middle">{{ $column['label'] }} {{ $column['unit'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->items() as $item)
                                    <tr>
                                        @foreach ($this->columnsName as $key => $column)
                                            <td class="text-center font-thin">{{ $item->$key }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('messages.chill_hours.result_no_data') }}</div>
                @endif
            </div>

            @if ($hasData)
                <div id="pnlFooter" class="mt-4 flex flex-col gap-3">
                    <div class="flex items-center">
                        <span class="border-2 border-gray-800 bg-white px-3 py-1 text-sm font-medium dark:border-gray-400 dark:bg-gray-700 dark:text-gray-200">
                            {{ __('messages.chill_hours.total_label') }} = {{ $totalCH }}
                        </span>
                    </div>
                    <div>{!! $data->links() !!}</div>
                </div>
            @endif
        @endif
    </div>
</div>

@section('js')
<script>
    let maxDate = new Date(@js($maxDate));
    let minDate = new Date(@js($minDate));

    document.addEventListener('livewire:initialized', () => {
        $('#range-select').on('change', function (e) {
            $('#pnlYear').hide(); $('#pnlMonth').hide(); $('#pnlDate').hide();
            let rangeValue = $(this).val();
            if (rangeValue == 'daily') { $('#pnlDate').show(); }
            if (rangeValue == 'monthly') { $('#pnlYear').show(); $('#pnlMonth').show(); }
            if (rangeValue == 'annual') { $('#pnlYear').show(); }
        });
    });

    Livewire.on('showData', ([data]) => {
        $("#station-select").prop("disabled", false);
        ['range-select','from-date-input','to-date-input','year-select','month-select']
            .forEach(id => { document.getElementById(id).disabled = false; });
    });

    function isValidDate(date) { return !isNaN(new Date(date).getTime()); }

    function handleBlurFromDate() {
        let val = document.getElementById('from-date-input').value;
        let d = new Date(val);
        if (!isValidDate(val)) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date')) }); return; }
        if (d > maxDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (d < minDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
    }

    function handleBlurToDate() {
        let fromVal = document.getElementById('from-date-input').value;
        let toVal = document.getElementById('to-date-input').value;
        let fromDate = new Date(fromVal), toDate = new Date(toVal);
        if (!isValidDate(toVal)) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date')) }); return; }
        if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (toDate < minDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
        if (isValidDate(fromVal)) {
            if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_less_than_from_date')) }); return; }
            if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_year_to_date_equal_than_year_from_date')) }); return; }
        }
    }

    function checkParams() {
        let station = document.getElementById('station-select').value;
        let range   = document.getElementById('range-select').value;
        let fromVal = document.getElementById('from-date-input').value;
        let toVal   = document.getElementById('to-date-input').value;
        let year    = document.getElementById('year-select').value;
        let month   = document.getElementById('month-select').value;
        let fromDate = new Date(fromVal), toDate = new Date(toVal);

        if (station < 1) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_station')) }); return; }
        if (range == 'daily') {
            if (!isValidDate(fromVal)) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date')) }); return; }
            if (fromDate > maxDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
            if (fromDate < minDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
            if (!isValidDate(toVal))  { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date')) }); return; }
            if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
            if (toDate < minDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
            if (isValidDate(fromVal)) {
                if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_to_date_less_than_from_date')) }); return; }
                if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.chill_hours.errors.select_year_to_date_equal_than_year_from_date')) }); return; }
            }
        }

        $("#station-select").prop("disabled", true);
        ['range-select','from-date-input','to-date-input','year-select','month-select']
            .forEach(id => { document.getElementById(id).disabled = true; });
        $('#pnlResults').hide(); $('#pnlFooter').hide();

        Livewire.dispatch('processParams', {
            selectedStation: station, selectedRange: range,
            selectedFromDate: fromVal, selectedToDate: toVal,
            selectedYear: year, selectedMonth: month,
        });
    }
</script>
@stop
