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
use Illuminate\Support\Facades\Password;

class AlertApiController extends Controller
{
    
	public function getAlert(Request $request){
        $station = [];
        date_default_timezone_set('America/La_Paz');  
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){  
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();

				$alerts = AlertM::where('station_id', $request->stationId)
				->where('state', 1) 
				->select('id','description','reg_date')->get();
				
				return response()->json([
                    "response" => true, 
                    "alerts" => $alerts,
					"failed" => 'no',
					"data_user" => $userPermission[3],              
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "alerts" => null, 
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
                "alerts" => null,
                "failed"=> $th->getMessage(),
				"data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
				"type_subscriptions" => null,
            ]);
        }
    }

	public function getAlert2(Request $request){
        $station = [];
        date_default_timezone_set('America/La_Paz');  
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){  
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();

				/*$favoriteStationIds = FavoriteStationM::where('user_id', $request->userId)->distinct()->pluck('station_id')->toArray();
				$alerts = null;
				if(count($favoriteStationIds) > 0){
					if(count($favoriteStationIds) <= 5){
						$alerts = DB::table('favorite_stations')
							->join('stations', 'favorite_stations.station_id', '=', 'stations.id')
							->join('alerts', 'stations.id', '=', 'alerts.station_id')
							//->leftJoin('alerts', 'stations.id', '=', 'alerts.station_id')
							->select(
								'favorite_stations.station_id',
								'stations.name',
								'stations.location',
								'stations.address',
								'alerts.id as alert_id',
								'alerts.description',
								'alerts.reg_date',
								'alerts.f',
								'alerts.state as alert_state'
							)
							->where('favorite_stations.user_id', $request->userId)
							->where('favorite_stations.state', 1)
							->get();
					}else{
						$alerts = DB::table('favorite_stations')
						->join('stations', 'favorite_stations.station_id', '=', 'stations.id')
						->leftJoin('alerts', 'stations.id', '=', 'alerts.station_id')
						->select(
							'favorite_stations.station_id',
							'stations.name',
							'stations.location',
							'stations.address',
							DB::raw('MAX(alerts.id) as alert_id'), 
							DB::raw('MAX(alerts.description) as description'),
							DB::raw('MAX(alerts.reg_date) as reg_date'),
							DB::raw('MAX(alerts.f) as f'),
							DB::raw('MAX(alerts.state) as alert_state')
						)
						->where('favorite_stations.user_id', $request->userId)
						->where('favorite_stations.state', 1) 
						->groupBy('favorite_stations.station_id', 'stations.name', 'stations.location', 'stations.address')
						->get();
					}
				} */  
				
				$alerts1 =  FavoriteStationM::with(['station.alerts'])
				->where('user_id', $request->userId)
				->where('state', 1) 
				->get();
				$alerts2 = $alerts1->filter(function($favorite) {
					return $favorite->station->alerts->isNotEmpty(); // Solo mantener estaciones con alertas
				});
				
				// Mapeamos los datos a la estructura deseada
				$alerts = $alerts2->map(function($favorite) {
					return [
						'station' => [
							'id' => $favorite->station->id,
							'name' => $favorite->station->name,
							'location' => $favorite->station->location,
							'address' => $favorite->station->address,
							'alerts' => $favorite->station->alerts->map(function($alert) {
								return [
									'alert_id' => $alert->id,
									'description' => $alert->description,
									'reg_date' => $alert->reg_date,
									'state' => $alert->state == 1 ? 'Activa' : 'Inactiva',
								];
							})->toArray()  // Convertir la colección de alertas a array
						]
					];
				})->toArray();
                  
                return response()->json([
                    "response" => true, 
                    "alerts" => $alerts,
					//"test" => FavoriteStationM::where('user_id', $request->userId)->distinct()->pluck('station_id')->toArray(),
                    "failed" => 'no',
					"data_user" => $userPermission[3],              
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],                
                ]);
            }else{
                return response()->json([
                    "response" => false, 
                    "alerts" => null, 
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
                "alerts" => null,
                "failed"=> $th->getMessage(),
				"data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
				"type_subscriptions" => null,
            ]);
        }
    }
    
    //llovizna
	function DZ ($prec) {
		return ((($prec<=0.89)&&($prec>=0.55))?10:0);
	}

	//lluvia
	function RA ($prec) {
		return ((($prec<=60)&&($prec>=0.9))?12:0);
	}

	//nieve
	function SN ($temp,$prec) {
		return ((($temp<=1.4)&&($prec>=0.55))?14:0);
	}

	function SH ($prec,$li) {
		return ((($prec>=0.78)&&($li<=-2.5))?16:0);
	}

	function TS ($prec,$li) {
		return ((($prec>=1)&&($li<-3))?18:0);
	}

	function GR20 ($prec,$li) {
		return ((($prec>=0.78)&&($li<=-8))?800000000:($prec<=1?0:($li<-12.1?900000000:0)));
	}

	function SKY ($nubes) {
		$cadena = "";
		if (($nubes>0)&&($nubes<=0.15)) { $cadena = $cadena . '0';}
		if (($nubes>0.15)&&($nubes<=45)) { $cadena = $cadena . '2';}
		if (($nubes>45)&&($nubes<=55.5)) { $cadena = $cadena . '4';}
		if (($nubes>55.5)&&($nubes<=98.78)) { $cadena = $cadena . '6';}
		if (($nubes>98.78)&&($nubes<=100)) { $cadena = $cadena . '8';}
		return intval($cadena);
	}
	
	function P_PREC ($prec) {
		$cadena = "";
		if (($prec<=0.55)) { $cadena = $cadena . '0%';}
		if (($prec>0.55)&&($prec<=1.5)) { $cadena = $cadena . '70%';}
		if (($prec>1.5)&&($prec<=3.1)) { $cadena = $cadena . '80%';}
		if (($prec>3.1)&&($prec<=5.1)) { $cadena = $cadena . '90%';}
		if (($prec>5.1)&&($prec<=8.1)) { $cadena = $cadena . '95%';}
		if (($prec>8.1)) { $cadena = $cadena . '99%';}
		return $cadena;
	}
	
	function P_PREC_TXT ($prec) {
		$cadena = "";
		if (($prec<=0.55)) { $cadena = $cadena . '';}
		if (($prec>0.55)&&($prec<=0.88)) { $cadena = $cadena . 'llovizna ligera intermitente';}
		if (($prec>0.88)&&($prec<=3.1)) { $cadena = $cadena . 'lluvia ligera intermitente';}
		if (($prec>3.1)&&($prec<=5)) { $cadena = $cadena . 'lluvia ligera';}
		if (($prec>5)&&($prec<=8)) { $cadena = $cadena . 'lluvia moderada';}
		if (($prec>8)&&($prec<=10)) { $cadena = $cadena . 'lluvia moderada a fuerte';}
		if (($prec>10)) { $cadena = $cadena . 'lluvia moderada a fuerte';}
		return $cadena;
	}
	
	function RAFAGA ($viento) {
		$cadena = $viento;
		if (($viento<=9)) { $cadena = $cadena + 5;}
		if (($viento>9)&&($viento<=12)) { $cadena = 25;}
		if (($viento>12)&&($viento<=18)) { $cadena = 35;}
		if (($viento>18)&&($viento<=23)) { $cadena = 45;}
		if (($viento>23)&&($viento<33)) { $cadena = 65;}
		if (($viento>=33)&&($viento<=35)) { $cadena = 80;}
		if (($viento>35)) { $cadena = 90;}
		return $cadena;
	}
	
	function icono($fecha,$temp,$prec,$li,$nubes){
		$indice =  max($this->DZ($prec),$this->RA($prec),$this->SN($temp,$prec),$this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li),$this->SKY($nubes));
		$ico = '';        
        if (($indice>=0)&&($indice<2)) { $ico = '1';}
		if (($indice>=2)&&($indice<4)) { $ico = '2';}
		if (($indice>=4)&&($indice<6)) { $ico = '3';}
		if (($indice>=6)&&($indice<8)) { $ico = '4';}
		if (($indice>=8)&&($indice<10)) { $ico = '5';}
		if (($indice>=10)&&($indice<12)) { $ico = '7';}
		if (($indice>=12)&&($indice<14)) { $ico = '6';}
		if (($indice>=14)&&($indice<16)) { $ico = '10';}
		if (($indice>=16)&&($indice<18)) { $ico = '8';}
		if (($indice>=18)&&($indice<20)) { $ico = '9';}
		if (($indice>=20)&&($indice<247.5)) { $ico = '11';}//granizo

        $dt = new DateTime(date($fecha));
		if( ($dt->format('H') >= 6) && ($dt->format('H') < 18) ){
			$ico = $ico."a";
		} else {
			$ico = $ico."b";
		}
		return $ico;
	}
}
