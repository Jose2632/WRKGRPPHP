<?php
include_once 'valconfig.php';
error_reporting(0);
$mysqli = new mysqli("localhost", "root", "", "$DB");
if (mysqli_connect_errno()) {
	printf("Fall¨® la conexi¨®n: %s\n", mysqli_connect_error());
	exit();
}

if (!mkdir("$DB/$TABLA", 0700, true)) {
	echo "<script type='text/javascript'>
					alert('Ya existe un CRUD generado con los siguientes parametros (DB: $DB - TABLA: $TABLA)');
					window.location.href='index.php';
					</script>";
	return;
}

mkdir("$DB/$TABLA", 0700);
mkdir("$DB/$TABLA/resources", 0700);
copyfolder("resources", "$DB/$TABLA/resources");

$ruta = "$DB/$TABLA/";

$WRKDBROUND = WRKDBROUND($mysqli, $DB, $TABLA);
$WRKHTMLINS = WRKHTMLINS ($mysqli, $DB, $TABLA);
$WRKPHPINS = WRKPHPINS ($mysqli, $DB, $TABLA);
$WRKHTMLDSP = WRKHTMLDSP ($mysqli, $DB, $TABLA);
$WRKHTMLLST = WRKHTMLLST ($mysqli, $DB, $TABLA);
$WRKPHPUPD = WRKPHPUPD($mysqli, $DB, $TABLA);
$WRKPHPDLT = WRKPHPDLT($mysqli, $DB, $TABLA);

$WRKGRP = [
	$WRKDBROUND,
	$WRKHTMLINS, 
	$WRKPHPINS,
	$WRKHTMLDSP,
	$WRKHTMLLST,
	$WRKPHPUPD, 
	$WRKPHPDLT
];

for ($i=0; $i <= count($WRKGRP)-1; $i++) { 
	switch ($WRKGRP[$i]) {
		case false:
		echo "<script type='text/javascript'>
		alert('Fallos encontrados en la generación, por favor revisar la configuración y vuelva a intentarlo');
		window.location.href='index.php';
		</script>";
		die;
		default:
		echo "<script type='text/javascript'>
		alert('CRUD GENERADO CORRECTAMENTE');
		window.location.href='index.php';
		</script>";
		break;
	}
}

function WRKDBROUND ($mysqli, $DB, $TABLA) {
	$name = "$DB/$TABLA/dbround.php";
	$plantillaphp = '
	<?php
	$mysqli = new mysqli("localhost", "root", "", "'.$DB.'");
	if (mysqli_connect_errno()) {
		printf("Fall¨® la conexi¨®n: %s\n", mysqli_connect_error());
		exit();
	} 
	?>';
	$fp = fopen($name, 'w');
	fwrite($fp,  $plantillaphp);
	fclose($fp);

	$name="$DB/$TABLA/index.php";
	$plantillaphp = '
	<?php
	header("Location: LST'.$TABLA.'.php"); 
	?>';
	$fp = fopen($name, 'w');
	fwrite($fp,  $plantillaphp);
	fclose($fp);
	return true;
}

