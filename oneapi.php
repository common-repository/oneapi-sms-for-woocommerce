<?php

/**
 * @wordpress-plugin
 * Plugin Name:       OneAPI
 * Plugin URI:        http://oneapi.ru/
 * Description:       SMS, Voice, Payment capability.
 * Version:           1.0.0
 * Author:            OneAPI
 * Author URI:        http://oneapi.ru/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       oneapi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if (!defined('ONEAPI_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('ONEAPI_LOG_DIR', $upload_dir['basedir'] . '/oneapi-logs/');
}

if (!defined('ONEAPI_PLUGIN_DIR_PATH')) {
    define('ONEAPI_PLUGIN_DIR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oneapi-activator.php
 */
function activate_oneapi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oneapi-activator.php';
	Oneapi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oneapi-deactivator.php
 */
function deactivate_oneapi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oneapi-deactivator.php';
	Oneapi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_oneapi' );
register_deactivation_hook( __FILE__, 'deactivate_oneapi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-oneapi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_oneapi() {

	$plugin = new Oneapi();
	$plugin->run();

}
run_oneapi();
