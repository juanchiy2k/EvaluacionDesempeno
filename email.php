<?php
require_once 'php/PHPMailer/class.phpmailer.php';
include_once("funciones.php");

class Email extends PHPMailer{
    //datos de remitente
    private $email = 'info.rrhh@pro-mujer.org';
    private $password = '1q2w3e4r5tPMA';
    private $lang;
    private $text;

    public function __construct(){

        include('internalizacion.php');
        $this->lang = $lang;
        include('locale/textos/text_layout.php');
        $this->text = $textos;
        parent::__construct();
        

        $this->isSMTP(); //indico a la clase que use SMTP
        $this->SMTPAuth = true; //Debo de hacer autenticación SMTP
        //$this->SMTPSecure   = "ssl";
        $this->Host = "smtp.pro-mujer.org"; //indico el puerto que usa Gmail
        $this->Port = 25;//465;
        
        $this->CharSet = 'UTF-8';
        $this->Encoding = 'quoted-printable';

        $this->Username = $this->email;
        $this->Password = $this->password;
        $this->setFrom($this->email, $this->text['perf_manag']);
        //$this->mail->addReplyTo('info.promujer@gmail.com', 'RRHH Pro Mujer');

    }

    public function enviarPlanificacion($data){
    	//indico destinatario
    	$address = $data['email'];
    	$this->addAddress($address, $data['nombres'].' '.$data['apellidos']);

       	$direccionJefeSuperior = $data['emailsuperior'];
       	$this->addAddress($direccionJefeSuperior, $data['nombressuperior'].' '.$data['apellidossuperior']);

       	$direccionJefeFuncional = $data['emailfuncional'];
       	if( isset($direccionJefeFuncional) && $direccionJefeFuncional !== $direccionJefeSuperior)
       	 	$this->AddCC($direccionJefeFuncional, $data['nombresfuncional'].' '.$data['apellidosfuncional']);

    	$this->Subject = $this->text['per_eval_from'].' '.$data['nombres'].' '.$data['apellidos'];

        //defino el cuerpo del mensaje en una variable $body
        //se trae el contenido de un archivo de texto
        //también podríamos hacer $body="contenido...";

    	$plantilla = ($this->lang == 'es_ES')?$plantilla='views/templates/planillaemail.html':'views/templates/planillaemailen.html';

    	$body = file_get_contents($plantilla);


    	$this->msgHTML($body);

        //asigno un archivo adjunto al mensaje
    	$this->addAttachment('docs/'.$this->text['plan'].' '.$data['codpersonal'].'.pdf');


    	if(!$this->send()) {
    		echo "Error al enviar: " . $this->ErrorInfo;
    	}
   
    }
    
    
	public function enviarPassword($data){
        $response = false;

		$destNombre = $this->text['pass_recov'];
        $address = $data['user'];
		$this->addAddress($address, $destNombre);
                
        $this->Subject = $this->text['pass_recov'];

		$body = '
			<div>
				<p>'.$this->text['pass_recov_body'].'</p>
				<p><strong>'.$this->text['date_user'].'</strong></p>
	            <p><strong>'.$this->text['user'].': </strong>'. $data['user'] . '</p>
	            <p><strong>'.$this->text['password'].': </strong>'. $data['pass'] . '</p>
			</div>
		';


		$this->msgHTML($body);

		if($this->send()) {
		  $response = true;
		}

		return $response;
            
            
	}


    public function enviarAprobacion($data){
        //indico destinatario
        $address = $data['email'];
        $this->addAddress($address, $data['nombres'].' '.$data['apellidos']);

        $this->Subject = $this->text['app_per_eval'].' '.$data['nombres'].' '.$data['apellidos'];

        $plantilla = ($this->lang == 'es_ES')?$plantilla='views/templates/aprobacion.html':'views/templates/aprobacionen.html';

        $body = file_get_contents($plantilla);


        $this->msgHTML($body);

        //asigno un archivo adjunto al mensaje
        $this->addAttachment('docs/'.$this->text['plan'].' '.$data['codpersonal'].'.pdf');

        if(!$this->send()) {
            echo "Error al enviar: " . $this->ErrorInfo;
        }

    }

    public function enviarEdicionPersonal($data){
        $this->addAddress($data['email'], $data['nombres'].' '.$data['apellidos']);
        $this->Subject = $this->text['edit_personal'];

        // Incluimos el template engine
        include('includes/templateEngine.inc.php');

        // Cargar extensión twig para poder usar gettext()
        $twig->addExtension(new Twig_Extensions_Extension_I18n());

        $this->msgHTML($twig->render('/templates/editarPersonal.html',array(
            "textos" => $this->text,
            "lenguaje" => substr($lang, 0, 2),
            "datos" => $data
        )));

        if(!$this->send()) {
            echo "Error al enviar: " . $this->ErrorInfo;
        }

    }
}

?>