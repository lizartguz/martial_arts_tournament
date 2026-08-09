@php
    use App\Support\AdminPanel;

    $panelUrl = auth()->check() ? route('admin-dashboard') : url('/login');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('messages.errors.403.title') }} | {{ AdminPanel::brandName() }}</title>

    <link rel="icon" type="image/png" href="{{ AdminPanel::favicon() }}?v=20260510">
    <link rel="shortcut icon" type="image/png" href="{{ AdminPanel::favicon() }}?v=20260510">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('darkMode');
                var theme = savedTheme === 'enabled' ? 'dark'
                    : savedTheme === 'disabled' ? 'light'
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-xl text-center">
            <img
                src="{{ AdminPanel::brandLogo() }}"
                alt="{{ AdminPanel::brandName() }}"
                class="mx-auto mb-8 h-16 w-16 rounded-full object-contain shadow-sm"
            >

            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800">
                <i class="fas fa-lock" aria-hidden="true"></i>
                <span>{{ __('messages.errors.403.eyebrow') }}</span>
            </div>

            <h1 class="text-3xl font-bold tracking-normal text-slate-950 sm:text-4xl">
                {{ __('messages.errors.403.title') }}
            </h1>

            <p class="mx-auto mt-4 max-w-md text-base leading-7 text-slate-700">
                {{ __('messages.errors.403.message') }}
            </p>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                {{ __('messages.errors.403.help') }}
            </p>

            <div class="mt-8">
                <a
                    href="{{ $panelUrl }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <i class="fas fa-home" aria-hidden="true"></i>
                    <span>{{ __('messages.errors.403.dashboard') }}</span>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
