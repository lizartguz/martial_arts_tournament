@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.subscription.title'))
@section('subtitle', __('mma.subscriber_portal.subscription.subtitle'))

@section('content')
    @php
        $subscriptionStatus = [0 => 'pending', 1 => 'active', 2 => 'expired', 3 => 'cancelled', 4 => 'suspended'];
        $settings = app(\App\Services\SystemSettingsService::class);
    @endphp

    <section class="rounded-lg border border-white/10 bg-white/5 p-4">
        <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.subscription.current') }}</h2>
        @if ($currentSubscription)
            <div class="mt-4 grid gap-4 md:grid-cols-4">
                <div>
                    <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.plan') }}</p>
                    <p class="mt-1 font-semibold">{{ $currentSubscription->plan?->name ?? __('mma.admin.common.not_available') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.status') }}</p>
                    <p class="mt-1 font-semibold">{{ __('mma.admin.user_subscriptions.status.'.($subscriptionStatus[$currentSubscription->status] ?? 'pending')) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.start') }}</p>
                    <p class="mt-1 font-semibold">{{ $currentSubscription->starts_at?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.end') }}</p>
                    <p class="mt-1 font-semibold">{{ $currentSubscription->ends_at?->format('d/m/Y') ?? __('mma.admin.user_subscriptions.open_ended') }}</p>
                </div>
            </div>

            @if ($currentSubscription->plan?->planFeatures?->isNotEmpty())
                <div class="mt-5 border-t border-white/10 pt-4">
                    <h3 class="text-sm font-semibold text-gray-300">{{ __('mma.subscriber_portal.subscription.benefits') }}</h3>
                    <ul class="mt-3 grid gap-2 md:grid-cols-2">
                        @foreach ($currentSubscription->plan->planFeatures as $feature)
                            <li class="flex items-start gap-2 text-sm">
                                <i class="fas fa-check-circle mt-0.5 text-emerald-400"></i>
                                <span>
                                    <span class="font-medium">{{ $feature->name }}</span>
                                    @if ($feature->value)
                                        <span class="text-gray-400">— {{ $feature->value }}</span>
                                    @endif
                                    @if ($feature->description)
                                        <span class="block text-xs text-gray-400">{{ $feature->description }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @else
            <p class="mt-4 text-sm text-gray-400">{{ __('mma.subscriber_portal.empty.no_subscription') }}</p>
        @endif
    </section>

    <section class="mt-5 rounded-lg border border-white/10 bg-white/5 p-4">
        <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.subscription.contact_title') }}</h2>
        <p class="mt-1 text-sm text-gray-400">{{ __('mma.subscriber_portal.subscription.contact_hint') }}</p>
        <div class="mt-3 flex flex-wrap gap-3">
            @if ($settings->whatsappPhone())
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsappPhone()) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            @endif
            @if ($settings->contactPhone())
                <a href="tel:{{ $settings->contactPhone() }}" class="inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                    <i class="fas fa-phone"></i>
                    <span>{{ $settings->contactPhone() }}</span>
                </a>
            @endif
            @if ($settings->contactEmail())
                <a href="mailto:{{ $settings->contactEmail() }}" class="inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $settings->contactEmail() }}</span>
                </a>
            @endif
        </div>
    </section>

    <section class="mt-5 rounded-lg border border-white/10 bg-white/5">
        <div class="border-b border-white/10 p-4">
            <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.subscription.history') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.plan') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.period') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($subscriptions as $subscription)
                        <tr>
                            <td class="px-4 py-3">{{ $subscription->plan?->name ?? __('mma.admin.common.not_available') }}</td>
                            <td class="px-4 py-3">{{ $subscription->starts_at?->format('d/m/Y') }} - {{ $subscription->ends_at?->format('d/m/Y') ?? __('mma.admin.user_subscriptions.open_ended') }}</td>
                            <td class="px-4 py-3">{{ __('mma.admin.user_subscriptions.status.'.($subscriptionStatus[$subscription->status] ?? 'pending')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-5 text-center text-gray-400">{{ __('mma.subscriber_portal.empty.no_subscription') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $subscriptions->links() }}</div>
    </section>
@endsection
