<?php
        $DB_HOST_STANDARD = "208.109.232.246";
        $DB_NAME_STANDARD = "artguzdb";
        $DB_USER_STANDARD = "root";
        $DB_PASS_STANDARD = "adminartguz";

        $DB_HOST_CURRENT_YEAR = "208.109.233.146";
        $DB_NAME_CURRENT_YEAR = "artguzmet2024";
        $DB_USER_CURRENT_YEAR = "root";
        $DB_PASS_CURRENT_YEAR = "dBpr0felclim4";

        try{
                /*$CONN_STANDARD = new PDO("mysql:host=".$DB_HOST_STANDARD.";dbname=".$DB_NAME_STANDARD,$DB_USER_STANDARD,$DB_PASS_STANDARD);
                $CONN_STANDARD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $CONN_CURRENT_YEAR = new PDO("mysql:host=".$DB_HOST_CURRENT_YEAR.";dbname=".$DB_NAME_CURRENT_YEAR,$DB_USER_CURRENT_YEAR,$DB_PASS_CURRENT_YEAR);
                $CONN_CURRENT_YEAR->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                */

        }catch(PDOException $e){
                echo $e->getMessage();
        }
?>