function WRKHTMLINS ($mysqli, $DB, $TABLA) {
	$name="$DB/$TABLA/INS$TABLA.html";
	$BUFIN = '';
	$BUFINVAR = '';
	$BUFINPARM = '';
	$PARMSR = '';
	$TYPES = '';
	$LABINP = '';
	$conts = 0;
	$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		$value = $row['Field'];
		if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
		}
		else  {
			if ($row['Null'] != 'YES') {
				$conts += 1;
			}
			if (stristr($row['Type'], 'int')) {
				$TYPES = $TYPES.''.'i';
				$LABINP = $LABINP."
				<div class='form-group'>
				<label for='$value'>$value</label>
				<input type='number' class='form-control' name='$value' required>
				</div>\n";
			}
			elseif(stristr($row['Type'], 'date')) {
				$TYPES = $TYPES.''.'s';
				$LABINP = $LABINP."
				<div class='form-group'>
				<label for='$value'>$value</label>
				<input type='date' class='form-control' name='$value' required>
				</div>\n";
			}
			elseif(stristr($row['Type'], 'time')) {
				$TYPES = $TYPES.''.'s';
				$LABINP = $LABINP."
				<div class='form-group'>
				<label for='$value'>$value</label>
				<input type='time' class='form-control' name='$value' required>
				</div>\n";
			}
			else {
				$TYPES = $TYPES.''.'s';
				$LABINP = $LABINP."
				<div class='form-group'>
				<label for='$value'>$value</label>
				<input type='text' class='form-control' name='$value' required>
				</div>\n";
			}	
			$BUFIN = $BUFIN.','.$row['Field'];
			$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
			$PARMSR = $PARMSR.',?';
		}
	}	
	$BUFIN = substr($BUFIN, 1);
	$BUFIN = explode(',', $BUFIN);
	$BUFINVAR = substr($BUFINVAR, 1);
	$TYPES = substr($TYPES, 1);
	$PARMSR = substr($PARMSR, 1);
	$x = "'"; 
	$plantillahtml = '<html>
	<head> 
	<meta charset="utf-8">
	<title>WRKGRP</title>
	<link rel="shortcut icon" href="resources/img/icon/WRKICO.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap core CSS -->
	<link href="resources/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	
	<link rel="stylesheet" href="resources/css/fonts/all.min.css">
	</head> 
	<body>
	<nav class="navbar navbar-expand-lg sticky-top navbar-light shadow p-3 bg-dark">
	<div class="container">
	<a> <img class="rounded" src="resources/img/WRK2.png" alt=""></a>
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
	<div class="container">
	<center> <h2>INSERTAR EN '.strtoupper($TABLA).'</h2> </center>
	<form action="INS'.$TABLA.'.php" method="POST" accept-charset="utf-8">
	'.$LABINP.'
	<button type="submit" name="INSPARMS" value="1" class="btn btn-dark">
	Aceptar
	</button>
	<a href="LST'.$TABLA.'.php" class="btn btn-dark">
	Atras
	</a>
	</form>
	</div>
	<footer>
	<center>
	<strong>Copyright © WRKPHP</strong>
	Todos los derechos reservados. <b>(Versión</b> <b>1.0)</b>
	</center>
	</footer>
	<script src="resources/jquery/jquery-3.5.1.min.js"></script>
	<script src="resources/js/bootstrap.bundle.min.js"></script>
	</body>
	</html>';	
	$fp = fopen($name, 'w');
	fwrite($fp,  $plantillahtml);
	fclose($fp);
	return true;
}


