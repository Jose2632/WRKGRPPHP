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
	$WRKHTMLLST = WRKHTMLLST ($mysqli, $DB, $TABLA);
	$WRKLSTTBLREAD = WRKLSTTBLREAD ($mysqli, $DB, $TABLA);
	$WRKDSPREAD = WRKDSPREAD ($mysqli, $DB, $TABLA);
	$WRKHTMLDSP = WRKHTMLDSP ($mysqli, $DB, $TABLA);
	$WRKPHPINS = WRKPHPINS($mysqli, $DB, $TABLA);
	$WRKPHPUPD = WRKPHPUPD($mysqli, $DB, $TABLA);
	$WRKPHPDLT = WRKPHPDLT($mysqli, $DB, $TABLA);
	$WRKPHPJS = WRKPHPJS($mysqli, $DB, $TABLA);

	$WRKGRP = [
		$WRKDBROUND,
		$WRKHTMLLST,
		$WRKLSTTBLREAD,
		$WRKDSPREAD,
		$WRKHTMLDSP,
		$WRKPHPINS,
		$WRKPHPUPD,
		$WRKPHPDLT,
		$WRKPHPJS
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
		$name="$DB/$TABLA/dbround.php";
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

	function WRKHTMLLST ($mysqli, $DB, $TABLA) {
		$name="$DB/$TABLA/LST$TABLA.php";
		$BUFIN = '';
		$BUFINVAR = '';
		$BUFINPARM = '';
		$PARMSR = '';
		$TYPES = '';
		$LABINP = '';
		$LABINP2 = '';
		$LABINP3 = '';
		$LABINP4 = '';
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
				$val = "KEYAUTO";
			}
			elseif ($row['Key'] == 'PRI') {
				$primarykey = $value;
				$primarykeyvalue = $value;
				$val = "KEY";
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
					$LABINP3 = $LABINP3."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='number' class='form-control' id='$value' required>
					</div>\n";
					$LABINP4 = $LABINP4."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='number' class='form-control' id='update_$value' required>
					</div>\n";
				}
				elseif (stristr($row['Type'], 'date')) {
					$TYPES = $TYPES.''.'i';
					$LABINP = $LABINP."
					<td>$value</td>\n";
					$LABINP2 = $LABINP2."
					<td>$rowini$x$value$x$rowend</td>\n";
					$LABINP3 = $LABINP3."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='date' class='form-control' id='$value' required>
					</div>\n";
					$LABINP4 = $LABINP4."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='date' class='form-control' id='update_$value' required>
					</div>\n";
				}
				elseif (stristr($row['Type'], 'time')) {
					$TYPES = $TYPES.''.'i';
					$LABINP = $LABINP."
					<td>$value</td>\n";
					$LABINP2 = $LABINP2."
					<td>$rowini$x$value$x$rowend</td>\n";
					$LABINP3 = $LABINP3."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='time' class='form-control' id='$value' required>
					</div>\n";
					$LABINP4 = $LABINP4."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='time' class='form-control' id='update_$value' required>
					</div>\n";
				}
				elseif (stristr($row['Type'], 'text')){
					$LABINP = $LABINP."
					<td>$value</td>\n";
					$LABINP2 = $LABINP2."
					<td>$rowini$x$value$x$rowend</td>\n";
					$LABINP3 = $LABINP3."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<textarea class='form-control' id='$value' required></textarea>
					</div>\n";
					$LABINP4 = $LABINP4."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<textarea class='form-control' id='update_$value' required></textarea>
					</div>\n";
				}
				else {
					$LABINP = $LABINP."
					<td>$value</td>\n";
					$LABINP2 = $LABINP2."
					<td>$rowini$x$value$x$rowend</td>\n";
					$LABINP3 = $LABINP3."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='text' class='form-control' id='$value' required>
					</div>\n";
					$LABINP4 = $LABINP4."
					<div class='form-group'>
					<label for='$value'>".ucfirst($value)."</label>
					<input type='text' class='form-control' id='update_$value' required>
					</div>\n";
				}	
			}
		}
		if ($val == "KEY") {
			$LABINP3 = "
					<div class='form-group'>
					<label for='$primarykey'>".ucfirst($primarykey)."</label>
					<input type='text' class='form-control' id='$primarykey' required>
					</div>\n".$LABINP3;
			
		}
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
		<div class="modal fade" id="INSmodal" tabindex="-1" aria-labelledby="INSmodal" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="INSmodal">INSERTAR EN '.strtoupper($TABLA).'</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body">
		<div class="container">
		<form id="INSpruebas" method="POST" accept-charset="utf-8">
		'.$LABINP3.'
		<button type="button" id="formins" onclick="insparms()" name="INSPARMS" value="1" class="btn btn-dark">
		Aceptar
		</button>
		<button type="button" id="cancelmodal" class="btn btn-dark">
		Cancelar
		</button>
		</form>
		<div id="contentreads"></div>
		</div>
		</div>
		</div>
		</div>
		</div>
		<div class="modal fade" id="modalupdparms" tabindex="-1" aria-labelledby="modalupdparms" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="modalupdparms">ACTUALIZAR EN '.strtoupper($TABLA).'</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body">
		<div class="container">
		<form id="UPDpruebas" method="POST" accept-charset="utf-8">
		'.$LABINP4.'
		<button type="button" id="formupd" onclick="updparms()" name="UPDPARMS" value="1" class="btn btn-dark">
		Aceptar
		</button>
		<button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
		<input type="hidden" id="idparm">
		</form>
		<div id="contentreads">
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>
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
		<a data-toggle="modal" data-target="#INSmodal" class="btn btn-dark text-light" id="modalingresar">
		Ingresar
		</a>
		<hr>
		<div id="LST'.$TABLA.'"></div>
		</div>
		<footer>
		<center>
		<strong>Copyright © WRKPHP</strong>
		Todos los derechos reservados. <b>(Versión</b> <b>1.0)</b>
		</center>
		</footer>
		<script src="resources/jquery/jquery-3.5.1.min.js"></script>
		<script src="resources/js/bootstrap.bundle.min.js"></script>
		<script src="script.js"></script>
		</body>
		</html>';
		$fp = fopen($name, 'w');
		fwrite($fp,  $plantillahtml);
		fclose($fp);
		return true;
	}


	function WRKLSTTBLREAD ($mysqli, $DB, $TABLA) {
		$name="$DB/$TABLA/LST$TABLA-read.php";
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
		$rowini = '$row[';
		$x = "'";
		$x2 = "'.";
		$x3 = ".'";
		$rowend = ']';
		while ($row = $result->fetch_assoc()) {
			$value = $row['Field'];
			if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
				$primarykey = $value;
				$primarykeyvalue = $value;
			}
			elseif ($row['Key'] == 'PRI') {
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
					<td>".ucfirst($value)."</td>\n";
					$LABINP2 = $LABINP2."
					<td>$x2$rowini$x$value$x$rowend$x3</td>\n";
				}
				else {
					$TYPES = $TYPES.''.'s';
					$LABINP = $LABINP."
					<td>".ucfirst($value)."</td>\n";
					$LABINP2 = $LABINP2."
					<td>$x2$rowini$x$value$x$rowend$x3</td>\n";
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
		$plantillajs = '
		<?php 
		include "dbround.php";
		$stmt = $mysqli->prepare("SELECT * FROM '.$TABLA.'");
		$stmt->execute();
		$result = $stmt->get_result();
		$data =  '.$x.'<div class="row justify-content-center">
		<div class="col-md-12">
		<div class="card">
		<center> 
		<h3><strong><div class="card-header">LST '.strtoupper($TABLA).' </div> </strong></h3>
		</center>
		<table class="table-responsive-md table table-hover">
		<thead>
		<tr>    
		'.$LABINP.'
		<td>Acciones</td>              
		</tr>
		</thead>
		<tbody>'.$x.';
		while($row = mysqli_fetch_assoc($result)) {
			$data.= '.$x.'<tr>
			'.$LABINP2.'
			<td> 
			<div class="btn-group" role="group" aria-label="acciones">

			<a href="DSP'.$TABLA.'.php?'.$primarykeyvalue.'='.$x2.$rowini.''.$x.''.$primarykeyvalue.''.$x.''.$rowend.$x3.'" class="btn btn-white" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i> 
			</a>
			<a onclick="getparms('.$x2.$rowini.''.$x.''.$primarykeyvalue.''.$x.''.$rowend.$x3.')" class="btn btn-primary" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i> 
			</a>
			<a onclick="dltparms('.$x2.$rowini.''.$x.''.$primarykeyvalue.''.$x.''.$rowend.$x3.')" class="btn btn-dark" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i> 
			</a>
			</div>
			</td>
			</tr>'.$x.';
		}					
		$data .='.$x.'
		</tbody>
		</table>
		</div>
		</div>
		</div>'.$x.';
		echo $data;
		?>';
		$fp = fopen($name, 'w');
		fwrite($fp,  $plantillajs);
		fclose($fp);
		return true;
	} 

	function WRKDSPREAD ($mysqli, $DB, $TABLA) {
		$name="$DB/$TABLA/DSP$TABLA-read.php";
		$BUFIN = '';
		$BUFINVAR = '';
		$BUFINVAR2 = '';
		$BUFINVAR3 = '';
		$BUFINPARM = '';
		$PARMSR = '';
		$TYPES = '';
		$x = "'";
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
			elseif ($row['Key'] == 'PRI') {
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
		$stmt = $mysqli->prepare("SELECT * FROM '.$TABLA.' WHERE '.$primarykey.' = ?");
		$stmt->bind_param ('.$x.''.$TYPEPKEY.''.$x.', $'.$primarykey.');

		if (isset($_POST['.$x.''.$primarykey.''.$x.'])) {
			$'.$primarykey.' = $_POST['.$x.''.$primarykey.''.$x.'];
		}
		else {
			header("Location: ./");
			return;
		}
		$stmt->execute();
		$result = $stmt->get_result();
		$parms = array();
		if(mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$parms = $row;
			}
		}
		echo json_encode($parms);
		?>';
		$fp = fopen($name, 'w');
		fwrite($fp,  $plantillaphp);
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
			elseif ($row['Key'] == 'PRI') {
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
					<input type='number' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend disabled>
					</div>\n";
				}
				elseif (stristr($row['Type'], 'date')) {
					$TYPES = $TYPES.''.'i';
					$LABINP = $LABINP."
					<div class='form-group'>
					<label for='$value'>$value</label>
					<input type='date' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend disabled>
					</div>\n";
				}
				elseif (stristr($row['Type'], 'time')) {
					$TYPES = $TYPES.''.'i';
					$LABINP = $LABINP."
					<div class='form-group'>
					<label for='$value'>$value</label>
					<input type='time' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend disabled>
					</div>\n";
				}
				else {
					$TYPES = $TYPES.''.'s';
					$LABINP = $LABINP."
					<div class='form-group'>
					<label for='$value'>$value</label>
					<input type='text' class='form-control' name='$value' value=$rowini$x$value$x$rowend placeholder=$rowini$x$value$x$rowend disabled>
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
		<script src="script.js"></script>
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
		$plantillaphp = '
		<?php
		include "dbround.php";
		if (isset($_POST["INSPARMS"])) {
			if (valparm($_POST) == false) {
				$data = ["error"=> 1,"msg"=>"Error, Revisar Campos"];
				echo json_encode($data);
				exit;
			}
			else {
				extract(sanparm($_POST, $mysqli));
				$stmt = $mysqli->prepare("INSERT INTO '.$TABLA.'('.$BUFIN.') VALUES ('.$PARMSR.')");
				$stmt->bind_param('."'".''.$TYPES.''."'".', '.$BUFINVAR.');
				$stmt->execute();
				mysqli_stmt_close($stmt);
				$data = ["error"=> 0,"msg"=>"Insertar Ejecutado"];
				echo json_encode($data);
				exit;
			}
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
			elseif ($row['Key'] == 'PRI') {
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
		$data = ["error"=> 1,"msg"=>"Error, Revisar Campos"];
		echo json_encode($data);
		exit;
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
		if (isset($_POST["'.$primarykey.'"])) {
			$'.$primarykey.' = $_POST["'.$primarykey.'"];
			$stmt = $mysqli->prepare("DELETE FROM '.$TABLA.' WHERE '.$primarykey.' = ?");
			$stmt->bind_param ('."'".''.''.$TYPEKEY.''.''."'".',$'.$primarykey.');
			$stmt->execute();
			mysqli_stmt_close($stmt);
		} ?>';
		$fp = fopen($name, 'w');
		fwrite($fp,  $plantillaphp);
		fclose($fp);
		return true;
	}

	function WRKPHPJS ($mysqli, $DB, $TABLA) {
		$name="$DB/$TABLA/script.js";
		$LABINP = '';
		$LABINP2 = '';
		$LABINP3 = '';
		$LABINP4 = '';
		$LABINP5 = '';
		$stmt = $mysqli->prepare("SHOW COLUMNS FROM $TABLA FROM $DB");
		$stmt->execute();
		$result = $stmt->get_result();
		$val = 'KEYAUTO';
		$x = "'";
		while ($row = $result->fetch_assoc()) {
			$value = $row['Field'];
			if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
				$primarykey = $value;
			}
			elseif ($row['Key'] == 'PRI') {
				$primarykey = $value;
				$val = 'KEY';
			}
			else  {
				if ($val == 'KEYAUTO') {
				$LABINP = $LABINP."var $value = $('#$value').val();";
				$LABINP2 = $LABINP2."$value:$value".',';
				$LABINP3 = $LABINP3."var $value = $('#update_$value').val();";
				$LABINP4 = $LABINP4."#".$value.",";
				$LABINP5 = $LABINP5. '$'."($x#"."update_$value"."$x).val(data.$value);";		
				}
				elseif ($val == 'KEY') {
				$LABINP = $LABINP."var $value = $('#$value').val();";
				$LABINP2 = $LABINP2."$value:$value".',';
				$LABINP3 = $LABINP3."var $value = $('#update_$value').val();";
				$LABINP4 = $LABINP4."#".$value.",";
				$LABINP5 = $LABINP5. '$'."($x#"."update_$value"."$x).val(data.$value);";	
				}	
			}
		}
		if ($val == 'KEY') {
			$LABINP = "var $primarykey = $('#$primarykey').val();".$LABINP;
			$LABINP2 = "$primarykey:$primarykey".','.$LABINP2;
			$LABINP3 = "var $primarykey = $('#update_$primarykey').val();".$LABINP3;
			$LABINP4 = "#".$primarykey.",".$LABINP4;
			$LABINP5 = '$'."($x#"."update_$primarykey"."$x).val(data.$primarykey);".$LABINP5;
			$LABINP4 = substr($LABINP4,0,-1);
		}
		else {
		$LABINP4 = substr($LABINP4,0,-1);
	}
		$plantillajs = <<<EOD
		$(document).ready(function () {
			lstparms();
			});
			$("#modalingresar").click(function() {
				$("#INSmodal").modal("show");
				$("#cancelmodal").click(function(){
					$('#contentreads').fadeIn(1000).html('');
					$("#INSmodal").modal("hide");
					});
					});

					function insparms() {
						$LABINP
						var INSPARMS = "INSPARMS";
						$("#formins").prop('disabled', true);
						$('#contentreads').html('<div class="d-flex align-items-center"><strong>Cargando...</strong><div class="spinner-border ms-auto" role="status" aria-hidden="true"></div></div>');
						$.ajax({
							type: "POST",
							url: "INS$TABLA.php",
							data: {
								$LABINP2
								INSPARMS: INSPARMS
								},
								cache: false,
								success: function(data) {
									data = JSON.parse(data);
									if (data.error == 1) {
										$('#contentreads').fadeIn(1000).html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>'+data.msg+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></div>');
										$("#formins").prop('disabled', false);
										return false;
									}
									else {
										$('#contentreads').fadeIn(1000).html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>'+data.msg+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></div>');
										$('$LABINP4').val('');
										$("#formins").prop('disabled', false);
										lstparms();
										return true;
									}     
									},
									error: function(data) {
										$('#contentreads').fadeIn(1000).html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>'+data.msg+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></div>');
										$("#formins").prop('disabled', false);
										return false;
										},
										});  
									}
									function getparms(id) {
										$("#idparm").val(id);
										$.post("DSP$TABLA-read.php", {
											$primarykey: id
											},
											function (data, status) {  
												var data = JSON.parse(data);   
												$LABINP5
											}
											);
											$("#modalupdparms").modal("show");
										}

										function dltparms(id) {
											var conf = confirm("¿Está seguro, realmente desea eliminar el registro?");
											if (conf == true) {
												$.post("DLT$TABLA.php", {
													$primarykey: id
													},
													function (data, status) {

														lstparms();
													}
													);
												}
											}

											function updparms() {
												$LABINP3
												var UPDPARMS = 'UPDPARMS';
												var $primarykey = $("#idparm").val();
												$.post("UPD$TABLA.php", {
													$primarykey: $primarykey,
													$LABINP2
													UPDPARMS: UPDPARMS
													},
													function (data, status) {
														$("#modalupdparms").modal("hide");
														lstparms();
													}
													);
												}
												function lstparms() {
													$.get("LST$TABLA-read.php", {}, function (data, status) {
														$("#LST$TABLA").html(data);
														});
													}
EOD;
													$fp = fopen($name, 'w');
													fwrite($fp,  $plantillajs);
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