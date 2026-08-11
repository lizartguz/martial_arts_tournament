<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->name }} | {{ $settings->productName() }}</title>
    <link rel="icon" type="image/png" href="{{ $settings->faviconUrl() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white">
    <main class="mx-auto min-h-screen max-w-5xl px-4 py-8">
        <a href="{{ route('landing.home') }}" class="inline-flex items-center gap-2 text-sm text-emerald-300 hover:text-emerald-200">
            <i class="fas fa-arrow-left"></i>{{ __('mma.landing.back') }}
        </a>

        <section class="mt-6 overflow-hidden rounded-lg border border-white/10 bg-white/5">
            @if ($event->poster_image)
                <img src="{{ asset($event->poster_image) }}" alt="{{ $event->name }}" class="h-72 w-full object-cover">
            @endif
            <div class="p-5">
                <div class="text-sm text-gray-400">{{ $event->starts_at?->format('d/m/Y H:i') }} · {{ $event->venue?->name }}</div>
                <h1 class="mt-2 text-3xl font-bold">{{ $event->name }}</h1>
                <p class="mt-3 text-gray-300">{{ $event->description ?? $event->subtitle }}</p>
            </div>
        </section>

        <section class="mt-6">
            <h2 class="text-xl font-semibold">{{ __('mma.landing.fights_title') }}</h2>
            <div class="mt-3 divide-y divide-white/10 rounded-lg border border-white/10 bg-white/5">
                @forelse ($event->fights as $fight)
                    <div class="grid gap-3 p-4 md:grid-cols-[1fr_auto_1fr] md:items-center">
                        <div>
                            <div class="font-semibold">{{ $fight->cornerRed?->nickname ?? $fight->cornerRed?->first_name }}</div>
                        </div>
                        <div class="text-center text-xs uppercase tracking-wide text-gray-400">
                            {{ $fight->weightClass?->name }} · {{ __('mma.landing.vs') }}
                        </div>
                        <div class="md:text-right">
                            <div class="font-semibold">{{ $fight->cornerBlue?->nickname ?? $fight->cornerBlue?->first_name }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-gray-400">{{ __('mma.landing.empty_fights') }}</div>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
