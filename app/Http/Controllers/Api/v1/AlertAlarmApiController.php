<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\AlertM;
use App\Models\StationM;
use App\Models\AlarmStoreM;
use App\Models\AlertAlarmM;
use App\Models\AlertStoreM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AlertAlarmApiController extends Controller
{
    public function getAlertAlarm(Request $request){       
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                
                $alertAlarm = AlertAlarmM::Where('station_id',$request->stationId)
                ->where('user_id',$request->userId)
                ->orderBy('type','desc')
                ->select('id','temp_max','temp_min','dewpoint','dewpoint_new','wind_max','rain_total','uv_max','solar_rad_max','drop_type','dt_max','dt_min','type','dt_new','notification_status','state','station_id','user_id')->get();  
                
               
                $alertStore = AlertStoreM::join('stations as s', 's.id', '=', 'alert_store.station_id')
                    ->where('s.id',$request->stationId)
                    ->where('user_id', $request->userId)
                    ->select(
                        'alert_store.reg_date',
                        'alert_store.value as value_number',
                        'alert_store.variable',
                        'alert_store.unit',
                        'alert_store.state',
                        DB::raw("CONCAT(alert_store.variable, ' ', alert_store.value, ' ', alert_store.unit) as value")
                    )
                    ->orderBy('alert_store.reg_date', 'DESC')
                    ->limit(10)
                    ->get();
                
                
               

                $alarmStore = AlarmStoreM::join('stations as s', 's.id', '=', 'alarm_store.station_id')
                    ->where('s.id',$request->stationId)
                    ->where('user_id', $request->userId)
                    ->select(
                        'alarm_store.reg_date',                        
                        'alarm_store.value as value_number',
                        'alarm_store.variable',
                        'alarm_store.unit',
                        'alarm_store.state',                       
                        DB::raw("CONCAT(alarm_store.variable, ' ', alarm_store.value, ' ', alarm_store.unit) as value")
                    )
                    ->orderBy('alarm_store.reg_date', 'DESC')
                    ->limit(10)
                    ->get();

                $alertProData = AlertM::where('station_id', $request->stationId)
                    ->select('reg_date','description')
                    ->orderBy('reg_date', 'ASC')
                    ->limit(5)
                    ->get();
               

                
                return response()->json([
                    "response" => true, 
                    "dataAA" => count($alertAlarm)>0?$alertAlarm:null ,
                    "alertData" => count($alertStore)>0?$alertStore:null ,
                    "alarmData" => count($alarmStore)>0?$alarmStore:null ,
                    "alertProData" => count($alertProData)>0?$alertProData:null ,
                    "failed" => 'no',       
                    "data_user" => $userPermission[3],             
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "dataAA" => null,  
                    "alertData" => null, 
                    "alarmData" => null, 
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
                "dataAA" => null,
                "alertData" => null, 
                "alarmData" => null,
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    }
    
    public function saveAlertAlarm(Request $request){       
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                
                $attributesToCheck = [
                    'station_id' => $request->stationId,
                    'user_id' => $request->userId,
                    'type' => $request->type,
                ];
                $valuesToUpdate = [
                    'temp_max' => $request->temp_max==''?null:$request->temp_max,
                    'temp_min' => $request->temp_min==''?null:$request->temp_min,                    
                    'dewpoint' => $request->has('dewpoint') && $request->input('dewpoint') !== ''? $request->input('dewpoint'): null,
                    'dewpoint_new' => $request->has('dewpoint_new') && $request->input('dewpoint_new') !== ''? $request->input('dewpoint_new'): null,
                    'wind_max' => $request->wind_max==''?null:$request->wind_max,
                    'rain_total' => $request->rain_total==''?null:$request->rain_total,
                    'uv_max' => $request->uv_max==''?null:$request->uv_max,
                    'solar_rad_max' => $request->solar_rad_max==''?null:$request->solar_rad_max,
                    'drop_type' => ($request->dropType=='' || $request->dropType=='Ninguna')?null:$request->dropType,
                    'dt_max' => $request->dt_max==''?null:$request->dt_max,
                    'dt_min' => $request->dt_min==''?null:$request->dt_min,
                    'dt_new' => $request->has('dt_new') && $request->input('dt_new') !== ''? $request->input('dt_new'): null,
                    'notification_status' => $request->notification_status,
                    //'state' => $request->state,                    
                ];
                AlertAlarmM::updateOrCreate($attributesToCheck, $valuesToUpdate);
                DB::commit();             
                
                return response()->json([
                    "response" => true, 
                    "message" => 'Cambios guardados!' ,
                    "failed" => 'no',  
                    "data_user" => $userPermission[3],                  
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "message" => $userPermission[1], 
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
                "message" => 'No se ha podido guardar los cambios, intente mas tarde.' ,              
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,             
            ]);
        }
    }
}
