<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin
 * @author     Cliptakes <info@cliptakes.com>
 */
class Cliptakes_Admin {
	private $cliptakes_options;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->tinymce_settings = array(
			'toolbar1' => 'undo redo styleselect formatselect bold italic bullist numlist alignleft aligncenter alignjustify'
		);
		
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cliptakes-interview-elements.php');
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-interview-list-table.php');

		add_action( 'cliptakes_general_settings_page_created', array( $this, 'cliptakes_general_settings_init' ), 10 );
		add_action( 'cliptakes_api_settings_page_created', array( $this, 'cliptakes_api_settings_init' ), 10 );

		add_action( 'wp_ajax_cliptakes_create_account', array( $this, 'cliptakes_create_account'), 10);
		
		add_action( 'wp_ajax_cliptakes_reset_intro_html', array( $this, 'cliptakes_reset_intro_html_handler'), 10 );
		add_action( 'wp_ajax_cliptakes_reset_signup_html', array( $this, 'cliptakes_reset_signup_html_handler'), 10 );
		add_action( 'wp_ajax_cliptakes_reset_upload_before_html', array( $this, 'cliptakes_reset_upload_before_html_handler'), 10 );
		add_action( 'wp_ajax_cliptakes_reset_upload_after_html', array( $this, 'cliptakes_reset_upload_after_html_handler'), 10 );
		
		add_action( 'wp_ajax_cliptakes_fetch_interview_data', array( $this, 'cliptakes_fetch_interview_data_handler'), 10 );
		add_action( 'wp_ajax_cliptakes_interview_data_display', array( $this, 'cliptakes_interview_data_display_handler'), 10 );
		add_action( 'wp_ajax_cliptakes_create_embed_page', array( $this, 'cliptakes_create_embed_page'), 10);

		add_action( 'add_option_cliptakes_api_settings_options', array( $this, 'get_subscription_status'), 10 );
		add_action( 'update_option_cliptakes_api_settings_options', array( $this, 'get_subscription_status'), 10 );
		add_action( 'wp_ajax_cliptakes_update_subscription_status', array( $this, 'get_subscription_status'), 10 );

		add_action( 'wp_ajax_cliptakes_send_deactivation_feedback', array( $this, 'send_deactivation_feedback'), 10 );

