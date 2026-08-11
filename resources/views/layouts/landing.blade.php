@php
    $settings = $settings ?? app(\App\Services\SystemSettingsService::class);
    $navItems = [
        ['route' => 'landing.home', 'label' => __('mma.landing.nav.home'), 'anchor' => null],
        ['route' => 'landing.fighters.index', 'label' => __('mma.landing.nav.fighters'), 'anchor' => null],
        ['route' => 'landing.news.index', 'label' => __('mma.landing.nav.news'), 'anchor' => null],
        ['route' => 'landing.subscription', 'label' => __('mma.landing.nav.subscription'), 'anchor' => null],
        ['route' => 'landing.contact', 'label' => __('mma.landing.nav.contact'), 'anchor' => null],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title')) ?: $settings->seoTitle() }}</title>
    @if ($settings->seoDescription())
        <meta name="description" content="{{ $settings->seoDescription() }}">
    @endif
    <link rel="icon" type="image/png" href="{{ $settings->faviconUrl() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
</head>
<body class="flex min-h-screen flex-col bg-gray-950 text-white">
    <header class="border-b border-white/10 bg-gray-950/95 sticky top-0 z-30 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4">
            <a href="{{ route('landing.home') }}" class="flex items-center gap-3">
                <img src="{{ $settings->logoUrl() }}" alt="{{ $settings->productName() }}" class="h-10 w-10 rounded-full object-cover">
                <span class="text-sm font-semibold">{{ $settings->productName() }}</span>
            </a>

            <nav class="order-3 flex w-full flex-wrap gap-1 text-sm lg:order-2 lg:w-auto lg:gap-2">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                        class="rounded px-3 py-1.5 font-medium transition {{ request()->routeIs($item['route']) ? 'bg-emerald-500 text-gray-950' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="order-2 flex items-center gap-2 lg:order-3">
                <x-locale-switcher />
                <a href="{{ route('login') }}" class="rounded border border-white/20 px-3 py-1.5 text-sm hover:bg-white hover:text-gray-950">
                    {{ __('mma.landing.login') }}
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-white/10 bg-gray-950">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ $settings->logoUrl() }}" alt="{{ $settings->productName() }}" class="h-9 w-9 rounded-full object-cover">
                    <span class="text-sm font-semibold">{{ $settings->productName() }}</span>
                </div>
                @if ($settings->shortDescription())
                    <p class="mt-3 text-sm text-gray-400">{{ $settings->shortDescription() }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-300">{{ __('mma.landing.footer.quick_links') }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    @foreach ($navItems as $item)
                        <li><a href="{{ route($item['route']) }}" class="hover:text-white">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-300">{{ __('mma.landing.footer.contact') }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    @if ($settings->whatsappPhone())
                        <li><a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsappPhone()) }}" target="_blank" rel="noopener" class="hover:text-white"><i class="fab fa-whatsapp mr-1"></i> WhatsApp</a></li>
                    @endif
                    @if ($settings->contactPhone())
                        <li><a href="tel:{{ $settings->contactPhone() }}" class="hover:text-white"><i class="fas fa-phone mr-1"></i> {{ $settings->contactPhone() }}</a></li>
                    @endif
                    @if ($settings->contactEmail())
                        <li><a href="mailto:{{ $settings->contactEmail() }}" class="hover:text-white"><i class="fas fa-envelope mr-1"></i> {{ $settings->contactEmail() }}</a></li>
                    @endif
                </ul>
            </div>

            @if (!empty($settings->socialLinks()))
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-300">{{ __('mma.landing.footer.follow_us') }}</h3>
                    <div class="mt-3 flex gap-3 text-lg text-gray-400">
                        @foreach ($settings->socialLinks() as $network => $url)
                            @if (filled($url))
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="hover:text-white">
                                    <i class="fab fa-{{ strtolower($network) }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-white/10 px-4 py-4 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ $settings->productName() }}. {{ __('mma.landing.footer.rights') }}
        </div>
    </footer>
</body>
</html>
