<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

$cliptakes_options = array(
	'cliptakes_general_settings_options',
	'cliptakes_api_settings_options',
	'cliptakes_subscription',
	'cliptakes_plugin_version'
);
foreach ($cliptakes_options as $option) {
	if (get_option($option)) delete_option($option);
}

/**
 * The class responsible for defining and initializing all Cliptales roles and capabilities.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cliptakes/includes/class-cliptakes-capability-manager.php';
$ct_cap_manager = new Cliptakes_Capability_Manager();
$ct_cap_manager->delete_roles_and_capabilities();
