<?php

return [
    // Tiempo de validez de las URLs firmadas entregadas a la aplicacion movil.
    'image_url_ttl_minutes' => (int) env('NOTIFICATION_IMAGE_URL_TTL', 15),
];
