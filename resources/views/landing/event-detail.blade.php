@extends('layouts.landing')

@section('title', $event->name.' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-8">
        <a href="{{ route('landing.home') }}" class="inline-flex items-center gap-2 text-sm text-emerald-300 hover:text-emerald-200">
            <i class="fas fa-arrow-left"></i>{{ __('mma.landing.back') }}
        </a>

        <section class="mt-6 overflow-hidden rounded-lg border border-white/10 bg-white/5">
            <img src="{{ \App\Support\PublicMedia::url($event->poster_image, 'images/mma/generated/event-main.webp') }}" alt="{{ $event->name }}" class="h-72 w-full object-cover">
            <div class="p-5">
                <div class="text-sm text-gray-400">{{ $event->starts_at?->format('d/m/Y H:i') }} &middot; {{ $event->venue?->name }}</div>
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
                    @php
                        $red = $fight->cornerRed;
                        $blue = $fight->cornerBlue;
                        $redName = $red?->nickname ?: trim(($red?->first_name ?? '').' '.($red?->last_name ?? ''));
                        $blueName = $blue?->nickname ?: trim(($blue?->first_name ?? '').' '.($blue?->last_name ?? ''));
                        $redImage = \App\Support\PublicMedia::url($red?->profile_image, ($red?->gender === 'female') ? 'images/mma/generated/fighter-female.webp' : 'images/mma/generated/fighter-male.webp');
                        $blueImage = \App\Support\PublicMedia::url($blue?->profile_image, ($blue?->gender === 'female') ? 'images/mma/generated/fighter-female.webp' : 'images/mma/generated/fighter-male.webp');
                    @endphp

                    <details class="group">
                        <summary class="grid cursor-pointer list-none gap-4 p-4 transition hover:bg-white/[0.03] focus-visible:outline focus-visible:outline-2 focus-visible:outline-emerald-400 md:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] md:items-center [&::-webkit-details-marker]:hidden">
                            <div class="flex min-w-0 items-center gap-3">
                                @if ($red)
                                    <span class="shrink-0 overflow-hidden rounded-full border border-white/10 bg-gray-900">
                                        <img src="{{ $redImage }}" alt="{{ $redName }}" class="h-14 w-14 object-cover">
                                    </span>
                                @else
                                    <span class="h-14 w-14 shrink-0 rounded-full border border-white/10 bg-gray-900"></span>
                                @endif

                                <span class="min-w-0">
                                    @if ($red)
                                        <span class="block truncate font-semibold">{{ $redName }}</span>
                                        <span class="mt-1 block text-xs text-gray-400">{{ $red->wins }}-{{ $red->losses }}-{{ $red->draws }}</span>
                                    @else
                                        <span class="font-semibold">Por confirmar</span>
                                    @endif
                                </span>
                            </div>

                            <span class="text-center text-xs uppercase tracking-wide text-gray-400 md:min-w-44">
                                @if ($fight->is_main_event)
                                    <span class="mb-1 block rounded bg-emerald-500 px-2 py-0.5 font-semibold text-gray-950">{{ __('mma.landing.event.main_event') }}</span>
                                @endif
                                <span class="inline-flex items-center justify-center gap-2 rounded border border-white/10 px-3 py-1.5 text-emerald-300 transition group-open:border-emerald-400/70 group-open:bg-emerald-400/10">
                                    <span>{{ $fight->weightClass?->name }} &middot; {{ __('mma.landing.vs') }}</span>
                                    <i class="fas fa-chevron-down text-[10px] transition group-open:rotate-180"></i>
                                </span>
                            </span>

                            <div class="flex min-w-0 items-center gap-3 md:flex-row-reverse md:text-right">
                                @if ($blue)
                                    <span class="shrink-0 overflow-hidden rounded-full border border-white/10 bg-gray-900">
                                        <img src="{{ $blueImage }}" alt="{{ $blueName }}" class="h-14 w-14 object-cover">
                                    </span>
                                @else
                                    <span class="h-14 w-14 shrink-0 rounded-full border border-white/10 bg-gray-900"></span>
                                @endif

                                <span class="min-w-0">
                                    @if ($blue)
                                        <span class="block truncate font-semibold">{{ $blueName }}</span>
                                        <span class="mt-1 block text-xs text-gray-400">{{ $blue->wins }}-{{ $blue->losses }}-{{ $blue->draws }}</span>
                                    @else
                                        <span class="font-semibold">Por confirmar</span>
                                    @endif
                                </span>
                            </div>
                        </summary>

                        <div class="grid gap-4 border-t border-white/10 bg-gray-950/45 p-4 md:grid-cols-2">
                            @foreach ([['fighter' => $red, 'name' => $redName, 'image' => $redImage, 'corner' => 'Esquina roja'], ['fighter' => $blue, 'name' => $blueName, 'image' => $blueImage, 'corner' => 'Esquina azul']] as $profile)
                                @php
                                    $fighter = $profile['fighter'];
                                    $record = $fighter
                                        ? $fighter->wins.'-'.$fighter->losses.'-'.$fighter->draws.($fighter->no_contests ? '-'.$fighter->no_contests.' NC' : '')
                                        : null;
                                @endphp

                                <article class="overflow-hidden rounded-lg border border-white/10 bg-white/[0.04]">
                                    @if ($fighter)
                                        <img src="{{ $profile['image'] }}" alt="{{ $profile['name'] }}" class="h-64 w-full object-cover">
                                        <div class="space-y-4 p-4">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">{{ $profile['corner'] }}</p>
                                                <h3 class="mt-1 text-xl font-bold">{{ $profile['name'] }}</h3>
                                                <p class="text-sm text-gray-400">{{ trim($fighter->first_name.' '.$fighter->last_name) }}</p>
                                            </div>

                                            <dl class="grid grid-cols-2 gap-3 text-sm">
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">R&eacute;cord</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $record }}</dd>
                                                </div>
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">Categor&iacute;a</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $fighter->weightClass?->name ?? $fight->weightClass?->name ?? 'Por definir' }}</dd>
                                                </div>
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">Equipo</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $fighter->team?->name ?? 'Independiente' }}</dd>
                                                </div>
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">Pa&iacute;s</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $fighter->country?->name ?? 'Por definir' }}</dd>
                                                </div>
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">Altura</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $fighter->height_cm ? number_format((float) $fighter->height_cm, 0).' cm' : 'Por definir' }}</dd>
                                                </div>
                                                <div class="rounded border border-white/10 bg-gray-900/70 p-3">
                                                    <dt class="text-xs uppercase text-gray-500">Alcance</dt>
                                                    <dd class="mt-1 font-semibold text-white">{{ $fighter->reach_cm ? number_format((float) $fighter->reach_cm, 0).' cm' : 'Por definir' }}</dd>
                                                </div>
                                            </dl>

                                            <a href="{{ route('landing.fighters.show', $fighter->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-300 hover:text-emerald-200">
                                                Ver perfil completo
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div class="flex min-h-64 items-center justify-center p-4 text-center text-sm text-gray-400">Peleador por confirmar</div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </details>
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
