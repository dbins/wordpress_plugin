<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once 'Database.php'; 

class Base {
	
	protected $tabela;
	protected $colunas;
	protected $valores;
	protected $campo_id;
	protected $valor_id;
	protected $conexao;
	protected $condicoes;
	protected $consulta;
	protected $paginacao;
	protected $posicao_registro;
	protected $ordenacao;
	protected $campo_agregado;
	protected $alias;
	protected $campo_data_cadastro;
	protected $campo_data_atualizacao;
	protected $join_tabelas = array();
	protected $sub_selects = array();
	
	
	public function __construct(){
		$this->conexao = new Database();
	}
	
	
	//Extraindo os valores
	public function getNomeTabela()	{ 
		return $this-> tabela;	 
	}
	public function getNomeCampos()	{ 
		return $this->colunas; }
		
	public function getValorCampos()	{ 
		return $this->valores;
	}
	
	public function getCondicoes()	{ 
		return $this->condicoes;
	}
	
	public function getPaginacao()	{ 
		return $this->paginacao;
	}
	
	public function getPosicaoRegistro()	{ 
		return $this->posicao_registro;
	}
	
	public function getOrdenacao()	{ 
		return $this->ordenacao;
	}
	
	public function getConsulta()	{ 
		return $this->consulta;
	}
	
	public function getCampoID()	{ 
		return $this->campo_ID;
	}
	
	public function getValorID()	{ 
		return $this->valor_ID;
	}
	
	public function getCampoAgregado()	{ 
		return $this->campo_agregado;
	}
	
	public function getJoinTabelas(){
		return $this->join_tabelas;
	}
	
	public function getCampoDataCadastro(){
		return $this->campo_data_cadastro;
	}
	
	public function getCampoDataAtualizacao(){
		return $this->campo_data_atualizacao;
	}
	
	public function getAlias(){
		return $this->alias;
	}
	
	public function getSubSelect(){
		return $this->sub_selects;
	}
	
	public function getQuery(){
		return $this->conexao->getQuery();
	}
	
	public function getExecutar(){
		return $this->conexao->getExecutar();
	}

	//Definindo os valores
	public function setNomeTabela($value)	{ 
		$this-> tabela = $value; 
	}
	public function setNomeCampos($value)	{ 
		$this->colunas = $value;
	}
		
	public function setValorCampos($value)	{ 
		$this->valores = $value;
	}
	
	public function setCondicoes($value)	{ 
		$this->condicoes = $value;
	}
	
	public function setPaginacao($value)	{ 
		$this->paginacao = $value;
	}
	
	public function setPosicaoRegistro($value)	{ 
		$this->posicao_registro =  $value;
	}
	
	public function setOrdenacao($value)	{ 
		$this->ordenacao = $value;
	}
	
	public function setConsulta($value)	{ 
		$this->consulta = $value;
	}
	
	public function setCampoID($value)	{ 
		$this->campo_ID = $value;
	}
	
	public function setValorID($value)
	{ 
		$this->valor_ID = $value;
	}
	
	public function setCampoAgregado($value)	{ 
		$this->campo_agregado =  $value;
	}

	public function setJoinTabelasApagar(){
		$this->join_tabelas =  Array();
	}	
	
	public function setJoinTabelas($value){
		$this->join_tabelas[] = $value;
	}	
	
	public function setCampoDataCadastro($value){
		$this->campo_data_cadastro = $value;
	}
	
	public function setCampoDataAtualizacao($value){
		$this->campo_data_atualizacao = $value;
	}
	
	public function setAlias($value){
		$this->alias = $value;
	}
	
	public function setSubSelect($value){
		$this->sub_selects[] = $value;
	}
	
	public function setSubSelectApagar(){
		$this->sub_selects =  Array();
	}

	public function setExecutar($value){
		return $this->conexao->setExecutar($value);
	}	
	
	//Funcoes de agregacao
	public function contar(){
		return $this->conexao->funcao_agregada("COUNT", $this);
	}
	
	public function max($value){
		return $this->conexao->funcao_agregada("MAX", $this);
	}
	
	public function min($value){
		return $this->conexao->funcao_agregada("MIN", $this);
	}
	
	public function sum($value){
		return $this->conexao->funcao_agregada("SUM", $this);
	}
	
	public function avg($value){
		return $this->conexao->funcao_agregada("AVG", $this);
	}
	
	//Funcoes para facilitar pesquisas simples
	
	public function campo_numero_contem($campo, $value){
		$this->condicoes  = $campo . " IN (" . $value . ")" ;
	}
	
	public function campo_numero_nao_contem($campo, $value){
		$this->condicoes  = $campo . " NOT IN (" . $value . ")" ;
	}
	
	public function campo_conteudo($campo, $comparacao, $value){
		$this->condicoes  = $campo . $comparacao . "'" . $value . "'" ;
	}
	
	public function campo_like($campo, $value){
		$this->condicoes  = $campo . " LIKE '%" . $value . "%'" ;
	}
	
	public function campo_not_like($campo, $value){
		$this->condicoes  = $campo . " NOT LIKE '%" . $value . "%'" ;
	}
	
	public function campo_nulo($campo){
		$this->condicoes  = $campo . " IS NULL";
	}
	
	public function campo_nao_nulo($campo){
		$this->condicoes  = $campo . " IS NOT NULL";
	}
	
	public function campo_numero_intervalo($campo, $value1, $value2){
		$this->condicoes  = $campo . " BETWEEN " . $value1 . " AND " . $value2;
	}
	
	public function campo_numero_comparacao($campo, $comparacao, $value){
		$this->condicoes  = $campo . " " . $comparacao . $value;
	}
	
	public function campo_data_intervalo($campo, $value1, $value2){
		$this->condicoes  = $campo . " BETWEEN '" . $value1 . "' AND '" . $value2 . "'";
	}
	
	public function campo_data_comparacao($campo, $comparacao, $value){
		$this->condicoes  = $campo . " " . $comparacao . "'" . $value . "'";
	}
	
	//Funcoes para o manuseio dos dados
	public function vincularTabelas($tipo,$tabela_destino, $campo1, $campo2){
		$temp_array=array($tipo, $tabela_destino, $campo1, $campo2);
		$this->setJoinTabelas($temp_array);
	}
	
	public function vincularSubQuery($query, $alias){
		$temp_array=array("(" . $query . ")", $alias);
		$this->setSubSelect($temp_array);
	}
	
	public function retornarID (){
		return $this->conexao->retornarID($this);
	}
			
	public function apagarID (){
		return $this->conexao->apagarID($this);
	}
	
	public function Apagar ($where = null){
		return $this->conexao->deletar($this->getNomeTabela(), $where);
	}
	
	public function Atualizar(){
		return $this->conexao->Atualizar($this);
	}
	
	public function AtualizarCondicional(){
		return $this->conexao->AtualizarCondicional($this);
	}

	public function Inserir(){
		return $this->conexao->Inserir($this);
	}
	
	public function Selecionar (){
		return $this->conexao->Selecionar($this);
	}
	
	public function ExecutarSQL($value){
		return $this->conexao->executarSQL($value);
	}
}
?>