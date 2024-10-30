<?php

/**
 * Cliptakes General settings
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin/partials
 */
?>

<br>
<details>
  <summary>
    <h4><?php _e('What are the General Settings?', 'cliptakes') ?></h4>
  </summary>
  <p>
  <?php _e('The below general settings are used to style the Cliptakes recording platform and set custom text. All interview templates will inherit the settings from this page. We’ve added a suggested welcome message and privacy policy which you can change as required.', 'cliptakes') ?>
  </p>
</details>
<br>
<hr>
<?php
  settings_fields( 'cliptakes_general_settings_options_group' );

  do_settings_sections('cliptakes_general_settings_branding');
  submit_button(__('Save All Changes', 'cliptakes'), 'primary', 'ctadmin-submit-branding');
  echo '<hr>';
  
  do_settings_sections('cliptakes_general_settings_intro');
  submit_button(__('Save All Changes', 'cliptakes'), 'primary', 'ctadmin-submit-intro');
  echo '<hr>';
  
  do_settings_sections('cliptakes_general_settings_signup');
  submit_button(__('Save All Changes', 'cliptakes'), 'primary', 'ctadmin-submit-signup');
  echo '<hr>';
  
  do_settings_sections('cliptakes_general_settings_recording');
  submit_button(__('Save All Changes', 'cliptakes'), 'primary', 'ctadmin-submit-recording');
  echo '<hr>';
  
  do_settings_sections('cliptakes_general_settings_upload');
  submit_button(__('Save All Changes', 'cliptakes'), 'primary', 'ctadmin-submit-upload');
?>
  <hr>
