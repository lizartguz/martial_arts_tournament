<div class="relative" x-data="{ open: false, hi: -1 }" @click.outside="open = false; hi = -1">
    <input type="text" class="tw-input"
           placeholder="{{ $placeholder ?: __('messages.downloads.station_placeholder') }}"
           autocomplete="off"
           role="combobox" aria-autocomplete="list" :aria-expanded="open"
           wire:model.live.debounce.300ms="search"
           @focus="open = true; $event.target.select()"
           @input="open = true; hi = -1"
           @keydown.escape="open = false; hi = -1"
           @keydown.arrow-down.prevent="open = true; hi = Math.min(hi + 1, {{ count($results) }} - 1)"
           @keydown.arrow-up.prevent="hi = Math.max(hi - 1, 0)"
           @keydown.enter.prevent="hi >= 0 && $refs.list.querySelectorAll('[role=option]')[hi]?.click()">

    <div x-show="open" x-cloak x-ref="list" role="listbox"
         class="absolute z-50 mt-1 max-h-64 w-full overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">
        @if(mb_strlen(trim($search)) < 2)
            <div class="px-3 py-2 text-sm text-gray-400">{{ __('messages.common.search_hint') }}</div>
        @elseif(count($results) === 0)
            <div class="px-3 py-2 text-sm text-gray-400">{{ __('messages.common.no_results') }}</div>
        @else
            @foreach($results as $i => $st)
                <button type="button" role="option"
                        class="block w-full px-3 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                        :class="hi === {{ $i }} ? 'bg-emerald-50 dark:bg-emerald-900/30' : ''"
                        @mouseenter="hi = {{ $i }}"
                        wire:click="selectStation({{ $st['id'] }})"
                        @click="open = false; hi = -1">
                    {{ $st['name'] }}
                </button>
            @endforeach
        @endif
    </div>

    {{-- Puente con el JS existente: checkParams() lee este value por DOM.
         Se enlaza con :value (Alpine actualiza la propiedad .value de forma fiable
         cuando cambia $wire.selectedId; wire:model no reflejaba el value en el DOM). --}}
    <input type="hidden" id="{{ $inputId }}" value="{{ $selectedId }}" :value="$wire.selectedId ?? ''">
</div>
