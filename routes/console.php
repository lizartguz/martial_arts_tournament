<?php

use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Ejecuta la logica del comando de consola.
 */
Artisan::command('app:notification-dispatch-push', function (PushNotificationService $pushNotificationService) {
    $result = $pushNotificationService->dispatchPendingNotifications();

    Log::info('Resumen de envio de avisos push.', $result);
    $this->info('Avisos procesados: ' . ($result['processed'] ?? 0));
})->everyFiveMinutes()->timezone('America/La_Paz');
