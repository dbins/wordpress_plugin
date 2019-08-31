<?php
//Funcoes auxiliares
/* Call this function to create a slug from $string */
function create_slug($string){
 $string = remove_accents($string);
 $string = symbols_to_words($string);
 $string = strtolower($string); // Force lowercase
 $space_chars = array(
  " ", // space
  "…", // ellipsis
  "–", // en dash
  "—", // em dash
  "/", // slash
  "\\", // backslash
  ":", // colon
  ";", // semi-colon
  ".", // period
  "+", // plus sign
  "#", // pound sign
  "~", // tilde
  "_", // underscore
  "|", // pipe
 );
 foreach($space_chars as $char){
  $string = str_replace($char, '-', $string); // Change spaces to dashes
 }
 // Only allow letters, numbers, and dashes
 $string = preg_replace('/([^a-zA-Z0-9\-]+)/', '', $string);
 $string = preg_replace('/-+/', '-', $string); // Clean up extra dashes
 if(substr($string, -1)==='-'){ // Remove - from end
  $string = substr($string, 0, -1);
 }
 if(substr($string, 0, 1)==='-'){ // Remove - from start
  $string = substr($string, 1);
 }
 return $string;
}


function symbols_to_words($output){
 $output = str_replace('@', ' at ', $output);
 $output = str_replace('%', ' percent ', $output);
 $output = str_replace('&', ' and ', $output);
 return $output;
}

//http://www.zdnet.com/article/programmatically-importing-thousands-of-featured-image-post-thumbnails-into-wordpress/
function InserirImagem($arquivo, $post_id){
	$filename = "";
	$wp_filetype = wp_check_filetype($filename, null );
	$mime_type = $wp_filetype[type];
	$attachment = array(
	'post_mime_type' => $wp_filetype['type'],
	'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
	'post_name' => preg_replace('/\.[^.]+$/', '', basename($filename)),
	'post_content' => '',
	'post_parent' => $post_id,
	'post_excerpt' => $thumb_credit,
	'post_status' => 'inherit'
	);
	$attachment_id = wp_insert_attachment($attachment, $filename, $post_id);
	if($attachment_id != 0) {
		$attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);
		wp_update_attachment_metadata($attachment_id, $attach_data);
		update_post_meta($post_id, '_thumbnail_id', $attachment_id);
	}
	
}

$acao = "";
$home = plugin_dir_path(__FILE__);
$url = plugin_dir_url(__FILE__);
if (isset($_POST["acao"])){
	$acao = $_POST["acao"];
} else {
	$acao = $_GET["acao"];
}

if ($acao <> ""){
	require_once 'phpexcel/Classes/PHPExcel.php';
	global $wpdb;
}

if ($acao =="IMPORTAR"){
	$nome_arquivo = "";
	$caminho = "";
	$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE; 
		
	if ($arquivo['name'] != ""){
		$carimbo = time();
		$nome_arquivo=$carimbo."_".$arquivo['name'];
		$diretorio =  $home . "upload_arquivos/"; 
		$caminho = $diretorio.$nome_arquivo;
		move_uploaded_file($arquivo['tmp_name'], $diretorio.$carimbo ."_".$arquivo['name']);
	}
	
	$inputFileName = $caminho;
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($inputFileName);
	$objWorksheet = $objPHPExcel->getActiveSheet();

	$highestRow = $objWorksheet->getHighestRow();
	$highestColumn = $objWorksheet->getHighestColumn();
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	$total_registros = 0;
	//Comecar pela linha 2, a primeira linha tem as colunas
	for ($row = 2; $row <= $highestRow; ++$row) {
		
		$titulo = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
		$conteudo = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
		$imagem =  $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
		$link =  $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
		$cidade = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
		$preco = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
		$vagas = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
		$banheiros = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
		$quartos = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
		
		//Verificar se existe a taxonomia, se nao existir, criar
		$slug_cidade = create_slug($cidade);
		$parent_term = term_exists($cidade, 'localizacao'); // array is returned if taxonomy is given
		$parent_term_id = $parent_term['term_id']; // get numeric term id
		if ($parent_term_id ==0){
		wp_insert_term(
		   $cidade, // the term 
		  'localizacao', // the taxonomy
		  array(
			'description'=> '',
			'slug' => $slug_cidade,
			'parent'=> $parent_term_id
		  )
		);
		}
		

			
		 
		$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='imoveis'", $titulo));
		if ($post){
			
			$post_atual = get_post($post, OBJECT);
			//Alterar
			$post_id = wp_insert_post(array(
				'comment_status'  => 'closed',
				'ping_status'   => 'closed',
				'post_author'   => $author_id,
				'post_title'    => $titulo,
				'post_content'    => $conteudo,
				'post_status'   => 'publish',
				'post_type'   => 'imovel'));
			
			wp_set_object_terms($post_id, $cidade, 'localizacao', true);
			update_post_meta($post_id, 'preco_id', $preco); 
			update_post_meta($post_id, 'vagas_id', $vagas); 
			update_post_meta($post_id, 'banheiros_id', $banheiros); 
			update_post_meta($post_id, 'quartos_id', $quartos); 
			
			InserirImagem($imagem, $post_id);
		} else {
			
			//Inserir
			$author_id = 1;
			$slug = '';
			$post_id = wp_insert_post(array(
				'comment_status'  => 'closed',
				'ping_status'   => 'closed',
				'post_author'   => $author_id,
				'post_name'   => NULL,
				'post_title'    => $titulo,
				'post_content'    => $conteudo,
				'post_status'   => 'publish',
				'post_type'   => 'imovel'));

			wp_set_object_terms($post_id, $cidade, 'localizacao', true);
			add_post_meta($post_id, 'preco_id', $preco, true); 
			add_post_meta($post_id, 'vagas_id', $vagas, true); 
			add_post_meta($post_id, 'banheiros_id', $banheiros, true); 
			add_post_meta($post_id, 'quartos_id', $quartos, true); 
			InserirImagem($imagem, $post_id);
		
		}
	
		
	}
		
}
	
	
if ($acao =="EXPORTAR"){
	$fileName2 = "imoveis_malura";
	
	$inputFileName = $home . "template_malura.xlsx";
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
}
$link_exportar = admin_url( 'admin.php?page=malura_subpage', 'http' );
//http://justintadlock.com/archives/2010/11/17/how-to-load-files-within-wordpress-themes
?>
<div class="wrap">
<script type="text/javascript">
function checkform_upload (form) {
	var continuar = true;
	var mensagem = "Ocorreram os seguintes erros:\n"
	
	var filename = form.arquivo.value;
	var filelength = parseInt(filename.length) - 3;
	var fileext = filename.substring(filelength,filelength + 3);
	fileext = fileext.toUpperCase();
	
	if (fileext == ""){		
		mensagem = mensagem + 'O arquivo deve ser selecionado\n';
		continuar = false;
	} else {
		
	}
	
		
	if (continuar) {
		return true;
	} else {
		alert(mensagem);
		return false;
	}

}

</script>

<div align="center">
<form action="" enctype="multipart/form-data" onSubmit="return checkform_upload(this);" method="POST" class="formee">	
		<h1 align="center">Importar Imóveis</h1>
		 <div>
			<label>Arquivo</label>
			<input name="arquivo" type="file">
		 </div>
		 
		  <div >
			<input type="submit" value=" Enviar Arquivo">
			<input type="hidden" name="acao" value="IMPORTAR" />
		 </div>
	 
	 
	 </form>
	 <p><a href="<?php echo ($link_exportar);?>">Exportar os Imoveis Malura!</a></p>
    </div>

</div>