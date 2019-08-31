<?php
if (isset($_GET["id"])){
	$imovel_selecionado = get_post($_GET["id"]);
	$id = $imovel_selecionado->ID;
	$titulo = $imovel_selecionado->post_title;
	$thumbnail =  get_the_post_thumbnail($id);
	$conteudo = $imovel_selecionado->post_content;
	$link = get_post_permalink($id); 
	$data = $imovel_selecionado->post_date;
	$imoveis_meta_data = get_post_meta($id); 
	$preco = $imoveis_meta_data['preco_id'][0]; 
	$vagas= $imoveis_meta_data['vagas_id'][0]; 
	$banheiros = $imoveis_meta_data['banheiros_id'][0]; 
	$quartos = $imoveis_meta_data['quartos_id'][0];
	$cidade = "";
	
	 $terms = get_the_terms($id, 'localizacao' );
	 // Loop over each item since it's an array
	 if ( $terms != null ){
		 foreach( $terms as $term ) {
			$cidade =  $term->name;
			unset($term);
		} 
	} 
?>
	<h1 align="center">Detalhe do Imóvel</h1>
	<table border="1" width="600" align="center" cellpadding="2" cellspacing="2">
		<tr><td width="100" bgcolor="#000000"><font color="#FFFFFF">ID</font></td><td><?php echo $id;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Título</font></td><td>	<?php echo $titulo;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Conteúdo</font></td><td><?php echo $conteudo;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Thumbnail</font></td><td><?php echo $thumbnail;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Link</font></td><td><?php echo $link;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Cidade</font></td><td><?php echo $cidade;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Preço</font></td><td><?php echo $preco;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Vagas</font></td><td><?php echo $vagas;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Banheiros</font></td><td><?php echo $banheiros;?></td></tr> 
		<tr><td bgcolor="#000000"><font color="#FFFFFF">Quartos</font></td><td><?php echo $quartos;?></td></tr> 
	</table>
<?php				
} 
?>
