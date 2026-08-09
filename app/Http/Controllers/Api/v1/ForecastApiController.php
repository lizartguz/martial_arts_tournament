<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use App\Models\ForecastM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\ForecastC;
use Illuminate\Http\JsonResponse;

class ForecastApiController extends Controller
{
    public function getForecast(Request $request){
        $station = [];
        date_default_timezone_set('America/La_Paz');  
        try {
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){  
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();

				$station = StationM::Where('stations.id',$request->stationId)
                ->select('stations.name','stations.address','stations.location')->first();

				$dateforecasts = ForecastM::Where('station_id',$request->stationId)
                ->join('stations as s','s.id','forecasts.station_id')
				->where('forecasts.reg_date','>=',date('Y-m-d').' 00:00:00')
                ->orderBy('reg_date','asc')->select(DB::raw('DATE(forecasts.reg_date) AS reg_date'),'forecasts.v10m_max','forecasts.tmax','forecasts.tmin','forecasts.hr2m_max','forecasts.hr2m_min','forecasts.prec_total')->distinct()->get();

				$maxIconsByDate = ForecastM::where('station_id', $request->stationId)
					->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
					->select(
						DB::raw('DATE(forecasts.reg_date) AS reg_date'),
						DB::raw('MAX(forecasts.icon) AS max_icon')
					)
					->groupBy(DB::raw('DATE(forecasts.reg_date)'))
					->get();

				$frequentIconsByDate = ForecastM::where('station_id', $request->stationId)
					->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
					->select(
						DB::raw('DATE(forecasts.reg_date) AS reg_date'),
						'forecasts.icon',
						DB::raw('COUNT(forecasts.icon) AS frequency')
					)
					->groupBy(DB::raw('DATE(forecasts.reg_date)'), 'forecasts.icon')
					->orderBy(DB::raw('DATE(forecasts.reg_date)'), 'asc')
					->orderBy('frequency', 'desc')
					->get()
					->groupBy('reg_date')
					->map(function ($icons) {
						$mostFrequent = $icons->first(); 
						return [
							'reg_date' => $mostFrequent->reg_date,
							'most_frequent_icon' => $mostFrequent->icon,
							'frequency' => $mostFrequent->frequency,
						];
					});

				$iconForDate = $maxIconsByDate->map(function ($maxIcon) use ($frequentIconsByDate) {
						$regDate = $maxIcon->reg_date;
						$frequentIcon = $frequentIconsByDate->get($regDate);					
						
						return [
							'reg_date' =>$regDate,
							'icon_date' => $maxIcon->max_icon >= 6
								? $this->calculateIconDate($maxIcon->max_icon,$regDate)
								: ($this->calculateIconDate($frequentIcon['most_frequent_icon'],$regDate) ?? null),
						];
					});

				
				/* 
				 ->where('forecasts.reg_date','>=',date('Y-m-d').' 00:00:00')
				 
				 Obliga a que el pronostico siemrpe inicie en el listado de la app movil con el dia actual, y no con dias anteriores registrados en su recoleccion de la pagina web.
				 */
				$forecast = ForecastM::Where('station_id',$request->stationId)
				->join('stations as s','s.id','forecasts.station_id')
				->orderBy('reg_date','asc')
				->where('forecasts.reg_date','>=',date('Y-m-d').' 00:00:00')
				->select('forecasts.id','forecasts.reg_date',DB::raw('DATE(forecasts.reg_date) AS only_date'),'forecasts.v10m','forecasts.v10m_dir','forecasts.v850','forecasts.v850_dir','forecasts.prec','forecasts.cape','forecasts.li','forecasts.dam','forecasts.a850','forecasts.a500','forecasts.t2m','forecasts.hr2m','forecasts.t850','forecasts.t500','forecasts.baro','forecasts.cloud','forecasts.snow','forecasts.dp','forecasts.sh_ts','forecasts.icon','forecasts.icon_image','forecasts.min_t2m','forecasts.min_dp','forecasts.state')->get();			
                
				/*$forecast = ForecastM::Where('station_id',$request->stationId)
                ->join('stations as s','s.id','forecasts.station_id')
                ->orderBy('reg_date','asc')
                ->select('forecasts.id','forecasts.reg_date','forecasts.v10m','forecasts.v10m_dir','forecasts.v850','forecasts.v850_dir','forecasts.prec','forecasts.cape','forecasts.li','forecasts.dam','forecasts.a850','forecasts.a500','forecasts.t2m','forecasts.hr2m','forecasts.t850','forecasts.t500','forecasts.baro','forecasts.cloud','forecasts.snow','forecasts.dp','forecasts.tmax','forecasts.tmin','forecasts.sh_ts','forecasts.icon','forecasts.icon_image','forecasts.min_t2m','forecasts.v10m_max','forecasts.hr2m_max','forecasts.hr2m_min','forecasts.min_dp','forecasts.state','forecasts.station_id','s.name','s.location','s.address')->get();*/

				
				
                $station_data = StationM::Where('stations.id', $request->stationId)
                    ->join('current_data as cd', 'cd.station_id', 'stations.id')                        
                    ->orderBy('cd.receipt_date', 'desc')
                    ->select('cd.receipt_date')
                    ->first();

				$receiptDateVerf = strtotime($station_data->receipt_date);
				$today = strtotime('today');
				if ($receiptDateVerf >= $today && $receiptDateVerf < strtotime('tomorrow')) {
					$receipt_date = $station_data->receipt_date; 
				} else {
					$receipt_date = date('Y-m-d H:i:s');//si la estacion esta caida el pronostico se reajusta al dia actual, forzando a que el pronostico este bien aunque la estacion este caida, permitiendo un icono adecuado
				}

                $nearest_forecast = ForecastM::Where('station_id', $request->stationId)
                    ->select('forecasts.*')
                    ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, forecasts.reg_date, ?))', [$receipt_date])
                    ->whereNotNull('forecasts.reg_date')->whereNotNull('forecasts.t2m')->whereNotNull('forecasts.prec')->whereNotNull('forecasts.li')->whereNotNull('forecasts.cloud')
                    ->first();
                
