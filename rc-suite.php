<?php

/**
 * @link              https://www.raulcarrion.com/
 * @since             1.0.0
 * @package           Rc_Suite
 *
 * @wordpress-plugin
 * Plugin Name:       RC Suite
 * Plugin URI:        https://www.raulcarrion.com/plugins/rc-suite/
 * Description:       Funcionalidades para mejorar tu sitio web.
 * Version:           1.1.7
 * Author:            Raúl Carrión
 * Author URI:        https://www.raulcarrion.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rc-suite
 * Domain Path:       /languages
 * Payment:           Free
 */

// Si se llama directamente no dejamos continuar
if ( ! defined( 'WPINC' ) ) {
	die; 
}

define( 'RC_SUITE_VERSION', '1.1.7' );
define( 'RC_PLUGIN_PRODUCT_ID', 'RCS' );
define( 'RC_PLUGIN_SLUG'	  , 'rc-suite');
define( 'PLUGIN_PATH_UPLOAD_FILES', wp_upload_dir()['basedir'] . "/rcsu_files/");

function activate_rc_suite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rc-suite-activator.php';
	Rc_Suite_Activator::activate();
}

function deactivate_rc_suite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rc-suite-deactivator.php';
	Rc_Suite_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rc_suite' );
register_deactivation_hook( __FILE__, 'deactivate_rc_suite' );

require plugin_dir_path( __FILE__ ) . 'includes/class-rc-suite.php';

/**
 * The class responsible for manage license key of the plugin.
 */
/*require_once plugin_dir_path( __FILE__ ) . 'lib/rc-client-license-manager/class-rc-client-license-manager.php';

$rcc_license_manager = new Rc_Client_License_Manager(RC_PLUGIN_SLUG,
													 RC_PLUGIN_PRODUCT_ID,
													 "https://www.raulcarrion.com/index.php"); // Obtenemos los datos de la licencia*/

/** Uncomment for plugin updates **/
require_once plugin_dir_path( __FILE__ ) . 'lib/wp-package-updater/class-wp-package-updater.php';

/** Enable plugin updates with license check **/
$rc_comments = new WP_Package_Updater(  "https://www.raulcarrion.com/", //Servidor de actualizacion,
										wp_normalize_path( __FILE__ ),
										wp_normalize_path( plugin_dir_path( __FILE__ ) ),
										false,
										RC_PLUGIN_PRODUCT_ID,
										"", // License Number,
										$_SERVER['SERVER_NAME']
				);

function run_rc_suite() {

	$plugin = new Rc_Suite();
	$plugin->run();

}
run_rc_suite();