function WRKHTMLLST ($mysqli, $DB, $TABLA) {
	$name="$DB/$TABLA/LST$TABLA.php";
	$BUFIN = '';
	$BUFINVAR = '';
	$BUFINPARM = '';
	$PARMSR = '';
	$TYPES = '';
	$LABINP = '';
	$LABINP2 = '';
	$conts = 0;	
	$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
	$stmt->execute();
	$result = $stmt->get_result();
	$rowini = '<?=$row[';
	$x = "'";
	$rowend = ']?>';
	while ($row = $result->fetch_assoc()) {
		$value = $row['Field'];
		if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
			$primarykey = $value;
			$primarykeyvalue = $value;
		}
		elseif($row['Key'] == 'PRI') {
			$primarykey = $value;
			$primarykeyvalue = $value;
		}
		else  {
			if ($row['Null'] != 'YES') {
				$conts += 1;
			}
			if (stristr($row['Type'], 'int')) {
				$TYPES = $TYPES.''.'i';
				$LABINP = $LABINP."
				<td>$value</td>\n";
				$LABINP2 = $LABINP2."
				<td>$rowini$x$value$x$rowend</td>\n";
			}
			else {
				$TYPES = $TYPES.''.'s';
				$LABINP = $LABINP."
				<td>$value</td>\n";
				$LABINP2 = $LABINP2."
				<td>$rowini$x$value$x$rowend</td>\n";
			}	
			$BUFIN = $BUFIN.','.$row['Field'];
			$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
			$PARMSR = $PARMSR.',?';
		}
	}
	$BUFIN = substr($BUFIN, 1);
	$BUFIN = explode(',', $BUFIN);
	$BUFINVAR = substr($BUFINVAR, 1);
	$TYPES = substr($TYPES, 1);
	$PARMSR = substr($PARMSR, 1);
	$x = "'"; 
	$plantillahtml = '
	<?php 
	include "dbround.php";
	$stmt = $mysqli->prepare("SELECT * FROM '.$TABLA.'");
	$stmt->execute();
	$result = $stmt->get_result();
	?>
	<html>
	<head> 
	<meta charset="utf-8">
	<title>WRKGRP</title>
	<link rel="shortcut icon" href="resources/img/icon/WRKICO.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Bootstrap core CSS -->
	<link href="resources/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	
	<link rel="stylesheet" href="resources/css/fonts/all.min.css">
	</head> 
	<body>
	<nav class="navbar navbar-expand-lg sticky-top navbar-light shadow p-3 bg-dark">
	<div class="container">
	<a> <img class="rounded" src="resources/img/WRK2.png" alt=""></a>
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
	<a href="INS'.$TABLA.'.html" class="btn btn-dark">
	Ingresar
	</a>
	<hr>
	<div class="row justify-content-center">
	<div class="col-md-12">
	<div class="card">
	<center> 
	<h3><strong><div class="card-header">LST '.strtoupper($TABLA).' </div> </strong></h3>
	</center>
	<table class="table-responsive-xl table table-hover">
	<thead>
	<tr>    
	'.$LABINP.'
	<td>Acciones</td>              
	</tr>
	</thead>
	<tbody>
	<?php while($row = mysqli_fetch_assoc($result)) { ?>
		<tr class="clickable-row" data-href="DSP'.$TABLA.'.php?'.$primarykey.'='.$rowini.$x.$primarykeyvalue.$x.$rowend.'">
		'.$LABINP2.'
		<td> <a href="DLT'.$TABLA.'.php?'.$primarykey.'='.$rowini.''.$x.''.$primarykeyvalue.''.$x.''.$rowend.'" class="btn btn-dark" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i> 
		</a></td>
		</tr>
		<?php } ?>
		</tbody>
		</table>
		</div>
		</div>
		</div>
		</div>
		<footer>
		<center>
		<strong>Copyright © WRKPHP</strong>
		Todos los derechos reservados. <b>(Versión</b> <b>1.0)</b>
		</center>
		</footer>
		<script src="resources/jquery/jquery-3.5.1.min.js"></script>
		<script src="resources/js/bootstrap.bundle.min.js"></script>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
				});
				});
				</script>
				
				</body>
				</html>';
				$fp = fopen($name, 'w');
				fwrite($fp,  $plantillahtml);
				fclose($fp);
				return true;
			}

			function WRKHTMLDSP ($mysqli, $DB, $TABLA) {
				$name="$DB/$TABLA/DSP$TABLA.php";
				$BUFIN = '';
				$BUFINVAR = '';
				$BUFINPARM = '';
				$PARMSR = '';
				$TYPES = '';
				$LABINP = '';
				$conts = 0;
				$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
				$stmt->execute();
				$result = $stmt->get_result();
				$rowini = '"<?=$row[';
				$x = "'";
				$rowend = ']?>"';
				while ($row = $result->fetch_assoc()) {
					$value = $row['Field'];
					if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
						$primarykey = $value;
						$primarykeyvalue = $value;
					}
					elseif($row['Key'] == 'PRI') {
						$primarykey = $value;
						$primarykeyvalue = $value;
					}
					else  {
						if ($row['Null'] != 'YES') {
							$conts += 1;
						}
						if (stristr($row['Type'], 'int')) {
							$TYPES = $TYPES.''.'i';
							$LABINP = $LABINP."
							<div class='form-group'>
							<label for='$value'>$value</label>
							<input type='text' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend>
							</div>\n";
						}
						elseif (stristr($row['Type'], 'date')) {
							$TYPES = $TYPES.''.'i';
							$LABINP = $LABINP."
							<div class='form-group'>
							<label for='$value'>$value</label>
							<input type='date' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend>
							</div>\n";
						}
						elseif (stristr($row['Type'], 'time')) {
							$TYPES = $TYPES.''.'i';
							$LABINP = $LABINP."
							<div class='form-group'>
							<label for='$value'>$value</label>
							<input type='time' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend>
							</div>\n";
						}
						else {
							$TYPES = $TYPES.''.'s';
							$LABINP = $LABINP."
							<div class='form-group'>
							<label for='$value'>$value</label>
							<input type='text' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend>
							</div>\n";
						}	
						$BUFIN = $BUFIN.','.$row['Field'];
						$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
						$PARMSR = $PARMSR.',?';
					}
				}

				$BUFIN = substr($BUFIN, 1);
				$BUFIN = explode(',', $BUFIN);
				$BUFINVAR = substr($BUFINVAR, 1);
				$TYPES = substr($TYPES, 1);
				$PARMSR = substr($PARMSR, 1);
				$x = "'"; 
				$plantillahtml = '
				<?php 
				include "dbround.php";
				$stmt = $mysqli->prepare("SELECT * FROM '.$TABLA.' WHERE '.$primarykey.' = ?");
				$stmt->bind_param ('."'".''.'i'.''."'".', $'.$primarykeyvalue.');
				if (isset($_GET['."'".''.$primarykeyvalue.''."'".'])) {
					$'.$primarykey.' = $_GET['."'".''.$primarykeyvalue.''."'".'];
				}
				else {
					header("Location: ./");
				}
				$stmt->execute();
				$result = $stmt->get_result();
				if (mysqli_num_rows($result) == 1) {
					$row = $result->fetch_assoc();
				}
				?>
				<html>
				<head> 
				<meta charset="utf-8">
				<title>WRKGRP</title>
				<link rel="shortcut icon" href="resources/img/icon/WRKICO.ico" />
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<!-- Bootstrap core CSS -->
				<link href="resources/css/bootstrap.min.css" rel="stylesheet">
				<!-- Custom styles for this template -->
				<link rel="stylesheet" href="resources/css/fonts/all.min.css">
				</head> 
				<body>
				<nav class="navbar navbar-expand-lg sticky-top navbar-light shadow p-3 bg-dark">
				<div class="container">
				<a> <img class="rounded" src="resources/img/WRK2.png" alt=""></a>
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
				<div class="container">
				<center> <h2>VER REGISTRO <?= $'.$primarykey.' ?> - '.strtoupper($TABLA).'</h2> </center>
				<form action="UPD'.$TABLA.'.php" method="POST" accept-charset="utf-8">
				<input type="hidden" name="'.$primarykey.'" value="<?= $'.$primarykey.'?>">
				'.$LABINP.'
				<button type="submit" name="UPDPARMS" value="1" class="btn btn-dark">
				Editar
				</button>
				<a href="LST'.$TABLA.'.php" class="btn btn-dark">
				Atras
				</a>
				</form>
				</div>
				<footer>
				<center>
				<strong>Copyright © WRKPHP</strong>
				Todos los derechos reservados. <b>(Versión</b> <b>1.0)</b>
				</center>
				</footer>
				<script src="resources/jquery/jquery-3.5.1.min.js"></script>
				<script src="resources/js/bootstrap.bundle.min.js"></script>
				</body>
				</html>';
				$fp = fopen($name, 'w');
				fwrite($fp,  $plantillahtml);
				fclose($fp);
				return true;
			}

			function WRKPHPINS ($mysqli, $DB, $TABLA) {
				$name="$DB/$TABLA/INS$TABLA.php";
				$BUFIN = '';
				$BUFINVAR = '';
				$BUFINPARM = '';
				$PARMSR = '';
				$TYPES = '';
				$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
				$stmt->execute();
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
					}
					else  {
						if (stristr($row['Type'], 'int')) {
							$TYPES = $TYPES.''.'i';
						}
						else {
							$TYPES = $TYPES.''.'s';
						}	
						$BUFIN = $BUFIN.','.$row['Field'];
						$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
						$PARMSR = $PARMSR.',?';
					}
				}
				$BUFIN = substr($BUFIN, 1);
				$BUFINVAR = substr($BUFINVAR, 1);
				$PARMSR = substr($PARMSR, 1);
				$plantillaphp = '<?php
				include "dbround.php";
				if (isset($_POST["INSPARMS"])) {

					if (valparm($_POST) == false) {
		echo "<script type='."'text/javascript'".'>
					alert('."'Error en el análisis de los datos recibidos, por favor revisar campos.'".');
					window.location.href='."'INS$TABLA.html'".';
					</script>";
					return;
	}

					extract(sanparm($_POST, $mysqli));
					$stmt = $mysqli->prepare("INSERT INTO '.$TABLA.'('.$BUFIN.') VALUES ('.$PARMSR.')");
					$stmt->bind_param('."'".''.$TYPES.''."'".', '.$BUFINVAR.');
					$stmt->execute();
					mysqli_stmt_close($stmt);
					echo "<script type='."'text/javascript'".'>
					alert('."'INSERTAR EJECUTADO'".');
					window.location.href='."'INS$TABLA.html'".';
					</script>";
				} 


				function valparm($PARMS) {
	foreach ($PARMS as $key => $value) {
		if ((empty($value) && !is_numeric($value)) || $value == "") {
			return false;
		}
	}
	return true;
} 

