<?php

/**
 * The file that defines the i18n dictionary for the Cliptakes admin script.
 *
 * @link       https://cliptakes.com
 * @since      1.2.5
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/includes
 */

$CLIPTAKES_ADMIN_SCRIPT_i18n = array (
	'no_active_sub' => __( 'No active subscription', 'cliptakes' ),
	'check_api_settings' => __( 'Please check your API Settings or visit www.cliptakes.com to create an account.', 'cliptakes' ),
	'use_https_alert' => __( 'Please make sure you are accessing this site with HTTPS protocol.', 'cliptakes' ),
	'recording_limit_reached' => _x( 'Your monthly recording limit of <MINUTES_LIMIT> minutes has been reached and will be reset on <RESET_DATE>.', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'upgrade_sub_increase_rec_limit' => sprintf(_x( 'Upgrade your Cliptakes subscription to increase your recording time by signing into %syour account%s and navigating to the "switch subscription" tab.', 'Upgrade your Cliptakes subscription to increase your recording time by signing into <LINK_START>your account<LINK_END> and navigating to the "switch subscription" tab.', 'cliptakes' ), '<a href="https://cliptakes.com/my-account/subscription/" target="_blank">', '</a>'),
	'upgrade_sub_custom_logo' => __( 'Please upgrade your subscription to use a custom logo.', 'cliptakes' ),
	'pro_only_feature' => __( 'This feature is only available in our Pro plan.', 'cliptakes' ),
	'reset_intro' => __( 'Are you sure you want to reset the interview intro page to the Cliptakes default intro?', 'cliptakes' ),
	'reset_sign_up' => __( 'Are you sure you want to reset the interview sign-up form to the Cliptakes default sign-up form?', 'cliptakes' ),
	'reset_ready_for_upload' => __( 'Are you sure you want to reset the "ready for upload" section to the Cliptakes default value?', 'cliptakes' ),
	'reset_upload_success' => __( 'Are you sure you want to reset the "upload successful" section to the Cliptakes default value?', 'cliptakes' ),
	'upgrade_sub_timelimit' => __( 'Please upgrade your subscription to add a timelimit to questions.', 'cliptakes' ),
	'question_limit_reached' => _x( 'You have reached the maximum number of questions allowed for this subscription type (<PRODUCT>: <QUESTION_LIMIT> questions per interview).', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'upgrade_sub_questions' => __( 'Please upgrade to add more questions.', 'cliptakes' ),
	'no_sub_question_limit' => __( 'You can only add a maximum of 3 questions while testing our template designer.', 'cliptakes' ),
	'select_interview_template' => __( 'Select Interview Template', 'cliptakes' ),
	'timelimit' => __( 'Timelimit', 'cliptakes' ),
	'timelimit_none' => _x( 'None', 'Timelimit option: No Timelimit', 'cliptakes' ),
	'timelimit_custom' => _x( 'Custom', 'Timelimit option: Custom Timelimit', 'cliptakes' ),
	'timelimit_minutes' => _x( 'min', 'Timelimit minutes', 'cliptakes' ),
	'timelimit_seconds' => _x( 'sec', 'Timelimit seconds', 'cliptakes' ),
	'remove_question' => __( 'Remove Question', 'cliptakes' ),
	'new_template' => __( 'New Template', 'cliptakes' ),
	'enter_question_here' => __( 'Enter your question here...', 'cliptakes' ),
	'default_first_slide' => __( 'Video Introduction', 'cliptakes' ),
	'default_last_slide' => __( 'This video was captured using Cliptakes', 'cliptakes' ),
	'template_limit_reached' => _x( 'You have reached your interview template allowance for your subscription type (<PRODUCT>: <TEMPLATES_LIMIT> interview <N_TEMPLATES>).', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'template_singular' => _n( 'template', 'templates', 1, 'cliptakes' ),
	'templates_plural' => _n( 'template', 'templates', 2, 'cliptakes' ),
	'upgrade_sub_templates' => __( 'Please upgrade to add more templates.', 'cliptakes' ),
	'confirm_delete_template' => __( 'Are you sure you want to delete this interview template? This action cannot be undone.', 'cliptakes' ),
	'dismiss_contact_changes' => __( 'Do you want to dismiss changes you made to the currently edited contact?', 'cliptakes' ),
	'confirm_delete_contact' => __( 'Are you sure you want to delete this contact?', 'cliptakes' ),
	'upgrade_sub_add_contacts' => __( 'Please upgrade your subscription to add contacts.', 'cliptakes' ),
	'contacts_limit_reached' => _x( 'You have reached your contact allowance for your subscription type (<PRODUCT>: <CONTACTS_LIMIT> contacts).', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'upgrade_sub_more_contacts' => __( 'Please upgrade to add more contacts.', 'cliptakes' ),
	'load_contacts_error' => __( 'Could not load contacts.', 'cliptakes' ),
	'edit' => __( 'Edit', 'cliptakes' ),
	'delete' => __( 'Delete', 'cliptakes' ),
	'copy' => __( 'Copy', 'cliptakes' ),
	'contact_name_missing' => __( 'Please enter a name for this contact.', 'cliptakes' ),
	'contact_name_error' => __( 'Name can only contain letters, numbers and spaces.', 'cliptakes' ),
	'contact_handle_missing' => __( 'Please enter a unique shorthand for this contact.', 'cliptakes' ),
	'contact_handle_error' => __( 'Handle can only contain letters, numbers and ".", "-", "_".', 'cliptakes' ),
	'contact_handle_taken' => __( 'This handle is already taken.', 'cliptakes' ),
	'contact_email_error' => __( 'Email must contain "@" and a valid domain name (e.g. "example.com")', 'cliptakes' ),
	'please_refresh' => __( 'Please refresh this page and try again.', 'cliptakes' ),
	'delete_contact_error' => __( 'An error occured while deleting this contact.', 'cliptakes' ),
	'recording_time' => _x( 'Recording Time', 'Displays the remaining minutes that can be used this month', 'cliptakes' ),
	'recording_statistics' => _x( '<REMAINING> Minutes of <LIMIT> remaining', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'resets_on' => _x( 'Resets On', 'Display the reset date for recording allowance', 'cliptakes' ),
	'load_statistics_error' => __( 'An error ocurred while loading your recording statistics.', 'cliptakes' ),
	'confirm_delete_interview' => __( 'Are you sure you want to delete this video and all associated personal information? This action is permanent and cannot be undone.', 'cliptakes' ),
	'delete_interview_error' => __( 'An error ocurred while deleting the interview.', 'cliptakes' ),
	'load_interviews_error' => __( 'Could not load interviews.', 'cliptakes' ),
	'expiry_message' => _x( 'Expires in <DAYS_UNTIL_DELETION> days', 'Do not edit "<..>" placeholders!', 'cliptakes' ),
	'save_changes_alert' => __( 'Please save your changes.', 'cliptakes' ),
	'authentication_error' => __( 'Authentication failed', 'cliptakes' ),
	'check_license_and_key' => __( 'Please check your Subscription ID and License Key.', 'cliptakes' )
)

?>
