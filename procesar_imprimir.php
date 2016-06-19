<?php
    session_name('responsiveForm');
    session_start();
    include_once('imprimir.php');

    $imp = new Imprimir();
    if(isset($_POST) && !empty($_POST)){
        switch($_POST['tarea']){
            case 'imprimirPlanificacion':
                    $imp->imprimirPlanificacion($_POST['codpersonal'], $_POST['periodo'], false, false);
                    break;
            
            default:
                echo 'No se imprimió ninguna operación';
        }
    } 
    else{
        $imp->estadoPlanificacion($_SESSION['codpais']);
    }    
    
	
?>