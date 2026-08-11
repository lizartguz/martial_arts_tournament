@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.events.title'))
@section('subtitle', __('mma.subscriber_portal.events.subtitle'))

@section('content')
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($events as $event)
            @include('subscriber.partials.event-card', ['event' => $event])
        @empty
            <div class="rounded-lg border border-white/10 bg-white/5 p-6 text-sm text-gray-400">
                {{ __('mma.subscriber_portal.empty.no_events') }}
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $events->links() }}
    </div>
@endsection
