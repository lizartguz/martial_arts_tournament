<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\AlertM;
use App\Models\StationM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\AccountDeletionRequestM;
use Illuminate\Support\Facades\Password;

class AccountDeletionApiController extends Controller
{
    public function setAccountDeletion(Request $request){
        date_default_timezone_set('America/La_Paz');  
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){  
                $user = User::where('id',$request->userId)->first();
                $user->update(['state' => 0 ]);
                $this->syncMobileFcmToken($request, (int) $request->userId);
				
				AccountDeletionRequestM::create([
					'user_id' => $request->userId,
					'reason' => $request->reason,
					'state' => 0,
					'validator_user_id' => null,
					'validation_comment' => null,
				]);
				DB::commit();
                  
                return response()->json([
                    "response" => true,                    					
                    "failed" => 'no',
					"data_user" => $userPermission[3],              
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false,                    
                    "failed" => 'no',
					"data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],  
                    "permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],                 
                ]); 
            }                     
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "response" => false,                
                "failed"=> $th->getMessage(),
				"data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
				"type_subscriptions" => null,
            ]);
        }
    }    
}
