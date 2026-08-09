<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use DateTime;

class ChangePasswordApiController extends Controller
{
    public function changePassword(Request $request){
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes') {
                try{
                    $user = User::where('email',$request->email)->where('id',$request->userId)->first();
                    
                    if($user!==null){
                        if(Hash::check($request->oldPassword, $user->password)) {
                            User::where('email', $request->email)->where('id', $request->userId)->update([
                                'password' => Hash::make($request->newPassword),
                            ]);
                            DB::commit();
                            return response()->json([
                                "response" => true,
                                "message" => 'Cambios aplicados exitosamente!',
                                "failed" => 'no',
                                "data_user" => $userPermission[3],
                                "user_permission" => $userPermission[0],
                                "permit_detail" => $userPermission[1],
                                "type_subscriptions" => $userPermission[2],
                            ]);
                        }else{
                            return response()->json([
                                "response" => false,
                                "message" => 'Su contraseña actual es incorrecta.',
                                "failed" => 'no',
                                "user_permission" => 'no',
                                "data_user" => $userPermission[3],
                                "permit_detail" => 'incorrect credentials',
                                "type_subscriptions" => $userPermission[2],
                            ]);
                        }
                    }
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        "response" => false,
                        "message" => 'No se pudo cambiar la contraseña. Intente más tarde.',
                        "failed"=> $th->getMessage(),
                        "data_user" => $userPermission[3],
                        "user_permission" => $userPermission[0],
                        "permit_detail" => $userPermission[1],
                        "type_subscriptions" => $userPermission[2],
                    ]);
                }
            }else{
                return response()->json([
                    "response" => false,
                    "message" => null,
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
                "message" => null,
                "failed" => $th->getMessage(),
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    }
}
