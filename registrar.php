<?php
    include_once("conexion.php");
    include_once("imprimir.php");
    include_once('funciones.php');
    include_once('email.php');
	
class Registrar{
	private $db;
	public function __construct($sql){
		$this->db = new Conexion($sql);
	}
	
	public function registrarPlanificacion($pais, $codPersonal, $nombre, $periodo, $objetivos, $actividades, $cronogramas, $metas, $pesos, $acciones, $actitud, $compromiso, $enviarmail){
		
	    try{
            $this->db->beginTransaction();
            $planificacion = $this->db->prepare('CALL sp_RegistrarPlanificacion(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

            $codigo = $pais.'-'.$nombre.'-'.date('Y');

            $planificacion->bindParam(1, $codigo, PDO::PARAM_STR, 20);
            $planificacion->bindParam(2, $codPersonal, PDO::PARAM_STR, 9);
            $planificacion->bindParam(3, $periodo, PDO::PARAM_STR, 4);
            $planificacion->bindParam(4, implode('^', $objetivos), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(5, implode('^', $actividades), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(6, implode('^', $cronogramas), PDO::PARAM_STR, 255);
            $planificacion->bindParam(7, implode('^', $metas), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(8, implode('^', $pesos), PDO::PARAM_STR, 100);
            $planificacion->bindParam(9, implode('^', $acciones), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(10, $actitud, PDO::PARAM_STR, 1000);
            $planificacion->bindParam(11, $compromiso, PDO::PARAM_STR, 1000);
            $planificacion->execute();

            $this->db->commit();

            
            $imprimir = new Imprimir();
            $imprimir->imprimirPlanificacion($codPersonal, $periodo, $enviarmail, false);
                       
        }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
        
            
    }

    public function editarPlanificacion($pais, $codPersonal, $nombre, $periodo, $codplanificacion, $codobjetivos, $objetivos, $actividades, $cronogramas, $metas, $pesos, $codacciones, $acciones, $codmejora, $actitud, $compromiso, $imprimir, $enviarmail){

        $codigo = $pais.'-'.$nombre.'-'.date('Y');

        try{
            $this->db->beginTransaction();
            $planificacion = $this->db->prepare('CALL sp_EditarPlanificacion(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

            $planificacion->bindParam(1, $codigo, PDO::PARAM_STR, 14);
            $planificacion->bindParam(2, $codplanificacion, PDO::PARAM_STR, 20);
            $planificacion->bindParam(3, implode('^', $codobjetivos), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(4, implode('^', $objetivos), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(5, implode('^', $actividades), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(6, implode('^', $cronogramas), PDO::PARAM_STR, 255);
            $planificacion->bindParam(7, implode('^', $metas), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(8, implode('^', $pesos), PDO::PARAM_STR, 100);
            $planificacion->bindParam(9, implode('^', $codacciones), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(10, implode('^', $acciones), PDO::PARAM_STR, 1000);
            $planificacion->bindParam(11, $codmejora, PDO::PARAM_STR, 20);
            $planificacion->bindParam(12, $actitud, PDO::PARAM_STR, 1000);
            $planificacion->bindParam(13, $compromiso, PDO::PARAM_STR, 1000);
            $planificacion->execute();
            
            $this->db->commit();
            
            $imprimir=($imprimir=="true")?true:false;
            if ($imprimir){
                $imprimir = new Imprimir();
                $imprimir->imprimirPlanificacion($codPersonal, $periodo, $enviarmail, false);
            }

        }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
        
    }


    public function aprobarPlanificacion($codplanificacion, $codpersonal, $periodo){

        try{
            $this->db->beginTransaction();
            $planificacion = $this->db->prepare('CALL sp_AprobarPlanificacion(?)');
            $planificacion->bindParam(1, $codplanificacion, PDO::PARAM_STR, 20);
            $planificacion->execute();

             $this->db->commit();

            $imprimir = new Imprimir();
            $imprimir->imprimirPlanificacion($codpersonal, $periodo, true, true);
            
        }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
       
    }

    public function registrarPersonal($apellido, $nombre, $tipodni, $dni, $fechanacimiento, $fechaingreso, $fechacargo, $email, $codpersuperior, $codperfuncional, $codpais, $codarea, $codoficina, $codcargo, $codperfil){
        try {
            $this->db->beginTransaction();
            $personal = $this->db->prepare('CALL sp_RegistrarEmpleado(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $personal->bindParam(1, $apellido, PDO::PARAM_STR, 45);
            $personal->bindParam(2, $nombre, PDO::PARAM_STR, 45);
            $personal->bindParam(3, $tipodni, PDO::PARAM_STR, 15);
            $personal->bindParam(4, $dni, PDO::PARAM_STR, 20);
            $personal->bindParam(5, normalizarFecha($fechanacimiento), PDO::PARAM_STR, 10);
            $personal->bindParam(6, normalizarFecha($fechaingreso), PDO::PARAM_STR, 10);
            $personal->bindParam(7, normalizarFecha($fechacargo), PDO::PARAM_STR, 10);
            $personal->bindParam(8, $email, PDO::PARAM_STR, 50);
            $personal->bindParam(9, $codpersuperior, PDO::PARAM_STR, 9);
            $personal->bindParam(10, $codperfuncional, PDO::PARAM_STR, 9);
            $personal->bindParam(11, $codpais, PDO::PARAM_STR, 3);
            $personal->bindParam(12, $codarea, PDO::PARAM_STR, 9);
            $personal->bindParam(13, $codoficina, PDO::PARAM_STR, 9);
            $personal->bindParam(14, $codcargo, PDO::PARAM_STR, 9);
            $personal->bindParam(15, $codperfil, PDO::PARAM_STR, 9);
            $personal->execute();

            $guardado = $this->db->commit();

            echo $guardado;

         }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
    }

    public function editarPersonal($codpersonal, $apellido, $nombre, $tipodni, $dni, $fechanacimiento, $fechaingreso, $fechacargo, $email, $codpersuperior, $codperfuncional, $codpais, $codarea, $codoficina, $codcargo, $codperfil, $motivo){
        try {
            $this->db->beginTransaction();
            $personal = $this->db->prepare('CALL sp_EditarEmpleado(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $personal->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
            $personal->bindParam(2, $apellido, PDO::PARAM_STR, 45);
            $personal->bindParam(3, $nombre, PDO::PARAM_STR, 45);
            $personal->bindParam(4, $tipodni, PDO::PARAM_STR, 15);
            $personal->bindParam(5, $dni, PDO::PARAM_STR, 20);
            $personal->bindParam(6, normalizarFecha($fechanacimiento), PDO::PARAM_STR, 10);
            $personal->bindParam(7, normalizarFecha($fechaingreso), PDO::PARAM_STR, 10);
            $personal->bindParam(8, normalizarFecha($fechacargo), PDO::PARAM_STR, 10);
            $personal->bindParam(9, $email, PDO::PARAM_STR, 50);
            $personal->bindParam(10, $codpersuperior, PDO::PARAM_STR, 9);
            $personal->bindParam(11, $codperfuncional, PDO::PARAM_STR, 9);
            $personal->bindParam(12, $codpais, PDO::PARAM_STR, 3);
            $personal->bindParam(13, $codarea, PDO::PARAM_STR, 9);
            $personal->bindParam(14, $codoficina, PDO::PARAM_STR, 9);
            $personal->bindParam(15, $codcargo, PDO::PARAM_STR, 9);
            $personal->bindParam(16, $codperfil, PDO::PARAM_STR, 9);
            $personal->bindParam(17, $motivo, PDO::PARAM_INT);
            $personal->execute();

            $guardado = $this->db->commit();
            $personal->closeCursor();

            $personal = $this->db->prepare('CALL sp_ObtenerEmpleado(?)');
            $personal->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
            $personal->execute();

            $datos = $personal->fetch(PDO::FETCH_ASSOC);
              
            $datos["success"] = $guardado;
            echo json_encode($datos);  

            //$email = new Email();
            //$email->enviarEdicionPersonal($datospersonal);

            

         }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
    }


    public function registrarBAja($codpersonal, $fechabaja, $motivo){
        try {
            $this->db->beginTransaction();
            $personal = $this->db->prepare('CALL sp_RegistrarBaja(?,?,?)');
            $personal->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
            $personal->bindParam(2, normalizarFecha($fechabaja), PDO::PARAM_STR, 10);
            $personal->bindParam(3, $motivo, PDO::PARAM_INT);
            $personal->execute();

            $guardado = $this->db->commit();

            echo $guardado;

         }catch(Exception $ex){
            $this->db->rollback();
            echo $ex->getMessage();
        }
    }

    
}


?>