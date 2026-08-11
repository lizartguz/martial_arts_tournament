<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->seoTitle() }}</title>
    @if ($settings->seoDescription())
        <meta name="description" content="{{ $settings->seoDescription() }}">
    @endif
    <link rel="icon" type="image/png" href="{{ $settings->faviconUrl() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white">
    <main class="min-h-screen">
        <section class="border-b border-white/10 bg-gray-950">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
                <a href="{{ route('landing.home') }}" class="flex items-center gap-3">
                    <img src="{{ $settings->logoUrl() }}" alt="{{ $settings->productName() }}" class="h-10 w-10 rounded-full object-cover">
                    <span class="text-sm font-semibold">{{ $settings->productName() }}</span>
                </a>
                <div class="flex items-center gap-2">
                    <x-locale-switcher />
                    <a href="{{ route('login') }}" class="rounded border border-white/20 px-3 py-1.5 text-sm hover:bg-white hover:text-gray-950">
                        {{ __('mma.landing.login') }}
                    </a>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <h1 class="text-4xl font-bold leading-tight sm:text-5xl">{{ $settings->publicTitle() }}</h1>
                <p class="mt-4 max-w-2xl text-base text-gray-300">{{ $settings->shortDescription() ?: __('mma.landing.hero_text') }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#eventos" class="rounded bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                        {{ __('mma.landing.view_events') }}
                    </a>
                    <a href="tel:{{ $settings->contactPhone() ?: $settings->whatsappPhone() ?: '+59100000000' }}" class="rounded border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white hover:text-gray-950">
                        {{ __('mma.landing.contact') }}
                    </a>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg border border-white/10 bg-white/5">
                @php($featured = $events->first())
                @if ($featured?->poster_image)
                    <img src="{{ asset($featured->poster_image) }}" alt="{{ $featured->name }}" class="h-80 w-full object-cover">
                @else
                    <div class="flex h-80 items-center justify-center bg-gray-900 text-gray-500">
                        {{ __('mma.landing.no_image') }}
                    </div>
                @endif
            </div>
        </section>

        <section id="eventos" class="mx-auto max-w-7xl px-4 pb-12">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold">{{ __('mma.landing.events_title') }}</h2>
                    <p class="text-sm text-gray-400">{{ __('mma.landing.events_subtitle') }}</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($events as $event)
                    <a href="{{ route('landing.events.show', $event->slug) }}" class="overflow-hidden rounded-lg border border-white/10 bg-white/5 hover:border-emerald-400/60">
                        @if ($event->poster_image)
                            <img src="{{ asset($event->poster_image) }}" alt="{{ $event->name }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-3 text-xs text-gray-400">
                                <span>{{ $event->starts_at?->format('d/m/Y H:i') }}</span>
                                @if ($event->is_featured)
                                    <span class="rounded bg-emerald-500 px-2 py-0.5 font-semibold text-gray-950">{{ __('mma.landing.featured') }}</span>
                                @endif
                            </div>
                            <h3 class="mt-2 text-lg font-semibold">{{ $event->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-300">{{ $event->subtitle ?? $event->description }}</p>
                            <p class="mt-3 text-sm text-gray-400">{{ $event->venue?->name }}</p>
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-white/10 bg-white/5 p-6 text-gray-300">
                        {{ __('mma.landing.empty_events') }}
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
