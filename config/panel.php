<?php

return [
    'brand' => [
        'name' => env('APP_NAME', 'Combate Real'),
        'logo' => 'images/mma/brand/combate-real-logo.svg',
        'favicon' => 'images/mma/brand/combate-real-favicon.png',
        'home_url' => '/admin/dashboard',
    ],

    'menu' => [
        [
            'text' => 'mma.menu.dashboard',
            'url' => '/admin/dashboard',
            'active' => ['admin/dashboard'],
            'icon' => 'fas fa-tachometer-alt',
            'can' => ['dashboard.view'],
        ],
        [
            'text' => 'mma.menu.events.group',
            'icon' => 'fas fa-calendar-alt',
            'can' => ['events.view', 'venues.view', 'fights.view', 'event_media.view'],
            'submenu' => [
                ['text' => 'mma.menu.events.events', 'url' => '/admin/events', 'active' => ['admin/events*'], 'icon' => 'fas fa-calendar', 'can' => ['events.view']],
                ['text' => 'mma.menu.events.venues', 'url' => '/admin/venues', 'active' => ['admin/venues*'], 'icon' => 'fas fa-map-marker-alt', 'can' => ['venues.view']],
                ['text' => 'mma.menu.events.fights', 'url' => '/admin/fights', 'active' => ['admin/fights*'], 'icon' => 'fas fa-fist-raised', 'can' => ['fights.view']],
                ['text' => 'mma.menu.events.results', 'url' => '/admin/fight-results', 'active' => ['admin/fight-results*'], 'icon' => 'fas fa-trophy', 'can' => ['fight_results.view']],
                ['text' => 'mma.menu.events.media', 'url' => '/admin/event-media', 'active' => ['admin/event-media*'], 'icon' => 'fas fa-images', 'can' => ['event_media.view']],
            ],
        ],
        [
            'text' => 'mma.menu.fighters.group',
            'icon' => 'fas fa-user-ninja',
            'can' => ['fighters.view', 'fighter_media.view', 'fighter_teams.view', 'weight_classes.view', 'rankings.view'],
            'submenu' => [
                ['text' => 'mma.menu.fighters.fighters', 'url' => '/admin/fighters', 'active' => ['admin/fighters*'], 'icon' => 'fas fa-users', 'can' => ['fighters.view']],
                ['text' => 'mma.menu.fighters.media', 'url' => '/admin/fighter-media', 'active' => ['admin/fighter-media*'], 'icon' => 'fas fa-photo-video', 'can' => ['fighter_media.view']],
                ['text' => 'mma.menu.fighters.teams', 'url' => '/admin/fighter-teams', 'active' => ['admin/fighter-teams*'], 'icon' => 'fas fa-dumbbell', 'can' => ['fighter_teams.view']],
                ['text' => 'mma.menu.fighters.weight_classes', 'url' => '/admin/weight-classes', 'active' => ['admin/weight-classes*'], 'icon' => 'fas fa-balance-scale', 'can' => ['weight_classes.view']],
                ['text' => 'mma.menu.fighters.rankings', 'url' => '/admin/rankings', 'active' => ['admin/rankings*'], 'icon' => 'fas fa-sort-numeric-up', 'can' => ['rankings.view']],
            ],
        ],
        [
            'text' => 'mma.menu.content.group',
            'icon' => 'fas fa-newspaper',
            'can' => ['news.view', 'landing.update', 'sponsors.view'],
            'submenu' => [
                ['text' => 'mma.menu.content.news', 'url' => '/admin/news', 'active' => ['admin/news*'], 'icon' => 'fas fa-newspaper', 'can' => ['news.view']],
                ['text' => 'mma.menu.content.landing', 'url' => '/admin/landing', 'active' => ['admin/landing*'], 'icon' => 'fas fa-window-maximize', 'can' => ['landing.update']],
                ['text' => 'mma.menu.content.sponsors', 'url' => '/admin/sponsors', 'active' => ['admin/sponsors*'], 'icon' => 'fas fa-handshake', 'can' => ['sponsors.view']],
            ],
        ],
        [
            'text' => 'mma.menu.commerce.group',
            'icon' => 'fas fa-credit-card',
            'can' => ['subscription_plans.view', 'subscribers.view', 'subscription_payments.view', 'purchase_requests.view'],
            'submenu' => [
                ['text' => 'mma.menu.commerce.plans', 'url' => '/admin/subscription-plans', 'active' => ['admin/subscription-plans*'], 'icon' => 'fas fa-tags', 'can' => ['subscription_plans.view']],
                ['text' => 'mma.menu.commerce.subscribers', 'url' => '/admin/subscribers', 'active' => ['admin/subscribers*'], 'icon' => 'fas fa-user-check', 'can' => ['subscribers.view']],
                ['text' => 'mma.menu.commerce.subscriptions', 'url' => '/admin/user-subscriptions', 'active' => ['admin/user-subscriptions*'], 'icon' => 'fas fa-id-card', 'can' => ['user_subscriptions.view']],
                ['text' => 'mma.menu.commerce.payments', 'url' => '/admin/subscription-payments', 'active' => ['admin/subscription-payments*'], 'icon' => 'fas fa-receipt', 'can' => ['subscription_payments.view']],
                ['text' => 'mma.menu.commerce.purchase_requests', 'url' => '/admin/purchase-requests', 'active' => ['admin/purchase-requests*'], 'icon' => 'fas fa-inbox', 'can' => ['purchase_requests.view']],
            ],
        ],
        [
            'text' => 'mma.menu.tickets.group',
            'icon' => 'fas fa-ticket-alt',
            'can' => ['ticket_links.view'],
            'submenu' => [
                ['text' => 'mma.menu.tickets.links', 'url' => '/admin/ticket-links', 'active' => ['admin/ticket-links*'], 'icon' => 'fas fa-link', 'can' => ['ticket_links.view']],
            ],
        ],
        [
            'text' => 'mma.menu.reports.group',
            'icon' => 'fas fa-chart-line',
            'can' => ['reports.events.view', 'reports.subscriptions.view', 'reports.sales.view'],
            'submenu' => [
                ['text' => 'mma.menu.reports.events', 'url' => '/admin/reports?type=events', 'active' => ['admin/reports*'], 'icon' => 'fas fa-calendar-check', 'can' => ['reports.events.view']],
                ['text' => 'mma.menu.reports.subscriptions', 'url' => '/admin/reports?type=subscriptions', 'active' => ['admin/reports*'], 'icon' => 'fas fa-id-card-alt', 'can' => ['reports.subscriptions.view']],
                ['text' => 'mma.menu.reports.sales', 'url' => '/admin/reports?type=sales', 'active' => ['admin/reports*'], 'icon' => 'fas fa-coins', 'can' => ['reports.sales.view']],
            ],
        ],
        [
            'text' => 'mma.menu.security.group',
            'icon' => 'fas fa-user-shield',
            'can' => ['users.view', 'authorization.access'],
            'submenu' => [
                ['text' => 'mma.menu.security.users', 'url' => '/admin/users', 'active' => ['admin/users*'], 'icon' => 'fas fa-users-cog', 'can' => ['users.view']],
                ['text' => 'mma.menu.security.roles', 'url' => '/admin/authorization', 'active' => ['admin/authorization*'], 'icon' => 'fas fa-lock', 'can' => ['authorization.access']],
            ],
        ],
        [
            'text' => 'mma.menu.settings.group',
            'icon' => 'fas fa-cog',
            'can' => ['system_settings.view', 'notifications.view', 'logs.view'],
            'submenu' => [
                ['text' => 'mma.menu.settings.system', 'url' => '/admin/settings', 'active' => ['admin/settings*'], 'icon' => 'fas fa-sliders-h', 'can' => ['system_settings.view']],
                ['text' => 'mma.menu.settings.notifications', 'url' => '/admin/notifications', 'active' => ['admin/notifications*'], 'icon' => 'fas fa-bell', 'can' => ['notifications.view']],
                ['text' => 'mma.menu.settings.logs', 'url' => '/admin/logs', 'active' => ['admin/logs*'], 'icon' => 'fas fa-file-alt', 'can' => ['logs.view']],
            ],
        ],
    ],
];
