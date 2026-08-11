<? session_start() ?> 
<?php
	require_once("classes/Login.php");
	require_once("classes/Estacion.php");
	$login = new Login();
	$estacion = new Estacion();
	$id = $_GET["id"];
    $dispositivo = $estacion->getEstacion($id);
	$info = $estacion->getUltimoDato($dispositivo['ID']);
		$viento = '';
		if(($info['WIND_DIR']!=-9999)){
		if (($info['WIND_DIR']>=337.5) || ($info['WIND_DIR']<22.5))
		{
			$viento = 'N ' ;
		};
		if (($info['WIND_DIR']>=22.5) && ($info['WIND_DIR']<67.5))
		{
			$viento = 'NE ' ;
		};
		if (($info['WIND_DIR']>=67.5) && ($info['WIND_DIR']<112.5))
		{
			$viento = 'E ' ;
		};
		if (($info['WIND_DIR']>=112.5) && ($info['WIND_DIR']<157.5))
		{
			$viento = 'SE ';
		};
		if (($info['WIND_DIR']>=157.5) && ($info['WIND_DIR']<202.5))
		{
			$viento = 'S ' ;
		};
		if (($info['WIND_DIR']>=202.5) && ($info['WIND_DIR']<247.5))
		{
			$viento = 'SO ';
		};
		if (($info['WIND_DIR']>=247.5) && ($info['WIND_DIR']<292.5))
		{
			$viento = 'O ' ;
		};
		if (($info['WIND_DIR']>=292.5) && ($info['WIND_DIR']<337.5))
		{
			$viento = 'NO ';
		};
		}
		else{ $viento = 'NA'; }
