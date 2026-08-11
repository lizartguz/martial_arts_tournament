@extends('layouts.subscriber')

@section('title', __('mma.subscriber_portal.profile.title'))
@section('subtitle', __('mma.subscriber_portal.profile.subtitle'))

@section('content')
    <form method="POST" action="{{ route('subscriber.profile.update') }}" class="max-w-3xl rounded-lg border border-white/10 bg-white/5 p-4">
        @csrf
        @method('PATCH')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.lastname') }}</label>
                <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.number_phone') }}</label>
                <input type="text" name="number_phone" value="{{ old('number_phone', $user->number_phone) }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.identity_document') }}</label>
                <input type="text" name="identity_document" value="{{ old('identity_document', $user->identity_document) }}" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                <i class="fas fa-save"></i>
                <span>{{ __('mma.subscriber_portal.actions.save') }}</span>
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('user-password.update') }}" class="mt-5 max-w-3xl rounded-lg border border-white/10 bg-white/5 p-4">
        @csrf
        @method('PUT')

        <h2 class="text-base font-semibold">{{ __('mma.subscriber_portal.profile.password_title') }}</h2>
        <p class="mt-1 text-sm text-gray-400">{{ __('mma.subscriber_portal.profile.password_hint') }}</p>

        @if (session('status') === 'password-updated')
            <div class="mt-4 rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ __('mma.subscriber_portal.profile.password_updated') }}
            </div>
        @endif

        @if ($errors->updatePassword->any())
            <div class="mt-4 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                {{ $errors->updatePassword->first() }}
            </div>
        @endif

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.current_password') }}</label>
                <input type="password" name="current_password" autocomplete="current-password" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.new_password') }}</label>
                <input type="password" name="password" autocomplete="new-password" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-300">{{ __('mma.subscriber_portal.profile.form.confirm_password') }}</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full rounded-md border-white/10 bg-gray-900 text-sm text-white">
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-gray-950 hover:bg-emerald-400">
                <i class="fas fa-key"></i>
                <span>{{ __('mma.subscriber_portal.profile.password_submit') }}</span>
            </button>
        </div>
    </form>
@endsection
