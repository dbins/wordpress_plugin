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

$acao = "";
//$home = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
$home = plugin_dir_path(__FILE__);
$url = plugin_dir_url(__FILE__);
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
	
	<form action="" method="POST" class="formee">
		  <h1 align="center">Exportar Dados</h1>
		  <div align="center">
			<input type="submit" value="Clique aqui para exportar os dados">
			<input type="hidden" name="acao" value="EXPORTAR" />
		 </div>
	 
	 
	</form>
	 
    </div>

</div>