<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once 'base.php'; 

class Acoes extends Base{
	
	/**
	 * Construtor
	 */
	public function __construct(){
		$this->conexao = new Database();
		$this->setNomeTabela('acoes');
		$this->setNomeCampos(array('aco_cod','aco_descricao', 'aco_cod_ext', 'aco_status',  'aco_titular', 'aco_data_cad', 'aco_data_atu', 'aco_categoria', 'aco_serie', 'aco_trimestre', 'aco_duracao','aco_exibicao','aco_gabarito', 'aco_ver_resposta', 'aco_participacao',  'aco_email',  'aco_empresa'));
		$this->setCampoID("aco_cod");
		$this->setCampoAgregado("aco_cod");
		$this->setValorID(0);
		$this->setCampoDataCadastro("aco_data_cad");
		$this->setCampoDataAtualizacao("aco_data_atu");
	}	
}
?>