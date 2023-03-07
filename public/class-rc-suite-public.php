<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/public
 */

/**
 * @package    Rc_Suite
 * @subpackage Rc_Suite/public
 * @author     Raúl Carrión <hola@raulcarrion.com>
 */
class Rc_Suite_Public {

	/**
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
	 * @since    1.0.0
	 * @param      string    $plugin_name      
	 * @param      string    $version
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rc-suite-public.css',array() , $this->version, false  );
		
		//4-2 Product colums
		if(get_option('rcsu_4_2_columns_enabled'))
			wp_enqueue_style( $this->plugin_name . "_4_2_colums", plugin_dir_url( __FILE__ ) . 'css/rc-suite-4-2-colums.css',array() , $this->version, false  );

		// Collapsable MENU
		if(get_option('rcsu_collapsable_megamenu_enabled'))
			wp_enqueue_style( $this->plugin_name . "_mobile_menu", plugin_dir_url( __FILE__ ) . 'css/rc-suite-mobile-menu.css',array() , $this->version, false  );

		// Mobile MENU Centered
		if(get_option('rcsu_mobile_search_centered_enabled'))
			wp_enqueue_style( $this->plugin_name . "_mobile_centerd", plugin_dir_url( __FILE__ ) . 'css/rc-suite-mobile-centered.css',array() , $this->version, false  );
	
		// Clases DIVI
		if(get_option('rcsu_activar_css_clases') || get_option('rcsu_activar_css_clases', "not-exist") == "not-exist")
			wp_enqueue_style( $this->plugin_name . "_divi_classes", plugin_dir_url( __FILE__ ) . 'css/rc-suite-divi-classes.css',array() , $this->version, false  );

		// Funciones JS
		if(get_option('rc_suite_activar_funciones_js'))
			wp_enqueue_style( $this->plugin_name . "_js_functions_css", plugin_dir_url( __FILE__ ) . 'css/rc-suite-js-functions.css',array() , $this->version, false  );

	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rc-suite-public.js', array( 'jquery' ), $this->version, false );
		
		// Collapsable MENU
		
		if(get_option('rcsu_collapsable_megamenu_enabled'))
			wp_enqueue_script( $this->plugin_name . "_mobile_menu", plugin_dir_url( __FILE__ ) . 'js/rc-suite-mobile-menu.js', array( 'jquery' ), $this->version, false );

		//	 Funciones JS
		if(get_option('rc_suite_activar_funciones_js'))
			wp_enqueue_script( $this->plugin_name . "_js_functions", plugin_dir_url( __FILE__ ) . 'js/rc-suite-js-functions.js', array( 'jquery' ), $this->version, false );


			// Paso de variables a los scrips de JS
		wp_localize_script( $this->plugin_name,'rc_suite_vars', array(	'ajax_nonce' => wp_create_nonce( 'ax_wp_action' ),
																		'ajax_url'   => admin_url( 'admin-ajax.php' ))
);
	}

	public function rc_anti_cache_css($content) {
		/**
		 * Modifica la versión del archivo CSS para reventar la caché mientras trabajamos
		 * Para dispositivos móviles es justo y necesario
		 */

		$version = mt_rand(1,1000);
		
