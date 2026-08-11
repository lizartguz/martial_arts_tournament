<?php

namespace App\Http\Controllers;

use App\Services\FcmTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FirebaseWebPushController extends Controller
{
    /**
     * Inyecta las dependencias requeridas por la clase.
     */
    public function __construct(
        private readonly FcmTokenService $fcmTokenService,
    ) {}

    /**
     * Entrega el service worker de Firebase con la configuración vigente.
     */
    public function serviceWorker(): Response
    {
        return response()
            ->view('firebase.messaging-sw')
            ->header('Content-Type', 'application/javascript; charset=UTF-8')
            ->header('Service-Worker-Allowed', '/')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Registra o actualiza el token web push del usuario.
     */
    public function storeToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
            // Se aceptan clientes antiguos que todavía envían el User-Agent completo.
            'browser' => ['nullable', 'string', 'max:1024'],
            'device_name' => ['nullable', 'string', 'max:1024'],
            'device_identifier' => ['nullable', 'string', 'max:255'],
        ]);

        $token = $this->fcmTokenService->registerWebTokenForUser(
            $request->user(),
            $validated['token'],
            [
                'browser' => $validated['browser'] ?? Str::limit((string) $request->userAgent(), 120, ''),
                'device_name' => $validated['device_name'] ?? 'Navegador web',
                'device_identifier' => $validated['device_identifier'] ?? null,
            ]
        );

        Log::info('Token Firebase Web Push registrado.', [
            'token_id' => $token->id,
            'user_id' => $request->user()->id,
            'device_identifier' => $token->device_identifier,
            'browser' => $token->browser,
        ]);

        return response()->json([
            'success' => true,
            'token_id' => $token->id,
        ]);
    }

    /**
     * Consulta si el token web push sigue activo para el usuario.
     */
    public function tokenStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
        ]);

        return response()->json([
            'success' => true,
            'registered' => $this->fcmTokenService->isActiveWebTokenForUser(
                $request->user(),
                $validated['token']
            ),
        ]);
    }

    /**
     * Desactiva el token web push enviado por el usuario.
     */
    public function destroyToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
        ]);

        $this->fcmTokenService->deactivateTokenByValue(
            $validated['token'],
            'Token web desregistrado desde el navegador'
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
