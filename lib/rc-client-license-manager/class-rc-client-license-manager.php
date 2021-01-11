<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.raulcarrion.com/
 * @since             1.0.0
 * @package           Rc_Client_License_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       RC Cliente License Manager
 * Plugin URI:        https://www.raulcarrion.com/shop/rc-client-license-manager/
 * Description:       Library to include a client to manage license
 * Version:           1.0.0
 * Author:            Raúl Carrión
 * Author URI:        https://www.raulcarrion.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rc-client-license-manager
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Rc_Client_License_Manager' ) ) {

	class Rc_Client_License_Manager {

		private $VERSION     		= '1.0.0';
		private $PLUGIN_SLUG 		= 'rc-client-license-manager';
		
		
		private $domain;
    	private $product_id;
		private $license_key;
		private $plugin_path;  
		private $parent_plugin_slug;
		private $server_url;
		private $active_tab; 

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct($parent_plugin_slug, $product_id, $server_url) {

			$this->active_tab           = false;
			$this->plugin_path  		= plugin_dir_path( __FILE__ );
			$this->parent_plugin_slug   = $parent_plugin_slug;
			$this->server_url 			= $server_url;
			$this->product_id           = $product_id;
        
			// Si es multisite agregamos el subdominio
			/*if (is_multisite())
				$domain .= "/" . get_current_blog_id();*/
			
			$this->domain      = get_option($this->parent_plugin_slug . "_license_key_domain", "");
			$this->license_key = get_option($this->parent_plugin_slug . "_license_key", "");
			
			// Cargamos dependencias y Hooks
			$this->load_dependencies();
			$this->define_hooks();

		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Rc_Client_License_Manager_Loader. Orchestrates the hooks of the plugin.
		 * - Rc_Client_License_Manager_i18n. Defines internationalization functionality.
		 * - Rc_Client_License_Manager_Admin. Defines all hooks for the admin area.
		 * - Rc_Client_License_Manager_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {
			
			$this->preprare_js_file();

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_hooks() {
			
			//Gestion de mensajes
			// Para el control de errores
			add_action( 'admin_notices', array($this,'identificador_admin_notices'));

			//Gestion de peticiones
			add_action("admin_init", array($this,'gestion_peticiones'));
			
			// Traducciones
			add_action('init', array($this, 'loadTextDomain'));

			// Estilos
			add_action('admin_enqueue_scripts', array($this,'setup_scripts'));
			
			// Tab y contenido del formulario de gestion de licencia
			$this->add_license_manager_tab();
			
		}

		/**
		 * Registra los estilos del plugin
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function setup_scripts() {
			
			//Estilo css
			wp_enqueue_style( $this->PLUGIN_SLUG, plugin_dir_url( __FILE__ ) . 'css/rc-client-license-manager-admin.css' );

			// Jquery
			wp_enqueue_script( $this->PLUGIN_SLUG, plugin_dir_url( __FILE__ ) . 'js/' . $this->parent_plugin_slug . '-rc-client-license-manager-admin.js', array( 'jquery' ));
			// La variable de Jquery
			wp_localize_script( $this->PLUGIN_SLUG,
								'rc_client_license_manager_translate', 
								array('parent_plugin_slug' => $this->parent_plugin_slug,		
			));
		}

		/**
		 * Prepara el fichero js para evitar la cache entre plugins. 
		 * of the plugin.
		 */
		private function preprare_js_file() {
			
			if (!file_exists($this->plugin_path . 'js/' .  $this->parent_plugin_slug . "-rc-client-license-manager-admin.js"))
				if (file_exists($this->plugin_path . 'js/rc-client-license-manager-admin.js'))
					rename($this->plugin_path . 'js/rc-client-license-manager-admin.js' , $this->plugin_path . 'js/' .  $this->parent_plugin_slug . "-rc-client-license-manager-admin.js");
		}

		/**
		 * Registra los estilos del plugin
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function loadTextDomain() {
			
			//Traduccones
			load_plugin_textdomain(
				$this->PLUGIN_SLUG,
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);

		}

		/**
		 * Registra los errores para la pagina de administracion
		 *
		 * @since    1.0.0
		 */
		
		function identificador_admin_notices() {
			settings_errors( $this->parent_plugin_slug . '-client-license-manager-alerts' );
		}

		/**
		 * Inicializa los datos de la clave de licencia
		 *
		 * @since    1.4.0
		 */
		public function __init($domain, $license_key) {
        
		// Si es multisite agregamos el subdominio
        /*if (is_multisite())
            $domain .= "/" . get_current_blog_id();*/

        $this->domain      = $domain;
        $this->license_key = $license_key;

        // Actualizamos los datos de la LK
        update_option($this->parent_plugin_slug . "_license_key"			, $this->license_key);
		update_option($this->parent_plugin_slug . "_license_key_domain"		, $this->domain);
		
		delete_transient($this->parent_plugin_slug . "_license_key_status");
		update_option ($this->parent_plugin_slug . "_license_status_text", __("License key status is unknow", $this->PLUGIN_SLUG));
           
		}

		/**
		 * Funcion que devuelve el texto del error
		 *
		 * @since    1.4.0
		 */	

		public function get_err_code_result($code)
		{ 
			switch ($code){
				case 'e001':
					$retorno = __("Incorrect data. Check license key.",$this->PLUGIN_SLUG);
					break;
				case 'e002':
					$retorno =  __("The license key is invalid!",$this->PLUGIN_SLUG);
					break;
				case 'e004':
					$retorno =  __("Order status is not completed! Please complete order before activating product.",$this->PLUGIN_SLUG);
					break;                
				case 'e110':
					$retorno =  __("Invalid licence key or licence key inactive",$this->PLUGIN_SLUG);
					break;
				case 'e112':
					$retorno =  __("Reached maximum allowable domains!",$this->PLUGIN_SLUG);
					break;
			default:
					$retorno = __("Unexpected error! : " . $code, $this->PLUGIN_SLUG);
					break;
			}
			return $retorno;
		}

		/**
		 * Funcion que activa el license key
		 *
		 * RETORNO ARRAY:  true  - Licencia activada
		 * 			 	   false - Licencia no activada
		 * 			
		 *                  Mensaje
		 * 			  	
		 * 
		 * @since    1.4.0
		 */	
		public function activate_license_key()
		{ 
			if (!empty($this->license_key))
			{
				delete_transient($this->parent_plugin_slug . '_license_key_status');

				$args = array(
					'woo_sl_action'     => 'activate',
					'licence_key'       => $this->license_key,
					'product_unique_id' => $this->product_id,
					'domain'            => $this->domain
				);

				$request_uri    = $this->server_url . '?' . http_build_query( $args );
				$data           = wp_remote_get( $request_uri );

				if(is_wp_error( $data ) || $data['response']['code'] != 200)
					return array(false, -99, __("Unexpected Error calling activation server! The activation did not success.", $this->PLUGIN_SLUG));
				
				$data_body = json_decode($data['body']);

				if(isset($data_body[0]->status)) 
				{
					if($data_body[0]->status == 'success' && ($data_body[0]->status_code == 's100' || $data_body[0]->status_code == 's101'))
					{
						//Validamos el estado de la licencia
						switch (strtolower($data_body[0]->licence_status)) 
						{
							case 'active':
								update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is activate", $this->PLUGIN_SLUG));
								set_transient( $this->parent_plugin_slug . '_license_key_status', 1, 24 * HOUR_IN_SECONDS );
								break;
							
							case 'expired':
								update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is expired", $this->PLUGIN_SLUG));
								set_transient( $this->parent_plugin_slug . '_license_key_status', 2, 24 * HOUR_IN_SECONDS );
								break;                         
						
							default:
								update_option ($this->parent_plugin_slug . "_license_status_text", __("License key status is unknow", $this->PLUGIN_SLUG));
								set_transient( $this->parent_plugin_slug . '_license_key_status', -99, 24 * HOUR_IN_SECONDS );
								break; 
							break;
						}                                       
						
						return array(true,1,"");; 
					}
					else
					{
						//Actualizamos el estado de la license key por si hubiera cambiado. 
						$this->update_license_key_status();

						switch ($data_body[0]->status_code) {
							case 'e110':
								// Invalid licence key or licence not active for domain.
								return array(false, $data_body[0]->status_code, __("Error: Invalid licence key or licence not active for domain.", $this->PLUGIN_SLUG));
								break;
							
							default:
								return array(false, $data_body[0]->status_code, __("The activation did not success: " . $data_body[0]->message, $this->PLUGIN_SLUG));
								break;
						}
					}
				}
				else
				{
					//there was a problem establishing a connection to the API server
					return array(false, -99, __("Unexpected Error calling activation server! The activation did not success.", $this->PLUGIN_SLUG));
				}
			}
			else
			{
				update_option($this->parent_plugin_slug  . "_license_key", "");
				update_option ($this->parent_plugin_slug . "_license_status_text", __("License key not found", $this->PLUGIN_SLUG));
				set_transient( $this->parent_plugin_slug . '_license_key_status', -2, 24 * HOUR_IN_SECONDS );
							
				update_option($this->parent_plugin_slug . "_license_key_domain", "");
				

				return array(false, -1, __("License key not found! The activation did not success.", $this->PLUGIN_SLUG));
			}
		}

		/**
		 * Funcion que desactiva la license key
		 *
		 * RETORNO ARRAY:  true - Licencia activada
		 * 			 	   false - Licencia no activada
		 * 			
		 *                  Mensaje
		 * 			  	
		 * 
		 * @since    1.4.0
		 */	
		public function deactivate_license_key()
		{ 
			if (!empty($this->license_key))
			{
				delete_transient($this->parent_plugin_slug . '_license_key_status');

				$args = array(
					'woo_sl_action'     => 'deactivate',
					'licence_key'       => $this->license_key,
					'product_unique_id' => $this->product_id,
					'domain'            => $this->domain
				);
				
				$request_uri    = $this->server_url. '?' . http_build_query( $args );
				$data           = wp_remote_get( $request_uri );

				if(is_wp_error( $data ) || $data['response']['code'] != 200)
				{
					return array(false, -99, __("Unexpected Error calling activation server! The activation did not success.", $this->PLUGIN_SLUG));
				}

				$data_body = json_decode($data['body']);
				
				if(isset($data_body[0]->status))
				{
					if($data_body[0]->status == 'success' && $data_body[0]->status_code == 's201')
						{
							update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is expired", $this->PLUGIN_SLUG));
							set_transient( $this->parent_plugin_slug . '_license_key_status', 0, 24 * HOUR_IN_SECONDS );

							return array(true,1,"");; 
						}
						else
						{
							//Actualizamos el estado de la license key por si hubiera cambiado. 
							$this->update_license_key_status();
							
							switch ($data_body[0]->status_code) {
								case 'e110':
									// Invalid licence key or licence not active for domain.
									return array(false, $data_body[0]->status_code, __("Error: Invalid licence key or licence key already inactive.", $this->PLUGIN_SLUG));
									break;
								
								default:
									return array(false, $data_body[0]->status_code, __("The deactivation did not success: " . $$data_body[0]->message, $this->PLUGIN_SLUG));
									break;
							}
							
						}
				}
				else
				{
					//there was a problem establishing a connection to the API server
					return array(false, -99, __("Unexpected Error calling deactivation server! The deactivation did not success.", $this->PLUGIN_SLUG));
				}
			}
			else
			{
				update_option($this->parent_plugin_slug . "_license_key", "");
				update_option ($this->parent_plugin_slug . "_license_status_text", __("License key not found", $this->PLUGIN_SLUG));
				set_transient( $this->parent_plugin_slug . '_license_key_status', -2, 24 * HOUR_IN_SECONDS );
							
				update_option($this->parent_plugin_slug . "_license_key_domain", "");
				
				return array(false, -1, __("License key not found! The deactivation did not success.", $this->PLUGIN_SLUG));
			}

		}	

		
		/**
		 * Funcion que devuelve el estado de la license key
		 *
		 * RETORNO: true: el valor se ha actualziado
		 * 			false: el valor no se ha podido actualizar por un error 
		 * 
		 * 			 2  - Licencia activa pero expirada
		 *           1  - Licencia activada
		 * 		 	 0  - Licencia no activada
		 * 			-1  - Licencia invalida
		 * 			-2  - Codigo de licencia no indicado
		 * 			-3  - Error de activacion
		 * 			-4  - Licencia desactivada
		 * 			-5  - Licencia bloqueada
		 * 			-6  - 
		 * 			-7  - Licencia activa pero no activa para el dominio
		 *          -8  - 
		 *          -9  - Licence Key does not match this product
		 *          -10 - Invalid Product Unique ID
		 *          -11 - El estado del pedido no es completado
		 * 			-99 - Desconocido
		 * 			
		 * 
		 * @since    1.4.0
		 */	
		public function check_license_key() 
		{
			if (($license_key_status = get_transient( $this->parent_plugin_slug . '_license_key_status' )) === false)
			{
				if (!empty($this->license_key))
				{
					$args = array(
						'woo_sl_action'     => 'status-check',
						'licence_key'       => $this->license_key,
						'product_unique_id' => $this->product_id,
						'domain'            => $this->domain
					);
					
					$request_uri    = $this->server_url . '?' . http_build_query( $args );
					$data           = wp_remote_get( $request_uri );
		
					if(is_wp_error( $data ) || $data['response']['code'] != 200)
					{
						$retorno =  array(false, -99, __("Unexpected Error checking license key status.", $this->PLUGIN_SLUG));
					}
					else
					{
						$data_body = json_decode($data['body']);
						
						if(isset($data_body[0]->status))
						{
							if($data_body[0]->status == 'success')
							{
								switch (strtolower($data_body[0]->status_code))
								{
									case "s205": //Licence key Is Active and Valid for Domain
										$retorno = array(true, 1);
										break;
									case "s203": //Licence Key Is Unassigned
										$retorno = array(true, 0);
										break;
									default:
										$retorno = array(true, -99);
										break;		
								}
							} // NO ES success
							else
							{
							switch ($data_body[0]->status_code) 
								{
									case 'e002': //Invalid license key
										$retorno = array(true, -1);
										break;
									case 'e004': //Order status not completed
										$retorno = array(true, -11);   
										break;                                    
									case 'e204': //Not active for current domain
										$retorno = array(true, -7);
										break;
									case 'e301': //Licence Key does not match this product
										$retorno = array(true, -9);
										break;                 
									case 'e312': //Licence is not Active, current status is ‘STATUS’
										$retorno = array(true, 0);   
										break;
									case 'e419': //Invalid Product Unique ID
										$retorno = array(true, -10);   
										break;
									default:
										$retorno = array(true, -99);
										break;	
								}
							}
						}
						else
						{
							//there was a problem establishing a connection to the API server
							$retorno = array(false, -99, __("Unexpected Error checking license key status.", $this->PLUGIN_SLUG));
						}
					}

					return $retorno;    
					
				
				}	
				else
					return(array(false, -2));
			}	
			else
				return (array(true, $license_key_status));
			
		}
		
		/**
		 * Funcion que actualiza el estado de la license key actual
		 *
		 * RETORNO ARRAY:  true  - Estado licencia actualizada
		 * 			 	   false - Estado Licencia no actualizada por error externo
		 * 
		 * @since    1.4.0
		 */	
		public function update_license_key_status()
		{ 
			// Debido al error, verificamos el estado de la license key y actualizamos 
			$license_status = $this->check_license_key(); 
						
			if ($license_status[0]) 
			{
				set_transient($this->parent_plugin_slug . '_license_key_status', $license_status[1], 24 * HOUR_IN_SECONDS ); 
				
				switch ($license_status[1]) 
				{
					// 2  - Licencia activa pero expirada
					// 1  - Licencia activa
					// 0  - Licencia no activada
					//-1  - Licencia invalida
					//-2  - Codigo de licencia no indicado
					//-3  - Error de activacion
					//-4  - Licencia desactivada
					//-5  - Licencia bloqueada
					//-6  - 
					//-7  - Licencia activa pero no activa para el dominio			
					//-8  - 
					//-9  - Licence Key does not match this product
					//-10 - Invalid Product Unique ID
					//-11 - El estado del pido no es completado
					//-99 - Desconocido
					
					case 2:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is active but has expired", $this->PLUGIN_SLUG));
						break;  			
					case 1:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is active", $this->PLUGIN_SLUG));
						break;
					case 0:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is inactive", $this->PLUGIN_SLUG));
						break;
					case -1:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is invalid", $this->PLUGIN_SLUG));
						break;  
					case -4:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is deactivated", $this->PLUGIN_SLUG));
						break;  								
					case -5:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is blocked", $this->PLUGIN_SLUG));
						break;  								
					case -7:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is active but deactivated to this domain, try activate it again.", $this->PLUGIN_SLUG));
						break; 
					case -9:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("Licence Key does not match this product.", $this->PLUGIN_SLUG));
						break; 
					case -10:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("Invalid Product Unique ID.", $this->PLUGIN_SLUG));
						break; 
					case -11:
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key is inactive. Please complete order before activating product.", $this->PLUGIN_SLUG));
						break; 
						
					case -99:
					default:
						set_transient( $this->parent_plugin_slug . '_license_key_status', -99, 24 * HOUR_IN_SECONDS );
						update_option ($this->parent_plugin_slug . "_license_status_text", __("License key status is unknow", $this->PLUGIN_SLUG));
						break;	
				}
			}

			return true;
		}
		
		/**
		 * Funcion que setea el LicenseKey
		 *
		 * RETORNO ARRAY:  true  - Estado licencia actualizada
		 * 			 	   false - Estado Licencia no actualizada por error externo
		 * 
		 * @since    1.4.0
		 */	
		public function set_license_key($license_key)
		{ 
			$this->license_key = $license_key;
		}

		/**
		 * Funcion que setea tab activo/inactivo
		 * para la gestion del tab de licencia
		 * 
		 * @since    1.4.0
		 */	
		public function set_tab_active()
		{ 
			$this->active_tab = true;
		}

		public function set_tab_inactive()
		{ 
			$this->active_tab = false;
		}

		/**
		 * Funcion que retorna el LicenseKey
		 *
		 * @since    1.4.0
		 */	

		public function get_license_key()
		{ 
			return $this->license_key;
		}

		/**
		 * Funcion que devuelve el texto del estado del license key
		 *
		 * @since    1.0.0
		 */	

		public function get_license_key_status_text()
		{ 
			return get_option ($this->parent_plugin_slug . "_license_status_text", __("License key status unknow","rc-license-manager"));
		}


		/**
		 * Funcion que elimina todos los datos referentes a la licencia
		 *
		 * @since    1.0.0
		 */	

		public function delete_all_data()
		{ 
			delete_option($this->parent_plugin_slug . "_license_key");
			delete_option($this->parent_plugin_slug . "_license_status_text");
			delete_option($this->parent_plugin_slug . "_license_key_domain");
			delete_transient($this->parent_plugin_slug . "_license_key_status");
					
			return true;
		}
		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_parent_plugin_slug() {
			return $this->parent_plugin_slug;
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_slug() {
			return $this->PLUGIN_SLUG;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->VERSION;
		}

		/**
		 * Funcion que imprime el formulario de activacion de la licencia
		 *
		 * @since    1.0.0
		 */	

		public function add_license_manager_tab(){
			
			$parent_plugin_slug_underscore = str_replace("-","_", $this->parent_plugin_slug);

			add_action( $parent_plugin_slug_underscore . '_settings_tab', array($this,'rc_client_license_manager_license_tab'));
			add_action( $parent_plugin_slug_underscore . '_settings_content', array($this,'rc_client_license_manager_license_content') );

		}

		function rc_client_license_manager_license_tab(){
			require_once $this->plugin_path . 'partials/rc-client-license-manager-tabs.php';
		}

		function rc_client_license_manager_license_content(){
			require_once $this->plugin_path . 'partials/rc-client-license-manager-license-form.php';
		}

		/**
		 * Funcion que imprime el banner de estado de licencia
		 *
		 * @since    1.0.0
		 */	

		public function get_license_status_banner(){
			require_once $this->plugin_path . 'partials/rc-client-license-manager-banner.php';
		}

		/**
		 * Gestion de las llamadas de activacion/desactivacion
		 */

		function gestion_peticiones()
		{
			/*
						ACTIVACION
			*/
			if(isset($_POST[$this->parent_plugin_slug . '_client_license_manager_bt_activate']))
			{
				// Verificamos el codigo nonce
				if ( ! isset( $_POST['rcclm_license'] ) 
				|| ! wp_verify_nonce( $_POST['rcclm_license'], 'license_key' ) )
				{
					wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=activate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg=nonce'));
				}  
				else 
				{
					// Comprobamos que el array de productos trae al menos 1
					$license_key = sanitize_text_field($_POST[$this->parent_plugin_slug . '_rcclm_license_key']);

					if (!empty($license_key))
					{
						// Inicializamos un nuevo objeto de clave de licencia
						$this->__init(	$_SERVER['SERVER_NAME'],
										$license_key);
						
						// Guardamos el LK
						//update_option("rc_recargo_equivalencia_license_key", $license_key);

						//$new_license_manager->set_license_key($license_key);
						
						//Activamos el LK
						$retorno = $this->activate_license_key();

						if ($retorno[0])	
							wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=activate-lc-ok'));
						else
							wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=activate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg='.$retorno[1]));

				}
				else
					wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=activate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg=noLK'));
				}

				exit();
			}

			/*
						DESACTIVACION
			*/

			if(isset($_POST[$this->parent_plugin_slug . '_client_license_manager_bt_deactivate']))
			{
				// Verificamos el codigo nonce
				if ( ! isset( $_POST['rcclm_license'] ) 
				|| ! wp_verify_nonce( $_POST['rcclm_license'], 'license_key' ) )
				{
					wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=deactivate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg=nonce'));
					exit;
				}  
				else 
				{
					// Comprobamos que el array de productos trae al menos 1
					$license_key = sanitize_text_field($_POST[$this->parent_plugin_slug . '_rcclm_license_key']);

					if (!empty($license_key))
					{
						// Inicializamos un nuevo objeto de clave de licencia
						$this->__init(	$_SERVER['SERVER_NAME'],
										$license_key);
																	
						//Desactivamos el LK
						$retorno = $this->deactivate_license_key();

						if ($retorno[0])	
							wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=deactivate-lc-ok'));
						else
							wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=deactivate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg='.$retorno[1]));

					}
					else
						wp_safe_redirect(get_home_url(get_current_blog_id() ,'/wp-admin/admin.php?page='.$this->parent_plugin_slug.'&tab=license&'.$this->parent_plugin_slug.'rcclm-status=deactivate-lc-ko&'.$this->parent_plugin_slug.'rcclm-msg=noLK'));
				}
			}

			/*
							MENSAJES
			*/

			if (isset($_GET[$this->parent_plugin_slug .'rcclm-status']))
			{	
				switch ($_GET[$this->parent_plugin_slug . 'rcclm-status']) 
				{ 
					case 'activate-lc-ok':
						add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments',__("License key activated successfully",$this->PLUGIN_SLUG), 'success');
						break;
			
					case 'activate-lc-ko':
						switch ($_GET[$this->parent_plugin_slug . 'rcclm-msg']){
							case 'noLK':
								add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments', __("Error activating license: Empty License key",$this->PLUGIN_SLUG), 'error');
								break;
							default:
								add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments', __("Error activating license: ",$this->PLUGIN_SLUG) . $this->get_err_code_result($_GET[$this->parent_plugin_slug. 'rcclm-msg']), 'error');
								break;
						}
						break;
			
					case 'deactivate-lc-ok':
						add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments',__("License key was deactivated successfully",$this->PLUGIN_SLUG), 'success');
						break;
			
					case 'deactivate-lc-ko':
						switch ($_GET[$this->parent_plugin_slug . 'rcclm-msg']){
							case 'noLK':
								add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments', __("Error deactivating license: Empty License key",$this->PLUGIN_SLUG) , 'error');
								break;
							default:
								add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments', __("Error deactivating license: ",$this->PLUGIN_SLUG) . $this->get_err_code_result($_GET[$this->parent_plugin_slug . 'rcclm-msg']), 'error');
								break; 
						}
						break;
					default:
						add_settings_error($this->parent_plugin_slug . '-client-license-manager-alerts', 'message-comments', __("Unrecognized message: ",$this->PLUGIN_SLUG). $_GET['rcclm-msg'], 'error');
						break;
				
				}

			}
		}
	}
}
