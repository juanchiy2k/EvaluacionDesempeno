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

if(!empty($_SESSION) && $_SESSION['userLogin'] == true){

	include('funciones.php');

	if (!permitirAcceso($_SESSION['perfil'])){
		$perfil = 'USUARIOS';
	}else{
		$perfil = 'EDITORES';
	}
	
	if(isset($_POST['codpersonal'])){  
		$codpersonal = $_POST['codpersonal'];
		$aprobacion = true;
	}  else {
		$codpersonal = $_SESSION['codpersonal'];
		$aprobacion = false;
	}

        // Incluimos el template engine
	include('includes/templateEngine.inc.php');

	// Cargar extensión twig para poder usar gettext()
	$twig->addExtension(new Twig_Extensions_Extension_I18n());


	$anoactual = date('Y');

	$anoanterior = strtotime('-1 year', strtotime ($anoactual)) ;
	$anoanterior = date('Y', $anoanterior);

	$anosiguiente = strtotime('+1 year', strtotime ($anoactual)) ;
	$anosiguiente = date('Y', $anosiguiente);

	$anosiguientesiguiente = strtotime('+2 year', strtotime ($anoactual)) ;
	$anosiguientesiguiente = date('Y', $anosiguientesiguiente);

	$anosiguientesiguientesiguiente = strtotime('+3 year', strtotime ($anoactual)) ;
	$anosiguientesiguientesiguiente = date('Y', $anosiguientesiguientesiguiente);

	// Cargamos la plantilla
	$twig->display('planificacion.html',array(
		"userName" => $_SESSION['userNombre'],
		"codpersonal" => $codpersonal,
		"aprobacion" => $aprobacion,
		"textos" => $textos,
		"lenguaje" => substr($lang, 0, 2),
		"perfil" => $perfil,
		"piePagina" => "<p>pro mujer | &copy; 2013"." - ".date('Y')." </p>",
		"anoanterior" => $anoanterior,
		'anoactual' => $anoactual, 
		'anosiguiente' => $anosiguiente,
		'anosiguientesiguiente' => $anosiguientesiguiente,
		'anosiguientesiguientesiguiente' => $anosiguientesiguientesiguiente
	));

	
}
	

?>