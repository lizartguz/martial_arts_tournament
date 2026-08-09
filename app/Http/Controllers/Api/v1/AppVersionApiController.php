<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AppUpdateM;

class AppVersionApiController extends Controller
{
    public $resultMaxMin;
    public $result;
    
    public function getAppVersion(Request $request){        
        try {
            date_default_timezone_set('America/La_Paz');
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                $this->result = AppUpdateM::Select('current_version_android_text','new_version_android_text','current_version_ios_text','new_version_ios_text','link_android','link_ios')->first();
                
                return response()->json([
                    "response" => true,
                    "data" => $this->result,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]);

            }else{

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
                "failed"=> $th->getMessage(),
                "data_user" => null, 
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }  
    }
      
}
