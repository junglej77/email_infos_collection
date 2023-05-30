<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://grdtest.com:81
 * @since             1.0.0
 * @package           Email_infos_collection
 *
 * @wordpress-plugin
 * Plugin Name:       email infos collection
 * Plugin URI:        https://grdtest.com:81
 * Description:       这是用来收集客户邮箱，电话，和其他自定义社媒信息。
 * Version:           1.0.0
 * Author:            grdtest.com:81
 * Author URI:        https://grdtest.com:81
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       email_infos_collection
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EMAIL_INFOS_COLLECTION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-email_infos_collection-activator.php
 */
function activate_email_infos_collection() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-email_infos_collection-activator.php';
	Email_infos_collection_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-email_infos_collection-deactivator.php
 */
function deactivate_email_infos_collection() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-email_infos_collection-deactivator.php';
	Email_infos_collection_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_email_infos_collection' );
register_deactivation_hook( __FILE__, 'deactivate_email_infos_collection' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-email_infos_collection.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_email_infos_collection() {

	$plugin = new Email_infos_collection();
	$plugin->run();

}
run_email_infos_collection();
