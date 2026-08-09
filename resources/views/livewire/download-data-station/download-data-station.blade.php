<div
    x-data="{
        selectedFormat: @entangle('selectedFormat'),
    }"
>
    <h5 class="w-full text-center mb-4">{{ __('messages.downloads.page') }}</h5>
    @if (session()->has('columns_success'))
        <div class="tw-alert tw-alert-green">
            {{ session('columns_success') }}
        </div>
    @endif
    <div class="w-full rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" x-cloak>
        <div class="w-full border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div class="flex flex-wrap gap-3">
                {{-- Estación: autocomplete server-side (componente aislado y reutilizable).
                     Al escribir solo se re-renderiza este hijo; los demás filtros no se tocan. --}}
                <div class="w-full lg:min-w-52 lg:flex-1">
                    <label class="tw-label">{{ __('messages.downloads.station_label') }}</label>
                    <livewire:components.station-autocomplete
                        :selected-id="$selectedStationId"
                        input-id="station-select"
                        :placeholder="__('messages.downloads.station_placeholder')"
                        wire:key="downloads-station-autocomplete" />
                </div>
                {{-- Formato --}}
                <div class="w-full sm:w-36">
                    <label class="tw-label">{{ __('messages.downloads.format_label') }}</label>
                    <select id="format-select" class="tw-input">
                        @foreach ($formatList as $key => $item)
                            <option value="{{ $key }}" {{ $selectedFormat==$key ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Rango de fechas --}}
                <div class="w-full lg:flex-1" id="pnlDate" x-show="selectedFormat==null || selectedFormat=='daily'">
                    <label class="tw-label">{{ __('messages.downloads.date_label') }}</label>
                    <div class="flex items-center gap-2">
                        <input wire:model='selectedFromDate' class="tw-input min-w-0 flex-1"
                               id="from-date-input" name="from-date-input" type="date" @blur="blurFromDate" />
                        <input wire:model='selectedToDate' class="tw-input min-w-0 flex-1"
                               id="to-date-input" name="to-date-input" type="date" @blur="blurToDate" />
                    </div>
                </div>
                {{-- Año / Mes --}}
                <div class="flex w-full gap-3 sm:w-auto" id="pnlYear" x-show="selectedFormat!='daily' && selectedFormat != null">
                    <div class="flex-1 sm:w-28">
                        <label class="tw-label">{{ __('messages.downloads.year_date_label') }}</label>
                        <select id="year-select" class="tw-input">
                            @for ($yearIndex=$minYear ; $yearIndex<=$maxYear ; $yearIndex++ )
                                <option value="{{ $yearIndex }}" {{$yearIndex == $selectedYear ? 'selected' : '' }}>{{ $yearIndex }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 sm:w-28" id="pnlMonth" x-show="selectedFormat=='monthly'">
                        <label class="tw-label">{{ __('messages.downloads.month_date_label') }}</label>
                        <select id="month-select" class="tw-input">
                            @foreach ($monthList as $key => $item)
                                <option value="{{ $key }}" {{$key == $selectedMonth ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Botones de acción --}}
                <div class="flex w-full flex-wrap items-end gap-2 sm:w-auto">
                    <button class="tw-btn tw-btn-blue flex-1 sm:flex-none sm:w-28" wire:loading.attr="disabled" onclick="showData()">
                        <span wire:loading.remove>{{ __('messages.downloads.show_button') }}</span>
                        <span wire:loading>{{ __('messages.downloads.processing_label') }}</span>
                    </button>
                    @if ($typeS !== 'qair')
                        <button type="button" class="tw-btn tw-btn-outline-blue flex-1 sm:flex-none sm:w-32" wire:loading.attr="disabled" wire:click="openColumnsModal">
                            <i class="fas fa-columns mr-1"></i>{{ __('messages.downloads.columns_button') }}
                        </button>
                    @endif
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button class="tw-btn tw-btn-emerald flex items-center gap-1" type="button" wire:loading.attr="disabled" @click="open = !open">
                            <span wire:loading.remove>{{ __('messages.downloads.download_button') }}</span>
                            <span wire:loading>{{ __('messages.downloads.processing_label') }}</span>
                            <i class="fas fa-chevron-down text-xs" wire:loading.remove></i>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 z-50 mt-1 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                            <button class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" type="button" @click="open=false; downloadData('excel')">
                                <i class="fas fa-file-excel text-green-600"></i>{{ __('messages.downloads.download_excel_option') }}
                            </button>
                            <button class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" type="button" @click="open=false; downloadData('pdf')">
                                <i class="fas fa-file-pdf text-red-600"></i>{{ __('messages.downloads.download_pdf_option') }}
                            </button>
                        </div>
                    </div>
                    <div wire:loading><span class="tw-spinner"></span></div>
                </div>
            </div>
        </div>
        @if ($showResultsView || $showResultsDownload)
            <div class="p-4" id="pnlResults" name="pnlResults">
                @if ($showResultsDownload && !$hasDataDownload)
                    <div class="w-full text-center mb-4">
                        <div class="tw-alert tw-alert-blue flex items-center" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>{{ __('messages.downloads.no_data_download') }}</div>
                        </div>
                    </div>
                @endif
                @if ($hasDataView)
                    @php
                        $fixedHead = $freezeDateColumn ? ($heads['receipt_date'] ?? null) : null;
                        $scrollHeads = $freezeDateColumn
                            ? array_filter($heads, fn ($key) => $key !== 'receipt_date', ARRAY_FILTER_USE_KEY)
                            : $heads;
                    @endphp
                    <div class="table-container">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="tw-badge tw-badge-blue px-3 py-1 text-sm">
                                <i class="fas fa-table mr-1"></i>
                                Total de registros: <strong>{{ count($data) }}</strong>
                            </span>
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-arrows-alt-v mr-1"></i>
                                Desplázate para ver más datos
                            </span>
                        </div>
                        @if ($freezeDateColumn && $fixedHead)
                            <div class="split-table-layout" data-scroll-sync-group="downloads-results">
                                <div class="split-table-fixed custom-scrollbar" data-scroll-sync="fixed">
                                    <table class="table table-hover table-bordered data-table data-table-fixed">
                                        <thead class="head">
                                            <tr>
                                                <th scope="col" class="text-center fixed-date-heading">
                                                    <div class="flex flex-col items-center">
                                                        <span class="font-bold">{{ $fixedHead['label'] }}</span>
                                                        <span class="tw-badge tw-badge-gray text-xs mt-1">{{ $fixedHead['unit'] }}</span>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $row)
                                            <tr class="data-row">
                                                <td scope="col" class="text-center fixed-date-cell">
                                                    {{ $row['receipt_date'] ?? '' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="split-table-scroll custom-scrollbar" data-scroll-sync="main">
                                    <div class="split-table-main-content" data-scroll-sync="main-content">
                                        <table class="table table-hover table-bordered data-table">
                                            <thead class="head">
                                                <tr>
                                                    @foreach($scrollHeads as $head)
                                                        <th scope="col" class="text-center">
                                                            <div class="flex flex-col items-center">
                                                                <span class="font-bold">{{ $head['label'] }}</span>
                                                                <span class="tw-badge tw-badge-gray text-xs mt-1">{{ $head['unit'] }}</span>
                                                            </div>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $row)
                                                <tr class="data-row">
                                                    @foreach($row as $key => $item)
                                                        @if($key !== 'receipt_date')
                                                            <td scope="col" class="text-center">
                                                                {{ $item }}
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="split-table-rail-spacer" aria-hidden="true"></div>
                                <div class="split-table-horizontal-rail custom-scrollbar" data-scroll-sync="horizontal-rail">
                                    <div class="split-table-horizontal-rail-inner" data-scroll-sync="horizontal-inner"></div>
                                </div>
                            </div>
                        @else
                            <div class="table-scroll-wrap custom-scrollbar">
                                <div class="table-scroll-inner">
                                    <table class="table table-hover table-bordered data-table">
                                        <thead class="head">
                                            <tr>
                                                @foreach($heads as $head)
                                                    <th scope="col" class="text-center">
                                                        <div class="flex flex-col items-center">
                                                            <span class="font-bold">{{ $head['label'] }}</span>
                                                            <span class="tw-badge tw-badge-gray text-xs mt-1">{{ $head['unit'] }}</span>
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $row)
                                            <tr class="data-row">
                                                @foreach($row as $item)
                                                    <td scope="col" class="text-center">
                                                        {{ $item }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    @if ($showResultsView)
                        <div class="w-full text-center p-4">
                            <div class="tw-alert tw-alert-amber flex items-center justify-center" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <div>{{ __('messages.downloads.no_data_show') }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>

    @if ($showColumnsModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data="{ columnSelection: @js($selectedColumns) }"
        >
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto shadow-xl dark:bg-gray-800">
                <div class="bg-emerald-600 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                    <h5 class="m-0 font-semibold">{{ __('messages.downloads.columns_modal_title') }}</h5>
                    <button type="button" class="text-white hover:text-gray-200 text-2xl leading-none" wire:click="closeColumnsModal" aria-label="{{ __('messages.actions.close') }}">
                        &times;
                    </button>
                </div>
                <div class="p-4">
                    <p class="text-gray-500 mb-3">{{ __('messages.downloads.columns_modal_help') }}</p>
                    <div class="columns-grid">
                        @foreach ($availableColumnOptions as $key => $head)
                            <div class="flex items-center gap-2 mb-1">
                                <input
                                    class="accent-emerald-600"
                                    type="checkbox"
                                    id="download-column-{{ $key }}"
                                    value="{{ $key }}"
                                    x-model="columnSelection"
                                >
                                <label class="text-sm text-gray-700 dark:text-gray-300" for="download-column-{{ $key }}">
                                    {{ $head['label'] }} {{ $head['unit'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-gray-200 p-4 flex justify-end gap-2 dark:border-gray-700">
                    <button type="button" class="tw-btn tw-btn-gray" wire:click="closeColumnsModal">{{ __('messages.actions.cancel') }}</button>
                    <button type="button" class="tw-btn tw-btn-emerald" x-on:click="$wire.saveColumnConfiguration(columnSelection)" wire:loading.attr="disabled">
                        {{ __('messages.downloads.columns_save_button') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="fixed inset-0 bg-black/40 z-40"></div>
    @endif
</div>


@section('css')
<style>
    /* Estilos para el header sticky de la tabla */
    .head {
        background: linear-gradient(135deg, var(--theme-gradient-start) 0%, var(--theme-gradient-end) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .head th {
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 1rem 0.75rem !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
        vertical-align: middle;
        background: linear-gradient(135deg, var(--theme-gradient-start) 0%, var(--theme-gradient-end) 100%);
        white-space: nowrap;
    }

    /* Estilos para la tabla de datos */
    .data-table {
        width: max-content;
        min-width: 100%;
        margin-bottom: 0;
        background: white;
        border-collapse: separate !important;
        border-spacing: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .data-table-fixed {
        width: 100%;
        min-width: 100%;
    }

    .data-table tbody tr.data-row {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .data-table tbody tr.data-row:hover {
        background-color: #f8f9fc !important;
    }

    .data-table tbody td {
        padding: 0.75rem;
        font-size: 0.9rem;
        color: #5a5c69;
        border-color: #e3e6f0;
        white-space: nowrap;
    }

    .data-table tbody tr:nth-child(even) {
        background-color: #fafbfc;
    }

    .split-table-layout {
        display: grid;
        grid-template-columns: 190px minmax(0, 1fr);
        grid-template-rows: minmax(0, 60vh) 22px;
        grid-template-areas:
            "fixed main"
            "spacer rail";
        gap: 0;
        width: 100%;
    }

    .split-table-layout.split-table-layout-no-horizontal-overflow {
        grid-template-rows: minmax(0, 60vh) 0;
    }

    .split-table-fixed,
    .split-table-scroll,
    .table-scroll-wrap {
        height: 60vh;
        max-height: 60vh;
        background: #fff;
    }

    .split-table-fixed {
        grid-area: fixed;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: none !important;
        -ms-overflow-style: none;
        border: 1px solid #e3e6f0;
        border-right: 0;
        border-bottom: 0;
        border-radius: 0.5rem 0 0 0;
    }

    .split-table-fixed.custom-scrollbar::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
    }

    .split-table-scroll {
        grid-area: main;
        overflow-y: auto;
        overflow-x: hidden;
        width: 100%;
        min-width: 0;
        border: 1px solid #e3e6f0;
        border-bottom: 0;
        border-radius: 0 0.5rem 0 0;
    }

    .split-table-main-content {
        width: max-content;
        min-width: 100%;
        will-change: transform;
    }

    .split-table-scroll .data-table {
        width: max-content;
        min-width: 100%;
    }

    .fixed-date-heading,
    .fixed-date-cell {
        width: 190px;
        min-width: 190px;
        max-width: 190px;
    }

    .split-table-rail-spacer {
        grid-area: spacer;
        height: 22px;
        background: #fff;
        border: 1px solid #e3e6f0;
        border-right: 0;
        border-radius: 0 0 0 0.5rem;
    }

    .split-table-horizontal-rail {
        grid-area: rail;
        height: 22px;
        max-height: 22px;
        overflow-x: auto;
        overflow-y: hidden;
        background: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0 0 0.5rem 0;
    }

    .split-table-horizontal-rail-inner {
        height: 1px;
    }

    .split-table-horizontal-rail.is-hidden,
    .split-table-rail-spacer.is-hidden {
        display: none;
    }

    .split-table-layout.split-table-layout-no-horizontal-overflow .split-table-fixed {
        border-bottom: 1px solid #e3e6f0;
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .split-table-layout.split-table-layout-no-horizontal-overflow .split-table-scroll {
        border-bottom: 1px solid #e3e6f0;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    /* Scrollbar personalizado */
    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
        height: 14px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--theme-gradient-start) 0%, var(--theme-gradient-end) 100%);
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--theme-gradient-hover-start) 0%, var(--theme-gradient-hover-end) 100%);
    }

    /* Container de la tabla */
    .table-container {
        animation: fadeIn 0.5s ease-in;
        width: 100%;
    }

    .columns-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.25rem 1rem;
    }

    @media (max-width: 992px) {
        .columns-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .columns-grid {
            grid-template-columns: 1fr;
        }
    }

    .table-scroll-wrap {
        position: relative;
        overflow: auto;
        width: 100%;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
    }

    .table-scroll-inner {
        width: max-content;
        min-width: 100%;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Select Control */
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

    /* ===== Modo oscuro: tabla de datos =====
       El texto del td ya lo aclara themes.css; aquí se corrigen los FONDOS
       (antes quedaban blancos → texto blanco sobre fondo blanco). */
    body[data-theme="dark"] .data-table,
    body[data-theme="dark"] .split-table-fixed,
    body[data-theme="dark"] .split-table-scroll,
    body[data-theme="dark"] .table-scroll-wrap,
    body[data-theme="dark"] .split-table-rail-spacer,
    body[data-theme="dark"] .split-table-horizontal-rail {
        background: var(--card-bg);
        border-color: var(--card-border);
    }

    body[data-theme="dark"] .data-table tbody td {
        color: var(--text-color);
        border-color: var(--card-border);
    }

    body[data-theme="dark"] .data-table tbody tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.04);
    }

    body[data-theme="dark"] .data-table tbody tr.data-row:hover {
        background-color: var(--hover-bg) !important;
    }

    body[data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.08);
    }
</style>
@stop

@section('js')
    <script>
        let maxDate = new Date(@js($maxDate));
        let minDate = new Date(@js($minDate));

        function clearRowHeight(row) {
            if (!row) {
                return;
            }

            row.style.height = '';
            row.querySelectorAll('th, td').forEach((cell) => {
                cell.style.height = '';
            });
        }

        function applyRowHeight(row, height) {
            if (!row) {
                return;
            }

            row.style.height = `${height}px`;
            row.querySelectorAll('th, td').forEach((cell) => {
                cell.style.height = `${height}px`;
            });
        }

        function applyHorizontalOffset(mainContent, scrollLeft) {
            if (!mainContent) {
                return;
            }

            mainContent.style.transform = `translateX(${-Math.max(0, scrollLeft)}px)`;
        }

        function synchronizeSplitTableHeights(group) {
            const fixedTable = group.querySelector('.data-table-fixed');
            const mainTable = group.querySelector('.split-table-scroll .data-table');

            if (!fixedTable || !mainTable) {
                return;
            }

            const fixedHeaderRow = fixedTable.querySelector('thead tr');
            const mainHeaderRow = mainTable.querySelector('thead tr');
            const fixedRows = Array.from(fixedTable.querySelectorAll('tbody tr'));
            const mainRows = Array.from(mainTable.querySelectorAll('tbody tr'));
            const rowsLength = Math.min(fixedRows.length, mainRows.length);

            clearRowHeight(fixedHeaderRow);
            clearRowHeight(mainHeaderRow);

            fixedRows.forEach(clearRowHeight);
            mainRows.forEach(clearRowHeight);

            if (fixedHeaderRow && mainHeaderRow) {
                const headerHeight = Math.max(
                    fixedHeaderRow.getBoundingClientRect().height,
                    mainHeaderRow.getBoundingClientRect().height
                );

                applyRowHeight(fixedHeaderRow, headerHeight);
                applyRowHeight(mainHeaderRow, headerHeight);
            }

            for (let index = 0; index < rowsLength; index++) {
                const fixedRow = fixedRows[index];
                const mainRow = mainRows[index];
                const rowHeight = Math.max(
                    fixedRow.getBoundingClientRect().height,
                    mainRow.getBoundingClientRect().height
                );

                applyRowHeight(fixedRow, rowHeight);
                applyRowHeight(mainRow, rowHeight);
            }
        }

        function synchronizeHorizontalRail(group) {
            const mainPanel = group.querySelector('[data-scroll-sync="main"]');
            const mainContent = group.querySelector('[data-scroll-sync="main-content"]');
            const mainTable = group.querySelector('.split-table-scroll .data-table');
            const rail = group.querySelector('[data-scroll-sync="horizontal-rail"]');
            const railInner = group.querySelector('[data-scroll-sync="horizontal-inner"]');
            const spacer = group.querySelector('.split-table-rail-spacer');

            if (!mainPanel || !mainContent || !mainTable || !rail || !railInner || !spacer) {
                return;
            }

            const contentWidth = Math.ceil(mainTable.scrollWidth);
            const viewportWidth = Math.ceil(mainPanel.clientWidth);
            const hasHorizontalOverflow = contentWidth > viewportWidth + 1;

            railInner.style.width = `${contentWidth}px`;
            group.classList.toggle('split-table-layout-no-horizontal-overflow', !hasHorizontalOverflow);
            rail.classList.toggle('is-hidden', !hasHorizontalOverflow);
            spacer.classList.toggle('is-hidden', !hasHorizontalOverflow);

            if (!hasHorizontalOverflow) {
                rail.scrollLeft = 0;
                applyHorizontalOffset(mainContent, 0);
                return;
            }

            const maxScrollLeft = Math.max(0, contentWidth - viewportWidth);

            if (rail.scrollLeft > maxScrollLeft) {
                rail.scrollLeft = maxScrollLeft;
            }

            applyHorizontalOffset(mainContent, rail.scrollLeft);
        }

        function initializeSplitTableSync() {
            document.querySelectorAll('[data-scroll-sync-group]').forEach((group) => {
                const fixedPanel = group.querySelector('[data-scroll-sync="fixed"]');
                const mainPanel = group.querySelector('[data-scroll-sync="main"]');
                const rail = group.querySelector('[data-scroll-sync="horizontal-rail"]');

                if (!fixedPanel || !mainPanel) {
                    return;
                }

                if (fixedPanel.dataset.scrollSyncInitialized !== 'true' || mainPanel.dataset.scrollSyncInitialized !== 'true') {
                    let activeVerticalSource = null;

                    const syncFromMain = () => {
                        if (activeVerticalSource === 'fixed') {
                            return;
                        }

                        activeVerticalSource = 'main';
                        fixedPanel.scrollTop = mainPanel.scrollTop;
                        window.requestAnimationFrame(() => {
                            activeVerticalSource = null;
                        });
                    };

                    const syncFromFixed = () => {
                        if (activeVerticalSource === 'main') {
                            return;
                        }

                        activeVerticalSource = 'fixed';
                        mainPanel.scrollTop = fixedPanel.scrollTop;
                        window.requestAnimationFrame(() => {
                            activeVerticalSource = null;
                        });
                    };

                    mainPanel.addEventListener('scroll', syncFromMain, { passive: true });
                    fixedPanel.addEventListener('scroll', syncFromFixed, { passive: true });
                    fixedPanel.dataset.scrollSyncInitialized = 'true';
                    mainPanel.dataset.scrollSyncInitialized = 'true';
                }

                if (rail && rail.dataset.scrollSyncInitialized !== 'true') {
                    const syncFromRail = () => {
                        const currentMainContent = group.querySelector('[data-scroll-sync="main-content"]');
                        applyHorizontalOffset(currentMainContent, rail.scrollLeft);
                    };

                    rail.addEventListener('scroll', syncFromRail, { passive: true });
                    rail.dataset.scrollSyncInitialized = 'true';
                }

                if (rail && mainPanel.dataset.horizontalWheelSyncInitialized !== 'true') {
                    const syncHorizontalWheel = (event) => {
                        const horizontalDelta = event.shiftKey ? event.deltaY : event.deltaX;

                        if (!horizontalDelta || rail.classList.contains('is-hidden')) {
                            return;
                        }

                        event.preventDefault();
                        rail.scrollLeft += horizontalDelta;
                    };

                    mainPanel.addEventListener('wheel', syncHorizontalWheel, { passive: false });
                    mainPanel.dataset.horizontalWheelSyncInitialized = 'true';
                }

                fixedPanel.scrollTop = mainPanel.scrollTop;
                synchronizeHorizontalRail(group);
                synchronizeSplitTableHeights(group);
            });
        }

        document.addEventListener('livewire:initialized', () => {
            $('#format-select').on('change', function (e) {
                $('#pnlYear').hide();
                $('#pnlMonth').hide();
                $('#pnlDate').hide();
                rangeValue = $(this).val();
                if (rangeValue == 'daily')
                {
                    $('#pnlDate').show();
                }
                if (rangeValue == 'monthly') {
                    $('#pnlYear').show();
                    $('#pnlMonth').show();
                }
                if (rangeValue == 'annual') {
                    $('#pnlYear').show();
                    $('#pnlMonth').hide();
                }
            });

            window.requestAnimationFrame(() => {
                initializeSplitTableSync();
            });

            window.addEventListener('resize', () => {
                window.requestAnimationFrame(() => {
                    initializeSplitTableSync();
                });
            });
        });

        Livewire.on('showResults', ([data]) => {
            $("#station-select").prop("disabled", false);
            document.getElementById('format-select').disabled = false;
            document.getElementById('from-date-input').disabled = false;
            document.getElementById('to-date-input').disabled = false;
            document.getElementById('year-select').disabled = false;
            document.getElementById('month-select').disabled = false;

            window.requestAnimationFrame(() => {
                initializeSplitTableSync();
            });

            window.setTimeout(() => {
                initializeSplitTableSync();
            }, 40);
        });

        function isValidDate(date) {
            const parsedDate = new Date(date);
            return !isNaN(parsedDate.getTime());
        }

        function blurFromDate() {
            let selectedFromDateVal = document.getElementById('from-date-input').value;
            let nowDate = new Date(selectedFromDateVal);
            if (!isValidDate(selectedFromDateVal)) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_from_date'))
                });
                return;
            }

            if (nowDate > maxDate) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')]))
                });
                return;
            }

            if (nowDate < minDate) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')]))
                });
                return;
            }
        }

        function blurToDate() {
            let selectedFromDateVal = document.getElementById('from-date-input').value;
            let selectedToDateVal = document.getElementById('to-date-input').value;
            let fromDate = new Date(selectedFromDateVal);
            let toDate = new Date(selectedToDateVal);
            if (!isValidDate(selectedToDateVal)) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_to_date'))
                });
                return;
            }

            if (toDate > maxDate) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')]))
                });
                return;
            }

            if (toDate < minDate) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')]))
                });
                return;
            }

            if (isValidDate(selectedFromDateVal)) {
                if (toDate < fromDate) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_to_date_less_than_from_date'))
                    });
                    return;
                }

                if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_year_to_date_equal_than_year_from_date'))
                    });
                    return;
                }
            }
        }

        function checkParams() {
            let selectedStationVal = document.getElementById('station-select').value;
            let selectedRangeVal = document.getElementById('format-select').value;
            let selectedFromDateVal = document.getElementById('from-date-input').value;
            let selectedToDateVal = document.getElementById('to-date-input').value;
            let selectedYearVal = document.getElementById('year-select').value;
            let selectedMonthVal = document.getElementById('month-select').value;
            let fromDate = new Date(selectedFromDateVal);
            let toDate = new Date(selectedToDateVal);

            if (selectedStationVal < 1) {
                Swal.fire({
                    text: @js(__('messages.downloads.errors.select_station'))
                });
                return false;
            }

            if (selectedRangeVal == 'daily') {
                if (!isValidDate(selectedFromDateVal)) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_from_date'))
                    });
                    return false;
                }

                if (fromDate > maxDate) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_from_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')]))
                    });
                    return false;
                }

                if (fromDate < minDate) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_from_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')]))
                    });
                    return false;
                }

                if (!isValidDate(selectedToDateVal)) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_to_date'))
                    });
                    return false;
                }

                if (toDate > maxDate) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_to_date_greater_than', ['maxdate' => $this->maxDate->format('d/m/Y')]))
                    });
                    return false;
                }

                if (toDate < minDate) {
                    Swal.fire({
                        text: @js(__('messages.downloads.errors.select_to_date_less_than', ['mindate' => $this->minDate->format('d/m/Y')]))
                    });
                    return false;
                }

                if (isValidDate(selectedFromDateVal)) {
                    if (toDate < fromDate) {
                        Swal.fire({
                            text: @js(__('messages.downloads.errors.select_to_date_less_than_from_date'))
                        });
                        return false;
                    }

                    if (fromDate.getUTCFullYear() != toDate.getUTCFullYear()) {
                        Swal.fire({
                            text: @js(__('messages.downloads.errors.select_year_to_date_equal_than_year_from_date'))
                        });
                        return false;
                    }
                }
            }

            return true;
        }

        function showData() {
            if (checkParams()) {
                let selectedStationVal = document.getElementById('station-select').value;
                let selectedFormatVal = document.getElementById('format-select').value;
                let selectedFromDateVal = document.getElementById('from-date-input').value;
                let selectedToDateVal = document.getElementById('to-date-input').value;
                let selectedYearVal = document.getElementById('year-select').value;
                let selectedMonthVal = document.getElementById('month-select').value;

                $("#station-select").prop("disabled", true);
                document.getElementById('format-select').disabled = true;
                document.getElementById('from-date-input').disabled = true;
                document.getElementById('to-date-input').disabled = true;
                document.getElementById('year-select').disabled = true;
                document.getElementById('month-select').disabled = true;
                $('#pnlResults').hide();
                Livewire.dispatch('showData', {
                    stationId: selectedStationVal,
                    format: selectedFormatVal,
                    fromDate: selectedFromDateVal,
                    toDate: selectedToDateVal,
                    year: selectedYearVal,
                    month: selectedMonthVal
                });
            }
        }

        function downloadData(downloadType = 'excel') {
            if (checkParams()) {
                let selectedStationVal = document.getElementById('station-select').value;
                let selectedFormatVal = document.getElementById('format-select').value;
                let selectedFromDateVal = document.getElementById('from-date-input').value;
                let selectedToDateVal = document.getElementById('to-date-input').value;
                let selectedYearVal = document.getElementById('year-select').value;
                let selectedMonthVal = document.getElementById('month-select').value;
                let eventName = downloadType === 'pdf' ? 'downloadPdfData' : 'downloadData';

                Livewire.dispatch(eventName, {
                    stationId: selectedStationVal,
                    format: selectedFormatVal,
                    fromDate: selectedFromDateVal,
                    toDate: selectedToDateVal,
                    year: selectedYearVal,
                    month: selectedMonthVal
                });
            }
        }
    </script>
@stop