		if ( strpos( $content, 'ver=' ) ){
        	$content = remove_query_arg( 'ver', $content );
        	$content = add_query_arg( 'ver',$version, $content );
		}
    	return $content;
	}

	public function rc_login_customer_sc( $args ) {

		global $current_user;

		$icono   = get_option('rc_login_customer_icon_enabled');

		if (is_user_logged_in())
		{
			if ($icono)
				$return = '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '"><img src="' . esc_url( get_avatar_url( get_current_user_id() ) ) . '" /></a></li>';
			else
				$return = '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( home_url() ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';
		}
		else
		{
			if ($icono)
				$return = '<li id="menu-item-rc" class="menu-item"><a href="' . wc_get_page_permalink( 'myaccount' ) . '">' . '<img src="' . esc_url( get_avatar_url( 0 ) ) . '" />'. '</a></li>';
			else
				$return = '<li id="menu-item-rc" class="menu-item"><a href="' . wc_get_page_permalink( 'myaccount' ) . '">' . __('Login', 'rc-suite') . '</a></li>';
		}

		return $return;
	}
	
	public function rc_login_customer( $items, $args ) {
		/**
		 * Añade al menú secundiario el enlace a mi cuenta añadiendo el nombre de usuario cuando está logueado.
		 */

		global $current_user;
		wp_get_current_user(); 

		$id_menu            = (is_object($args->menu) ? $args->menu->term_id : $args->menu);
		$id_menu_selected   = get_option('rc_suite_woo_menu');
		$icono   			= get_option('rc_login_customer_icon_enabled');
		$imagen_icono		= (!empty(get_option('rc_login_customer_icon_path')) ? get_option('rc_login_customer_icon_path') : esc_url( get_avatar_url( get_current_user_id(), array("size" => 32) ) )) ;

		if ($id_menu == $id_menu_selected)
		{
			/*if (is_user_logged_in() ) 
				$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';
			else
				$items .= '<li id="menu-item-rc" class="menu-item"><a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Login', 'rc-suite') . '</a></li>';
			*/

			if (is_user_logged_in())
			{
				if ($icono)
					$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '"><img style="max-width:32px" src="' . $imagen_icono  . '" /></a></li>';
				else
					$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( home_url() ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';
			}
			else
			{
				if ($icono)
					$items .= '<li id="menu-item-rc" class="menu-item"><a href="' . wc_get_page_permalink( 'myaccount' ) . '">' . '<img src="' . $imagen_icono . '" style="max-width:32px" />'. '</a></li>';
				else
					$items .= '<li id="menu-item-rc" class="menu-item"><a href="' . wc_get_page_permalink( 'myaccount' ) . '">' . __('Login', 'rc-suite') . '</a></li>';
			}
		
		}

		return $items;
	}


	function rc_parent_css() {
		/**
		 * Añade el CSS del tema padre en el tema hijo
		 */
		wp_enqueue_style( 'rc-master', get_template_directory_uri() . '/style.css' );
	}

	/**
	 * Oculta el variable price range de WooCommerce
	 */

	function rcsu_hide_variable_product_price( $v_price, $v_product ) {
		
		// Product Prices MAX y MIN
		$prod_prices    = array( $v_product->get_variation_price( 'min', true ), $v_product->get_variation_price( 'max', true ) );
		$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ), $v_product->get_variation_regular_price( 'max', true ) );
		
		sort( $regular_prices );

		//Hay precios en oferta
		if ( $prod_prices[0] !== $regular_prices[0] ) 
		{
			if ($prod_prices[0]!==$prod_prices[1])//Precios distintos
				$prod_price = sprintf(__('Desde <del>%s</del><ins>%s</ins> ' . $v_product->get_price_suffix(), 'woocommerce'), wc_price( $regular_prices[0] ), wc_price( $prod_prices[0] ));
			else //Mismos precios
			$prod_price = sprintf(__('<del>%s</del><ins>%s</ins> ' . $v_product->get_price_suffix(), 'woocommerce'), wc_price( $regular_prices[0] ), wc_price( $prod_prices[0] ));

		}
		//No hay precios en oferta
		else 
		{
			if ($prod_prices[0]!==$prod_prices[1])//Precios distintos
				$prod_price = sprintf(__('Desde %1$s ' . $v_product->get_price_suffix(), 'woocommerce'), wc_price( $prod_prices[0] ));
			else//Mismos precios
				$prod_price = wc_price( $prod_prices[0] ) . $v_product->get_price_suffix();
		}

		return $prod_price;
	}

	/**
	 * SEO - Blogs - Obliga a que los posts del blog aparezcan bajo /blog/
	 */
	public function rcsu_seo_blog_posts_add_rewrite_rules( $wp_rewrite )
	{

		if (!function_exists('pll_languages_list')) 
		{
			$categoria = !empty(get_option( 'category_base' )) ? get_option( 'category_base' ) : "category";

			$new_rules = [
				'blog/'.$categoria.'/(.+?)/page/([0-9]{1,})/?$' => 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1) . '&paged='. $wp_rewrite->preg_index(2),
				'blog/'.$categoria.'/(.+?)/?$'   				=> 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1),

				'blog/page/([0-9]{1,})/?(?!?et_blog)$' 			=> 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1),
				'blog/(?!page)(.+?)/?$'							=> 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1),
			];
		}
		else
		{
			$categoria = (!empty(get_option( 'category_base' )) ? pll_translate_string(get_option( 'category_base' ), pll_default_language()) : pll_translate_string("category", pll_default_language()));
			$idioma_def = pll_default_language();

			$new_rules['blog/'.$categoria.'/(.+?)/page/([0-9]{1,})/?$']  	= 'index.php?lang='.$idioma_def.'&taxonomy=category&term='. $wp_rewrite->preg_index(1). '&paged='. $wp_rewrite->preg_index(2);
			$new_rules['blog/'.$categoria.'/(.+?)/?$']  					= 'index.php?lang='.$idioma_def.'&taxonomy=category&term='. $wp_rewrite->preg_index(1);
			
			$new_rules['blog/page/([0-9]{1,})/?(?!?et_blog)$'] 	= 'index.php?lang='.$idioma_def.'&post_type=post&paged='. $wp_rewrite->preg_index(1);
			$new_rules['blog/(?!page)(.+?)/?$']            		= 'index.php?lang='.$idioma_def.'&post_type=post&name='. $wp_rewrite->preg_index(1);

			foreach(array_diff(pll_languages_list(), array(pll_default_language())) as $idioma)
			{
				$categoria = (!empty(get_option( 'category_base' )) ? pll_translate_string(get_option( 'category_base' ), $idioma) : pll_translate_string("category", $idioma));

				$new_rules[$idioma.'/blog/'.$categoria.'/(.+?)/page/([0-9]{1,})/?$']  	= 'index.php?lang='.$idioma.'&taxonomy=category&term='. $wp_rewrite->preg_index(1). '&paged='. $wp_rewrite->preg_index(2);
				$new_rules[$idioma.'/blog/'.$categoria.'/(.+?)/?$']  					= 'index.php?lang='.$idioma.'&taxonomy=category&term='. $wp_rewrite->preg_index(1);
				
				$new_rules[$idioma.'/blog/page/([0-9]{1,})/?(?!?et_blog)$'] 	= 'index.php?lang='.$idioma.'&post_type=post&paged='. $wp_rewrite->preg_index(1);
				$new_rules[$idioma.'/blog/(?!page)(.+?)/?$']            		= 'index.php?lang='.$idioma.'&post_type=post&name='. $wp_rewrite->preg_index(1);
			}
		}

		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

		return $wp_rewrite->rules;
	}
	
	public function rcsu_seo_blog_posts_change_blog_links($post_link, $id=0)
	{
		$post = get_post($id);
		if( is_object($post) && $post->post_type == 'post' && !is_admin()){
			return home_url('/blog/'. $post->post_name.'/');
		}
		return $post_link;
	}

	public function rcsu_seo_blog_posts_change_category_links( $termlink, $term_id)
	{
		$cat = get_term($term_id);

		if( is_object($cat) && $cat->taxonomy == 'category' ){
			if (!function_exists('pll_translate_string'))
			{
				$categoria = !empty(get_option( 'category_base' )) ? get_option( 'category_base' ) : "category";
				$termlink  =  home_url('/blog/'.$categoria.'/'. $cat->slug.'/');
			}
			else
			{
				$categoria = (!empty(get_option( 'category_base' )) ? pll_translate_string(get_option( 'category_base' ), pll_get_term_language($term_id)) : pll_translate_string("category", pll_get_term_language($term_id)));
				$termlink  = home_url('/blog/'. $categoria . '/' . $cat->slug.'/');
			}
		}

		return $termlink;
	}

	/*	Redirecciona a /blog si se inserta la URL sin el /blog */
	function redirect_old_urls() {

		if ( is_singular('post') && !is_preview() && !is_admin()) {
			global $post;
	
			if ( strpos( $_SERVER['REQUEST_URI'], 'et_fb=1') === false){
				if ( strpos( $_SERVER['REQUEST_URI'], '/blog/') === false) {
				wp_redirect( home_url( user_trailingslashit( "blog/$post->post_name" ) ), 301 );
				exit();
				}
			}
		}
		
	}

	/*	Cambiamos el canonical de los blogs	solo si se ha seleccionado afgregar /blog/*/
	function yoast_remove_canonical_items( $canonical ) 
	{
		if ( is_singular('post') ) 
		{
			if (function_exists('pll_home_url'))
				$canonical = pll_home_url() . user_trailingslashit('blog/'. get_post_field( 'post_name'));
			else
				$canonical = home_url(user_trailingslashit('blog/'. get_post_field( 'post_name')));
		}
		elseif (is_category())
		{
			$cat = get_category( get_query_var( 'cat' ) );

			if (!is_wp_error($cat) && !empty($cat))
			{
				if (!function_exists('pll_translate_string'))
				{
					$categoria = !empty(get_option( 'category_base' )) ? get_option( 'category_base' ) : "category";
					$canonical  =  home_url('/blog/'.$categoria.'/'. $cat->slug.'/');
				}
				else
				{
					$categoria = (!empty(get_option( 'category_base' )) ? pll_translate_string(get_option( 'category_base' ), pll_get_term_language($term_id)) : pll_translate_string("category", pll_get_term_language($term_id)));
					$canonical  = home_url('/blog/'. $categoria . '/' . $cat->slug.'/');
				}
			}

		}

		//Use a second if statement here when needed 
		return $canonical; 
	}

	/**
	 * REMOVE WEB FROM COMMENTS 
	 */

	/* Quitar Web de las notificaciones */
	public function rc_filter_comment_text( $notify_message, $comment_id ) {
		return str_replace('URL: ','',$notify_message);
	}

	/* Quitar web del formulario para comentar */
	public function rc_disable_url_comment($fields) {
		unset($fields['url']);
		return $fields;
	}

	/* Quitar los enlaces de los nombres de autores de los comentarios*/

	public function rc_disable_comment_author_links( $author_link ){
		return strip_tags( $author_link );
	}

	public function rc_comment_post( $incoming_comment ) {
		$incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
		$incoming_comment['comment_content'] = str_replace( "'", '&amp;apos;', $incoming_comment['comment_content'] );
		return( $incoming_comment );
	}

	public function rc_comment_display( $comment_to_display ) {
		$comment_to_display = str_replace( '&amp;apos;', "'", $comment_to_display );
		return $comment_to_display;
	}


	/**
	 * ADD GOOGLE TAG MANAGER
	 */
	public function rc_add_gtag_head() {
		if (!empty(get_option('rcsu_gtag_head')))
			echo get_option('rcsu_gtag_head');
	}

	/*
	*	ADD GLOBAL BANNER
	*/

	public function rc_add_global_banner_filter($top_header)
	{
		if (!empty(get_option('rcsu_banner_content')))
		{
			$banner = '<div id="global-banner" style="padding:'.get_option('rcsu_banner_padding').'px;background-color:'. get_option('rcsu_banner_color').'">
							<p style="display:flex; justify-content:space-between">'.get_option('rcsu_banner_content').'<span id="banner-cerrar" style="cursor:pointer;">X</span></p>
						</div>
						<script>
							jQuery(function($){

								$("#banner-cerrar").on("click", function(){
									$("#global-banner").hide(500);
								});
							
							});
						</script>';
			$top_header = str_replace('<div class="container clearfix et_menu_container">', $banner . '<div class="container clearfix et_menu_container">', $top_header);
		}	
		return $top_header;
	}

	public function rc_add_global_banner_action()
	{
		if (!empty(get_option('rcsu_banner_content')))
		{
			?>
			<div id="global-banner" style="padding:<?php echo get_option('rcsu_banner_padding') ?>px;background-color:<?php echo get_option('rcsu_banner_color')?>;">
				<div style="display:flex; justify-content:space-between">
					<div style="width:100%"><?php echo get_option('rcsu_banner_content') ?></div>
					<span id="banner-cerrar" style="cursor:pointer; color:white;">X</span>
				</div>
			</div>
			<script>
				jQuery(function($){

					$("#banner-cerrar").on("click", function(){
						$("#global-banner").hide(500);
					});
				
				});
			</script>
		<?php
		}	
	}

	function rc_ax_ocultar_banner()
	{
		// Verificamos el codigo de seguridad
		$nonce = sanitize_text_field( $_POST['nonce'] );
			
		if ( ! wp_verify_nonce( $nonce, 'ax_wp_action' ) ) {
			die ( __('Unathorized petition!',"rc-suite")); 
		}

		setcookie('wp_visitor_banner', serialize("NO"), 0, "/"); //Con 0 expira al final de la sesion
		
		wp_die();
	}
}