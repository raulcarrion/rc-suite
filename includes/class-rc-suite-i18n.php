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
class Rc_Suite_i18n {


	/**
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rc-suite',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
