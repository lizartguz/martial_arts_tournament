@php
    use App\Support\AdminPanel;

    $pageTitle    = trim($__env->yieldContent('title'));
    $rawHeaderContent = $__env->yieldContent('content_header');
    $headerContent = trim(preg_replace('/<!--.*?-->/s', '', $rawHeaderContent));
    $footerContent = trim($__env->yieldContent('footer'));
    $menuItems    = AdminPanel::menu();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated-user-id" content="{{ auth()->id() }}">
    @yield('meta_tags')

    {{-- Aplica el tema antes del primer pintado para evitar flash --}}
    <script>
        window.AppAlertDefaults = {
            confirmButtonText: @js(__('messages.actions.close')),
        };

        /**
         * Inicializa este comportamiento inmediato del navegador.
         */
        (function () {
            try {
                var s = localStorage.getItem('darkMode');
                var t = s === 'enabled' ? 'dark'
                      : s === 'disabled' ? 'light'
                      : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    {{-- Fija el estado del menú lateral antes del primer pintado (sin parpadeo) y lo
         conserva entre vistas vía localStorage, igual que el tema oscuro. --}}
    <script>
        /**
         * Inicializa este comportamiento inmediato del navegador.
         */
        (function () {
            try {
                var isDesktop = window.matchMedia('(min-width: 1024px)').matches;
                var saved = localStorage.getItem('sidebar'); // 'open' | 'closed' | null
                // Escritorio: abierto salvo que el usuario lo haya cerrado.
                // Móvil: siempre cerrado al cargar (el sidebar es un overlay).
                var open = isDesktop ? (saved !== 'closed') : false;
                document.documentElement.setAttribute('data-sidebar', open ? 'open' : 'closed');
            } catch (e) {
                document.documentElement.setAttribute('data-sidebar', 'open');
            }
        })();
    </script>

    <title>{{ AdminPanel::pageTitle($pageTitle !== '' ? $pageTitle : null) }}</title>

    <link rel="icon"           type="image/png" href="{{ AdminPanel::favicon() }}?v=20260510">
    <link rel="shortcut icon"  type="image/png" href="{{ AdminPanel::favicon() }}?v=20260510">
    <link rel="apple-touch-icon"                href="{{ AdminPanel::favicon() }}?v=20260510">

    <link rel="preload" href="{{ asset('vendor/fontawesome-free/webfonts/fa-solid-900.woff2') }}"  as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('vendor/fontawesome-free/webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('vendor/fontawesome-free/webfonts/fa-brands-400.woff2') }}"  as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/theme/themes.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @yield('css')
    @stack('css')

    <style>
        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal; font-weight: 400; font-display: swap;
            src: url("{{ asset('vendor/fontawesome-free/webfonts/fa-regular-400.woff2') }}") format("woff2");
        }
        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal; font-weight: 900; font-display: swap;
            src: url("{{ asset('vendor/fontawesome-free/webfonts/fa-solid-900.woff2') }}") format("woff2");
        }
        @font-face {
            font-family: "Font Awesome 5 Brands";
            font-style: normal; font-weight: 400; font-display: swap;
            src: url("{{ asset('vendor/fontawesome-free/webfonts/fa-brands-400.woff2') }}") format("woff2");
        }

        /* Gradiente de fondo del área de contenido */
        .admin-content-bg {
            background:
                radial-gradient(circle at top right, rgba(16,185,129,0.08), transparent 28%),
                linear-gradient(180deg, rgba(240,253,244,0.75) 0%, rgba(255,255,255,0.96) 16%, rgba(255,255,255,1) 100%);
        }
        [data-theme="dark"] .admin-content-bg {
            background:
                radial-gradient(circle at top right, rgba(16,185,129,0.08), transparent 24%),
                linear-gradient(180deg, rgba(13,17,23,0.98) 0%, rgba(22,27,34,0.98) 18%, rgba(26,26,26,0.98) 100%);
        }

        :root {
            --admin-topbar-height: 3.5rem;
            --admin-sidebar-width: 16rem;
            --admin-content-gap: 1rem;
        }

        /* Topbar global: siempre fija al borde superior del viewport. */
        .admin-topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--admin-topbar-height);
            z-index: 30;
            transition: left .3s ease-in-out;
        }

        /* Sidebar: fijo a la izquierda; fuera de pantalla por defecto. */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--admin-sidebar-width);
            z-index: 50;
            transform: translateX(-100%);
            transition: transform .3s ease-in-out;
        }

        /* Contenedor de contenido: reserva el alto de la topbar. Con box-sizing:border-box
           (preflight de Tailwind) min-h-screen ya incluye este padding, sin overflow. */
        .admin-shell {
            padding-top: var(--admin-topbar-height);
            transition: padding-left .3s ease-in-out;
        }

        .admin-page-main {
            padding: var(--admin-content-gap) 1rem 1rem;
        }

        @media (min-width: 768px) {
            .admin-page-main {
                padding: var(--admin-content-gap) 1.5rem 1.5rem;
            }
        }

        /* Estado abierto: el sidebar entra en pantalla. */
        [data-sidebar="open"] .admin-sidebar {
            transform: translateX(0);
        }

        /* Solo en escritorio y con el sidebar abierto, la topbar y el contenido se
           desplazan para empezar después del ancho del sidebar. */
        @media (min-width: 1024px) {
            [data-sidebar="open"] .admin-topbar {
                left: var(--admin-sidebar-width);
            }
            [data-sidebar="open"] .admin-shell {
                padding-left: var(--admin-sidebar-width);
            }
        }
    </style>
