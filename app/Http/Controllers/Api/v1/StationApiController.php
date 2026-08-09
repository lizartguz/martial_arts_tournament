<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use App\Models\AlarmStoreM;
use App\Models\AlertStoreM;
use App\Models\NotificationM;
use App\Models\CurrentDataM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use App\Models\ForecastM;
use App\Livewire\Station\Station;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class StationApiController extends Controller
{
    public $favoriteStation = []; 
    public $startDate;
    public $endDate;

    public function getStation(Request $request){
        $station = [];
        
        try {

            DB::beginTransaction();
            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);            

            if($userPermission[0]=='yes'){            
                FavoriteStationM::OrderBy('id')->where('user_id',$request->userId)
                ->lazy()->each(function (object $value) {                         
                     $this->favoriteStation[] = $value->station_id; 
                 });

                switch ($request->variable) {
                    case 'location':
                        $station = StationM::Where('stations.state',1)
                        ->join('current_data as c','c.station_id','stations.id')
                        ->orderBy('id','desc')->select('stations.id','stations.name','stations.location','stations.latitude','stations.longitude','c.tempout','c.windspeed','c.winddir','c.windspeedhi','c.rainrate','c.raintotal','c.receipt_date','c.humout','c.dewptout','c.press')/*->limit(10)*/->get();
                        break;
                    case 'temperature' || 'rain' || 'wind' || 'humidity':
                        $station = StationM::Where('stations.state',1)
                        ->join('current_data as c','c.station_id','stations.id')
                        ->leftJoin('favorite_stations as f','f.station_id','stations.id')
                        ->orderBy('stations.id','desc')->select('stations.id','stations.name','stations.location','stations.latitude','stations.longitude','c.tempout','c.tempoutmin','c.tempoutmax','c.windspeed','c.winddir','c.windspeedhi','c.rainrate','c.raintotal','c.receipt_date','c.humout','c.dewptout','c.press', DB::raw("CASE WHEN f.station_id IN ('" . implode("','", $this->favoriteStation) . "') THEN True ELSE False END as is_favorite_station"))->get();
                        break;                   
                    
                    default:
                    $station = StationM::Where('stations.state',1)
                    ->join('current_data as c','c.station_id','stations.id')
                    ->leftJoin('favorite_stations as f','f.station_id','stations.id')
                    ->orderBy('stations.id','desc')->select('stations.id','stations.name','stations.location','stations.latitude','stations.longitude','c.tempout','c.tempoutmin','c.tempoutmax','c.windspeed','c.winddir','c.windspeedhi','c.rainrate','c.raintotal','c.receipt_date','c.humout','c.dewptout','c.press', DB::raw("CASE WHEN f.station_id IN ('" . implode("','", $this->favoriteStation) . "') THEN True ELSE False END as is_favorite_station"))->get();
                        break;
                }

                $max_precipitation = CurrentDataM::Where('state',1)->max('raintotal');
                $max_wind = CurrentDataM::Where('state',1)->max('windspeed');            
                
                return response()->json([
                    "response" => true, 
                    "data" => $station,
                    "failed" => 'no',
                    "max_precipitation" => $max_precipitation, 
                    "max_wind" => $max_wind, 
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
                    "max_precipitation" => null,
                    "max_wind" => null,  
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
                "max_precipitation" => null, 
                "max_wind" => null,  
                "data_user" => null, 
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    }

    public function getStationMap(Request $request){
       // $stations = StationM::where('stations.state',1)->select('id','name')->get();
        //return $stations;
        try {
            DB::beginTransaction();
            $result = null; 
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);            
            if($userPermission[0]=='yes'){  
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                FavoriteStationM::OrderBy('id')->where('user_id',$request->userId)
                ->lazy()->each(function (object $value) {                         
                     $this->favoriteStation[] = $value->station_id; 
                 });
                $this->startDate = null;
                $this->endDate = null;
                $filter = $request->weatherTimeFilter;                
                switch ($filter) {
                    case 'Ahora':                                                        
                        break;
                    case 'Última hora':
                        $fechaActual = new DateTime();
                        $this->endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-1 hour');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');                                
                        break;
                    case 'Últimas 12 horas':
                        $fechaActual = new DateTime();
                        $this->endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-12 hours');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');
                        break;
                    case 'Últimas 24 horas':
                        $fechaActual = new DateTime();
                        $this->endDate  = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-24 hours');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');
                        break;
                    case 'Últimos 7 días':
                        $fechaActual = new DateTime();
                        $this->endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-7 days');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');
                        break;
                    case 'Últimos 30 días':
                        $fechaActual = new DateTime();
                        $this->endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-30 days');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');                   
                        break;
                    case 'Últimos 3 meses':
                        $fechaActual = new DateTime();
                        $this->endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-3 months');
                        $this->startDate = $fechaActual->format('Y-m-d H:i:s');                       
                        break;
                    default:
                        $result = null; 
                        break;
                }
                
                if($request->weatherTimeFilter=='Ahora'){
                    $result = StationM::Join('current_data as cd','stations.id','cd.station_id')                              
                    ->leftJoin('favorite_stations as f','f.station_id','=','stations.id')                    
                    ->orderBy('raintotal','asc')                    
                    ->groupBy('stations.id', 'stations.name', 'stations.location', 'stations.address', 'stations.latitude', 'stations.longitude', 'f.station_id')
                    ->where('stations.state',1)
                    /*->whereRaw("TIMESTAMPDIFF(HOUR, cd.receipt_date, NOW()) <= 6")*/                    
                    ->select('stations.id', 'stations.name', 'stations.location', 'stations.address', 'stations.latitude', 'stations.longitude','cd.receipt_date',DB::raw('AVG(cd.tempout) as tempout'),DB::raw('MIN(cd.tempoutmin) as tempoutmin'),DB::raw('MAX(cd.tempoutmax) as tempoutmax'),DB::raw('MAX(cd.raintotal) as raintotal'),DB::raw('AVG(cd.rainrate) as rainrate'),DB::raw('AVG(cd.windspeed) as windspeed'),DB::raw('AVG(cd.windspeedhi) as windspeedhi'),DB::raw('AVG(cd.winddir) as winddir'),DB::raw('AVG(cd.windspeed2) as windspeed2'),DB::raw('AVG(cd.windspeedhi2) as windspeedhi2'),DB::raw('AVG(cd.winddir2) as winddir2'),DB::raw("CASE WHEN f.station_id IN ('" . implode("','", $this->favoriteStation) . "') THEN True ELSE False END as is_favorite_station"),
                    DB::raw('false as visibledate'))->distinct('stations.id')->get();
                    $max_precipitation = CurrentDataM::Where('state',1)->max('raintotal');
                    $max_wind = CurrentDataM::Where('state',1)->max('windspeed');
                    $max_wind2 = CurrentDataM::Where('state',1)->max('windspeed2');
                    if($max_wind < $max_wind2){
                        $max_wind = $max_wind2;
                    }

                }else{

                    $stations = StationM::where('stations.state',1) 
                    ->Join('current_data as cd','stations.id','cd.station_id')                   
                    ->leftJoin('favorite_stations as f','f.station_id','=','stations.id')   
                    ->whereRaw("TIMESTAMPDIFF(HOUR, cd.receipt_date, NOW()) <= 6")  
                    ->orderBy('cd.raintotal','asc')                                
                    ->select('stations.id', 'stations.name', 'stations.location', 'stations.address', 'stations.latitude', 'stations.longitude',DB::raw("CASE WHEN f.station_id IN ('" . implode("','", $this->favoriteStation) . "') THEN True ELSE False END as is_favorite_station"),DB::raw('false as visibledate'),'cd.raintotal')/*->orderBy('stations.id','asc')*/->distinct('s.id')->get();            
                    $ids = StationM::join('current_data as cd','stations.id','cd.station_id') 
                    ->where('stations.state',1)
                    ->whereRaw("TIMESTAMPDIFF(HOUR, cd.receipt_date, NOW()) <= 6")
                    ->orderBy('cd.raintotal','desc')
                    ->pluck('stations.id')->toArray();
                    
                    $year = date('Y');
                    $connection = 'senvatec_db_' . $year;

                    $result = DB::connection($connection)->table('data')
                        ->select(
                            'station_id',
                            DB::raw('AVG(tempout) as tempout'),
                            DB::raw('MIN(tempoutmin) as tempoutmin'),
                            DB::raw('MAX(tempoutmax) as tempoutmax'),
                            DB::raw('AVG(windspeed) as windspeed'),
                            DB::raw('MAX(windspeedhi) as windspeedhi'),
                            DB::raw('AVG(winddir) as winddir'), 
                            DB::raw('AVG(windspeed2) as windspeed2'),
                            DB::raw('MAX(windspeedhi2) as windspeedhi2'),
                            DB::raw('AVG(winddir2) as winddir2'), 
                            DB::raw('AVG(rainrate) as rainrate'), 
                            DB::raw('SUM(max_daily_rain) as raintotal') 
                        )
                        ->fromSub(function ($query) use ($ids) {
                            $query->from('data')
                                ->select(
                                    'station_id',
                                    DB::raw('DATE(receipt_date) as day'),
                                    DB::raw('MAX(raintotal) as max_daily_rain'), 
                                    DB::raw('AVG(tempout) as tempout'),
                                    DB::raw('MIN(tempoutmin) as tempoutmin'),
                                    DB::raw('MAX(tempoutmax) as tempoutmax'),
                                    DB::raw('AVG(windspeed) as windspeed'),
                                    DB::raw('MAX(windspeedhi) as windspeedhi'),
                                    DB::raw('AVG(winddir) as winddir'), 
                                    DB::raw('AVG(windspeed2) as windspeed2'),
                                    DB::raw('MAX(windspeedhi2) as windspeedhi2'),
                                    DB::raw('AVG(winddir2) as winddir2'), 
                                    DB::raw('AVG(rainrate) as rainrate'),
                                )
                                ->whereIn('station_id', $ids)    
                                //->where('station_id', 22)                       
                                ->whereBetween('receipt_date', [$this->startDate, $this->endDate])
                                ->groupBy('station_id', DB::raw('DATE(receipt_date)'));
                        }, 'daily_data')
                        ->groupBy('station_id')
                        ->get();

                    $aggregatedData = [];
                    foreach ($result as $row) {
                        $aggregatedData[$row->station_id] = $row;
                    }

                    $result = $stations->map(function ($station) use ($aggregatedData) {
                        $stationId = $station->id;                        
                        
                        // Verificar si hay datos agregados para esta estación
                        if (isset($aggregatedData[$stationId])) {
                            $station->tempout = $aggregatedData[$stationId]->tempout;
                            $station->tempoutmin = $aggregatedData[$stationId]->tempoutmin;
                            $station->tempoutmax = $aggregatedData[$stationId]->tempoutmax;                            
                            if(($aggregatedData[$stationId]->windspeed!==null || $aggregatedData[$stationId]->windspeedhi!==null)){
                                $station->windspeed = $aggregatedData[$stationId]->windspeed ?? null;
                                $station->windspeedhi = $aggregatedData[$stationId]->windspeedhi ?? null;
                                $station->winddir = $aggregatedData[$stationId]->winddir ?? null;
                            }else{
                                $station->windspeed = $aggregatedData[$stationId]->windspeed2 ?? null;
                                $station->windspeedhi = $aggregatedData[$stationId]->windspeedhi2 ?? null;
                                $station->winddir = $aggregatedData[$stationId]->winddir2 ?? null;
                            }
                            $station->rainrate = $aggregatedData[$stationId]->rainrate;
                            $station->raintotal = $aggregatedData[$stationId]->raintotal;
                            
                        } else {
                            // Si no hay datos para esta estación, definir valores nulos o predeterminados
                            $station->tempout = null;
                            $station->tempoutmin = null;
                            $station->tempoutmax = null;
                            $station->windspeed = null;
                            $station->windspeedhi = null;
                            $station->winddir = null;                            
                            $station->windspeed2 = null;
                            $station->windspeedhi2 = null;
                            $station->winddir2 = null;
                            $station->rainrate = null;
                            $station->raintotal = null;                            
                        }
                        // $station->station = true;
                        
                        return $station;
                    }); 
                                  
                    $max_precipitation = DB::connection($connection)->table('data')->whereBetween('receipt_date', [$this->startDate, $this->endDate])->whereIn('station_id', $ids)->max('raintotal');
                    $max_wind = DB::connection($connection)->table('data')->whereBetween('receipt_date', [$this->startDate, $this->endDate])->whereIn('station_id', $ids)->max('windspeed');
                    $max_wind2 = DB::connection($connection)->table('data')->whereBetween('receipt_date', [$this->startDate, $this->endDate])->whereIn('station_id', $ids)->max('windspeed2');
                    
                    if($max_wind < $max_wind2){
                        $max_wind = $max_wind2;
                    }
                }  
                
                $stationDataItem = null;
                if(isset($request->stationId)){
                    if($request->stationId!=null || $request->stationId!=''){
                        $stationDataItem = StationM::where('state',1)->where('id',$request->stationId)->select('id', 'name', 'location', 'address', 'latitude', 'longitude')->first();
                    }                     
                }

                return response()->json([
                    "response" => true, 
                    "stationDataItem" => $stationDataItem,                            
                    "stationData" => $result,                    
                    "max_precipitation" => $max_precipitation,
                    "max_wind" => $max_wind,                  
                    "failed" => 'no',    
                    "data_user" => $userPermission[3],                 
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);         

            }else{
                return response()->json([
                    "response" => false, 
                    "stationDataItem" => null,
                    "stationData" => null,
                    "failed" => 'no',
                    "max_precipitation" => null,
                    "max_wind" => null,
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
                "stationDataItem" => null,
                "stationData" => null,
                "failed"=> $th->getMessage(),
                "max_precipitation" => null, 
                "max_wind" => null,   
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,      
            ]);
        }
    }

    public function getHomeStation(Request $request){
        date_default_timezone_set('America/La_Paz');
        $station = [];
        try {
            DB::beginTransaction();

            $page = (int) ($request->input('page', 1));
            $perPage = (int) ($request->input('perPage', 20));
            if ($page < 1) {
                $page = 1;
            }
            if ($perPage < 1) {
                $perPage = 20;
            }
            if ($perPage > 100) {
                $perPage = 100;
            }

            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $user = null;
                $cad = '';
                $user = User::where('id',$request->userId)->select('id','username','name','lastname','email','number_phone','device_identifier','version_app','is_update')->first();
                $isUpdate=$user->is_update;
                if ($request->has('versionApp')) {
                    if($request->fcmToken!==''){                        
                        $user->update(['version_app' => $request->versionApp ]);
                        $this->syncMobileFcmToken($request, (int) $request->userId);
                        DB::commit();                        
                    }else{                        
                        $user->update(['version_app' => $request->versionApp ]);
                        DB::commit();                        
                    }
                }else{
                    if($request->fcmToken!==''){                        
                        $this->syncMobileFcmToken($request, (int) $request->userId);
                        DB::commit();                        
                    }
                }
                
                
                $cad = 'user_id';            
                $station = null;
                $baseQuery = StationM::where('stations.state', 1)
                    ->join('current_data as c','c.station_id','stations.id')
                    ->select(
                        'stations.id',
                        'stations.name',
                        'stations.search_concatenation',
                        'stations.address',
                        'stations.location',
                        'stations.latitude',
                        'stations.longitude',
                        'c.tempout',
                        'c.windspeed',
                        'c.winddir',
                        'c.windspeedhi',
                        'c.rainrate',
                        'c.raintotal',
                        'c.receipt_date',
                        'c.humout',
                        'c.dewptout',
                        'c.press'
                    )
                    ->orderBy('stations.id','desc');

                $total = (clone $baseQuery)->count();

                $station = (clone $baseQuery)
                    ->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();

                $hasMore = ($page * $perPage) < $total;
                $nextPage = $hasMore ? $page + 1 : null;
                /*$alerts = [];                

                $alarmU = AlarmStoreM::join('stations as s','s.id','alarm_store.station_id')
                    ->where('user_id',$request->userId)->select('alarm_store.reg_date','alarm_store.value','alarm_store.variable','alarm_store.unit','alarm_store.state','s.id as station_id','s.name','s.location','s.address')->orderBy('alarm_store.reg_date','DESC')->limit(4)->get();

                $alertU = AlertStoreM::join('stations as s','s.id','alert_store.station_id')
                    ->where('user_id',$request->userId)->select('alert_store.reg_date','alert_store.value','alert_store.variable','alert_store.unit','alert_store.state','s.id as station_id','s.name','s.location','s.address')->orderBy('alert_store.reg_date','DESC')->limit(4)->get();

                $notifications = NotificationM::where('state',1)->where('deadline', '<=', date('Y-m-d'))->select('title','description','link','state','reg_date')->orderBy('id','DESC')->limit(3)->get();
                
                return response()->json([
                    "response" => true,
                    "data_station" => $station,
                    "alerts" => count($alerts)>0?$alerts:null,
                    "alarmU" => count($alarmU)>0?$alarmU:null,
                    "alertU" => count($alertU)>0?$alertU:null,
                    "notifications" => count($notifications)>0?$notifications:null,
                    "data_user" => $userPermission[3],
                    "failed" => 'no', 
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                ]); 
                */

                DB::commit();

                return response()->json([
                    "response" => true,
                    "data_station" => $station,                   
                    "data_user" => $userPermission[3],
                    "failed" => 'no', 
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],
                    "current_page" => $page,
                    "per_page" => $perPage,
                    "next_page" => $nextPage,
                    "has_more" => $hasMore,
                    "total" => $total,
                ]);

            }else{
                /*return response()->json([
                    "response" => false,                    
                    "data_station" => null,
                    "alerts" => null,
                    "alarmU" => null,
                    "alertU" => null,
                    "notifications" => null, 
                    "data_user" => null,
                    "failed" => 'no', 
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],  
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                 
                ]); */
                DB::commit();

                return response()->json([
                    "response" => false,                    
                    "data_station" => null,        
                    "data_user" => null,
                    "failed" => 'no', 
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0],  
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                 
                    "current_page" => $page ?? 1,
                    "per_page" => $perPage ?? 20,
                    "next_page" => null,
                    "has_more" => false,
                    "total" => 0,
                ]);
            }                  
                        
        } catch (\Throwable $th) {
            DB::rollBack();
            /*return response()->json([
                "response" => false,                
                "data_station" => null,
                "alerts" => null, 
                "alarmU" => null,
                "alertU" => null,
                "notifications" => null,  
                "data_user" => null,
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);*/
            return response()->json([
                "response" => false,                
                "data_station" => null,              
                "data_user" => null,
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,
                "current_page" => (int) $request->input('page', 1),
                "per_page" => (int) $request->input('perPage', 20),
                "next_page" => null,
                "has_more" => false,
                "total" => 0,
            ]);
        }
    }

    public function getFavoriteStation(Request $request){                
        try {
            DB::beginTransaction();            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $user = null;
                $cad = '';
                
                    $user = User::where('id',$request->userId)->first();
                    $this->syncMobileFcmToken($request, (int) $request->userId);
                    DB::commit();
                    $cad = 'user_id'; 
                
                $variable = '';
                switch ($request->parameter) {                    
                    case 'temperature':
                        $variable = 'tempout';                        
                        break;
                    case 'rain':
                        $variable = 'raintotal';                       
                        break;
                    case 'wind':
                        $variable = 'windspeed';                        
                        break;
                    case 'humidity':
                        $variable = 'humout';                       
                        break;                    
                    default:
                        $variable = 'tempout';
                        break;
                }
                
                $stationFavorite = StationM::Where('stations.state',1)
                    ->join('current_data as c','c.station_id','stations.id')
                    ->join('favorite_stations as fs','fs.station_id','stations.id')
                    ->where('fs.'.$cad,$user->id)
                    ->select('stations.id','stations.name','stations.location','stations.latitude','stations.longitude','c.tempout','c.windspeed','c.winddir','c.windspeedhi','c.rainrate','c.raintotal','c.receipt_date','c.humout','c.dewptout','c.press')->orderBy('c.'.$variable,'desc')->get();
                
                $stationFavoriteIds = StationM::where('stations.state', 1)
                    ->join('current_data as c', 'c.station_id', 'stations.id')
                    ->join('favorite_stations as fs', 'fs.station_id', 'stations.id')
                    ->where('fs.' . $cad, $user->id)
                    ->orderBy('stations.id', 'asc')
                    ->pluck('stations.id')
                    ->toArray(); 

                $forecast = ForecastM::WhereIn('station_id',$stationFavoriteIds)
                    ->join('stations as s','s.id','forecasts.station_id')
                    ->orderBy('forecasts.station_id','asc')
                    ->orderBy('forecasts.reg_date','asc')
                    ->where('forecasts.reg_date','>=',date('Y-m-d').' 00:00:00')
                    ->select('forecasts.id','forecasts.reg_date','forecasts.v10m','forecasts.v10m_dir','forecasts.v850','forecasts.v850_dir','forecasts.prec','forecasts.cape','forecasts.li','forecasts.dam','forecasts.a850','forecasts.a500','forecasts.t2m','forecasts.hr2m','forecasts.t850','forecasts.t500','forecasts.baro','forecasts.cloud','forecasts.snow','forecasts.dp','forecasts.tmax','forecasts.tmin','forecasts.sh_ts','forecasts.icon','forecasts.icon_image','forecasts.min_t2m','forecasts.min_dp','forecasts.state','forecasts.station_id','s.name','s.location','s.address')->get();
               
                    $stationFavorite->map(function ($station) use ($forecast) {
                        $nearestForecast = null;
                        $minDifference = PHP_INT_MAX;
                    
                        foreach ($forecast as $forecastItem) {
                            if ($forecastItem->station_id == $station->id) {
                                $receipt_date = null;
                                $receiptDateVerf = strtotime($station->receipt_date);
                                $today = strtotime('today');
                                if ($receiptDateVerf >= $today && $receiptDateVerf < strtotime('tomorrow')) {
                                    $receipt_date = $station->receipt_date; 
                                } else {
                                    $receipt_date = date('Y-m-d H:i:s');//si la estacion esta caida el pronostico se reajusta al dia actual, forzando a que el pronostico este bien aunque la estacion este caida, permitiendo un icono adecuado
                                }

                                $diff = abs(strtotime($receipt_date) - strtotime($forecastItem->reg_date));
                                if ($diff < $minDifference) {
                                    $minDifference = $diff;
                                    $nearestForecast = $forecastItem;
                                }
                            }
                        }                    
                        
                        if ($nearestForecast) {
                            $icon_background = $this->icono($nearestForecast->reg_date, $nearestForecast->t2m, $nearestForecast->prec, $nearestForecast->li, $nearestForecast->cloud);
                            $station->icon_background = $icon_background; 
                        } else {
                            // Manejo en caso de que no haya un pronóstico cercano
                            $station->icon_background = null; 
                        }
                    
                        return $station;
                    });

                return response()->json([
                    "response" => true, 
                    "data_favorite_station" => $stationFavorite,                    
                    "failed" => 'no', 
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]); 

            }else{
                return response()->json([
                    "response" => false, 
                    "data_favorite_station" => null,                    
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
                "data_favorite_station" => null,                
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    }

    public function getOnlyFavoriteStation(Request $request){
        $station = [];
        
        try {
            DB::beginTransaction();            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();
                $variable = '';
                switch ($request->parameter) {                    
                    case 'temperature':
                        $variable = 'tempout';                        
                        break;
                    case 'rain':
                        $variable = 'raintotal';                       
                        break;
                    case 'wind':
                        $variable = 'windspeed';                        
                        break;
                    case 'humidity':
                        $variable = 'humout';                       
                        break;                    
                    default:
                        $variable = 'tempout';
                        break;
                }
                
                $stationFavorite = StationM::Where('stations.state',1)
                    ->join('current_data as c','c.station_id','stations.id')
                    ->join('favorite_stations as fs','fs.station_id','stations.id')
                    ->where('fs.user_id',$user->id)
                    ->select('stations.id','stations.name','stations.location','stations.latitude','stations.longitude','c.tempout','c.windspeed','c.winddir','c.windspeedhi','c.rainrate','c.raintotal','c.receipt_date','c.humout','c.dewptout','c.press')->orderBy('c.'.$variable,'desc')->get();               

                return response()->json([
                    "response" => true, 
                    "data_favorite_station" => $stationFavorite,                   
                    "failed" => 'no', 
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]); 

            }else{
                return response()->json([
                    "response" => false, 
                    "data_favorite_station" => null,                   
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
                "data_favorite_station" => null,               
                "failed"=> $th->getMessage(), 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,      
            ]);
        }
    }

    public function addFavoriteStation(Request $request){
        $station = [];
        
        try {
            DB::beginTransaction();            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);            

            if($userPermission[0]=='yes'){            
                if(FavoriteStationM::Where('station_id',$request->stationId)->where('user_id',$request->userId)->exists()){
                    return response()->json([
                        "response" => 'it already exists', 
                        "error" => 'no', 
                        "detailsError" =>'',
                        "data_user" => $userPermission[3],
                        "user_permission" => $userPermission[0], 
                        "permit_detail" => $userPermission[1],
                        "type_subscriptions" => $userPermission[2],               
                    ]); 
                }else{

                    FavoriteStationM::create([
                        'station_id' => $request->stationId,
                        'user_id'=> $request->userId,
                        'state'=> 1,
                    ]);
                    DB::commit();

                    return response()->json([
                        "response" => 'registered', 
                        "error" => 'no', 
                        "detailsError" =>'',
                        "data_user" => $userPermission[3],
                        "user_permission" => $userPermission[0], 
                        "permit_detail" => $userPermission[1],
                        "type_subscriptions" => $userPermission[2],                
                    ]);
                }      

            }else{
                return response()->json([
                    "response" => '', 
                    "detailsError" =>'',
                    "error" => 'no', 
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
                "response" => 'error catch', 
                "detailsError" => $th->getMessage(),
                "error" => 'yes', 
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,     
            ]);
        }
    }

    public function getConcatenationName(Request $request){
        $station = [];
        try {
            DB::beginTransaction();            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $user = null;
                $cad = '';
                $user = User::where('id',$request->userId)->first();
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();                
                               
                $station = StationM::Where('stations.state',1)->select('stations.id','stations.name','stations.location','stations.address','stations.search_concatenation',)->orderBy('stations.name','asc')->get();

                return response()->json([
                    "response" => true,                   
                    "data" => $station,
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

    public function removeStation(Request $request){
        try {
            DB::beginTransaction();            
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $user = User::where('id',$request->userId)->first();
                $this->syncMobileFcmToken($request, (int) $request->userId);
                
                FavoriteStationM::where('user_id','=',$request->userId)->where('station_id','=',$request->stationId)->delete();
                DB::commit();

                return response()->json([
                    "response" => true, 
                    "error" => 'no', 
                    "detailsError" =>'',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],                
                ]);

            }else{
                return response()->json([
                    "response" => false, 
                    "detailsError" =>'',
                    "error" => 'no', 
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
                "detailsError" => $th->getMessage(),
                "error" => 'yes', 
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
