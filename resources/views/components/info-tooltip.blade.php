@props(['title' => null])

{{--
    Icono de información que muestra un globo explicativo al hacer clic.
    Uso:
        <x-info-tooltip title="Título opcional">Texto explicativo del campo.</x-info-tooltip>
--}}
<span x-data="{ open: false }"
      @keydown.escape.window="open = false"
      class="relative ml-1 inline-block align-middle font-normal">
    <button type="button"
            @click.stop.prevent="open = !open"
            @click.outside="open = false"
            :aria-expanded="open"
            aria-label="Más información"
            class="cursor-pointer text-blue-500 transition-colors hover:text-blue-700 focus:outline-none dark:text-blue-400 dark:hover:text-blue-300">
        <i class="fas fa-info-circle"></i>
    </button>

    <div x-show="open"
         x-cloak
         x-transition.opacity
         @click.stop
         class="absolute left-1/2 top-full z-50 mt-2 w-64 -translate-x-1/2 rounded-lg border border-gray-200 bg-white p-3 text-left text-xs font-normal leading-relaxed text-gray-600 shadow-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
        @if ($title)
            <span class="mb-1 block font-semibold text-gray-800 dark:text-gray-100">{{ $title }}</span>
        @endif
        {{ $slot }}
    </div>
</span>
