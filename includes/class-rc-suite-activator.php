<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 */

/**
 * @since      1.0.0
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 * @author     Raúl Carrión <hola@raulcarrion.com>
 */
class Rc_Suite_Activator {

	/**
	 * @since    1.0.0
	 */
	public static function activate() {

		//Creamos el directorios para subir CSVs
		if (!file_exists(PLUGIN_PATH_UPLOAD_FILES))
		{
			mkdir(PLUGIN_PATH_UPLOAD_FILES);
		}
	}

}
