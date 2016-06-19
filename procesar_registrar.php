<?php
    session_name('responsiveForm');
    session_start();
    include('registrar.php');

    $reg = new Registrar();
    switch($_POST['tarea']){
        case 'registrarPlanificacion':
            $reg->registrarPlanificacion($_POST['pais'], $_POST['codpersonal'], $_POST['nombre'], $_POST['periodo'], $_POST['objetivos'], $_POST['actividades'], $_POST['cronogramas'], $_POST['metas'], $_POST['pesos'], $_POST['acciones'], $_POST['actitud'], $_POST['compromiso'], $_POST['enviarmail']);
            break;
        case 'editarPlanificacion':
            $reg->editarPlanificacion($_POST['pais'], $_POST['codpersonal'], $_POST['nombre'], $_POST['periodo'], $_POST['codplanificacion'], $_POST['codobjetivos'], $_POST['objetivos'], $_POST['actividades'], $_POST['cronogramas'], $_POST['metas'], $_POST['pesos'], $_POST['codacciones'], $_POST['acciones'], $_POST['codmejora'], $_POST['actitud'], $_POST['compromiso'], $_POST['imprimir'], $_POST['enviarmail']);
           break;
        case 'aprobarPlanificacion':
            $reg->aprobarPlanificacion($_POST['codplanificacion'], $_POST['codpersonal'], $_POST['periodo']);
            break;
        case 'registrarPersonal':
            $reg->registrarPersonal($_POST['txtApellido'], $_POST['txtNombre'], $_POST['sltTipoDni'], $_POST['txtDni'], $_POST['txtFechaNacimiento'], $_POST['txtFechaIngreso'], $_POST['txtFechaCargo'], $_POST['txtEmail'], $_POST['hdnCodSuperior'], $_POST['hdnCodFuncional'], $_POST['sltPais'], $_POST['sltArea'], $_POST['sltOficina'], $_POST['sltCargo'], $_POST['sltGrupo']);
            break;
        case 'editarPersonal':
            if (empty($_POST['txtSuperiorInmediatoApellido'])) {
                $codSuperior = '';
            }else {
                $codSuperior = $_POST['hdnCodSuperior'];
            }
            if (empty($_POST['txtSuperiorFuncionalApellido'])){
                $codFuncional = '';
            }else{
                $codFuncional = $_POST['hdnCodFuncional'];
            }
            $reg->editarPersonal($_POST['hdnCodPersonal'], $_POST['txtApellido'], $_POST['txtNombre'], $_POST['sltTipoDni'], $_POST['txtDni'], $_POST['txtFechaNacimiento'], $_POST['txtFechaIngreso'], $_POST['txtFechaCargo'], $_POST['txtEmail'], $codSuperior, $codFuncional, $_POST['sltPais'], $_POST['sltArea'], $_POST['sltOficina'], $_POST['sltCargo'], $_POST['sltGrupo'], $_POST['sltMotivo']);
            break;
        case 'registrarBaja':
            $reg->registrarBaja($_POST['hdnCodPersonal'], $_POST['txtFechaBaja'], $_POST['sltMotivo']);
            break;
        default:
            echo 'No se registró ninguna operación';
    }

?>