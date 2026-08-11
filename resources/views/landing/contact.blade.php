@extends('layouts.landing')

@section('title', __('mma.landing.contact_page.title').' | '.$settings->productName())

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('mma.landing.contact_page.title') }}</h1>
        <p class="mt-2 text-sm text-gray-400">{{ __('mma.landing.contact_page.subtitle') }}</p>

        @if ($event)
            <div class="mt-4 rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ __('mma.landing.contact_page.about_event', ['event' => $event->name]) }}
            </div>
        @endif

        @if ($plan)
            <div class="mt-4 rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ __('mma.landing.contact_page.about_plan', ['plan' => $plan->name]) }}
            </div>
        @endif

        @if (session('success'))
            <div class="mt-4 rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <form method="POST" action="{{ route('landing.contact.submit') }}" enctype="multipart/form-data" class="rounded-lg border border-white/10 bg-white/5 p-5">
                @csrf
                @if ($event)
                    <input type="hidden" name="event_slug" value="{{ $event->slug }}">
                @endif
                @if ($plan)
                    <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.name') }}</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" required class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.email') }}</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email') }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.phone') }}</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.whatsapp') }}</label>
                        <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp') }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.channel') }}</label>
                        <select name="preferred_channel" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                            <option value="whatsapp" @selected(old('preferred_channel', 'whatsapp') === 'whatsapp')>{{ __('mma.landing.contact_page.channel_options.whatsapp') }}</option>
                            <option value="phone" @selected(old('preferred_channel') === 'phone')>{{ __('mma.landing.contact_page.channel_options.phone') }}</option>
                            <option value="email" @selected(old('preferred_channel') === 'email')>{{ __('mma.landing.contact_page.channel_options.email') }}</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.type') }}</label>
                        <select name="request_type" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                            <option value="general_contact" @selected(old('request_type', $requestType) === 'general_contact')>{{ __('mma.landing.contact_page.type_options.general_contact') }}</option>
                            <option value="event_ticket" @selected(old('request_type', $requestType) === 'event_ticket')>{{ __('mma.landing.contact_page.type_options.event_ticket') }}</option>
                            <option value="subscription" @selected(old('request_type', $requestType) === 'subscription')>{{ __('mma.landing.contact_page.type_options.subscription') }}</option>
                            <option value="payment_proof" @selected(old('request_type', $requestType) === 'payment_proof')>{{ __('mma.landing.contact_page.type_options.payment_proof') }}</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.message') }}</label>
                        <textarea name="message" rows="4" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">{{ old('message') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.landing.contact_page.form.proof') }}</label>
                        <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
                        <p class="mt-1 text-xs text-gray-500">{{ __('mma.landing.contact_page.form.proof_hint') }}</p>
                    </div>
                </div>

                <button type="submit" class="mt-5 inline-flex items-center gap-2 rounded-md bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                    <i class="fas fa-paper-plane"></i>
                    <span>{{ __('mma.landing.contact_page.form.submit') }}</span>
                </button>
            </form>

            <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                <h2 class="text-base font-semibold">{{ __('mma.landing.contact_page.direct_title') }}</h2>
                <p class="mt-1 text-sm text-gray-400">{{ __('mma.landing.contact_page.direct_hint') }}</p>
                <div class="mt-4 flex flex-col gap-3">
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
            </div>
        </div>
    </div>
@endsection
