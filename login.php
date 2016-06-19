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

// Cargar extensión twig para poder usar gettext()
$twig->addExtension(new Twig_Extensions_Extension_I18n());

// Cargar extensión twig para poder usar gettext()
$twig->addExtension(new Twig_Extensions_Extension_I18n());

$twig->display('login.html',array(
		"textos" => $textos));

?>