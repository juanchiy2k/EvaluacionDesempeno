<?php
session_name('responsiveForm');
session_start();


// array de salida
$appResponse = array(
	"respuesta" => false,
	"mensaje" => "Error en la aplicación",
	"contenido" => ""
);

$root = '../';

// Verificamos las variables post y que exista la variable accion
if(isset($_POST) && !empty($_POST) && isset($_POST['accion'])){

	// incluimos el archivo de funciones y conexión a la base de datos
	include($root.'usuario.php');

	//if($errorDbConexion == false){
    $usuario = new Usuario();
    switch ($_POST['accion']) {
		case 'login':
			
			$appResponse['respuesta'] = $usuario->userLogin($_POST);
			$appResponse['mensaje'] = "Usuario Encontrado";

		break;

		case 'cambiarPass':
			if(!empty($_POST['txtNewPass']) && !empty($_POST['txtRepeatPass']) && ($_POST['txtNewPass'] == $_POST['txtRepeatPass'] )){

				$appResponse['respuesta'] = $usuario->actualizaClave($_SESSION['email'], $_POST);
			}
			break;

		case 'recuperaPass':
			// verificar variable de correo que no este vacía
			if(!empty($_POST['txtRecEmail'])){
				// Verificamos que exista la cuentad e correo electrónico en nuestra tabla de usuarios
				if($usuario->verificaCorreo($_POST['txtRecEmail'])){

					// Crear clave de acceso
					$_POST['txtUsrPass'] = $usuario->creaPassword();

					// Actulizamos el password en la tabla
					if($usuario->actualizaClave('', $_POST)){
						// Enviamos por correo electrónico la clave

						include_once($root.'email.php');

						$email = new Email();						

						if($email->enviarPassword($_POST)){

							$appResponse['respuesta'] = true;
							$appResponse['mensaje'] = "Se envió correctamente la clave de aceso";

						}else{
							$appResponse['mensaje'] = "Se creo correctamente la contraseña pero no se realizó el envío de la misma por correo electrónico";
						}

					}
					else{
						$appResponse['mensaje'] = "No se puede actualziar la contraseña del usuario";
					}

				}
				else{
					$appResponse['mensaje'] = "Usuario no encontrado";
				}
			}
		break;

		case 'administracion':
			$appResponse = array(
				"respuesta" => true,
				"mensaje" => "",
				"contenido" => '
					<section>
					  <ul class="breadcrumb">
					    <li><a href="index.php">Inicio</a> <span class="divider">/</span></li>
					    <li>Administración</li>
					  </ul>
					</section>
					<div class="hero-unit">
					  <h1>Administración</h1>
					  <p>Esta sección se esta cargando por medio de ajax con la función $.ajax() de jquery.</p>
					  <p>
					  <a class="btn btn-primary btn-large">
					  Leer más...
					  </a>
					  </p>
					</div>
				'
			);

		break;

		default:
			$appResponse['mensaje'] = "Opción no disponible";
		break;
		}

	// }else{
	// 	$appResponse['mensaje'] = "Error al conectar con la base de datos";
	// }
		
}
else{
	$appResponse['mensaje'] = "Variables no definidas";
}

// Retorno de JSON
echo json_encode($appResponse);

?>