<?php
session_name('responsiveForm');
session_start();

// Directorio Raíz de la app
// Es utilizado en templateEngine.inc.php
$root = '';

include_once('internalizacion.php');

// Incluimos el archivo de textos
include('locale/textos/text_layout.php');

// Incluimos el template engine
include('includes/templateEngine.inc.php');
include('funciones.php');

// Cargar extensión twig para poder usar gettext()
$twig->addExtension(new Twig_Extensions_Extension_I18n());

if(!empty($_SESSION) && $_SESSION['userLogin'] == true){

	if (!permitirAcceso($_SESSION['perfil'])){
		header("Location:index.php");
	}else{
		$perfil = 'EDITORES';
		// Cargamos la plantilla
		$twig->display('personal.html',array(
			"userName" => $_SESSION['userNombre'],
			"textos" => $textos,
			"lenguaje" => substr($lang, 0, 2),
			"perfil" => $perfil,
			"piePagina" => "<p>pro mujer | &copy; 2013"." - ".date('Y')." </p>"
		));
	}
}
	

?>