		$cliptakes_subscription = get_option('cliptakes_subscription');
		$this->subscription = $cliptakes_subscription ? $cliptakes_subscription : [];
		$this->active_tab = 'general_settings';
	}

	public function enqueue_plugin_page_script() {
		$currentScreen = get_current_screen();
		if( $currentScreen->id === "plugins" ) {
			wp_enqueue_style( $this->plugin_name . '-plugin-page', plugin_dir_url( __FILE__ ) . 'css/cliptakes-plugin-page.css', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name . '-plugin-page', plugin_dir_url( __FILE__ ) . 'js/cliptakes-plugin-page.js', array( 'jquery' ), $this->version, false );
			
			wp_localize_script( $this->plugin_name . '-plugin-page', 'cliptakes_ajax_obj', array(
				'nonce'    => wp_create_nonce('cliptakes_settings')
			));

			include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cliptakes-plugin-page-script-i18n.php');
			wp_localize_script( $this->plugin_name . '-plugin-page', 'cliptakes_i18n', $CLIPTAKES_PLUGIN_PAGE_SCRIPT_i18n);
		}
	}

	function send_deactivation_feedback() {
		check_ajax_referer( 'cliptakes_settings' );
		$reason = sanitize_text_field( $_REQUEST['reason'] );
        $additional_info = sanitize_text_field ( $_REQUEST['additional_info'] );
        $email = sanitize_email( $_REQUEST['email'] );

		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/publicApi';
		$endpoint = '/v1/logPluginDeactivationFeedback';
		$params = array(
			'subscription' => get_option('cliptakes_api_settings_options'),
			'reason' => $reason,
			'additionalInfo' => $additional_info,
			'email' => $email
		);
		$response = $this->post_request($params, $api_url, $endpoint);
		if ($response['http_code'] != 200) {
			wp_send_json_error();
		}
		wp_send_json_success();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cliptakes-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cliptakes-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
		$subscription_info = get_option('cliptakes_api_settings_options');
		$subscription_info = array_merge($this->subscription, $subscription_info ? $subscription_info : []);
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cliptakes-admin-script-i18n.php');

		wp_localize_script( $this->plugin_name, 'cliptakes_ajax_obj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce('cliptakes_settings'),
			'subscription' => $subscription_info
		));
		wp_localize_script( $this->plugin_name, 'cliptakes_i18n', $CLIPTAKES_ADMIN_SCRIPT_i18n);
	}

	public function page_init() {
		// Redirect to Cliptakes settings after plugin activation
		if ( ! get_transient( 'cliptakes_activation_redirect' ) ) {
            return;
		}
		if ( wp_doing_ajax() ) {
			return;
		}

        delete_transient( 'cliptakes_activation_redirect' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

        wp_safe_redirect( admin_url( 'admin.php?page=cliptakes-settings&get-started=1' ) );
        exit;
	}

	public function add_action_links( $actions ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=cliptakes-settings' ) . '">' . __('Settings', 'cliptakes') . '</a>',
			'<a href="https://cliptakes.com/support-ticket/" target="_blank">' . __('Support', 'cliptakes') . '</a>',
		);
		$actions = array_merge( $mylinks, $actions );
		return $actions;
	}

	public function add_menu_page() {
		$this->cliptakes_general_settings_options = get_option('cliptakes_general_settings_options');
		$this->cliptakes_api_settings_options = get_option('cliptakes_api_settings_options');

		$cliptakes_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjxzdmcgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgMTc1IDE3NSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+DQo8cGF0aCBzdHlsZT0iZmlsbDojZmZmZmZmIiBkPSJtODQuOTU5IDBjLTExLjUzNCAwLTIyLjA5MyAyLjU4NC0zMS42NzggNy43NTItOS40MjIyIDUuMTY3OS0xNi44OTUgMTIuMjM1LTIyLjQxOCAyMS4yMDMtNS41MjMzIDguODE1OS04LjI4NTIgMTguNjk3LTguMjg1MiAyOS42NDEgM2UtNiAxMC45NDQgMi43NjE4IDIwLjg5OSA4LjI4NTIgMjkuODY3IDUuNTIzMyA4LjgxNTkgMTIuOTk2IDE1LjgwOSAyMi40MTggMjAuOTc3IDkuNTg0NiA1LjE2NzkgMjAuMTQ0IDcuNzUyIDMxLjY3OCA3Ljc1MiA5Ljc0NzEgMCAxOC41MTktMS42NzM2IDI2LjMxNi01LjAxNzYgNy45NjAxLTMuNDk2IDE0LjcwMy04LjM1OTkgMjAuMjI3LTE0LjU5MmwtMTguMDMzLTE3LjFjLTcuMzEwMyA4LjY2MzktMTYuODEzIDEyLjk5Ni0yOC41MSAxMi45OTYtNi4zMzU2IDAtMTIuMjY2LTEuNDQ0MS0xNy43ODktNC4zMzItNS4zNjA5LTMuMDQtOS43NDY3LTcuMTQyNi0xMy4xNTgtMTIuMzExLTMuMjQ5LTUuMzE5OS00Ljg3My0xMS40LTQuODczLTE4LjI0czEuNjI0LTEyLjg0NCA0Ljg3My0xOC4wMTJjMy40MTE1LTUuMzE5OSA3Ljc5NzMtOS40MjQ1IDEzLjE1OC0xMi4zMTIgNS41MjMzLTMuMDQgMTEuNDUzLTQuNTYwNSAxNy43ODktNC41NjA1IDExLjY5NyAwIDIxLjE5OSA0LjMzMjIgMjguNTEgMTIuOTk2bDE4LjAzMy0xNy4xYy01LjUyMzMtNi4yMzE5LTEyLjI2Ni0xMS4wMTktMjAuMjI3LTE0LjM2My03Ljc5NzctMy40OTYtMTYuNTY5LTUuMjQ0MS0yNi4zMTYtNS4yNDQxem0tNDQuNDA4IDExMWMtMjUgMTUtNDAuNTQyIDQ1LjA3Mi00MC41NTEgNTkuNTI3aDEzMS45M2MtMTIuMzc5LTE4LjUyOC0zLjM3OS0zNy41MjcgMTQuNjE3LTQzLjUyNS01LjM3NjktNi4wODMtMTEuMjIxLTExLjY1My0xNy4wMi0xNi4wMDItMjMuOTc2IDIwLTYzLjk3NyAyMC04OC45NzcgMHptMTEyIDI0LjVjLTkuNjY1IDAtMTcuNSA3LjgzNS0xNy41IDE3LjUgMCA5LjY2NSA3LjgzNSAxNy41IDE3LjUgMTcuNSA5LjY2NSAwIDE3LjUtNy44MzUgMTcuNS0xNy41IDAtOS42NjUtNy44MzUtMTcuNS0xNy41LTE3LjV6Ii8+DQo8L3N2Zz4NCg==';

		$parent_capability = 'manage_options';
		$this->main_tab = 'general_settings';
		if ( ! current_user_can( 'manage_options' ) ) {
			if ( current_user_can( 'manage_cliptakes-general-settings' ) ) {
				$parent_capability = 'manage_cliptakes-general-settings';
			} elseif ( current_user_can( 'manage_cliptakes-interview-templates' ) ) {
				$parent_capability = 'manage_cliptakes-interview-templates';
				$this->main_tab = 'templates';
			} elseif ( current_user_can( 'manage_cliptakes-email-notifications' ) ) {
				$parent_capability = 'manage_cliptakes-email-notifications';
				$this->main_tab = 'contacts';
			} elseif ( current_user_can( 'manage_cliptakes-recorded-interviews' ) ) {
				$parent_capability = 'manage_cliptakes-recorded-interviews';
				$this->main_tab = 'interviews';
			} elseif ( current_user_can( 'manage_cliptakes-api-settings' ) ) {
				$parent_capability = 'manage_cliptakes-api-settings';
				$this->main_tab = 'api_settings';
			}
		}

		add_menu_page(
			'Cliptakes', // page_title
			'Cliptakes', // menu_title
			$parent_capability, // capability
			'cliptakes-settings', // menu_slug
			function () {$this->get_menu_page_callback($this->main_tab);}, // function
			$cliptakes_icon, // icon_url
			65 // position
		);

		add_submenu_page( 
			'cliptakes-settings', // parent_slug
			__('General Settings', 'cliptakes'), // page_title
			__('General Settings', 'cliptakes'), // menu_title
			'manage_cliptakes-general-settings', // capability
			'cliptakes-settings' // menu_slug
		);

		add_submenu_page( 
			'cliptakes-settings', // parent_slug
			__('Interview Templates', 'cliptakes'), // page_title
			__('Interview Templates', 'cliptakes'), // menu_title
			'manage_cliptakes-interview-templates', // capability
			'cliptakes-templates', // menu_slug
			function () {$this->get_menu_page_callback('templates');} // function
		);

		add_submenu_page( 
			'cliptakes-settings', // parent_slug
			__('Email Notifications', 'cliptakes'), // page_title
			__('Email Notifications', 'cliptakes'), // menu_title
			'manage_cliptakes-email-notifications', // capability
			'cliptakes-contacts', // menu_slug
			function () {$this->get_menu_page_callback('contacts');} // function
		);

		add_submenu_page( 
			'cliptakes-settings', // parent_slug
			__('Recorded Interviews', 'cliptakes'), // page_title
			__('Recorded Interviews', 'cliptakes'), // menu_title
			'manage_cliptakes-recorded-interviews', // capability
			'cliptakes-interviews', // menu_slug
			function () {$this->get_menu_page_callback('interviews');} // function
		);

		add_submenu_page( 
			'cliptakes-settings', // parent_slug
			__('API Settings', 'cliptakes'), // page_title
			__('API Settings', 'cliptakes'), // menu_title
			'manage_cliptakes-api-settings', // capability
			'cliptakes-api-settings', // menu_slug
			function () {$this->get_menu_page_callback('api_settings');} // function
		);
		
		do_action( 'cliptakes_general_settings_page_created' );
		do_action( 'cliptakes_templates_page_created' );
		do_action( 'cliptakes_contacts_page_created' );
		do_action( 'cliptakes_interviews_page_created' );
		do_action( 'cliptakes_api_settings_page_created' );
	}

	function get_subscription_status() {	
		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/plugin';
		$endpoint = '/v1/authorize';
		$params = array(
			'subscriptionId' => get_option('cliptakes_api_settings_options')['subscription_id']
		);
		$response = $this->post_request($params, $api_url, $endpoint);
		$body = json_decode($response['body']);
		$this->subscription = array(
			'status' => intval($body->status) ? true : false,
			'product' => $body->product
		);
		update_option('cliptakes_subscription', $this->subscription);
	}

	public function get_menu_page_callback($tab = null) {
		if( ! wp_script_is( $this->plugin_name , $list = 'enqueued' ) ) { wp_enqueue_script($this->plugin_name); }
		if( ! wp_style_is( $this->plugin_name, $list = 'enqueued' ) ) { wp_enqueue_style($this->plugin_name); }

		if ($tab) {
			$this->active_tab = $tab;
		}

		if ( $_GET['get-started'] ) {
			include(plugin_dir_path(__FILE__) . 'partials/cliptakes-get-started.php');
		} elseif ( $_GET['create-free-account'] ) {
			include(plugin_dir_path(__FILE__) . 'partials/cliptakes-create-free-account.php');
		} else {			
			include(plugin_dir_path(__FILE__) . 'partials/cliptakes-admin-display.php');
		}
	}

	/***** Start - Tab 01 - General Settings Script ***/
	function cliptakes_general_settings_init() { 
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cliptakes-default-settings.php');           
		register_setting(
			'cliptakes_general_settings_options_group', // option_group
			'cliptakes_general_settings_options', // option_name
			array( $this, 'cliptakes_general_settings_options_sanitize' ) // sanitize_callback
		);			
		add_settings_section(
			'cliptakes_general_settings_branding_section', // id
			__('Branding', 'cliptakes'), // title
			null, // callback
			'cliptakes_general_settings_branding' // page
		);		
		add_settings_section(
			'cliptakes_general_settings_intro_section', // id
			__('Welcome Page', 'cliptakes'), // title
			null, // callback
			'cliptakes_general_settings_intro' // page
		);	
		add_settings_section(
			'cliptakes_general_settings_signup_section', // id
			__('Sign-Up Page', 'cliptakes'), // title
			null, // callback
			'cliptakes_general_settings_signup' // page
		);
		add_settings_section(
			'cliptakes_general_settings_recording_section', // id
			__('Recording', 'cliptakes'), // title
			null, // callback
			'cliptakes_general_settings_recording' // page
		);
		add_settings_section(
			'cliptakes_general_settings_upload_section', // id
			__('Upload Page', 'cliptakes'), // title
			null, // callback
			'cliptakes_general_settings_upload' // page
		);

		add_settings_field( 
			'question_size', 
			__('Question Size', 'cliptakes'), 
			array( $this, 'question_size_callback' ), // callback
			'cliptakes_general_settings_branding', 
			'cliptakes_general_settings_branding_section'
		);
		add_settings_field( 
			'main_color', 
			__('Main Color', 'cliptakes'), 
			array( $this, 'main_color_callback' ), // callback
			'cliptakes_general_settings_branding', 
			'cliptakes_general_settings_branding_section'
		);
		add_settings_field( 
			'logo_file', 
			__('Logo', 'cliptakes'), 
			array( $this, 'logo_file_callback' ), // callback
			'cliptakes_general_settings_branding', 
			'cliptakes_general_settings_branding_section'
		);

		add_settings_field( 
			'intro_html_markup', 
			__('Intro Section', 'cliptakes'), 
			array( $this, 'intro_html_markup_callback' ), // callback
			'cliptakes_general_settings_intro', 
			'cliptakes_general_settings_intro_section'
		);
		add_settings_field( 
			'intro_next_button', 
			__('Intro Continue Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_intro', 
			'cliptakes_general_settings_intro_section',
			array('setting_id' => 'intro_next_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['intro_next_button'])
		);

		add_settings_field( 
			'signup_html_markup', 
			__('Sign-Up Form', 'cliptakes'), 
			array( $this, 'signup_html_markup_callback' ), // callback
			'cliptakes_general_settings_signup', 
			'cliptakes_general_settings_signup_section'
		);
		add_settings_field( 
			'signup_submit_button', 
			__('Submit Sign-Up Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_signup', 
			'cliptakes_general_settings_signup_section',
			array('setting_id' => 'signup_submit_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['signup_submit_button'])
		);

		add_settings_field( 
			'setup_next_button', 
			__('Finish Setup Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_recording', 
			'cliptakes_general_settings_recording_section',
			array('setting_id' => 'setup_next_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['setup_next_button'])
		);
		add_settings_field( 
			'upload_video_button', 
			__('Upload Video Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_recording', 
			'cliptakes_general_settings_recording_section',
			array('setting_id' => 'upload_video_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['upload_video_button'])
		);
		add_settings_field( 
			'retake_video_button', 
			__('Retake Video Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_recording', 
			'cliptakes_general_settings_recording_section',
			array('setting_id' => 'retake_video_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['retake_video_button'])
		);
		add_settings_field( 
			'next_question_button', 
			__('Next Question Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_recording', 
			'cliptakes_general_settings_recording_section',
			array('setting_id' => 'next_question_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['next_question_button'])
		);
		add_settings_field( 
			'timelimit_info_text', 
			__('Timelimit Info Text', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_recording', 
			'cliptakes_general_settings_recording_section',
			array(
				'setting_id' => 'timelimit_info_text',
				'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['timelimit_info_text'],
				'info_text' => __('This text will be added to questions with a set time limit.', 'cliptakes') . '<br>
' . sprintf(__('Place %s to display the set time in seconds. Example: Our default text %s displays %s after a question with a 90 second limit.', 'cliptakes'), '<b>[t]</b>', '<b>' . sprintf(_x('(Max. %s Seconds)', 'Default text to be added to questions with a set timelimit.', 'cliptakes'), '[t]') . '</b>', '<b>' . sprintf(_x('(Max. %s Seconds)', 'Default text to be added to questions with a set timelimit.', 'cliptakes'), '90') . '</b>') . '<br>
' . sprintf(__('Alternatively place %s and %s to display minutes and seconds separately for a "digital clock"-format. Example: %s displays %s after a question with a 90 second limit.', 'cliptakes'), '<b>[m]</b>', '<b>[s]</b>',
'<b>' . sprintf(_x('(Max. %s:%s)', 'Timelimit with minute / seconds: (Max. [m]:[s])', 'cliptakes'), '[m]', '[s]') . '</b>',
'<b>' . sprintf(_x('(Max. %s:%s)', 'Timelimit with minute / seconds: (Max. [m]:[s])', 'cliptakes'), '1', '30') . '</b>')
			)
		);

		add_settings_field( 
			'upload_before_html_markup', 
			__('"Ready for Upload" Section', 'cliptakes'), 
			array( $this, 'upload_before_html_markup_callback' ), // callback
			'cliptakes_general_settings_upload', 
			'cliptakes_general_settings_upload_section'
		);
		add_settings_field( 
			'upload_interview_button', 
			__('Upload Interview Button', 'cliptakes'), 
			array( $this, 'setting_text_input_callback' ), // callback
			'cliptakes_general_settings_upload', 
			'cliptakes_general_settings_upload_section',
			array('setting_id' => 'upload_interview_button', 'default_value' => $CLIPTAKES_DEFAULT_SETTINGS['upload_interview_button'])
		);
		add_settings_field( 
			'upload_after_html_markup', 
			__('"Upload Successful" Section', 'cliptakes'), 
			array( $this, 'upload_after_html_markup_callback' ), // callback
			'cliptakes_general_settings_upload', 
			'cliptakes_general_settings_upload_section'
		);
	}

	public function cliptakes_general_settings_options_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['main_color'] ) ) {
			$sanitary_values['main_color'] = sanitize_hex_color ($input['main_color'] );
		}
		if ( isset( $input['question_size'] ) ) {
			$question_size = sanitize_text_field ($input['question_size'] );
			if ( preg_match( "/h[1-6]/i", $question_size ) ) {
				$sanitary_values['question_size'] = strtolower( $question_size );
			} else {
				$sanitary_values['question_size'] = get_option('cliptakes_general_settings_options')['question_size'];
			}
		}
		if ( isset( $input['logo_file'] ) && !empty($_FILES["logo_file_input"]["tmp_name"])) {
			$allowed_mimes = array('image/jpeg','image/png');
			$file_info = wp_get_image_mime($_FILES["logo_file_input"]["tmp_name"]);
			if(in_array($file_info, $allowed_mimes)) {
				$upload_result = $this->handle_logo_upload(
					$_FILES["logo_file_input"]["tmp_name"],
					$input['logo_file'],
					$_FILES["logo_file_input"]["type"]
				);
				if ($upload_result) {
					$this->reset_logo();
					$sanitary_values['logo_file'] = get_option('cliptakes_api_settings_options')['subscription_id'] . '/logo.' . $input['logo_file'];
				} else {					
					$this->restore_logo_backup();
					$sanitary_values['logo_file'] = get_option('cliptakes_general_settings_options')['logo_file'];
				}
			} else {
				$sanitary_values['logo_file'] = get_option('cliptakes_general_settings_options')['logo_file'];
			}
		}

		// post content options
		$post_content_options = array(
			'intro_html_markup',
			'signup_html_markup',
			'upload_before_html_markup',
			'upload_after_html_markup'
		);
		foreach ($post_content_options as $option_id) {
			if ( isset( $input[$option_id] ) ) {
				$sanitary_values[$option_id] = wp_kses_post( $input[$option_id] );
			}
		}

		// text content options
		$text_content_options = array(
			'intro_next_button',
			'signup_submit_button',
			'setup_next_button',
			'upload_video_button',
			'retake_video_button',
			'next_question_button',
			'timelimit_info_text',
			'upload_interview_button'
		);
		foreach ($text_content_options as $option_id) {
			if ( isset( $input[$option_id] ) ) {
				$sanitary_values[$option_id] = sanitize_text_field( $input[$option_id] );
			}
		}
		return $sanitary_values;
	}

	function question_size_callback() {
		$content = empty($this->cliptakes_general_settings_options['question_size']) ? $CLIPTAKES_DEFAULT_SETTINGS['question_size'] : $this->cliptakes_general_settings_options['question_size'];
		echo '<select name="cliptakes_general_settings_options[question_size]">';
		for ($i = 1; $i <= 6; $i++) {
			$option_value = 'h' . $i;
			echo '<option value="' . esc_attr( $option_value ) . '"' . ($content === $option_value ? ' selected' : '') . '>Heading ' . esc_attr( $i ) . '</option>';
		}
		echo '</select>';
	}

	function main_color_callback() {
		$content = empty($this->cliptakes_general_settings_options['main_color']) ? $CLIPTAKES_DEFAULT_SETTINGS['main_color'] : $this->cliptakes_general_settings_options['main_color'] ;
		echo '<input type="text" class="ctadmin-color-picker" name="cliptakes_general_settings_options[main_color]" value="' . esc_attr( $content ) . '" />';
	}

	function logo_file_callback() {
		$content = isset( $this->cliptakes_general_settings_options['logo_file'] ) ?  $this->cliptakes_general_settings_options['logo_file'] : "";
		echo '<input type="text" id="ctadmin-logo-url" name="cliptakes_general_settings_options[logo_file]" value="' . esc_attr( $content ) . '" hidden/>';
		echo '<img id="ctadmin-logo-preview" ' 
		. (empty($content) ? 'hidden' : 'src="https://files.cliptakes.com/' . esc_attr( $content ) . '?nocache=' . time() . '"') 
		. '/>';
		echo '<label for="ctadmin-logo-input" class="button ctadmin-button button-disabled">
		<input id="ctadmin-logo-input" type="file" accept="image/png, image/jpeg" name="logo_file_input" disabled/>
		' . __('Upload Logo Image', 'cliptakes') . '
		</label>';
	}

	function setting_text_input_callback(array $args) {
		$content = isset( $this->cliptakes_general_settings_options[$args['setting_id']] ) ?  $this->cliptakes_general_settings_options[$args['setting_id']] : false;
		if (empty($content)) {
			$content = $args['default_value'];
		}
		echo '<input type="text" name="cliptakes_general_settings_options[' . esc_attr( $args['setting_id'] ) . ']" value="' . esc_attr ($content ) . '" />';
		if (isset($args['info_text']) && !empty($args['info_text'])) {
			$allowed_tags = array( 'br' => array(), 'b' => array(), 'i' => array() );
			echo '<div class="ctadmin-text-input-info-box">
			<span class="dashicons dashicons-info-outline" style="grid-column: 1;padding: 0 10px 0 10px;"></span>
			<div style="grid-column: 2;">' . wp_kses($args['info_text'], $allowed_tags) . '</div>
			</div>';
		}
	}

	function intro_html_markup_callback() {
		$content = isset( $this->cliptakes_general_settings_options['intro_html_markup'] ) ?  $this->cliptakes_general_settings_options['intro_html_markup'] : false;
		if (empty($content)) {
			$content = get_default_intro();
		}
		wp_editor( $content, 'intro_html_markup', array( 
			'textarea_name' => 'cliptakes_general_settings_options[intro_html_markup]',
			'media_buttons' => false,
			'tinymce' => $this->tinymce_settings,
		) );
		echo '<button id="ctadmin-reset-intro" class="button ctadmin-button">' . __('Reset Intro Page', 'cliptakes') . '</button>';
	}
	
	function signup_html_markup_callback() {
		$content = isset( $this->cliptakes_general_settings_options['signup_html_markup'] ) ?  $this->cliptakes_general_settings_options['signup_html_markup'] : false;
		if (empty($content)) {
			$content = get_default_signup();
		}
		echo '<details><summary><h4>' . __('Sign-Up Form Shortcodes', 'cliptakes') . '</h4></summary>
		<div>
		' . __('You may use the below shortcodes in this section to customise the sign-up page as required.', 'cliptakes') . '
		<pre><b>[cliptakes_input_first_name]</b></pre>
		' . sprintf(__('Generate a mandatory field for your candidate to enter their %s.', 'cliptakes'), '<b>' . __('first name', 'cliptakes') . '</b>') . '<br>
		<br>
		<pre><b>[cliptakes_input_last_name]</b></pre>
		' . sprintf(__('Generate a mandatory field for your candidate to enter their %s.', 'cliptakes'), '<b>' . __('last name', 'cliptakes') . '</b>') . '<br>
		<br>
		<pre><b>[cliptakes_input_accept_policy]</b></pre>
		' . __('Generate a mandatory tick box for your candidates to agree to the following statement: ', 'cliptakes') . __('I have read and agree to the Privacy Policy.', 'cliptakes') . '<br>
		<br>
		' . sprintf(_x('All 3 are used in our demo which can be %sviewed here%s.', 'All 3 are used in our demo which can be <START_LINK>viewed here<END_LINK>.', 'cliptakes'), '<a href="https://www.cliptakes.com/plugin-demo" target="_blank">', '</a>') . '
		</div></details>';
		wp_editor( $content, 'signup_html_markup', array( 
			'textarea_name' => 'cliptakes_general_settings_options[signup_html_markup]',
			'media_buttons' => false,
			'tinymce' => $this->tinymce_settings
		) );
		echo '<button id="ctadmin-reset-signup" class="button ctadmin-button">' . __('Reset Sign-Up Form', 'cliptakes') . '</button>';
	}
	
	function upload_before_html_markup_callback() {
		$content = isset( $this->cliptakes_general_settings_options['upload_before_html_markup'] ) ?  $this->cliptakes_general_settings_options['upload_before_html_markup'] : false;
		if (empty($content)) {
			$content = get_default_upload_before();
		}
		wp_editor( $content, 'upload_before_html_markup', array( 
			'textarea_name' => 'cliptakes_general_settings_options[upload_before_html_markup]',
			'media_buttons' => false,
			'tinymce' => $this->tinymce_settings
		) );
		echo '<button id="ctadmin-reset-upload-before" class="button ctadmin-button">' . __('Reset "Ready for Upload" Section', 'cliptakes') . '</button>';
	}
	function upload_after_html_markup_callback() {
		$content = isset( $this->cliptakes_general_settings_options['upload_after_html_markup'] ) ?  $this->cliptakes_general_settings_options['upload_after_html_markup'] : false;
		if (empty($content)) {
			$content = get_default_upload_after();
		}
		wp_editor( $content, 'upload_after_html_markup', array( 
			'textarea_name' => 'cliptakes_general_settings_options[upload_after_html_markup]',
			'media_buttons' => false,
			'tinymce' => $this->tinymce_settings
		) );
		echo '<button id="ctadmin-reset-upload-after" class="button ctadmin-button">' . __('Reset "Upload Successful" Section', 'cliptakes') . '</button>';
	}
	/*** End - Tab 01 - General Settings Script ***/

	/***** Start - Tab 05 - API Settings Script ***/
	function cliptakes_api_settings_init() {            
		register_setting(
			'cliptakes_api_settings_options_group', // option_group
			'cliptakes_api_settings_options', // option_name
			array( $this, 'cliptakes_api_settings_options_sanitize' ) // sanitize_callback
		);
		
		add_settings_section(
			'cliptakes_api_settings_options_section', // id
			__('API Settings', 'cliptakes'), // title
			array( $this, 'cliptakes_api_settings_options_section_info' ), // callback
			'cliptakes_api_settings_options' // page			
		);

		add_settings_field( 
			'subscription_id', 
			__('Subscription Number', 'cliptakes'), 
			array( $this, 'subscription_id_callback' ), // callback
			'cliptakes_api_settings_options', 
			'cliptakes_api_settings_options_section' 
		);

		add_settings_field( 
			'license_key', 
			__('License Key', 'cliptakes'), 
			array( $this, 'license_key_callback' ), // callback
			'cliptakes_api_settings_options', 
			'cliptakes_api_settings_options_section' 
		);

	}

	public function cliptakes_api_settings_options_section_info() {
		echo '<details>
		<summary><h4>' . __('How to set up the API connection', 'cliptakes') . '</h4></summary>
		<p>
		' . sprintf(_x('Upon %sregistration%s, you will receive an automated email with a unique Cliptakes Licence Key and Subscription Number.', 'Upon <START_LINK>registration<END_LINK>, you will receive an automated email with a unique Cliptakes Licence Key and Subscription Number.', 'cliptakes'), '<a href="https://www.cliptakes.com" target="_blank">', '</a>') . '<br>
		<br>
		<i>' . __('Your Subscription Number can also be found in your account settings by navigating to your “Manage Subscription” tab. If you require your Cliptakes Licence Key again, you may request this via email by clicking on the "Cliptakes Licence Key" tab in your account settings. Your licence key will be emailed to you within minutes.', 'cliptakes') . '</i><br>
		<br>
		' . __('Paste the Licence Key and Subscription Number in the fields below, check the subscription status using the button provided and click "Save Changes". After successfully authorising your account, this section will turn green, indicating that you are good to go! If you have any difficulties, use the "Check Subscription" button to find out what the issue is and how to resolve it.', 'cliptakes') . '<br>
		<br>
		' . sprintf(_x('If you require further support, please refer to our full documentation or %sraise a support ticket%s on our website.', '', 'cliptakes'), '<a href="https://cliptakes.com/support-ticket/" target="_blank">', '</a>') . ' 
		</p>
		</details>';
	}

	public function cliptakes_api_settings_options_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['subscription_id'] ) ) {
			$sanitary_values['subscription_id'] = sanitize_text_field( str_replace('#', '', $input['subscription_id']) );
		}

		if ( isset( $input['license_key'] ) ) {
			$sanitary_values['license_key'] = sanitize_text_field( $input['license_key'] );
		}
		return $sanitary_values;
	}

	function subscription_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="cliptakes_api_settings_options[subscription_id]" id="ctadmin-subscription_id" value="%s" placeholder="' . __('Your subscription number, e.g. #1234', 'cliptakes') . '">',
			(isset( $this->cliptakes_api_settings_options['subscription_id'] ) && !empty($this->cliptakes_api_settings_options['subscription_id']) ) ? '#'.esc_attr( $this->cliptakes_api_settings_options['subscription_id']) : ''
		);
	}

	function license_key_callback() {
		printf(
			'<input class="regular-text" type="text" name="cliptakes_api_settings_options[license_key]" id="ctadmin-license_key" value="%s" placeholder="' . __('Your license key, e.g. XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX', 'cliptakes') . '">',
			isset( $this->cliptakes_api_settings_options['license_key'] ) ? esc_attr( $this->cliptakes_api_settings_options['license_key']) : ''
		);
	}
	/*** End - Tab 05 - API Settings Script ***/

	/*** AJAX request Handlers */
    function cliptakes_create_account() {
		check_ajax_referer( 'cliptakes_settings' );
        $email = sanitize_email( $_REQUEST['email'] );
		$password = sanitize_text_field( $_REQUEST['password'] );

		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/publicApi';
		$endpoint = '/v1/createAccount';
		$params = array(
			'email' => $email,
			'password' => $password
		);
		$response = $this->post_request($params, $api_url, $endpoint, 30);
		$body = json_decode($response['body'], true);
		$api_settings = array(
			'subscription_id' => strval($body['subscriptionNumber']),
			'license_key' => $body['licenseKey']
		);
		update_option('cliptakes_api_settings_options', $api_settings);
		$body['path'] = admin_url( 'admin.php?page=cliptakes-templates' );
		wp_send_json($body);
		wp_die();		
	}

	function cliptakes_reset_intro_html_handler() {
		check_ajax_referer( 'cliptakes_settings' );
		$default_intro = get_default_intro();
		wp_send_json($default_intro);
		wp_die();
	}
	function cliptakes_reset_signup_html_handler() {
		check_ajax_referer( 'cliptakes_settings' );
		$default_signup = get_default_signup();
		wp_send_json($default_signup);
		wp_die();
	}
	function cliptakes_reset_upload_before_html_handler() {
		check_ajax_referer( 'cliptakes_settings' );
		$default_upload_before = get_default_upload_before();
		wp_send_json($default_upload_before);
		wp_die();
	}
	function cliptakes_reset_upload_after_html_handler() {
		check_ajax_referer( 'cliptakes_settings' );
		$default_upload_after = get_default_upload_after();
		wp_send_json($default_upload_after);
		wp_die();
	}
	function cliptakes_sanitize_array( $data ) {
		$sanitized_data = array();
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$sanitized_item = array();
				$sanitized_data[$key] = is_array ( $value ) ? $this->cliptakes_sanitize_array( $value ) : sanitize_text_field( $value );
			}
		}
		return $sanitized_data;
	}
	function cliptakes_fetch_interview_data_handler() {
		$interview_list_table = new Cliptakes_Interview_List_Table();
		$interview_list_table->items = isset( $_POST['interview_items'] ) ? 
			$this->cliptakes_sanitize_array( wp_unslash( $_POST['interview_items'] ) ) : array();
		$interview_list_table->total_items = isset( $_POST['total_items'] ) ? intval( $_POST['total_items'] ) : 0;
		$interview_list_table->items_per_page = isset( $_POST['items_per_page'] ) ? intval( $_POST['items_per_page'] ) : 15;
		$interview_list_table->ajax_response();
	}

	function cliptakes_interview_data_display_handler() {
		check_ajax_referer( 'cliptakes_settings' );
	
		$interview_list_table = new Cliptakes_Interview_List_Table();
		$interview_list_table->items = [];
		$interview_list_table->prepare_items();
	
		ob_start();
		$interview_list_table->display();
		$display = ob_get_clean();
	
		die(json_encode(array("display" => $display)));	
	}
	
    function cliptakes_create_embed_page() {
		check_ajax_referer( 'cliptakes_settings' );
		$video_link = 'https://api.cliptakes.com/embed/'. sanitize_text_field( $_REQUEST['link'] );
        $embed_page_content = '<iframe src="' . esc_url( $video_link ) . '" width="960" height="540" frameborder="0" allowfullscreen></iframe>';
        $interview_details = array(
			'link'			=> $video_link,
			'first_name'	=> sanitize_text_field ( $_REQUEST['first_name'] ),
			'last_name'	=> sanitize_text_field ( $_REQUEST['last_name'] )
		);
		$post_details = array(
            'post_title'	=> sprintf('Interview %s %s', $interview_details['first_name'], $interview_details['last_name']),
            'post_content'  => $embed_page_content,
            'post_type' 	=> 'page'
        );
		$post_details = apply_filters(
			'cliptakes_create_embed_page_details',
			$post_details,
			$interview_details
		);
        $post_id = wp_insert_post( $post_details );
		$page_link = get_edit_post_link($post_id, "no-display");
		die( json_encode(array("link" => $page_link )));		
	}

	function post_request($body, $api_url, $endpoint, $timeout = null) {
		$url = $api_url.$endpoint;

		$api_settings = get_option('cliptakes_api_settings_options');
		$date_string = gmdate('Y-m-d\TH:i:s.u\Z');
		$data = 'POST'.$endpoint.$date_string;
		$auth_header = $api_settings['subscription_id'].':'.hash_hmac ( 'sha256' , $data , $api_settings['license_key']);
				
		$headers = array(
			'Origin'		=> get_site_url(),
			'Timestamp'		=> $date_string,
			'Authorization'	=> $auth_header
		);
		$args = array(
			'body'		=> $body,
			'headers'	=> $headers
		);
		if ($timeout) {
			$args += array('timeout' => $timeout);
		}
		$response = wp_remote_post($url, $args);
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);

		return array(
			'body' => $response_body,
			'http_code' => $response_code
		);
	}
	
	function handle_logo_upload($src_url, $file_extension, $target_type) {
		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/settings';
		$endpoint = '/v1/getLogoUploadUrl';
		$params = array(
			'subscriptionId' => get_option('cliptakes_api_settings_options')['subscription_id'],
			'fileExtension' => $file_extension,
			'type' => $target_type
		);
		$response = $this->post_request($params, $api_url, $endpoint);

		if ($response['http_code'] != 200) return false;

		$upload_url = json_decode($response['body'])->data->url;
		$file = fopen($src_url, 'r');
		$file_size = filesize($src_url);
		$file_data = fread( $file, $file_size);

		$headers = array (
			'accept'		=> 'application/json',
			'content-type'	=> $target_type,
		);
		$args = array(
			'method'	=> 'PUT',
			'body'		=> $file_data,
			'headers'	=> $headers
		);
		$upload_response = wp_remote_post($upload_url, $args);
		$response_code = wp_remote_retrieve_response_code($upload_response);
	
		fclose($file);
		
		return ($response_code == 200);
	}
	
	function reset_logo() {
		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/settings';
		$endpoint = '/v1/resetLogo';
		$params = array(
			'subscriptionId' => get_option('cliptakes_api_settings_options')['subscription_id']
		);
		$response = $this->post_request($params, $api_url, $endpoint);

		return ($response['http_code'] == 200);
	}
	
	function restore_logo_backup() {
		$api_url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/settings';
		$endpoint = '/v1/restoreLogoBackup';
		$params = array(
			'subscriptionId' => get_option('cliptakes_api_settings_options')['subscription_id']
		);
		$response = $this->post_request($params, $api_url, $endpoint);

		return ($response['http_code'] == 200);
	}
}
