<?php

namespace App\Http\Controllers;

use App\Models\ForecastM;
use App\Models\StationM;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Http;

class ForecastC extends Controller
{
    public function index(){
        return view('forecasts.index');
    }

    public function api1(){
        $apiKey = 'c31b5658118845b5911140811251001';
        $url = "http://api.weatherapi.com/v1/current.json?key={$apiKey}&q=bulk";

        $payload = [
            'locations' => [
                [
                    'q' => '53,-0.12',
                    'custom_id' => 'my-id-1',
                ],
                [
                    'q' => 'London',
                    'custom_id' => 'any-internal-id',
                ],
                [
                    'q' => '90201',
                    'custom_id' => 'us-zipcode-id-765',
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->successful()) {
            return $response->json(); // Devuelve la respuesta como JSON
        }

        return response()->json(['error' => 'Error fetching data'], 500);
    }

    public function verifIconoFecha(){
                $maxIconsByDate = ForecastM::where('station_id', 22)
					->where('forecasts.reg_date', '>=', date('Y-m-d') . ' 00:00:00')
					->select(
						DB::raw('DATE(forecasts.reg_date) AS reg_date'),
						DB::raw('MAX(forecasts.icon) AS max_icon')
					)
					->groupBy(DB::raw('DATE(forecasts.reg_date)'))
					->get();

               // dd($maxIconsByDate);

				$frequentIconsByDate = ForecastM::where('station_id', 22)
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
                dd($maxIconsByDate,$frequentIconsByDate);
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

                dd($iconForDate);
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

    public function getForecastCurl(Request $request, $fromLivewire = false, bool $byCoordinates = false){
           
        $station_id = null;
        $latitude = $request->input('lat');
        $longitude = $request->input('lon');

        if (!is_null($latitude) && $latitude !== '' && !is_null($longitude) && $longitude !== '') {
            $byCoordinates = true;
            $stations = collect([
                (object) [
                    'id' => null,
                    'name' => 'Custom Coordinates',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
            ]);
        } else {
            if ($request->has('station_id')) {
                $station_id = $request->input('station_id');
                if(!is_null($station_id) && $station_id!=''){                
                   $stations = StationM::select('id','name','latitude','longitude')->where('id',$station_id)->get(); 
                }else{
                    $stations = StationM::select('id','name','latitude','longitude')->orderby('id','asc')->get();
                    /*$stations = DB::table('stations as s')
                    ->select(
                        's.id', 's.name', 's.latitude', 's.longitude',
                        DB::raw('MIN(f.reg_date) AS oldest_date')
                    )
                    ->leftJoin('forecasts as f', 's.id', '=', 'f.station_id')
                    ->groupBy('s.id', 's.name', 's.latitude', 's.longitude')
                    ->havingRaw('COUNT(f.station_id) < 110')
                    ->orHavingRaw('MIN(DATE(f.reg_date)) < DATE(NOW())')
                    ->orHavingRaw('COUNT(f.station_id) = 0')
                    ->orderBy('s.id', 'asc')
                    ->get();*/
                }          
            }else {
                $stations = StationM::select('id','name','latitude','longitude')->orderby('id','asc')->get();
                //$stations = StationM::select('id','name','latitude','longitude')->whereIn('id',[22,332,480])->orderby('id','asc')->get();
                /*$stations = DB::table('stations as s')
                    ->select(
                        's.id', 's.name', 's.latitude', 's.longitude',
                        DB::raw('MIN(f.reg_date) AS oldest_date')
                    )
                    ->leftJoin('forecasts as f', 's.id', '=', 'f.station_id')
                    ->groupBy('s.id', 's.name', 's.latitude', 's.longitude')
                    ->havingRaw('COUNT(f.station_id) < 110')
                    ->orHavingRaw('MIN(DATE(f.reg_date)) < DATE(NOW())')
                    ->orHavingRaw('COUNT(f.station_id) = 0')
                    ->orderBy('s.id', 'asc')                
                    ->get();*/
            }
        }
        
        // Solo configurar output buffering si NO es desde Livewire
        if (!$fromLivewire) {
            if (ob_get_level() > 0) {
                ob_end_flush();
            }
            
            ob_implicit_flush(true);

            while (ob_get_level()) {
                ob_end_clean();
            }
            
            echo "PRONOSTICOS - SENVATEC <br><br>";
            flush();
        }

        set_time_limit(1800);
        $cont=1;
        $processedStations = 0;
        $errors = [];
        $resultData = [
            'forecasts' => [],
            'alerts' => [],
        ];
        
        //dd($stations);
        foreach($stations as $station){
            $fullData = [];
            
            $url = "https://api.meteopt.com/gfs/json?lat=".$station->latitude."&lon=".$station->longitude."&run=06&lang=en&unit=c";
            //$url2 = "https://www.meteopt.com/modelos/meteogramas/gfs.php?lat=".$station->latitude."&lon=".$station->longitude."&lang=es&type=txt&units=m&run=06";

            if (!$fromLivewire) {
                echo $cont.".- ".$station->name.": Procesando...";
                flush();
            }
            
            $results_page = $this->curl($url);
            
          
            $result = json_decode($results_page);
            //dd($result,count($result->data->wind10m->values));
            if (isset($result->error) && $result->error === true) {
                $errors[] = ['station' => $station->name, 'error' => $result->message];
                if (!$fromLivewire) {
                    echo '<a style="color: red; font-weight: bold; text-decoration: none;">' . preg_replace("/\r\n|\r|\n/", ' ', $result->message) . '</a><br>';
                    flush();
                }
            } else {                
                if(count($result->data->wind10m->values)>0){
                    $limit = count($result->data->wind10m->values);
                    $current_year = date('Y');
                    if (!$byCoordinates) {
                        ForecastM::Where('station_id',$station->id)->delete();
                    }
                    $startDate =  date('Y-m-d').' 00:00:00';
                    $endDate =  date('Y-m-d').' 23:59:59';
                    /*$TEMPmin = DB::connection('db'.$current_year)
                        ->table('data')
                        ->where('station_id',$station->id)
                        ->where('tempoutmin','!=',0)
                        ->whereBetween('receipt_date',[$startDate,$endDate])
                        ->min('tempoutmin');*/    
                    $TEMPmin = null;
                    $i = 0;
                    $diff = 0;

                    $diff = null;
                    $flagFirstMajor= false;
                    $t2m_original_aux = null; 
                    while ($i<$limit) {
                        
                        $date_time_raw = $result->forecast_dates->values[$i];
                        //dd($date_time_raw);                  
                        $parts = explode(' ', $date_time_raw);
                        $date_time_with_year = "{$parts[0]} {$parts[1]} {$parts[2]} {$current_year} {$parts[3]}";
                        $hora = str_replace("Z", "", $parts[3]);
                        $hora_completa = "$hora:00 +0000";
                        $date_time_for_php = "{$parts[0]} {$parts[1]} {$parts[2]} {$current_year} $hora_completa";
                        $fecha_utc = DateTime::createFromFormat('l, d M Y H:i O', $date_time_for_php, new DateTimeZone('UTC'));
                        if ($fecha_utc === false) {
                            if (!$fromLivewire) {
                                echo "Error al parsear la fecha";
                                exit;
                            } else {
                                throw new \Exception("Error al parsear la fecha para estación: " . $station->name);
                            }
                        }                    
                        $fecha_utc->setTimezone(new DateTimeZone('America/La_Paz'));                    
                        $fecha = $fecha_utc->format('Y-m-d H:i:s');
                        //dd($fecha);                               
                        $t2m_original = $result->data->tmp2m->values[$i]; 
                        $hr2m = $result->data->rh2m->values[$i];  
                        $v10m = $result->data->wind10m->values[$i]; 
                        $v10m_dir = $result->data->wind10mdir->values[$i]; 
                        $v850 = $result->data->wind850->values[$i]; 
                        $v850_dir = $result->data->wind850dir->values[$i]; 
                        $prec = $result->data->apcp->values[$i]; 
                        $cape = $result->data->cape->values[$i]; 
                        $li = $result->data->lftx->values[$i]; 
                        $dam = $result->data->{'z500-1000'}->values[$i]; 
                        $a850 = $result->data->hgt850mb->values[$i]; 
                        $a500 = $result->data->hgt500mb->values[$i];
                        $t850 = $result->data->tmp850mb->values[$i];
                        $t500 = $result->data->tmp500mb->values[$i];
                        $baro = $result->data->prmsl->values[$i];
                        $cloud = $result->data->tcdc->values[$i];
                        $snow = $result->data->snlevel->values[$i];                 
            
                        if($t2m_original!=='' && $t2m_original!==null){
                            
                            if ($station->id != 480){
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
                            }else{
                               $t2m = $t2m_original; 
                            }
                            
                            
                            $dp = ($t2m-(14.55+0.114*$t2m)*(1-(0.01*$hr2m))-pow(((2.5+0.007*$t2m)*(1-(0.01*$hr2m))),3)-(15.9+0.117*$t2m)*pow((1-(0.01*$hr2m)),14)); 

                            if ($prec == 0.1) {
                                $prec = 0.6;
                            } elseif ($prec == 0.2) {
                                $prec = 0.8;
                            } elseif ($prec == 0.3) {
                                $prec = 0.9;
                            } elseif ($prec == 0.4) {
                                $prec = 1.2;
                            } elseif ($prec == 0.5) {
                                $prec = 1.5;
                            }
                            
                            if (!is_null($v10m_dir)) {
                                if ($v10m_dir >= 337.5 || $v10m_dir < 22.5) {
                                    $winddir_text = 'N';
                                } elseif ($v10m_dir >= 22.5 && $v10m_dir < 67.5) {
                                    $winddir_text = 'NE';
                                } elseif ($v10m_dir >= 67.5 && $v10m_dir < 112.5) {
                                    $winddir_text = 'E';
                                } elseif ($v10m_dir >= 112.5 && $v10m_dir < 157.5) {
                                    $winddir_text = 'SE';
                                } elseif ($v10m_dir >= 157.5 && $v10m_dir < 202.5) {
                                    $winddir_text = 'S';
                                } elseif ($v10m_dir >= 202.5 && $v10m_dir < 247.5) {
                                    $winddir_text = 'SO';
                                } elseif ($v10m_dir >= 247.5 && $v10m_dir < 292.5) {
                                    $winddir_text = 'O';
                                } elseif ($v10m_dir >= 292.5 && $v10m_dir < 337.5) {
                                    $winddir_text = 'NO';
                                }
                            } else {
                                $winddir_text = '';
                            }                        

                            if (!is_null($v850_dir)) {
                                if ($v850_dir >= 337.5 || $v850_dir < 22.5) {
                                    $winddir_v850_text = 'N';
                                } elseif ($v850_dir >= 22.5 && $v850_dir < 67.5) {
                                    $winddir_v850_text = 'NE';
                                } elseif ($v850_dir >= 67.5 && $v850_dir < 112.5) {
                                    $winddir_v850_text = 'E';
                                } elseif ($v850_dir >= 112.5 && $v850_dir < 157.5) {
                                    $winddir_v850_text = 'SE';
                                } elseif ($v850_dir >= 157.5 && $v850_dir < 202.5) {
                                    $winddir_v850_text = 'S';
                                } elseif ($v850_dir >= 202.5 && $v850_dir < 247.5) {
                                    $winddir_v850_text = 'SO';
                                } elseif ($v850_dir >= 247.5 && $v850_dir < 292.5) {
                                    $winddir_v850_text = 'O';
                                } elseif ($v850_dir >= 292.5 && $v850_dir < 337.5) {
                                    $winddir_v850_text = 'NO';
                                }
                            } else {
                                $winddir_v850_text = '';
                            }

                            $fullData[] = [
                                'reg_date' => $fecha,
                                'v10m'=> $v10m,
                                'v10m_dir'=> $winddir_text,
                                'v850'=> $v850,
                                'v850_dir'=> $winddir_v850_text,
                                'prec'=> $prec,
                                'cape'=> $cape,
                                'li'=> $li,
                                'dam'=>$dam,
                                'a850'=>$a850,
                                'a500'=>$a500,
                                't2m'=>$t2m,
                                'hr2m'=>$hr2m,
                                't850'=>$t850,
                                't500'=>$t500,
                                'baro'=>$baro,
                                'cloud'=>$cloud,
                                'snow'=>$snow,
                                'dp'=> $dp,
                                'tmax'=> null,
                                'tmin'=> null,
                                'sh_ts'=> max($this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li)),
                                'icon'=> max($this->DZ($prec),$this->RA($prec),$this->SN($t2m,$prec),$this->SH($prec,$li),$this->TS($prec,$li),$this->GR20($prec,$li),$this->SKY($cloud)), 
                                'icon_image' => $this->icono($fecha, $t2m,$prec,$li,$cloud),                    
                                'min_t2m'=>$t2m,
                                'min_dp'=> $dp,
                                'state'=>1,                    
                                'station_id' => $station->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            
                        }                        
                        
                        $i = $i + 1;
                    }

                    if (!$byCoordinates) {
                        try {
                            DB::beginTransaction(); 
                            ForecastM::insert($fullData);
                            DB::commit();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            //dd($th);
                            //throw $th;
                            //echo "Forecast (create): ".$th->getMessage();
                        }
                    }
                    $forecasts = $byCoordinates
                        ? collect($this->buildDailyAggregates($fullData))->map(function ($item) {
                            return (object) $item;
                        })
                        : DB::table('forecasts')
                            ->selectRaw('DATE(reg_date) as day, MAX(t2m) as tmax, MIN(t2m) as tmin, MAX(v10m) as v10mMax, MAX(hr2m) as hr2mMax, MIN(hr2m) as hr2mMin, SUM(prec) as precTotal,MAX(icon) AS icon,MIN(dp) AS dp,MAX(sh_ts) AS sh_ts')
                            ->groupBy(DB::raw('DATE(reg_date)'))
                            ->where('station_id',$station->id)
                            ->get();                    
                    
                    
                    //$fullData = [];
                    $data_f = [];                    

                    $dailyUpdates = [];
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

                        //---------  Para alertas ------------
                        if (floatval($forecast->sh_ts) == 18) {
                            $data_f[] = ["description" => "Tormentas eléctricas y lluvia moderada a fuerte","reg_date" => strval($forecast->day),"f" =>1,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];                            
                        }
                        if (floatval($forecast->v10mMax) > 23 && floatval($forecast->v10mMax) <= 33) {
                            $data_f[] = ["description" => "Viento Máximo 65 Km/h","reg_date" => strval($forecast->day),"f" => 2,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];

                        } elseif (floatval($forecast->v10mMax) > 33 && floatval($forecast->v10mMax) <= 35) {
                            $data_f[] = ["description" => "Viento Máximo 80 Km/h","reg_date" => strval($forecast->day),"f" => 2,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];
                        
                        } elseif (floatval($forecast->v10mMax) > 35) {
                            $data_f[] = ["description" => "Viento Máximo 90 Km/h","reg_date" => strval($forecast->day),"f" => 2,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];
                        }

                        if (floatval($forecast->icon) == 20) {
                            $data_f[] = ["description" => "80% Tormentas Eléctricas con Lluvia y Granizo","reg_date" => strval($forecast->day),"f" => 3,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];
                        }

                        if (floatval($forecast->tmin) <= 1.4 && floatval($forecast->dp) <= 0) {
                            $data_f[] = ["description" => "90% Probable Helada","reg_date" => strval($forecast->day),"f" => 4,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];
                        }

                        if (floatval($forecast->icon) == 14) {
                            $data_f[] = ["description" => "80% Nieve","reg_date" => strval($forecast->day),"f" => 5,"station_id" => $station->id,"state" => 1,"created_at" => now(),"updated_at" => now()];
                        }

                        //------------------------------------

                        if ($byCoordinates) {
                            $dailyUpdates[] = [
                                'day' => $forecast->day,
                                'tmax' => $forecast->tmax + 0.8,
                                'tmin' => $forecast->tmin,
                                'v10m_max' => $wind_speed_max!=''?$wind_speed_max:null,
                                'hr2m_max' => $forecast->hr2mMax,
                                'hr2m_min' => $forecast->hr2mMin,
                                'prec_total' => $forecast->precTotal,
                                'icon' => $forecast->icon,
                                'icon_image' => $forecast->icon_image ?? null,
                                'dp' => $forecast->dp,
                                'sh_ts' => $forecast->sh_ts,
                            ];
                        } else {
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
                                        'prec_total' => $forecast->precTotal,

                                    ]);
                                DB::commit();
                            } catch (\Throwable $th) {
                                DB::rollBack();
                                //dd($th);
                                //echo "Update(tmax, tmin, v10mmax, hr2mmax, hr2mmin): ".$th->getMessage();
                            }
                        }
                    }

                    
                    if ($byCoordinates) {
                        $stationKey = sprintf('%s,%s', $station->latitude, $station->longitude);
                        $resultData['forecasts']= [
                            /*'station' => [
                                'id' => $station->id,
                                'name' => $station->name,
                                'latitude' => $station->latitude,
                                'longitude' => $station->longitude,
                            ],*/
                            'hourly' => $fullData,
                            'daily' => $dailyUpdates,
                        ];
                        $resultData['alerts'] = [
                            'alerts' => $data_f,                            
                        ];
                    } else {
                        try {
                            DB::table('alerts')->Where('station_id',$station->id)->delete();
                            DB::table('alerts')->insert($data_f);
                        } catch (\Throwable $th) {
                        }
                    }
                    
                    $processedStations++;
                    if (!$fromLivewire) {
                        echo '<a href="'.$url2.'" target="_blank" style="color: green; font-weight: bold; text-decoration: none;">Completada!🌡☀️⛅☔❄ Click aqui para ver más</a><br>';
                        flush();
                    }
                }else{
                    if (!$fromLivewire) {
                        echo '<a href="'.$url2.'" target="_blank" style="color: red; font-weight: bold; text-decoration: none;"> No pudo completarse! Click aqui para ver el problema</a><br>';
                        flush();
                    }
                }
                $cont=$cont+1;
            }
        }       
        
        if (!$fromLivewire) {
            echo "<br> ******************* Ejecución Finalizada para pronosticos cada 3 horas ********************<br>";
        }
        
        // Retornar resultado para Livewire
        if ($fromLivewire) {
            return [
                'success' => true,                
                'errors' => $errors,
                'data' => $byCoordinates ? $resultData : null,
            ];
        }

        if ($byCoordinates) {
            return $resultData;
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
		return ((($prec>=0.78)&&($li<=-8.5))?800000000:($prec<=1?0:($li<-12.1?900000000:0)));
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

    protected function buildDailyAggregates(array $fullData): array
    {
        $daily = [];
        foreach ($fullData as $entry) {
            $day = substr($entry['reg_date'], 0, 10);
            if (!isset($daily[$day])) {
                $daily[$day] = [
                    'day' => $day,
                    'tmax' => $entry['t2m'],
                    'tmin' => $entry['t2m'],
                    'v10mMax' => $entry['v10m'],
                    'hr2mMax' => $entry['hr2m'],
                    'hr2mMin' => $entry['hr2m'],
                    'precTotal' => $entry['prec'],
                    'icon' => $entry['icon'],
                    'icon_image' => $entry['icon_image'] ?? null,
                    'dp' => $entry['dp'],
                    'sh_ts' => $entry['sh_ts'],
                ];
            } else {
                $daily[$day]['tmax'] = max($daily[$day]['tmax'], $entry['t2m']);
                $daily[$day]['tmin'] = min($daily[$day]['tmin'], $entry['t2m']);
                $daily[$day]['v10mMax'] = max($daily[$day]['v10mMax'], $entry['v10m']);
                $daily[$day]['hr2mMax'] = max($daily[$day]['hr2mMax'], $entry['hr2m']);
                $daily[$day]['hr2mMin'] = min($daily[$day]['hr2mMin'], $entry['hr2m']);
                $daily[$day]['precTotal'] += $entry['prec'];
                if ($entry['icon'] >= $daily[$day]['icon']) {
                    $daily[$day]['icon'] = $entry['icon'];
                    $daily[$day]['icon_image'] = $entry['icon_image'] ?? $daily[$day]['icon_image'];
                }
                $daily[$day]['dp'] = min($daily[$day]['dp'], $entry['dp']);
                $daily[$day]['sh_ts'] = max($daily[$day]['sh_ts'], $entry['sh_ts']);
            }
        }

        return array_values($daily);
    }
}
