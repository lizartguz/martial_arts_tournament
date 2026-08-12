@extends('layouts.landing')

@section('title', __('mma.landing.news.title').' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('mma.landing.news.title') }}</h1>
        <p class="mt-2 text-sm text-gray-400">{{ __('mma.landing.news.subtitle') }}</p>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            @forelse ($newsPosts as $post)
                <a href="{{ route('landing.news.show', $post->slug) }}" class="overflow-hidden rounded-lg border border-white/10 bg-white/5 hover:border-emerald-400/60">
                    <img src="{{ asset($post->cover_image ?: 'images/mma/generated/news-cover.webp') }}" alt="{{ $post->title }}" class="h-40 w-full object-cover">
                    <div class="p-4">
                        <p class="text-xs text-gray-400">{{ $post->published_at?->format('d/m/Y') }}</p>
                        <h2 class="mt-1 line-clamp-2 text-base font-semibold">{{ $post->title }}</h2>
                        @if ($post->excerpt)
                            <p class="mt-1 line-clamp-2 text-sm text-gray-400">{{ $post->excerpt }}</p>
                        @endif
                        <span class="mt-3 inline-block text-sm font-semibold text-emerald-400">{{ __('mma.landing.news.read_more') }}</span>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded border border-white/10 bg-white/5 p-6 text-gray-300">
                    {{ __('mma.landing.news.empty') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $newsPosts->links() }}</div>
    </div>
@endsection
