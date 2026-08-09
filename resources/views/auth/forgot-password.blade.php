<x-guest-layout>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 min-h-screen">
        
        <!-- Contenedor del formulario con diseño moderno -->
        <div class="w-full sm:max-w-md animate-slide-up">
            <div class="auth-card">
                
                <!-- Logo dentro del formulario -->
                <div class="px-6 pt-2 pb-0">
                    <img src="{{asset('frontend/images/reduced_logo.png')}}" 
                         class="w-full h-auto object-contain animate-fade-in" 
                         style="filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.08));"
                         alt="Logo" />
                </div>
                
                <!-- Header con gradiente -->
                <div class="">
                    <p class="auth-header-text text-center text-black">{{ __('messages.forgot_password.title') }}</p>
                </div>

                <!-- Cuerpo del formulario -->
                <div class="auth-body">
                    
                    <!-- Instrucciones -->
                    <p class="text-sm text-gray-600 mb-6 text-center">
                        {{ __('messages.forgot_password.instructions') }}
                    </p>

                    <!-- Mensajes de éxito -->
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

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Campo Email -->
                        <div class="mb-5">
                            <label for="email" class="auth-label">
                                <i class="fas fa-envelope"></i> {{ __('messages.forgot_password.email_label') }}
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
                                placeholder="{{ __('messages.forgot_password.email_placeholder') }}"
                            />
                        </div>

                        <!-- Botón de envío -->
                        <button type="submit" class="auth-button auth-button-primary mb-4">
                            <i class="fas fa-paper-plane mr-2"></i> {{ __('messages.forgot_password.submit') }}
                        </button>

                        <!-- Link de regreso al login -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200 hover:underline">
                                <i class="fas fa-arrow-left mr-1"></i> {{ __('messages.forgot_password.back_to_login') }}
                            </a>
                        </div>

                    </form>
                </div>

                <!-- Footer opcional -->
                <div class="auth-footer">
                    <p class="auth-footer-text">
                        © {{ date('Y') }} SenvaTec. {{ __('messages.login.rights_reserved') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