</head>
<body class="min-h-screen admin-content-bg" x-data="adminLayout()">

    {{-- Aplica data-theme al body antes de que Alpine arranque --}}
    <script>
        /**
         * Inicializa este comportamiento inmediato del navegador.
         */
        (function () {
            var t = document.documentElement.getAttribute('data-theme');
            if (t === 'dark') document.body.setAttribute('data-theme', 'dark');
        })();
    </script>

    {{-- ── Overlay móvil ────────────────────────────────────────────── --}}
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- ── Sidebar ───────────────────────────────────────────────────── --}}
    <aside class="admin-sidebar flex flex-col text-white bg-sidebar-bg">

        {{-- Brand --}}
        <a href="{{ AdminPanel::homeUrl() }}"
           class="flex items-center gap-3 px-4 py-4 bg-sidebar-brand border-b border-black/20 flex-shrink-0">
            <img src="{{ AdminPanel::brandLogo() }}"
                 alt="{{ AdminPanel::brandName() }}"
                 class="h-10 w-auto max-w-[150px] object-contain flex-shrink-0">
            <span class="truncate text-sm font-semibold text-sidebar-text">
                {{ AdminPanel::brandName() }}
            </span>
        </a>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
            @foreach($menuItems as $item)
                @include('layouts.partials.admin-sidebar-item', ['item' => $item])
            @endforeach
        </nav>
    </aside>

    {{-- ── Topbar global (hija directa del body: ninguna vista puede empujarla) ── --}}
    <header class="admin-topbar border-b border-gray-200 bg-white pl-2 pr-3 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-full min-w-0 items-center gap-2">
                <div class="flex min-w-0 items-center gap-2">
                    {{-- Toggle sidebar --}}
                    <button @click="toggleSidebar()"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-gray-500 transition-colors
                                   hover:bg-gray-100 hover:text-gray-700
                                   dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                            aria-label="Toggle sidebar">
                        <i class="fas fa-bars text-sm"></i>
                    </button>

                    {{-- Nombre de la app solo en móvil/tablet; en escritorio ya lo aporta el sidebar --}}
                    <a href="{{ AdminPanel::homeUrl() }}"
                       class="min-w-0 truncate text-sm font-medium text-gray-700 transition-colors
                              hover:text-emerald-600 lg:hidden
                              dark:text-gray-300 dark:hover:text-emerald-400">
                        {{ AdminPanel::brandName() }}
                    </a>
                </div>

                {{-- Acciones lado derecho --}}
                <div class="ml-auto flex shrink-0 items-center gap-0.5 whitespace-nowrap">

                    {{-- Selector de idioma --}}
                    <livewire:components.language-select triggerClass="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md" />

                    {{-- Notificaciones push --}}
                    <a id="web-push-toggle" href="#" aria-label="Activar notificaciones"
                       class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-gray-500 transition-colors
                              hover:bg-gray-100 hover:text-gray-700
                              dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                        <i class="fas fa-bell text-sm"></i>
                    </a>

                    {{-- Pantalla completa --}}
                    <button onclick="toggleFullscreen()"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-gray-500 transition-colors
                                   hover:bg-gray-100 hover:text-gray-700
                                   dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                            aria-label="Pantalla completa">
                        <i class="fas fa-expand-arrows-alt text-sm"></i>
                    </button>

                    {{-- Modo oscuro --}}
                    <button onclick="toggleDarkMode(); return false;"
                            class="dark-mode-toggle inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-gray-500 transition-colors
                                   hover:bg-gray-100 hover:text-gray-700
                                   dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                            title="Modo Oscuro/Claro">
                        <i class="fas fa-moon text-sm"></i>
                    </button>

                    {{-- Menú de usuario --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative shrink-0">
                        <button @click="open = !open"
                                class="inline-flex h-10 items-center gap-2 rounded-md px-3 text-sm
                                       text-gray-700 transition-colors hover:bg-gray-100
                                       dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="fas fa-user-circle text-gray-400 dark:text-gray-500"></i>
                            <span class="hidden md:inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
                            <i class="fas fa-angle-down text-xs text-gray-400 transition-transform duration-200"
                               :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1 w-56 rounded-lg border border-gray-100
                                    bg-white py-1 shadow-lg z-50
                                    dark:border-gray-700 dark:bg-gray-800">

                            <div class="border-b border-gray-100 px-4 py-2 text-xs text-gray-400
                                        dark:border-gray-700 dark:text-gray-500">
                                {{ Auth::user()->email ?? '' }}
                            </div>

                            <a href="{{ route('profile.show') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700
                                      transition-colors hover:bg-gray-50
                                      dark:text-gray-300 dark:hover:bg-gray-700">
                                <i class="fas fa-user-cog w-4 text-center text-gray-400"></i>
                                {{ __('Profile') }}
                            </a>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <a href="{{ route('api-tokens.index') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700
                                          transition-colors hover:bg-gray-50
                                          dark:text-gray-300 dark:hover:bg-gray-700">
                                    <i class="fas fa-key w-4 text-center text-gray-400"></i>
                                    {{ __('API Tokens') }}
                                </a>
                            @endif

                            <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm
                                               text-red-600 transition-colors hover:bg-red-50
                                               dark:text-red-400 dark:hover:bg-red-900/20">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

    {{-- ── Contenedor principal: reserva el alto de la topbar y el ancho del sidebar ── --}}
    <div class="admin-shell flex min-h-screen flex-col">

        {{-- ── Contenido de la página ───────────────────────────────── --}}
        <main class="admin-page-main flex-1">

            {{-- Encabezado opcional del contenido; la topbar global siempre se renderiza arriba. --}}
            @if($headerContent !== '')
                <div class="mb-4">
                    {!! $rawHeaderContent !!}
                </div>
            @endif

            {{-- Flash: éxito --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-4 flex items-start gap-3 rounded-lg border border-green-200
                            bg-green-50 p-4 text-green-800
                            dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                    <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                    <span class="flex-1 text-sm">{{ session('success') }}</span>
                    <button @click="show = false"
                            class="text-green-600 hover:text-green-800 dark:text-green-400">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- Flash: error --}}
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-4 flex items-start gap-3 rounded-lg border border-red-200
                            bg-red-50 p-4 text-red-800
                            dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                    <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                    <span class="flex-1 text-sm">{{ session('error') }}</span>
                    <button @click="show = false"
                            class="text-red-600 hover:text-red-800 dark:text-red-400">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- Panel de contenido --}}
            <div class="rounded-2xl border border-emerald-900/[0.08] bg-white/80 p-5 shadow-sm
                        dark:border-white/[0.08] dark:bg-gray-800/90 dark:shadow-gray-900/40">
                @yield('content')
            </div>
        </main>

        {{-- ── Footer ───────────────────────────────────────────────── --}}
        <footer class="border-t border-gray-200 px-6 py-3 text-center text-xs text-gray-500
                        dark:border-gray-700 dark:text-gray-400">
            @if($footerContent !== '')
                {!! $__env->yieldContent('footer') !!}
            @else
                {{ AdminPanel::brandName() }} {{ date('Y') }}
            @endif
        </footer>
    </div>

    {{-- ── Scripts ──────────────────────────────────────────────────── --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    <script>
        if (window.jQuery && window.$ !== window.jQuery) {
            window.$ = window.jQuery;
        }

        /**
         * Alterna la vista de pantalla completa del panel.
         */
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen();
            }
        }

        /**
         * Define el estado interactivo del layout administrativo.
         */
        function adminLayout() {
            return {
                // Estado inicial = el que ya fijó el script del <head> antes de pintar (sin salto).
                sidebarOpen: document.documentElement.getAttribute('data-sidebar') === 'open',

                init() {
                    // Refleja el estado al atributo del <html>; el CSS hace el resto.
                    this.$watch('sidebarOpen', (value) => {
                        document.documentElement.setAttribute('data-sidebar', value ? 'open' : 'closed');
                    });

                    // Al cruzar el breakpoint, ajusta sin pisar la preferencia guardada.
                    window.addEventListener('resize', () => {
                        if (window.matchMedia('(min-width: 1024px)').matches) {
                            this.sidebarOpen = localStorage.getItem('sidebar') !== 'closed';
                        } else {
                            this.sidebarOpen = false;
                        }
                    });
                },

                // Acción del botón: alterna y conserva la preferencia (solo en escritorio).
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    if (window.matchMedia('(min-width: 1024px)').matches) {
                        try {
                            localStorage.setItem('sidebar', this.sidebarOpen ? 'open' : 'closed');
                        } catch (e) {}
                    }
                },
            };
        }

        /**
         * Posiciona los menus de acciones fuera del scroll horizontal de las tablas.
         */
        function tableActionMenu() {
            return {
                open: false,
                style: '',

                toggle(trigger) {
                    if (this.open) {
                        this.close();
                        return;
                    }

                    this.openAt(trigger);
                },

                openAt(trigger) {
                    if (!trigger) {
                        return;
                    }

                    const rect = trigger.getBoundingClientRect();
                    const width = Number(trigger.dataset.menuWidth || 192);
                    const gap = 6;
                    const estimatedHeight = 220;
                    const left = Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8));
                    const preferredTop = rect.bottom + gap;
                    const top = preferredTop + estimatedHeight > window.innerHeight - 8
                        ? Math.max(8, rect.top - estimatedHeight - gap)
                        : preferredTop;

                    this.style = `left: ${left}px; top: ${top}px; width: ${width}px;`;
                    this.open = true;
                },

                close() {
                    this.open = false;
                },
            };
        }

    </script>

    @livewireScripts
    @vite('resources/js/analytics.js')
    @vite('resources/js/push-notifications.js')
    @yield('js')
    @stack('js')
</body>
</html>
