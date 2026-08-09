<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center no-underline" aria-label="{{ __('Volver al inicio') }}">
                    <img src="{{ asset('images/te-veo.gif') }}" alt="{{ __('Te veo') }}" class="w-40 h-40 object-contain">
                </a>
                <p class="mt-3 text-sm text-gray-500">{{ __('Zona secreta para curiosos con demasiado tiempo libre.') }}</p>
            </div>
        </x-slot>

        <div class="text-center">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Registro no disponible') }}</h2>
            <p class="mt-3 text-sm text-gray-600">
                {{ __('Buen intento, explorador de URLs. Aquí no aparecen cuentas nuevas por arte de magia.') }}
            </p>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Si realmente necesitas acceso, inicia sesión con una cuenta existente o habla con el administrador.') }}
            </p>

            <div class="mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Ir a iniciar sesión') }}
                </a>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