?>
<html lang="es">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="content_security_policy": "default-src 'none'; style-src 'self'; script-src 'self'; connect-src https://maps.googleapis.com; img-src https://maps.google.com">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Primera y única plataforma meteorológica boliviana integrada a sistemas científicos mundiales, actualizados y padronizados para ajustar la información a la necesidad del usuario en nuestro país y en mejora continua."/
    <meta name="keywords" content="artguz, clima, santa cruz, bolivia, estaciones meteorológicas, estaciones, meteorológicas, meteorologicas, temperatura, humedad, humedad relativa, velocidad de aire, presión, presion, luz, intensidad luminosa, punto de rocío, punto de rocio, software, rs-232, logger de datos, información, informacion, técnica, tecnica, servicio"/>
    <meta name="author" content="Profel" />
    <meta name="copyright" content="Profel" />
    <link rel="icon" href="{{ asset('frontend/images/logo_with_text.png') }}" type="image/png" />
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
    <link href="./frontend/css/frontend.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
	<div class="panel panel-primary">
		<div class="panel-heading clearfix">
			<h4 class="panel-title pull-left">
				<?php echo htmlentities(utf8_encode($dispositivo['NAME']));?></a><br><?php echo htmlentities(utf8_encode($dispositivo['ADDRESS']));?>
				<small><br><?php echo "LAT: ".round($dispositivo['LAT'],6,PHP_ROUND_HALF_ODD);?> / <?php echo "LON: ".round($dispositivo['LON'],6,PHP_ROUND_HALF_ODD);?></small>
			</h4>
			<div class="col-md-1">
			</div>
			<div class="btn-group pull-right">
				<a class="btn btn-default btn-sm" href="solicitademo.php" >Mas Datos</a>
			</div>
		</div>
				<table class="panel-body table table-hover table-condensed" style="font-size:14px;">
					<thead>
						<tr>
							<td colspan="2"><small><strong>Actualizado el: <?php echo $info['FECHA'];?></strong></small></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><small>Temperatura</small></td>
							<td>
								<small>
								<?php 
									if( ($info['TEMP_OUT']==-9999)){
										echo $info['TEMP_OUT']= "NA";
									}
									else{
										echo  round( $info['TEMP_OUT']);
									}
								?> ºC
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Humedad</small></td>
							<td> 
								<small>
								<?php 
									if( ($info['HUM_OUT']==-9999)){
										echo $info['HUM_OUT']= "NA";
									}
									else{
										echo  round( $info['HUM_OUT']);
									}
								?> %
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Punto de Rocio</small></td>
							<td> 
								<small>
								<?php 
									if( ($info['DEW_PT_OUT']==-9999)){
										echo $info['DEW_PT_OUT']= "NA";
									}
									else{
										echo  round( $info['DEW_PT_OUT']);
									}
								?> ºC
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Dirección del Viento</small></td>
							<td> 
								<small>
								<?php echo round( $info['WIND_DIR'])."° ".$viento ; ?>
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Velocidad del Viento</small></td>
							<td> 
								<small>
								<?php 
									if( ($info['WIND_SPEED']==-9999)){
										echo $info['WIND_SPEED']= "NA";
									}
									else{
    				if( ($dispositivo['ID']==20)){
						echo  round( $info['WIND_SPEED']/1.852)." Kt";
					}
					else {
						echo  round( $info['WIND_SPEED'])." km/h";
					}
									}
								?>
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Ráfaga </small></td>
							<td> 
								<small>
								<?php 
									if( ($info['WIND_SPEED_HI']==-9999)){
										echo $info['WIND_SPEED_HI']= "NA";
									}
									else{
    				if( ($dispositivo['ID']==20)){
						echo  round( $info['WIND_SPEED_HI']/1.852)." Kt";
					}
					else {
						echo  round( $info['WIND_SPEED_HI'])." km/h";
					}
									}
								?>
								</small>
							</td>
						</tr>
						<tr>
							<td><small>Precipitación Acum. del Día</small></td>
							<td>
								<small>
								<?php 
									if( ($info['RAIN_TOTAL']==-9999)){
										echo $info['RAIN_TOTAL']= "NA";
									}
									else{
										echo  round( $info['RAIN_TOTAL'],2);
									}
								?> mm
								</small>
							</td>
						</tr>
						<tr>
    		<?php 
				if( ($dispositivo['ID']==20)){
					echo "<td><small>QNH</small></td>";
					echo "<td>";
					echo "<small> ";
						if( ($info['PRESS']==-9999)){
							echo $info['PRESS']= "NA";
						}
						else{
					$z = 3837;
					$_R = 5.2553026;
					$_const3 = 288;
					$_const2 = 0.0065;
					$_N = 0.190284;
					$_const = 0.3;
					$_setA = $info['PRESS'] - $_const;
					$_setB = 1013.25 / $_setA;
					$_b1 = pow($_setB, $_N);
					$_setC = $_b1 * $_const2 * $z / $_const3;
					$_setD = 1 + $_setC;
					$_b2 = pow($_setD, $_R);
					$Q = $_setA * $_b2;
					$A = $Q / 33.8639;
							echo  "Q".round($Q,0)."mb   A".round($A,2)."inHG";
						}
					echo "</small> ";
					echo "</td>";
				} else {
					echo "<td><small>Presión Barométrica</small></td>";
					echo "<td> ";
					echo "<small> ";
					if( ($info['PRESS']==-9999)){
						echo $info['PRESS']= "NA";
					}
					else{
						echo  round( $info['PRESS'])." mb";
					}
					echo "</small> ";
					echo "</td>";
				} 
			?>
						</tr>
					</tbody>
				</table>
    		<div class="btn-group pull-right">
        		<a class="btn btn-primary btn-sm" href="solicitademo.php" >Pronosticos</a>
                <div class="col-sm-1 col-xs-1 col-md-1 col-lg-1"></div>
    			<a class="btn btn-primary btn-sm" href="solicitademo.php" >Alertas</a>
			</div>
	</div>
