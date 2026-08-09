<div>
    <h5 class="mb-4 w-full text-center text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.early_warning.page') }}</h5>

    <div x-cloak>
        {{-- Filtros --}}
        <div class="mb-4 border-b border-gray-200 pb-4 dark:border-gray-700">
            <div class="flex w-full flex-col justify-between gap-4 md:flex-row md:gap-10">
                <div class="flex w-full flex-col gap-4">
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="w-full" wire:ignore>
                            <label class="tw-label">{{ __('messages.early_warning.station_label') }}</label>
                            <x-adminlte-select-bs
                                id="station-select"
                                name="station-select"
                                title="{{ __('messages.early_warning.station_placeholder') }}"
                                data-live-search="true"
                                data-style="selectControl">
                                @foreach ($stationList as $station)
                                    <option value="{{ $station['id'] }}" {{ $selectedStation == $station['id'] ? 'selected' : '' }}>{{ $station['name'] }}</option>
                                @endforeach
                            </x-adminlte-select-bs>
                        </div>
                        <div class="w-full">
                            <label class="tw-label">{{ __('messages.early_warning.date_label') }}</label>
                            <div class="flex items-center gap-3">
                                <input wire:model='selectedFromDate' class="tw-input" id="from-date-input" name="from-date-input" type="date" @blur="handleBlurFromDate">
                                <input wire:model='selectedToDate' class="tw-input" id="to-date-input" name="to-date-input" type="date" @blur="handleBlurToDate">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="w-full min-w-44 md:w-fit" wire:ignore>
                            <label class="tw-label">{{ __('messages.early_warning.crop_label') }}</label>
                            <x-adminlte-select-bs
                                id="crop-select"
                                name="crop-select"
                                title="{{ __('messages.early_warning.crop_placeholder') }}"
                                data-style="selectControl">
                                @foreach ($cropList as $key => $item)
                                    <option value="{{ $key }}" {{ $selectedCrop == $key ? 'selected' : '' }}>{{ $item['label'] }}</option>
                                @endforeach
                            </x-adminlte-select-bs>
                        </div>
                        <div class="min-w-[30%] grow" wire:ignore>
                            <label class="tw-label">{{ __('messages.early_warning.crop_condition_label') }}</label>
                            <select class="tw-input" id="crop-condition-select" name="crop-condition-select"></select>
                        </div>
                        <div class="w-full min-w-44 md:w-fit" wire:ignore>
                            <label class="tw-label">{{ __('messages.early_warning.leaf_sensor_label') }}</label>
                            <x-adminlte-select-bs
                                id="leaf-station-select"
                                name="leaf-station-select"
                                title="{{ __('messages.early_warning.leaf_sensor_placeholder') }}"
                                data-style="selectControl">
                                @foreach ($leafStationList as $key => $item)
                                    <option value="{{ $key }}" {{ $selectedLeafStation == $key ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </x-adminlte-select-bs>
                        </div>
                    </div>
                </div>

                <div class="flex items-end justify-end">
                    <div class="flex w-36 items-center gap-2 mb-3">
                        <button class="tw-btn tw-btn-emerald w-full" wire:loading.attr="disabled" onclick="checkParams()">
                            <span wire:loading.remove>{{ __('messages.early_warning.show_button_label') }}</span>
                            <span wire:loading>{{ __('messages.early_warning.processing_button_label') }}</span>
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
                                    <tr class="{{ $item->conditions_sw == 0 ? 'bg-green-100' : '' }}
                                               {{ $item->conditions_sw == 1 ? 'bg-red-100' : '' }}
                                               {{ $item->conditions_sw == 2 ? 'bg-orange-100' : '' }}
                                               {{ $item->conditions_sw == 3 ? 'bg-blue-100' : '' }}">
                                        @foreach ($this->columnsName as $key => $column)
                                            <td class="text-center font-thin">{{ $item->$key }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-6 text-center text-gray-500 dark:text-gray-400">{{ __('messages.early_warning.result_no_data') }}</div>
                @endif
            </div>

            @if ($hasData)
                <div id="pnlFooter" class="mt-4">
                    {!! $data->links() !!}
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
        let cropList = @js($cropList);
        document.getElementById('crop-select').addEventListener('change', function () {
            if (this.value === '' || cropList[this.value] === undefined) return;
            let conditions = cropList[this.value].conditions;
            const select = document.getElementById("crop-condition-select");
            select.innerHTML = "";
            Object.entries(conditions).forEach(([key, value]) => {
                const option = document.createElement("option");
                option.value = key;
                option.textContent = value.label;
                select.appendChild(option);
            });
        });
    });

    Livewire.on('showData', ([data]) => {
        $("#station-select").prop("disabled", false);
        document.getElementById('from-date-input').disabled = false;
        document.getElementById('to-date-input').disabled = false;
        $("#crop-select").prop("disabled", false);
        $("#crop-condition-select").prop("disabled", false);
        $("#leaf-station-select").prop("disabled", false);
    });

    function isValidDate(date) { return !isNaN(new Date(date).getTime()); }

    function handleBlurFromDate() {
        let val = document.getElementById('from-date-input').value;
        let d = new Date(val);
        if (!isValidDate(val)) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date')) }); return; }
        if (d > maxDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (d < minDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
    }

    function handleBlurToDate() {
        let fromVal = document.getElementById('from-date-input').value;
        let toVal = document.getElementById('to-date-input').value;
        let fromDate = new Date(fromVal), toDate = new Date(toVal);
        if (!isValidDate(toVal)) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date')) }); return; }
        if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (toDate < minDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
        if (isValidDate(fromVal)) {
            if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_less_than_from_date')) }); return; }
            if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_year_to_date_equal_than_year_from_date')) }); return; }
        }
    }

    function checkParams() {
        let station      = document.getElementById('station-select').value;
        let fromVal      = document.getElementById('from-date-input').value;
        let toVal        = document.getElementById('to-date-input').value;
        let crop         = document.getElementById('crop-select').value;
        let cropCond     = document.getElementById('crop-condition-select').value;
        let leafStation  = document.getElementById('leaf-station-select').value;
        let fromDate = new Date(fromVal), toDate = new Date(toVal);

        if (station < 1) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_station')) }); return; }
        if (!isValidDate(fromVal)) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date')) }); return; }
        if (fromDate > maxDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (fromDate < minDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
        if (!isValidDate(toVal)) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date')) }); return; }
        if (toDate > maxDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')])) }); return; }
        if (toDate < minDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')])) }); return; }
        if (isValidDate(fromVal)) {
            if (toDate < fromDate) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_to_date_less_than_from_date')) }); return; }
            if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_year_to_date_equal_than_year_from_date')) }); return; }
        }
        if (crop < 1) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_crop')) }); return; }
        if (cropCond < 1) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_crop_condition')) }); return; }
        if (leafStation < 1) { Swal.fire({ text: @js(__('messages.early_warning.errors.select_leaf_station')) }); return; }

        $("#station-select").prop("disabled", true);
        document.getElementById('from-date-input').disabled = true;
        document.getElementById('to-date-input').disabled = true;
        $("#crop-select").prop("disabled", true);
        $("#crop-condition-select").prop("disabled", true);
        $("#leaf-station-select").prop("disabled", true);
        $('#pnlResults').hide();

        Livewire.dispatch('processParams', {
            selectedStation: station,
            selectedFromDate: fromVal,
            selectedToDate: toVal,
            selectedCrop: crop,
            selectedCropCondition: cropCond,
            selectedLeafStation: leafStation,
        });
    }
</script>
@stop
