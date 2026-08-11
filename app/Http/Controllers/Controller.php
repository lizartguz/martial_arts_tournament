<?php

namespace App\Http\Controllers;

use App\Services\FcmTokenService;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Sincroniza el token FCM móvil enviado por el cliente.
     */
    protected function syncMobileFcmToken(Request $request, int $userId): void
    {
        $token = trim((string) $request->input('fcmToken', ''));

        if ($token === '') {
            return;
        }

        app(FcmTokenService::class)->registerMobileTokenForUser(
            $userId,
            $token,
            [
                'device_identifier' => $request->input('deviceIdentifier')
                    ?: $request->input('device_identifier')
                    ?: $request->input('identifier'),
                'device_name' => $request->input('deviceName')
                    ?: $request->input('device_name')
                    ?: $request->input('versionApp')
                    ?: $request->input('version_app'),
            ]
        );
    }
}
