<?php
    include_once ('conexion.php');

class Usuario {
    private $db;
    private $text;
        
    public function __construct(){
        $this->db = new Conexion();
        include('internalizacion.php');
        $this->lang = $lang;
        include('locale/textos/text_layout.php');
        $this->text = $textos;

    }

    public function userLogin($data){

    	$arrayUsuario = array(
				    		'userLogin' => false,
				    		'codpersonal' => '',
				    		'userNombre' => '',
				    		'mail' => '',
				    		'perfil' => '',
                            'codpais' => '');

		// validar si hay datos en los parametros
		if(!empty($data)){

			$query = $this->db->prepare('CALL sp_ObtenerUsuario(?, ?)');
			$query->bindParam(1, $data['user'], PDO::PARAM_STR, 50);
			$query->bindParam(2, md5(trim($data['pass'])), PDO::PARAM_STR, 32);
			$query->execute();

			if ($query->rowCount() != 0){
				$userData = $query->fetch(PDO::FETCH_ASSOC);

				$arrayUsuario = array(
									  'userLogin' => true,
									  'codpersonal' => $userData['codpersonal'],
									  'userNombre' => $userData['nombres'].' '.$userData['apellidos'],
									  'mail' => $userData['email'],
									  'perfil' => $userData['perfil'],
									  'codpais' => $userData['codpais']);
			}

		}

		return $arrayUsuario;
	}

	// Función para verificar la existencia del correo electrónico en la tabla de usuarios
	public function verificaCorreo($data){
		// Bandera de logueo
		$response = false;

		// validar si hay datos en los parametros
		if(!empty($data)){

			$query = $this->db->prepare('CALL sp_ObtenerCorreo(?)');
			$query->bindParam(1, $data, PDO::PARAM_STR, 50);
			$query->execute();

			if ($query->rowCount() != 0){
				$response = true;
			}

		}

		return $response;
	}


	// Función para crear una cadena aleatoria
	public function creaPassword( $length = 10 ){
		$key = "";
		$pattern = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789";

		for($i = 0 ; $i < $length ; $i++ )
		{
			$key .= $pattern[rand(0,53)];
		}

		return $key;
	}

	// Actualizar la contraseña del usuario en la tabla
	public function actualizaClave($data){
		$query = $this->db->prepare('CALL sp_EditarClave(?, ?)');
		$query->bindParam(1, $data['user'], PDO::PARAM_STR, 50);
		$query->bindParam(2, md5(trim($data['pass'])), PDO::PARAM_STR, 32);
		
		$query->execute();

		if($query->rowCount() == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
}


?>
