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
		
		// Incluimos el fichero partials aqui para permitir que las funciones 
		// de output HTML, esten disponibles en las llamadas AJAX
		require_once plugin_dir_path( __FILE__ ) . '/partials/rc-suite-admin-html.php';

		$this->plugin_name  = $plugin_name;
		$this->version 		= $version;
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
		
		// Paso de variables a los scrips de JS
		wp_localize_script( $this->plugin_name,'rc_suite_vars', array(	'ajax_nonce' => wp_create_nonce( 'ax_wp_action' ),
																		'ajax_url'   => admin_url( 'admin-ajax.php' ))
		);
	}
	
	/**
	 * Para mensajes de error
	 */
	function rc_suite_admin_notices() {
		settings_errors();
	}

	/**
	 * 		AJAX
	 */

	/**
	* 	Reemplazador - Obtener detalle de fichero
	*/
	function rcsu_ajx_replacer_get_file_details()
	{
		// Verificamos el codigo de seguridad
		$nonce = sanitize_text_field( $_POST['nonce'] );
		
		if ( ! wp_verify_nonce( $nonce, 'ax_wp_action' ) ) {
			die ( __("Authorization denied!", "rc-suite")); 
		}
		else 
		{
			if (!empty($_POST['rcsu-file']))
			{
				$reemplazador = new Rc_Suite_Reemplazador();
				if (($reemplazos = $reemplazador->get_replaces_by_csv(PLUGIN_PATH_UPLOAD_FILES.$_POST['rcsu-file'])) !== FALSE)
				{
					rcsu_html_file_replacer_details($reemplazos);
				}
				else
					_e("Error opening the file","rc-suite");
			}
			else
			{
				_e("File name is empty!!", "rc-suite");
			}
		}

		exit();
	}

	/**
	* 	Reemplazador - Test fichero
	*/
	function rcsu_ajx_test_file()
	{
		// Verificamos el codigo de seguridad
		$nonce = sanitize_text_field( $_POST['nonce'] );
		
		if ( ! wp_verify_nonce( $nonce, 'ax_wp_action' ) ) {
			die ( __("Authorization denied!", "rc-suite")); 
		}

		else 
		{
			if (!empty($_POST['rcsu-file']))
			{
				$reemplazador = new Rc_Suite_Reemplazador();
		
				//Leemos las reglas de reemplazo del fichero
				if ($reemplazador->set_replaces_by_csv(PLUGIN_PATH_UPLOAD_FILES.$_POST['rcsu-file']) !== FALSE)
				{
					// Vemos cuantos reemplazos se efectuarian
					if (($reemplazos = $reemplazador->get_num_reemplazos()) !== FALSE)
						rcsu_html_file_replacer_test($reemplazos);					
					else
						_e("Error testing the file!","rc-suite");	
				}
				else
					_e("Error opening the file","rc-suite");
			}
			else
			{
				_e("File name is empty!!", "rc-suite");
			}
		}

		exit();
	}

	/**
	* 	Reemplazador - Test fichero
	*/
	function rcsu_ajx_process_file()
	{
		// Verificamos el codigo de seguridad
		$nonce = sanitize_text_field( $_POST['nonce'] );
		
		if ( ! wp_verify_nonce( $nonce, 'ax_wp_action' ) ) {
			die ( __("Authorization denied!", "rc-suite")); 
		}

		else 
		{
			if (!empty($_POST['rcsu-file']))
			{
				$reemplazador = new Rc_Suite_Reemplazador();
		
				//Leemos las reglas de reemplazo del fichero
				if ($reemplazador->set_replaces_by_csv(PLUGIN_PATH_UPLOAD_FILES.$_POST['rcsu-file']) !== FALSE)
				{
					// Vemos cuantos reemplazos se efectuarian
					if (($reemplazos = $reemplazador->do_replaces()) !== FALSE)
						rcsu_html_file_replacer_replaces($reemplazos);					
					else
						_e("Error processing the file!","rc-suite");	
				}
				else
					_e("Error opening the file","rc-suite");
			}
			else
			{
				_e("File name is empty!!", "rc-suite");
			}
		}

		exit();
	}

	/**
	 * Añade al menú principal la sección para configurar el plugin
	 */
	public function rc_menu_plugin() {

		include_once plugin_dir_path( __FILE__ ) . '/partials/rc-suite-admin-display.php';
		
		//add_menu_page(string $nombre_pagina, string $nombre_menu, string $permisos, string $menu_slug, callable $funcion, string $url_icono, int $posicion)
		add_menu_page('Setup de RC Suite', 'RC Suite', 'manage_options', 'rc-suite', 'rc_suite_admin_page', plugin_dir_url( __FILE__ ) . 'rc-suite-icon.png');
		
	}	

	/**
	 * Oculta por CSS los avisos de los plugins del área de administración
	 */

	public function rc_anti_publi_plugins() {
		wp_enqueue_style('admin-styles', plugin_dir_url( __FILE__ ) . 'css/rc-suite-admin-anti-publi-plugins.css');	
	}

	/**
	 * Oculta los proyectos de DIVI
	 */

	public function rc_divi_projects_disabled( $args ) {
		return array_merge( $args, array(
			'public'              => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_ui'             => false
		));

	}

	/* Enlazar estilos para el área de administración*/
	function rc_admin_style() {
		wp_enqueue_style('rc-admin-styles', get_stylesheet_directory_uri().'/admin.css');
	}

	
}

