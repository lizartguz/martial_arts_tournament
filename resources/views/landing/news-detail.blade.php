@extends('layouts.landing')

@section('title', $post->title.' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8">
        <a href="{{ route('landing.news.index') }}" class="inline-flex items-center gap-2 text-sm text-emerald-300 hover:text-emerald-200">
            <i class="fas fa-arrow-left"></i>{{ __('mma.landing.back') }}
        </a>

        <article class="mt-6">
            @if ($post->cover_image)
                <img src="{{ asset($post->cover_image) }}" alt="{{ $post->title }}" class="h-72 w-full rounded-lg object-cover">
            @endif

            <p class="mt-4 text-sm text-gray-400">{{ $post->published_at?->format('d/m/Y') }}</p>
            <h1 class="mt-1 text-3xl font-bold">{{ $post->title }}</h1>

            @if ($post->excerpt)
                <p class="mt-3 text-lg text-gray-300">{{ $post->excerpt }}</p>
            @endif

            @if ($post->content)
                <div class="mt-5 whitespace-pre-line text-gray-300">{{ $post->content }}</div>
            @endif
        </article>
    </div>
@endsection
