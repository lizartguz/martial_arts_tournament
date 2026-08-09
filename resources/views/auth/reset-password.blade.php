<x-guest-layout>
    <style>
        .reset-screen {
            position: relative;
            overflow: hidden;
        }

        .reset-background {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(135deg, rgba(16, 24, 40, 0.48), rgba(15, 122, 58, 0.22)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.08)),
                url('{{ asset('images/login-background-station-senvatec.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transform: scale(1.02);
        }

        .reset-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 50% 20%, rgba(255, 255, 255, 0.18), transparent 38%),
                linear-gradient(180deg, rgba(241, 245, 249, 0.08), rgba(15, 23, 42, 0.22));
        }

        .reset-content {
            position: relative;
            z-index: 1;
        }

        .reset-panel {
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .reset-brand-wrap {
            padding: 1.35rem 1.35rem 1rem;
            background-color: #ffffff !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }

        .reset-brand-logo {
            height: 5rem;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 0.9rem;
            filter: drop-shadow(0 10px 18px rgba(15, 23, 42, 0.12));
        }

        .reset-title {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            margin-top: 1rem;
            text-align: center;
        }

        .reset-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.55;
            margin-top: 0.45rem;
            text-align: center;
        }

        .reset-body {
            padding-top: 1.15rem;
        }

        .reset-submit {
            border-radius: 0.85rem;
            box-shadow: 0 14px 28px -14px rgba(34, 197, 94, 0.6);
        }

        .reset-safe-note {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.9rem;
            color: #166534;
            font-size: 0.82rem;
            line-height: 1.55;
            margin-top: 1rem;
            padding: 0.8rem 0.95rem;
        }
    </style>

    <div class="reset-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 min-h-screen">
        <div class="reset-background" aria-hidden="true"></div>

        <div class="reset-content w-full sm:max-w-md animate-slide-up px-4 sm:px-0">
            <div class="auth-card reset-panel">
                <div class="reset-brand-wrap">
                    <div class="flex justify-center animate-fade-in">
                        <img src="{{ asset('images/logo_rectangular.png') }}"
                            class="reset-brand-logo"
                            alt="Logo SENVATEC" />
                    </div>

                    <h1 class="reset-title">{{ __('messages.reset_password.title') }}</h1>
                    <p class="reset-subtitle">
                        {{ __('messages.reset_password.subtitle') }}
                    </p>
                </div>

                <div class="auth-body reset-body">
                    @if ($errors->any())
                    <div class="alert-error">
                        <svg class="alert-error-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div class="alert-error-content">
                            <p class="alert-error-title">{{ __('messages.reset_password.error_title') }}</p>
                            <ul class="alert-error-list">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-4">
                            <label for="email" class="auth-label">
                                <i class="fas fa-envelope"></i> {{ __('messages.reset_password.email_label') }}
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="auth-input"
                                placeholder="correo@ejemplo.com" />
                        </div>

                        <div class="mt-3 mb-4">
                            <label for="password" class="auth-label">
                                <i class="fas fa-lock"></i> {{ __('messages.reset_password.new_password_label') }}
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="auth-input"
                                placeholder="{{ __('messages.reset_password.new_password_placeholder') }}" />
                        </div>

                        <div class="mt-3 mb-4">
                            <label for="password_confirmation" class="auth-label">
                                <i class="fas fa-shield-alt"></i> {{ __('messages.reset_password.confirm_password_label') }}
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="auth-input"
                                placeholder="{{ __('messages.reset_password.confirm_password_placeholder') }}" />
                        </div>

                        <div class="reset-safe-note">
                            <i class="fas fa-leaf mr-1"></i>
                            {{ __('messages.reset_password.safe_note') }}
                        </div>

                        <button type="submit" class="auth-button auth-button-primary reset-submit mt-5">
                            <i class="fas fa-key mr-2"></i> {{ __('messages.reset_password.submit') }}
                        </button>
                    </form>
                </div>

                <div class="auth-footer">
                    <p class="auth-footer-text">
                        © {{ date('Y') }} SenvaTec. {{ __('messages.login.rights_reserved') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
