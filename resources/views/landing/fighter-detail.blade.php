@extends('layouts.landing')

@php
    $fullName = trim($fighter->first_name.' '.$fighter->last_name);
@endphp

@section('title', $fullName.' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-8">
        <a href="{{ route('landing.fighters.index') }}" class="inline-flex items-center gap-2 text-sm text-emerald-300 hover:text-emerald-200">
            <i class="fas fa-arrow-left"></i>{{ __('mma.landing.back') }}
        </a>

        <section class="mt-6 grid gap-6 rounded-lg border border-white/10 bg-white/5 p-5 sm:grid-cols-[220px_1fr]">
            <div class="mx-auto aspect-square w-full max-w-[220px] overflow-hidden rounded-lg bg-gray-900">
                <img src="{{ asset($fighter->profile_image ?: ($fighter->gender === 'female' ? 'images/mma/generated/fighter-female.webp' : 'images/mma/generated/fighter-male.webp')) }}" alt="{{ $fullName }}" class="h-full w-full object-cover">
            </div>

            <div>
                @if ($fighter->nickname)
                    <p class="text-sm text-emerald-400">"{{ $fighter->nickname }}"</p>
                @endif
                <h1 class="text-3xl font-bold">{{ $fullName }}</h1>

                <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-300">
                    @if ($fighter->weightClass)
                        <span class="rounded-full border border-white/15 px-3 py-1">{{ $fighter->weightClass->name }}</span>
                    @endif
                    @if ($fighter->team)
                        <span class="rounded-full border border-white/15 px-3 py-1">{{ $fighter->team->name }}</span>
                    @endif
                    @if ($fighter->country)
                        <span class="rounded-full border border-white/15 px-3 py-1">{{ $fighter->country->name }}</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3 text-center sm:max-w-xs">
                    <div class="rounded-lg border border-white/10 bg-gray-900 py-3">
                        <p class="text-xl font-bold text-emerald-400">{{ $fighter->wins }}</p>
                        <p class="text-xs text-gray-400">{{ __('mma.landing.fighters.wins') }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-gray-900 py-3">
                        <p class="text-xl font-bold text-red-400">{{ $fighter->losses }}</p>
                        <p class="text-xs text-gray-400">{{ __('mma.landing.fighters.losses') }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-gray-900 py-3">
                        <p class="text-xl font-bold text-gray-300">{{ $fighter->draws }}</p>
                        <p class="text-xs text-gray-400">{{ __('mma.landing.fighters.draws') }}</p>
                    </div>
                </div>

                @if ($fighter->bio)
                    <div class="mt-5 border-t border-white/10 pt-4">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-300">{{ __('mma.landing.fighters.bio_title') }}</h2>
                        <p class="mt-2 text-sm text-gray-300">{{ $fighter->bio }}</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="mt-6">
            <h2 class="text-xl font-semibold">{{ __('mma.landing.fighters.fight_history') }}</h2>
            <div class="mt-3 divide-y divide-white/10 rounded-lg border border-white/10 bg-white/5">
                @forelse ($fights as $fight)
                    @php
                        $isRed = (int) $fight->corner_red_fighter_id === (int) $fighter->id;
                        $opponent = $isRed ? $fight->cornerBlue : $fight->cornerRed;
                        $wonFight = $fight->result && (int) $fight->result->winner_fighter_id === (int) $fighter->id;
                        $lostFight = $fight->result && $fight->result->winner_fighter_id && (int) $fight->result->winner_fighter_id !== (int) $fighter->id;
                        $isDraw = $fight->result && in_array($fight->result->result_type, ['draw', 'no_contest'], true);
                    @endphp
                    <a href="{{ route('landing.events.show', $fight->event->slug) }}" class="flex flex-wrap items-center justify-between gap-3 p-4 hover:bg-white/5">
                        <div>
                            <p class="text-xs text-gray-400">{{ $fight->event->starts_at?->format('d/m/Y') }} · {{ $fight->event->name }}</p>
                            <p class="mt-1 font-semibold">
                                {{ __('mma.landing.vs') }} {{ $opponent?->nickname ?? trim(($opponent?->first_name ?? '').' '.($opponent?->last_name ?? '')) }}
                            </p>
                            @if ($fight->result)
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $fight->result->method }}
                                    @if ($fight->result->round)
                                        · {{ __('mma.landing.fighters.round') }} {{ $fight->result->round }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if ($wonFight)
                            <span class="shrink-0 rounded bg-emerald-500 px-2 py-1 text-xs font-semibold text-gray-950">{{ __('mma.landing.fighters.result_win') }}</span>
                        @elseif ($lostFight)
                            <span class="shrink-0 rounded bg-red-500/80 px-2 py-1 text-xs font-semibold text-white">{{ __('mma.landing.fighters.result_loss') }}</span>
                        @elseif ($isDraw)
                            <span class="shrink-0 rounded bg-amber-400/80 px-2 py-1 text-xs font-semibold text-gray-950">{{ __('mma.landing.fighters.result_draw') }}</span>
                        @else
                            <span class="shrink-0 rounded bg-white/10 px-2 py-1 text-xs font-semibold text-gray-300">{{ __('mma.landing.fighters.result_pending') }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-4 text-gray-400">{{ __('mma.landing.fighters.no_fights') }}</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
