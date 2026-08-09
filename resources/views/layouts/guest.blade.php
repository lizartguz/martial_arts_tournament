<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SenvaTec 3.0') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('frontend/images/logo_with_text.png') }}?v=20260510">
        <link rel="shortcut icon" type="image/png" href="{{ asset('frontend/images/logo_with_text.png') }}?v=20260510">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/analytics.js'])
        
        <!-- Tema Claro/Oscuro -->
        <link rel="stylesheet" href="{{ asset('css/theme/themes.css') }}">
        
        <!-- Auth Styles -->
        @vite(['resources/css/auth-common.css', 'resources/css/auth-buttons.css', 'resources/css/auth-forms.css', 'resources/css/auth-alerts.css'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body style="background: linear-gradient(135deg,rgb(36, 48, 102) 0%,rgb(98, 153, 53) 100%);">
        @php
            $showThemeToggle = ! request()->routeIs('login', 'password.request');
        @endphp

        @unless (request()->routeIs('register', 'public.station.feed'))
        <!-- Barra superior con controles de idioma y tema -->
        <div class="fixed top-0 right-0 z-50 flex items-center gap-2 p-4">
            <!-- Selector de idiomas -->
            <livewire:components.language-select triggerClass="hover:bg-white/20"/>
            
            @if($showThemeToggle)
                <!-- Toggle de modo oscuro -->
                <button class="dark-mode-toggle p-2 rounded text-white hover:bg-white/20 transition" onclick="toggleDarkMode()" title="Modo Oscuro/Claro">
                    <i class="fas fa-moon"></i>
                </button>
            @endif
        </div>
        @endunless
        
        <div class="font-sans text-gray-900 antialiased" >
            {{ $slot }}
        </div>

        @livewireScripts

        @if($showThemeToggle && ! request()->routeIs('register', 'public.station.feed'))
        <script>
            // Función para alternar modo oscuro
            function toggleDarkMode() {
                const body = document.body;
                const toggle = document.querySelector('.dark-mode-toggle i');
                
                if (body.hasAttribute('data-theme')) {
                    body.removeAttribute('data-theme');
                    localStorage.setItem('darkMode', 'disabled');
                    toggle.classList.remove('fa-sun');
                    toggle.classList.add('fa-moon');
                } else {
                    body.setAttribute('data-theme', 'dark');
                    localStorage.setItem('darkMode', 'enabled');
                    toggle.classList.remove('fa-moon');
                    toggle.classList.add('fa-sun');
                }
            }

            // Cargar preferencia guardada
            document.addEventListener("DOMContentLoaded", function () {
                const darkMode = localStorage.getItem('darkMode');
                const toggle = document.querySelector('.dark-mode-toggle i');
                
                if (darkMode === 'enabled') {
                    document.body.setAttribute('data-theme', 'dark');
                    if (toggle) {
                        toggle.classList.remove('fa-moon');
                        toggle.classList.add('fa-sun');
                    }
                }
            });
        </script>
        @endif
    </body>
</html>
