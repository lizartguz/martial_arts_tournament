<?php
set_time_limit(2000);
require_once("pf3connection.php");
$est = new Estacion();

$estaciones = $est->getEstaciones();
$forecast = new Forecast();

//$idestacion = 0;
//if (isset($_POST["idestacion"])) {$idestacion = $_POST["idestacion"];}
echo "Procesando..."."<br>";
$contador=1;
foreach($estaciones as $estacion){
   // if ($estacion['id']==$idestacion) {
        $url = "https://www.meteopt.com/modelos/meteogramas/gfs.php?lat=".$estacion['latitude']."&lon=".$estacion['longitude']."&lang=es&type=txt&units=m&run=06";
        echo $url."<br>";
        $results_page = curl($url);
        
        $results_page = scrape_between($results_page, '<table class="datatxt"><tr class="datatxt">', '</tr></table><br><table class="rodape">');
        
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
                $row[$i] = trim(scrape_between($separate_result, '>', '<'));
                $aux = trim(scrape_between($separate_result, '<img src="/modelos/meteogramas/gfs_imagens/', '.png"'));
                if ($aux!='') {
                    $i = $i + 1;
                    $row[$i] = $aux;
                }
                $i = $i + 1;
            }
        }
        //usleep(10000000);
        if (count($result)>0) {
            $forecast->delete_meteopt($estacion['id']);
            $today = date('d/m/Y 5:00:00');
            $dato = $est->getDato($estacion['id'],$today,1800);
            $i = 0;
            $promedios = array();
            $linea_2 = array();
            $sw = 0;
            $dif_temp_out = 0;
            $dif_hum_out = 0;
            $swDif = true;
            while ($i<count($result)) {
                $linea = $result[$i];
                if ($linea[13]==-273.2) {
                    if (($i==0)) { 
                        $linea =  $result[$i];
                        $linea[3] = $dato['windspeed']; //viento vel
                        $linea[4] = 'N'; //viento dir
                        $linea[13] = $dato['tempout']; //temp
                        $linea[14] = $dato['humout']; //hr
                        $linea[7] = $dato['raintotal']; //prec
                        $linea[20] = $dato['dewptout']; //dp
                        $swDif = false;
                    } else {
                    if (($i==count($result)-1)) { break; }
                    $linea[3] = ($result[$i-1][3]-$result[$i+1][3])/2 + $result[$i+1][3];
                    $linea[4] = ($result[$i-1][4]-$result[$i+1][4])/2 + $result[$i+1][4];
                    $linea[5] = ($result[$i-1][5]-$result[$i+1][5])/2 + $result[$i+1][5];
                    $linea[6] = ($result[$i-1][6]-$result[$i+1][6])/2 + $result[$i+1][6];
                    $linea[7] = ($result[$i-1][7]-$result[$i+1][7])/2 + $result[$i+1][7];
                    $linea[8] = ($result[$i-1][8]-$result[$i+1][8])/2 + $result[$i+1][8];
                    $linea[9] = ($result[$i-1][9]-$result[$i+1][9])/2 + $result[$i+1][9];
                    $linea[10] = ($result[$i-1][10]-$result[$i+1][10])/2 + $result[$i+1][10];
                    $linea[11] = ($result[$i-1][11]-$result[$i+1][11])/2 + $result[$i+1][11];
                    $linea[12] = ($result[$i-1][12]-$result[$i+1][12])/2 + $result[$i+1][12];
                    $linea[13] = ($result[$i-1][13]-$result[$i+1][13])/2 + $result[$i+1][13];
                    $linea[14] = ($result[$i-1][14]-$result[$i+1][14])/2 + $result[$i+1][14];
                    $linea[15] = ($result[$i-1][15]-$result[$i+1][15])/2 + $result[$i+1][15];
                    $linea[16] = ($result[$i-1][16]-$result[$i+1][16])/2 + $result[$i+1][16];
                    $linea[17] = ($result[$i-1][17]-$result[$i+1][17])/2 + $result[$i+1][17];
                    $linea[18] = ($result[$i-1][18]-$result[$i+1][18])/2 + $result[$i+1][18];
                    $linea[19] = ($result[$i-1][19]-$result[$i+1][19])/2 + $result[$i+1][19];
                    $linea[20] = ($linea[13]-(14.55+0.114*$linea[13])*(1-(0.01*$linea[14]))-pow(((2.5+0.007*$linea[13])*(1-(0.01*$linea[14]))),3)-(15.9+0.117*$linea[13])*pow((1-(0.01*$linea[14])),14));
                    }
                }
                $fecha = new DateTime(date('d-m-Y 02:00:00'));
                $fecha->modify($linea[1].' hour');
                $hora = explode(' ',$linea[2]);
                $hora = str_replace('H','',$hora[2]);
                if ($sw==0) {
                    if (($dato['tempout']!='') && ($dato['tempout']!=-9999) && ($dato['tempout']!=null)) {
                        $dif_temp_out = $dato['tempout'] - $linea[13];
                    }
                    //if (($dato['HUM_OUT']!='')&&($dato['HUM_OUT']!=-9999)) {
                    //  $dif_hum_out = $dato['HUM_OUT'] - $linea[14];
                    //}
                    $sw = 1;
                }
                if (!$swDif) {
                    $dif_temp_out = 0;
                }
                //16/08/2019 verificar
                //$linea[13] = $linea[13]+$dif_temp_out;
                if ($linea[14]+$dif_hum_out<6) {
                    $linea[14] = $linea[14];
                } else {
                    $linea[14] = $linea[14]+$dif_hum_out;
                }
                $dp =($linea[13]-(14.55+0.114*$linea[13])*(1-(0.01*$linea[14]))-pow(((2.5+0.007*$linea[13])*(1-(0.01*$linea[14]))),3)-(15.9+0.117*$linea[13])*pow((1-(0.01*$linea[14])),14));
                $linea[20] = $dp;
                if (in_array($hora,array(3,9,15,21))) {
                    if (count($linea_2)>0) {
                        $prec_aux = ($linea[7]+$linea_2[7]);
                        if ((string)$prec_aux=="0.1") {
                            $prec_aux = $prec_aux * 6;
                        };
                        if ((string)$prec_aux=="0.2") {
                            $prec_aux = $prec_aux * 4;
                        };
                        if ((string)$prec_aux=="0.3") {
                            $prec_aux = $prec_aux * 3;
                        };
                        if ((string)$prec_aux=="0.4") {
                            $prec_aux = $prec_aux * 2;
                        };
                        if ((string)$prec_aux=="0.5") {
                            $prec_aux = $prec_aux * 3;
                        };
                        
                        $forecast->create_meteopt($estacion['id'],$fecha->format('d/m/Y H:i:s'),
                                                    max($linea[3],$linea_2[3]),//V10M  //antes era solo linea[3]
                                                    $linea[4],//V10M_DIR
                                                    $linea[5],//V850
                                                    $linea[6],//V850_DIR
                                                    $prec_aux, //PREC
                                                    max($linea[8],$linea_2[8]), //CAPE
                                                    min($linea[9],$linea_2[9]), //LI
                                                    $linea[10], //DAM
                                                    $linea[11], //A850
                                                    $linea[12], //A500
                                                    ($linea[13]+$linea_2[13])/2, //T2M
                                                    ($linea[14]+$linea_2[14])/2, //HR2M
                                                    $linea[15], //T850
                                                    $linea[16], //T500
                                                    ($linea[17]+$linea_2[17])/2, //BARO
                                                    ($linea[18]+$linea_2[18])/2, //NUBES
                                                    min($linea[19],$linea_2[19]), //NIEVE
                                                    ($linea[20]+$linea_2[20])/2, //DP
                                                    max(DZ($linea[7]+$linea_2[7]),RA($linea[7]+$linea_2[7]),SN(($linea[13]+$linea_2[13])/2,$linea[7]+$linea_2[7]),SH($linea[7]+$linea_2[7],min($linea[9],$linea_2[9])),TS($linea[7]+$linea_2[7],min($linea[9],$linea_2[9])),GR20($linea[7]+$linea_2[7],min($linea[9],$linea_2[9])),SKY(($linea[18]+$linea_2[18])/2)), //ICON
                                                    max(SH($linea[7]+$linea_2[7],min($linea[9],$linea_2[9])),TS($linea[7]+$linea_2[7],min($linea[9],$linea_2[9])),GR20($linea[7]+$linea_2[7],min($linea[9],$linea_2[9]))), //SH_TS
                                                    min($linea[13],$linea_2[13]), //MIN_T2M
                                                    min($linea[20],$linea_2[20]) //MIN_DP
                                                );
                    } else {
                        $prec_aux = $linea[7];
                        if ((string)$prec_aux=="0.1") {
                            $prec_aux = $prec_aux * 6;
                        };
                        if ((string)$prec_aux=="0.2") {
                            $prec_aux = $prec_aux * 4;
                        };
                        if ((string)$prec_aux=="0.3") {
                            $prec_aux = $prec_aux * 3;
                        };
                        if ((string)$prec_aux=="0.4") {
                            $prec_aux = $prec_aux * 2;
                        };
                        if ((string)$prec_aux=="0.5") {
                            $prec_aux = $prec_aux * 3;
                        };
                        
                        $forecast->create_meteopt($estacion['id'],$fecha->format('d/m/Y H:i:s'),
                                                    $linea[3],
                                                    $linea[4],
                                                    $linea[5],
                                                    $linea[6],
                                                    $prec_aux,
                                                    $linea[8],
                                                    $linea[9],
                                                    $linea[10],
                                                    $linea[11],
                                                    $linea[12],
                                                    $linea[13],
                                                    $linea[14],
                                                    $linea[15],
                                                    $linea[16],
                                                    $linea[17],
                                                    $linea[18],
                                                    $linea[19],
                                                    $linea[20], //DP
                                                    max(DZ($linea[7]),RA($linea[7]),SN($linea[13],$linea[7]),SH($linea[7],$linea[9]),TS($linea[7],$linea[9]),GR20($linea[7],$linea[9]),SKY($linea[18])), //ICON
                                                    max(SH($linea[7],$linea[9]),TS($linea[7],$linea[9]),GR20($linea[7],$linea[9])), //SH_TS
                                                    $linea[13], //MIN_T2M
                                                    $linea[20] //MIN_DP
                                                );
                    }
                } else {
                    $linea_2 = array();
                    $linea_2[1] = $linea[1];
                    $linea_2[2] = $linea[2];
                    $linea_2[3] = $linea[3];
                    $linea_2[4] = $linea[4];
                    $linea_2[5] = $linea[5];
                    $linea_2[6] = $linea[6];
                    $linea_2[7] = $linea[7];
                    $linea_2[8] = $linea[8];
                    $linea_2[9] = $linea[9];
                    $linea_2[10] = $linea[10];
                    $linea_2[11] = $linea[11];
                    $linea_2[12] = $linea[12];
                    $linea_2[13] = $linea[13];
                    $linea_2[14] = $linea[14];
                    $linea_2[15] = $linea[15];
                    $linea_2[16] = $linea[16];
                    $linea_2[17] = $linea[17];
                    $linea_2[18] = $linea[18];
                    $linea_2[19] = $linea[19];
                    $linea_2[20] = $linea[20];
                }
                $i = $i + 1;
            }
        }
        //alertas TS
        $forecast->delete_meteopt_a($estacion['id']);
        foreach ($forecast->getTS($estacion['id']) as $row) {
            if ($row['ts']==18) {
                $ts_txt = "Tormentas electricas y lluvia moderada a fuerte";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$ts_txt,1);
            }
        }
        //
        
        //alerta viento
        foreach ($forecast->getVMAX($estacion['id']) as $row) {
            if ($row['v10m']>23&$row['v10m']<=33) {
                $v10m_txt = "Viento Maximo 65 Km/h";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$v10m_txt,2);
            }
            if ($row['v10m']>33&$row['v10m']<=35) {
                $v10m_txt = "Viento Maximo 80 Km/h";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$v10m_txt,2);
            }
            if ($row['v10m']>35) {
                $v10m_txt = "Viento Maximo 90 Km/h";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$v10m_txt,2);
            }
        }
        //alerta Granizo
        foreach ($forecast->getMAXICON($estacion['id']) as $row) {
            if ($row['icon']==20) {
                $t_txt = "80% Tormentas Electricas con Lluvia y Granizo";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$t_txt,3);
            }
        }
        //falta heladas
        foreach ($forecast->getTmin_DPmin($estacion['id']) as $row) {
            if ($row['t2m']<=1.4&&$row['dp']<=0) {
                $t_txt = "90%Probable Helada";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$t_txt,4);
            }
        }
        //alerta nevada
        foreach ($forecast->getMAXICON($estacion['id']) as $row) {
            if ($row['icon']==14) {
                $t_txt = "80% Nieve";
                $forecast->create_meteopt_a($estacion['id'],$row['reg_date'],$t_txt,5);
            }
        }
   // }

   echo "Completado: ".$estacion['name']."<br>";
   $contador=$contador+1;
}
echo "Flujo completado";















