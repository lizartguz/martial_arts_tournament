@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.purchases.title'))
@section('subtitle', __('mma.subscriber_portal.purchases.subtitle'))

@section('content')
    @php
        $paymentStatus = [0 => 'pending', 1 => 'paid', 2 => 'failed', 3 => 'refunded', 4 => 'expired'];
        $requestStatus = [0 => 'pending', 1 => 'in_review', 2 => 'contacted', 3 => 'converted', 4 => 'closed', 5 => 'rejected'];
    @endphp

    <section class="rounded-lg border border-white/10 bg-white/5">
        <div class="border-b border-white/10 p-4">
            <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.purchases.payments') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.concept') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.amount') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.method') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.status') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('subscriber.purchases.payments.show', $payment) }}" class="font-semibold text-emerald-400 hover:underline">
                                    {{ $payment->subscription?->plan?->name ?? __('mma.admin.common.not_available') }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                            <td class="px-4 py-3">{{ __('mma.admin.subscription_payments.status.'.($paymentStatus[$payment->status] ?? 'pending')) }}</td>
                            <td class="px-4 py-3">{{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-gray-400">{{ __('mma.subscriber_portal.empty.no_purchases') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $payments->links() }}</div>
    </section>

    <section class="mt-5 rounded-lg border border-white/10 bg-white/5">
        <div class="border-b border-white/10 p-4">
            <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.purchases.requests') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.concept') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.channel') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.status') }}</th>
                        <th class="px-4 py-3">{{ __('mma.subscriber_portal.columns.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($purchaseRequests as $request)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('subscriber.purchases.requests.show', $request) }}" class="font-semibold text-emerald-400 hover:underline">
                                    {{ $request->event?->name ?? $request->plan?->name ?? __('mma.admin.purchase_requests.request_types.'.$request->request_type) }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ __('mma.admin.purchase_requests.channels.'.$request->preferred_channel) }}</td>
                            <td class="px-4 py-3">{{ __('mma.admin.purchase_requests.status.'.($requestStatus[$request->status] ?? 'pending')) }}</td>
                            <td class="px-4 py-3">{{ $request->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center text-gray-400">{{ __('mma.subscriber_portal.empty.no_requests') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $purchaseRequests->links() }}</div>
    </section>
@endsection
