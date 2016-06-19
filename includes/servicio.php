<?php
$root = '../';
require_once($root.'php/nusoap/nusoap.php');
require_once($root.'usuario.php');
include_once($root.'email.php');

//creo un new soap server
$server = new soap_server();
//configuro el WSDL
$server->configureWSDL('usuario_wsdl', 'urn:usuario_wsdl'/*$namespace*/);

//seteo el namespace
//$server->wsdl->schemaTargetSpace = 'urn:usuariowsdl';//$namespace;
$server->wsdl->addComplexType(
	'UsuarioContrasena',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'user' => array('name' => 'user', 'type' => 'xsd:string'), 
		'pass' => array('name' => 'pass', 'type' => 'xsd:string')
	)
);

$server->wsdl->addComplexType(
	'DatosUsuario',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'userLogin' => array('name' => 'userLogin', 'type' => 'xsd:boolean'), 
		'codpersonal' => array('name' => 'codpersonal', 'type' => 'xsd:string'),
		'userNombre' => array('name' => 'userNombre', 'type' => 'xsd:string'),
		'mail' => array('name' => 'mail', 'type' => 'xsd:string'),
		'perfil' => array('name' => 'perfil', 'type' => 'xsd:string'),
		'codpais' => array('name' => 'codpais', 'type' => 'xsd:string')
	)
);
$server->register('login',
	array('datoslogin' => 'tns:UsuarioContrasena'),
	array('return' => 'tns:DatosUsuario'),
	'usuario_wsdl',
	'usuario_wsdl#login',
	'rpc',
    'encoded',
    'Este servicio retorna informacion de login' 
); 
$server->register('actualizaClave',
	array('datoslogin' => 'tns:UsuarioContrasena'),
	array('return' => 'xsd:boolean'),
	'usuario_wsdl',
	'usuario_wsdl#actualizaClave',
	'rpc',
    'encoded',
    'Este servicio actualiza el password del usuario' 
);
$server->register('recuperaPass',
	array('email' => 'xsd:string'),
	array('return' => 'xsd:boolean'),
	'usuario_wsdl',
	'usuario_wsdl#recuperaPass',
	'rpc',
    'encoded',
    'Este servicio recupera el password del usuario' 
);

$request = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($request);


function login($datos){
	$usuario = new Usuario();
	$usuarioAcceso = $usuario->userLogin($datos);
	
	return $usuarioAcceso;
}

function actualizaClave($datos){
	$usuario = new Usuario();
	$passModificada = $usuario->actualizaClave($datos);

	return $passModificada;
}

function recuperaPass($email){
	$respuestaReq = false;
	$usuario = new Usuario();
	if($usuario->verificaCorreo($email)){
		// Crear clave de acceso
		$pass = $usuario->creaPassword();

		$datos = array('user' => $email, 'pass' => $pass);

		// Actulizamos el password en la tabla
		if($usuario->actualizaClave($datos)){
			// Enviamos por correo electrónico la clave

			$email = new Email();						

			if($email->enviarPassword($datos)){
				$respuestaReq = true;
			}
		}
	}
	return $respuestaReq;
}


?>