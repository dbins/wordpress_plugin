<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once 'Conexao.php'; 
class Database{
	
	public $database;

	public function Database(){
		$this->database	=new Conexao();
	}
	
	public function getExecutar()	{ 
		return $this->database->getExecutar();	 
	}
	
	public function getQuery()	{ 
		return $this->database->getQuery();	 
	}
	
	public function setExecutar($value)	{ 
		$this->database->setExecutar($value); 
	}

	public function funcao_agregada($tipo, $objeto)
	{
		if($this->database->tabelaExiste($objeto->getNomeTabela())){
			if ($objeto->getJoinTabelas() == null) {
				$StrSQL = "SELECT ". $tipo . "(". $objeto->getCampoAgregado() . ") FROM " . $objeto->getNomeTabela();
			} else {	
				$tabelas_selecionadas = $this->MontarJoin($objeto->getNomeTabela(), $objeto->getJoinTabelas());
				$StrSQL = "SELECT ". $tipo . "(". $objeto->getCampoAgregado() . ") FROM " . $tabelas_selecionadas;
			}
			
			if ($objeto->getCondicoes() != null) {
				$StrSQL .= " WHERE " . $objeto->getCondicoes();
			}
			
			
			$query = $this->database->executarSQL($StrSQL);
			if ($objeto->getExecutar() == "SIM"){
				$linhas_tabela = mysqli_fetch_row($query);
				return $linhas_tabela[0];
			} else {
				return 0;
			}
		} else {
			return -1;	
		}
	}
	
	public function retornarID($objeto)
	{
		if($this->database->tabelaExiste($objeto->getNomeTabela())){
			$StrSQL = "SELECT * FROM " . $objeto->getNomeTabela() . " WHERE " . $objeto->getCampoID() . "=" . $objeto->getValorID();
			//echo($StrSQL);
			$resultado = $this->database->executarSQL($StrSQL);
			return $resultado;
		} else {
			return false;	
		}
	}
	
	public function apagarID($objeto)
	{
		if($this->database->tabelaExiste($objeto->getNomeTabela())){
			$StrSQL = "DELETE FROM " . $objeto->getNomeTabela() . " WHERE " . $objeto->getCampoID() . "=" . $objeto->getValorID();
			//echo($StrSQL);
			$resultado = $this->database->executarSQL($StrSQL);
			return $resultado;
		} else {
			return false;	
		}
	}
	
	public function Inserir($objeto){ 
		$campos  = $objeto->getNomeCampos();
		$valores = $objeto->getValorCampos();
		$string_campos =  '';
		$string_valores =  '';
		//Retirar do insert o nome do campo chave
		//gerando os campos que serao inseridos
		for ($i=0;$i <= count($campos)-1;$i++){ 
			if (array_key_exists ($campos[$i], $valores)) {
				if ($campos[$i] != $objeto->getCampoID()) {
					if ($campos[$i] != $objeto->getCampoDataAtualizacao()) {
						if ($campos[$i] != $objeto->getCampoDataCadastro()) {
							$string_campos .= $campos[$i];
						}
					}	
				}
				if ($i < count($campos)-1){ $string_campos .= ','; }
			}
			
		}
		
		if (substr($string_campos,0,1)==","){
			$string_campos = substr($string_campos,1,strlen($string_campos)-1);
		}
		
		
		if (substr($string_campos,strlen($string_campos)-1,1)==","){
			$string_campos = substr($string_campos,0,strlen($string_campos)-1);
		}
		
		//Adcionando o campo data padrao
		$string_campos .= ',' . $objeto->getCampoDataCadastro();
		
		//Gerando os valores que serao inseridos
		for ($i=0;$i <= count($campos)-1;$i++){ 
			//Inserindo apenas os campos postados que existem no array de campos da tabela
			if (array_key_exists ($campos[$i], $valores)) {
				//Excluindo o campo chave da tabela na montagem do SET, somente usar ele no final
				if ($campos[$i] != $objeto->getCampoID()) {
					if ($campos[$i] != $objeto->getCampoDataAtualizacao()) {
						if ($campos[$i] != $objeto->getCampoDataCadastro()) {
							$string_valores .= "'".$valores[$campos[$i]] . "'";
						}
					}
				}
				if ($i < count($campos)-1){ $string_valores .= ','; }
			}
		}
		
		if (substr($string_valores,0,1)==","){
			$string_valores = substr($string_valores,1,strlen($string_valores)-1);
		}
		
		
		if (substr($string_valores,strlen($string_valores)-1,1)==","){
			$string_valores = substr($string_valores,0,strlen($string_valores)-1);
		}
		
		//Adcionando o campo data padrao
		$string_valores .= ', CURRENT_TIMESTAMP';
		
		
		$StrSQL = "INSERT INTO ".$objeto->getNomeTabela()."(". $string_campos .") VALUES ( ". $string_valores .")";
		//echo($StrSQL);
		$valor = $this->database->inserirSQL($StrSQL);
		unset($objeto);
		return $valor;
	}
	