class Estacion{
    
    public $errors = array();    
    public $messages = array();   
    private $db_connection = null;
    private $db_connection_current = null;

    public function __construct(){
		
        $DB_HOST_STANDARD = "208.109.232.246";
        $DB_NAME_STANDARD = "artguzdb";
        $DB_USER_STANDARD = "root";
        $DB_PASS_STANDARD = "adminartguz";
		$this->db_connection = mysqli_connect($DB_HOST_STANDARD, $DB_USER_STANDARD, $DB_PASS_STANDARD, $DB_NAME_STANDARD);
		mysqli_set_charset( $this->db_connection, 'utf8');
        $DB_HOST_CURRENT_YEAR = "208.109.233.146";
        $DB_NAME_CURRENT_YEAR = "artguzmet2024";
        $DB_USER_CURRENT_YEAR = "root";
        $DB_PASS_CURRENT_YEAR = "dBpr0felclim4";
    	$this->db_connection_current = mysqli_connect($DB_HOST_CURRENT_YEAR, $DB_USER_CURRENT_YEAR, $DB_PASS_CURRENT_YEAR, $DB_NAME_CURRENT_YEAR);
		
		if (!mysqli_connect_errno()) {
		} else {
			$this->errors[] = "Database connection problem.";
		}
	}

    public function getEstaciones(){
		$result = mysqli_query($this->db_connection,"SELECT * FROM stations WHERE state=1 order by name");
        $results_array = array();
        while ($row = $result->fetch_assoc()) {
            $results_array[] = $row;
        }
        return $results_array;
    }            
            
	
    public function getDato($id,$fecha,$intervalo){
		$fecha=str_replace('/','-',$fecha);
		$timestamp=strtotime($fecha);
		$date=date('Y-m-d H:i:s', $timestamp);
    	$result = mysqli_query($this->db_connection_current, "SELECT TIMESTAMPDIFF(SECOND,receipt_date,'$date') AS DIFERENCIA, d.* ".
														" FROM `data` d WHERE `station_id`=$id AND ABS(TIMESTAMPDIFF(SECOND,receipt_date,'$date')) <= $intervalo".
														 " ORDER BY tempout ASC limit 1");
		$row = $result->fetch_assoc();
		return $row;
    }   
}


