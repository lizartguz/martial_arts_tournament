@extends('layouts.landing')

@section('title', $event->name.' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-8">
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

        <nav class="mt-4 flex items-center justify-between gap-3 text-sm">
            @if ($previousEvent)
                <a href="{{ route('landing.events.show', $previousEvent->slug) }}" class="flex min-w-0 items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-4 py-2 hover:border-emerald-400/60">
                    <i class="fas fa-chevron-left shrink-0"></i>
                    <span class="min-w-0">
                        <span class="block text-xs text-gray-400">{{ __('mma.landing.event.prev') }}</span>
                        <span class="block truncate font-semibold">{{ $previousEvent->name }}</span>
                    </span>
                </a>
            @else
                <span></span>
            @endif

            @if ($nextEvent)
                <a href="{{ route('landing.events.show', $nextEvent->slug) }}" class="flex min-w-0 items-center justify-end gap-2 rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-right hover:border-emerald-400/60">
                    <span class="min-w-0">
                        <span class="block text-xs text-gray-400">{{ __('mma.landing.event.next') }}</span>
                        <span class="block truncate font-semibold">{{ $nextEvent->name }}</span>
                    </span>
                    <i class="fas fa-chevron-right shrink-0"></i>
                </a>
            @endif
        </nav>

        <section class="mt-6">
            <h2 class="text-xl font-semibold">{{ __('mma.landing.fights_title') }}</h2>
            <div class="mt-3 divide-y divide-white/10 rounded-lg border border-white/10 bg-white/5">
                @forelse ($event->fights as $fight)
                    <div class="grid gap-3 p-4 md:grid-cols-[1fr_auto_1fr] md:items-center">
                        <div>
                            <div class="font-semibold">{{ $fight->cornerRed?->nickname ?? $fight->cornerRed?->first_name }}</div>
                        </div>
                        <div class="text-center text-xs uppercase tracking-wide text-gray-400">
                            @if ($fight->is_main_event)
                                <span class="mb-1 block rounded bg-emerald-500 px-2 py-0.5 font-semibold text-gray-950">{{ __('mma.landing.event.main_event') }}</span>
                            @endif
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

        <section class="mt-6 rounded-lg border border-white/10 bg-white/5 p-5">
            <h2 class="text-xl font-semibold">{{ __('mma.landing.event.tickets_title') }}</h2>

            @if ($event->ticketLinks->isNotEmpty())
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($event->ticketLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-3 rounded-lg border border-white/10 bg-gray-900 px-4 py-3 hover:border-emerald-400/60">
                            <span>
                                <span class="block font-semibold">{{ $link->label ?? $link->provider_name }}</span>
                                @if ($link->price_from)
                                    <span class="block text-xs text-gray-400">{{ __('mma.landing.event.price_from') }} {{ number_format((float) $link->price_from, 2) }} {{ $link->currency }}</span>
                                @endif
                            </span>
                            <i class="fas fa-arrow-up-right-from-square text-emerald-400"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-gray-400">{{ __('mma.landing.event.no_tickets') }}</p>
            @endif

            <div class="mt-4 flex flex-wrap gap-3 border-t border-white/10 pt-4">
                <a href="{{ route('landing.contact', ['event' => $event->slug, 'type' => 'event_ticket']) }}" class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                    <i class="fas fa-comment-dots"></i>
                    <span>{{ __('mma.landing.event.contact_cta') }}</span>
                </a>
                @if ($settings->whatsappPhone())
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsappPhone()) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-md border border-white/20 px-4 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                @endif
            </div>
        </section>
    </div>
@endsection
