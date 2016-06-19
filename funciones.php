<?php
/*	function fechaFormateada($fecha){
		if(!empty($fecha)){
			$var = explode('-', str_replace('/','-',$fecha));
			return "$var[2]/$var[1]/$var[0]";
		}
	}
*/
    function normalizarFecha($fecha){
        if(!empty($fecha)){
            $var = explode('-', str_replace('/','-',$fecha));
            return "$var[2]-$var[1]-$var[0]";
        }
    }
/*
	function iniciales($nombre) {
        $trozos = explode(' ',elimarEspacioBlancoNombre($nombre));
        $iniciales = '';
        for($i=0;$i<=2;$i++){
            $iniciales .= substr($trozos[$i],0,1);
        }
        return $iniciales;
    }
*/
    function elimarEspacioBlancoNombre($nom){
        $nombre = $nom.split(' ');
        $socia = '';
        for($i = 0; $i < strlen($nombre); $i++) {
            if($nombre[$i] != '')  $socia .= $nombre[$i];
        }
        return trim($socia);
    }


    function sanear_string($string){

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        
        return $string;
    }

    // funciones que estabaleceran si se permite el acceso - functions that will determine if access is allowed
    function permitirAcceso($grupo){
        if ($grupo == 'EDITORES' )
            {
                $permitido = true;
            }else{
                $permitido = false;
            }
        return $permitido;
    }
	
?>