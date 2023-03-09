<html>
<head> 
	<meta charset="utf-8">
	<title>WRKGRP</title>
	<link rel="shortcut icon" href="resource/img/icon/WRKICO.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap core CSS -->
	<link href="resource/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="resource/css/app.css" rel="stylesheet">
	<link rel="stylesheet" href="resource/css/fonts/all.min.css">
</head> 
<body style="background: #B0B3D6;">
	<nav class="navbar navbar-expand-lg sticky-top navbar-light shadow p-3 bg-dark">
		<div class="container">
			<a> <img class="rounded" src="resource/img/WRK2.png" alt=""></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false">
				<i class="fas fa-bars"></i>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<!-- Left Side Of Navbar -->
				<ul class="navbar-nav mr-auto"></ul>
				<br>
				<ul class="navbar-nav ml-auto">
				</ul>
				<!-- Right Side Of Navbar -->
			</div>
		</div>
	</nav>
	<br>
	<div class="container-fluid">
		<center><h1><strong><i>WRKPHP</i></strong></h1><h1><strong>C</strong>REATE - <strong>R</strong>EAD - <strong>U</strong>PDATE - <strong>D</strong>ELETE</h1>
		</center>	
		<div class="card">
			<div class="card-body">
				<h5 class="card-title font-weight-bold">INSTRUCCIONES</h5>
				<p class="card-text">Ir al archivo <strong>valconfig.php</strong> ubicado en la raíz del proyecto y modificar las variables <strong>$DB</strong> (Base de datos a la que desea conectar) y <strong>$TABLA</strong> (Tabla de datos de la cual requiere generar un CRUD), luego de modificar guarde los cambios y proceda a generar su CRUD haciendo click en el botón <a class="btn btn-primary text-white">Generar</a> en cualquiera de las opciones.</p>
				<hr>
				<p class="card-text">Cada CRUD es generado en un folder dentro del proyecto con sus respectivos scripts y recursos, si desea migrar su CRUD a otra aplicación o proyecto solo debe ubicar el folder, los mismos son nombrados de la siguiente manera <i>"Nombre de la base de datos/Nombre de la tabla"</i>.</p>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title font-weight-bold">CRUD WITH AJAX/JQUERY</h5>
						<p class="card-text">Generación de CRUD básico con frontend y backend en PHP utilizando JQUERY/AJAX.</p>
						<a href="WRKGRPJS.php" class="btn btn-primary">Generar</a>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title font-weight-bold">CRUD WITHOUT AJAX/JQUERY</h5>
						<p class="card-text">Generación de CRUD básico con frontend y backend en PHP sin utilizar JQUERY/AJAX.</p>
						<a href="WRKGRP.php" class="btn btn-primary">Generar</a>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<center><h2>CRUDS GENERADOS</h2></center>
		<div class="row">
			<?php 
			$CRUDS = scandir("./"); 
			foreach ($CRUDS as $key => $value) { 
				if (is_dir($value) && $value != "." && $value != ".." && $value != "resource" && $value != ".git") { ?>
					<div class="col-sm-3">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title"><?=$value ?></h5>
								<a href="<?php $dir = scandir($value.'//'); echo $value."/".$dir[2]; ?>" class="btn btn-primary">Ir al CRUD</a>
								<a href="<?php $dir = scandir($value.'//'); echo "bin.php?idcrud=".$value; ?>" class="btn btn-danger">Eliminar CRUD</a>
							</div>
						</div>
						<hr>
					</div>
				<?php } } ?>
			</div>
			<footer>
				<center>
					<strong>Copyright © WRKPHP</strong>
					Todos los derechos reservados. <b>(Versión</b> <b>1.0)</b>
				</center>
			</footer>
		</body>
		</html>