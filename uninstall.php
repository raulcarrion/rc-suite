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
rmdir(wp_upload_dir()['basedir'] . "/rcsu_files/");
