<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/admin
 */

/**
 * @package    Rc_Suite
 * @subpackage Rc_Suite/admin
 * @author     Raúl Carrión <hola@raulcarrion.com>
 */

class Rc_Suite_Admin {

	/**
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name 
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rc-suite-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rc-suite-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function rc_menu_plugin() {
		/**
		 * Añade al menú principal la sección para configurar el plugin
		 */

		include_once plugin_dir_path( __FILE__ ) . '/partials/rc-suite-admin-display.php';

		//add_menu_page(string $nombre_pagina, string $nombre_menu, string $permisos, string $menu_slug, callable $funcion, string $url_icono, int $posicion)

		add_menu_page('Setup de RC Suite', 'RC Suite', 'manage_options', 'rc-plugin', 'rc_suite_admin_page', plugin_dir_url( __FILE__ ) . 'rc-suite-icon.png');
	}	


	public function rc_anti_publi_plugins() {
		/**
		 * Oculta por CSS los avisos de los plugins del área de administración
		 */
		wp_enqueue_style('admin-styles', plugin_dir_url( __FILE__ ) . 'css/rc-suite-admin-anti-publi-plugins.css');	
	}


	public function rc_divi_projects_disabled( $args ) {
		return array_merge( $args, array(
			'public'              => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_ui'             => false
		));
}

}