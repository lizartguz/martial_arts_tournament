@php
    use App\Services\SystemSettingsService;

    $settings = app(SystemSettingsService::class);
    $pageTitle = trim($__env->yieldContent('title'));
    $navItems = [
        ['route' => 'subscriber.dashboard', 'permission' => 'subscriber.dashboard.view', 'icon' => 'fas fa-home', 'label' => __('mma.subscriber_portal.menu.dashboard')],
        ['route' => 'subscriber.purchases', 'permission' => 'subscriber.purchases.view', 'icon' => 'fas fa-receipt', 'label' => __('mma.subscriber_portal.menu.purchases')],
        ['route' => 'subscriber.events', 'permission' => 'subscriber.events.view', 'icon' => 'fas fa-calendar-alt', 'label' => __('mma.subscriber_portal.menu.events')],
        ['route' => 'subscriber.subscription', 'permission' => 'subscriber.subscription.view', 'icon' => 'fas fa-id-card', 'label' => __('mma.subscriber_portal.menu.subscription')],
        ['route' => 'subscriber.profile', 'permission' => 'subscriber.profile.view', 'icon' => 'fas fa-user', 'label' => __('mma.subscriber_portal.menu.profile')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ? "{$pageTitle} | {$settings->productName()}" : $settings->productName() }}</title>
    <link rel="icon" type="image/png" href="{{ $settings->faviconUrl() }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-950 text-white">
    <header class="border-b border-white/10 bg-gray-950/95">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('subscriber.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <img src="{{ $settings->logoUrl() }}" alt="{{ $settings->productName() }}" class="h-10 w-10 rounded-full object-cover">
                    <span class="truncate text-sm font-semibold">{{ $settings->productName() }}</span>
                </a>
                <div class="lg:hidden">
                    <x-locale-switcher />
                </div>
            </div>

            <nav class="flex gap-2 overflow-x-auto pb-1 lg:pb-0">
                @foreach ($navItems as $item)
                    @can($item['permission'])
                        <a href="{{ route($item['route']) }}"
                            class="inline-flex shrink-0 items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition {{ request()->routeIs($item['route']) ? 'bg-emerald-500 text-gray-950' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endcan
                @endforeach
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                @csrf
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>

            <div class="hidden items-center gap-3 lg:flex">
                <x-locale-switcher />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm font-semibold text-gray-200 hover:bg-white hover:text-gray-950">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('Log Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-5">
            <h1 class="text-2xl font-semibold">{{ $pageTitle }}</h1>
            @hasSection('subtitle')
                <p class="mt-1 text-sm text-gray-400">@yield('subtitle')</p>
            @endif
        </div>

        @yield('content')
    </main>
</body>
</html>
