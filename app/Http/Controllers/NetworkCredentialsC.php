<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NetworkCredentialsC extends Controller
{
    /**
     * Obtiene las credenciales de red (SSID y Password) para una estación específica
     * 
     * Este endpoint es consumido por nodos de estación para obtener las credenciales
     * de la red Wi-Fi a la cual deben conectarse.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Ejemplo de petición:
     * GET /api/getNetwork?code=550e8400-e29b-41d4-a716-446655440000
     * 
     * Respuesta exitosa:
     * {
     *   "ssid": "nombre_red",
     *   "password": "contraseña_red"
     * }
     * 
     * Respuesta de error:
     * {
     *   "error": "Mensaje de error"
     * }
     */
    public function getNetworkCredentials(Request $request)
    {
        try {
            // Obtener el código de la estación desde los parámetros GET
            $stationCode = $request->input('code');

            // Validar que se proporcionó el código
            if (empty($stationCode)) {
                Log::warning('[NetworkCredentials] Petición sin código de estación');
                return response()->json([
                    'error' => 'Código de estación no proporcionado'
                ], 400);
            }

            Log::info("[NetworkCredentials] Buscando credenciales para estación: {$stationCode}");

            // Buscar la estación por su código UUID
            $station = DB::table('stations')
                ->where('code', $stationCode)
                ->where('state', 1) // Solo estaciones activas
                ->first();

            // Validar que la estación existe
            if (!$station) {
                Log::warning("[NetworkCredentials] Estación no encontrada o inactiva: {$stationCode}");
                return response()->json([
                    'error' => 'Estación no encontrada o inactiva'
                ], 404);
            }

            // Buscar las credenciales de red activas para esta estación
            $credentials = DB::table('network_credentials')
                ->where('station_id', $station->id)
                ->where('state', 1) // Solo credenciales activas
                ->orderBy('created_at', 'desc') // Obtener la más reciente
                ->first();

            // Validar que existen credenciales configuradas
            if (!$credentials) {
                Log::warning("[NetworkCredentials] No hay credenciales activas para estación ID: {$station->id}");
                return response()->json([
                    'error' => 'No hay credenciales de red configuradas para esta estación'
                ], 404);
            }

            // Validar que las credenciales tienen SSID y password
            if (empty($credentials->ssid) || empty($credentials->password)) {
                Log::error("[NetworkCredentials] Credenciales incompletas para estación ID: {$station->id}");
                return response()->json([
                    'error' => 'Credenciales de red incompletas'
                ], 500);
            }

            // Registrar el éxito
            Log::info("[NetworkCredentials] Credenciales entregadas exitosamente para estación: {$stationCode}");

            // --- VITAL: Desactivar la bandera sent_to_device para que el servidor 
            // deje de responder "senvatec_network" en el ciclo de datos
            DB::table('network_credentials')
                ->where('id', $credentials->id)
                ->update(['sent_to_device' => 0]);

            // Retornar las credenciales en formato JSON
            return response()->json([
                'ssid' => $credentials->ssid,
                'password' => $credentials->password
            ], 200);

        } catch (\Exception $e) {
            // Log del error
            Log::error("[NetworkCredentials] Error al obtener credenciales: " . $e->getMessage());
            Log::error("[NetworkCredentials] Stack trace: " . $e->getTraceAsString());

            // Retornar error genérico
            return response()->json([
                'error' => 'Error interno del servidor al obtener credenciales'
            ], 500);
        }
    }

    /**
     * Actualiza o crea credenciales de red para una estación
     * 
     * Este método permite administrar las credenciales de red desde el panel web.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNetworkCredentials(Request $request)
    {
        try {
            // Validar datos de entrada
            $request->validate([
                'station_id' => 'required|exists:stations,id',
                'ssid' => 'required|string|max:100',
                'password' => 'required|string|max:100',
            ]);

            $stationId = $request->input('station_id');
            $ssid = $request->input('ssid');
            $password = $request->input('password');
            $userId = auth()->id(); // Usuario autenticado

            // Desactivar credenciales anteriores para esta estación
            DB::table('network_credentials')
                ->where('station_id', $stationId)
                ->update(['state' => 0]);

            // Crear nuevas credenciales activas
            $credentialId = DB::table('network_credentials')->insertGetId([
                'ssid' => $ssid,
                'password' => $password,
                'station_id' => $stationId,
                'user_creator_id' => $userId,
                'state' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("[NetworkCredentials] Credenciales actualizadas para estación ID: {$stationId} por usuario ID: {$userId}");

            return response()->json([
                'success' => true,
                'message' => 'Credenciales de red actualizadas correctamente',
                'credential_id' => $credentialId
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("[NetworkCredentials] Error al actualizar credenciales: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al actualizar credenciales de red'
            ], 500);
        }
    }
}

