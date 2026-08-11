<?php
// include the configs / constants for the database connection
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__."/config/config.php");

// load the login class
require_once(__ROOT__."/classes/Login.php");
$login = new Login();

?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="content_security_policy": "default-src 'none'; style-src 'self'; script-src 'self'; connect-src https://maps.googleapis.com; img-src https://maps.google.com">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Primera y única plataforma meteorológica boliviana integrada a sistemas científicos mundiales, actualizados y padronizados para ajustar la información a la necesidad del usuario en nuestro país y en mejora continua."/
    <meta name="keywords" content="artguz, clima, santa cruz, bolivia, estaciones meteorológicas, estaciones, meteorológicas, meteorologicas, temperatura, humedad, humedad relativa, velocidad de aire, presión, presion, luz, intensidad luminosa, punto de rocío, punto de rocio, software, rs-232, logger de datos, información, informacion, técnica, tecnica, servicio"/>
    <meta name="author" content="Profel" />
    <meta name="copyright" content="Profel" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png" />

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <link href="./frontend/css/frontend.css" rel="stylesheet">
	<link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css">
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
<!-- Google Tag Manager -->
/**
 * Inicializa este comportamiento inmediato del navegador.
 */
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NVNJZTM');</script>
<!-- End Google Tag Manager -->