</div>
<div class="barragris">
		<div class="container">
			<div class="row text-center">
				<p><a class="textobarragris" href="quienessomos.php">QUIÉNES SOMOS</a>
				| <a class="textobarragris" href="servicios.php">SERVICIOS</a>
				| <!--a class="textobarragris" href="http://blog.artguz.com">BLOG</a-->
				</p>
			</div>
		</div>
	</div>
	<div class="barrafooter">
		<div class="container">
			<div class="row">
				<br>
			</div>
			<div class="row">
				<div class="col-md-4">
					<a href="http://amigo.artguz.net" target="_blank" title="amigoPROFEL">
						<img class="center-block" src="./frontend/images/amigoartguz.png" alt="" width="75%">
					</a>
				</div>
				<div class="col-md-4">
						<div class="row center-block">
							<div class="col-xs-7">
								<div class="row footert1">
									<div class="col-md-2">
										<img class="align-top" src="./frontend/images/icono-01.png" alt="" width="25" height="22">
									</div>
									<div class="col-md-10">
										<!--p><b>PROFEL ESTE</b><br>
										4680 Virgen de Cotoca, 5°</p-->
										<p><b>PROFEL NORTE</b><br>
										6680 Cristo Redentor, 7°</p>
									</div>
								</div>
							</div>
							<div class="col-xs-5">
									<div class="row footert1">
										<div class="col-md-12">
											<p><img src="./frontend/images/icono-02.png" alt="" width="22" height="24"> 3 348 0707</p>
										</div>
									</div>
									<div class="row footert1">
										<div class="col-md-12" >
											<p><img src="./frontend/images/icono-03.png" alt="" width="21" height="23"> 7109 4415<br>
											<img src="./frontend/images/icono-03.png" alt="" width="21" height="23"> 7109 4432</p>
										</div>
									</div>
							</div>
						</div>
				</div>
				<div class="col-md-4">
						<div class="row footert1">
							<div class="col-xs-3">
								<p class="footert2"><b>SIGUENOS:</b></p>
							</div>
							<div class="col-xs-9">
							<ul class="list-unstyled list-inline social">
										<li class="list-inline-item"><a target="_blank" href="https://twitter.com/ProfelBolivia" title="twitter"><img src="./frontend/images/twitter.png" alt="" width="30" height="30"></a></li>
										<li class="list-inline-item"><a target="_blank" href="https://www.facebook.com/artguz.bolivia" title="facebook"><img src="./frontend/images/facebook.png" alt="" width="30" height="30"></a></li>
										<li class="list-inline-item"><a target="_blank" href="https://www.youtube.com/channel/UCMiGdmZGPqfnjgnXeVbQFKQ" title="youtube"><img src="./frontend/images/youtube.png" alt="" width="30" height="30"></a></li>
										<li class="list-inline-item"><a target="_blank" href="https://www.linkedin.com/in/artguz-bolivia-14200862" title="linkedin"><img src="./frontend/images/linkedin.png" alt="" width="30" height="30"></a></li>
									</ul>
							

							</div>
						</div>
				</div>
			</div>
		</div>
	</div>
	
<div class="btn-conoce">
	<a href="conoce.php">
		<img src="./frontend/images/conoce.jpg" alt="" width="120px">
	</a>
</div>

