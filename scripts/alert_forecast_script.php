<?php
    date_default_timezone_set('America/La_Paz');    
    require_once("pf3connection.php");
     
    
    try {        
        $DB_STANDARD = new mysqli($DB_HOST_STANDARD, $DB_USER_STANDARD, $DB_PASS_STANDARD, $DB_NAME_STANDARD);
        //$DB_CURRENT_YEAR = new mysqli($DB_HOST_CURRENT_YEAR, $DB_USER_CURRENT_YEAR, $DB_PASS_CURRENT_YEAR, $DB_NAME_CURRENT_YEAR);
        
        if ($DB_STANDARD->connect_error) {
            echo "Error de conexión DB estándar: " . $DB_STANDARD->connect_error;
            exit;
        }    
        if ($DB_CURRENT_YEAR->connect_error) {
            echo "Error de conexión DB año actual: " . $DB_CURRENT_YEAR->connect_error;
            exit;
        }
        $sql = "SELECT aa.station_id, aa.user_id, aa.temp_max, aa.temp_min, aa.wind_max, aa.rain_total, aa.uv_max, 
                    aa.solar_rad_max, aa.solar_rad_max,
                    CASE 
                        WHEN aa.wind_max = f.v10m THEN 'wind_max'
                        ELSE NULL
                    END AS match_wind,
                    CASE 
                        WHEN aa.temp_max = f.t2m THEN 'temp_max'
                        ELSE NULL
                    END AS match_temp_max,
                    CASE 
                        WHEN aa.temp_min = f.t2m THEN 'temp_min'
                        ELSE NULL
                    END AS match_temp_min
                FROM alerts_alarms AS aa
                INNER JOIN forecasts AS f ON aa.station_id = f.station_id
                WHERE aa.wind_max = f.v10m
                OR aa.temp_max = f.t2m
                OR aa.temp_min = f.t2m;";
            

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