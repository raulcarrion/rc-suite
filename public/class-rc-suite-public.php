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


	public function rc_login_customer( $items, $args ) {
		/**
		 * Añade al menú secundiario el enlace a mi cuenta añadiendo el nombre de usuario cuando está logueado.
		 */

		global $current_user;
		wp_get_current_user(); 

		if (is_user_logged_in() && $args->theme_location == 'secondary-menu') {

		$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';

		}

		elseif (!is_user_logged_in() && $args->theme_location == 'secondary-menu') {

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
				$prod_price = wc_price( $prod_prices[0] );
		
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
		$new_rules = [
			'blog/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged='. $wp_rewrite->preg_index(1),
			'blog/(.+?)/?$' => 'index.php?post_type=post&name='. $wp_rewrite->preg_index(1),
		];
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}
	
	public function rcsu_seo_blog_posts_change_blog_links($post_link, $id=0)
	{
		$post = get_post($id);
		if( is_object($post) && $post->post_type == 'post'){
			return home_url('/blog/'. $post->post_name.'/');
		}
		return $post_link;
	}
}