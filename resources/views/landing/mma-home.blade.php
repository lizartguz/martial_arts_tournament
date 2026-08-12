@extends('layouts.landing')

@section('title', $settings->seoTitle())

@section('content')
    @php
        $featured = $events->first();
        $heroImage = $featured?->poster_image ?: ($settings->defaultImagePath() ?: 'images/mma/generated/landing-hero.webp');
    @endphp

    <section class="relative overflow-hidden border-b border-white/10">
        <div class="absolute inset-0" aria-hidden="true">
            <img src="{{ asset($heroImage) }}" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/82 to-gray-950/25"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-gray-950/35"></div>
        </div>

        <div class="relative mx-auto grid min-h-[620px] max-w-7xl gap-8 px-4 py-16 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
            <div class="pb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-300">{{ $settings->productName() }}</p>
                <h1 class="mt-4 max-w-3xl text-4xl font-black leading-tight text-white sm:text-6xl">{{ $settings->publicTitle() }}</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-gray-200">{{ $settings->shortDescription() ?: __('mma.landing.hero_text') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#eventos" class="rounded bg-emerald-400 px-5 py-3 text-sm font-bold text-gray-950 shadow-lg shadow-emerald-950/30 transition hover:bg-emerald-300">
                        {{ __('mma.landing.view_events') }}
                    </a>
                    <a href="{{ route('landing.contact') }}" class="rounded border border-white/30 px-5 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-gray-950">
                        {{ __('mma.landing.contact') }}
                    </a>
                </div>
            </div>

            @if ($featured)
                <a href="{{ route('landing.events.show', $featured->slug) }}" class="mb-10 max-w-xl overflow-hidden rounded border border-white/15 bg-gray-950/72 shadow-2xl shadow-black/30 backdrop-blur transition hover:border-emerald-300/70">
                    <div class="grid gap-0 sm:grid-cols-[180px_1fr]">
                        <img src="{{ asset($featured->poster_image ?: 'images/mma/generated/event-main.webp') }}" alt="{{ $featured->name }}" class="h-48 w-full object-cover sm:h-full">
                        <div class="p-5">
                            <div class="flex items-center justify-between gap-3 text-xs text-gray-400">
                                <span>{{ $featured->starts_at?->format('d/m/Y H:i') }}</span>
                                @if ($featured->is_featured)
                                    <span class="rounded bg-red-500 px-2 py-0.5 font-bold text-white">{{ __('mma.landing.featured') }}</span>
                                @endif
                            </div>
                            <h2 class="mt-3 text-xl font-bold text-white">{{ $featured->name }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm text-gray-300">{{ $featured->subtitle ?? $featured->description }}</p>
                            <p class="mt-4 text-sm text-emerald-300">{{ $featured->venue?->name }}</p>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </section>

    <section id="eventos" class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">{{ __('mma.landing.events_title') }}</h2>
                <p class="mt-1 text-sm text-gray-400">{{ __('mma.landing.events_subtitle') }}</p>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($events as $event)
                <a href="{{ route('landing.events.show', $event->slug) }}" class="group overflow-hidden rounded border border-white/10 bg-white/[0.06] transition hover:-translate-y-0.5 hover:border-emerald-300/70 hover:bg-white/[0.09]">
                    <img src="{{ asset($event->poster_image ?: 'images/mma/generated/event-card.webp') }}" alt="{{ $event->name }}" class="h-52 w-full object-cover transition duration-300 group-hover:scale-105">
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-3 text-xs text-gray-400">
                            <span>{{ $event->starts_at?->format('d/m/Y H:i') }}</span>
                            @if ($event->is_featured)
                                <span class="rounded bg-emerald-400 px-2 py-0.5 font-bold text-gray-950">{{ __('mma.landing.featured') }}</span>
                            @endif
                        </div>
                        <h3 class="mt-2 text-lg font-bold">{{ $event->name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm leading-6 text-gray-300">{{ $event->subtitle ?? $event->description }}</p>
                        <p class="mt-3 text-sm text-gray-400">{{ $event->venue?->name }}</p>
                    </div>
                </a>
            @empty
                <div class="rounded border border-white/10 bg-white/[0.06] p-6 text-gray-300">
                    {{ __('mma.landing.empty_events') }}
                </div>
            @endforelse
        </div>
    </section>

    {{--
        Ranking publico por categoria de peso y genero: preparado para activarse
        cuando el negocio lo habilite (ver App\Services\SystemSettingsService::landingShowRankings()).

        @if ($settings->landingShowRankings())
            <section id="rankings" class="mx-auto max-w-7xl px-4 pb-12">
                <h2 class="text-2xl font-semibold">{{ __('mma.landing.rankings.title') }}</h2>
                ...listado de FighterRanking por categoria de peso y genero...
            </section>
        @endif
    --}}

    @if ($featuredFighters->isNotEmpty())
        <section class="border-t border-white/10 bg-white/[0.02]">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold">{{ __('mma.landing.fighters.featured_title') }}</h2>
                        <p class="text-sm text-gray-400">{{ __('mma.landing.fighters.featured_subtitle') }}</p>
                    </div>
                    <a href="{{ route('landing.fighters.index') }}" class="shrink-0 text-sm font-semibold text-emerald-400 hover:underline">{{ __('mma.landing.fighters.view_all') }}</a>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($featuredFighters as $fighter)
                        <a href="{{ route('landing.fighters.show', $fighter->slug) }}" class="group overflow-hidden rounded-lg border border-white/10 bg-white/5 hover:border-emerald-400/60">
                            <div class="aspect-square overflow-hidden bg-gray-900">
                                <img src="{{ asset($fighter->profile_image ?: ($fighter->gender === 'female' ? 'images/mma/generated/fighter-female.webp' : 'images/mma/generated/fighter-male.webp')) }}" alt="{{ trim($fighter->first_name.' '.$fighter->last_name) }}" class="h-full w-full object-cover transition group-hover:scale-105">
                            </div>
                            <div class="p-3">
                                <p class="truncate text-sm font-semibold">{{ $fighter->nickname ? "\"{$fighter->nickname}\"" : trim($fighter->first_name.' '.$fighter->last_name) }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $fighter->weightClass?->name }}</p>
                                <p class="mt-1 text-xs text-emerald-400">{{ $fighter->wins }}-{{ $fighter->losses }}-{{ $fighter->draws }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latestNews->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold">{{ __('mma.landing.news.section_title') }}</h2>
                </div>
                <a href="{{ route('landing.news.index') }}" class="shrink-0 text-sm font-semibold text-emerald-400 hover:underline">{{ __('mma.landing.news.view_all') }}</a>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($latestNews as $post)
                    <a href="{{ route('landing.news.show', $post->slug) }}" class="overflow-hidden rounded-lg border border-white/10 bg-white/5 hover:border-emerald-400/60">
                        <img src="{{ asset($post->cover_image ?: 'images/mma/generated/news-cover.webp') }}" alt="{{ $post->title }}" class="h-40 w-full object-cover">
                        <div class="p-4">
                            <p class="text-xs text-gray-400">{{ $post->published_at?->format('d/m/Y') }}</p>
                            <h3 class="mt-1 line-clamp-2 text-base font-semibold">{{ $post->title }}</h3>
                            @if ($post->excerpt)
                                <p class="mt-1 line-clamp-2 text-sm text-gray-400">{{ $post->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
