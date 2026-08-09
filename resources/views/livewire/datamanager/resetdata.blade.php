<div>
    {{-- Flash alerts --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-green">
            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1 font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800 dark:text-green-400"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif
    @if (session('failed'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-amber">
            <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1 font-medium">{{ session('failed') }}</span>
            <button @click="show = false" class="text-amber-600 hover:text-amber-800 dark:text-amber-400"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-red">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1 font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800 dark:text-red-400"><i class="fas fa-times text-xs"></i></button>
        </div>
    @endif

    {{-- Controles --}}
    <div class="tw-panel-header flex-col gap-4 sm:flex-row">
        {{-- Izquierda: paginación + carga Excel --}}
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="tw-label">Pag.</label>
                <select wire:model.live="perPage" class="tw-input-sm" style="width:auto">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="tw-btn tw-btn-emerald" wire:click='processData'>
                    <i class="fas fa-file-excel text-xs"></i> Cargar Excel
                </button>
                <input class="tw-input-sm" style="width:auto" wire:model="xlsxfile" id="formFileSm" type="file" accept=".xlsx, .xls">
            </div>
        </div>

        {{-- Derecha: filtro por rango de fechas --}}
        <div class="flex flex-wrap items-end gap-2">
            <label class="tw-label w-full">Buscar por rango:</label>
            <div wire:loading class="flex items-center gap-1 text-sm text-gray-500">
                <span class="tw-spinner-sm"></span> Procesando...
            </div>
            <input class="tw-input-sm" style="width:auto" wire:model="dateStart" id="dateStart" type="date">
            <input class="tw-input-sm" style="width:auto" wire:model="dateEnd" id="dateEnd" type="date">
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="flex gap-4">
        {{-- Panel de búsqueda de estación --}}
        @if($stationId == null)
            <div class="w-full md:w-72 flex-shrink-0">
                <div class="mb-2">
                    <label class="tw-label">Buscar estación:</label>
                    <input type="text" placeholder="Nombre estación" wire:model.live="search"
                           class="tw-input-sm"
                           @if($stationId != null) style="background-color:#670d2e" @endif>
                </div>
                <div class="tw-table-wrap">
                    <table class="tw-table">
                        <thead></thead>
                        <tbody>
                            @if (count($data) > 0)
                                @foreach ($data as $item)
                                    <tr>
                                        <td>
                                            <a class="text-blue-600 hover:underline dark:text-blue-400"
                                               href="#" wire:click="setStationId({{ $item->ID }}, '{{ $item->NAME }}')">
                                                <span class="font-semibold">{{ $item->ID }}</span> {{ $item->NAME }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $data->links() }}
                </div>
            </div>
        @endif

        {{-- Panel de resultados --}}
        @if($stationId != null)
            <div class="flex-1">
                <div x-data="{ show: true }" x-show="show" class="tw-alert tw-alert-amber mb-3 text-center">
                    <span class="flex-1 font-semibold">{{ $stationName }}</span>
                    <button wire:click="resetStation" @click="show = false"
                            class="text-amber-600 hover:text-amber-800 dark:text-amber-400">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="tw-table-wrap">
                    <table class="tw-table">
                        <thead></thead>
                        <tbody>
                            @if (count($dataResponse) > 0)
                                @foreach ($dataResponse as $item)
                                    <tr><td>{{ $item }}</td></tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="py-4 text-center text-gray-500 dark:text-gray-400">No hay datos</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
