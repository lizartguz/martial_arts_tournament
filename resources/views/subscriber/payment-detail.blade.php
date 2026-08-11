@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.purchases.detail_title'))
@section('subtitle', __('mma.subscriber_portal.purchases.detail_subtitle'))

@section('content')
    @php
        $paymentStatus = [0 => 'pending', 1 => 'paid', 2 => 'failed', 3 => 'refunded', 4 => 'expired'];
        $settings = app(\App\Services\SystemSettingsService::class);
    @endphp

    <div class="mb-4">
        <a href="{{ route('subscriber.purchases') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white">
            <i class="fas fa-arrow-left"></i>
            <span>{{ __('mma.subscriber_portal.actions.back_to_purchases') }}</span>
        </a>
    </div>

    <section class="rounded-lg border border-white/10 bg-white/5 p-4">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.concept') }}</p>
                <p class="mt-1 font-semibold">{{ $payment->subscription?->plan?->name ?? __('mma.admin.common.not_available') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.amount') }}</p>
                <p class="mt-1 font-semibold">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.method') }}</p>
                <p class="mt-1 font-semibold">{{ $payment->payment_method }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.status') }}</p>
                <p class="mt-1 font-semibold">{{ __('mma.admin.subscription_payments.status.'.($paymentStatus[$payment->status] ?? 'pending')) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.columns.date') }}</p>
                <p class="mt-1 font-semibold">{{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.purchases.proof_status') }}</p>
                <p class="mt-1 font-semibold">
                    {{ filled($payment->payment_proof_path) ? __('mma.subscriber_portal.purchases.proof_uploaded_label') : __('mma.subscriber_portal.purchases.proof_missing_label') }}
                </p>
            </div>
        </div>

        @if ($payment->notes)
            <div class="mt-4 border-t border-white/10 pt-4">
                <p class="text-sm text-gray-400">{{ __('mma.subscriber_portal.purchases.notes') }}</p>
                <p class="mt-1 text-sm">{{ $payment->notes }}</p>
            </div>
        @endif
    </section>

    @if ((int) $payment->status === 0)
        <section class="mt-5 rounded-lg border border-white/10 bg-white/5 p-4">
            <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.purchases.upload_proof_title') }}</h2>
            <p class="mt-1 text-sm text-gray-400">{{ __('mma.subscriber_portal.purchases.upload_proof_hint') }}</p>

            <form method="POST" action="{{ route('subscriber.purchases.payments.proof', $payment) }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.purchases.proof_field') }}</label>
                    <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                    <i class="fas fa-upload"></i>
                    <span>{{ __('mma.subscriber_portal.purchases.upload_proof_submit') }}</span>
                </button>
            </form>
        </section>
    @endif

    <section class="mt-5 rounded-lg border border-white/10 bg-white/5 p-4">
        <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.purchases.contact_title') }}</h2>
        <p class="mt-1 text-sm text-gray-400">{{ __('mma.subscriber_portal.purchases.contact_hint') }}</p>
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
@endsection