function sanparm($params, $mysqli) {
  foreach ($params as $key => &$value) {
    // Filtrar y limpiar el valor recibido
    $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);

    // Limpieza adicional del valor para evitar inyecciones SQL
    $value = mysqli_real_escape_string($mysqli, $value);
  }

  // Devolver el array modificado
  return $params;
}


				?>';
				$fp = fopen($name, 'w');
				fwrite($fp,  $plantillaphp);
				fclose($fp);
				return true;
			}

			function WRKPHPUPD ($mysqli, $DB, $TABLA) {
				$name="$DB/$TABLA/UPD$TABLA.php";
				$BUFIN = '';
				$BUFINVAR = '';
				$BUFINVAR2 = '';
				$BUFINVAR3 = '';
				$BUFINPARM = '';
				$PARMSR = '';
				$TYPES = '';	
				$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
				$stmt->execute();
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
						$primarykey = $row['Field'];
						$primarykeyvalue = $row['Field'];
						$PARMSR = $PARMSR.',?';
						if (stristr($row['Type'], 'int')) {
							$TYPEPKEY = 'i';
						}
						else {
							$TYPEPKEY = 's';
						}	
					}
					elseif($row['Key'] == 'PRI') {
						$primarykey = $row['Field'];
						$primarykeyvalue = $row['Field'];
						$PARMSR = $PARMSR.',?';
						if (stristr($row['Type'], 'int')) {
							$TYPEPKEY = 'i';
						}
						else {
							$TYPEPKEY = 's';
						}	
					}
					else  {
						if (stristr($row['Type'], 'int')) {
							$TYPES = $TYPES.''.'i';
						}
						else {
							$TYPES = $TYPES.''.'s';
						}	
						$BUFIN = $BUFIN.','.$row['Field'].'= ?';
						$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
						$BUFINVAR2 = $BUFINVAR2.'$'.$row['Field'].'db='.'$row["'.$row['Field'].'"];'."\n";
						$BUFINVAR3 = $BUFINVAR3.''.'if($'.$row['Field'].'db=='.'$'.$row['Field'].'){ $'.$row['Field'].' = $'.$row['Field'].'db;}'."\n";
						$PARMSR = $PARMSR.',?';
					}
				}
				$BUFIN = substr($BUFIN, 1);
				$BUFINVAR = substr($BUFINVAR, 1);
				$BUFINVAR2 = substr($BUFINVAR2, 0,-1);
				$PARMSR = substr($PARMSR, 1);
				$TYPES = $TYPES.''.'i';
				$BUFINVAR = $BUFINVAR.','.'$'.$primarykey;
				$plantillaphp = '
				<?php
				include "dbround.php";
				if (isset($_POST["UPDPARMS"])) {

						if (valparm($_POST) == false) {
							$id = $_POST['."'".''.$primarykey.''."'".'];
							echo "<script type='."'text/javascript'".'>
					alert('."'Error en el análisis de los datos recibidos, por favor revisar campos.'".');
					window.location.href='."'DSP$TABLA.php?$primarykey=$"."id".''."'".';
					</script>";
					return;
		
	}

					extract(sanparm($_POST, $mysqli));
					$stmt = $mysqli->prepare("SELECT * FROM '.$TABLA.' WHERE '.$primarykey.' = ?");
					$stmt->bind_param ('."'".''.$TYPEPKEY.''."'".', $'.$primarykeyvalue.');
					$stmt->execute();
					$result = $stmt->get_result();
					if (mysqli_num_rows($result) == 1) {
						$row = $result->fetch_assoc();
						'.$BUFINVAR2.';
					}
					'.$BUFINVAR3.'
					$stmt = $mysqli->prepare("UPDATE '.$TABLA.' SET '.$BUFIN.' WHERE '.$primarykey.' = ?");
					$stmt->bind_param ('."'".''.''.$TYPES.''.''."'".','.$BUFINVAR.');
					$stmt->execute();
					mysqli_stmt_close($stmt);
					echo "<script type='."'text/javascript'".'>
					alert('."'ACTUALIZAR EJECUTADO'".');
					window.location.href='."'DSP$TABLA.php?$primarykey=".'$'.""."$primarykey'".';
					</script>";
					} 


					function valparm($PARMS) {
	foreach ($PARMS as $key => $value) {
		if ((empty($value) && !is_numeric($value)) || $value == "") {
			return false;
		}
	}
	return true;
} 

