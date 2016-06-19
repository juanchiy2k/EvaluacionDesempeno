<?php
    include_once 'conexion.php';


class Listado {
    var $value;
    var $label;
    
   function __construct($label, $value){
      $this->label = $label;
      $this->value = $value;
   }
   
}
   
    
class Buscar {
    private $db;

        
    public function __construct(){
        $this->db = new Conexion();

    }
    
    public function buscarPersonal($codpersonal){
        
        if(!empty($codpersonal)){
            $result = $this->db->prepare('CALL sp_ObtenerEmpleado(?)');
            $result->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
            $result->execute();
            
            $datos = $result->fetch(PDO::FETCH_ASSOC);
            echo json_encode($datos);
            
        }
    }

    public function buscarPersonalCargo($codpersonal){
        
        if(!empty($codpersonal)){
            $result = $this->db->prepare('CALL sp_ObtenerPersonalCargo(?)');
            $result->bindParam(1, $codpersonal, PDO::PARAM_INT);
            $result->execute();

            while($datos = $result->fetch(PDO::FETCH_ASSOC)){
                $json[] = array(
                    "codpersonal" => $datos["codpersonal"],
                    "apellidos" => $datos["apellidos"],
                    "nombres" => $datos["nombres"],
                    "estado" => $datos["estado"]
                );
            
            }
            echo json_encode($json);
            
        }
    }

     public function buscarPlanificacion($codpersonal, $periodo){
        if (!empty($codpersonal) && !empty($periodo)) {
            $result = $this->db->prepare('CALL sp_ObtenerPlanificacion(?,?)');
            $result->bindParam(1, $codpersonal, PDO::PARAM_STR, 9);
            $result->bindParam(2, $periodo, PDO::PARAM_STR, 4);
            $result->execute();

            $i = 1;
            do {
                $rowset = $result->fetchAll(PDO::FETCH_ASSOC);
                if ($rowset) {
                    switch ($i){
                        case 1:
                            $array['mejora'] = $this->printResultSet($rowset, $i);
                            break;
                        case 2:
                            $array['objetivos'] = $this->printResultSet($rowset, $i);
                            break;
                        case 3:
                            $array['acciones'] = $this->printResultSet($rowset, $i);
                            break;
                    }
                   
                }
                $i++;
            } while ($result->nextRowset());

            echo json_encode($array);
            
        }


    }

   
    public function printResultSet(&$rowset, $i) {
        
        foreach ($rowset as $row) {
            switch ($i){
                    case 1: 
                         $array[] = array(
                                    'codplanificacion' => $row['codplanificacion'],
                                    'estado' => $row['estado'],
                                    'codmejora' => $row['codmejora'],
                                    'mejora' => $row['mejora'],
                                    'compromiso' => $row['compromiso']
                                );
                    break;
                    case 2:
                        $array[] =  array(
                                    'codobjetivo' => $row['codobjetivo'],
                                    'objetivo' => $row['objetivo'],
                                    'actividad' => $row['actividad'],
                                    'cronograma' => $row['cronograma'],
                                    'meta' => $row['meta'],
                                    'pesorelativo' => $row['pesorelativo']
                                );
                    break;
                    case 3:
                         $array[] =  array(
                                    'codaccion' => $row['codaccion'],
                                    'accion' => $row['accion']
                                );
                    break;
                }
        }
        return $array;
    }

    public function listarPersonalNombre($nombre, $pais, $empleado){
        if (!empty($nombre)){ 
            $pais = (isset($pais))?$pais:'';
            $result = $this->db->prepare('CALL sp_ListarPersonalNombre(?,?,?)');
            $result->bindParam(1, $nombre, PDO::PARAM_STR, 4);
            $result->bindParam(2, $pais, PDO::PARAM_STR, 3);
            $result->bindParam(3, $empleado, PDO::PARAM_STR, 15);
            $result->execute();

            //creo el array de los elementos sugeridos
            $arrayElementos = array();
            while($datos = $result->fetch(PDO::FETCH_ASSOC)){
                array_push($arrayElementos, new Listado($datos['personal'], $datos['codpersonal']));
            }

            print_r(json_encode($arrayElementos));
        }   

    }

