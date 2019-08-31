<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


include_once 'base.php'; 

class BLL_Acoes extends Base_BLL {

	protected $modelo;	
	
	public function __construct(){
		$this->modelo = new Acoes();
	}	
	
	public function Teste() {
		$retorno = 0;
		$StrSQL = "SELECT * FROM customers";
		$resultado = $this->modelo->ExecutarSQL($StrSQL);
		return $resultado;
	}
	
}