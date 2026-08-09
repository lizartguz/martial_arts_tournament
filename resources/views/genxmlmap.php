<?php
	require_once("config/config.php");
	require_once("classes/funciones.php");
	require_once("classes/Estacion.php");
	require_once("classes/Login.php");
	$login = new Login();
	
    $type_device = 'estacion';
    if (isset($_GET["type_device"])) { $type_device = $_GET["type_device"]; }

	$estacion = new Estacion();
	$rows = $estacion->getEstaciones($type_device);

	header("Content-type: text/xml; charset=utf-8");
	echo '<?xml version = "1.0" encoding = "UTF-8" standalone = "no" ?>';
	echo '<dispositivos>';
	foreach($rows as $row)
	{
		// ADD TO XML DOCUMENT NODE
		echo '<dispositivo ';
		echo 'id="' . parseToXML($row['ID']) . '" ';
    	echo 'ns="' . parseToXML($row['SN']) . '" ';
		echo 'name="' . parseToXML($row['NAME']) . '" ';
		echo 'address="' . parseToXML($row['ADDRESS']) . '" ';
		echo 'lat="' . $row['LAT'] . '" ';
		echo 'lon="' . $row['LON'] . '" ';
		echo 'type="'.$estacion->isOwner($login->User_Id(), $row['ID']).'" ';
		echo 'type_device="'.$row['TYPE'].'" ';
    	$info = $estacion->getUltimoDato($row['ID']);
        $fecha = $info['FECHA'];
//        $fecha = $row['FECHA'];
//        echo 'state="0" ';
        if ($fecha=="")
        {
    	    echo 'state="0" ';  //no esta enviando
        }
        else
        {
            $date1 = new DateTime('NOW', new DateTimeZone('America/La_Paz'));
            $date2 = new DateTime($info['FECHA'], new DateTimeZone('America/La_Paz'));
            $intvl = $date2->diff($date1);
            $diferencia_horas=$intvl->h + ($intvl->days*24);
            if ($diferencia_horas<4)
            {
                echo 'state="1" '; //dif de 4 horas
            }
            else
            {
                if ($diferencia_horas<12)
                {
                    echo 'state="2" '; //dif de hasta 24 hrs
                }
                else
                {
                    if ($diferencia_horas>168)
                    {
                        echo 'state="4" ';  //mayor a 36 hrs
                    }
                    else
                    {
                        echo 'state="3" ';//dif hasta 36 hrs.
                    }
                }   
            }
        }
		echo '/>';
	}
	echo '</dispositivos>';
?>
