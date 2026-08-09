<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @if (session('bienvenida'))
                    <div class="m-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800" role="alert">
                        <h4 class="mb-1 text-lg font-semibold">Hola {{ session('nameUser') }} !</h4>
                        <p>{{ session('bienvenida') }}</p>
                    </div>
                @endif
                <x-welcome/>
            </div>
        </div>
    </div>
</x-app-layout>
