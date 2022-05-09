<?php 
if (isset($_GET['idcrud'])) {
	$dir = $_GET['idcrud'];
	delcrud($dir);
	header('Location: index.php');
	return;	
}
function delcrud($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delcrud("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }
?>