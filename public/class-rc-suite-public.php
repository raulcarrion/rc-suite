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
	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rc-suite-public.js', array( 'jquery' ), $this->version, false );

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
		get_currentuserinfo(); 

		if (is_user_logged_in() && $args->theme_location == 'secondary-menu') {

		$items .= '<li id="menu-item-rc" class="menu-item"><a class="rc-mi-cuenta" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">' . __('Hello', 'rc-suite'). ' ' . $current_user->display_name . '</a> (<a class="salir-link" href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'">' . __('Log out', 'rc-suite') . '</a>)</li>';

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
}