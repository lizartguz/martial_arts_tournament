<a href="{{ route('landing.events.show', $event->slug) }}" class="overflow-hidden rounded-lg border border-white/10 bg-white/5 transition hover:border-emerald-400/70">
    <img src="{{ \App\Support\PublicMedia::url($event->poster_image, 'images/mma/generated/event-card.webp') }}" alt="{{ $event->name }}" class="h-44 w-full object-cover">

    <div class="p-4">
        <div class="flex items-center justify-between gap-3 text-xs text-gray-400">
            <span>{{ $event->starts_at?->format('d/m/Y H:i') ?? __('mma.admin.common.not_available') }}</span>
            @if ($event->is_featured)
                <span class="rounded bg-emerald-500 px-2 py-0.5 font-semibold text-gray-950">{{ __('mma.landing.featured') }}</span>
            @endif
        </div>
        <h2 class="mt-2 line-clamp-2 text-base font-semibold">{{ $event->name }}</h2>
        <p class="mt-1 line-clamp-2 text-sm text-gray-400">{{ $event->subtitle ?? $event->description }}</p>
        <p class="mt-3 text-sm text-gray-500">{{ $event->venue?->name ?? __('mma.admin.common.not_available') }}</p>
    </div>
</a>
