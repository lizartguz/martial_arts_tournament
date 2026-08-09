<?php

try {
	require_once("pf3connection.php");
	echo "HI Santiago!";
} catch (\Throwable $th) {
	//throw $th;
	echo $th->getMessage();
}
    
    
?>