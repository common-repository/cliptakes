<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 * @author     Cliptakes <info@cliptakes.com>
 */
class Cliptakes {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cliptakes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CLIPTAKES_VERSION' ) ) {
			$this->version = CLIPTAKES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cliptakes';

		$this->load_dependencies();
		$this->check_for_updates();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cliptakes_Loader. Orchestrates the hooks of the plugin.
	 * - Cliptakes_i18n. Defines internationalization functionality.
	 * - Cliptakes_Admin. Defines all hooks for the admin area.
	 * - Cliptakes_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cliptakes-loader.php';

		/**
		 * The class responsible for running necessary database updates.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cliptakes-update-manager.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cliptakes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cliptakes-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cliptakes-public.php';

		$this->loader = new Cliptakes_Loader();

	}

	/**
	 * Check if the plugin was updated and run necessary database updates.
	 *
	 * @since    1.3.2
	 * @access   private
	 */
	private function check_for_updates() {

		$plugin_update_manager = new Cliptakes_Update_Manager();

		$this->loader->add_action( 'plugins_loaded', $plugin_update_manager, 'check_for_updates' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cliptakes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cliptakes_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cliptakes_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'page_init' );
		$this->loader->add_action( 'current_screen', $plugin_admin, 'enqueue_plugin_page_script' );
		
		$this->loader->add_filter( 'plugin_action_links_cliptakes/cliptakes.php', $plugin_admin, 'add_action_links');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cliptakes_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'cliptakes_input_first_name', $plugin_public, 'cliptakes_input_first_name_func' );
		$this->loader->add_shortcode( 'cliptakes_input_last_name', $plugin_public, 'cliptakes_input_last_name_func' );
		$this->loader->add_shortcode( 'cliptakes_input_email', $plugin_public, 'cliptakes_input_email_func' );
		$this->loader->add_shortcode( 'cliptakes_input_accept_policy', $plugin_public, 'cliptakes_input_accept_policy_func' );
		$this->loader->add_shortcode( 'cliptakes_custom_input', $plugin_public, 'cliptakes_custom_input_func' );
		$this->loader->add_shortcode( 'cliptakes_custom_select', $plugin_public, 'cliptakes_custom_select_func' );
		$this->loader->add_shortcode( 'cliptakes_interview', $plugin_public, 'cliptakes_interview_func' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cliptakes_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
