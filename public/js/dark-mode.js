// Script global para modo oscuro
(function () {
    function resolveThemePreference() {
        const storedTheme = localStorage.getItem('darkMode');

        if (storedTheme === 'enabled') {
            return 'dark';
        }

        if (storedTheme === 'disabled') {
            return 'light';
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function syncTheme(theme) {
        const root = document.documentElement;
        const body = document.body;
        const toggles = document.querySelectorAll('.dark-mode-toggle i');
        const isDark = theme === 'dark';

        root.setAttribute('data-theme', theme);

        if (body) {
            if (isDark) {
                body.setAttribute('data-theme', 'dark');
            } else {
                body.removeAttribute('data-theme');
            }
        }

        toggles.forEach((toggle) => {
            toggle.classList.toggle('fa-sun', isDark);
            toggle.classList.toggle('fa-moon', !isDark);
        });
    }

    function applyStoredTheme() {
        syncTheme(resolveThemePreference());
    }

    window.toggleDarkMode = function toggleDarkMode() {
        const nextTheme = resolveThemePreference() === 'dark' ? 'light' : 'dark';

        localStorage.setItem('darkMode', nextTheme === 'dark' ? 'enabled' : 'disabled');
        syncTheme(nextTheme);
    };

    window.applyStoredTheme = applyStoredTheme;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyStoredTheme, { once: true });
    } else {
        applyStoredTheme();
    }

    document.addEventListener('livewire:navigated', applyStoredTheme);
    window.addEventListener('pageshow', applyStoredTheme);
})();
