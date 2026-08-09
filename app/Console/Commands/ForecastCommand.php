<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\Models\ForecastM;
use App\Models\StationM;
use Illuminate\Support\Facades\DB;
use DateTimeZone;


class ForecastCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:forecast-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $formato_entrada = 'D d/m H\H';
        $dias = [
            'Lun' => 'Mon',
            'Mar' => 'Tue',
            'Mié' => 'Wed',
            'Jue' => 'Thu',
            'Vie' => 'Fri',
            'Sáb' => 'Sat',
            'Dom' => 'Sun',
        ];       
       
        

        $stations = StationM::select('id','latitude','longitude')->get();
        //dd($stations);
        foreach($stations as $station){
            $url = "https://www.meteopt.com/modelos/meteogramas/gfs.php?lat=".$station->latitude."&lon=".$station->longitude."&lang=es&type=txt&units=m&run=06";
            $results_page = $this->curl($url);
            //dd($url);
            $results_page = $this->scrape_between($results_page, '<table class="datatxt"><tr class="datatxt">', '</tr></table><br><table class="rodape">');
            $separate_results = explode('<td class="data', $results_page);
            $i = 1;
            $row = array();
            $result = array();
            foreach ($separate_results as $separate_result) {
                if (($i % 20) == 0) {
                    if (count($row)>0) {
                        $result[] = $row;
                    }
                    $i = 1;
                }
                if ($separate_result != "") {
                    $row[$i] = trim($this->scrape_between($separate_result, '>', '<'));
                    $aux = trim($this->scrape_between($separate_result, '<img src="/modelos/meteogramas/gfs_imagens/', '.png"'));
                    if ($aux!='') {
                        $i = $i + 1;
                        $row[$i] = $aux;
                    }
                    $i = $i + 1;
                }
            }

            if(count($result)>0){
            
                ForecastM::Where('station_id',$station->id)->delete();
                $startDate =  date('Y-m-d').' 00:00:00';
                $endDate =  date('Y-m-d').' 23:59:59';
                $TEMPmin = DB::connection('db_current_year')
                    ->table('data')
                    ->where('station_id',$station->id)
                    ->where('tempoutmin','!=',0)
                    ->whereBetween('receipt_date',[$startDate,$endDate])
                    ->min('tempoutmin');         
                $i = 0;
                $diff = 0;

                $diff = null;
                $flagFirstMajor= false;
                $t2m_original_aux = null; 
                while ($i<count($result)) {
                            
                    $fecha_raw = $result[$i][2];            
                    //FOREACH: de español a ingles
                    $fecha_limpia = preg_replace('/\s+/', ' ', trim($fecha_raw)); 
                    foreach ($dias as $es => $en) {
                        if (strpos($fecha_limpia, $es) === 0) { 
                            $fecha_limpia = str_replace($es, $en, $fecha_limpia);
                            break;
                        }
                    }
                    $formato_entrada = 'D d/m H\H';            
                    $fechaFormato = DateTime::createFromFormat($formato_entrada, $fecha_limpia, new DateTimeZone('UTC'));
                
                    if ($fechaFormato != false) {
                        if($result[$i][13]!='' || $result[$i][13]!=null){
                            $fechaFormato->setTimezone(new DateTimeZone('America/La_Paz'));
                            $fecha = $fechaFormato->format('Y-m-d H:i:s');            
                            $t2m_original = $result[$i][13];
                            
                            if($i==0){
                                $diff = $t2m_original - ($TEMPmin ?? ($t2m_original + 2.4));//Solo una vez el valor de $diff, tambien se agrega un valor cuero de 2.4 en caaso $TEMPmin sea nulo
                                $t2m = ($TEMPmin<=0)?$t2m_original:$TEMPmin;
                                $t2m_original_aux = $t2m_original;
                            }else if($i==1){
                                $t2m = $t2m_original - (-$diff);
                                
                                if($t2m_original>$t2m_original_aux){
                                    $flagFirstMajor= true;
                                }else{
                                    $flagFirstMajor= false;
                                }
                                $t2m_original_aux = $t2m_original;
                            
                            }else{
                                

                                if($t2m_original>$t2m_original_aux){
                                    if($flagFirstMajor){
                                        $t2m = $t2m_original - (-($diff)*1.5); 
                                                                
                                    }else{
                                        $t2m = $t2m_original - (-$diff);
                                        $flagFirstMajor= true;
                                    }                        
                                    $t2m_original_aux = $t2m_original;
                                }else{
                                    
                                    if($flagFirstMajor){
                                        $t2m = $t2m_original - (-($diff)*1.5);
                                        $flagFirstMajor= false;  
                                                            
                                    }else{
                                        $t2m = $t2m_original - (-$diff);
                                        $flagFirstMajor= true;
                                    }
                                    $t2m_original_aux = $t2m_original;
                                } 
                            }
                            
                            
                            $dp = ($t2m-(14.55+0.114*$t2m)*(1-(0.01*$result[$i][14]))-pow(((2.5+0.007*$t2m)*(1-(0.01*$result[$i][14]))),3)-(15.9+0.117*$t2m)*pow((1-(0.01*$result[$i][14])),14)); 
                                        
                            try {
                                DB::beginTransaction(); 
                                ForecastM::create([
                                    'reg_date' => $fecha,
                                    'v10m'=> $result[$i][3],
                                    'v10m_dir'=> $result[$i][4],
                                    'v850'=> $result[$i][5],
                                    'v850_dir'=> $result[$i][6],
                                    'prec'=> $result[$i][7],
                                    'cape'=> $result[$i][8],
                                    'li'=> $result[$i][9],
                                    'dam'=>$result[$i][10],
                                    'a850'=>$result[$i][11],
                                    'a500'=>$result[$i][12],
                                    't2m'=>$t2m,
                                    'hr2m'=>$result[$i][14],
                                    't850'=>$result[$i][15],
                                    't500'=>$result[$i][16],
                                    'baro'=>$result[$i][17],
                                    'cloud'=>$result[$i][18],
                                    'snow'=>$result[$i][19],
                                    'dp'=> $dp,
                                    'tmax'=> null,
                                    'tmin'=> null,
                                    'sh_ts'=> max($this->SH($result[$i][7],$result[$i][9]),$this->TS($result[$i][7],$result[$i][9]),$this->GR20($result[$i][7],$result[$i][9])),
                                    'icon'=> max($this->DZ($result[$i][7]),$this->RA($result[$i][7]),$this->SN($result[$i][13],$result[$i][7]),$this->SH($result[$i][7],$result[$i][9]),$this->TS($result[$i][7],$result[$i][9]),$this->GR20($result[$i][7],$result[$i][9]),$this->SKY($result[$i][18])), 
                                    'icon_image' => $this->icono($fecha, $t2m,$result[$i][7],$result[$i][9],$result[$i][18]),                    
                                    'min_t2m'=>$t2m,
                                    'min_dp'=> $dp,
                                    'state'=>1,                    
                                    'station_id' => $station->id,                        
                                ]);
                                DB::commit();
                            } catch (\Throwable $th) {
                                DB::rollBack();
                                //dd($th);
                                //throw $th;
                                //echo "Forecast (create): ".$th->getMessage();
                            }
                            
                        }
                    }
                    
                    $i = $i + 1;
                }
            
                $forecasts = DB::table('forecasts')
                                ->selectRaw('DATE(reg_date) as day, MAX(t2m) as tmax, MIN(t2m) as tmin, MAX(v10m) as v10mMax, MAX(hr2m) as hr2mMax, MIN(hr2m) as hr2mMin')
                                ->groupBy(DB::raw('DATE(reg_date)'))
                                ->where('station_id',$station->id)
                                ->get();
                
                

                foreach ($forecasts as $forecast) {
                    $wind_speed_max = "";
                    if ($forecast->v10mMax > 12) {
                        $wind_speed_max .= "";
                    } elseif ($forecast->v10mMax > 9) {
                        $wind_speed_max .= "25";
                    } else {
                        $wind_speed_max .= "15";
                    }
                    
                    // Segunda evaluación
                    if ($forecast->v10mMax > 18) {
                        $wind_speed_max .= "";
                    } elseif ($forecast->v10mMax > 12) {
                        $wind_speed_max .= "35";
                    } else {
                        $wind_speed_max .= "";
                    }
                    
                    // Tercera evaluación
                    if ($forecast->v10mMax > 23) {
                        $wind_speed_max .= "";
                    } elseif ($forecast->v10mMax > 18) {
                        $wind_speed_max .= "45";
                    } else {
                        $wind_speed_max .= "";
                    }
                    
                    // Cuarta evaluación
                    if ($forecast->v10mMax > 33) {
                        $wind_speed_max .= "";
                    } elseif ($forecast->v10mMax > 23) {
                        $wind_speed_max .= "65";
                    } else {
                        $wind_speed_max .= "";
                    }
                    
                    // Quinta evaluación
                    if ($forecast->v10mMax > 33) {
                        $wind_speed_max .= "80";
                    } elseif ($forecast->v10mMax > 35) {
                        $wind_speed_max .= "90";
                    } else {
                        $wind_speed_max .= "";
                    }

                    try {
                        DB::beginTransaction();  
                        DB::table('forecasts')
                            ->whereDate('reg_date', $forecast->day)
                            ->where('station_id',$station->id)
                            ->update([
                                'tmax' => $forecast->tmax + 0.8,
                                'tmin' => $forecast->tmin,
                                'v10m_max' => $wind_speed_max!=''?$wind_speed_max:null,
                                'hr2m_max' => $forecast->hr2mMax,
                                'hr2m_min' => $forecast->hr2mMin,

                            ]);
                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        //dd($th);
                        //echo "Update(tmax, tmin, v10mmax, hr2mmax, hr2mmin): ".$th->getMessage();                  
                    }
                } 
            }         
           
        }
       
    }

    function curl($url) {
    
        $options = Array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",
            CURLOPT_URL => $url,
        );
         
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    function scrape_between($data, $start, $end){
        $data = stristr($data, $start);
        $data = substr($data, strlen($start));
        $stop = stripos($data, $end);
        $data = substr($data, 0, $stop);
        return $data;  
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
