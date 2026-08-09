@props([
    'close' => null,            {{-- Nombre del método Livewire para cerrar (backdrop, ESC, botón X). Si es null, no se renderiza el cierre automático. --}}
    'maxWidth' => '2xl',
    'title' => null,
    'icon' => null,
    'headerClass' => 'bg-emerald-600 text-white',
])

@php
    $maxWidthClass = [
        'sm'  => 'max-w-sm',  'md'  => 'max-w-md',  'lg'  => 'max-w-lg',
        'xl'  => 'max-w-xl',  '2xl' => 'max-w-2xl', '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl', '5xl' => 'max-w-5xl', '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl', 'full'=> 'max-w-full',
    ][$maxWidth] ?? 'max-w-2xl';
@endphp

<div class="fixed inset-0 z-[60] overflow-y-auto"
     x-data
     @if($close) x-on:keydown.escape.window="$wire.{{ $close }}()" @endif
     role="dialog" aria-modal="true">

    {{-- Fondo --}}
    <div class="fixed inset-0 bg-black/50" @if($close) wire:click="{{ $close }}" @endif></div>

    {{-- Panel --}}
    <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
        <div class="relative w-full {{ $maxWidthClass }} rounded-lg bg-white shadow-xl dark:bg-gray-800 dark:text-gray-100">

            @if($title !== null || isset($header))
                <div class="flex items-center justify-between rounded-t-lg px-4 py-3 {{ $headerClass }}">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h5 class="m-0 text-base font-semibold">
                            @if($icon)<i class="{{ $icon }} mr-2"></i>@endif{{ $title }}
                        </h5>
                    @endisset

                    @if($close)
                        <button type="button" wire:click="{{ $close }}"
                                class="text-white/80 transition hover:text-white" aria-label="Cerrar">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            @endif

            <div class="max-h-[75vh] overflow-y-auto p-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex items-center justify-end gap-2 rounded-b-lg border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
