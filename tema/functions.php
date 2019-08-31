<?php

add_theme_support('post-thumbnails');

function cadastrando_post_type_imoveis() {

    $nomeSingular = 'Imóvel';
    $nomePlural = 'Imóveis';
    $description = 'Imóveis da Imobiliária Malura';

    $labels = array(
        'name' => $nomePlural,
        'name_singular' => $nomeSingular,
        'add_new_item' => 'Adicionar novo ' . $nomeSingular,
        'edit_item' => 'Editar ' . $nomeSingular
    );

    $supports = array(
        'title',
        'editor',
        'thumbnail'
    );

    $args = array(
        'labels' => $labels,
        'description' => $descricao,
        'public' => true,
        'menu_icon' => 'dashicons-admin-home',
        'supports' => $supports
    );


    register_post_type( 'imovel', $args);    
}



add_action('init', 'cadastrando_post_type_imoveis');

function cadastrando_post_type_contato() {

    $nomeSingular = 'Contato';
    $nomePlural = 'Contatos';
    $description = 'Contatos da Imobiliária Malura';

    $labels = array(
        'name' => $nomePlural,
        'name_singular' => $nomeSingular,
        'add_new_item' => 'Adicionar novo ' . $nomeSingular,
        'edit_item' => 'Editar ' . $nomeSingular
    );

    $supports = array(
        'title',
        'editor',
        'thumbnail'
    );

    $args = array(
        'labels' => $labels,
        'description' => $descricao,
        'public' => true,
        'menu_icon' => 'dashicons-admin-home',
        'supports' => $supports
    );


    register_post_type( 'contato', $args);    
}
add_action('init', 'cadastrando_post_type_contato');

function registrar_menu_navegacao() { 
    register_nav_menu('header-menu', 'main_menu');
}

function get_titulo() {
    bloginfo ('name'); 
    if( !is_home() ) echo ' | ';
    the_title();
}


function criando_taxonomia_localizacao() {
    $singular = 'Localização';
    $plural = 'Localizações';

    $labels = array(
        'name' => $plural,
        'singular_name' => $singular,
        'view_item' => 'Ver ' . $singular,
        'edit_item' => 'Editar ' . $singular,
        'new_item' => 'Novo ' . $singular,
        'add_new_item' => 'Adicionar novo ' . $singular
        );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'hierarchical' => true
        );

    register_taxonomy('localizacao', 'imovel', $args);
}

add_action( 'init' , 'criando_taxonomia_localizacao' );
add_action('init', 'registrar_menu_navegacao');

function preenche_conteudo_informacoes_imovel($post) {  
$imoveis_meta_data = get_post_meta( $post-> ID );
//print_r($imoveis_meta_data);
require "assets/custom/metabox_imovel.php";
}


function preenche_conteudo_informacoes_contato($post) {  
$imoveis_meta_data = get_post_meta( $post-> ID );

$nome = "";
if (isset($imoveis_meta_data['nome'][0])){
$imoveis_meta_data['nome'][0];
}
?>


    <style>
        .maluras-metabox {
            display: flex;
            justify-content: space-between;
        }

        .maluras-metabox-item {
            flex-basis: 30%;

        }

        .maluras-metabox-item label {
            font-weight: 700;
            display: block;
            margin: .5rem 0;

        }

        .input-addon-wrapper {
            height: 30px;
            display: flex;
            align-items: center;
        }

        .input-addon {
            display: block;
            border: 1px solid #CCC;
            border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            height: 100%;
            width: 30px;
            text-align: center;
            line-height: 30px;
            box-sizing: border-box;
            background-color: #888;
            color: #FFF;
        }

        .maluras-metabox-input {
            height: 100%;
            border: 1px solid #CCC;
            border-left: none;
            margin: 0;
        }

    </style>
    <div class="maluras-metabox">
        <div class="maluras-metabox-item">
            <label for="maluras-preco-input">Nome:</label>
            <div class="input-addon-wrapper">
           
                <input id="maluras-preco-input" class="maluras-metabox-input" type="text" name="nome"
                value="<?= $nome; ?>">
            </div>
        </div>

        <div class="maluras-metabox-item">
            <label for="maluras-vagas-input">Email:</label>
            <input id="maluras-vagas-input" class="maluras-metabox-input" type="text" name="email"
            value="<?= $imoveis_meta_data['email'][0]; ?>">
        </div>

        <div class="maluras-metabox-item">
            <label for="maluras-banheiros-input">Imovel:</label>
            <input id="maluras-banheiros-input" class="maluras-metabox-input" type="text" name="imovel"
            value="<?= $imoveis_meta_data['imovel'][0]; ?>">
        </div>

    </div>
<?php

}


function registra_meta_boxes() {

    add_meta_box(
        'informacoes-imoveis',
        'Informacoes do Imóvel',
        'preenche_conteudo_informacoes_imovel',
        'imovel',
        'normal',
        'high'
    );
}

function registra_meta_boxes_contato() {

    add_meta_box(
        'informacoes-contato',
        'Informacoes do Contato',
        'preenche_conteudo_informacoes_contato',
        'contato',
        'normal',
        'high'
    );
}

//https://developer.wordpress.org/reference/functions/add_meta_box/
add_action('add_meta_boxes', 'registra_meta_boxes');
add_action('add_meta_boxes', 'registra_meta_boxes_contato');

function salvar_meta_info_imoveis( $post_id ) {
    if( isset($_POST['preco_id']) ) {
        update_post_meta( $post_id, 'preco_id', $_POST['preco_id']);
    }
    if( isset($_POST['vagas_id']) ) {
        update_post_meta( $post_id, 'vagas_id', $_POST['vagas_id']);
    }
    if( isset($_POST['banheiros_id']) ) {
        update_post_meta( $post_id, 'banheiros_id', $_POST['banheiros_id']);
    }
    if( isset($_POST['quartos_id']) ) {
        update_post_meta( $post_id, 'quartos_id', $_POST['quartos_id']);
    }
}

add_action('save_post', 'salvar_meta_info_imoveis');

function salvar_meta_info_contato( $post_id ) {
    if( isset($_POST['nome']) ) {
        update_post_meta( $post_id, 'nome', $_POST['nome']);
    }
    if( isset($_POST['email']) ) {
        update_post_meta( $post_id, 'email', $_POST['email']);
    }
    if( isset($_POST['imovel']) ) {
        update_post_meta( $post_id, 'imovel', $_POST['imovel']);
    }
  
}

add_action('save_post', 'salvar_meta_info_contato');


function enviar_e_checar_email($nome, $email, $mensagem) {
  return wp_mail( 'bins.br@gmail.com', 'Email Malura', 'Nome: ' . $nome . "\n" . $mensagem  );
}

function paginar_resultados($query) {

  	//Se tiver apenas 1 pagina de resultados, nao imprimir
	if ($query->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $query->max_num_pages,
		'current'  => $paged,
		'mid_size' => 3,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Anterior' ),
		'next_text' => __( 'Proximo &rarr;'),
		'type'      => 'list',
	) );

	$paginador = "";
	if ( $links ) :

	
	$paginador .= '<nav class="navigation paging-navigation" role="navigation">';
	
	$paginador .= $links;
	$paginador .= '<h1 class="screen-reader-text">Pagina ' . $paged . ' de ' . $query->max_num_pages . '</h1>';
	$paginador .= '</nav>';
	
	endif;
	
	return $paginador;
}


flush_rewrite_rules();
?>