<div id="whatsapp" class="tiembla" title="Comunícate con un consultor">
    <a href="https://bit.ly/Senvatec" target="_blank">
	<div style="background-color: #25D366; padding: 14px; border-radius: 50%; box-shadow: 0px 0px 11px rgba(0,0,0,.5);">
		<svg style="pointer-events:none; display:block; height:40px; width:40px;" width="40px" height="40px" viewBox="0 0 1219.547 1225.016">
			<path fill="#E0E0E0" d="M1041.858 178.02C927.206 63.289 774.753.07 612.325 0 277.617 0 5.232 272.298 5.098 606.991c-.039 106.986 27.915 211.42 81.048 303.476L0 1225.016l321.898-84.406c88.689 48.368 188.547 73.855 290.166 73.896h.258.003c334.654 0 607.08-272.346 607.222-607.023.056-162.208-63.052-314.724-177.689-429.463zm-429.533 933.963h-.197c-90.578-.048-179.402-24.366-256.878-70.339l-18.438-10.93-191.021 50.083 51-186.176-12.013-19.087c-50.525-80.336-77.198-173.175-77.16-268.504.111-278.186 226.507-504.503 504.898-504.503 134.812.056 261.519 52.604 356.814 147.965 95.289 95.36 147.728 222.128 147.688 356.948-.118 278.195-226.522 504.543-504.693 504.543z"></path>
			<linearGradient id="htwaicona-chat" gradientUnits="userSpaceOnUse" x1="609.77" y1="1190.114" x2="609.77" y2="21.084">
				<stop id="s3_1_offset_1" offset="0" stop-color="#25D366"></stop>
				<stop id="s3_1_offset_2" offset="1" stop-color="#25D366"></stop>
			</linearGradient>
			<path fill="url(#htwaicona-chat)" d="M27.875 1190.114l82.211-300.18c-50.719-87.852-77.391-187.523-77.359-289.602.133-319.398 260.078-579.25 579.469-579.25 155.016.07 300.508 60.398 409.898 169.891 109.414 109.492 169.633 255.031 169.57 409.812-.133 319.406-260.094 579.281-579.445 579.281-.023 0 .016 0 0 0h-.258c-96.977-.031-192.266-24.375-276.898-70.5l-307.188 80.548z"></path>
			<image overflow="visible" opacity=".08" width="682" height="639" transform="translate(270.984 291.372)"></image>
			<path fill-rule="evenodd" clip-rule="evenodd" fill="#FFF" d="M462.273 349.294c-11.234-24.977-23.062-25.477-33.75-25.914-8.742-.375-18.75-.352-28.742-.352-10 0-26.25 3.758-39.992 18.766-13.75 15.008-52.5 51.289-52.5 125.078 0 73.797 53.75 145.102 61.242 155.117 7.5 10 103.758 166.266 256.203 226.383 126.695 49.961 152.477 40.023 179.977 37.523s88.734-36.273 101.234-71.297c12.5-35.016 12.5-65.031 8.75-71.305-3.75-6.25-13.75-10-28.75-17.5s-88.734-43.789-102.484-48.789-23.75-7.5-33.75 7.516c-10 15-38.727 48.773-47.477 58.773-8.75 10.023-17.5 11.273-32.5 3.773-15-7.523-63.305-23.344-120.609-74.438-44.586-39.75-74.688-88.844-83.438-103.859-8.75-15-.938-23.125 6.586-30.602 6.734-6.719 15-17.508 22.5-26.266 7.484-8.758 9.984-15.008 14.984-25.008 5-10.016 2.5-18.773-1.25-26.273s-32.898-81.67-46.234-111.326z"></path>
			<path fill="#FFF" d="M1036.898 176.091C923.562 62.677 772.859.185 612.297.114 281.43.114 12.172 269.286 12.039 600.137 12 705.896 39.633 809.13 92.156 900.13L7 1211.067l318.203-83.438c87.672 47.812 186.383 73.008 286.836 73.047h.255.003c330.812 0 600.109-269.219 600.25-600.055.055-160.343-62.328-311.108-175.649-424.53zm-424.601 923.242h-.195c-89.539-.047-177.344-24.086-253.93-69.531l-18.227-10.805-188.828 49.508 50.414-184.039-11.875-18.867c-49.945-79.414-76.312-171.188-76.273-265.422.109-274.992 223.906-498.711 499.102-498.711 133.266.055 258.516 52 352.719 146.266 94.195 94.266 146.031 219.578 145.992 352.852-.118 274.999-223.923 498.749-498.899 498.749z"></path>
		</svg>
	</div>
	</a>
</div>
</body>
</html>
