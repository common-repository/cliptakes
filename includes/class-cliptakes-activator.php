<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 * @author     Cliptakes <info@cliptakes.com>
 */
class Cliptakes_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
            return;
        }
		if ( !get_option('cliptakes_plugin_version') && !get_option('cliptakes_api_settings_options')) {
			set_transient( 'cliptakes_activation_redirect', true, MINUTE_IN_SECONDS );
		}

		/**
		 * 1.3.2 - Initialize all Cliptakes roles and capabilities.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cliptakes-capability-manager.php';
		$ct_cap_manager = new Cliptakes_Capability_Manager();
		$ct_cap_manager->init_roles_and_capabilities();
		
		update_option('cliptakes_plugin_version', CLIPTAKES_VERSION);
	}

}
