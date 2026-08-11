@extends('layouts.landing')

@section('title', $settings->seoTitle())

@section('content')
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <h1 class="text-4xl font-bold leading-tight sm:text-5xl">{{ $settings->publicTitle() }}</h1>
            <p class="mt-4 max-w-2xl text-base text-gray-300">{{ $settings->shortDescription() ?: __('mma.landing.hero_text') }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#eventos" class="rounded bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                    {{ __('mma.landing.view_events') }}
                </a>
                <a href="{{ route('landing.contact') }}" class="rounded border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white hover:text-gray-950">
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
                                @if ($fighter->profile_image)
                                    <img src="{{ asset($fighter->profile_image) }}" alt="{{ trim($fighter->first_name.' '.$fighter->last_name) }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center text-gray-600"><i class="fas fa-user-ninja text-3xl"></i></div>
                                @endif
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
                        @if ($post->cover_image)
                            <img src="{{ asset($post->cover_image) }}" alt="{{ $post->title }}" class="h-40 w-full object-cover">
                        @endif
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
