<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DateTime;

class CampbellC extends Controller
{
    public function run(){
        $stationCampbell = [['villa_abecia_va',476], ['san_lucas_sl',480], ['villa_charcas_vc',487], ['machareti_m',486]];        
        for ($i=0; $i < count($stationCampbell); $i++) {
            $nameStation = $stationCampbell[$i][0];
            $dateTimeStation = DB::connection('db_current_year')->table('data')->where('station_id',$stationCampbell[$i][1])->orderBy('receipt_date','desc')->value('receipt_date');            
            if($dateTimeStation==null){
                try {
                    $itemsStationData = DB::connection('db_campbell')->table($nameStation)->where('station_id',$stationCampbell[$i][1])->orderBy('TmStamp','asc')->get();                
                    if(count($itemsStationData) > 0 ){
                        foreach($itemsStationData as $item){
                            $dewptout=null;
                            if(($item->temp!==null && $item->temp!=="") && ($item->hum!==null && $item->hum!=="")){
                                $dewptout = ($item->temp-(14.55+0.114*$item->temp)*(1-(0.01*$item->hum))-pow(((2.5+0.007*$item->temp)*(1-(0.01*$item->hum))),3)-(15.9+0.117*$item->temp)*pow((1-(0.01*$item->hum)),14));
                            }
                            $fechaFormateada = date('Y-m-d', strtotime($item->TmStamp)) .' 00:00:00';
                            $raintotal = DB::connection('db_campbell')->table($stationCampbell[$i][0])
                                ->whereBetween('TmStamp', [$fechaFormateada, $item->TmStamp])
                                ->where('station_id',$stationCampbell[$i][1])
                                ->sum('rain');
                            if($item->rain !== null && $item->rain !== ""){
                                $raintotal = $raintotal + $item->rain;
                            }
                            
                            $this->updateOrInsertStationData($stationCampbell[$i], $item, $dewptout, $raintotal,$nameStation);
                            $this->updateOrInsertStationDataPC2($stationCampbell[$i], $item, $dewptout, $raintotal); 
                        }
                    }
                } catch (\Throwable $th) {}
                
            }else{
                try {
                     $TmStamp = DB::connection('db_campbell')->table($stationCampbell[$i][0])
                    ->where('station_id',$stationCampbell[$i][1])->orderBy('TmStamp','desc')->value('TmStamp');
                    $receipt_date = DB::connection('db_current_year')->table('data')
                    ->where('station_id',$stationCampbell[$i][1])->orderBy('receipt_date','desc')->value('receipt_date');
                    $TmStampF = new DateTime($TmStamp);
                    $receipt_dateF = new DateTime($receipt_date);
                    if ($TmStampF > $receipt_dateF) {

                        $itemsStationData = DB::connection('db_campbell')->table($stationCampbell[$i][0])
                        ->whereBetween('TmStamp', [$receipt_date, $TmStamp])
                        ->where('station_id',$stationCampbell[$i][1])->orderBy('TmStamp','asc')->get();

                        foreach($itemsStationData as $item){
                            $dewptout=null;
                            if(($item->temp!==null && $item->temp!=="") && ($item->hum!==null && $item->hum!=="")){
                                $dewptout = ($item->temp-(14.55+0.114*$item->temp)*(1-(0.01*$item->hum))-pow(((2.5+0.007*$item->temp)*(1-(0.01*$item->hum))),3)-(15.9+0.117*$item->temp)*pow((1-(0.01*$item->hum)),14));
                            }                     
                            $fechaFormateada = date('Y-m-d', strtotime($item->TmStamp)) .' 00:00:00';
                            $raintotal = DB::connection('db_campbell')->table($stationCampbell[$i][0])
                                ->whereBetween('TmStamp', [$fechaFormateada, $item->TmStamp])
                                ->where('station_id',$stationCampbell[$i][1])
                                ->sum('rain');
                            if($item->rain !== null && $item->rain !== ""){
                                $raintotal = $raintotal + $item->rain;
                            }
                            $this->updateOrInsertStationData($stationCampbell[$i], $item, $dewptout, $raintotal,$nameStation); 
                            $this->updateOrInsertStationDataPC2($stationCampbell[$i], $item, $dewptout, $raintotal); 
                                                    
                        }
                    }
                } catch (\Throwable $th) {}               
            }
        }        
    }