class Forecast{

	private $db;
	
	function __construct(){
        $DB_HOST_STANDARD = "208.109.232.246";
        $DB_NAME_STANDARD = "artguzdb";
        $DB_USER_STANDARD = "root";
        $DB_PASS_STANDARD = "adminartguz";

        $DB_con = new PDO("mysql:host=".$DB_HOST_STANDARD.";dbname=".$DB_NAME_STANDARD.";charset=utf8",$DB_USER_STANDARD,$DB_PASS_STANDARD);
	    $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->db = $DB_con;
	}
	
	public function create_meteopt($id,$fecha,
							$v10m,
							$v10m_dir,
							$v850,
							$v850_dir,
							$prec,
							$cape,
							$li,
							$dam,
							$a850,
							$a500,
							$t2m,
							$hr2m,
							$t850,
							$t500,
							$baro,
							$nubes,
							$nieve,
							$dp,
                            $icon,
    						$sh_ts,
    						$min_t2m,
    						$min_dp
						  )
	{
		$fecha=str_replace('/','-',$fecha);
		$timestamp=strtotime($fecha);
		$date=date('Y-m-d G:i:s', $timestamp);
		try
		{
			
            $icon_image = icono($fecha,$t2m,$prec,$li,$nubes);

            $stmt = $this->db->prepare("INSERT INTO forecasts(
											station_id,
													   reg_date,
													   v10m,
													   v10m_dir,
													   v850,
													   v850_dir,
													   prec,
													   cape,
													   li,
													   dam,
													   a850,
													   a500,
													   t2m,
													   hr2m,
													   t850,
													   t500,
													   baro,
													   cloud,
													   snow,
													   dp,
    												   icon,
                                                       icon_image,
    												   sh_ts,
        											   min_t2m,
    												   min_dp
										)
										VALUES(
											:station_id,:reg_date,
													   :v10m,
													   :v10m_dir,
													   :v850,
													   :v850_dir,
													   :prec,
													   :cape,
													   :li,
													   :dam,
													   :a850,
													   :a500,
													   :t2m,
													   :hr2m,
													   :t850,
													   :t500,
													   :baro,
													   :nubes,
													   :nieve,
													   :dp,
    												   :icon,
                                                       :icon_image,
    												   :sh_ts,
            										   :min_t2m,
    												   :min_dp
										)");
			$stmt->bindparam(":station_id",$id);
			$stmt->bindparam(":reg_date",$date);
			$stmt->bindparam(":v10m",$v10m);
			$stmt->bindparam(":v10m_dir",$v10m_dir);
			$stmt->bindparam(":v850",$v850);
			$stmt->bindparam(":v850_dir",$v850_dir);
			$stmt->bindparam(":prec",$prec);
			$stmt->bindparam(":cape",$cape);
			$stmt->bindparam(":li",$li);
			$stmt->bindparam(":dam",$dam);
			$stmt->bindparam(":a850",$a850);
			$stmt->bindparam(":a500",$a500);
			$stmt->bindparam(":t2m",$t2m);
			$stmt->bindparam(":hr2m",$hr2m);
			$stmt->bindparam(":t850",$t850);
			$stmt->bindparam(":t500",$t500);
			$stmt->bindparam(":baro",$baro);
			$stmt->bindparam(":nubes",$nubes);
			$stmt->bindparam(":nieve",$nieve);
			$stmt->bindparam(":dp",$dp);
    		$stmt->bindparam(":icon",$icon);
            $stmt->bindparam(":icon_image",$icon_image);
    		$stmt->bindparam(":sh_ts",$sh_ts);
    		$stmt->bindparam(":min_t2m",$min_t2m);
    		$stmt->bindparam(":min_dp",$min_dp);
			$stmt->execute();
			return true;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();	
			return false;
		}
		
	}

