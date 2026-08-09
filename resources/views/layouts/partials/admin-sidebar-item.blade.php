@php
    use App\Support\AdminPanel;

    $isActive = AdminPanel::isActive($item);
    $hasSubmenu = !empty($item['submenu']);
@endphp

@if($hasSubmenu)
    {{-- El menu ya llega filtrado por permisos desde AdminPanel::menu(). --}}
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm
                       transition-colors duration-150
                       {{ $isActive
                           ? 'bg-sidebar-active text-sidebar-text font-medium'
                           : 'text-sidebar-text/80 hover:bg-sidebar-hover hover:text-sidebar-text' }}">

            @if(!empty($item['icon']))
                <i class="{{ $item['icon'] }} w-5 text-center text-sidebar-icon flex-shrink-0"></i>
            @endif

            <span class="flex-1 text-left">{{ AdminPanel::label($item['text'] ?? '') }}</span>

            <i class="fas fa-angle-right text-xs text-sidebar-icon/60 transition-transform duration-200"
               :class="open ? 'rotate-90' : ''"></i>
        </button>

        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-0.5 ml-4 space-y-0.5 border-l border-white/10 pl-3">
            @foreach($item['submenu'] as $child)
                @include('layouts.partials.admin-sidebar-item', ['item' => $child])
            @endforeach
        </div>
    </div>
@else
    <a href="{{ AdminPanel::url($item) }}"
       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm
              transition-colors duration-150
              {{ $isActive
                  ? 'bg-sidebar-active text-sidebar-text font-medium'
                  : 'text-sidebar-text/80 hover:bg-sidebar-hover hover:text-sidebar-text' }}">

        @if(!empty($item['icon']))
            <i class="{{ $item['icon'] }} w-5 text-center text-sidebar-icon flex-shrink-0"></i>
        @endif

        <span>{{ AdminPanel::label($item['text'] ?? '') }}</span>
    </a>
@endif
