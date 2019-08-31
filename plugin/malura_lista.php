<?php
if (isset($_SESSION['malura_email'])){
	echo "E-mail postado: " . $_SESSION['malura_email'] . "<br/>";
}
$link_exportar = admin_url( 'admin.php?page=malura_subpage3', 'http' );
$args = array('post_type' => 'imovel');
$loop = new WP_Query($args);
if($loop->have_posts() ) {
?>			
	<h1 align="center">Imóveis cadastrados</h1>
	<table border="1">
		<thead>
			<tr width="800" align="center" cellpadding="2" cellspacing="2">
				<th bgcolor="000000">&nbsp;</th>
				<th bgcolor="000000"><font color="#FFFFFF">Titulo</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Conteudo</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Thumbnail</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Link</font></th> 
				<th bgcolor="000000"><font color="#FFFFFF">Cidade</font></th> 
				<th bgcolor="000000"><font color="#FFFFFF">Preço</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Vagas</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Banheiros</font></th>
				<th bgcolor="000000"><font color="#FFFFFF">Quartos</font></th>
			</tr>
		<thead>
<?php		
while($loop->have_posts()){
	$loop->the_post(); 
	$id = get_the_ID();
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
?>				
	<tr>
		<td><a href="<?php echo ($link_exportar);?>&id=<?php echo $id;?>"><?php echo $id;?></a></td> 
		<td><?php echo $titulo;?></td> 
		<td><?php echo $conteudo;?></td> 
		<td><?php echo $thumbnail;?></td>
		<td><?php echo $link;?></td> 
		<td><?php echo $cidade;?></td>
		<td><?php echo $preco;?></td>
		<td><?php echo $vagas;?></td>
		<td><?php echo $banheiros;?></td>
		<td><?php echo $quartos;?></td>
	</tr>
<?php				
} 
?>
</table>
<?php			
} 
?>