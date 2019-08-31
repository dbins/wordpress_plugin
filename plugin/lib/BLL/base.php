<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class Base_BLL {

	protected $modelo;
	
	public function Inserir($data){
		$this->modelo->setValorCampos($data);
		$this->modelo->Inserir();
	}	
	
	public function ExcluirID($id){
		$this->modelo->setValorID($id);
		$this->modelo->apagarID();
	}
	
	public function ExcluirCondicional($condicao){
		$this->modelo->Apagar($condicao);
	}
	
	
	
	
	public function Selecionar($condicoes, $ordenacao) {
		$this->modelo->setCondicoes($condicoes);
		$this->modelo->setOrdenacao($ordenacao);
		return $this->modelo->Selecionar();
	}
	
	public function SelecionarTudo() {
		return $this->modelo->Selecionar();
	}

	
	public function SelecionarComPaginacao($condicoes, $posicao_inicial, $total_registros, $ordenacao) {
		$this->modelo->setCondicoes($condicoes);
		$this->modelo->setPaginacao($total_registros);
		$this->modelo->setPosicaoRegistro($posicao_inicial);
		$this->modelo->setOrdenacao($ordenacao);
		return $this->modelo->Selecionar();
	}
	
	public function RetornaItem($id) {
		$this->modelo->setValorID($id);
		return $this->modelo->retornarID();
	}

	public function RetornaCodigo($condicoes) {
		$retorno = 0;
		$this->modelo->setCondicoes($condicoes);
		$tmp_var = $this->modelo->Selecionar();
		while ($tmp_linha=mysqli_fetch_array($tmp_var)) {
			$campo_id = $this->modelo->getCampoID(); 
			$retorno = $tmp_linha[$campo_id];
		}
		return $retorno;
	}	
	
	public function Atualizar($data, $id){
		$this->modelo->setValorCampos($data);
		$this->modelo->setValorID($id);
		$this->modelo->Atualizar();
	}
	
	public function AtualizarCondicional($data, $condicao){
		$this->modelo->setValorCampos($data);
		$this->modelo->setCondicoes($condicao);
		$this->modelo->AtualizarCondicional();
	}
	
	public function Contar($condicao) {
		if ($condicao != ""){
		$this->modelo->setCondicoes($condicao);
		}
		return $this->modelo->Contar();
	}
	
}