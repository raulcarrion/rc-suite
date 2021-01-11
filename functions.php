<?php

/* 

        GENERAL

*/

// Para poder usar las fontawesome
add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );

function enqueue_load_fa() {
    wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
}

// Quitar el IVA al rol "Cliente europeo sin impuesto"

function prefix_exclude_tax_by_role() 
{
    if( function_exists( 'WC' ) ) 
    {
        if( isset( WC()->customer ) ) 
        {
            $role = WC()->customer->get_role();
            
            if( $role == 'clientes_europeos_sin_iva' ) 
                WC()->customer->set_is_vat_exempt( true );
            else 
                WC()->customer->set_is_vat_exempt( false );
        }
    }
}

add_action( 'wp', 'prefix_exclude_tax_by_role' );

/* 

        PAGINA DE LA CATEGORIA

*/

//Cambiar de sitio la descripcion de la categoria
remove_action('woocommerce_archive_description','woocommerce_taxonomy_archive_description');
remove_action('woocommerce_archive_description','woocommerce_product_archive_description');

add_action('woocommerce_after_main_content','woocommerce_taxonomy_archive_description',10);
add_action('woocommerce_after_main_content','woocommerce_product_archive_description',10);

//Agregar el boton de comprar
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 20 );

//Agregar la descripcion corta de la categoria
add_action('woocommerce_before_shop_loop','vinilos_agregar_descripcion_corta_categoria',10);

function vinilos_agregar_descripcion_corta_categoria()
{
    $cate = get_queried_object();
    $cateID = $cate->term_id;
    ?>
    <p>
        <?php echo $productCatMetaTitle = get_term_meta($cateID, 'descripcion_corta_categoria', true); ?>
    </p>
    <?php
}

/* 

        PAGINA DEL PRODUCTO

*/

//Quitar la categoria
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

function rc_posts_add_rewrite_rules( $wp_rewrite )
{
    $new_rules = [
        'blog/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1),
        'blog/(.+?)/?$' => 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1),
    ];
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'rc_posts_add_rewrite_rules');

function rc_posts_change_blog_links($post_link, $id=0){
    $post = get_post($id);
    if( is_object($post) && $post->post_type == 'post'){
        return home_url('/blog/'. $post->post_name.'/');
    }
    return $post_link;
}
add_filter('post_link', 'rc_posts_change_blog_links', 1, 3);

?>

