<?php

namespace App\Http\Controllers\Api\v1;

use DateTime;
use App\Models\User;
use App\Models\StationM;
use App\Models\CurrentDataM;
use Illuminate\Http\Request;
use App\Models\FavoriteStationM;
use App\Models\UsersSubscriptionM;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CropAlertApiController extends Controller
{
    
    public $result;
    public $resultITH;
    public $receipt_date_min_roya_de_la_cana = null;
    public $receipt_date_max_roya_de_la_cana = null;
    public $value_roya_de_la_cana_min = null;
    public $value_roya_de_la_cana_max = null;

    public $receipt_date_min_roya_de_la_soja = null;
    public $receipt_date_max_roya_de_la_soja = null;
    public $value_roya_de_la_soja_min = null;
    public $value_roya_de_la_soja_max = null;

    public $receipt_date_min_mancha_marron = null;
    public $receipt_date_max_mancha_marron = null;
    public $value_mancha_marron_min = null;
    public $value_mancha_marron_max = null;

    public $receipt_date_min_tizon_de_la_hoja = null;
    public $receipt_date_max_tizon_de_la_hoja = null;
    public $value_tizon_de_la_hoja_min = null;
    public $value_tizon_de_la_hoja_max = null;

    public $receipt_date_min_mancha_anillada_de_la_hoja = null;
    public $receipt_date_max_mancha_anillada_de_la_hoja = null;
    public $value_mancha_anillada_de_la_hoja_min = null;
    public $value_mancha_anillada_de_la_hoja_max = null;

    public $receipt_date_min_tizon_bacteriano = null;
    public $receipt_date_max_tizon_bacteriano = null;
    public $value_tizon_bacteriano_min = null;
    public $value_tizon_bacteriano_max = null;

    public function getCropAlertMap(Request $request){         
        $this->result = null;       
        try {
            $startDate = null;
            $endDate = null;
            date_default_timezone_set('America/La_Paz');
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                $this->syncMobileFcmToken($request, (int) $request->userId);
                DB::commit();             
                $filter = $request->timeFilter;                
                switch ($filter) {
                   case 'Últimas 24 horas':
                        $fechaActual = new DateTime();
                        $endDate  = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-24 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                     
                        break;
                    case 'Últimos 7 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-7 days');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                       
                        break;
                    case 'Últimos 30 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-30 days');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');                        
                        break;
                    case 'Últimos 3 meses':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-3 months');
                        $startDate = $fechaActual->format('Y-m-d H:i:s'); 
                                       
                        break;
                    default:                        
                        $this->result = null; 
                        break;
                }  
               $this->getSqlData($startDate, $endDate,$filter,$request->cropTypeFilter);           
                            
               /*$ids0 = DB::connection('db_current_year')->table('data as d')
               ->select('d.station_id')                    
               ->whereBetween('d.receipt_date', [$startDate,$endDate])
               //->whereRaw("TIMESTAMPDIFF(HOUR, cd.receipt_date, NOW()) <= 6")
               ->whereNotNull('d.leafhum1')
               ->distinct()
               ->orderBy('d.station_id','asc')->pluck('station_id')->toArray();
   
                $ids = DB::connection('db_current_year')->table('data as d')
                            ->select('d.station_id')                    
                            ->whereRaw("TIMESTAMPDIFF(HOUR, d.receipt_date, NOW()) <= 6")
                            ->whereIn('d.station_id', $ids0)
                            ->distinct()
                            ->orderBy('d.station_id','asc')->pluck('station_id')->toArray();*/
                            
                return response()->json([
                    "response" => true,
                    //"ids" => $ids,
                    "stationAlertCultivoData" =>  $this->result,
                    "ITHData" => $this->resultITH,
                    "failed" => 'no',
                    "data_user" => $userPermission[3],
                    "user_permission" => $userPermission[0], 
                    "permit_detail" => $userPermission[1],
                    "type_subscriptions" => $userPermission[2],               
                ]);
            }else{
                return response()->json([
                    "response" => false,
                    "stationAlertCultivoData" => null,
                    "ITHData" => null,
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
                "ITHData" => null,
                "stationAlertCultivoData" => null,
                "failed"=> $th->getMessage(),
                "data_user" => null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,              
            ]);
        }
    }
    
    private function getSqlData($startDate,$endDate,$type,$cropTypeFilter){
        $this->resultITH = null;
        if($cropTypeFilter=='Índice de Temperatura-Humedad (ITH)'){
           
            $ids = StationM::join('current_data as cd','cd.station_id','stations.id')
                    ->select('stations.id')                    
                    ->whereRaw("TIMESTAMPDIFF(HOUR, cd.receipt_date, NOW()) <= 6")
                    ->distinct()
                    ->orderBy('stations.id','asc')->pluck('stations.id')->toArray(); 
            
            $estaciones = DB::table('stations')
                    ->whereIn('id', $ids)
                    ->get(['id', 'name', 'address', 'location', 'latitude', 'longitude']);
            
                
            $estacionesArray = $estaciones->keyBy('id')->map(function ($estacion) {
                return [
                    'id' => $estacion->id,
                    'name' => $estacion->name,
                    'address' => $estacion->address,
                    'location' => $estacion->location,
                    'latitude' => $estacion->latitude,
                    'longitude' => $estacion->longitude,
                ];
            });
            
            if($type == 'Últimas 24 horas'){
                
                $ITHs =  DB::connection('db_current_year')->table('data as d')
                    ->whereIn('d.station_id', $ids)
                    ->select('d.station_id',
                        DB::raw('
                            CASE 
                                WHEN AVG(d.tempout) IS NULL OR AVG(d.humout) IS NULL THEN NULL
                                ELSE ROUND(
                                    (
                                        ((1.8 * AVG(d.tempout)) + 32)
                                        - ((0.55 - (0.55 * AVG(d.humout))) / 100)
                                        * ((1.8 * AVG(d.tempout)) - 26)
                                    ), 
                                    0
                                )
                            END as ith
                        ')
                    )
                    ->whereBetween('d.receipt_date', [$startDate, $endDate])
                    ->groupBy('d.station_id')
                    ->get();


                $ITHs = $ITHs->reject(function ($value) use ($estacionesArray) {
                    return !isset($estacionesArray[$value->station_id]);
                });

                $ITHs = $ITHs->filter(function ($value) {
                    return $value->ith !== null;
                });

                foreach ($ITHs as $value) {
                    $stationData = $estacionesArray[$value->station_id] ?? null;
                    if ($stationData) {
                        $value->name = $stationData['name'] ?? '';
                        $value->address = $stationData['address'] ?? '';
                        $value->location = $stationData['location'] ?? '';
                        $value->latitude = $stationData['latitude'] ?? '';
                        $value->longitude = $stationData['longitude'] ?? '';
                    } else {
                        $value->name = '';
                        $value->address = '';
                        $value->location = '';
                        $value->latitude = '';
                        $value->longitude = '';
                    }
                }
                $this->resultITH = $ITHs;

            }else{
                
                if($type == 'Últimos 7 días'){

                    $ITHs = DB::connection('db_current_year')->table(DB::raw('
                        (SELECT 
                            d.station_id, 
                            DATE(d.receipt_date) as fecha, 
                            AVG(d.tempout) as temp_promedio, 
                            AVG(d.humout) as hum_promedio
                        FROM data as d
                        WHERE d.station_id IN (' . implode(',', $ids) . ') 
                        AND d.receipt_date BETWEEN "' . $startDate . '" AND "' . $endDate . '"
                        GROUP BY d.station_id, DATE(d.receipt_date)) AS subquery
                    '))
                    ->select(
                        'station_id',
                        DB::raw('AVG(temp_promedio) as temp_promedio_final'),
                        DB::raw('AVG(hum_promedio) as hum_promedio_final')
                    )
                    ->groupBy('station_id')
                    ->get();

                    foreach ($ITHs as $item) {
                        $promedio_temp = $item->temp_promedio_final;
                        $promedio_hum = $item->hum_promedio_final;                
                        
                        if ($promedio_temp !== null && $promedio_hum !== null) {
                            $ith_promedio = round(((1.8 * $promedio_temp) + 32) - ((0.55 - (0.55 * $promedio_hum)) / 100) * ((1.8 + $promedio_temp) - 26), 0);
                        } else {
                            $ith_promedio = null;
                        }                
                        
                        $item->ith = $ith_promedio;
                    }

                    $ITHs = $ITHs->reject(function ($value) use ($estacionesArray) {
                        return !isset($estacionesArray[$value->station_id]);
                    });
    
                    $ITHs = $ITHs->filter(function ($value) {
                        return $value->ith !== null;
                    });
    
                    foreach ($ITHs as $value) {
                        $stationData = $estacionesArray[$value->station_id] ?? null;
                        if ($stationData) {
                            $value->name = $stationData['name'] ?? '';
                            $value->address = $stationData['address'] ?? '';
                            $value->location = $stationData['location'] ?? '';
                            $value->latitude = $stationData['latitude'] ?? '';
                            $value->longitude = $stationData['longitude'] ?? '';
                        } else {
                            $value->name = '';
                            $value->address = '';
                            $value->location = '';
                            $value->latitude = '';
                            $value->longitude = '';
                        }
                        unset($value->temp_promedio_final);
                        unset($value->hum_promedio_final);
                    }

                    $this->resultITH = $ITHs;

                }
            }
        }else{        
            $listIds = [];
            $ids0 = DB::connection('db_current_year')->table('data as d')
                        ->select('d.station_id')                    
                        ->whereBetween('d.receipt_date', [$startDate,$endDate])
                        ->where(function($query) {
                            $query->orWhereNotNull('d.soiltemp1')
                                ->orWhereNotNull('d.soiltemp2')
                                ->orWhereNotNull('d.soiltemp3')
                                ->orWhereNotNull('d.soiltemp4')
                                ->orWhereNotNull('d.soilhum1')
                                ->orWhereNotNull('d.soilhum2')
                                ->orWhereNotNull('d.leafhum3')
                                ->orWhereNotNull('d.leafhum4')
                                ->orWhereNotNull('d.leaftemp1')
                                ->orWhereNotNull('d.leaftemp2')
                                ->orWhereNotNull('d.leaftemp3')
                                ->orWhereNotNull('d.leaftemp4')
                                ->orWhereNotNull('d.leafhum1')
                                ->orWhereNotNull('d.leafhum2')
                                ->orWhereNotNull('d.leafhum3')
                                ->orWhereNotNull('d.leafhum4');
                        })
                        ->distinct()
                        ->orderBy('d.station_id','asc')->pluck('station_id')->toArray();
            
            $ids = DB::connection('db_current_year')->table('data as d')
                                ->select('d.station_id')                    
                                ->whereRaw("TIMESTAMPDIFF(HOUR, d.receipt_date, NOW()) <= 6")
                                ->whereIn('d.station_id', $ids0)
                                ->distinct()
                                ->orderBy('d.station_id','asc')->pluck('station_id')->toArray();       
            
            $stations = StationM::where('stations.state',1)
                ->whereIn('stations.id', $ids)
                ->select('stations.id as station_id', 'stations.name', 'stations.location', 'stations.address', 'stations.latitude', 'stations.longitude')->orderBy('stations.id','asc')->distinct('stations.id')->get();
            

            foreach($stations as $item){
                $listIds[]  = $item->station_id;
            }        
        
            $this->result = DB::connection('db_current_year')->table('data as d')
                    ->whereIn('d.station_id', $listIds)
                    ->whereBetween('d.receipt_date', [$startDate, $endDate])
                    ->groupBy('d.station_id')
                    ->orderBy('d.station_id')
                    ->select(
                        'd.station_id',                  
                        DB::raw('AVG(d.tempout) as tempout'),
                        DB::raw('MIN(d.tempoutmin) as tempoutmin'),
                        DB::raw('MAX(d.tempoutmax) as tempoutmax'),
                        DB::raw('AVG(d.raintotal) as raintotal'),
                        DB::raw('AVG(d.windspeed) as windspeed'),
                        DB::raw('MAX(d.windspeedhi) as windspeedhi'),
                        DB::raw('AVG(d.humout) as humout'),
                        DB::raw('COUNT(d.rainrate) as rainrate'),
                        DB::raw('AVG(d.leaftemp1) as leaftemp1'),
                        DB::raw('AVG(d.leaftemp1) as total_leaftemp1'),
                        DB::raw('SUM(CASE WHEN d.leafhum1 > 0 THEN 1 ELSE 0 END) as leafhum1_count'),                   
                        DB::raw('SUM(CASE WHEN d.humout >= 90 THEN 1 ELSE 0 END) as humout_count'),
                        DB::raw('SUM(CASE WHEN d.humout >= 70 THEN 1 ELSE 0 END) as humout_count_tizon_hoja'),
                        DB::raw('SUM(CASE WHEN d.humout >= 80 THEN 1 ELSE 0 END) as humout_count_mancha_anillada'),
                        DB::raw('SUM(CASE WHEN d.windspeed > 0 THEN 1 ELSE 0 END) as windspeed_count'),
                        DB::raw('SUM(CASE WHEN d.rainrate > 0 THEN 1 ELSE 0 END) as rainrate_count')                     
                    )->get();
                    
            
            switch ($cropTypeFilter) {
                case 'Roya de la Caña':           
                    $this->result->map(function($item){
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->humout_count;
                        $Horas90HR = (($CantDatosCond*$item->humout)/144)/6;
                        $riesgo = 'NO';
                        if (($HorasMojado>=8) && (($Horas90HR>=90) && ($item->tempout>=17) && ($item->tempout<=23))) { 
                            $riesgo = 'SI';
                        }
                        switch ($riesgo) {
                            case 'SI': 
                                $item->color  = 'danger';
                            break;
                            case 'NO': 
                                $item->color  = 'success';
                            break;
                        }
                        return $item;            
                    });
                    break;
                case 'Roya de la Soja':                
                    $this->result->map(function($item){
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->humout_count;
                        $Horas90HR = (($CantDatosCond*$item->humout)/144)/6;
                        $riesgo = 'BAJO';
                        if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>=19) && ($item->leaftemp1<=24))) { $riesgo = 'ALTO';}
                        if (($HorasMojado>12) && (($item->leaftemp1>=11) && ($item->leaftemp1<=28))) { 
                            $riesgo = 'ALTO';}
                        if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>=11) && ($item->leaftemp1<19))) { $riesgo = 'MODERADO';}
                        if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>24) && ($item->leaftemp1<=28))) { $riesgo = 'MODERADO';}
                        if (($HorasMojado>6) && (($item->leaftemp1<11) || ($item->leaftemp1>28))) { 
                            $riesgo = 'LIGERO';}
                        switch ($riesgo) {
                            case 'ALTO': $item->color = 'danger';
                            break;
                            case 'MODERADO': $item->color = 'warning';
                            break;
                            case 'LIGERO': $item->color = 'info';
                            break;
                            case 'BAJO': $item->color = 'success';
                            break;
                        }
                                        
                        return $item;            
                    });                
                    break;
                case 'Mancha Marrón':
                    $this->result->map(function($item){
                        
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->humout_count;
                        $Horas90HR = (($CantDatosCond*$item->humout)/144)/6;
                        $riesgo = 'apto';
                        if (($HorasMojado>=6) && (($item->leaftemp1>=15) && ($item->leaftemp1<=30))) { $riesgo = 'Progreso';}
                        if (($item->windspeed>=10) && ($item->raintotal>=5)) { $riesgo = 'Dispersion';}
                        if (($riesgo=='Progreso') && ($item->windspeed>=10) && ($item->raintotal>=5)) { $riesgo = '(Pro)(Dis)';}
                        switch ($riesgo) {
                            case '(Pro)(Dis)': $item->color = 'danger';
                            break;
                            case 'Progreso': $item->color = 'warning';
                            break;
                            case 'Dispersion': $item->color = 'warning';
                            break;
                            case 'apto': $item->color = 'success';
                            break;
                        }
                        return $item;            
                    });                  
                    break;

                case 'Tizón de la Hoja':                
                    $this->result->map(function($item){
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->humout_count_tizon_hoja;
                        $Horas70HR = (($CantDatosCond*$item->leafhum1_count)/144)/6;
                        $riesgo = 'NO';
                        if (($Horas70HR>=6) && (($item->tempout>=24) && ($item->tempout<=32))) { $riesgo = 'Infeccion';}
                        if ($HorasMojado>=6) { $riesgo = 'Germinacion';}
                        if (($HorasMojado>=6) && (($item->windspeed>=15) || ($item->raintotal>=5))) { $riesgo = 'Germinacion-Dispersion';}
                        if (($item->windspeed>=15) && ($item->raintotal>=5)) { $riesgo = 'Dispersion';}
                        switch ($riesgo) {
                            case 'Germinacion-Dispersion': $item->color = 'danger';
                            break;
                            case 'Dispersion': $item->color = 'danger';
                            break;
                            case 'Germinacion': $item->color = 'warning';
                            break;
                            case 'Infeccion': $item->color = 'info';
                            break;
                            case 'NO': $item->color = 'success';
                            break;
                        }
                                            
                        return $item;            
                    });                
                    break;
                
                case 'Mancha Anillada de la Soja':
                    $this->result->map(function($item){
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->humout_count_mancha_anillada;
                        $Horas80HR = (($CantDatosCond*$item->leafhum1_count)/144)/6;
                        $riesgo = 'NO';
                        if (($Horas80HR>=24) && (($item->tempout>=18) && ($item->tempout<=21))) { 
                            $riesgo = 'Infeccion';}
                        switch ($riesgo) {
                            case 'Infeccion': $item->color = 'danger';
                            break;
                            case 'NO': $item->color = 'success';
                            break;
                        }
                        return $item;            
                    });
                    break;
                case 'Tizón Bacteriano':
                    $this->result->map(function($item){
                        $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
                        $CantDatosCond = $item->windspeed_count;
                        $HorasWind = (($CantDatosCond*$item->windspeed)/144)/6;
                        $CantDatosCond = $item->rainrate_count;
                        $HorasRain = (($CantDatosCond*$item->rainrate)/144)/6;                    
                        
                        $riesgo = 'NO';
                        if ((($HorasWind>=6) && ($HorasRain>=6)) || (($item->tempout>=20) && ($item->tempout<=26))) { $riesgo = 'SI';}
                        switch ($riesgo) {
                            case 'SI': $item->color = 'danger';
                            break;
                            case 'NO': $item->color = 'success';
                            break;
                        }
                        return $item;            
                    });
                    break;            
                default:               
                    $this->result = null;
                    break;        
            }
            
            $aggregatedData = [];
            foreach ($this->result as $row) {
                $aggregatedData[$row->station_id] = $row;
            }
            $stations->map(function ($station) use ($aggregatedData) {
                $stationId = $station->station_id;
                if (isset($aggregatedData[$stationId])) {
                    $station->color = $aggregatedData[$stationId]->color;               
                } else {
                    $station->color =  null; 
                    //$station->color =  'danger';//Eliminar leugo ,solo es para prueba               
                }
                return $station;
            });
            $this->result = $stations;            
            
            /*
            // Cuenta la frecuencia de cada color
            $colorCounts = $this->result->pluck('color')->countBy();

            // Ordena la colección basada en la frecuencia de color
            $this->result = $this->result->sortBy(function ($item) use ($colorCounts) {
                return -$colorCounts[$item->color]; // Ordena de mayor a menor frecuencia
            })->values();
            */
        }       
    }

    public function getCropAlertGraphic(Request $request){         
        $this->result = null;       
        try {
            $startDate = null;
            $endDate = null;
            date_default_timezone_set('America/La_Paz');
            DB::beginTransaction();
            $userPermission = UserPermissionVerif::verif($request->userId,$request->email,$request->phone,$request->identifier);
            if($userPermission[0]=='yes'){
                if($request->fcmToken!=null && $request->fcmToken!=''){
                    $this->syncMobileFcmToken($request, (int) $request->userId);
                    DB::commit();
                }
                $filter = $request->filterTime;                
                switch ($filter) {
                    case 'Últimas 24 horas':
                        /*    
                        $fechaActual = new DateTime();
                        $endDate  = $fechaActual->format('Y-m-d H:i:s');
                        $fechaActual->modify('-24 hours');
                        $startDate = $fechaActual->format('Y-m-d H:i:s');*/  
                        $fechaActual = new DateTime();
                        $endDate  = $fechaActual->format('Y-m-d H:i:s');
                        //$fechaActual->modify('-24 hours');
                        $startDate = $fechaActual->format('Y-m-d').' 00:00:00';                   
                        break;
                    case 'Últimos 7 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d').' 23:59:59';
                        $fechaActual->modify('-7 days');
                        $startDate = $fechaActual->format('Y-m-d').' 00:00:00';                       
                        break;
                    case 'Últimos 30 días':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d').' 23:59:59';
                        $fechaActual->modify('-30 days');
                        $startDate = $fechaActual->format('Y-m-d').' 00:00:00';                        
                        break;
                    case 'Últimos 3 meses':
                        $fechaActual = new DateTime();
                        $endDate = $fechaActual->format('Y-m-d').' 23:59:59';
                        $fechaActual->modify('-3 months');
                        $startDate = $fechaActual->format('Y-m-d').' 00:00:00';                        
                        break;

                    default:                        
                        $this->result = null; 
                        break;
                }
                $station = StationM::where('stations.state',1)
                ->select('stations.id', 'stations.name', 'stations.location', 'stations.address')
                ->where('id',$request->stationId)
                ->orderBy('stations.id','asc')->distinct('s.id')->first();  

                $this->getSqlDataGraphic($request->stationId,$startDate, $endDate,$filter);  
                
                $minMaxCropData =  [
                        'receipt_date_min_roya_de_la_cana' => $this->receipt_date_min_roya_de_la_cana,
                        'receipt_date_max_roya_de_la_cana' => $this->receipt_date_max_roya_de_la_cana,
                        'value_roya_de_la_cana_min' =>  $this->value_roya_de_la_cana_min,
                        'value_roya_de_la_cana_max' =>  $this->value_roya_de_la_cana_max,

                        'receipt_date_min_roya_de_la_soja' => $this->receipt_date_min_roya_de_la_soja,
                        'receipt_date_max_roya_de_la_soja' => $this->receipt_date_max_roya_de_la_soja,
                        'value_roya_de_la_soja_min' =>  $this->value_roya_de_la_soja_min,
                        'value_roya_de_la_soja_max' =>  $this->value_roya_de_la_soja_max,

                        'receipt_date_min_mancha_marron' => $this->receipt_date_min_mancha_marron,
                        'receipt_date_max_mancha_marron' => $this->receipt_date_max_mancha_marron,
                        'value_mancha_marron_min' =>  $this->value_mancha_marron_min,
                        'value_mancha_marron_max' =>  $this->value_mancha_marron_max,

                        'receipt_date_min_tizon_de_la_hoja' => $this->receipt_date_min_tizon_de_la_hoja,
                        'receipt_date_max_tizon_de_la_hoja' => $this->receipt_date_max_tizon_de_la_hoja,
                        'value_tizon_de_la_hoja_min' =>  $this->value_tizon_de_la_hoja_min,
                        'value_tizon_de_la_hoja_max' =>  $this->value_tizon_de_la_hoja_max,

                        'receipt_date_min_mancha_anillada_de_la_hoja' => $this->receipt_date_min_mancha_anillada_de_la_hoja,
                        'receipt_date_max_mancha_anillada_de_la_hoja' => $this->receipt_date_max_mancha_anillada_de_la_hoja,
                        'value_mancha_anillada_de_la_hoja_min' =>  $this->value_mancha_anillada_de_la_hoja_min,
                        'value_mancha_anillada_de_la_hoja_max' =>  $this->value_mancha_anillada_de_la_hoja_max,

                        'receipt_date_min_tizon_bacteriano' => $this->receipt_date_min_tizon_bacteriano,
                        'receipt_date_max_tizon_bacteriano' => $this->receipt_date_max_tizon_bacteriano,
                        'value_tizon_bacteriano_min' =>  $this->value_tizon_bacteriano_min,
                        'value_tizon_bacteriano_max' =>  $this->value_tizon_bacteriano_max,                       
                    ];
                            
                return response()->json([
                    "response" => true,
                    "stationData" => $station,
                    "minMaxCropData" => $minMaxCropData,
                    "cropEarlWarning" => $this->result,
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
                    "minMaxCropData" => null,
                    "cropEarlWarning" => null,
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
                "minMaxCropData" => null,
                "cropEarlWarning" => null,
                "failed"=> $th->getMessage(), 
                "data_user" =>null,
                "user_permission" => 'no',
                "permit_detail" => '',
                "type_subscriptions" => null,             
            ]);
        }
    }

    private function getSqlDataGraphic($stationId,$startDate,$endDate,$filterTime){
      
        /*$this->result = DB::connection('db_current_year')->table('data as d')
                ->where('d.station_id',$stationId)
                ->whereBetween('d.receipt_date', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(d.receipt_date)'))
                ->orderBy(DB::raw('DATE(d.receipt_date)'))
                ->select(
                    DB::raw('DATE(d.receipt_date) as  receipt_date'),                 
                    DB::raw('AVG(d.tempout) as tempout'),
                    DB::raw('MIN(d.tempoutmin) as tempoutmin'),
                    DB::raw('MAX(d.tempoutmax) as tempoutmax'),
                    DB::raw('AVG(d.raintotal) as raintotal'),
                    DB::raw('AVG(d.windspeed) as windspeed'),
                    DB::raw('MAX(d.windspeedhi) as windspeedhi'),
                    DB::raw('AVG(d.humout) as humout'),
                    DB::raw('COUNT(d.rainrate) as rainrate'),
                    DB::raw('AVG(d.leaftemp1) as leaftemp1'),
                    DB::raw('AVG(d.leaftemp1) as total_leaftemp1'),
                    DB::raw('SUM(CASE WHEN d.leafhum1 > 0 THEN 1 ELSE 0 END) as leafhum1_count'),                   
                    DB::raw('SUM(CASE WHEN d.humout >= 90 THEN 1 ELSE 0 END) as humout_count'),
                    DB::raw('SUM(CASE WHEN d.humout >= 70 THEN 1 ELSE 0 END) as humout_count_tizon_hoja'),
                    DB::raw('SUM(CASE WHEN d.humout >= 80 THEN 1 ELSE 0 END) as humout_count_mancha_anillada'),
                    DB::raw('SUM(CASE WHEN d.windspeed > 0 THEN 1 ELSE 0 END) as windspeed_count'),
                    DB::raw('SUM(CASE WHEN d.rainrate > 0 THEN 1 ELSE 0 END) as rainrate_count')                     
                )->get();
        */
        
        $this->result = DB::connection('db_current_year')->table('data as d')
                ->where('d.station_id',$stationId)
                ->whereBetween('d.receipt_date', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(d.receipt_date)'))
                ->orderBy(DB::raw('DATE(d.receipt_date)'))
                ->select(
                    DB::raw('DATE(d.receipt_date) as  receipt_date'),                 
                    DB::raw('AVG(d.tempout) as tempout'),
                    DB::raw('MIN(d.tempout) as tempoutmin'),
                    DB::raw('MAX(d.tempout) as tempoutmax'),
                    DB::raw('MAX(d.raintotal) as raintotal'),
                    DB::raw('AVG(d.windspeed) as windspeed'),
                    DB::raw('MAX(d.windspeedhi) as windspeedhi'),
                    DB::raw('AVG(d.humout) as humout'),
                    DB::raw('SUM(CASE WHEN d.rainrate IS NOT NULL THEN 1 ELSE 0 END) AS rainrate'),
                    DB::raw('AVG(d.leaftemp1) as leaftemp1'),
                    DB::raw('AVG(d.leaftemp1) as total_leaftemp1'),
                    DB::raw('AVG(d.leafhum1) as leafhum1'),
                    DB::raw('SUM(CASE WHEN d.leafhum1 IS NOT NULL THEN 1 ELSE 0 END) AS total_leafhum1'),
                    DB::raw('SUM(CASE WHEN d.leafhum1 > 0 THEN 1 ELSE 0 END) as leafhum1_count'),                   
                    DB::raw('SUM(CASE WHEN d.humout >= 90 THEN 1 ELSE 0 END) as humout_count'),
                    DB::raw('SUM(CASE WHEN d.humout >= 70 THEN 1 ELSE 0 END) as humout_count_tizon_hoja'),
                    DB::raw('SUM(CASE WHEN d.humout >= 80 THEN 1 ELSE 0 END) as humout_count_mancha_anillada'),
                    DB::raw('SUM(CASE WHEN d.windspeed > 0 THEN 1 ELSE 0 END) as windspeed_count'),
                    DB::raw('SUM(CASE WHEN d.rainrate > 0 THEN 1 ELSE 0 END) as rainrate_count')                     
                )->get();
                
         
        

        $this->result->map(function($item){
            $HorasMojado = (($item->leafhum1_count * $item->total_leafhum1)/144)/6;
            $CantDatosCond = $item->humout_count;

            
            //******** Roya de la Caña ******* */
            $Horas90HR = (($CantDatosCond*$item->total_leafhum1)/144)/6;
            $riesgo = 'NO';
            $value = null;
            if (($HorasMojado>=8) && (($Horas90HR>=90) && ($item->tempout>=17) && ($item->tempout<=23))) { 
                $riesgo = 'SI';
            }
            switch ($riesgo) {
                case 'SI': 
                    //$item->color  = 'danger';  
                    $value=3;                 
                    $item->value_roya_de_la_cana =  3;
                    break;
                case 'NO': 
                    //$item->color  = 'success'; 
                    $value=0;                   
                    $item->value_roya_de_la_cana =  0;
                    break;
            }
            if( $this->value_roya_de_la_cana_min == null){
                $this->value_roya_de_la_cana_min = $value;
                $this->receipt_date_min_roya_de_la_cana = $item->receipt_date;
            }else{
                if( $value < $this->value_roya_de_la_cana_min){
                    $this->value_roya_de_la_cana_min = $value;
                    $this->receipt_date_min_roya_de_la_cana = $item->receipt_date;
                }
            }
            if( $this->value_roya_de_la_cana_max == null){
                $this->value_roya_de_la_cana_max = $value;
                $this->receipt_date_max_roya_de_la_cana = $item->receipt_date;
            } else{
                if( $value > $this->value_roya_de_la_cana_max){
                    $this->value_roya_de_la_cana_max = $value;
                    $this->receipt_date_max_roya_de_la_cana = $item->receipt_date;
                }
            }

            
            /************* Roya de la Soja ***********/
            $riesgo = 'BAJO';
            $value = null;
            if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>=19) && ($item->leaftemp1<=24))) { $riesgo = 'ALTO';}
            if (($HorasMojado>12) && (($item->leaftemp1>=11) && ($item->leaftemp1<=28))) { 
                $riesgo = 'ALTO';}
            if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>=11) && ($item->leaftemp1<19))) { $riesgo = 'MODERADO';}
            if ((($HorasMojado>=6) && ($HorasMojado<=12)) && (($item->leaftemp1>24) && ($item->leaftemp1<=28))) { $riesgo = 'MODERADO';}
            if (($HorasMojado>6) && (($item->leaftemp1<11) || ($item->leaftemp1>28))) { 
                $riesgo = 'LIGERO';}
                switch ($riesgo) {
                    case 'ALTO': 
                        //$item->color = 'danger';
                        $value = 3;
                        $item->value_roya_de_la_soja =  3;
                    break;
                    case 'MODERADO': 
                        //$item->color = 'warning';
                        $value = 2;
                        $item->value_roya_de_la_soja =  2;
                    break;
                    case 'LIGERO': 
                        //$item->color = 'info';
                        $value = 1;
                        $item->value_roya_de_la_soja =  1;
                    break;
                    case 'BAJO': 
                        //$item->color = 'success';
                        $value = 0;
                        $item->value_roya_de_la_soja =  0;
                    break;
                }  
                if( $this->value_roya_de_la_soja_min == null){
                    $this->value_roya_de_la_soja_min = $value;
                    $this->receipt_date_min_roya_de_la_soja = $item->receipt_date;
                }else{
                    if( $value < $this->value_roya_de_la_soja_min){
                        $this->value_roya_de_la_soja_min = $value;
                        $this->receipt_date_min_roya_de_la_soja = $item->receipt_date;
                    }
                }
                if( $this->value_roya_de_la_soja_max == null){
                    $this->value_roya_de_la_soja_max = $value;
                    $this->receipt_date_max_roya_de_la_soja = $item->receipt_date;
                } else{
                    if( $value > $this->value_roya_de_la_soja_max){
                        $this->value_roya_de_la_soja_max = $value;
                        $this->receipt_date_max_roya_de_la_soja = $item->receipt_date;
                    }
                }       
            

            /************** Mancha Marrón **************/
            $value = null;
            $riesgo = 'apto';   
            if (($HorasMojado>=6) && (($item->leaftemp1>=15) && ($item->leaftemp1<=30))) { $riesgo = 'Progreso';}
            if (($item->windspeed>=10) && ($item->raintotal>=5)) { $riesgo = 'Dispersion';}
            if (($riesgo=='Progreso') && ($item->windspeed>=10) && ($item->raintotal>=5)) { $riesgo = '(Pro)(Dis)';}
            switch ($riesgo) {
                case '(Pro)(Dis)': 
                    //$item->color = 'danger';
                    $value = 3;
                    $item->value_mancha_marron =  3;
                break;
                case 'Progreso': 
                    //$item->color = 'warning';
                    $value = 2;
                    $item->value_mancha_marron =  2;
                break;
                case 'Dispersion': 
                    //$item->color = 'warning';
                    $value = 2;
                    $item->value_mancha_marron =  2;
                break;
                case 'apto': 
                    //$item->color = 'success';
                    $value = 0;
                    $item->value_mancha_marron =  0;
                break;
            }
            if( $this->value_mancha_marron_min == null){
                $this->value_mancha_marron_min = $value;
                $this->receipt_date_min_mancha_marron = $item->receipt_date;
            }else{
                if( $value < $this->value_mancha_marron_min){
                    $this->value_mancha_marron_min = $value;
                    $this->receipt_date_min_mancha_marron = $item->receipt_date;
                }
            }
            if( $this->value_mancha_marron_max == null){
                $this->value_mancha_marron_max = $value;
                $this->receipt_date_max_mancha_marron = $item->receipt_date;
            } else{
                if( $value > $this->value_mancha_marron_max){
                    $this->value_mancha_marron_max = $value;
                    $this->receipt_date_max_mancha_marron = $item->receipt_date;
                }
            }

            /************** Tizón de la Hoja **************/
            $CantDatosCond = $item->humout_count_tizon_hoja;
            $Horas70HR = (($CantDatosCond*$item->leafhum1_count)/144)/6;
            $riesgo = 'NO';
            $value=null;
            if (($Horas70HR>=6) && (($item->tempout>=24) && ($item->tempout<=32))) { $riesgo = 'Infeccion';}
            if ($HorasMojado>=6) { $riesgo = 'Germinacion';}
            if (($HorasMojado>=6) && (($item->windspeed>=15) || ($item->raintotal>=5))) { $riesgo = 'Germinacion-Dispersion';}
            if (($item->windspeed>=15) && ($item->raintotal>=5)) { $riesgo = 'Dispersion';}
            switch ($riesgo) {
                case 'Germinacion-Dispersion': 
                    //$item->color = 'danger';
                    $value=3;
                    $item->value_tizon_de_la_hoja =  3;
                break;
                case 'Dispersion': 
                    //$item->color = 'danger';
                    $value=3;
                    $item->value_tizon_de_la_hoja =  3;
                break;
                case 'Germinacion': 
                    //$item->color = 'warning';
                    $value=2;
                    $item->value_tizon_de_la_hoja =  2;
                break;
                case 'Infeccion': 
                   // $item->color = 'info';
                   $value=1;
                    $item->value_tizon_de_la_hoja =  1;
                break;
                case 'NO': 
                    //$item->color = 'success';
                    $value=0;
                    $item->value_tizon_de_la_hoja =  0;
                break;
            }
            if( $this->value_tizon_de_la_hoja_min == null){
                $this->value_tizon_de_la_hoja_min = $value;
                $this->receipt_date_min_tizon_de_la_hoja = $item->receipt_date;
            }else{
                if( $value < $this->value_tizon_de_la_hoja_min){
                    $this->value_tizon_de_la_hoja_min = $value;
                    $this->receipt_date_min_tizon_de_la_hoja = $item->receipt_date;
                }
            }
            if( $this->value_tizon_de_la_hoja_max == null){
                $this->value_tizon_de_la_hoja_max = $value;
                $this->receipt_date_max_tizon_de_la_hoja = $item->receipt_date;
            } else{
                if( $value > $this->value_tizon_de_la_hoja_max){
                    $this->value_tizon_de_la_hoja_max = $value;
                    $this->receipt_date_max_tizon_de_la_hoja = $item->receipt_date;
                }
            }


            /************** Mancha anillada de la hoja **************/
            $CantDatosCond = $item->humout_count_mancha_anillada;
            $Horas80HR = (($CantDatosCond*$item->leafhum1_count)/144)/6;
            $riesgo = 'NO';
            $value=null;
            if (($Horas80HR>=24) && (($item->tempout>=18) && ($item->tempout<=21))) { 
                $riesgo = 'Infeccion';}
            switch ($riesgo) {
                case 'Infeccion': 
                    //$item->color = 'danger';
                    $value=3;
                    $item->value_mancha_anillada_de_la_hoja =  3;
                break;
                case 'NO': 
                    //$item->color = 'success';
                    $value=0;
                    $item->value_mancha_anillada_de_la_hoja =  0;
                break;
            }
            if( $this->value_mancha_anillada_de_la_hoja_min == null){
                $this->value_mancha_anillada_de_la_hoja_min = $value;
                $this->receipt_date_min_mancha_anillada_de_la_hoja = $item->receipt_date;
            }else{
                if( $value < $this->value_mancha_anillada_de_la_hoja_min){
                    $this->value_mancha_anillada_de_la_hoja_min = $value;
                    $this->receipt_date_min_mancha_anillada_de_la_hoja = $item->receipt_date;
                }
            }
            if( $this->value_mancha_anillada_de_la_hoja_max == null){
                $this->value_mancha_anillada_de_la_hoja_max = $value;
                $this->receipt_date_max_mancha_anillada_de_la_hoja = $item->receipt_date;
            } else{
                if( $value > $this->value_mancha_anillada_de_la_hoja_max){
                    $this->value_mancha_anillada_de_la_hoja_max = $value;
                    $this->receipt_date_max_mancha_anillada_de_la_hoja = $item->receipt_date;
                }
            }

            /************** Tizón Bacteriano **************/
            $CantDatosCond = $item->windspeed_count;
            $HorasWind = (($CantDatosCond*$item->windspeed)/144)/6;
            $CantDatosCond = $item->rainrate_count;
            $HorasRain = (($CantDatosCond*$item->rainrate)/144)/6;                    
            $value=null;
            $riesgo = 'NO';
            if ((($HorasWind>=6) && ($HorasRain>=6)) || (($item->tempout>=20) && ($item->tempout<=26))) { $riesgo = 'SI';}
            switch ($riesgo) {
                case 'SI': 
                    //$item->color = 'danger';
                    $value=3;
                    $item->value_mancha_anillada_de_la_hoja =  3;
                break;
                case 'NO': 
                    //$item->color = 'success';
                    $value=0;
                    $item->value_mancha_anillada_de_la_hoja =  0;
                break;
            }
            if( $this->value_tizon_bacteriano_min == null){
                $this->value_tizon_bacteriano_min = $value;
                $this->receipt_date_min_tizon_bacteriano = $item->receipt_date;
            }else{
                if( $value < $this->value_tizon_bacteriano_min){
                    $this->value_tizon_bacteriano_min = $value;
                    $this->receipt_date_min_tizon_bacteriano = $item->receipt_date;
                }
            }
            if( $this->value_tizon_bacteriano_max == null){
                $this->value_tizon_bacteriano_max = $value;
                $this->receipt_date_max_tizon_bacteriano = $item->receipt_date;
            } else{
                if( $value > $this->value_tizon_bacteriano_max){
                    $this->value_tizon_bacteriano_max = $value;
                    $this->receipt_date_max_tizon_bacteriano = $item->receipt_date;
                }
            }

            return $item;
        });                
        
                      
                         
                     
        
        //'Tizón Bacteriano':
        $this->result->map(function($item){
            $HorasMojado = (($item->leafhum1_count * $item->total_leaftemp1)/144)/6;
            $CantDatosCond = $item->windspeed_count;
            $HorasWind = (($CantDatosCond*$item->windspeed)/144)/6;
            $CantDatosCond = $item->rainrate_count;
            $HorasRain = (($CantDatosCond*$item->rainrate)/144)/6;                    
            
            $riesgo = 'NO';
            if ((($HorasWind>=6) && ($HorasRain>=6)) || (($item->tempout>=20) && ($item->tempout<=26))) { $riesgo = 'SI';}
            switch ($riesgo) {
                case 'SI': 
                    //$item->color = 'danger';
                    $item->value_tizon_bacteriano =  3;
                break;
                case 'NO': 
                    //$item->color = 'success';
                    $item->value_tizon_bacteriano =  0;
                break;
            }
            return $item;            
        });



        //eliminando campos sobrantes:        
        $this->result->map(function($item){                             
            unset($item->tempout);
            unset($item->tempoutmax);
            unset($item->tempoutmin);
            unset($item->raintotal);
            unset($item->windspeed);
            unset($item->windspeedhi);
            unset($item->humout);
            unset($item->rainrate);
            unset($item->leaftemp1);
            unset($item->total_leaftemp1);
            unset($item->leafhum1_count);
            unset($item->humout_count);
            unset($item->humout_count_tizon_hoja);
            unset($item->humout_count_mancha_anillada);
            unset($item->windspeed_count);
            unset($item->rainrate_count);
            return $item;            
        });
       
    }
}
