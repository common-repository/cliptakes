<?php

/**
 * Cliptakes Update Manager
 *
 * Checks if the plugin was updated and runs necessary database updates.
 *
 * @link       https://cliptakes.com
 * @since      1.3.2
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 */

/**
 * Cliptakes Update Manager
 *
 * Checks if the plugin was updated and runs necessary database updates.
 *
 * @since      1.3.2
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 * @author     Cliptakes <info@cliptakes.com>
 */
class Cliptakes_Update_Manager {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.3.2
	 */
	public function check_for_updates() {

        if (CLIPTAKES_VERSION !== get_option('cliptakes_plugin_version')) {
            require_once plugin_dir_path( __FILE__ ) . 'class-cliptakes-activator.php';
            Cliptakes_Activator::activate( false );
		}

	}



}
