<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once(ABSPATH . 'wp-config.php');

class Conexao{
		private $local;
		private $user;
		private $senha;
		private $msg0;
		private $msg1;
		private $nome_db;
		private $db;
		private $query;
		private $resultado;
		private $erro;
		private $executar;
	
	public function Conexao(){
		$this->local 	=	DB_HOST;
		$this->user  	=	DB_USER;
		$this->senha 	=	DB_PASSWORD;
		$this->msg0  	=	'Conexão falhou, erro: '.mysqli_connect_error();
		$this->msg1  	=	'Não foi possível selecionar o banco de dados!';
		$this->nome_db 	=	DB_NAME;
		$this->executar	=	'SIM';
	}
	
	public function getExecutar()	{ 
		return $this->executar;	 
	}
	
	public function setExecutar($value)	{ 
		$this->executar = $value; 
	}
	
	public function getQuery()	{ 
		return $this->query;	 
	}
		
	public function abrir(){
		$this->db = @mysqli_connect($this->local,$this->user,$this->senha, $this->nome_db) or die($this->msg0);
		mysqli_select_db($this->db, $this->nome_db) or die($this->msg1);
	}
	
	public function fechar(){
		$closed = mysqli_close($this->db);
		$closed = NULL;
	}
	
	public function tabelaExiste($table)
    {
	$this->abrir();
	$tablesInDb = mysqli_query($this->db, 'SHOW TABLES FROM '.$this->nome_db.' LIKE "'.$table.'"');
		
        if($tablesInDb)
        {
            if(mysqli_num_rows($tablesInDb)==1)
            {
                $this->fechar(); 
				return true;
            }
            else
            {
				$this->fechar(); 
                return false;
            }
        }
    }
	
	// Cria a função para query no Banco de Dados
    public function executarSQL($query){
		if ($this->executar == "SIM"){
			$this->abrir();
			$this->query = $query;
			// Conecta e faz a query no MySQL
			if($this->resultado = mysqli_query($this->db, $this->query)){
				$this->fechar();
				return $this->resultado;
			} else{
				// Caso ocorra um erro, exibe uma mensagem com o Erro
				print "Ocorreu um erro ao executar a Query MySQL: <b>$query</b>";
				print "<br><br>";
				print "Erro no MySQL: <b>".mysqli_connect_error()."</b>";
				die();
				$this->fechar();
			}
		} else {
			$this->query = $query;
		}
    }
	
	public function inserirSQL($query){
		$this->abrir();
        $this->query = $query;
		// Conecta e faz a query no MySQL
        if($this->resultado = mysqli_query($this->db, $this->query)){
            $valor = mysqli_insert_id();
			if ($valor == NULL){
				$valor = 0;
			}
			return $valor;
        } else{
			// Caso ocorra um erro, exibe uma mensagem com o Erro
            print "Ocorreu um erro ao executar a Query MySQL: <b>$query</b>";
			print "<br><br>";
			print "Erro no MySQL: <b>".mysqli_connect_error()."</b>";
			die();
            $this->fechar();
        }      
	}
}
?>