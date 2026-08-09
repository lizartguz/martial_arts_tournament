<?php

return [
    'brand' => [
        'name' => 'SENVATEC',
        'logo' => 'images/logo_circle.png',
        'favicon' => 'frontend/images/logo_with_text.png',
        'home_url' => '/dashboard',
    ],

    'menu' => [
        [
            'text' => 'Dashboard',
            'url' => '/dashboard',
            'active' => ['dashboard'],
            'icon' => 'fas fa-tachometer-alt',
        ],
        [
            'text' => 'notifications',
            'url' => '/notificationsp',
            'active' => ['notificationsp', 'notificationsp/*'],
            'icon' => 'fa fa-bell',
            'can' => ['notifications.view'],
        ],
        [
            'text' => 'subscriptions',
            'url' => '/subscription',
            'active' => ['subscription', 'subscription/*'],
            'icon' => 'fa fa-envelope',
            'can' => ['subscriptions.view'],
        ],
        [
            'text' => 'sales_and_subscribers',
            'url' => '/userSubscription',
            'active' => ['userSubscription', 'userSubscription/*'],
            'icon' => 'fa fa-chart-line',
            'can' => ['sales_subscribers.view'],
        ],
        [
            'text' => 'user_management',
            'icon' => 'fas fa-users',
            'can' => ['user_management.view', 'pay_renewals.view'],
            'submenu' => [
                [
                    'text' => 'Datos de usuario',
                    'url' => '/userManagement',
                    'active' => ['userManagement', 'userManagement/*'],
                    'icon' => 'fas fa-user-cog',
                    'can' => ['user_management.view'],
                ],
                [
                    'text' => 'Pagos y renovaciones',
                    'url' => '/pay-renew-c',
                    'active' => ['pay-renew-c', 'pay-renew-c/*'],
                    'icon' => 'fas fa-credit-card',
                    'can' => ['pay_renewals.view'],
                ],
            ],
        ],
        [
            'text' => 'stations.label',
            'icon' => 'fa fa-cloud-sun',
            'can' => ['stations.table.view', 'stations.map.view', 'stations.charts.view', 'stations.downloads.view'],
            'submenu' => [
                [
                    'text' => 'stations.table',
                    'url' => '/station',
                    'active' => ['station', 'station/*'],
                    'icon' => 'fa fa-map-marked-alt',
                    'can' => ['stations.table.view'],
                ],
                [
                    'text' => 'stations.map',
                    'url' => '/station-map',
                    'active' => ['station-map', 'station-map/*'],
                    'icon' => 'fa fa-map',
                    'can' => ['stations.map.view'],
                ],
                [
                    'text' => 'stations.charts',
                    'url' => '/graphs',
                    'active' => ['graphs', 'graphs/*'],
                    'icon' => 'fa fa-chart-bar',
                    'can' => ['stations.charts.view'],
                ],
                [
                    'text' => 'stations.downloads',
                    'url' => '/downloads',
                    'active' => ['downloads', 'downloads/*'],
                    'icon' => 'fa fa-download',
                    'can' => ['stations.downloads.view'],
                ],
            ],
        ],
        [
            'text' => 'agro.label',
            'icon' => 'fa fa-tractor',
            'can' => ['agro.chill_hours.view', 'agro.gdd.view', 'agro.early_warning.view'],
            'submenu' => [
                [
                    'text' => 'agro.chill_hours',
                    'url' => '/chill-hours',
                    'active' => ['chill-hours', 'chill-hours/*'],
                    'icon' => 'fa fa-snowflake',
                    'can' => ['agro.chill_hours.view'],
                ],
                [
                    'text' => 'agro.gdd',
                    'url' => '/gdd',
                    'active' => ['gdd', 'gdd/*'],
                    'icon' => 'fa fa-seedling',
                    'can' => ['agro.gdd.view'],
                ],
                [
                    'text' => 'agro.early_warning',
                    'url' => '/early-warning',
                    'active' => ['early-warning', 'early-warning/*'],
                    'icon' => 'fa fa-exclamation-triangle',
                    'can' => ['agro.early_warning.view'],
                ],
            ],
        ],
        [
            'text' => 'support',
            'icon' => 'fa fa-headset',
            'url' => '/support',
            'active' => ['support', 'support/*'],
        ],
        [
            'text' => 'data_management.label',
            'icon' => 'fa fa-database',
            'can' => ['data.import_global.view', 'data.organize_users.view'],
            'submenu' => [
                [
                    'text' => 'Import Data Global',
                    'url' => '/importdataglobal',
                    'active' => ['importdataglobal', 'importdataglobal/*'],
                    'can' => ['data.import_global.view'],
                ],
                [
                    'text' => 'Organize Users',
                    'url' => '/organizeuser',
                    'active' => ['organizeuser', 'organizeuser/*'],
                    'can' => ['data.organize_users.view'],
                ],
            ],
        ],
        [
            'text' => 'administration.label',
            'icon' => 'fa fa-cog',
            'can' => ['authorization.access', 'administration.forecasts_edit.view', 'administration.logs.view', 'administration.catalog.view'],
            'submenu' => [
                [
                    'text' => 'administration.authorization',
                    'url' => '/authorization',
                    'active' => ['authorization', 'authorization/*'],
                    'icon' => 'fa fa-user-shield',
                    'can' => ['authorization.access'],
                ],
                [
                    'text' => 'administration.forecasts_edit',
                    'url' => '/forecasts-edit',
                    'active' => ['forecasts-edit', 'forecasts-edit/*'],
                    'can' => ['administration.forecasts_edit.view'],
                ],
                [
                    'text' => 'administration.logs',
                    'url' => '/logs',
                    'active' => ['logs', 'logs/*'],
                    'icon' => 'fa fa-file-alt',
                    'can' => ['administration.logs.view'],
                ],
                [
                    'text' => 'administration.catalog',
                    'url' => '/catalog',
                    'active' => ['catalog', 'catalog/*'],
                    'icon' => 'fa fa-tags',
                    'can' => ['administration.catalog.view'],
                ],
            ],
        ],
    ],
];
