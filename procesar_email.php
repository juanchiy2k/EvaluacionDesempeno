<?php
    session_name('responsiveForm');
    session_start();
    include_once('email.php');

    $email = new Email();
    switch($_POST['tarea']){
        
        case 'actualizarDatos':
            $email->enviarEdicionPersonal($_POST['datos']);
        default:
            echo 'No se registró ninguna operación';
    }

?>