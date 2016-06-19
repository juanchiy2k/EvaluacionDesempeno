<?php
session_name('responsiveForm');
session_start();

$root = '../';
require_once ($root.'php/nusoap/nusoap.php');

// array de salida
$appResponse = array(
	'respuesta' => false,
	'mensaje' => "Error en la aplicación"
);

// Verificamos las variables post y que exista la variable accion
if(isset($_POST) && !empty($_POST) && isset($_POST['accion'])){

	$wsdl = 'http://localhost:8080/rrhh/includes/servicio.php?wsdl';

	$client = new nusoap_client($wsdl, 'wsdl');

	switch ($_POST['accion']) {
		case 'login':

			$datos = array('user' => $_POST['txtUsrEmail'],
						   'pass' => $_POST['txtUsrPass']);

			$response = $client->call('login', array($datos));

			$appResponse['respuesta'] = $response['userLogin'];
			if ($appResponse['respuesta'] == true){
				$_SESSION['userLogin'] = true;
				$_SESSION['codpersonal'] = $response['codpersonal'];
				$_SESSION['userNombre'] = $response['userNombre'];
				$_SESSION['email'] = $response['mail'];
				$_SESSION['perfil'] = $response['perfil'];
				$_SESSION['codpais'] = $response['codpais'];
			}
		break;
		
		case 'cambiarPass':
			if(!empty($_POST['txtNewPass']) && !empty($_POST['txtRepeatPass']) && ($_POST['txtNewPass'] == $_POST['txtRepeatPass'] )){

				$datos = array('user' => $_SESSION['email'], 'pass' => $_POST['txtNewPass']);
				$appResponse['respuesta'] = $client->call('actualizaClave', array($datos));
			}
		break;

		case 'recuperaPass':
			// verificar variable de correo que no este vacía
			if(!empty($_POST['txtRecEmail'])){
				
				$appResponse['respuesta'] = $client->call('recuperaPass', array('email' => $_POST['txtRecEmail'])); 
				if ($appResponse['respuesta'] == true){
					$appResponse['mensaje'] = "Se envió correctamente la clave de aceso";

				}
				
			}
		break;

		default:
			$appResponse['mensaje'] = "Opción no disponible";
		break;
        }
}
else{
	$appResponse['mensaje'] = "Variables no definidas";
}

// Retorno de JSON
echo json_encode($appResponse);

?>