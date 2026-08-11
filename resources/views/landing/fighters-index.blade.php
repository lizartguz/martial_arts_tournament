@extends('layouts.landing')

@section('title', __('mma.landing.fighters.title').' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('mma.landing.fighters.title') }}</h1>
        <p class="mt-2 text-sm text-gray-400">{{ __('mma.landing.fighters.subtitle') }}</p>

        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @forelse ($fighters as $fighter)
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
            @empty
                <div class="col-span-full rounded border border-white/10 bg-white/5 p-6 text-gray-300">
                    {{ __('mma.landing.fighters.empty') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $fighters->links() }}</div>
    </div>
@endsection
