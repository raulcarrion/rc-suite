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
	
	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rc-suite-public.js', array( 'jquery' ), $this->version, false );
		
		// Collapsable MENU
		
		if(get_option('rcsu_collapsable_megamenu_enabled'))
			wp_enqueue_script( $this->plugin_name . "_mobile_menu", plugin_dir_url( __FILE__ ) . 'js/rc-suite-mobile-menu.js', array( 'jquery' ), $this->version, false );

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

		if (is_user_logged_in())
			$return = '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( home_url() ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';
		else
			$return = '<li id="menu-item-rc" class="menu-item"><a href="' . site_url( "/login/" ) . '">' . __('Login', 'rc-suite') . '</a></li>';

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

		if ($id_menu == $id_menu_selected)
		{
			if (is_user_logged_in() ) 
				$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';
			else
				$items .= '<li id="menu-item-rc" class="menu-item"><a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Login', 'rc-suite') . '</a></li>';
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

		if ( $prod_prices[0] !== $regular_prices[0] ) 
			$prod_price = sprintf(__('Desde <del>%s</del><ins>%s</ins> ' . $v_product->get_price_suffix(), 'woocommerce'), wc_price( $regular_prices[0] ), wc_price( $prod_prices[0] ));
		else
			if ($prod_prices[0]!==$prod_prices[1])
				$prod_price = sprintf(__('Desde %1$s ' . $v_product->get_price_suffix(), 'woocommerce'), wc_price( $prod_prices[0] ));
			else
				$prod_price = wc_price( $prod_prices[0] ) . $v_product->get_price_suffix();
		
		/*// Product Price
		$prod_prices = array( $v_product->get_variation_price( 'min', true ), 
									$v_product->get_variation_price( 'max', true ) );
		$prod_price = $prod_prices[0]!==$prod_prices[1] ? sprintf(__('Desde %1$s '. $v_product->get_price_suffix() , 'woocommerce'), 
								wc_price( $prod_prices[0] ) ) : wc_price( $prod_prices[0] );
		
		// Regular Price
		$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ), 
									$v_product->get_variation_regular_price( 'max', true ) );
		sort( $regular_prices );
		$regular_price = $regular_prices[0]!==$regular_prices[1] ? sprintf(__('Desde %1$s' . $v_product->get_price_suffix(),'woocommerce')
								, wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );
		
		if ( $prod_price !== $regular_price ) {
		$prod_price = '<del>'.$regular_price.$v_product->get_price_suffix() . '</del> <ins>' . 
								$prod_price . $v_product->get_price_suffix() . '</ins>';
		}*/

		return $prod_price;
	}

	/**
	 * SEO - Blogs - Obliga a que los posts del blog aparezcan bajo /blog/
	 */
	public function rcsu_seo_blog_posts_add_rewrite_rules( $wp_rewrite )
	{
		if (!function_exists('pll_languages_list')) 
		{
			$new_rules = [
				'blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").'category/(.+?)/page/([0-9]{1,})/?$'   => 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1) . '&paged='. $wp_rewrite->preg_index(2),
				'blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").'category/(.+?)/?$'   => 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1),
				'blog/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1),
				'blog/(.+?)/?$' => 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1),
			];
		}
		else
		{
			$new_rules['blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").pll_translate_string("categoria", pll_default_language()).'/(.+?)/page/([0-9]{1,})/?$']  = 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1). '&paged='. $wp_rewrite->preg_index(2);
			$new_rules['blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").pll_translate_string("categoria", pll_default_language()).'/(.+?)/?$']  = 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1);
			$new_rules['blog/page/([0-9]{1,})/?$'] = 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1);
			$new_rules['blog/(.+?)/?$']            = 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1);

			foreach(array_diff(pll_languages_list(), array(pll_default_language())) as $idioma)
			{
				$new_rules[$idioma.'/blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").pll_translate_string("categoria", $idioma).'/(.+?)/page/([0-9]{1,})/?$']  = 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1). '&paged='. $wp_rewrite->preg_index(2);
				$new_rules[$idioma.'/blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").pll_translate_string("categoria", $idioma).'/(.+?)/?$']  = 'index.php?taxonomy=category&term='. $wp_rewrite->preg_index(1);
				$new_rules[$idioma.'/blog/page/([0-9]{1,})/?$'] = 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1);
				$new_rules[$idioma.'/blog/(.+?)/?$']            = 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1);
			}
		}

		error_log(var_export($new_rules, true));

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

		if( is_object($cat) && $cat->taxonomy == 'category' && !is_admin()){
			if (function_exists('pll__'))
				$termlink =  home_url('/blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : "").pll__("categoria").'/'. $cat->slug.'/');
			else
				$termlink = home_url('/blog/'.(!empty(get_option( 'category_base' )) ? get_option( 'category_base' ) .'/' : ""). $cat->taxonomy . '/' . $cat->slug.'/');
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

	/*	Cambiamos el canonical de los blogs	*/
	function yoast_remove_canonical_items( $canonical ) {
		if ( is_singular('post') ) {
			if (function_exists('pll_home_url'))
				$canonical = pll_home_url() . user_trailingslashit('blog/'. get_post_field( 'post_name'));
			else
				$canonical = home_url(user_trailingslashit('blog/'. get_post_field( 'post_name')));
		}
		//error_log("Es es: " . get_post_field( 'post_name'));

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

}