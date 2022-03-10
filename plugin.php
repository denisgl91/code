<?php
/**
 * Example of plugin file
 *
 * @since   1.0.0
 * @package Some plugin
 * @author  DHL
 *
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
define( 'SOME_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-copart-activator.php
 */
function activate_copart() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-copart-activator.php';
	Copart_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-copart-deactivator.php
 */
function deactivate_copart() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-copart-deactivator.php';
	Copart_Deactivator::deactivate();
}

register_activation_hook(   __FILE__, 'activate_copart' );
register_deactivation_hook( __FILE__, 'deactivate_copart' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-copart.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_copart() {
	$plugin = new Copart();
	$plugin->run();
}
run_copart();
