<?php
session_name('responsiveForm');
session_start();

// Directorio Raíz de la app
// Es utilizado en templateEngine.inc.php
$root = '';

// números de captcha
//$_SESSION['inicia_form'] = true;
include_once('internalizacion.php');


// Incluimos el archivo de textos
include('locale/textos/text_layout.php');

// Incluimos el template engine
include('includes/templateEngine.inc.php');

// Cargar extensión twig para poder usar gettext()
$twig->addExtension(new Twig_Extensions_Extension_I18n());

/*
if ( !isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' ) {
  header("Location: " . "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
  exit();
}
*/


if(!empty($_SESSION) && $_SESSION['userLogin'] == true){

	include('funciones.php');

	if (!permitirAcceso($_SESSION['perfil'])){
		$perfil = 'USUARIOS';
	}else{
		$perfil = 'EDITORES';
	}

	// Cargamos la plantilla
	$twig->display('index_form.html',array(
		"userName" => $_SESSION['userNombre'],
		"textos" => $textos,
		"lenguaje" => substr($lang, 0, 2),
		"perfil" => $perfil,
		"piePagina" => "<p>pro mujer | &copy; 2013"." - ".date('Y')." </p>"
	));
}
else{
    
       // Cargamos la plantilla
	$twig->display('login.html',array(
		"textos" => $textos));
}
	

?>