    public function listarPersonalPaginado($codpais, $muestraInicio, $muestraLongitud, $filtro, $echo){
        $result = $this->db->prepare('CALL sp_ListarPersonalPaginado(?,?,?,?,@cantRegSinFiltar,@cantReg)');
        $result->bindParam(1, $codpais, PDO::PARAM_STR, 3);
        $result->bindParam(2, $muestraInicio, PDO::PARAM_INT);
        $result->bindParam(3, $muestraLongitud, PDO::PARAM_INT);
        $result->bindParam(4, $filtro, PDO::PARAM_STR, 100);
        $result->execute();

       // if ($result->rowCount() > 0) {
            $aColumns = array( 'apellidos', 'nombres', 'cargo', 'oficina', 'codpersonal', 'codpersonal' );
            
            $registros = array();
            $i=1;
          
            while ($row = $result->fetch()) {

                $field = array();
               
                for ($i=0; $i<count($aColumns); $i++){
                    
                    $field[] = $row[$aColumns[$i]];

                }
                $registros[] = $field;

            }
            
            $result->closeCursor();

            $outputArray = $this->db->query("select @cantRegSinFiltar, @cantReg")->fetch(PDO::FETCH_ASSOC);

            $iTotal = $outputArray['@cantReg'];
            $iFilteredTotal = $outputArray['@cantRegSinFiltar'];


            $output = array(
                "sEcho" => intval($echo),
                "iTotalRecords" => intval($iTotal),
                "iTotalDisplayRecords" => intval($iFilteredTotal),
                "aaData" => $registros
            );
            echo json_encode($output);
       // }
    }

    public function cargarAreas($pais){
        if (!empty($pais)){ 
            $pais = (isset($pais))?$pais:'';
            $areas = $this->db->prepare('CALL sp_ObtenerAreas(?)');
            $areas->bindParam(1, $pais, PDO::PARAM_STR, 3);
            $areas->execute();

            $json = array();
            while($datos = $areas->fetch()){
                $json[] = array(
                            'codarea' => $datos['codarea'],
                            'area' => $datos['area']
                          );
            }
             $areas->closeCursor();
            //echo json_encode($json);

            $regionales = $this->db->prepare('CALL sp_ObtenerRegionales(?)');
            $regionales->bindParam(1, $pais, PDO::PARAM_STR, 3);
            $regionales->execute();

            $json2 = array();
            while($datos = $regionales->fetch()){
                $json2[] = array(
                            'codregional' => $datos['codregional'],
                            'regional' => $datos['regional']
                          );

            }

            $nuevojson = array_merge($json, $json2);
            echo json_encode($nuevojson); 

        }   
    }

    public function cargarCargos($area, $pais){
        if (!empty($area) && !empty($pais)){ 
            $cargos = $this->db->prepare('CALL sp_ObtenerCargos(?,?)');
            $cargos->bindParam(1, $area, PDO::PARAM_STR, 9);
            $cargos->bindParam(2, $pais, PDO::PARAM_STR, 3);
            $cargos->execute();

            $json = array();
            while($datos = $cargos->fetch()){
                $json[] = array(
                            'codcargo' => $datos['codcargo'],
                            'cargo' => $datos['cargo']
                          );
            }

            echo json_encode($json);
        }   
    }  

    public function cargarOficinas($regional){
        if (!empty($regional)){ 
            $oficinas = $this->db->prepare('CALL sp_ObtenerOficinas(?)');
            $oficinas->bindParam(1, $regional, PDO::PARAM_STR, 9);
            $oficinas->execute();

            $json = array();
            while($datos = $oficinas->fetch()){
                $json[] = array(
                            'codoficina' => $datos['codoficina'],
                            'oficina' => $datos['oficina']
                          );

            }

            echo json_encode($json);
        }   
    }

    public function cargarMotivos($idmotivocambio){
        if (!empty($idmotivocambio)){ 
            $motivos = $this->db->prepare('CALL sp_ObtenerMotivos(?)');
            $motivos->bindParam(1, $idmotivocambio, PDO::PARAM_INT);
            $motivos->execute();

            $json = array();
            while($datos = $motivos->fetch()){
                $json[] = array(
                            'idmotivo' => $datos['idmotivo'],
                            'motivo' => $datos['motivo']
                          );
            }

            echo json_encode($json);
        }   
    }  
}
?>
