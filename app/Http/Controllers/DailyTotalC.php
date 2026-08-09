<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DailyDataM;
use App\Models\StationM;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class DailyTotalC extends Controller
{
    public function setDailyTotal(Request $request){
        date_default_timezone_set('America/La_Paz');
        
        $anio = null;
        $mes = null;
        $dia = null;
        $station_id = null;
        $registration_date = null;

        try {
            
            if ($request->isMethod('get')) {
                
                if ($request->has('date')) {
                   
                    try {
                        $fecha = Carbon::parse($request->date);
                        $registration_date = $fecha->format('Y-m-d');
                        $anio = $fecha->format('Y');
                        $mes = $fecha->format('m');
                        $dia = $fecha->format('d'); 

                    } catch (\Throwable $th) {       
                        Log::error('-Error DAILY_DATA request->has(date): ' . $th->getMessage());               
                    }
                }else{
                    Log::info('✅ Entrando 4 a setDailyTotal()');
                    $fecha = Carbon::today();
                    $registration_date = $fecha->format('Y-m-d');
                    $anio = Carbon::now()->year;
                    $mes = Carbon::now()->month;
                    $dia = Carbon::now()->day;
                }

                if ($request->has('station_id')) {
                    
                    try {
                        if (StationM::where('id', $request->station_id)->exists()) {
                            $station_id = (int) $request->station_id;
                        }
                    } catch (\Throwable $th) {  
                        Log::error('Error DAILY_DATA request->has(station_id): ' . $th->getMessage());                  
                    }
                }

            } else {
                
                //$fecha = Carbon::yesterday();
                $fecha = Carbon::today();
                $registration_date = $fecha->format('Y-m-d');
                $anio = Carbon::now()->year;
                $mes = Carbon::now()->month;
                $dia = Carbon::now()->day;
            }
        } catch (\Throwable $th) {
            Log::error(' Error DAILY_DATA INICIANDO...: ' . $th->getMessage());
        }

        try{
            
            DB::beginTransaction();
            $sql ="
                d.station_id,
                DATE(d.receipt_date) AS date,
                AVG(d.tempout) AS tempout,
                MAX(d.tempout) AS tempoutmax,
                MIN(d.tempout) AS tempoutmin,
                AVG(d.dewptout) AS dewptout,
                AVG(d.humout) AS humout,
                AVG(d.winddir) AS winddir,
                AVG(d.windspeed) AS windspeed,
                MAX(d.windspeedhi) AS windspeedhi,
                AVG(d.winddir2) AS winddir2,
                AVG(d.windspeed2) AS windspeed2,
                MAX(d.windspeedhi2) AS windspeedhi2,
                MAX(d.raintotal) AS raintotal,
                AVG(d.press) AS press,
                AVG(d.heatindexout) AS heatindexout,
                AVG(d.uvindex) AS uvindex,
                AVG(d.solrad) AS solrad,
                MAX(d.solevo) AS solevo,
                AVG(d.soiltemp1) AS soiltemp1,
                AVG(d.soilhum1) AS soilhum1,
                AVG(d.leaftemp1) AS leaftemp1,
                AVG(d.leafhum1) AS leafhum1,
                SUM(CASE WHEN d.leafhum1 IS NOT NULL THEN 1 ELSE 0 END) AS total_leafhum1,
                SUM(CASE WHEN d.tempout IS NOT NULL THEN 1 ELSE 0 END) AS total_tempout,
                SUM(CASE WHEN d.humout IS NOT NULL THEN 1 ELSE 0 END) AS total_humout,
                SUM(CASE WHEN d.rainrate IS NOT NULL THEN 1 ELSE 0 END) AS total_rainrate,
                AVG(d.soiltemp2) AS soiltemp2,
                AVG(d.soilhum2) AS soilhum2,
                AVG(d.leaftemp2) AS leaftemp2,
                AVG(d.leafhum2) AS leafhum2,
                SUM(CASE WHEN d.leafhum2 IS NOT NULL THEN 1 ELSE 0 END) AS total_leafhum2,
                AVG(d.soiltemp3) AS soiltemp3,
                AVG(d.soilhum3) AS soilhum3,
                AVG(d.leaftemp3) AS leaftemp3,
                AVG(d.leafhum3) AS leafhum3,
                SUM(CASE WHEN d.leafhum3 IS NOT NULL THEN 1 ELSE 0 END) AS total_leafhum3,
                AVG(d.soiltemp4) AS soiltemp4,
                AVG(d.soilhum4) AS soilhum4,
                AVG(d.leaftemp4) AS leaftemp4,
                AVG(d.leafhum4) AS leafhum4,
                SUM(CASE WHEN d.leafhum4 IS NOT NULL THEN 1 ELSE 0 END) AS total_leafhum4,
                (SELECT winddir FROM data WHERE winddir IS NOT NULL  AND DATE(receipt_date) = ? AND station_id = d.station_id GROUP BY winddir ORDER BY COUNT(*) DESC LIMIT 1) AS winddir_mode,
                (SELECT winddir2 FROM data WHERE winddir2 IS NOT NULL  AND DATE(receipt_date) = ? AND station_id = d.station_id GROUP BY winddir2 ORDER BY COUNT(*) DESC LIMIT 1) AS winddir2_mode
            ";
            if($station_id!==null){
                
                $data = DB::connection('db' . $anio)
                ->table('data as d')
                ->selectRaw($sql, [$registration_date, $registration_date])
                ->whereRaw("d.station_id = ?", [$station_id])
                ->whereRaw("DATE(d.receipt_date) = ?", [$registration_date])
                ->groupBy('d.station_id', DB::raw('DATE(d.receipt_date)'))
                ->get();
               
                foreach ($data as $item) {    
                                         
                    DB::connection('db'.$anio)
                    ->table('daily_data')->updateOrInsert(
                        [
                            'station_id' => $item->station_id,
                            'f_year' => $anio,
                            'f_month' => $mes,
                            'f_day' => $dia,
                            'registration_date' => $registration_date
                        ],
                        [
                            'tempout' => $item->tempout,  
                            'tempoutmax' => $item->tempoutmax, 
                            'tempoutmin' => $item->tempoutmin, 
                            'dewptout' => $item->dewptout, 
                            'humout' => $item->humout, 
                            'winddir' => $item->winddir, 
                            'windspeed' => $item->windspeed, 
                            'windspeedhi' => $item->windspeedhi, 
                            'winddirstr' => $this->getPCardinal($item->winddir_mode,$item->windspeed),
                            'winddir2' => $item->winddir2,
                            'windspeed2' => $item->windspeed2,
                            'windspeedhi2' => $item->windspeedhi2,
                            'winddirstr2' => $this->getPCardinal($item->winddir2_mode,$item->windspeed2), 
                            'raintotal' => $item->raintotal, 
                            'press' => $item->press, 
                            'heatindexout' => $item->heatindexout, 
                            'uvindex' => $item->uvindex, 
                            'solrad' => $item->solrad, 
                            'solevo' => $item->solevo, 
                            'soiltemp1' => $item->soiltemp1, 
                            'soilhum1' => $item->soilhum1, 
                            'leaftemp1' => $item->leaftemp1, 
                            'leafhum1' => $item->leafhum1, 
                            'total_leafhum1' => $item->total_leafhum1, 
                            'total_tempout' => $item->total_tempout, 
                            'total_humout' => $item->total_humout, 
                            'total_rainrate' => $item->total_rainrate, 
                            'soiltemp2' => $item->soiltemp2, 
                            'soilhum2' => $item->soilhum2, 
                            'leaftemp2' => $item->leaftemp2, 
                            'leafhum2' => $item->leafhum2, 
                            'total_leafhum2' => $item->total_leafhum2, 
                            'soiltemp3' => $item->soiltemp3, 
                            'soilhum3' => $item->soilhum3, 
                            'leaftemp3' => $item->leaftemp3, 
                            'leafhum3' => $item->leafhum3, 
                            'total_leafhum3' => $item->total_leafhum3, 
                            'soiltemp4' => $item->soiltemp4, 
                            'soilhum4' => $item->soilhum4, 
                            'leaftemp4' => $item->leaftemp4, 
                            'leafhum4' => $item->leafhum4, 
                            'total_leafhum4' => $item->total_leafhum4
                        ]
                    );               
                    DB::commit();
                    
                }  
            }else{
               
                $data = DB::connection('db' . $anio)
                ->table('data as d')
                ->selectRaw($sql, [$registration_date, $registration_date])
                ->whereRaw("DATE(d.receipt_date) = ?", [$registration_date])
                ->groupBy('d.station_id', DB::raw('DATE(d.receipt_date)'))
                ->get();
                
                foreach ($data as $item) {   
                                     
                    DB::connection('db'.$anio)
                    ->table('daily_data')->updateOrInsert(
                        [
                            'station_id' => $item->station_id,
                            'f_year' => $anio,
                            'f_month' => $mes,
                            'f_day' => $dia,
                            'registration_date' => $registration_date
                        ],
                        [
                            'tempout' => $item->tempout,  
                            'tempoutmax' => $item->tempoutmax, 
                            'tempoutmin' => $item->tempoutmin, 
                            'dewptout' => $item->dewptout, 
                            'humout' => $item->humout, 
                            'winddir' => $item->winddir, 
                            'windspeed' => $item->windspeed, 
                            'windspeedhi' => $item->windspeedhi, 
                            'winddirstr' => $this->getPCardinal($item->winddir_mode,$item->windspeed),
                            'winddir2' => $item->winddir2,
                            'windspeed2' => $item->windspeed2,
                            'windspeedhi2' => $item->windspeedhi2,
                            'winddirstr2' => $this->getPCardinal($item->winddir2_mode,$item->windspeed2), 
                            'raintotal' => $item->raintotal, 
                            'press' => $item->press, 
                            'heatindexout' => $item->heatindexout, 
                            'uvindex' => $item->uvindex, 
                            'solrad' => $item->solrad, 
                            'solevo' => $item->solevo, 
                            'soiltemp1' => $item->soiltemp1, 
                            'soilhum1' => $item->soilhum1, 
                            'leaftemp1' => $item->leaftemp1, 
                            'leafhum1' => $item->leafhum1, 
                            'total_leafhum1' => $item->total_leafhum1, 
                            'total_tempout' => $item->total_tempout, 
                            'total_humout' => $item->total_humout, 
                            'total_rainrate' => $item->total_rainrate, 
                            'soiltemp2' => $item->soiltemp2, 
                            'soilhum2' => $item->soilhum2, 
                            'leaftemp2' => $item->leaftemp2, 
                            'leafhum2' => $item->leafhum2, 
                            'total_leafhum2' => $item->total_leafhum2, 
                            'soiltemp3' => $item->soiltemp3, 
                            'soilhum3' => $item->soilhum3, 
                            'leaftemp3' => $item->leaftemp3, 
                            'leafhum3' => $item->leafhum3, 
                            'total_leafhum3' => $item->total_leafhum3, 
                            'soiltemp4' => $item->soiltemp4, 
                            'soilhum4' => $item->soilhum4,
                            'leaftemp4' => $item->leaftemp4, 
                            'leafhum4' => $item->leafhum4, 
                            'total_leafhum4' => $item->total_leafhum4
                        ]
                    ); 
                              
                   
                }  
                DB::commit();
               
            }
                              
        }catch(\Exception $e){
            DB::rollback();
           
            Log::error(' Error DailyData EN EL SQL: ' . $th->getMessage());
         
        }finally {
           
        if ($anio !== null) {
            Log::info('✅ Entrando 17 a setDailyTotal()');
            DB::disconnect('db' . $anio);
        }
    }
    }

    private function getPCardinal($WIND_DIR,$windspeed){
        $viento = null;
        if($windspeed!==null && ($windspeed===0.0 || $windspeed===0)){
            $viento ='CALMO';
        }else{
            if($WIND_DIR!==null){            
                if (($WIND_DIR>=337.5) && ($WIND_DIR<22.5)){
                    $viento = 'N' ;
                }else
                if (($WIND_DIR>=22.5) && ($WIND_DIR<67.5)){
                    $viento = 'NE' ;
                }else
                if (($WIND_DIR>=67.5) && ($WIND_DIR<112.5)){
                    $viento = 'E' ;
                }else
                if (($WIND_DIR>=112.5) && ($WIND_DIR<157.5)){
                    $viento = 'SE';
                }else
                if (($WIND_DIR>=157.5) && ($WIND_DIR<202.5)){
                    $viento = 'S' ;
                }else
                if (($WIND_DIR>=202.5) && ($WIND_DIR<247.5)){
                    $viento = 'SO';
                }else
                if (($WIND_DIR>=247.5) && ($WIND_DIR<292.5)){
                    $viento = 'O' ;
                }else
                if (($WIND_DIR>=292.5) && ($WIND_DIR<337.5)){
                    $viento = 'NO';
                }else{
                    $viento = '';
                }
            }
        }
        return $viento;
    }

    public function setDailyTotal_Old(){
        date_default_timezone_set('America/La_Paz');
        $anio = date("Y");
        $mes = date("n");
        $dia = date("j");
        
        try{

            DB::beginTransaction();
            $result = DB::statement("INSERT INTO daily_data (station_id, f_year, f_month, f_day, tempout, tempoutmax, tempoutmin, dewptout, humout, winddir, windspeed, windspeedhi, winddir2, windspeed2, windspeedhi2, raintotal, press, heatindexout, uvindex, solrad, solevo, soiltemp1, soilhum1, leaftemp1, leafhum1, total_leafhum1, total_tempout, total_humout, total_rainrate, soiltemp2, soilhum2, leaftemp2, leafhum2, total_leafhum2, soiltemp3, soilhum3, leaftemp3, leafhum3, total_leafhum3, soiltemp4, soilhum4, leaftemp4, leafhum4, total_leafhum4) 
            SELECT station_id, f_year, f_month, f_day, tempout, tempoutmax, tempoutmin, dewptout, humout, winddir, windspeed, windspeedhi, winddir2, windspeed2, windspeedhi2, raintotal, press, heatindexout, uvindex, solrad, solevo, soiltemp1, soilhum1, leaftemp1, leafhum1, total_leafhum1, total_tempout, total_humout, total_rainrate, soiltemp2, soilhum2, leaftemp2, leafhum2, total_leafhum2, soiltemp3, soilhum3, leaftemp3, leafhum3, total_leafhum3, soiltemp4, soilhum4, leaftemp4, leafhum4, total_leafhum4 
            FROM (SELECT station_id AS station_id, year(receipt_date) AS f_year, month(receipt_date) AS f_month, dayofmonth(receipt_date) AS f_day, avg(nullif(tempout,-(9999))) AS tempout,  max(nullif(tempout,-(9999))) AS tempoutmax, min(nullif(tempout,-(9999))) AS tempoutmin, avg(nullif(dewptout,-(9999))) AS dewptout, avg(nullif(humout,-(9999))) AS humout, avg(nullif(winddir,-(9999))) AS winddir, avg(nullif(windspeed,-(9999))) AS windspeed, max(nullif(windspeedhi,-(9999))) AS windspeedhi, avg(nullif(winddir2,-(9999))) AS winddir2, avg(nullif(windspeed2,-(9999))) AS windspeed2, max(nullif(windspeedhi2,-(9999))) AS windspeedhi2, max(nullif(raintotal,-(9999))) AS raintotal, avg(nullif(press,-(9999))) AS press, avg(nullif(heatindexout,-(9999))) AS heatindexout, avg(nullif(uvindex,-(9999))) AS uvindex, avg(nullif(solrad,-(9999))) AS solrad, max(nullif(solevo,-(9999))) AS solevo, avg(nullif(soiltemp1,-(9999))) AS soiltemp1, avg(nullif(soilhum1,-(9999))) AS soilhum1, avg(nullif(leaftemp1,-(9999))) AS leaftemp1, avg(nullif(leafhum1,-(9999))) AS leafhum1, count(nullif(leafhum1,-(9999))) AS total_leafhum1, count(nullif(tempout,-(9999))) AS total_tempout, count(nullif(humout,-(9999))) AS total_humout, count(nullif(RAIN_RATE,-(9999))) AS total_rainrate, avg(nullif(soiltemp2,-(9999))) AS soiltemp2, avg(nullif(soilhum2,-(9999))) AS soilhum2, avg(nullif(leaftemp2,-(9999))) AS leaftemp2, avg(nullif(leafhum2,-(9999))) AS leafhum2, count(nullif(leafhum2,-(9999))) AS total_leafhum2, avg(nullif(soiltemp3,-(9999))) AS soiltemp3, avg(nullif(soilhum3,-(9999))) AS soilhum3, avg(nullif(leaftemp3,-(9999))) AS leaftemp3, avg(nullif(leafhum3,-(9999))) AS leafhum3, count(nullif(leafhum3,-(9999))) AS total_leafhum3, avg(nullif(soiltemp4,-(9999))) AS soiltemp4, avg(nullif(soilhum4,-(9999))) AS soilhum4, avg(nullif(leaftemp4,-(9999))) AS leaftemp4, avg(nullif(leafhum4,-(9999))) AS leafhum4, count(nullif(leafhum4,-(9999))) AS total_leafhum4 
            FROM v_data 
            WHERE year(receipt_date)=".$anio." and month(receipt_date)=".$mes." and dayofmonth(receipt_date)=".$dia." GROUP BY station_id,year(receipt_date),month(receipt_date),dayofmonth(receipt_date)) as D ON DUPLICATE KEY UPDATE tempout = d.tempout, tempoutmax = d.tempoutmax, tempoutmin = d.tempoutmin, dewptout = d.dewptout, humout = d.humout, winddir = d.winddir, windspeed = d.windspeed, windspeedhi = d.windspeedhi, winddir2 = d.winddir2, windspeed2 = d.windspeed2, windspeedhi2 = d.windspeedhi2, raintotal = d.raintotal, press = d.press, heatindexout = d.heatindexout, uvindex = d.uvindex, solrad = d.solrad, solevo = d.solevo, soiltemp1 = d.soiltemp1, soilhum1 = d.soilhum1, leaftemp1 = d.leaftemp1, leafhum1 = d.leafhum1, total_leafhum1 = d.total_leafhum1, total_tempout = d.total_tempout, total_humout = d.total_humout, total_rainrate = d.total_rainrate, soiltemp2 = d.soiltemp2, soilhum2 = d.soilhum2, leaftemp2 = d.leaftemp2, leafhum2 = d.leafhum2, total_leafhum2 = d.total_leafhum2, soiltemp3 = d.soiltemp3, soilhum3 = d.soilhum3, leaftemp3 = d.leaftemp3, leafhum3 = d.leafhum3, total_leafhum3 = d.total_leafhum3, soiltemp4 = d.soiltemp4, soilhum4 = d.soilhum4, leaftemp4 = d.leaftemp4, leafhum4 = d.leafhum4, total_leafhum4 = d.total_leafhum4");
            
            if($result){
                DB::commit();
            }else{
                $result = DB::statement("INSERT INTO daily_data (station_id, f_year, f_month, f_day, winddirstr) SELECT station_id, f_year, f_month, f_day, winddirstr FROM 
                            (SELECT station_id, f_year, f_month, f_day, wind_dir_str, max(count) AS contador FROM v_data_wind WHERE f_year=".$anio." AND f_month=".$mes." AND f_day=".$dia." GROUP BY station_id,f_year,f_month,f_day, winddirstr) d ON DUPLICATE KEY UPDATE winddirstr = d.winddirstr");
                if($result){
                    DB::commit();
                }else{

                    $result = DB::statement("INSERT INTO daily_data (station_id, f_year, f_month, f_day, winddirstr2) SELECT station_id, f_year, f_month, f_day, winddirstr FROM 
                            (SELECT station_id, f_year, f_month, f_day, wind_dir_str, max(count) AS contador FROM v_data_wind2 WHERE f_year=".$anio." AND f_month=".$mes." AND f_day=".$dia." GROUP BY station_id,f_year,f_month,f_day, winddirstr) d ON DUPLICATE KEY UPDATE winddirstr2 = d.winddirstr");

                    if($result){
                        DB::commit();
                    }
                }
            }            
        }catch(\Exception $e){
            DB::rollback();
            //return back()->with('error', $e->getMessage());
            return back()->with('error', $e->getMessage());
            //return back()->with('error', $i."-".$this->nombres." ".$this->apellido_paterno);
        }    
    }
}
