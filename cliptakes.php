<?php

/**
 *
 * @link              https://cliptakes.com
 * @since             1.0.0
 * @package           Cliptakes
 *
 * @wordpress-plugin
 * Plugin Name:       Cliptakes
 * Plugin URI:        https://www.cliptakes.com
 * Description:       Intuitive All-in-one Video Interview and Editing Plugin. Saving Recruiters Time and Capturing Talent. Masterfully.
 * Version:           1.3.4
 * Author:            Cliptakes Ltd
 * Author URI:        https://cliptakes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       cliptakes
 * Domain Path:       /cliptakes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CLIPTAKES_VERSION', '1.3.4' );

function activate_cliptakes( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cliptakes-activator.php';
	Cliptakes_Activator::activate( $network_wide );
}

function deactivate_cliptakes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cliptakes-deactivator.php';
	Cliptakes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cliptakes' );
register_deactivation_hook( __FILE__, 'deactivate_cliptakes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cliptakes.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_cliptakes() {

	$plugin = new Cliptakes();
	$plugin->run();

}
run_cliptakes();
