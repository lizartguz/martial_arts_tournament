<div
    x-data="{
        showResults: @entangle('showResults'),
        hasData: @entangle('hasData'),
    }"
>
    <h5 class="mb-4 w-full text-center text-base font-semibold text-gray-800 dark:text-gray-200">{{ __('messages.forecasts_edit.page') }}</h5>
    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" x-cloak>
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex w-full flex-col justify-between gap-4 md:flex-row md:gap-10">
                <div class="flex w-full flex-col gap-4">
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="w-full" wire:ignore>
                            <label class="tw-label">{{ __('messages.forecasts_edit.station_label') }}</label>
                            <x-adminlte-select-bs
                                id="station-select"
                                name="station-select"
                                title="{{ __('messages.forecasts_edit.station_placeholder') }}"
                                data-live-search="true"
                                data-style="selectControl"
                            >
                                @foreach ($stationList as $station)
                                    <option value="{{ $station['id'] }}" {{ $selectedStation==$station['id'] ? 'selected' : '' }}>{{$station['name']}}</option>
                                @endforeach
                            </x-adminlte-select-bs>
                        </div>
                    </div>
                </div>
                <div class="flex items-end justify-end">
                    <div class="mb-3 flex items-center gap-4">
                        <button class="tw-btn tw-btn-emerald" wire:loading.attr="disabled" wire:loading.class="disabled" onclick="checkParams()">
                            <div wire:loading.remove>{{ __('messages.forecasts_edit.show_button_label') }}</div>
                            <div wire:loading>{{ __('messages.forecasts_edit.processing_button_label') }}</div>
                        </button>
                        <button class="tw-btn tw-btn-amber" wire:loading.attr="disabled" wire:loading.class="disabled" wire:target="collectForecasts" onclick="collectForecastsData()" style="min-width: 150px;">
                            <div wire:loading.remove wire:target="collectForecasts">
                                <i class="fas fa-download"></i> Recolectar
                            </div>
                            <div wire:loading wire:target="collectForecasts">
                                <i class="fas fa-spinner fa-spin"></i> Recolectando...
                            </div>
                        </button>
                        <div wire:loading><span class="tw-spinner"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-4 p-4" id="pnlResults" name="pnlResults" x-show="showResults">
            <div class="w-full" x-show="hasData">
                <div id="tblData" class="disable-auto-theme" wire:ignore></div>
            </div>
            <div class="flex items-center justify-end" x-show="hasData">
                <button class="tw-btn tw-btn-blue" wire:loading.attr="disabled" onclick="saveData()">
                    <div wire:loading.remove>{{ __('messages.forecasts_edit.save_button_label') }}</div>
                    <div wire:loading>{{ __('messages.forecasts_edit.processing_button_label') }}</div>
                </button>
            </div>
            <div class="w-full text-center text-gray-500 dark:text-gray-400" x-show="!hasData">
                {{ __('messages.early_warning.result_no_data') }}
            </div>
        </div>
    </div>
</div>

