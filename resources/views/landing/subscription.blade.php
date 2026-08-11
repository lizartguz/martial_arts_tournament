@extends('layouts.landing')

@section('title', __('mma.landing.subscription.title').' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('mma.landing.subscription.title') }}</h1>
        <p class="mt-2 max-w-2xl text-sm text-gray-400">{{ __('mma.landing.subscription.subtitle') }}</p>

        @if ($plans->isEmpty())
            <div class="mt-6 rounded border border-white/10 bg-white/5 p-6 text-gray-300">
                {{ __('mma.landing.subscription.empty') }}
            </div>
        @else
            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="flex flex-col rounded-lg border border-white/10 bg-white/5 p-5">
                        <h2 class="text-lg font-semibold">{{ $plan->name }}</h2>
                        @if ($plan->description)
                            <p class="mt-1 text-sm text-gray-400">{{ $plan->description }}</p>
                        @endif

                        <p class="mt-4">
                            <span class="text-3xl font-bold">{{ number_format((float) $plan->price, 2) }}</span>
                            <span class="text-sm text-gray-400">{{ $plan->currency }} / {{ __('mma.admin.subscription_plans.billing_periods.'.$plan->billing_period) }}</span>
                        </p>

                        @if ($plan->planFeatures->isNotEmpty())
                            <ul class="mt-4 flex-1 space-y-2 text-sm">
                                @foreach ($plan->planFeatures as $feature)
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check-circle mt-0.5 text-emerald-400"></i>
                                        <span>
                                            {{ $feature->name }}
                                            @if ($feature->value)
                                                <span class="text-gray-400">— {{ $feature->value }}</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        <a href="{{ route('landing.contact', ['plan' => $plan->slug, 'type' => 'subscription']) }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                            <i class="fas fa-comment-dots"></i>
                            <span>{{ __('mma.landing.subscription.cta') }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