    public function delete_meteopt($id)
	{
		$stmt = $this->db->prepare("DELETE from forecasts WHERE station_id=:id ");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		return true;
    }
	
    public function create_meteopt_a($id,$fecha,$desc,$f){
		$fecha=str_replace('/','-',$fecha);
		$timestamp=strtotime($fecha);
    	$date=date('Y-m-d G:i:s', $timestamp);
		try
		{
			$stmt = $this->db->prepare("INSERT INTO alerts(
											station_id,
													   reg_date,
    												   description,
                                                       f
										)
										VALUES(
											:id,:fecha,:descrip,:f
										)");
			$stmt->bindparam(":id",$id);
			$stmt->bindparam(":fecha",$date);
    		$stmt->bindparam(":descrip",$desc);
        	$stmt->bindparam(":f",$f);
			$stmt->execute();
			return true;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();	
			return false;
		}
		
	}
	
	
    public function getTS($id){
		$stmt = $this->db->prepare("SELECT DATE(reg_date) AS reg_date, MAX(sh_ts) AS ts FROM forecasts WHERE station_id=:id GROUP BY DATE(reg_date)");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		$editRow=$stmt->fetchall(PDO::FETCH_BOTH);
		return $editRow;
	}

    public function getVMAX($id){
		$stmt = $this->db->prepare("SELECT DATE(reg_date) AS reg_date, MAX(v10m) AS v10m FROM forecasts WHERE station_id=:id GROUP BY DATE(reg_date)");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		$editRow=$stmt->fetchall(PDO::FETCH_BOTH);
		return $editRow;
	}

