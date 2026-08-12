<x-guest-layout>
    @php
        $brand = app(\App\Services\SystemSettingsService::class);
    @endphp

    <div class="login-screen">
        <div class="login-background" aria-hidden="true"></div>

        <section class="login-shell">
            <div class="login-intro" aria-hidden="true">
                <div class="login-kicker">{{ __('mma.landing.nav.home') }}</div>
                <h1>{{ $brand->productName() }}</h1>
                <p>{{ __('messages.login.subtitle') }}</p>
            </div>

            <div class="login-card auth-card">
                <div class="login-brand-wrap">
                    <img src="{{ $brand->logoUrl() }}"
                        class="login-brand-logo"
                        alt="{{ $brand->productName() }}">
                    <p class="login-subtitle">{{ __('messages.login.subtitle') }}</p>
                </div>

                <div class="auth-body login-body">
                    @if ($errors->any())
                        <div class="alert-error">
                            <svg class="alert-error-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div class="alert-error-content">
                                <p class="alert-error-title">{{ __('messages.login.error_title') }}</p>
                                <ul class="alert-error-list">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert-success">
                            <svg class="alert-success-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div class="alert-success-content">
                                <p class="alert-success-text">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        <div>
                            <label for="email" class="auth-label">
                                <i class="fas fa-envelope"></i> {{ __('messages.login.user_label') }}
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="auth-input"
                                placeholder="{{ __('messages.login.user_placeholder') }}">
                        </div>

                        <div>
                            <label for="password" class="auth-label">
                                <i class="fas fa-lock"></i> {{ __('messages.login.password_label') }}
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="auth-input"
                                placeholder="{{ __('messages.login.password_placeholder') }}">
                        </div>

                        <div class="login-actions">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-help-link">
                                    {{ __('messages.login.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="auth-button auth-button-primary login-submit">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ __('messages.login.submit') }}</span>
                        </button>
                    </form>
                </div>

                <div class="auth-footer">
                    <p class="auth-footer-text">
                        &copy; {{ date('Y') }} {{ $brand->productName() }}. {{ __('messages.login.rights_reserved') }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
