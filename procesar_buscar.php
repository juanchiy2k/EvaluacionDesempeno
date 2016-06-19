<?php
    session_name('responsiveForm');
    session_start();
    include 'buscar.php';
    
    $bus = new Buscar();
    switch ($_POST['tarea']){
        case 'buscarPersonal':
            $bus->buscarPersonal($_POST['codpersonal']);
            break;
        case 'buscarPersonalCargo':
            $bus->buscarPersonalCargo($_SESSION['codpersonal']);
            break;
        case 'buscarPlanificacion':
            $bus->buscarPlanificacion($_POST['codpersonal'], $_POST['periodo']);
            break;
        case 'listarPersonalNombre':
            $bus->listarPersonalNombre($_POST['apellido'], $_POST['pais'], $_POST['empleado']);
            break; 
        case 'listarPersonalPaginado':
            $bus->listarPersonalPaginado($_SESSION['codpais'], $_POST['iDisplayStart'], $_POST['iDisplayLength'], $_POST['sSearch'], $_POST['sEcho']);
            break;
        case 'cargarAreas':
            $bus->cargarAreas($_POST['pais']);
            break;
        case 'cargarCargos':
            $bus->cargarCargos($_POST['area'], $_POST['pais']);
            break;
        case 'cargarOficinas':
            $bus->cargarOficinas($_POST['regional']);
            break;
        case 'cargarMotivos':
            $bus->cargarMotivos($_POST['motivo']);
            break;
       
    }

    /*switch ($_GET['tarea']) {
         case 'listarPersonal':
            $bus = new Buscar();
            $bus->listarPersonal($_GET['apellido'], $_GET['pais']);
            break;
    }*/


  /*  if ($_GET["term"] != ""){
        if ($_GET["tarea"] == "listarPersonal"){
            $bus = new Buscar();
            $bus->listarPersonal($_GET['term'], $_GET['pais']);
            
        }
        
    }*/
?>