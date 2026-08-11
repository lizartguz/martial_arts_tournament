<x-guest-layout>
    @php($brand = app(\App\Services\SystemSettingsService::class))
    <style>
        .login-screen {
            position: relative;
            overflow: hidden;
        }

        .login-background {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(135deg, rgba(16, 24, 40, 0.42), rgba(15, 122, 58, 0.18)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08)),
                url('{{ asset('images/login-background-station-senvatec.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transform: scale(1.02);
        }

        .login-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at center, rgba(255, 255, 255, 0.12), transparent 48%),
                linear-gradient(180deg, rgba(241, 245, 249, 0.1), rgba(248, 250, 252, 0.2));
        }

        .login-content {
            position: relative;
            z-index: 1;
        }

        .login-panel {
            position: relative;
            isolation: isolate;
            backdrop-filter: blur(10px);
        }

        .login-panel::before {
            content: none;
        }

        .login-brand-wrap {
            position: relative;
            padding: 1.35rem 1.35rem 0.75rem;
            z-index: 2;
            background-color: #ffffff !important;
            background-image: none !important;
            opacity: 1;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }

        .login-brand-logo {
            height: 5rem;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 0.9rem;
            filter: drop-shadow(0 10px 18px rgba(15, 23, 42, 0.12));
        }

        .login-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.75rem;
            text-align: center;
        }

        .login-body {
            position: relative;
            z-index: 1;
            padding-top: 1.1rem;
        }

        .login-help-link {
            color: #1f7a3f;
        }

        .login-help-link:hover {
            color: #14532d;
        }

        .login-submit {
            border-radius: 0.85rem;
            box-shadow: 0 14px 28px -14px rgba(34, 197, 94, 0.6);
        }
    </style>
    <div class="login-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 min-h-screen">
        <div class="login-background" aria-hidden="true"></div>

        <!-- Contenedor del formulario con diseño moderno -->
        <div class="login-content w-full sm:max-w-md animate-slide-up px-4 sm:px-0">
            <div class="auth-card login-panel">

                <!-- Logo dentro del formulario -->
                <div class="login-brand-wrap">
                    <div class="flex justify-center animate-fade-in">
                        <img src="{{ $brand->logoUrl() }}"
                            class="login-brand-logo"
                            alt="{{ $brand->productName() }}" />
                    </div>

                    <div class="mb-0.5">
                        <p class="login-subtitle">{{ __('messages.login.subtitle') }}</p>
                    </div>
                </div>

                <!-- Cuerpo del formulario -->
                <div class="auth-body login-body">

                    <!-- Mensajes de error -->
                    @if ($errors->any())
                    <div class="alert-error">
                        <svg class="alert-error-icon" viewBox="0 0 20 20" fill="currentColor">
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

                    <!-- Mensaje de éxito -->
                    @if (session('status'))
                    <div class="alert-success">
                        <svg class="alert-success-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div class="alert-success-content">
                            <p class="alert-success-text">{{ session('status') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Campo Email -->
                        <div class="mb-4">
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
                                placeholder="{{ __('messages.login.user_placeholder') }}" />
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mt-3 mb-4">
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
                                placeholder="{{ __('messages.login.password_placeholder') }}" />
                        </div>

                        <!-- Opciones adicionales -->
                        <div class="flex items-center justify-between mt-1 mb-5">
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="login-help-link text-sm font-medium transition-colors duration-200 hover:underline">
                                {{ __('messages.login.forgot_password') }}
                            </a>
                            @endif
                        </div>

                        <!-- Botón de envío -->
                        <button type="submit" class="auth-button auth-button-primary login-submit">
                            <i class="fas fa-sign-in-alt mr-2"></i> {{ __('messages.login.submit') }}
                        </button>

                    </form>
                </div>

                <!-- Footer opcional -->
                <div class="auth-footer">
                    <p class="auth-footer-text">
                        © {{ date('Y') }} {{ $brand->productName() }}. {{ __('messages.login.rights_reserved') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
