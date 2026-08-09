<?php
    date_default_timezone_set('America/La_Paz');    
    require_once("pf3connection.php");
    if (isset($_GET["a"])) {
        $anio = $_GET["a"];
    } else {
        $anio = date("Y");
    }
    if (isset($_GET["m"])) {
        $mes = $_GET["m"];
    } else {
        $mes = date("n");
    }
    if (isset($_GET["d"])) {
        $dia = $_GET["d"];
    } else {
        $dia = date("j");
    }

    $dateFilter = $anio.'-'.str_pad($mes, 2, '0', STR_PAD_LEFT).'-'.str_pad($dia, 2, '0', STR_PAD_LEFT);
    
    try {        
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
        $sql = "INSERT INTO daily_data (
            station_id, f_year, f_month, f_day, registration_date, tempout, tempoutmax, tempoutmin, dewptout, 
            humout, humoutmin, humoutmax, winddir, winddirstr, windspeed, windspeedhi, raintotal, press, heatindexout, 
            uvindex, solrad, solevo, soiltemp1, soilhum1, leaftemp1, leafhum1, total_leafhum1, total_tempout, 
            total_humout, total_rainrate, soiltemp2, soilhum2, leaftemp2, leafhum2, total_leafhum2, soiltemp3, 
            soilhum3, leaftemp3, leafhum3, total_leafhum3, soiltemp4, soilhum4, leaftemp4, leafhum4, total_leafhum4, 
            windspeed2, winddir2, windspeedhi2, winddirstr2, pm1, pm10, pm2_5
        ) 
        SELECT 
            station_id, 
            YEAR(receipt_date) as f_year,
            MONTH(receipt_date) as f_month,
            DAYOFMONTH(receipt_date) as f_day,
            receipt_date,
            AVG(tempout), 
            MAX(tempoutmax), 
            MIN(tempoutmin),
            AVG(dewptout),
            AVG(humout), 
            MIN(humoutmin), 
            MAX(humoutmax),
            AVG(winddir),
            NULL, 
            AVG(windspeed),
            MAX(winddirhi), 
            MAX(raintotal),
            AVG(press),
            AVG(heatindexout), 
            AVG(uvindex),
            AVG(solrad),
            MAX(solevo),
            AVG(soiltemp1), 
            AVG(soilhum1), 
            AVG(leaftemp1),
            AVG(leafhum1),
            COUNT(IFNULL(leafhum1, NULL)),
            COUNT(IFNULL(tempout, NULL)),
            COUNT(IFNULL(humout, NULL)),
            COUNT(IFNULL(rainrate, NULL)),
            AVG(soiltemp2), 
            AVG(soilhum2),
            AVG(leaftemp2),
            AVG(leafhum2),
            COUNT(IFNULL(leafhum2, NULL)),
            AVG(soiltemp3),
            AVG(soilhum3),
            AVG(leaftemp3),
            AVG(leafhum3),
            COUNT(IFNULL(leafhum3, NULL)),
            AVG(soiltemp4),
            AVG(soilhum4),
            AVG(leaftemp4),
            AVG(leafhum4),
            COUNT(IFNULL(leafhum4, NULL)),
            AVG(windspeed2),
            AVG(winddir2),
            AVG(windspeedhi2),
            NULL,
            AVG(pm1),
            AVG(pm10),
            AVG(pm2_5)
        FROM data
        WHERE receipt_date >= ? AND receipt_date <= ?
        GROUP BY station_id, receipt_date
        ON DUPLICATE KEY UPDATE 
            tempout = VALUES(tempout),
            tempoutmax = VALUES(tempoutmax),
            tempoutmin = VALUES(tempoutmin),
            dewptout = VALUES(dewptout),
            humout = VALUES(humout),
            humoutmin = VALUES(humoutmin),
            humoutmax = VALUES(humoutmax),
            winddir = VALUES(winddir),
            windspeed = VALUES(windspeed),
            windspeedhi = VALUES(windspeedhi),
            raintotal = VALUES(raintotal),
            press = VALUES(press),
            heatindexout = VALUES(heatindexout),
            uvindex = VALUES(uvindex),
            solrad = VALUES(solrad),
            solevo = VALUES(solevo),
            soiltemp1 = VALUES(soiltemp1),
            soilhum1 = VALUES(soilhum1),
            leaftemp1 = VALUES(leaftemp1),
            leafhum1 = VALUES(leafhum1),
            total_leafhum1 = VALUES(total_leafhum1),
            total_tempout = VALUES(total_tempout),
            total_humout = VALUES(total_humout),
            total_rainrate = VALUES(total_rainrate),
            soiltemp2 = VALUES(soiltemp2),
            soilhum2 = VALUES(soilhum2),
            leaftemp2 = VALUES(leaftemp2),
            leafhum2 = VALUES(leafhum2),
            total_leafhum2 = VALUES(total_leafhum2),
            soiltemp3 = VALUES(soiltemp3),
            soilhum3 = VALUES(soilhum3),
            leaftemp3 = VALUES(leaftemp3),
            leafhum3 = VALUES(leafhum3),
            total_leafhum3 = VALUES(total_leafhum3),
            soiltemp4 = VALUES(soiltemp4),
            soilhum4 = VALUES(soilhum4),
            leaftemp4 = VALUES(leaftemp4),
            leafhum4 = VALUES(leafhum4),
            total_leafhum4 = VALUES(total_leafhum4),
            windspeed2 = VALUES(windspeed2),
            winddir2 = VALUES(winddir2),
            windspeedhi2 = VALUES(windspeedhi2),
            pm1 = VALUES(pm1),
            pm10 = VALUES(pm10),
            pm2_5 = VALUES(pm2_5)";
            

            $startDate = $dateFilter . ' 00:00:00';
            $endDate = $dateFilter . ' 23:59:59';

            $stmt = $DB_STANDARD->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ss", $startDate, $endDate);

                $DB_STANDARD->begin_transaction();
                try {
                    if ($stmt->execute()) {           
                        $DB_STANDARD->commit();
                        echo "Registro insertado o actualizado exitosamente.";
                    } else {            
                        $DB_STANDARD->rollback();
                        echo "Error en la consulta: " . $DB_STANDARD->error;
                    }
                } catch (Exception $e) {        
                    $DB_STANDARD->rollback();
                    echo "Excepción capturada: " . $e->getMessage();
                }
                $stmt->close();
            }        
        
        

        $startDate = $dateFilter . ' 00:00:00';
        $endDate = $dateFilter . ' 23:59:59';

        $stmt = $DB_CURRENT_YEAR->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $startDate, $endDate);

            $DB_CURRENT_YEAR->begin_transaction();
            try {
                if ($stmt->execute()) {           
                    $DB_CURRENT_YEAR->commit();
                    echo "Registro insertado o actualizado exitosamente.";
                } else {            
                    $DB_CURRENT_YEAR->rollback();
                    echo "Error en la consulta: " . $DB_CURRENT_YEAR->error;
                }
            } catch (Exception $e) {        
                $DB_CURRENT_YEAR->rollback();
                echo "Excepción capturada: " . $e->getMessage();
            }
            $stmt->close();
        }

    } catch (Exception $e) {        
       
        echo "Fallo conexión u otros: " . $e->getMessage();
    }

?>