function sanparm($params, $mysqli) {
  foreach ($params as $key => &$value) {
    // Filtrar y limpiar el valor recibido
    $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);

    // Limpieza adicional del valor para evitar inyecciones SQL
    $value = mysqli_real_escape_string($mysqli, $value);
  }

  // Devolver el array modificado
  return $params;
}


					?>
					';
					$fp = fopen($name, 'w');
					fwrite($fp,  $plantillaphp);
					fclose($fp);
					return true;
				}

				function WRKPHPDLT ($mysqli, $DB, $TABLA) {
					$name="$DB/$TABLA/DLT$TABLA.php";
					$BUFIN = '';
					$BUFINVAR = '';
					$BUFINVAR2 = '';
					$BUFINVAR3 = '';
					$BUFINPARM = '';
					$PARMSR = '';
					$TYPES = '';
					$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
							$primarykey = $row['Field'];
							$primarykeyvalue = $row['Field'];
							$PARMSR = $PARMSR.',?';
							if (stristr($row['Type'], 'int')) {
								$TYPEKEY = 'i';
							}
							else {
								$TYPEKEY = 's';
							}
						}
						elseif ($row['Key'] == 'PRI') {
							$primarykey = $row['Field'];
							$primarykeyvalue = $row['Field'];
							$PARMSR = $PARMSR.',?';
							if (stristr($row['Type'], 'int')) {
								$TYPEKEY = 'i';
							}
							else {
								$TYPEKEY = 's';
							}		
						}
						else  {
							if (stristr($row['Type'], 'int')) {
								$TYPES = $TYPES.''.'i';
							}
							else {
								$TYPES = $TYPES.''.'s';
							}	
							$BUFIN = $BUFIN.','.$row['Field'].'= ?';
							$BUFINVAR = $BUFINVAR.','.'$'.$row['Field'];
							$BUFINVAR2 = $BUFINVAR2.'$'.$row['Field'].'db='.'$row["'.$row['Field'].'"];'."\n";
							$BUFINVAR3 = $BUFINVAR3.''.'if($'.$row['Field'].'db=='.'$'.$row['Field'].'){ $'.$row['Field'].' = $'.$row['Field'].'db;}'."\n";
							$PARMSR = $PARMSR.',?';
						}
					}
					$BUFIN = substr($BUFIN, 1);
					$BUFINVAR = substr($BUFINVAR, 1);
					$BUFINVAR2 = substr($BUFINVAR2, 0,-1);
					$PARMSR = substr($PARMSR, 1);
					$TYPES = $TYPES.''.'i';
					$BUFINVAR = $BUFINVAR.','.'$'.$primarykey;
					$plantillaphp = '
					<?php
					include "dbround.php";
					if (isset($_GET["'.$primarykey.'"])) {
						$'.$primarykey.' = $_GET["'.$primarykey.'"];
						$stmt = $mysqli->prepare("DELETE FROM '.$TABLA.' WHERE '.$primarykey.' = ?");
						$stmt->bind_param ('."'".''.''.$TYPEKEY.''.''."'".',$'.$primarykey.');
						$stmt->execute();
						mysqli_stmt_close($stmt);
						echo "<script type='."'text/javascript'".'>
						alert('."'ELIMINAR EJECUTADO'".');
						window.location.href='."'LST$TABLA.php'".';
						</script>";
						} ?>
						';
						$fp = fopen($name, 'w');
						fwrite($fp,  $plantillaphp);
						fclose($fp);
						return true;
					}

					function copyfolder( $source, $target ) {
						if ( is_dir( $source ) ) {
							@mkdir( $target );
							$d = dir( $source );
							while ( FALSE !== ( $entry = $d->read() ) ) {
								if ( $entry == '.' || $entry == '..' ) {
									continue;
								}
								$Entry = $source . '/' . $entry; 
								if ( is_dir( $Entry ) ) {
									copyfolder( $Entry, $target . '/' . $entry );
									continue;
								}
								copy( $Entry, $target . '/' . $entry );
							}
							
							$d->close();
						}else {
							copy( $source, $target );
						}
					}
				?>