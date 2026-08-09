<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\NetworkCredentialM;
use App\Models\NetworkCredentialHistoryM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class NetworkCredentialApiController extends Controller
{
    public function saveNetworkCredential(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $userPermission = UserPermissionVerif::verif($request->userId, $request->email, $request->phone, $request->identifier);
            
            if ($userPermission[0] == 'yes') {
                // Buscar si existe un registro para este station_id
                $existingCredential = NetworkCredentialM::where('station_id', $request->station_id)
                    ->first();
                
                if ($existingCredential) {
                    // Si existe, copiar el registro actual al historial
                    NetworkCredentialHistoryM::create([
                        'ssid' => $existingCredential->ssid,
                        'password' => $existingCredential->password,
                        'state' => $existingCredential->state,
                        'network_credential_id' => $existingCredential->id,
                        'user_creator_id' => $existingCredential->user_creator_id,
                    ]);
                    
                    // Actualizar el registro existente con los nuevos datos
                    $existingCredential->update([
                        'ssid' => $request->ssid,
                        'password' => $request->password,
                        'user_creator_id' => $request->user_id,
                        'state' => 1, // Activo
                        'sent_to_device' => 1, // Para que el nodo recoja la actualización
                    ]);
                    
                    $credential = $existingCredential->fresh();
                    $message = 'Credenciales de red actualizadas exitosamente';
                    
                } else {
                    // Si no existe, crear un nuevo registro
                    $credential = NetworkCredentialM::create([
                        'ssid' => $request->ssid,
                        'password' => $request->password,
                        'station_id' => $request->station_id,
                        'user_creator_id' => $request->user_id,
                        'state' => 1, // Activo
                        'sent_to_device' => 1, // Para que el nodo recoja las credenciales
                    ]);
                    
                    $message = 'Credenciales de red creadas exitosamente';
                }
                
                DB::commit();
                
                return response()->json([
                    "response" => true,
                    "data" => $credential,
                    "message" => $message,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
                
            } else {
                DB::rollBack();
                return response()->json([
                    "response" => false,
                    "data" => null,
                    "message" => 'Usuario sin permisos',
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false,
                "data" => null,
                "message" => 'Error al guardar credenciales',
                "failed" => $th->getMessage(),
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    }
    
    public function getNetworkCredential(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $userPermission = UserPermissionVerif::verif($request->userId, $request->email, $request->phone, $request->identifier);
            
            if ($userPermission[0] == 'yes') {
                // Obtener las credenciales de la estación
                $credential = NetworkCredentialM::where('station_id', $request->station_id)
                    ->where('state', 1)
                    ->first();
                
                DB::commit();
                
                return response()->json([
                    "response" => true,
                    "data" => $credential,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
                
            } else {
                DB::rollBack();
                return response()->json([
                    "response" => false,
                    "data" => null,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false,
                "data" => null,
                "failed" => $th->getMessage(),
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    }
    
    public function getNetworkCredentialHistory(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $userPermission = UserPermissionVerif::verif($request->userId, $request->email, $request->phone, $request->identifier);
            
            if ($userPermission[0] == 'yes') {
                // Obtener el credential_id de la estación
                $credential = NetworkCredentialM::where('station_id', $request->station_id)
                    ->first();
                
                if ($credential) {
                    // Obtener el historial
                    $history = NetworkCredentialHistoryM::where('network_credential_id', $credential->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {
                    $history = [];
                }
                
                DB::commit();
                
                return response()->json([
                    "response" => true,
                    "data" => $history,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
                
            } else {
                DB::rollBack();
                return response()->json([
                    "response" => false,
                    "data" => null,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false,
                "data" => null,
                "failed" => $th->getMessage(),
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    }
}

