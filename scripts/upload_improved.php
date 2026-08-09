<?php
 require_once("pf3connection.php");    


    /**************************** NEW DATABASE ***************************/        
    $sn             = isset($_GET['sn']) ? $_GET['sn'] : null;   
    $mac            = isset($_GET['mac']) ? $_GET['mac'] : null; 
    $receipt_date = isset($_GET['fecha']) ? date('Y-m-d H:i:s', strtotime($_GET['fecha'])) : date('Y-m-d H:i:s');

    $tempout        = isset($_GET['tempout']) ? $_GET['tempout'] : null;
    $tempin         = isset($_GET['tempin']) ? $_GET['tempin'] : null;
    $tempoutmax     = isset($_GET['maxtempout']) ? $_GET['maxtempout'] : null;
    $tempoutmin     = isset($_GET['mintempout']) ? $_GET['mintempout'] : null;
    
    $humout         = isset($_GET['humout']) ? $_GET['humout'] : null;
    $humin          = isset($_GET['humin']) ? $_GET['humin'] : null;
    $humoutmax      = isset($_GET['humoutmax']) ? $_GET['humoutmax'] : null;
    $humoutmin      = isset($_GET['humoutmin']) ? $_GET['humoutmin'] : null;
    
    $dewptout       = isset($_GET['dewptout']) ? $_GET['dewptout'] : null;
    $heatindexout   = isset($_GET['heatindexout']) ? $_GET['heatindexout'] : null;        
    $dewptin        = isset($_GET['dewptin']) ? $_GET['dewptin'] : null;
    $press          = isset($_GET['press']) ? $_GET['press'] : null;
    $seapress       = isset($_GET['seapress']) ? $_GET['seapress'] : null;    
    
    $windavg        = isset($_GET['windavg']) ? $_GET['windavg'] : null;
    $winddir        = isset($_GET['winddir']) ? $_GET['winddir'] : null;
    $windchill      = isset($_GET['windchill']) ? $_GET['windchill'] : null;
    $windspeed      = isset($_GET['windspeed']) ? $_GET['windspeed'] : null;
    $windspeedhi    = isset($_GET['windspeedhi']) ? $_GET['windspeedhi'] : null;
    $winddirhi      = isset($_GET['winddirhi']) ? $_GET['winddirhi'] : null;
    $windspeed2     = isset($_GET['windspeed2']) ? $_GET['windspeed2'] : null;
    $winddir2       = isset($_GET['winddir2']) ? $_GET['winddir2'] : null;
    $windspeedhi2   = isset($_GET['windspeedhi2']) ? $_GET['windspeedhi2'] : null;
    $winddirhi2     = isset($_GET['winddirhi2']) ? $_GET['winddirhi2'] : null;

    $rainrate       = isset($_GET['rainrate']) ? $_GET['rainrate'] : null;
    $raintotal      = isset($_GET['raintotal']) ? $_GET['raintotal'] : null;
    $uvindex        = isset($_GET['uvindex']) ? $_GET['uvindex'] : null;
    $solrad         = isset($_GET['solrad']) ? $_GET['solrad'] : null;
    $solevo         = isset($_GET['solevo']) ? $_GET['solevo'] : null;
    
    $solradhi = isset($_GET['solradhi']) ? $_GET['solradhi'] : null; 
    $soiltemp1 = isset($_GET['soiltemp1']) ? $_GET['soiltemp1'] : null; 
    $soiltemp2 = isset($_GET['soiltemp2']) ? $_GET['soiltemp2'] : null; 
    $soiltemp3 = isset($_GET['soiltemp3']) ? $_GET['soiltemp3'] : null; 
    $soiltemp4 = isset($_GET['soiltemp4']) ? $_GET['soiltemp4'] : null; 

    $soilhum1 = isset($_GET['soilhum1']) ? $_GET['soilhum1'] : null; 
    $soilhum2 = isset($_GET['soilhum2']) ? $_GET['soilhum2'] : null; 
    $soilhum3 = isset($_GET['soilhum3']) ? $_GET['soilhum3'] : null; 
    $soilhum4 = isset($_GET['soilhum4']) ? $_GET['soilhum4'] : null;

    $leaftemp1 = isset($_GET['leaftemp1']) ? $_GET['leaftemp1'] : null; 
    $leaftemp2 = isset($_GET['leaftemp2']) ? $_GET['leaftemp2'] : null; 
    $leaftemp3 = isset($_GET['leaftemp3']) ? $_GET['leaftemp3'] : null; 
    $leaftemp4 = isset($_GET['leaftemp4']) ? $_GET['leaftemp4'] : null; 

    $leafhum1 = isset($_GET['leafhum1']) ? $_GET['leafhum1'] : null;    
    $leafhum2 = isset($_GET['leafhum2']) ? $_GET['leafhum2'] : null;
    $leafhum3 = isset($_GET['leafhum3']) ? $_GET['leafhum3'] : null;
    $leafhum4 = isset($_GET['leafhum4']) ? $_GET['leafhum4'] : null; 
    $RESULT ='';
    $state=1;
    $winddirstr= null;
    $station_id = null;
  
    //---------------------------------------------------------------------------------//

    date_default_timezone_set('America/La_Paz');
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;
    $registration_date = $created_at;

    $DB_STANDARD = new mysqli($DB_HOST_STANDARD, $DB_USER_STANDARD, $DB_PASS_STANDARD, $DB_NAME_STANDARD);
    $DB_CURRENT_YEAR = new mysqli($DB_HOST_CURRENT_YEAR, $DB_USER_CURRENT_YEAR, $DB_PASS_CURRENT_YEAR, $DB_NAME_CURRENT_YEAR);

    if ($DB_STANDARD->connect_error) {
        echo "Error de conexión DB estándar: " . $DB_STANDARD->connect_error;
        exit;
    }

    if ($DB_CURRENT_YEAR->connect_error) {
        echo "Error de conexión DB año actual: " . $DB_CURRENT_YEAR->connect_error;
        exit;
    }

    if($DB_STANDARD->connect_error==null && $DB_CURRENT_YEAR->connect_error==null){

        $query = "SELECT stations.id, stations.et, ET_CALCULATION(stations.id, '$receipt_date', $tempout, $tempoutmax, $tempoutmin, $humout, $windspeed, $press, $solrad) as V_ET FROM stations WHERE stations.code='$sn'";
        $result = null;   
        $result = $DB_STANDARD->query($query); 

        $queryData ="INSERT INTO data (
                    station_id, receipt_date, registration_date, tempin, tempout, tempoutmin, tempoutmax, 
                    humin, humout, humoutmin, humoutmax, dewptout, dewptin, heatindexout, press, seapress, 
                    windchill, windavg, windspeed, winddir, windspeedhi, winddirhi, winddirstr, windspeed2, 
                    winddir2, windspeedhi2, winddirhi2, rainrate, raintotal, uvindex, solrad, 
                    solradhi, solevo, soiltemp1, soiltemp2, soiltemp3, soiltemp4, soilhum1, soilhum2, soilhum3, 
                    soilhum4, leaftemp1, leaftemp2, leaftemp3, leaftemp4, leafhum1, leafhum2, leafhum3, leafhum4, 
                    pm10, pm2_5, pm1 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    receipt_date = VALUES(receipt_date), 
                    registration_date = VALUES(registration_date), 
                    tempin = VALUES(tempin), 
                    tempout = VALUES(tempout), 
                    tempoutmin = VALUES(tempoutmin), 
                    tempoutmax = VALUES(tempoutmax), 
                    humin = VALUES(humin), 
                    humout = VALUES(humout), 
                    humoutmin = VALUES(humoutmin), 
                    humoutmax = VALUES(humoutmax), 
                    dewptout = VALUES(dewptout),
                    dewptin = VALUES(dewptin),
                    heatindexout = VALUES(heatindexout),
                    press = VALUES(press),
                    seapress = VALUES(seapress),
                    windchill = VALUES(windchill),
                    windavg = VALUES(windavg),
                    windspeed = VALUES(windspeed),
                    winddir = VALUES(winddir),
                    windspeedhi = VALUES(windspeedhi),
                    winddirhi = VALUES(winddirhi),
                    winddirstr = VALUES(winddirstr),
                    windspeed2 = VALUES(windspeed2),
                    winddir2 = VALUES(winddir2),
                    windspeedhi2 = VALUES(windspeedhi2),
                    winddirhi2 = VALUES(winddirhi2),
                    rainrate = VALUES(rainrate),
                    raintotal = VALUES(raintotal),
                    uvindex = VALUES(uvindex),
                    solrad = VALUES(solrad),
                    solradhi = VALUES(solradhi),
                    solevo = VALUES(solevo),
                    soiltemp1 = VALUES(soiltemp1),
                    soiltemp2 = VALUES(soiltemp2),
                    soiltemp3 = VALUES(soiltemp3),
                    soiltemp4 = VALUES(soiltemp4),
                    soilhum1 = VALUES(soilhum1),
                    soilhum2 = VALUES(soilhum2),
                    soilhum3 = VALUES(soilhum3),
                    soilhum4 = VALUES(soilhum4),
                    leaftemp1 = VALUES(leaftemp1),
                    leaftemp2 = VALUES(leaftemp2),
                    leaftemp3 = VALUES(leaftemp3),
                    leaftemp4 = VALUES(leaftemp4),
                    leafhum1 = VALUES(leafhum1),
                    leafhum2 = VALUES(leafhum2),
                    leafhum3 = VALUES(leafhum3),
                    leafhum4 = VALUES(leafhum4),
                    pm10 = VALUES(pm10),
                    pm2_5 = VALUES(pm2_5),
                    pm1 = VALUES(pm1)";

        $queryCurrentData ="INSERT INTO current_data (
                    station_id, receipt_date, registration_date, tempin, tempout, tempoutmin, tempoutmax, 
                    humin, humout, humoutmin, humoutmax, dewptout, dewptin, heatindexout, press, seapress, 
                    windchill, windavg, windspeed, winddir, windspeedhi, winddirhi, winddirstr, windspeed2, 
                    winddir2, windspeedhi2, winddirhi2, winddirstr2, rainrate, raintotal, uvindex, solrad, 
                    solradhi, solevo, soiltemp1, soiltemp2, soiltemp3, soiltemp4, soilhum1, soilhum2, soilhum3, 
                    soilhum4, leaftemp1, leaftemp2, leaftemp3, leaftemp4, leafhum1, leafhum2, leafhum3, leafhum4, 
                    pm10, pm2_5, pm1, state, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    receipt_date = VALUES(receipt_date), 
                    registration_date = VALUES(registration_date), 
                    tempin = VALUES(tempin), 
                    tempout = VALUES(tempout), 
                    tempoutmin = VALUES(tempoutmin), 
                    tempoutmax = VALUES(tempoutmax), 
                    humin = VALUES(humin), 
                    humout = VALUES(humout), 
                    humoutmin = VALUES(humoutmin), 
                    humoutmax = VALUES(humoutmax), 
                    dewptout = VALUES(dewptout),
                    dewptin = VALUES(dewptin),
                    heatindexout = VALUES(heatindexout),
                    press = VALUES(press),
                    seapress = VALUES(seapress),
                    windchill = VALUES(windchill),
                    windavg = VALUES(windavg),
                    windspeed = VALUES(windspeed),
                    winddir = VALUES(winddir),
                    windspeedhi = VALUES(windspeedhi),
                    winddirhi = VALUES(winddirhi),
                    winddirstr = VALUES(winddirstr),
                    windspeed2 = VALUES(windspeed2),
                    winddir2 = VALUES(winddir2),
                    windspeedhi2 = VALUES(windspeedhi2),
                    winddirhi2 = VALUES(winddirhi2),
                    winddirstr2 = VALUES(winddirstr2),
                    rainrate = VALUES(rainrate),
                    raintotal = VALUES(raintotal),
                    uvindex = VALUES(uvindex),
                    solrad = VALUES(solrad),
                    solradhi = VALUES(solradhi),
                    solevo = VALUES(solevo),
                    soiltemp1 = VALUES(soiltemp1),
                    soiltemp2 = VALUES(soiltemp2),
                    soiltemp3 = VALUES(soiltemp3),
                    soiltemp4 = VALUES(soiltemp4),
                    soilhum1 = VALUES(soilhum1),
                    soilhum2 = VALUES(soilhum2),
                    soilhum3 = VALUES(soilhum3),
                    soilhum4 = VALUES(soilhum4),
                    leaftemp1 = VALUES(leaftemp1),
                    leaftemp2 = VALUES(leaftemp2),
                    leaftemp3 = VALUES(leaftemp3),
                    leaftemp4 = VALUES(leaftemp4),
                    leafhum1 = VALUES(leafhum1),
                    leafhum2 = VALUES(leafhum2),
                    leafhum3 = VALUES(leafhum3),
                    leafhum4 = VALUES(leafhum4),
                    pm10 = VALUES(pm10),
                    pm2_5 = VALUES(pm2_5),
                    pm1 = VALUES(pm1),
                    state = VALUES(state),
                    created_at = VALUES(created_at),
                    updated_at = VALUES(updated_at)";

        $row = $result->fetch_assoc();
        if (!empty($row['id'])) {        
            $station_id = $row['id'];
            if (isset($row['et']) && $row['et'] == 1) {
                $solevo = $row['V_ET'];
            }
            
            try {
                $stmt = $DB_STANDARD->prepare($queryCurrentData);
                $stmt->bind_param(
                    "issdddddddddddddddddddsddddsdddddddddddddddddddddddddiss",
                    $station_id, $receipt_date, $registration_date, $tempin, $tempout, $tempoutmin, $tempoutmax, 
                    $humin, $humout, $humoutmin, $humoutmax, $dewptout, $dewptin, $heatindexout, $press, 
                    $seapress, $windchill, $windavg, $windspeed, $winddir, $windspeedhi, $winddirhi, 
                    $winddirstr, $windspeed2, $winddir2, $windspeedhi2, $winddirhi2, $winddirstr2, 
                    $rainrate, $raintotal, $uvindex, $solrad, $solradhi, $solevo, $soiltemp1, $soiltemp2, 
                    $soiltemp3, $soiltemp4, $soilhum1, $soilhum2, $soilhum3, $soilhum4, $leaftemp1, 
                    $leaftemp2, $leaftemp3, $leaftemp4, $leafhum1, $leafhum2, $leafhum3, $leafhum4, 
                    $pm10, $pm2_5, $pm1, $state, $created_at, $updated_at);
                
                $stmt->execute();
                $stmt->close();  
                        
            } catch (Exception $e) {        
                 $RESULT = "Failed SQL: " . $e->getMessage();
            }                      
            $DB_STANDARD->close();


        
            try {            
                $stmt = $DB_CURRENT_YEAR->prepare($queryCurrentData);
                $stmt->bind_param(
                    "issdddddddddddddddddddsddddsdddddddddddddddddddddddddiss",
                    $station_id, $receipt_date, $registration_date, $tempin, $tempout, $tempoutmin, $tempoutmax, 
                    $humin, $humout, $humoutmin, $humoutmax, $dewptout, $dewptin, $heatindexout, $press, 
                    $seapress, $windchill, $windavg, $windspeed, $winddir, $windspeedhi, $winddirhi, 
                    $winddirstr, $windspeed2, $winddir2, $windspeedhi2, $winddirhi2, $winddirstr2, 
                    $rainrate, $raintotal, $uvindex, $solrad, $solradhi, $solevo, $soiltemp1, $soiltemp2, 
                    $soiltemp3, $soiltemp4, $soilhum1, $soilhum2, $soilhum3, $soilhum4, $leaftemp1, 
                    $leaftemp2, $leaftemp3, $leaftemp4, $leafhum1, $leafhum2, $leafhum3, $leafhum4, 
                    $pm10, $pm2_5, $pm1, $state, $created_at, $updated_at);
                $stmt->execute();
                $stmt->close();  

                $stmt = $DB_CURRENT_YEAR->prepare($queryData);
                $stmt->bind_param(
                    "issdddddddddddddddddddsddddddddddddddddddddddddddddd",
                    $station_id, $receipt_date, $registration_date, $tempin, $tempout, $tempoutmin, $tempoutmax, 
                    $humin, $humout, $humoutmin, $humoutmax, $dewptout, $dewptin, $heatindexout, $press, 
                    $seapress, $windchill, $windavg, $windspeed, $winddir, $windspeedhi, $winddirhi, 
                    $winddirstr, $windspeed2, $winddir2, $windspeedhi2, $winddirhi2,$rainrate, $raintotal, $uvindex, $solrad, $solradhi, $solevo, $soiltemp1, $soiltemp2,$soiltemp3, $soiltemp4, $soilhum1, $soilhum2, $soilhum3, $soilhum4, $leaftemp1,$leaftemp2, $leaftemp3, $leaftemp4, $leafhum1, $leafhum2, $leafhum3, $leafhum4, 
                    $pm10, $pm2_5, $pm1);
                $stmt->execute();
                $stmt->close();            
            } catch (Exception $e) {
                $RESULT = "Failed SQL: " . $e->getMessage();
            }
            $DB_CURRENT_YEAR->close();           
        
        }
    }

    echo $RESULT;

?>
