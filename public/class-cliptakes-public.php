<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/public
 */

class Cliptakes_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->plugin_name . '_interview', plugin_dir_url( __FILE__ ) . 'css/cliptakes-interview.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name . '_interview', plugin_dir_url( __FILE__ ) . 'js/cliptakes-interview.js', array(), $this->version, true );
		wp_localize_script( $this->plugin_name . '_interview', 'cliptakes_subscription_info', get_option('cliptakes_api_settings_options'));
		
		include_once(plugin_dir_path( dirname( __FILE__ ) ) . 'public/cliptakes-interview-script-i18n.php');
		wp_localize_script( $this->plugin_name . '_interview', 'cliptakes_i18n', $CLIPTAKES_INTERVIEW_SCRIPT_i18n);
	}

	public function cliptakes_signup_input_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'id' => '',
				'name' => '',
				'label' => __('New Input', 'cliptakes'),
				'placeholder' => $atts['label'],
				'value' => '',
				'optional' => false,
				'readonly' => false,
				'custom' => false
			), $atts, 'cliptakes_signup_input'
		);
		$input_id = "ctiv-signup-" . (!empty($input_atts['id']) ? $input_atts['id'] : sanitize_title($input_atts['label']));
		$o = '<div class="ctiv-signup-row">
		<label for="' . $input_id . '">' . $input_atts['label'] . '</label>
		<input
		  type="text"' .
		  ($input_atts['custom'] ? 'class="ctiv-custom-input"\n' : '') . '		  
		  id="' . $input_id . '"
		  name="' . (!empty($input_atts['name']) ? $input_atts['name'] : $input_atts['label']) . '"
		  placeholder="' . $input_atts['placeholder'] . '"' .
		  (!empty($input_atts['value']) ? 'value="' . $input_atts['value'] . '"\n' : '') .		
		  ($input_atts['readonly'] ? ' readonly' : '') .		
		  ($input_atts['optional'] ? '' : ' required') . '
		/>
	  </div>';
		return $o;
	}
	public function cliptakes_input_first_name_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'label' => _x('First Name', 'Frontend: label for first name input', 'cliptakes'),
				'placeholder' => _x('Your First Name', 'Frontend: placeholder for first name input', 'cliptakes')
			), $atts, 'cliptakes_input_first_name'
		);
		$input_atts['id'] = 'first-name';
		$input_atts['name'] = 'firstname';
		return $this->cliptakes_signup_input_func($input_atts);
	}
	public function cliptakes_input_last_name_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'label' => _x('Last Name', 'Frontend: label for last name input', 'cliptakes'),
				'placeholder' => _x('Your Last Name', 'Frontend: placeholder for last name input', 'cliptakes')
			), $atts, 'cliptakes_input_last_name'
		);
		$input_atts['id'] = 'last-name';
		$input_atts['name'] = 'lastname';
		return $this->cliptakes_signup_input_func($input_atts);
	}
	public function cliptakes_input_email_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'label' => 'Email',
				'placeholder' => 'Your Email Address'
			), $atts, 'cliptakes_input_email'
		);
		$input_atts['id'] = 'email';
		$input_atts['name'] = 'email';
		return $this->cliptakes_signup_input_func($input_atts);
	}
	public function cliptakes_input_accept_policy_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'label' => _x('I have read and agree to the Privacy Policy.', 'Frontend: Checkbox Accept Privacy Policy', 'cliptakes')
			), $atts, 'cliptakes_input_accept_policy'
		);
		$o = '<div>
		<input type="checkbox" id="ctiv-signup-accept" required />
		<label for="ctiv-signup-accept">' . $input_atts['label'] . '</label>
	  </div>';
		return $o;
	}
	public function cliptakes_custom_input_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$input_atts = shortcode_atts(
			array(
				'id' => '',
				'name' => '',
				'label' => __('New Input', 'cliptakes'),
				'placeholder' => $atts['label'],
				'value' => '',
				'optional' => false,
				'readonly' => false,
				'custom' => true
			), $atts, 'cliptakes_custom_input'
		);
		/**
		 * Filters the input field attributes.
		 *
		 * @since 1.3.0
		 *
		 * @param string $input_atts Custom input field attributes.
		 */
		$input_atts = apply_filters('cliptakes_custom_signup_input', $input_atts);
		return $this->cliptakes_signup_input_func($input_atts);
	}

	public function cliptakes_signup_select_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$select_atts = shortcode_atts(
			array(
				'id' => '',
				'name' => '',
				'label' => __('New Select', 'cliptakes'),
				'options' => __('No Selection', 'cliptakes'),
				'default' => '',
				'optional' => false,
				'custom' => false
			), $atts, 'cliptakes_signup_select'
		);
		$select_id = "ctiv-signup-" . (!empty($select_atts['id']) ? $select_atts['id'] : sanitize_title($select_atts['label']));
		$default_option = $select_atts['default'];
		$options_elements = implode(
			"\n",
			array_map(
				function ($opt) use ($default_option) {
					return '<option value="' . $opt . '"' . (strcmp($opt, $default_option) == 0 ? ' selected' : '') . '>' . $opt . '</option>';
				},
				explode(";", $select_atts['options'])
			)
		);
		$o = '<div class="ctiv-signup-row">
		<label for="' .$select_id . '">' . $select_atts['label'] . '</label>
		<select
		  ' . ($select_atts['custom'] ? 'class="ctiv-custom-input"\n' : '') . '		  
		  id="' . $select_id . '"
		  name="' . (!empty($select_atts['name']) ? $select_atts['name'] : $select_atts['label']) . '">' .
		  $options_elements . '
		</select>
	  </div>';
		return $o;
	}
	public function cliptakes_custom_select_func($atts = array()){
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$select_atts = shortcode_atts(
			array(
				'id' => '',
				'name' => '',
				'label' => __('New Select', 'cliptakes'),
				'options' => __('No Selection', 'cliptakes'),
				'default' => '',
				'optional' => false,
				'custom' => true
			), $atts, 'cliptakes_custom_select'
		);
		/**
		 * Filters the select field attributes.
		 *
		 * @since 1.3.1
		 *
		 * @param string $input_atts Custom select field attributes.
		 */
		$select_atts = apply_filters('cliptakes_custom_signup_select', $select_atts);
		return $this->cliptakes_signup_select_func($select_atts);
	}
	public function cliptakes_interview_func($atts = array(), $content=null){
		if( ! wp_script_is( $this->plugin_name . '_interview', $list = 'registered' ) ) { wp_register_script( $this->plugin_name . '_interview', plugin_dir_url( __FILE__ ) . 'js/cliptakes-interview.js', array(), $this->version, true ); }
		include_once(plugin_dir_path( __DIR__ ) . 'includes/cliptakes-default-settings.php');  
		$cliptakes_options = get_option( 'cliptakes_general_settings_options' );
		if (empty($cliptakes_options['main_color']) ) {
			$cliptakes_options['main_color'] = $CLIPTAKES_DEFAULT_SETTINGS['main_color'];
		}

		if( ! wp_script_is( $this->plugin_name . '_interview', $list = 'enqueued' ) ) { wp_enqueue_script($this->plugin_name . '_interview'); }
		if( ! wp_style_is( $this->plugin_name . '_interview', $list = 'enqueued' ) ) { wp_enqueue_style($this->plugin_name . '_interview'); }
		
		// import html markup definitions
		require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cliptakes-interview-elements.php');

		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		
		// override default attributes with user attributes
		$interview_atts = shortcode_atts(
			array(
				'templateid' => '',
				'sendcandidatemail' => false,
			), $atts, 'cliptakes_interview'
		);
		
		// Get interview questions
		$api_settings = get_option('cliptakes_api_settings_options');
		$date_string = gmdate('Y-m-d\TH:i:s.u\Z');
		$data = 'POST'.'/v1/getQuestions'.$date_string;
		$auth_header = $api_settings['subscription_id'].':'.hash_hmac ( 'sha256' , $data , $api_settings['license_key']);
		
		$url = 'https://europe-west2-cliptakes-api.cloudfunctions.net/settings/v1/getQuestions';
		$body = array(
			'subscriptionId' => $api_settings['subscription_id'],
			'templateId' => $interview_atts['templateid']
		);			
		$headers = array(
			'Origin'		=> get_site_url(),
			'Timestamp'		=> $date_string,
			'Authorization'	=> $auth_header
		);
		$args = array(
			'body'		=> $body,
			'headers'	=> $headers
		);

		$response = wp_remote_post($url, $args);
		$response_body = wp_remote_retrieve_body($response);
		
		$questions = json_decode($response_body)->questions;
		$interview_questions = [];
		if ( is_array($questions) and count($questions)>0 ) {
			foreach( $questions as $question ) { 
				$timelimit = (isset($question->timelimit) ? intval($question->timelimit) : 0);
				$timelimit_minutes = floor($timelimit / 60);
				$timelimit_seconds = $timelimit % 60;
				$timelimit_info_text = '';
				if ($timelimit > 0) {
					$timelimit_info_text = empty($cliptakes_options['timelimit_info_text']) ?
						$CLIPTAKES_DEFAULT_SETTINGS['timelimit_info_text'] :
						$cliptakes_options['timelimit_info_text'];
					$timelimit_info_text = str_replace(
						'[t]', $timelimit, str_replace(
							'[m]', $timelimit_minutes, str_replace(
								'[s]', $timelimit_seconds, $timelimit_info_text
								)
							)
						);
				}
				$timelimit_info_text = apply_filters(
					'cliptakes_format_timelimit_info_text',
					$timelimit_info_text,
					array(
						'timelimit' => $timelimit,
						'minutes'	=> $timelimit_minutes,
						'seconds' 	=> $timelimit_seconds
					)
					);
				$interview_questions[] = array(
					'id' => $question->questionId,
					'text' => $question->text . (empty($timelimit_info_text) ? '' : ' ' . $timelimit_info_text),
					'timelimit' => $timelimit
				);
			}
		}
		wp_localize_script(
			$this->plugin_name . '_interview',
			'cliptakes_interview_questions',
			$interview_questions
		);

		// Start main container
		$o = '<div id="ctiv-main-container"
		data-template-id="' . $interview_atts['templateid'] . '"
		'. ($interview_atts['sendcandidatemail'] ? ' data-send-candidate-mail=True' : '') .'>
		<style>
		  :root {
			--cliptakes-neutral: #666666;
			--cliptakes-disabled: #66666666;
			--cliptakes-main: ' . $cliptakes_options['main_color'] . ';
			--cliptakes-main-overlay: ' . $cliptakes_options['main_color'] . '50;
			--cliptakes-light: #fff;
			--cliptakes-dark: #1f1f1f;
			--cliptakes-overlay: #000000b0;
			--cliptakes-overlay-focused: #1f1f1fdd;
		  }
		</style>';

		// Intro - Start
		$o .= '<div id="ctiv-intro-section" class="ctiv-section">';
		if ( ! empty($cliptakes_options['intro_html_markup'])) {
			$content = $cliptakes_options['intro_html_markup'];
			$content = apply_filters( 'the_content', $content );
			// run shortcode parser recursively
			$content = do_shortcode( $content );
			$o .= $content;
		} else {
			$o .= get_default_intro();
		}
		$o .= get_intro_next_button(empty($cliptakes_options['intro_next_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['intro_next_button'] : $cliptakes_options['intro_next_button'], count($interview_questions)>0);
		$o .= '
			<div id="ctiv-auth-spinner" class="ctiv-hidden"></div>';
		$o .= '
			<div id="ctiv-intro-authorization-error"' . (count($interview_questions)>0 ? ' class="ctiv-hidden"' : '') . '>';
		$o .= _x('This interview is currently unavailable. Please ask the person who sent you this link to check their video interview settings.', 'Frontend: Interview not available message', 'cliptakes');
        $o .= '</div></div>';
		// Intro - End		

		// Sign-Up
		$o .= '<form id="ctiv-signup-form" class="ctiv-hidden ctiv-section">';
		
		$content = empty($cliptakes_options['signup_html_markup']) 
			? get_default_signup()
			: $cliptakes_options['signup_html_markup'];
		$content = apply_filters( 'the_content', $content );
		// run shortcode parser recursively
		$content = do_shortcode( $content );
		$o .= $content;	
		$o .= get_signup_submit_button(empty($cliptakes_options['signup_submit_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['signup_submit_button'] : $cliptakes_options['signup_submit_button']);
		$o .= '</form>';	

		// Navigation
		$o .= '
		<div id="ctiv-nav-section" class="ctiv-hidden">
            <button id="ctiv-nav-back" class="ctiv-nav-button">
			<svg>
				<use href="#ctiv-back-arrow-icon"></use>
			</svg>
			</button>
            <div id="ctiv-nav-bar">
				<div data-id="-1">' . _x('Setup', 'Frontend: Navigation', 'cliptakes') . '</div>';
		for ($i = 0; $i < count($interview_questions); $i++) {
			$o .= '<div data-id="' . $i . '">' . ($i + 1) . '</div>';
		}
		
		$o .= '
				<div data-id="' . count($interview_questions) . '">' . _x('Upload', 'Frontend: Navigation', 'Cliptakes') . '</div>
			</div>
			<div id="ctiv-nav-text"><h4 id="ctiv-nav-text-heading"></h4></div>
			<button id="ctiv-nav-next" class="ctiv-nav-button">
				<svg>
					<use href="#ctiv-next-arrow-icon"></use>
				</svg>
			</button>
	  	</div>';
		
		$o .= get_setup_section(empty($cliptakes_options['setup_next_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['setup_next_button'] : $cliptakes_options['setup_next_button']);

		$o .= get_question_section(
			empty($cliptakes_options['question_size']) ? $CLIPTAKES_DEFAULT_SETTINGS['question_size'] : $cliptakes_options['question_size'],
			empty($cliptakes_options['upload_video_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['upload_video_button'] : $cliptakes_options['upload_video_button'],
			empty($cliptakes_options['retake_video_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['retake_video_button'] : $cliptakes_options['retake_video_button'],
			empty($cliptakes_options['next_question_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['next_question_button'] : $cliptakes_options['next_question_button']
		);

		// Get Upload Section
		$upload_before = empty($cliptakes_options['upload_before_html_markup']) 
			? get_default_upload_before()
			: $cliptakes_options['upload_before_html_markup'];
		$upload_before = apply_filters( 'the_content', $upload_before );

		$upload_after = empty($cliptakes_options['upload_after_html_markup']) 
			? get_default_upload_after()
			: $cliptakes_options['upload_after_html_markup'];
		$upload_after = apply_filters( 'the_content', $upload_after );
		// run shortcode parser recursively
		$upload_section = do_shortcode( 
			get_upload_section(
				empty($cliptakes_options['upload_interview_button']) ? $CLIPTAKES_DEFAULT_SETTINGS['upload_interview_button'] : $cliptakes_options['upload_interview_button'],
				$upload_before,
				$upload_after
			)
		);

		$o .= $upload_section;

		// End main container
		$o .= '</div>';

		$o .= get_svg_elements();

		// return output
		return $o;
	}

}
?>
