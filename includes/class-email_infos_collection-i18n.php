<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://grdtest.com:81
 * @since      1.0.0
 *
 * @package    Email_infos_collection
 * @subpackage Email_infos_collection/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Email_infos_collection
 * @subpackage Email_infos_collection/includes
 * @author     grdtest.com:81 <447494332@qq.com>
 */
class Email_infos_collection_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'email_infos_collection',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