    public function getTmin_DPmin($id){
    	$stmt = $this->db->prepare("SELECT DATE(reg_date) AS reg_date, MIN(t2m) AS t2m, MIN(dp) as dp FROM forecasts WHERE station_id=:id GROUP BY DATE(reg_date)");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		$editRow=$stmt->fetchall(PDO::FETCH_BOTH);
		return $editRow;
	}

    public function getMAXICON($id){
    	$stmt = $this->db->prepare("SELECT DATE(reg_date) AS reg_date, MAX(icon) AS icon FROM forecasts WHERE station_id=:id GROUP BY DATE(reg_date)");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		$editRow=$stmt->fetchall(PDO::FETCH_BOTH);
		return $editRow;
	}
	
    public function delete_meteopt_a($id){
		$stmt = $this->db->prepare("DELETE from alerts WHERE station_id=:id ");
		$stmt->bindparam(":id",$id);
		$stmt->execute();
		return true;
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
//		$temp = $temp;
//		$prec = $prec;
    $indice =  max(DZ($prec),RA($prec),SN($temp,$prec),SH($prec,$li),TS($prec,$li),GR20($prec,$li),SKY($nubes));
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
        $ico =$ico. "a";
    } else {
        $ico = $ico. "b";
    }
    return $ico;
}

?>