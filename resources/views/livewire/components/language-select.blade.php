<div class="inline-flex shrink-0 items-center" x-cloak>
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button type="button" class="inline-flex h-10 items-center gap-2 rounded px-2 leading-none cursor-pointer {{$triggerClass}}">
                <img
                    class="block h-5 w-7 rounded-sm border border-black/10 object-cover shadow-sm"
                    src="{{ asset($languages[$selectedLanguage]['icon']) }}"
                    alt="{{ $languages[$selectedLanguage]['label'] }}"
                >
                <div class="ms-1">
                    <svg class="block w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            @foreach ($languages as $key => $item)
                <div class="flex items-center gap-2 px-4 py-1 text-slate-700 cursor-pointer hover:text-white hover:bg-[#263381]" onclick="selectLanguage('{{ $key }}')">
                    <div class="w-5">
                        @if ($key === $selectedLanguage)
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        @endif
                    </div>
                    <img
                        class="block h-5 w-7 rounded-sm border border-black/10 object-cover shadow-sm"
                        src="{{ asset($item['icon']) }}"
                        alt="{{ $item['label'] }}"
                    >
                    <div>
                        {{ $item['label'] }}
                    </div>
                </div>
            @endforeach
        </x-slot>
    </x-dropdown>
</div>

<script nonce="{{ \App\Support\CspNonce::value() }}">
    document.addEventListener('livewire:initialized', () => {
        let locale = localStorage.getItem('locale');
        let localeB = "{{ $selectedLanguage }}"
        if (locale === null) {
            localStorage.setItem('locale', localeB);
            locale = localeB;
            Livewire.dispatch('updateSession', [locale]);
        }
    })

    /**
     * Guarda y sincroniza el idioma seleccionado.
     */
    function selectLanguage(locale) {
        localStorage.setItem('locale', locale);
        Livewire.dispatch('setLanguage', [locale]);
    }
</script>
