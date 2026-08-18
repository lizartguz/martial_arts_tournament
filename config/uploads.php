<?php

return [
    'payment_proofs' => [
        'disk' => env('PAYMENT_PROOFS_DISK', 'local'),
        'directory' => 'payment-proofs',
        'max_mb' => 5,
        'image_mimes' => ['image/jpeg', 'image/png'],
        'document_mimes' => ['application/pdf'],
        'image_extensions' => ['jpg', 'jpeg', 'png'],
        'document_extensions' => ['pdf'],
    ],

    'public_images' => [
        'disk' => 'local',
        'directory' => 'mma',
        'max_mb' => 5,
        'image_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'image_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    ],
];
