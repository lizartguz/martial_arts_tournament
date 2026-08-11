@props([
    'id' => null,
    'name' => null,
    'multiple' => false,
    'placeholder' => null,
    'search' => true,
    'actionsBox' => false,
    'maxOptions' => null,
])

@php
    $selectId = $id ?: ('tw-select-' . uniqid());
    $ph = $placeholder ?: __('messages.common.select_option');
@endphp

<div
    x-data="twSelect({
        multiple: {{ $multiple ? 'true' : 'false' }},
        search: {{ $search ? 'true' : 'false' }},
        actionsBox: {{ $actionsBox ? 'true' : 'false' }},
        maxOptions: {{ $maxOptions !== null && $maxOptions !== '' ? (int) $maxOptions : 'null' }},
        placeholder: @js($ph),
    })"
    @click.outside="open = false"
    class="relative w-full"
>
    {{-- Select nativo: fuente de verdad (lo lee el JS existente con .value / .val()) --}}
    <select
        x-ref="native"
        id="{{ $selectId }}"
        @if($name) name="{{ $name }}" @endif
        @if($multiple) multiple @endif
        class="sr-only"
        tabindex="-1"
        aria-hidden="true"
    >
        {{ $slot }}
    </select>

    {{-- Disparador --}}
    <button type="button" @click="toggle()" :disabled="disabled"
        class="tw-input flex min-h-[38px] w-full items-center justify-between gap-2 text-left"
        :class="disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'">
        <span class="truncate" x-html="labelHtml()"></span>
        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
    </button>

    {{-- Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute z-50 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700">

        <template x-if="search">
            <div class="border-b border-gray-100 p-2 dark:border-gray-600">
                <input type="text" x-model="query" @click.stop x-ref="search"
                       placeholder="Buscar..." class="tw-input-sm">
            </div>
        </template>

        <template x-if="multiple && actionsBox">
            <div class="flex items-center justify-between border-b border-gray-100 px-3 py-1.5 text-xs dark:border-gray-600">
                <button type="button" class="text-emerald-600 hover:underline" @click="selectAll()">Todos</button>
                <button type="button" class="text-gray-500 hover:underline" @click="deselectAll()">Ninguno</button>
            </div>
        </template>

        <ul class="max-h-60 overflow-y-auto py-1">
            <template x-for="opt in filteredOptions()" :key="opt.value">
                <li>
                    <button type="button" @click="choose(opt)"
                            class="flex w-full items-center gap-2 px-3 py-1.5 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                            :class="opt.selected ? 'bg-emerald-50 font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'text-gray-700'">
                        <template x-if="multiple">
                            <input type="checkbox" :checked="opt.selected" class="pointer-events-none accent-emerald-600">
                        </template>
                        <span class="truncate" x-html="opt.html"></span>
                    </button>
                </li>
            </template>
            <template x-if="filteredOptions().length === 0">
                <li class="px-3 py-2 text-sm text-gray-400">Sin resultados</li>
            </template>
        </ul>
    </div>
</div>

@once
<script>
    if (!window.twSelect) {
        /**
         * Inicializa el selector personalizado de la interfaz.
         */
        window.twSelect = function (config) {
            return {
                multiple: !!config.multiple,
                search: !!config.search,
                actionsBox: !!config.actionsBox,
                maxOptions: config.maxOptions || null,
                placeholder: config.placeholder || 'Seleccionar...',
                open: false,
                disabled: false,
                query: '',
                options: [],

                init() {
                    const sel = this.$refs.native;

                    // Replica el selectpicker: sin selección por defecto (un <select> nativo
                    // auto-selecciona la primera opción; lo evitamos para que .value devuelva ''
                    // hasta que el usuario elija).
                    if (!this.multiple) {
                        const hasDefault = Array.from(sel.options).some(o => o.defaultSelected);
                        if (!hasDefault) sel.selectedIndex = -1;
                    }

                    this.readOptions();
                    this.disabled = sel.disabled;

                    // Sincroniza disabled cuando el JS existente hace .prop('disabled', ...)
                    this._obs = new MutationObserver(() => {
                        this.disabled = sel.disabled;
                        if (sel.disabled) this.open = false;
                    });
                    this._obs.observe(sel, { attributes: true, attributeFilter: ['disabled'] });

                    // Re-lee si cambian las opciones
                    this._optObs = new MutationObserver(() => this.readOptions());
                    this._optObs.observe(sel, { childList: true });

                    // Enfoca el buscador al abrir
                    this.$watch('open', (v) => {
                        if (v && this.search) this.$nextTick(() => this.$refs.search && this.$refs.search.focus());
                    });
                },

                readOptions() {
                    const sel = this.$refs.native;
                    this.options = Array.from(sel.options)
                        .filter(o => o.value !== '')
                        .map(o => ({
                            value: o.value,
                            text: (o.textContent || '').trim(),
                            html: o.getAttribute('data-content') || ((o.textContent || '').trim()),
                            selected: o.selected,
                        }));
                },

                nativeOption(value) {
                    return Array.from(this.$refs.native.options).find(o => o.value === value);
                },

                selectedCount() {
                    return this.options.filter(o => o.selected).length;
                },

                filteredOptions() {
                    const q = this.query.trim().toLowerCase();
                    if (!q) return this.options;
                    return this.options.filter(o => o.text.toLowerCase().includes(q));
                },

                labelHtml() {
                    const chosen = this.options.filter(o => o.selected);
                    if (chosen.length === 0) {
                        return '<span class="text-gray-400">' + this.escape(this.placeholder) + '</span>';
                    }
                    if (this.multiple && chosen.length > 1) {
                        return chosen.length + ' seleccionados';
                    }
                    return chosen[0].html;
                },

                escape(s) {
                    const d = document.createElement('div');
                    d.textContent = s == null ? '' : s;
                    return d.innerHTML;
                },

                toggle() {
                    if (this.disabled) return;
                    this.open = !this.open;
                },

                choose(opt) {
                    const o = this.nativeOption(opt.value);
                    if (!o) return;
                    if (this.multiple) {
                        if (!o.selected && this.maxOptions && this.selectedCount() >= this.maxOptions) return;
                        o.selected = !o.selected;
                        opt.selected = o.selected;
                    } else {
                        Array.from(this.$refs.native.options).forEach(x => x.selected = false);
                        this.options.forEach(x => x.selected = false);
                        o.selected = true;
                        opt.selected = true;
                        this.open = false;
                    }
                    this.fireChange();
                },

                selectAll() {
                    let count = this.selectedCount();
                    this.filteredOptions().forEach(opt => {
                        const o = this.nativeOption(opt.value);
                        if (o && !o.selected) {
                            if (this.maxOptions && count >= this.maxOptions) return;
                            o.selected = true;
                            opt.selected = true;
                            count++;
                        }
                    });
                    this.fireChange();
                },

                deselectAll() {
                    this.options.forEach(opt => {
                        const o = this.nativeOption(opt.value);
                        if (o) o.selected = false;
                        opt.selected = false;
                    });
                    this.fireChange();
                },

                fireChange() {
                    this.$refs.native.dispatchEvent(new Event('change', { bubbles: true }));
                },
            };
        };
    }
</script>
@endonce
