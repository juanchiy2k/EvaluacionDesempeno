<?php
session_name('responsiveForm');
session_start();


// Vaciamos las variables de sesión
if(!empty($_SESSION)){
	$_SESSION = array();
	session_destroy();
}

header("Location:index.php");

?>