<?php

/**
 * Cliptakes API settings
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin/partials
 */
?>

<div id="ctadmin-api-connection-status" <?php
  echo ($subscription_status ? ' class="ctadmin-subscription-checked"' : '')
?> >
  <?php
    settings_fields( 'cliptakes_api_settings_options_group' );
    do_settings_sections( 'cliptakes_api_settings_options' );
  ?>
  <button id="ctadmin-check-subscription" class="button"><?php _e('Check Subscription', 'cliptakes'); ?></button>
  <div id="ctadmin-check-subscription-response" class="ctadmin-message" hidden></div>
  <div id="ctadmin-check-subscription-error" class="ctadmin-message"
  <?php echo ($subscription_status ? 'hidden' : '') ?>
  >
    <p><?php _e('Please check your subscription.', 'cliptakes'); ?></p>
  </div>
</div>
<?php submit_button(); ?>