	public function MontarJoin($tabela_inicial, $array_tabelas) {
		$string_join = $tabela_inicial . " ";
		
		for ($i=0;$i <= count($array_tabelas)-1;$i++){ 
			$temp_array = $array_tabelas[$i];
			//Tipo de join + tabela +  campo vinculado + campo_vinculado
			$string_join .= $temp_array[0] . " " . $temp_array[1] . " ON "  . $temp_array[2] . " = " . $temp_array[3] . " ";
		}
		return $string_join;
	
	}
	
	public function MontarSubQuery($array_tabelas) {
		$string_join = ",";
		$continuar = 0;
		for ($i=0;$i <= count($array_tabelas)-1;$i++){ 
			$continuar = 1;
			$temp_array = $array_tabelas[$i];
			//subquery + alias do campo
			$string_join .= $temp_array[0] . " as " . $temp_array[1];
			if ($i < count($array_tabelas)-1){ $string_join .= ','; }
		}
		return $string_join;
	
	}
	
	public function Selecionar($objeto){
		$StrSQL = "";
		$string_campos="";
		$tabelas_selecionadas = "";
		
		//Select simples quando nao tem joins	
		if ($objeto->getJoinTabelas() == null) {
			$campos  = $objeto->getNomeCampos();
			for ($i=0;$i <= count($campos)-1;$i++){ 
				$string_campos .= $campos[$i];
				if ($i < count($campos)-1){ $string_campos .= ','; }
			}
			if ($objeto->getSubSelect() != null) {
				$selects_adicionais = $this->MontarSubQuery($objeto->getSubSelect());
				$string_campos .= $selects_adicionais;
			}
			
			
			$StrSQL = "SELECT " .  $string_campos . " FROM " . $objeto->getNomeTabela();
			if ($objeto->getAlias() != null) {
				$StrSQL .= " as " . $objeto->getAlias();
			}
		} else {
			// No caso de joins, retornar todos os campos das tabelas vinculadas
			$campos  = $objeto->getNomeCampos();
			if ($campos==""){
				$string_campos=" * ";
			} else {
				if (is_array($campos)){
					$string_campos=" * ";
				} else {
					$string_campos=$campos;
				}
			}
			if ($objeto->getSubSelect() != null) {
				$selects_adicionais = $this->MontarSubQuery($objeto->getSubSelect());
				$string_campos .= $selects_adicionais;
			}
			
			if ($objeto->getAlias() != null) {
				$tabela_inicial = $objeto->getNomeTabela() . " as " . $objeto->getAlias();
			} else {
				$tabela_inicial = $objeto->getNomeTabela();
			}
			
			$tabelas_selecionadas = $this->MontarJoin($tabela_inicial, $objeto->getJoinTabelas());
			$StrSQL = "SELECT " .  $string_campos . " FROM " . $tabelas_selecionadas;
		
		}
		
		
		if ($objeto->getCondicoes() != null) {
			$StrSQL .= " WHERE " . $objeto->getCondicoes();
		}
		if ($objeto->getOrdenacao() != null) {
			$StrSQL .= " ORDER BY " . $objeto->getOrdenacao();
		}
		
		if ($objeto->getPaginacao() != null) {
			//if ($objeto-> getPosicaoRegistro() != null) {
				$StrSQL .= " LIMIT " . $objeto->getPosicaoRegistro() . "," . $objeto->getPaginacao();
			//}
		}
		//echo($StrSQL);
		$query = $this->database->executarSQL($StrSQL);
		return $query;
	}