    //artguz Clima 3
    function updateOrInsertStationData($stationData, $item, $dewptout, $raintotal,$nameStation) {              
        try {
            DB::beginTransaction();
            $flag = false;
            $commonData = [
                'receipt_date' => $item->TmStamp,
                'registration_date' => date('Y-m-d H:i:s'),
                'tempout' => ($item->temp !== null && $item->temp !== "") ? $item->temp : null,
                'humout' => ($item->hum !== null && $item->hum !== "") ? $item->hum : null,
                'humoutmin' => ($item->hum_min !== null && $item->hum_min !== "") ? $item->hum_min : null,
                'humoutmax' => ($item->hum_max !== null && $item->hum_max !== "") ? $item->hum_max : null,
                'dewptout' => $dewptout,
                'press' => ($item->bp !== null && $item->bp !== "") ? $item->bp : null,
                'rainrate' => ($item->rain !== null && $item->rain !== "") ? $item->rain : null,
                'raintotal' => $raintotal ?? (($item->rain !== null && $item->rain !== "") ? $item->rain : null),
                'solrad' => ($item->sol_rad !== null && $item->sol_rad !== "") ? $item->sol_rad : null,
                'solevo' => ($item->etrs !== null && $item->etrs !== "") ? $item->etrs : null,
                'tempoutmax' => ($item->temp_max !== null && $item->temp_max !== "") ? $item->temp_max : null,
                'tempoutmin' => ($item->temp_min !== null && $item->temp_min !== "") ? $item->temp_min : null,
                'windspeed2' => ($item->wind_speed !== null && $item->wind_speed !== "") ? $item->wind_speed : null,
                'winddir2' => ($item->wind_dir !== null && $item->wind_dir !== "") ? $item->wind_dir : null,
                'windspeedhi2' => ($item->wind_speed_max !== null && $item->wind_speed_max !== "") ? $item->wind_speed_max : null,
                'winddirhi2' => null,
                'state' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            DB::table('current_data')->updateOrInsert(
                [
                    'station_id' => $stationData[1]                    
                ],
                $commonData
            );
            unset($commonData['receipt_date']);
            unset($commonData['state']);
            unset($commonData['created_at']);
            unset($commonData['updated_at']);

            DB::connection('db_current_year')->table('data')->updateOrInsert(
                [
                    'station_id' => $stationData[1],
                    'receipt_date' => $item->TmStamp
                ],
                $commonData
            );
               
            DB::commit();
            $flag = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th->getMessage());
            Log::error('PC3 - Error al actualizar los datos de la estación: ' . $th->getMessage());
        }finally{
            if($flag){
                try{
                    DB::connection('db_campbell')->table($nameStation)->truncate();
                } catch (\Throwable $th) {}
            }
        }
    }
    //artguz Clima 2
    function updateOrInsertStationDataPC2($stationData, $item, $dewptout, $raintotal) {
        try {
            DB::beginTransaction();
            $commonData = [
                'FECHA' => $item->TmStamp,
                'FECHA_REG' => date('Y-m-d H:i:s'),
                'TEMP_OUT' => ($item->temp !== null && $item->temp !== "") ? $item->temp : null,
                'HUM_OUT' => ($item->hum !== null && $item->hum !== "") ? $item->hum : null,                
                'DEW_PT_OUT' => $dewptout,
                'PRESS' => ($item->bp !== null && $item->bp !== "") ? $item->bp : null,
                'RAIN_RATE' => ($item->rain !== null && $item->rain !== "") ? $item->rain : null,
                'RAIN_TOTAL' => $raintotal ?? (($item->rain !== null && $item->rain !== "") ? $item->rain : null),
                'SOL_RAD' => ($item->sol_rad !== null && $item->sol_rad !== "") ? $item->sol_rad : null,
                'SOL_EVO' => ($item->etrs !== null && $item->etrs !== "") ? $item->etrs : null,
                'TEMP_OUT_MAX' => ($item->temp_max !== null && $item->temp_max !== "") ? $item->temp_max : null,
                'TEMP_OUT_MIN' => ($item->temp_min !== null && $item->temp_min !== "") ? $item->temp_min : null,
                'WIND_SPEED2' => ($item->wind_speed !== null && $item->wind_speed !== "") ? $item->wind_speed : null,
                'WIND_DIR2' => ($item->wind_dir !== null && $item->wind_dir !== "") ? $item->wind_dir : null,
                'WIND_SPEED_HI2' => ($item->wind_speed_max !== null && $item->wind_speed_max !== "") ? $item->wind_speed_max : null,
                'HEAT_INDEX_OUT' => -9999.00,
                'TEMP_IN' => -9999.00,
                'HUM_IN' => -9999.00,
                'DEW_PT_IN' => -9999.00,
                'SEAPRESS' => -9999.00,
                'WIND_AVG' => -9999.00,
                'WIND_SPEED' => -9999.00,
                'WIND_DIR' => -9999.00,
                'WIND_CHILL' => -9999.00,
                'UV_INDEX' => -9999.00,
                'SOIL_TEMP1' => -9999.00,
                'SOIL_HUM1' => -9999.00,
                'LEAF_TEMP1' => -9999.00,
                'LEAF_HUM1' => -9999.00,
                'WIND_DIR_STR' => "",
                'WIND_SPEED_HI' => -9999.00,
                'WIND_DIR_HI' => -9999.00,
                'SOL_RAD_HI' => -9999.00,
                'SOIL_TEMP2' => -9999.00,
                'SOIL_HUM2' => -9999.00,
                'LEAF_TEMP2' => -9999.00,
                'LEAF_HUM2' => -9999.00,
                'SOIL_TEMP3' => -9999.00,
                'SOIL_HUM3' => -9999.00,
                'LEAF_TEMP3' => -9999.00,
                'LEAF_HUM3' => -9999.00,
                'SOIL_TEMP4' => -9999.00,
                'SOIL_HUM4' => -9999.00,
                'LEAF_TEMP4' => -9999.00,
                'LEAF_HUM4' => -9999.00,
                'WIND_DIR_HI2' => -9999.00,
                'PM1' => -9999.00,
                'PM10' => -9999.00,
                'PM2_5' => -9999.00,
            ];

            DB::connection('artguz2current')->table('datos_last')->updateOrInsert(
                [
                    'DISPOSITIVO_ID' => $stationData[1],                    
                ],
                $commonData
            );
            unset($commonData['FECHA']);
            DB::connection('artguz2currentyear')->table('datos')->updateOrInsert(
                [
                    'DISPOSITIVO_ID' => $stationData[1],
                    'FECHA' => $item->TmStamp
                ],
                $commonData
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th->getMessage());
            Log::error('PC2 - Error al actualizar los datos de la estación: ' . $th->getMessage());
        }
    }
}
