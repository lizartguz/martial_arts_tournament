@php
    // Selector de idioma autónomo (sin Livewire/Tailwind): apto para la landing.
    // Cada opción enlaza a la ruta locale.switch, que setea la cookie 'locale'
    // (la lee LocaleMiddleware) y persiste la elección por 1 año.
    $localeOptions = [
        'es' => ['label' => 'Español',  'flag' => 'images/flags/bo.svg'],
        'en' => ['label' => 'English',  'flag' => 'images/flags/us.svg'],
        'pt' => ['label' => 'Português','flag' => 'images/flags/br.svg'],
        'fr' => ['label' => 'Français', 'flag' => 'images/flags/fr.svg'],
        'de' => ['label' => 'Deutsch',  'flag' => 'images/flags/de.svg'],
        'ru' => ['label' => 'Русский',  'flag' => 'images/flags/ru.svg'],
    ];
    $currentLocale = array_key_exists(app()->getLocale(), $localeOptions) ? app()->getLocale() : 'es';
@endphp

@once
<style>
    .locale-switcher { position: relative; display: inline-flex; }
    .locale-switcher__btn {
        display: inline-flex; align-items: center; gap: 8px;
        height: 40px; padding: 0 12px; cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 999px; background: rgba(255, 255, 255, 0.08);
        color: #fff; transition: background 160ms ease, border-color 160ms ease;
    }
    .locale-switcher__btn:hover { background: rgba(255, 255, 255, 0.16); border-color: rgba(255, 255, 255, 0.45); }
    .locale-switcher__flag {
        display: block; width: 26px; height: 18px; border-radius: 4px;
        object-fit: cover; border: 1px solid rgba(0, 0, 0, 0.12);
    }
    .locale-switcher__chevron { width: 14px; height: 14px; fill: currentColor; transition: transform 160ms ease; }
    .locale-switcher.is-open .locale-switcher__chevron { transform: rotate(180deg); }
    .locale-switcher__menu {
        position: absolute; top: calc(100% + 8px); right: 0; min-width: 188px;
        display: none; flex-direction: column; gap: 2px; padding: 6px;
        background: #0b1120; border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px; box-shadow: 0 22px 44px rgba(2, 6, 23, 0.45); z-index: 60;
    }
    .locale-switcher.is-open .locale-switcher__menu { display: flex; }
    /* Selectores con prefijo .locale-switcher para sobreponerse a estilos de menús contenedores. */
    .locale-switcher .locale-switcher__item {
        display: flex; align-items: center; gap: 10px;
        margin: 0; padding: 9px 10px; border-radius: 10px; color: rgba(255, 255, 255, 0.85);
        font-size: 13px; font-weight: 700; text-transform: none; letter-spacing: 0;
        transition: background 140ms ease, color 140ms ease;
    }
    .locale-switcher .locale-switcher__item:hover { background: rgba(255, 255, 255, 0.08); color: #fff; }
    .locale-switcher .locale-switcher__item.is-active { background: rgba(34, 197, 94, 0.16); color: #bbf7d0; }
    .locale-switcher__dot { width: 7px; height: 7px; margin-left: auto; border-radius: 999px; background: #22c55e; opacity: 0; }
    .locale-switcher .locale-switcher__item.is-active .locale-switcher__dot { opacity: 1; }
</style>
@endonce

<div class="locale-switcher" data-locale-switcher>
    <button type="button" class="locale-switcher__btn" data-locale-toggle
            aria-haspopup="true" aria-expanded="false" aria-label="{{ __('messages.landing.home.lang') }}">
        <img class="locale-switcher__flag"
             src="{{ asset($localeOptions[$currentLocale]['flag']) }}"
             alt="{{ $localeOptions[$currentLocale]['label'] }}">
        <svg class="locale-switcher__chevron" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div class="locale-switcher__menu" role="menu">
        @foreach ($localeOptions as $code => $option)
            <a class="locale-switcher__item {{ $code === $currentLocale ? 'is-active' : '' }}"
               href="{{ route('locale.switch', $code) }}" role="menuitem" hreflang="{{ $code }}">
                <img class="locale-switcher__flag" src="{{ asset($option['flag']) }}" alt="{{ $option['label'] }}">
                <span>{{ $option['label'] }}</span>
                <span class="locale-switcher__dot"></span>
            </a>
        @endforeach
    </div>
</div>

@once
<script nonce="{{ \App\Support\CspNonce::value() }}">
    /**
     * Inicializa el comportamiento cuando el DOM esta listo.
     */
    document.addEventListener('DOMContentLoaded', function () {
        var switchers = document.querySelectorAll('[data-locale-switcher]');

        /**
         * Procesa cada elemento de la coleccion visual.
         */
        switchers.forEach(function (sw) {
            var toggle = sw.querySelector('[data-locale-toggle]');
            if (!toggle) return;
            /**
             * Gestiona el clic del elemento interactivo.
             */
            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var isOpen = sw.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        /**
         * Gestiona los clics delegados de la vista.
         */
        document.addEventListener('click', function () {
            /**
             * Procesa cada elemento de la coleccion visual.
             */
            switchers.forEach(function (sw) {
                sw.classList.remove('is-open');
                var toggle = sw.querySelector('[data-locale-toggle]');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        });
    });
</script>
@endonce
