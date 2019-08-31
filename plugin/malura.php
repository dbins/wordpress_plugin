<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
    /*
    Plugin Name: MALURA Importador de Imoveis
    Plugin URI: http://www.alura.com.br
    Description: Plugin para importar e exportar imoveis
    Author: C. BINS
    Version: 1.0
    Author URI: http://www.dbins.com.br
    */
	
	//https://code.tutsplus.com/tutorials/create-a-custom-wordpress-plugin-from-scratch--net-2668
	
	function malura_admin() {
		$acao = "";
		include('malura_admin.php');
	}
	
	function malura_subpage() {
		
		include('malura_exportar.php');
	}
	
	function malura_lista() {
		
		include('malura_lista.php');
	}
	
	function malura_detalhe() {
		
		include('malura_detalhe.php');
	}
	
	function malura_visitante() {
		
		include('malura_visitante.php');
	}
	
	function malura_listagem_clientes() {
		include('malura_listagem_clientes.php');
	}
	
	function malura_teste_importar($atts = [], $content = null, $tag = ''){
		malura_visitante();
	}
	
	//[malura_imoveis localizacao="Rio de Janeiro"]
	function malura_tabela($atts = [], $content = null, $tag = ''){
		$retorno = "";
		// normalize attribute keys, lowercase
		$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
		//print_r($atts);
		extract(shortcode_atts(array("localizacao" => ""), $atts, $tag));
		global $wpdb;
		$localizacao = $atts[1];
		$localizacao = str_replace('class="attribute-value">localizacao="','',$localizacao);
		$localizacao = str_replace('"','',$localizacao);
		echo ($localizacao);
		if ($localizacao != ""){
			$taxQuery = array(
				array(
					'taxonomy' => 'localizacao',
					'field' => 'slug',
					'terms' =>$localizacao
					)
			);
		}
		$args = array('post_type' => 'imovel');
		if (isset($taxQuery)){
			$args["tax_query"] = $taxQuery;
		}
		print_r($args);
		$loop = new WP_Query($args);
		
		if($loop->have_posts() ) {
			$retorno = "<table border=1>";	
			$retorno .= "<tr>";
			$retorno .= "<td>Titulo</td>"; 
			$retorno .= "<td>Conteudo</td>"; 
			$retorno .= "<td>Thumbnail</td>"; 
			$retorno .= "<td>Link</td><td>"; 
			$retorno .= "<td>Cidade</td>"; 
			$retorno .= "<td>Preço</td>"; 
			$retorno .= "<td>Vagas</td>"; 
			$retorno .= "<td>Banheiros</td>"; 
			$retorno .= "<td>Quartos</td>"; 
			$retorno .= "</tr>";
			while($loop->have_posts() ) {
				$loop->the_post(); 
				$titulo = get_the_title();
				$thumbnail =  get_the_post_thumbnail_url();
				$conteudo = get_the_content();
				$link = get_the_permalink(); 
				$data = get_the_date();
				$imoveis_meta_data = get_post_meta(get_the_ID()); 
				$preco = $imoveis_meta_data['preco_id'][0]; 
				$vagas= $imoveis_meta_data['vagas_id'][0]; 
				$banheiros = $imoveis_meta_data['banheiros_id'][0]; 
				$quartos = $imoveis_meta_data['quartos_id'][0];
				$cidade = "";
				
				 $terms = get_the_terms(get_the_ID(), 'localizacao' );
				 // Loop over each item since it's an array
				 if ( $terms != null ){
					 foreach( $terms as $term ) {
						$cidade =  $term->name;
						unset($term);
					} 
				} 
				
				$retorno .= "<tr>";
				$retorno .= "<td>" . $titulo . "</td>"; 
				$retorno .= "<td>" . $conteudo . "</td>"; 
				$retorno .= "<td>" . $thumbnail . "</td>"; 
				$retorno .= "<td>" . $link . "</td>"; 
				$retorno .= "<td>" . $cidade . "</td>"; 
				$retorno .= "<td>" . $preco . "</td>"; 
				$retorno .= "<td>" . $vagas . "</td>"; 
				$retorno .= "<td>" . $banheiros . "</td>"; 
				$retorno .= "<td>" . $quartos . "</td>"; 
				$retorno .= "</tr>";
				
			} 
			$retorno .= "</table>";	
		}
		return $retorno;
		 // run shortcode parser recursively
		//$content = do_shortcode($content);
	 
		// always return
		//return $content;
	}
	
	function malura_download(){
		$home = plugin_dir_path(__FILE__);
		$url = plugin_dir_url(__FILE__);
		$acao = "";
		if (isset($_POST["acao"])){
			$acao = $_POST["acao"];
		} else {
			$acao = $_GET["acao"];
		}
		if ($acao <> ""){
			require_once 'phpexcel/Classes/PHPExcel.php';
			global $wpdb;
			
		}
		if ($acao =="EXPORTAR"){
			$fileName2 = "imoveis_malura";
			$inputFileName = $home . "/templates/template_malura.xlsx";
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($inputFileName);
			$objPHPExcel->setActiveSheetIndex(0); 

			$rowCount = 1; 
			//Comecar pela linha 2, a primeira linha tem as colunas
			//http://www.billerickson.net/code/wp_query-arguments/
			$args = array('post_type' => 'imovel');
			$loop = new WP_Query($args);
			
			if($loop->have_posts() ) { 
				while($loop->have_posts() ) {
					$loop->the_post(); 
					$titulo = get_the_title();
					$thumbnail =  get_the_post_thumbnail_url();
					$conteudo = get_the_content();
					$link = get_the_permalink(); 
					$data = get_the_date();
					$imoveis_meta_data = get_post_meta(get_the_ID()); 
					$preco = $imoveis_meta_data['preco_id'][0]; 
					$vagas= $imoveis_meta_data['vagas_id'][0]; 
					$banheiros = $imoveis_meta_data['banheiros_id'][0]; 
					$quartos = $imoveis_meta_data['quartos_id'][0];
					$cidade = "";
					
					 $terms = get_the_terms(get_the_ID(), 'localizacao' );
					 // Loop over each item since it's an array
					 if ( $terms != null ){
						 foreach( $terms as $term ) {
							$cidade =  $term->name;
							unset($term);
						} 
					} 
					
					$rowCount++;
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $titulo); 
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $conteudo); 
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $thumbnail); 
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $link); 
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $cidade); 
					$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $preco); 
					$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $vagas); 
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $banheiros); 
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $quartos); 
					
				} 
			}		
			
			
			// Write the file
			 header("Pragma: public");
			 header("Expires: 0");
			 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			 header("Content-Type: application/force-download");
			 header("Content-Type: application/octet-stream");
			 header("Content-Type: application/download");;
			 header("Content-Disposition: attachment;filename=$fileName2.xlsx");
			 header("Content-Transfer-Encoding: binary ");

			 $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
			 $objWriter->setOffice2003Compatibility(true);
			 $objWriter->save('php://output');	
			 exit();
		}
	}
	
	//http://clivern.com/adding-menus-and-submenus-for-wordpress-plugins/
	
	function malura_admin_actions() {
		 //Adiciona dentro do menu de configuracoes
		 //add_options_page("MALURA Importador de Imoveis", "MALURA Importador de Imoveis", 10, "MALURA Importador de Imoveis", "malura_admin");
		 add_menu_page( 'MALURA Importador de Imoveis', 'MALURA Importador de Imoveis', 'manage_options', 'malura', 'malura_admin' );
		 add_submenu_page('malura', 'Plugin Malura', 'Exportar Dados', 10, 'malura_subpage', 'malura_subpage');
		 add_submenu_page('malura', 'Plugin Malura', 'Listar', 10, 'malura_subpage2', 'malura_lista');
		 //Para ocultar
		 add_submenu_page(null, 'Plugin Malura', 'Detalhe', 10, 'malura_subpage3', 'malura_detalhe');
		 add_submenu_page('malura', 'Plugin Malura', 'Clientes', 10, 'malura_subpage4', 'malura_listagem_clientes');
	}
 
	add_action('admin_menu', 'malura_admin_actions');
	add_action('admin_init','malura_download');	
	add_filter('widget_text', 'do_shortcode');
	add_shortcode('malura_imoveis', 'malura_tabela');
	add_shortcode('malura_teste', 'malura_teste_importar');
	//https://medium.com/@JhonatanChristian/como-criar-plugins-para-wordpress-parte-2-85625f910086
	//http://objota.com.br/web/como-criar-plugins-para-wordpress-parte-1.html
	
	function create_plugin_database_table() {
		global $table_prefix, $wpdb;

		$tblname = 'pin';
		$wp_track_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it

		if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
		{

			$sql = "CREATE TABLE `". $wp_track_table . "` ( ";
			$sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
			$sql .= "  `pincode`  int(128)   NOT NULL, ";
			$sql .= "  PRIMARY KEY `order_id` (`id`) "; 
			$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}

	register_activation_hook( __FILE__, 'create_plugin_database_table' );
	
	add_action('init', 'myStartSession', 1);
	add_action('wp_logout', 'myEndSession');
	add_action('wp_login', 'myEndSession');

	function myStartSession() {
		if(!session_id()) {
			session_start();
		}
	}

	function myEndSession() {
		session_destroy ();
	}
	
?>