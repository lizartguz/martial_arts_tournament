<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApplicationTechnologyController extends Controller
{
    public $historical = null;
    public $forecast = null;
    public $windspeedAVGorMAX = null;

    public function getTAgraphic(Request $request){         
        try {
            $startDate = null;
            $endDate = null;
            $endDateForecast = null;
            $startDateForecast = null;
            date_default_timezone_set('America/La_Paz');
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();             
                $timeFilter = $request->timeFilter;                
                switch ($timeFilter) {
                   case 'Últimas 3 horas':
                        $fechaActual = new DateTime();
                        $endDate  = $fechaActual->format('Y-m-d H:i:s');
                        $startDateForecast  = $endDate;
                        $fechaActual->modify('-3 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('+27 hours');
                        $endDateForecast = $fechaActual->format('Y-m-d H:i:s');             
                        break;
                    case 'Últimas 6 horas':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $startDateForecast  = $endDate;
                        $fechaActual->modify('-6 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s'); 
                        $fechaActual->modify('+30 hours');    
                        $endDateForecast = $fechaActual->format('Y-m-d H:i:s');                       
                        break;
                    case 'Últimas 12 horas':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $startDateForecast  = $endDate;
                        $fechaActual->modify('-12 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s'); 
                        $fechaActual->modify('+36 hours');    
                        $endDateForecast = $fechaActual->format('Y-m-d H:i:s');                       
                        break;
                    case 'Últimas 24 horas':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $startDateForecast  = $endDate;
                        $fechaActual->modify('-24 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s'); 
                        $fechaActual->modify('+48 hours');    
                        $endDateForecast = $fechaActual->format('Y-m-d H:i:s');
                        break;
                    default:                        
                        $this->forecast = null; 
                        $this->historical = null; 
                        break;
                }
                
                $stationData = StationM::Where('id',$request->stationId)->select('stations.name','stations.location','stations.address')->first();
                $this->getSqlData($request->stationId, $startDate, $endDate,$startDateForecast, $endDateForecast,$timeFilter); 
                
                $stationData->windspeedAVGorMAX = $this->windspeedAVGorMAX;
                
                return response()->json([
                    "response" => true,
                    "stationData" => $stationData,
                    "historical" =>  $this->historical,
                    "forecast" =>  $this->forecast,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1], 
                    "type_subscriptions" => $userPermission[2],               
                ]);
            }else{
                return response()->json([
                    "response" => false,
                    "stationData" => null,
                    "historical" => null,
                    "forecast" => null,
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
                "stationData" => null,
                "historical" => null,
                "forecast" => null,
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    }


    private function getSqlData($stationId, $startDate, $endDate,$startDateForecast,$endDateForecast, $timeFilter){
        /*$this->historical = DB::connection('db_current_year')->table('data as d')->where('d.station_id',$stationId)                        
        ->whereBetween('d.receipt_date',[$startDate, $endDate])
        ->select('d.receipt_date','d.dewptout','d.tempout','d.press')->get();*/

        $this->historical = DB::connection('db_current_year')
        ->table('data as d')
        ->where('d.station_id', $stationId)
        ->whereBetween('d.receipt_date', [$startDate, $endDate])
        ->selectRaw("
            DATE_FORMAT(d.receipt_date, '%Y-%m-%d %H:00:00') AS receipt_date,
            AVG(d.dewptout) AS dewptout,
            AVG(d.tempout) AS tempout,
            AVG(d.press) AS press,
            AVG(d.windspeed) AS windspeed,
            AVG(d.windspeed2) AS windspeed2
        ")
        ->groupByRaw("DATE_FORMAT(d.receipt_date, '%Y-%m-%d %H:00:00')") // Agrupar usando la columna normalizada
        ->orderBy('receipt_date', 'asc')
        ->get();

        $windSpeedAVG = 0;
        $windSpeedMAX = 0;
        $this->historical->map(function($item) use (&$windSpeedAVG, &$windSpeedMAX){
            if ($item->windspeed === null && $item->windspeed2 !== null) {
                $item->windspeed = $item->windspeed2;
            }
            if($item->windspeed != null){
                $windSpeedAVG = $windSpeedAVG + $item->windspeed;
                if(floatval($item->windspeed) > floatval($windSpeedMAX)){
                    $windSpeedMAX = $item->windspeed;
                }
            }
            $item->windspeed = round($item->windspeed);
            unset($item->windspeed2);
            
            if($item->dewptout!=null && $item->tempout!=null && $item->press!=null){
                $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                $bulboHumedoMin = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));
                $deltaT = $item->tempout - round($bulboHumedoMin,3);
                $item->deltaT = round($deltaT,3); 
                unset($item->dewptout);
                unset($item->tempout);
                unset($item->press);
                return $item;
            }
        });

        if(count($this->historical)>0){
            $windSpeedAVG = floatval($windSpeedAVG) / count($this->historical);
            $windSpeedAVG = round($windSpeedAVG);
        }        
        //$this->windspeedAVGorMAX = $windSpeedAVG;
        $this->windspeedAVGorMAX = round($windSpeedMAX);

        $this->forecast = DB::table('forecasts as f')->where('station_id',$stationId)                        
        ->whereBetween('f.reg_date',[$startDateForecast, $endDateForecast])
        ->select(DB::raw('f.reg_date as receipt_date'),DB::raw('f.dp as dewptout'),DB::raw('f.t2m as tempout'),DB::raw('f.prec as press'))->get();

        $this->forecast->map(function($item){
            if($item->dewptout!=null && $item->tempout!=null && $item->press!=null){
                $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                $bulboHumedoMin = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                $deltaT = $item->tempout - round($bulboHumedoMin,3);
                $item->deltaT = round($deltaT,3); 
                
                unset($item->dewptout);
                unset($item->tempout);
                unset($item->press);

                return $item;
            }
        });

        $lastHistorical = $this->historical->last();
        $firstForecast = $this->forecast->first();        
        if ($lastHistorical && $firstForecast) {
            $averageDeltaT = ($lastHistorical->deltaT + $firstForecast->deltaT) / 2;
            $lastReceiptDate = \Carbon\Carbon::parse($lastHistorical->receipt_date);
            $firstReceiptDate = \Carbon\Carbon::parse($firstForecast->receipt_date);
            $averageReceiptDate = $lastReceiptDate->addSeconds($lastReceiptDate->diffInSeconds($firstReceiptDate) / 2);            
            $newRecord = (object) [
                'receipt_date' => $averageReceiptDate->format('Y-m-d H:i:s'),
                'deltaT' => round($averageDeltaT, 3)
            ];
            $this->historical->push($newRecord);
            $this->forecast->prepend($newRecord);
        }

        
    }
}
