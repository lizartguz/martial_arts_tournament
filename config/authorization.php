<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role hierarchy
    |--------------------------------------------------------------------------
    |
    | A higher rank can manage users and roles with the same or lower rank.
    | Super administrator roles are hidden from every non-super user.
    |
    */
    'role_ranks' => [
        'super_manager' => 100,
        'admin' => 80,
        'manager' => 60,
        'meteorology' => 60,
        'meteorologist' => 60,
        'technical' => 50,
        'sales' => 40,
        'marketing' => 40,
        'subscriber' => 10,
    ],

    'super_roles' => [
        'super_manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles que ven el detalle tecnico de errores
    |--------------------------------------------------------------------------
    |
    | Estos roles ven el mensaje crudo de las excepciones (util para depurar).
    | Cualquier otro rol ve un mensaje amigable y generico. Es el unico lugar
    | a ajustar para cambiar quien ve los errores tecnicos en el panel.
    | Usado por App\Support\ErrorMessage.
    |
    */
    'error_detail_roles' => [
        'super_manager',
    ],

    'protected_permissions' => [
        'authorization.access',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',

        'notifications.view',
        'subscriptions.view',
        'sales_subscribers.view',

        'user_management.view',
        'pay_renewals.view',

        'stations.table.view',
        'stations.map.view',
        'stations.charts.view',
        'stations.downloads.view',

        'agro.chill_hours.view',
        'agro.gdd.view',
        'agro.early_warning.view',

        'data.import_global.view',
        'data.organize_users.view',

        'administration.forecasts_edit.view',
        'administration.logs.view',
        'administration.catalog.view',
    ],
];