                $icon_background = $this->icono($nearest_forecast->reg_date,$nearest_forecast->t2m,$nearest_forecast->prec,$nearest_forecast->li,$nearest_forecast->cloud);
                
                $is_favorite_station = false;
                if(FavoriteStationM::where('user_id',$request->userId)->where('station_id',$request->stationId)->exists()){
                    $is_favorite_station = true;
                }
                return response()->json([
                    "response" => true, 
					"stationData" => $station ?? null,
					"iconForDate" => count($iconForDate)>0?$iconForDate:null ,
					"dateForecast" => count($dateforecasts)>0?$dateforecasts:null ,
                    "dataForecast" => count($forecast)>0?$forecast:null,					
                    "icon_background" => $icon_background,
                    "failed" => 'no', 
                    "is_favorite_station" => $is_favorite_station, 
					"data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],               
                ]);
            }else{
                return response()->json([
                    "response" => false, 
					"iconForDate" => null,
					"stationData" =>  null,
                    "dataForecast" => null, 
					"dateForecast" => null, 
                    "icon_background" => null, 
                    "failed" => 'no', 
                    "is_favorite_station" => null,
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
				"iconForDate" => null,
				"stationData" =>  null,
                "dataForecast" => null,
				"dateForecast" => null, 
                "icon_background" => null,
                "is_favorite_station" => null,
                "failed"=> $th->getMessage(),
				"data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
				"type_subscriptions" => null,
            ]);
        }
    }

	public function getForecastApi(Request $request){
		try {
			DB::beginTransaction();
			$userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
			if($userPermission[0]=='yes'){  
				$this->syncMobileFcmToken($request, (int) $request->userId);
				DB::commit();
				
				$fromLivewire = true;
				$byCoordinates = true;
				$forecast = (new ForecastC())->getForecastCurl($request, $fromLivewire, $byCoordinates);

				$creditResponse = app(UserCreditApiController::class)->consumeUserCredits($request);
				$creditPayload = $creditResponse instanceof JsonResponse
					? $creditResponse->getData(true)
					: json_decode($creditResponse->getContent(), true);

				if (!($creditPayload['response'] ?? false)) {
					return response()->json([
						"success" => false,
						"errors" => [
							$creditPayload['message'] ?? 'No fue posible consumir un crédito para esta consulta.',
						],
						"data" => null,
						"data_user" => $userPermission[3],
						"user_permission" => $userPermission[0],
						"permit_detail" => $userPermission[1],
						"type_subscriptions" => $userPermission[2],
					], 400);
				}

				if (is_array($forecast)) {
					$forecast['credit'] = $creditPayload['data'] ?? $creditPayload;
					$forecast['data_user'] = $userPermission[3];
					$forecast['user_permission'] = $userPermission[0];
					$forecast['permit_detail'] = $userPermission[1];
					$forecast['type_subscriptions'] = $userPermission[2];
				}

				return response()->json($forecast);
			}else{
				DB::rollBack();
				return response()->json([
					"success" => false,					
					"errors" => ['Usuario no autorizado'],
					"data" => null,
					"data_user" => $userPermission[3],
					"user_permission" => $userPermission[0],
					"permit_detail" => $userPermission[1],
					"type_subscriptions" => $userPermission[2],
				], 403);
			}
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json([
				"success" => false,				
				"errors" => [$th->getMessage()],
				"data" => null,
				"data_user" => null,
				"user_permission" => 'no',
				"permit_detail" => '',
				"type_subscriptions" => null,
			], 500);
		}
		
	}




	function calculateIconDate($value,$regDate) {
		$ico = '';        
        if (($value>=0)&&($value<=1)) { $ico = '1';}
		if (($value>=2)&&($value<4)) { $ico = '2';}
		if (($value>=4)&&($value<6)) { $ico = '3';}
		if (($value>=6)&&($value<8)) { $ico = '4';}
		if (($value>=8)&&($value<10)) { $ico = '5';}
		if (($value>=10)&&($value<12)) { $ico = '7';}
		if (($value>=12)&&($value<14)) { $ico = '6';}
		if (($value>=14)&&($value<16)) { $ico = '10';}
		if (($value>=16)&&($value<18)) { $ico = '8';}
		if (($value>=18)&&($value<20)) { $ico = '9';}
		if (($value>=20)&&($value<247.5)) { $ico = '11';}//granizo

        $dt = new DateTime(date($regDate));
		if( ($dt->format('H') >= 6) && ($dt->format('H') < 18) ){
			$ico = $ico."a";
		} else {
			$ico = $ico."b";
		}
		return $ico;
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