	public function Atualizar($objeto)
		{
		$campos  = $objeto->getNomeCampos();
		$valores = $objeto->getValorCampos();
		$StrSQL2 = "";
		
		$StrSQL = 'UPDATE ' .$objeto->getNomeTabela().' SET ';
		for ($i=0;$i <= count($campos)-1;$i++){ 
			//Inserindo apenas os campos postados que existem no array de campos da tabela
			if (array_key_exists ($campos[$i], $valores)) {
				//Excluindo o campo chave da tabela na montagem do SET, somente usar ele no final
				if ($campos[$i] != $objeto->getCampoID()) {
					$StrSQL2 .= $campos[$i]." = '".$valores[$campos[$i]] . "'";
				}
				if ($i < count($campos)-1){ $StrSQL2 .= ','; }
			}
		}
		
		if (substr($StrSQL2,0,1)==","){
			$StrSQL2 = substr($StrSQL2,1,strlen($StrSQL2)-1);
		}
		
		
		if (substr($StrSQL2,strlen($StrSQL2)-1,1)==","){
			$StrSQL2 = substr($StrSQL2,0,strlen($StrSQL2)-1);
		}
		
		//Adcionando o campo data padrao
		$StrSQL2 .= ',' . $objeto->getCampoDataAtualizacao() . '= CURRENT_TIMESTAMP';
		
		// concatena a SQL com a cláusula where.
		$StrSQL .= $StrSQL2 . ' WHERE ' . $objeto->getCampoID() .' = '. $objeto->getValorID();
		//echo($StrSQL);
		$query = $this->database->executarSQL($StrSQL);
		return $query;
	}
	
	public function AtualizarCondicional($objeto)
		{
		$campos  = $objeto->getNomeCampos();
		$valores = $objeto->getValorCampos();
		$StrSQL2 = "";
		
		$StrSQL = 'UPDATE ' .$objeto->getNomeTabela().' SET ';
		for ($i=0;$i <= count($campos)-1;$i++){ 
			//Inserindo apenas os campos postados que existem no array de campos da tabela
			if (array_key_exists ($campos[$i], $valores)) {
				//Excluindo o campo chave da tabela na montagem do SET, somente usar ele no final
				if ($campos[$i] != $objeto->getCampoID()) {
					$StrSQL2 .= $campos[$i]." = '".$valores[$campos[$i]] . "'";
				}
				if ($i < count($campos)-1){ $StrSQL2 .= ','; }
			}
		}
		
		if (substr($StrSQL2,0,1)==","){
			$StrSQL2 = substr($StrSQL2,1,strlen($StrSQL2)-1);
		}
		
		
		if (substr($StrSQL2,strlen($StrSQL2)-1,1)==","){
			$StrSQL2 = substr($StrSQL2,0,strlen($StrSQL2)-1);
		}
		
		// concatena a SQL com a cláusula where.
		$StrSQL .= $StrSQL2 . ' WHERE ' . $objeto->getCondicoes();
		//echo($StrSQL);
		$query = $this->database->executarSQL($StrSQL);
		return $query;
	}
	
    public function deletar($table, $where = null)
    {
        if($this->database->tabelaExiste($table))
        {
            if($where == null)
            {
                $StrSQL = 'DELETE '.$table;
            }
            else
            {
                $StrSQL = 'DELETE FROM '.$table.' WHERE '.$where;
            }
			$del = $this->database->executarSQL($StrSQL);

            if($del)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
	
	   public function executarSQL($query){
			$query = $this->database->executarSQL($query);
			return $query;
	   }
}
?>