<?php

class conexion extends PDO {
    private $host;
    private $usuario;
    private $password;
    private $dbname;
    private $errorDbConexion;
    
    public function __construct()
    {	
        $this->host = "mysql:host=localhost";
        $this->usuario = "root";
        $this->password = "1q2w3e4r5tPMA";
        $this->dbname = "dbname=rrhh";
        $params = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
        $this->errorDbConexion = false;
           
        try {
            parent::__construct($this->host.";".$this->dbname,$this->usuario,$this->password, $params);
        } catch (PDOException $e) {
                print "<p>Error: No puede conectarse con la base de datos.</p>\n";
                $this->errorDbConexion = true;
        }
    }
	
	public function __destruct(){
	}
}

?>