@section('content_header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="{{ asset('vendor/handsontable/15.2.0/handsontable.full.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/handsontable/15.2.0/handsontable.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/handsontable/15.2.0/ht-theme-main.min.css') }}" />
@stop

@section('css')
<style>
    .selectControl {
        background: white;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        color: #495057;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    .selectControl:focus {
        border: 1px solid #80bdff;
    }

    .selectControl.btn.disabled {
        background: #e9ecef;
        opacity: 1;
    }

    .bootstrap-select .dropdown-toggle:focus {
        outline: none !important;
    }

    /* Estilos para Handsontable con tema verde */
    .ht-theme-main .ht_clone_top thead th,
    .ht-theme-main .handsontable thead th {
        background: linear-gradient(135deg, var(--theme-gradient-start, #047857) 0%, var(--theme-gradient-end, #10b981) 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
    }

    .ht-theme-main tbody tr:hover {
        background-color: var(--theme-bg-light, #f0fdf4) !important;
    }

    /* Scrollbar para Handsontable */
    .ht_master .wtHolder::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .ht_master .wtHolder::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .ht_master .wtHolder::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--theme-gradient-start, #047857) 0%, var(--theme-gradient-end, #10b981) 100%);
        border-radius: 10px;
    }

    .ht_master .wtHolder::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--theme-gradient-hover-start, #065f46) 0%, var(--theme-gradient-hover-end, #059669) 100%);
    }
</style>
@stop

@section('js')
    <script>
        let columnHeaders = @json($columnsName);
        let hot;
        let deletedIds = new Set();
        let updatedIds = new Set();
        document.addEventListener('livewire:initialized', () => {
            const container = document.querySelector('#tblData');

            hot = new Handsontable(container, {
                rowHeaders: true,
                undo: false,
                redo: false,
                width: '100%',
                height: '450',
                contextMenu: true,
                autoColumnSize: true,
                autoColumnSize: {
                    syncLimit: '40%',
                    useHeaders: true,
                    samplingRatio: 10,
                    allowSampleDuplicates: true
                },
                hiddenColumns: {
                    columns: [0],
                    indicators: false,
                },
                autoWrapRow: true,
                autoWrapCol: true,
                stretchH: 'all',
                manualColumnResize: true,
                colHeaders: columnHeaders,
                minCols: columnHeaders.length,
                maxCols: columnHeaders.length,
                themeName: 'ht-theme-main',
                columns: [
                    {},
                    {
                        type: 'text',
                        validator: function (value, callback) {
                            if (isValidDate(value)) {
                                callback(true);
                            } else {
                                callback(false);
                            }
                        },

                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'text',
                        validator: function (value, callback) {
                            const allowedValues = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO']; // Valores permitidos
                            if (allowedValues.includes(value)) {
                                callback(true);
                            } else {
                                callback(false);
                            }
                        },
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                    {
                        type: 'numeric',
                        allowInvalid: true,
                    },
                ],
                beforeRemoveRow: function(index, amount, removedRows) {
                    removedRows.forEach((row) => {
                        const removedRowData = hot.getDataAtRow(row);
                        const id = removedRowData[0];
                        if (id > 0) {
                            deletedIds.add(id);
                            updatedIds.delete(id);
                        }
                    });
                },
                afterChange: function(changes, source) {
                    if (source === 'edit' || source === 'CopyPaste.paste') {
                        changes.forEach(([row, prop, oldValue, newValue]) => {
                            if (oldValue != newValue) {
                                const modifiedRowData = hot.getDataAtRow(row);
                                if (newValue !== null && prop === 3) {
                                    this.setDataAtCell(row, prop, newValue.toUpperCase());
                                }
                                const id = modifiedRowData[0];
                                if (id > 0) {
                                    updatedIds.add(id);
                                }
                            }
                        });
                    }
                },
                licenseKey: 'non-commercial-and-evaluation'
            });
        });

        Livewire.on('showData', ([data]) => {
            $("#station-select").prop("disabled", false);
            hot.loadData(data);
            setTimeout(() => {
                hot.render();
                hot.render();
            }, 100);
        });

        Livewire.on('error', data => {
            $("#station-select").prop("disabled", false);

            Swal.fire({
                type: 'error',
                text: data.message,
            });
        });

        Livewire.on('collectSuccess', data => {
            $("#station-select").prop("disabled", false);

            Swal.fire({
                type: 'success',
                title: '¡Pronósticos recolectados!',
                text: data.message,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
            });
        });

        function checkParams() {
            let selectedStationVal = document.getElementById('station-select').value;
            if (selectedStationVal == '') {
                Swal.fire({
                    text: @js(__('messages.forecasts_edit.errors.station_required'))
                });
                return;
            }

            Livewire.dispatch('processParams',{
                selectedStation: selectedStationVal,
            });
        }

        function collectForecastsData() {
            let selectedStationVal = document.getElementById('station-select').value;

            Swal.fire({
                title: '¿Recolectar pronósticos?',
                text: selectedStationVal && selectedStationVal !== ''
                    ? 'Se recolectarán los pronósticos para la estación seleccionada.'
                    : 'Se recolectarán los pronósticos para todas las estaciones. Esto puede tomar varios minutos.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, recolectar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#station-select").prop("disabled", true);

                    @this.call('collectForecasts', selectedStationVal || null)
                        .catch((error) => {
                            console.error('Error al ejecutar método:', error);
                            $("#station-select").prop("disabled", false);
                            Swal.fire({
                                type: 'error',
                                text: 'Ocurrió un error al recolectar los pronósticos.',
                            });
                        });
                }
            });
        }

        function isValidDate(dateString) {
            const date = Date.parse(dateString);
            return !isNaN(date);
        }

        function isNumber(value) {
            return !isNaN(value) && !isNaN(parseFloat(value));
        }

        function saveData() {
            let gridData = hot.getData();
            let isOk = true;
            for (const [rowIndex, row] of gridData.entries()) {
                const isEmptyRow = row.every(cell => cell === '' || cell === null);
                if (isEmptyRow) {
                    continue;
                }

                for (const [colIndex, cell] of row.entries()) {
                    if (colIndex === 1 && !isValidDate(cell)) {
                        let translatedText = @js(__('messages.forecasts_edit.errors.date'));
                        translatedText = translatedText.replace(':row', rowIndex + 1);
                        isOk = false;
                        Swal.fire({
                            text: translatedText
                        });
                        return;
                    }
                    if ([2, 4, 5, 6, 7, 8, 9, 10, 11, 12].includes(colIndex) && !isNumber(cell)) {
                        let translatedText = @js(__('messages.forecasts_edit.errors.data_row_required'));
                        translatedText = translatedText.replace(':row', rowIndex + 1);
                        isOk = false;
                        Swal.fire({
                            text: translatedText
                        });
                        return;
                    }
                }
            }
            if (isOk) {
                Livewire.dispatch('saveData',{
                    gridData: gridData,
                    updatedIds: [...updatedIds],
                    deletedIds: [...deletedIds]
                });
            }
        }
    </script>
@stop
