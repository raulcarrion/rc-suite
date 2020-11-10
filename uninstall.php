<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Borramos el directorios para subir CSVs
rmdir(PLUGIN_PATH_UPLOAD_FILES);
