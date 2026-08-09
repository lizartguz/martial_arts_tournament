@props([
    'data' => [],
    'top' => '58px',
])

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="{ panelOpen: false }" style="--map-search-panel-top: {{ $top }};">
    <button
        type="button"
        class="flex items-center justify-center border border-gray-200 bg-white text-gray-700 shadow hover:bg-gray-100"
        x-show="!panelOpen"
        x-transition.opacity
        @click="panelOpen = true"
        style="position: absolute; top: var(--map-search-panel-top); left: 10px; z-index: 1001; border-radius: 999px; width: 42px; height: 42px;"
        title="{{ __('messages.stations.map.table.title') }}"
        aria-label="{{ __('messages.stations.map.table.title') }}"
    >
        <i class="fas fa-search"></i>
    </button>

    <div
        class="map-floating-panel bg-white shadow"
        x-cloak
        style="position: absolute; top: var(--map-search-panel-top); left: 10px; z-index: 1000; width: min(300px, calc(100vw - 20px)); max-height: 90%; border-radius: 8px; overflow: hidden; transform: translateX(calc(-100% - 16px)); opacity: 0; pointer-events: none; transition: transform 0.25s ease, opacity 0.25s ease;"
        :style="panelOpen
            ? 'position: absolute; top: var(--map-search-panel-top); left: 10px; z-index: 1000; width: min(300px, calc(100vw - 20px)); max-height: 90%; border-radius: 8px; overflow: hidden; transform: translateX(0); opacity: 1; pointer-events: auto; transition: transform 0.25s ease, opacity 0.25s ease;'
            : 'position: absolute; top: var(--map-search-panel-top); left: 10px; z-index: 1000; width: min(300px, calc(100vw - 20px)); max-height: 90%; border-radius: 8px; overflow: hidden; transform: translateX(calc(-100% - 16px)); opacity: 0; pointer-events: none; transition: transform 0.25s ease, opacity 0.25s ease;'"
    >
        <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
            <strong>{{ __('messages.stations.map.table.title') }}</strong>
            <button
                type="button"
                class="tw-btn-xs tw-btn-outline"
                @click="panelOpen = false"
                title="{{ __('messages.actions.close') }}"
                aria-label="{{ __('messages.actions.close') }}"
            >
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

        <div style="padding: 10px;">
            <input type="text" placeholder="{{ __('messages.stations.map.search.station') }}" wire:model.live="search" class="tw-input-sm w-full mb-2">
            <input type="text" placeholder="{{ __('messages.stations.map.search.location') }}" wire:model.live="searchLocation" class="tw-input-sm w-full">
        </div>

        <div class="map-station-list" style="max-height: calc(75vh - 160px); overflow-y: auto;">
            <table class="w-full text-sm">
                <thead class="themed-thead" style="position: sticky; top: 0; z-index: 10;">
                    <th class="text-center">{{ __('messages.stations.map.table.title') }}</th>
                </thead>
                <tbody>
                    @if (count($data) > 0)
                        @foreach ($data as $item)
                            <tr class="map-station-row" style="cursor: pointer;" wire:click="setGoMarkerZoom({{ json_encode($item) }})" @click="panelOpen = false">
                                <td>
                                    <div class="px-2 py-1">
                                        <div style="font-weight: 500;">{{ $item->name }}</div>
                                        <small class="text-sky-600">{{ $item->location }}</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center"><strong>{{ __('messages.stations.table.no_data') }}</strong></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
