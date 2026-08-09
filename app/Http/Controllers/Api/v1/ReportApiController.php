<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use App\Models\ReportM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CurrentDataM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ReportApiController extends Controller
{
    public $resultMaxMin;
    public $result;
    
    public function getReport(Request $request){ 
        try {
            date_default_timezone_set('America/La_Paz');
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                $this->result = null; 
                $this->result = StationM::Where('stations.id',$request->stationId)
                ->join('current_data as cd','cd.station_id','stations.id')                        
                ->orderBy('cd.receipt_date','desc')
                ->select('cd.station_id','stations.name','stations.location','stations.address','cd.receipt_date','cd.tempout','cd.dewptout','cd.press','cd.tempoutmin','cd.tempoutmax','cd.raintotal','cd.windspeed','cd.winddir','cd.windspeedhi','cd.windspeed as windspeed1','cd.winddir as winddir1','cd.windspeedhi as windspeedhi1','cd.windspeed2','cd.winddir2','cd.windspeedhi2','cd.humoutmin','cd.humoutmax','cd.humout','cd.solrad','cd.solevo','cd.soiltemp1','cd.soiltemp2','cd.soiltemp3','cd.soiltemp3','cd.soiltemp4','cd.soilhum1','cd.soilhum2','cd.soilhum3','cd.soilhum4','cd.leaftemp1','cd.leaftemp2','cd.leaftemp3','cd.leaftemp4','cd.leafhum1','cd.leafhum2','cd.leafhum3','cd.leafhum4','cd.uvindex')->first();
                if($this->result->dewptout!=null && $this->result->tempout!=null && $this->result->press!=null && $this->result->dewptout!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->result->dewptout / (237.7 + $this->result->dewptout))));
                    $bulboHumedo = (((0.00066 * $this->result->press) *  $this->result->tempout) + ((4098 * $value) / pow(($this->result->dewptout + 237.7) , 2) * $this->result->dewptout)) / ((0.00066 *  $this->result->press ) + (4098 * $value) / pow(($this->result->dewptout + 237.7) , 2));                        
                    $detalT = $this->result->tempout - round($bulboHumedo,3);
                    $this->result->deltat = (string)$detalT;  
                }else{
                    $this->result->deltat = null;                    
                }
                
                
                $viento = '';
                if(($this->result->winddir !== null)){
                    if (($this->result->winddir>=337.5) || ($this->result->winddir<22.5)){
                        $viento = 'N' ;
                    };
                    if (($this->result->winddir>=22.5) && ($this->result->winddir<67.5)){
                        $viento = 'NE' ;
                    };
                    if (($this->result->winddir>=67.5) && ($this->result->winddir<112.5)){
                        $viento = 'E' ;
                    };
                    if (($this->result->winddir>=112.5) && ($this->result->winddir<157.5)){
                        $viento = 'SE';
                    };
                    if (($this->result->winddir>=157.5) && ($this->result->winddir<202.5)){
                        $viento = 'S' ;
                    };
                    if (($this->result->winddir>=202.5) && ($this->result->winddir<247.5)){
                        $viento = 'SO';
                    };
                    if (($this->result->winddir>=247.5) && ($this->result->winddir<292.5)){
                        $viento = 'O' ;
                    };
                    if (($this->result->winddir>=292.5) && ($this->result->winddir<337.5)){
                        $viento = 'NO';
                    }
                }else{ 
                    $viento = ''; 
                }
                $this->result->winddirstr = $viento;
                $this->result->winddirstr1 = $viento;
                $viento2 = '';
                if(($this->result->winddir2 !== null)){
                    if (($this->result->winddir2>=337.5) || ($this->result->winddir2<22.5)){
                        $viento2 = 'N' ;
                    };
                    if (($this->result->winddir2>=22.5) && ($this->result->winddir2<67.5)){
                        $viento2 = 'NE' ;
                    };
                    if (($this->result->winddir2>=67.5) && ($this->result->winddir2<112.5)){
                        $viento2 = 'E' ;
                    };
                    if (($this->result->winddir2>=112.5) && ($this->result->winddir2<157.5)){
                        $viento2 = 'SE';
                    };
                    if (($this->result->winddir2>=157.5) && ($this->result->winddir2<202.5)){
                        $viento2 = 'S' ;
                    };
                    if (($this->result->winddir2>=202.5) && ($this->result->winddir2<247.5)){
                        $viento2 = 'SO';
                    };
                    if (($this->result->winddir2>=247.5) && ($this->result->winddir2<292.5)){
                        $viento2 = 'O' ;
                    };
                    if (($this->result->winddir2>=292.5) && ($this->result->winddir2<337.5)){
                        $viento2 = 'NO';
                    }
                }else{
                    $viento2 = '';
                }
                $this->result->winddirstr2 = $viento2;
                if($this->result->winddir===null || $this->result->windspeed===null || $this->result->windspeedhi===null){
                    $this->result->winddir=$this->result->winddir2;
                    $this->result->windspeed=$this->result->windspeed2;
                    $this->result->windspeedhi=$this->result->windspeedhi2;
                }
                $this->result->wind_sensors = 'all'; // 1(2mt), 2(10mt), all(2mt y 10mt)
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
            DB::commit();
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

    public function getReportWithFilter(Request $request){ 
        $this->resultMaxMin = null; 
        $this->result = null;       
        try {
            date_default_timezone_set('America/La_Paz');
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                $dataStation = null;
                $dataStation = StationM::Where('id',$request->stationId)->select('stations.name','stations.location','stations.address')->first();
                
                
                $filter = $request->filter;                
                switch ($filter) {
                    case 'Hoy':
                        $fechaActual = new DateTime();
                        $startDate = $fechaActual->format('Y-m-d').' 00:00:00';
                        $endDate = $fechaActual->format('Y-m-d H:i:s');     
                        $startDateTime = new DateTime($startDate);
                        $endDateTime = new DateTime($endDate);
                        $interval = $startDateTime->diff($endDateTime);
                        $horasDiferencia = $interval->h + ($interval->days * 24); // Total horas incluyendo días
                        $groupRange = '';
                        if ($horasDiferencia <= 2) {                           
                            $groupRange = '-2';
                        } elseif ($horasDiferencia > 2 && $horasDiferencia <= 12) {
                           $groupRange = '2_at_12';
                        } elseif ($horasDiferencia > 12 && $horasDiferencia <= 24) {
                            $groupRange = '13_at_24';
                        } else {
                            //echo "La fecha actual supera el final del día.\n";
                            $groupRange = 'others';
                        }
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Hoy',$groupRange);                                     
                        break;

                    case 'Última hora':
                       $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-1 hour');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Última hora',null);           
                        break;
                    case 'Últimas 12 horas':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-12 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                         
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Últimas 12 horas',null); 
                        break;
                    case 'Últimas 24 horas':
                        $fechaActual = new DateTime();
                        $endDate  = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-24 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                         
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Últimas 24 horas',null);
                        break;
                    case 'Últimos 7 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-7 days');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                         
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Últimos 7 días',null);
                        break;
                    case 'Últimos 30 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-30 days');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                         
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Últimos 30 días',null);
                        break;
                    case 'Últimos 3 meses':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-3 months');
                        $startDate = $fechaActual->format('Y-m-d H:i:s'); 
                        //return $startDate.'---'.$endDate;
                        $this->getSqlData($request->stationId,$startDate, $endDate,'Últimos 3 meses',null);
                        break;
                    default:
                        $this->resultMaxMin = null; 
                        $this->result = null; 
                        break;
                }  
                
                
                if($this->resultMaxMin!==null){
                    $this->resultMaxMin->wind_sensors = 'all';
                }
                return response()->json([
                    "response" => true, 
                    "stationData" => $dataStation,
                    "minMaxData" => $this->resultMaxMin,
                    "graphData" => $this->result,                   
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
                    "minMaxData" => null,
                    "graphData" => null, 
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
                "minMaxData" => null,
                "graphData" => null,
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
            ]);
        }
    }
    
    private function getSqlData($stationId,$startDate,$endDate,$type,$groupRange){
        
        switch ($type) {
            case 'Hoy':
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),
                DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),
                DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutaveragemin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutaveragemin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutaveragemin - round($bulboHumedoMin,3);                        
                } 
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutaveragemax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutaveragemax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutaveragemax - round($bulboHumedoMax,3);                        
                } 
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);                
                

                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }
                
                if ($groupRange == '-2') {                        
                    
                    $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                   ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                   ->groupBy(DB::raw('cd.receipt_date'))
                   ->orderBy(DB::raw('cd.receipt_date'))
                   ->select(DB::raw('cd.receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                } elseif ($groupRange=='2_at_12') {
                   $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                   ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                   ->groupBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00")'))
                   ->orderBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00")'))
                   ->select(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00") as receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                } elseif ($groupRange=='13_at_24') {
                    $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                    ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                    ->groupBy(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")'))
                    ->orderBy(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")'))
                    ->select(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00") as receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get(); 
                } else {
                    $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                    ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                    ->groupBy('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")')
                    ->orderBy('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")')
                    ->select(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00") as receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get(); 
                }
                               
                
                $this->result->map(function($item){
                    /*if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }*/

                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
                break;
            
            case 'Última hora':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutaveragemin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutaveragemin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutaveragemin - round($bulboHumedoMin,3);                        
                }
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutaveragemax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutaveragemax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutaveragemax - round($bulboHumedoMax,3);                        
                }
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);                
                
                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }
                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('cd.receipt_date'))
                ->orderBy(DB::raw('cd.receipt_date'))
                ->select(DB::raw('cd.receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();               
                
                $this->result->map(function($item){
                     if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });                
                break;
            

            case 'Últimas 12 horas':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('cd.station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'))->first();
                if ($this->resultMaxMin) {
                    if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutmin!=null && $this->resultMaxMin->pressmin!=null){
                        $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                        $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutmin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                        $detalTmin = $this->resultMaxMin->tempoutmin - round($bulboHumedoMin,3);                        
                    } 
                    if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutmax!=null && $this->resultMaxMin->pressmax!=null){
                        $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                        $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutmax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                        $detalTmax = $this->resultMaxMin->tempoutmax - round($bulboHumedoMax,3);                        
                    } 
                    $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                    $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);

                    if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                        $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                        $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                        $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                        $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                    }
                }
                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00")'))
                ->orderBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00")'))
                ->select(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m-%d %H:00:00") as receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                
               
                $this->result->map(function($item){
                    if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
               
                break;



            case 'Últimas 24 horas':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutmin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutmin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutmin - round($bulboHumedoMin,3);                        
                } 
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutmax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutmax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutmax - round($bulboHumedoMax,3);                        
                } 
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);
                
                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }
                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")'))
                ->orderBy(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00")'))
                ->select(DB::raw('CONCAT(DATE_FORMAT(cd.receipt_date, "%Y-%m-%d "), LPAD(FLOOR(HOUR(cd.receipt_date) / 3) * 3, 2, "0"), ":00:00") as receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                
                
                $this->result->map(function($item){
                    if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
                
                break;

            
            case 'Últimos 7 días':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutmin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutmin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutmin - round($bulboHumedoMin,3);                        
                } 
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutmax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutmax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutmax - round($bulboHumedoMax,3);                        
                } 
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);

                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }
                
                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('DATE(cd.receipt_date)'))
                ->orderBy(DB::raw('DATE(cd.receipt_date)'))
                ->select(DB::raw('DATE(cd.receipt_date) as  receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                
                
                $this->result->map(function($item){
                    if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
                
                break;



            case 'Últimos 30 días':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutmin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutmin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutmin - round($bulboHumedoMin,3);                        
                } 
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutmax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutmax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutmax - round($bulboHumedoMax,3);                        
                } 
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);
                
                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }

                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('YEARWEEK(cd.receipt_date, 1)'))
                ->orderBy(DB::raw('YEARWEEK(cd.receipt_date, 1)'))
                ->select(DB::raw('YEARWEEK(cd.receipt_date, 1) as  receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                
                
                $this->result->map(function($item){
                    if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    $year = substr($item->receipt_date, 0, 4);
                    $week = substr($item->receipt_date, 4, 2);
                    $date = new DateTime();
                    $date->setISODate($year, $week); 
                    $item->receipt_date = $date->format('Y-m-d');
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
                
                break;

            case 'Últimos 3 meses':                
                $detalTmin = null;
                $detalTmax = null;      
                $this->resultMaxMin = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->select(DB::raw('MAX(cd.dewptout) as dewptoutmax'),DB::raw('MIN(cd.dewptout) as dewptoutmin'),DB::raw('MIN(cd.tempout) as tempoutaveragemin'),DB::raw('MAX(cd.tempout) as tempoutaveragemax'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('MIN(cd.windspeed) as windspeedmin'),DB::raw('MAX(cd.windspeed) as windspeedmax'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax'),DB::raw('MIN(cd.windspeed) as windspeedmin1'),DB::raw('MAX(cd.windspeed) as windspeedmax1'),DB::raw('MIN(cd.windspeedhi) as windspeedhimin1'),DB::raw('MAX(cd.windspeedhi) as windspeedhimax1'),DB::raw('MIN(cd.windspeed2) as windspeedmin2'),DB::raw('MAX(cd.windspeed2) as windspeedmax2'),DB::raw('MIN(cd.windspeedhi2) as windspeedhimin2'),DB::raw('MAX(cd.windspeedhi2) as windspeedhimax2'),DB::raw('MIN(cd.humoutmin) as humoutmin'),DB::raw('MAX(cd.humoutmax) as humoutmax'),DB::raw('MIN(cd.humout) as humoutaveragemin'),DB::raw('MAX(cd.humout) as humoutaveragemax'),DB::raw('MIN(cd.solrad) as solradmin'),DB::raw('MAX(cd.solrad) as solradmax'),DB::raw('MIN(cd.solevo) as solevomin'),DB::raw('MAX(cd.solevo) as solevomax'),DB::raw('MIN(cd.press) as pressmin'),DB::raw('MAX(cd.press) as pressmax'),DB::raw('MIN(cd.soiltemp1) as soiltemp1min'),DB::raw('MAX(cd.soiltemp1) as soiltemp1max'),DB::raw('MIN(cd.soiltemp2) as soiltemp2min'),DB::raw('MAX(cd.soiltemp2) as soiltemp2max'),DB::raw('MIN(cd.soiltemp3) as soiltemp3min'),DB::raw('MAX(cd.soiltemp3) as soiltemp3max'),DB::raw('MIN(cd.soiltemp4) as soiltemp4min'),DB::raw('MAX(cd.soiltemp4) as soiltemp4max'),DB::raw('MIN(cd.soilhum1) as soilhum1min'),DB::raw('MAX(cd.soilhum1) as soilhum1max'),DB::raw('MIN(cd.soilhum2) as soilhum2min'),DB::raw('MAX(cd.soilhum2) as soilhum2max'),DB::raw('MIN(cd.soilhum3) as soilhum3min'),DB::raw('MAX(cd.soilhum3) as soilhum3max'),DB::raw('MIN(cd.soilhum4) as soilhum4min'),DB::raw('MAX(cd.soilhum4) as soilhum4max'),DB::raw('MIN(cd.leaftemp1) as leaftemp1min'),DB::raw('MAX(cd.leaftemp1) as leaftemp1max'),DB::raw('MIN(cd.leaftemp2) as leaftemp2min'),DB::raw('MAX(cd.leaftemp2) as leaftemp2max'),DB::raw('MIN(cd.leaftemp3) as leaftemp3min'),DB::raw('MAX(cd.leaftemp3) as leaftemp3max'),DB::raw('MIN(cd.leaftemp4) as leaftemp4min'),DB::raw('MAX(cd.leaftemp4) as leaftemp4max'),DB::raw('MIN(cd.leafhum1) as leafhum1min'),DB::raw('MAX(cd.leafhum1) as leafhum1max'),DB::raw('MIN(cd.leafhum2) as leafhum2min'),DB::raw('MAX(cd.leafhum2) as leafhum2max'),DB::raw('MIN(cd.leafhum3) as leafhum3min'),DB::raw('MAX(cd.leafhum3) as leafhum3max'),DB::raw('MIN(cd.leafhum4) as leafhum4min'),DB::raw('MAX(cd.leafhum4) as leafhum4max'))->first();
                
                if($this->resultMaxMin->dewptoutmin!=null && $this->resultMaxMin->tempoutmin!=null && $this->resultMaxMin->pressmin!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmin / (237.7 + $this->resultMaxMin->dewptoutmin))));
                    $bulboHumedoMin = (((0.00066 * $this->resultMaxMin->pressmin) *  $this->resultMaxMin->tempoutmin) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2) * $this->resultMaxMin->dewptoutmin)) / ((0.00066 *  $this->resultMaxMin->pressmin ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmin + 237.7) , 2));                        
                    $detalTmin = $this->resultMaxMin->tempoutmin - round($bulboHumedoMin,3);                        
                } 
                if($this->resultMaxMin->dewptoutmax!=null && $this->resultMaxMin->tempoutmax!=null && $this->resultMaxMin->pressmax!=null){
                    $value = (6.11 * pow(10,(7.5 *$this->resultMaxMin->dewptoutmax / (237.7 + $this->resultMaxMin->dewptoutmax))));
                    $bulboHumedoMax = (((0.00066 * $this->resultMaxMin->pressmax) *  $this->resultMaxMin->tempoutmax) + ((4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2) * $this->resultMaxMin->dewptoutmax)) / ((0.00066 *  $this->resultMaxMin->pressmax ) + (4098 * $value) / pow(($this->resultMaxMin->dewptoutmax + 237.7) , 2));                        
                    $detalTmax = $this->resultMaxMin->tempoutmax - round($bulboHumedoMax,3);                        
                } 
                $this->resultMaxMin->deltatmin = (string)round($detalTmin,3);
                $this->resultMaxMin->deltatmax = (string)round($detalTmax,3);
                
                if($this->resultMaxMin->windspeedmin==null && $this->resultMaxMin->windspeedmax==null && $this->resultMaxMin->windspeedhimin==null && $this->resultMaxMin->windspeedhimax==null){
                    $this->resultMaxMin->windspeedmin=$this->resultMaxMin->windspeedmin2;
                    $this->resultMaxMin->windspeedmax=$this->resultMaxMin->windspeedmax2;
                    $this->resultMaxMin->windspeedhimin=$this->resultMaxMin->windspeedhimin2;
                    $this->resultMaxMin->windspeedhimax=$this->resultMaxMin->windspeedhimax2;
                }
                
                $this->result = DB::connection('db_current_year')->table('data as cd')->where('station_id',$stationId)                        
                ->whereBetween('cd.receipt_date',[$startDate, $endDate])
                ->groupBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m")'))
                ->orderBy(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m")'))
                ->select(DB::raw('DATE_FORMAT(cd.receipt_date, "%Y-%m") as  receipt_date'),DB::raw('AVG(cd.tempout) as tempout'),DB::raw('AVG(cd.tempoutmin) as tempoutmin'),DB::raw('AVG(cd.tempoutmax) as tempoutmax'),DB::raw('AVG(cd.raintotal) as raintotal'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.windspeed) as windspeed1'),DB::raw('AVG(cd.windspeedhi) as windspeedhi1'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.humoutmin) as humoutmin'),DB::raw('AVG(cd.humoutmax) as humoutmax'),DB::raw('AVG(cd.humout) as humout'),DB::raw('AVG(cd.solrad) as solrad'),DB::raw('AVG(cd.solevo) as solevo'),DB::raw('AVG(cd.press) as press'),DB::raw('AVG(cd.soiltemp1) as soiltemp1'),DB::raw('AVG(cd.soiltemp2) as soiltemp2'),DB::raw('AVG(cd.soiltemp3) as soiltemp3'),DB::raw('AVG(cd.soiltemp4) as soiltemp4'),DB::raw('AVG(cd.soilhum1) as soilhum1'),DB::raw('AVG(cd.soilhum2) as soilhum2'),DB::raw('AVG(cd.soilhum3) as soilhum3'),DB::raw('AVG(cd.soilhum4) as soilhum4'),DB::raw('AVG(cd.leaftemp1) as leaftemp1'),DB::raw('AVG(cd.leaftemp2) as leaftemp2'),DB::raw('AVG(cd.leaftemp3) as leaftemp3'),DB::raw('AVG(cd.leaftemp4) as leaftemp4'),DB::raw('AVG(cd.leafhum1) as leafhum1'),DB::raw('AVG(cd.leafhum2) as leafhum2'),DB::raw('AVG(cd.leafhum3) as leafhum3'),DB::raw('AVG(cd.leafhum4) as leafhum4'),DB::raw('AVG(cd.dewptout) as dewptout'))->get();
                
                
                $this->result->map(function($item){
                    if($item->windspeed==null && $item->windspeedhi==null){
                        $item->windspeed=$item->windspeed2;
                        $item->windspeedhi=$item->windspeedhi2;
                    }
                    if($item->dewptout!=null && $item->tempout!=null && $item->press!=null && $item->dewptout!=null){
                        $value = (6.11 * pow(10,(7.5 *$item->dewptout / (237.7 + $item->dewptout))));
                        $bulboHumedo = (((0.00066 * $item->press) *  $item->tempout) + ((4098 * $value) / pow(($item->dewptout + 237.7) , 2) * $item->dewptout)) / ((0.00066 *  $item->press ) + (4098 * $value) / pow(($item->dewptout + 237.7) , 2));                        
                        $detalT = $item->tempout - round($bulboHumedo,3);
                        $item->deltat = (string)$detalT;                        
                        return $item;                                
                    }else{
                        $item->deltat = null;
                        return $item;
                    }                 
                });
                
                break;
            
            default:
                $this->resultMaxMin = null;
                $this->result = null;
                break;        
        }
        /*
        if($this->resultMaxMin!==null){
            $this->resultMaxMin->wind_sensors = 'all';
        } */       
    }    
}
