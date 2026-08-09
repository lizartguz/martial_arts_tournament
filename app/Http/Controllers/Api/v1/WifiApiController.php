<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\StationM;
use Illuminate\Http\Request;

/**
 * Controlador para la sección WiFi de la app móvil.
 * Lista de estaciones para selector y lógica futura de WiFi.
 */
class WifiApiController extends Controller
{
    /**
     * Lista de estaciones activas para el selector de WiFi (id, name, code).
     * Requiere permisos de usuario (userId, email, phone, identifier).
     */
    public function getStationsForWifi(Request $request)
    {
        try {
            $userPermission = UserPermissionVerif::verif(
                $request->userId,
                $request->email,
                $request->phone,
                $request->identifier
            );

            if ($userPermission[0] !== 'yes') {
                return response()->json([
                    'response' => false,
                    'data' => [],
                    'failed' => 'Usuario sin permisos',
                    'data_user' => $userPermission[3] ?? null,
                    'user_permission' => $userPermission[0],
                    'permit_detail' => $userPermission[1] ?? '',
                    'type_subscriptions' => $userPermission[2] ?? null,
                ], 403);
            }

            $stations = StationM::where('state', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'code']);

            return response()->json([
                'response' => true,
                'data' => $stations,
                'failed' => null,
                'data_user' => $userPermission[3] ?? null,
                'user_permission' => $userPermission[0],
                'permit_detail' => $userPermission[1] ?? '',
                'type_subscriptions' => $userPermission[2] ?? null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => false,
                'data' => [],
                'failed' => $th->getMessage(),
                'data_user' => null,
                'user_permission' => 'no',
                'permit_detail' => '',
                'type_subscriptions' => null,
            ], 500);
        }
    }
}
