@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.dashboard.title'))
@section('subtitle', __('mma.subscriber_portal.dashboard.subtitle'))

@section('content')
    @php
        $subscriptionStatus = [0 => 'pending', 1 => 'active', 2 => 'expired', 3 => 'cancelled', 4 => 'suspended'];
        $paymentStatus = [0 => 'pending', 1 => 'paid', 2 => 'failed', 3 => 'refunded', 4 => 'expired'];
        $requestStatus = [0 => 'pending', 1 => 'in_review', 2 => 'contacted', 3 => 'converted', 4 => 'closed', 5 => 'rejected'];
    @endphp

    <div class="grid gap-4 md:grid-cols-3">
        <section class="rounded-lg border border-white/10 bg-white/5 p-4">
            <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.dashboard.cards.account') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ auth()->user()->state === 1 ? __('mma.admin.common.active') : __('mma.admin.common.inactive') }}</p>
        </section>

        <section class="rounded-lg border border-white/10 bg-white/5 p-4">
            <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.dashboard.cards.subscription') }}</p>
            <p class="mt-2 text-2xl font-semibold">
                @if ($subscription)
                    {{ __('mma.admin.user_subscriptions.status.'.($subscriptionStatus[$subscription->status] ?? 'pending')) }}
                @else
                    {{ __('mma.subscriber_portal.empty.no_subscription') }}
                @endif
            </p>
        </section>

        <section class="rounded-lg border border-white/10 bg-white/5 p-4">
            <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.dashboard.cards.events') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $events->count() }}</p>
        </section>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <section class="rounded-lg border border-white/10 bg-white/5">
            <div class="flex items-center justify-between gap-3 border-b border-white/10 p-4">
                <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.dashboard.latest_purchases') }}</h2>
                <a href="{{ route('subscriber.purchases') }}" class="text-sm font-semibold text-emerald-300 hover:text-emerald-200">{{ __('mma.subscriber_portal.actions.view_all') }}</a>
            </div>
            <div class="divide-y divide-white/10">
                @forelse ($payments as $payment)
                    <div class="flex items-center justify-between gap-4 p-4">
                        <div>
                            <p class="font-semibold">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
                            <p class="text-sm text-gray-400">{{ $payment->subscription?->plan?->name ?? __('mma.admin.common.not_available') }}</p>
                        </div>
                        <span class="rounded-full bg-white/10 px-2 py-1 text-xs font-semibold text-gray-200">
                            {{ __('mma.admin.subscription_payments.status.'.($paymentStatus[$payment->status] ?? 'pending')) }}
                        </span>
                    </div>
                @empty
                    <p class="p-4 text-sm text-gray-400">{{ __('mma.subscriber_portal.empty.no_purchases') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-white/10 bg-white/5">
            <div class="flex items-center justify-between gap-3 border-b border-white/10 p-4">
                <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.dashboard.latest_requests') }}</h2>
                <a href="{{ route('subscriber.purchases') }}" class="text-sm font-semibold text-emerald-300 hover:text-emerald-200">{{ __('mma.subscriber_portal.actions.view_all') }}</a>
            </div>
            <div class="divide-y divide-white/10">
                @forelse ($purchaseRequests as $request)
                    <div class="flex items-center justify-between gap-4 p-4">
                        <div>
                            <p class="font-semibold">{{ $request->event?->name ?? $request->plan?->name ?? __('mma.admin.purchase_requests.request_types.'.$request->request_type) }}</p>
                            <p class="text-sm text-gray-400">{{ $request->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="rounded-full bg-white/10 px-2 py-1 text-xs font-semibold text-gray-200">
                            {{ __('mma.admin.purchase_requests.status.'.($requestStatus[$request->status] ?? 'pending')) }}
                        </span>
                    </div>
                @empty
                    <p class="p-4 text-sm text-gray-400">{{ __('mma.subscriber_portal.empty.no_requests') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-5 rounded-lg border border-white/10 bg-white/5">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 p-4">
            <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.dashboard.next_events') }}</h2>
            <a href="{{ route('subscriber.events') }}" class="text-sm font-semibold text-emerald-300 hover:text-emerald-200">{{ __('mma.subscriber_portal.actions.view_all') }}</a>
        </div>
        <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-4">
            @forelse ($events as $event)
                @include('subscriber.partials.event-card', ['event' => $event])
            @empty
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.empty.no_events') }}</p>
            @endforelse
        </div>
    </section>
@endsection
