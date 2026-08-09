
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NVNJZTM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Static navbar -->
<div class="navbar-wrapper">
	<div class="container">

		<nav class="navbar navbar-fixed-top my-navbar">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Menu</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="principal.php">
						<img src="frontend/images/logo.png" alt="">
					</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="quienessomos.php" class="dropdown-toggle" aria-expanded="false">QUIENES SOMOS</a>
						</li>
					</ul>
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">SERVICIOS<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="agro.php">Agro</a></li>
								<li><a href="civil.php">Civil</a></li>
								<li><a href="consultoria.php">Consultoría</a></li>
								<li><a href="academia.php">Academia</a></li>
							</ul>
						</li>
					</ul>
					<!--ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="http://blog.artguz.net" target="_blank" class="dropdown-toggle" aria-expanded="false">BLOG</a>
						</li>
					</ul-->
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="soporte.php" class="text-uppercase">
								<div class="btn botonmenu">SOPORTE</div>
							</a>
						</li>
						<?php if ($login->isUserLoggedIn() == false) {?>
						<li class="dropdown">
							<a class="dropdown-toggle" href="#" data-toggle="dropdown">
								<div class="btn botonmenu">
									INGRESAR
								</div>
							</a>
							<div class="dropdown-menu" style="padding: 15px;">						
								<div class="form">
									<form class="login-form" method="post" action="index.php" accept-charset="UTF-8">
										<input type="text" id="inputEmail" class="" placeholder="Usuario" name="user_name" required autofocus>
										<input type="password" id="inputPassword" class="" placeholder="Contrase&ntilde;a" name="user_password" required>
										<button class="" type="submit" name="login">Ingresar</button>
									</form>
								</div>
							</div>
						</li>
						<?php } else { ?>
						<li class="dropdown">
							<a href="map.php" class="text-uppercase">
								<div class="btn botonmenu">INGRESAR</div>
							</a>
						</li>
						<?php } ?>
						<li class="dropdown">
							<a href="http://www.artguz.net" target="_blank" class="dropdown-toggle" aria-expanded="false"><img src="frontend/images/logoartguz.png" alt=""></a>
						</li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>
	</div>
</div>
