<?php

/**
 * Cliptakes Roles and Capabilities
 *
 * Defines and initializes user roles and capabilities for this plugin.
 *
 * @link       https://cliptakes.com
 * @since      1.3.2
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 */

/**
 * Cliptakes Roles and Capabilities
 *
 * Defines and initializes user roles and capabilities for this plugin.
 *
 * @since      1.3.2
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 * @author     Cliptakes <info@cliptakes.com>
 */
class Cliptakes_Capability_Manager {

	/**
	 * List of Cliptakes capabilities.
	 *
	 * @since    1.3.2
	 * @access   private
	 * @var      array
	 */
	private $all_capabilities = array(
        'manage_cliptakes-general-settings',
        'manage_cliptakes-interview-templates',
        'manage_cliptakes-email-notifications',
        'manage_cliptakes-recorded-interviews',
        'manage_cliptakes-api-settings'
    );

    /**
	 * Get Cliptakes roles.
	 *
	 * @since    1.3.2
	 */
	private function get_roles() {
        return array(
            'cliptakes_manager' => array(
                'title' => 'Cliptakes Manager',
                'capabilities'  => $this->all_capabilities
            ),
            'cliptakes_editor' => array(
                'title' => 'Cliptakes Editor',
                'capabilities'  => array_diff($this->all_capabilities, array('manage_cliptakes-api-settings'))
            )
            );
    }

    /**
     * Initialize Cliptakes roles and capabilities
     *
     * @since 1.3.2
     */
    public function init_roles_and_capabilities() {
		global $wp_roles;

		if ( !class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( !isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
		}

        foreach ( $this->get_roles() as $role_id => $role_properties ) {
            $wp_roles->remove_role( $role_id );
            $capabilities = array_combine($role_properties['capabilities'], array_fill(0, count($role_properties['capabilities']), true));
            add_role( $role_id, $role_properties['title'], $capabilities );
        }

        foreach ( $this->all_capabilities as $capability ) {
            $wp_roles->add_cap( 'administrator', $capability );
        }
    }

    /**
     * Remove Cliptakes roles and capabilities (only for uninstalling the plugin!)
     *
     * @since 1.3.2
     */
    public function delete_roles_and_capabilities() {
		global $wp_roles;

		if ( !class_exists( 'WP_Roles' ) || !isset( $wp_roles ) ) {
			return;
		}

        foreach ( $this->get_roles() as $role_id => $role_properties ) {
            $wp_roles->remove_role( $role_id );
        }

        foreach ( $this->all_capabilities as $capability ) {
            $wp_roles->remove_cap( 'administrator', $capability );
        }
    }

}
