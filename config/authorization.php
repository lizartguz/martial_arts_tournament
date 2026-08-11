<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jerarquía de roles
    |--------------------------------------------------------------------------
    |
    | Un rol solo puede administrar usuarios o roles de rango inferior.
    | `super_manager` es la excepción y administra todo el sistema.
    |
    */
    'role_ranks' => [
        'super_manager' => 100,
        'admin' => 80,
        'manager' => 60,
        'publisher' => 50,
        'sales' => 40,
        'checkin' => 30,
        'support' => 20,
        'subscriber' => 10,
    ],

    'super_roles' => [
        'super_manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles que ven detalle técnico de errores
    |--------------------------------------------------------------------------
    */
    'error_detail_roles' => [
        'super_manager',
    ],

    'protected_permissions' => [
        'authorization.access',
        'system_settings.view',
        'system_settings.update',
        'roles.view',
        'roles.update',
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'users.assign_roles',
        'reports.export',
        'logs.view',
        'logs.download',
    ],

    'permission_labels' => [
        'authorization.access' => 'Acceder a roles y permisos',
        'system_settings.view' => 'Ver configuración del sistema',
        'system_settings.update' => 'Actualizar configuración del sistema',
        'events.view' => 'Ver eventos',
        'events.create' => 'Crear eventos',
        'events.update' => 'Editar eventos',
        'events.delete' => 'Eliminar eventos',
        'events.publish' => 'Publicar eventos',
        'subscription_payments.view' => 'Ver pagos de suscripción',
        'subscription_payments.upload_proof' => 'Cargar comprobante de pago',
        'subscription_payments.confirm' => 'Confirmar pagos de suscripción',
        'purchase_requests.view' => 'Ver solicitudes de compra',
        'subscriber.purchases.view' => 'Ver mis compras',
    ],
];
