<?php

/**
 * Cliptakes Contact List Editor
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin/partials
 */
?>

<div id="ctadmin-contacts">
    <h2><?php _e('Email Notifications', 'cliptakes'); ?></h2>
    <details>
      <summary><h4><?php _e('How our Email Notifications work', 'cliptakes'); ?></h4></summary>
      <p>
      <?php _e('By default, completed interview recordings are emailed to the email address specified in your Cliptakes account set during initial account creation. This main email address can be changed at anytime by visiting your account settings on our website.', 'cliptakes'); ?><br>
      <br>
      <?php _e('The below settings can be used to notify additional contacts that a recording they have requested has been completed. The main contact email will still be copied into the notification.', 'cliptakes'); ?><br>
      <br>
      <b><?php _e('Adding a contact', 'cliptakes'); ?></b><br>
      <?php _e('Click "Add Contact" and define a name, handle, and email address. Clicking save will generate a unique URL-Parameter which can be added to the end of the interview page link you have set. This tells Cliptakes to send the email notification to the main account email and also the newly created contact.', 'cliptakes'); ?><br>
      <br>
      <b><?php _e('Example', 'cliptakes'); ?></b><br>
      <?php printf(_x('You created your interview platform on: %s and add a new contact using the following information:', 'You created your interview platform on: <EXAMPLE LINK> and add a new contact using the following information:', 'cliptakes'), '<b>www.yourwebsite.com/interview</b>')?><br>
      <br>
      <b><?php _e('Name', 'cliptakes'); ?>:</b> London<br>
      <b><?php _ex('Handle', 'Unique shorthand for a contact', 'cliptakes'); ?>:</b> Ldn<br>
      <b><?php _e('Email', 'cliptakes'); ?>:</b> London@yourwebsite.com<br>
      <?php printf(_x('The generated %s will be:', 'The generated URL-Parameter will be: <EXAMPLE>', 'cliptakes'), '<b>' . __('URL-Parameter', 'cliptakes') . '</b>') ?> ?contact=Ldn<br>
      <br>
      <?php printf(_x('Add the newly created %s to the end of your link when requesting interviews, as follows:', 'Add the newly created URL-Parameter to the end of your link when requesting interviews, as follows:', 'cliptakes'), __('URL-Parameter', 'cliptakes')); ?><br>
      www.yourwebsite.com/interview<b>?contact=Ldn</b>
      </p>
		</details>
    <br>
    <button id="ctadmin-add-contact-button" class="button ctadmin-button"><?php _e('Add Contact', 'cliptakes'); ?></button>
    <br>    
    <?php if ($subscription_status): ?>
      <div id="ctadmin-contact-info" class="ctadmin-hidden">
        <p>
          <label>
            <b><?php _e('Name', 'cliptakes'); ?></b>
            <input id="ctadmin-contact-info-name" type="text"
              title="<?php _e('Contact Name', 'cliptakes'); ?>"/>
          </label> 
          <label>
            <b><?php _ex('Handle', 'Unique shorthand for a contact', 'cliptakes'); ?></b>
            <input id="ctadmin-contact-info-handle" type="text"
              title="<?php _e('Unique shorthand for this contact', 'cliptakes'); ?>"/>
          </label>  
        </p>
        <p>
          <label>
            <b><?php _e('Email', 'cliptakes'); ?></b>
            <input id="ctadmin-contact-info-email" type="text"
              title="<?php _e('Contact Email Address', 'cliptakes'); ?>"/>
          </label>  
        </p>
        <p>
          <button id="ctadmin-contact-save-button" class="button button-primary"><?php _e('Save', 'cliptakes'); ?></button>
          <button id="ctadmin-contact-cancel-button" class="button ctadmin-button-red"><?php _e('Cancel', 'cliptakes'); ?></button>
        </p>
        </div>
      <div id="ctadmin-contact-saved-message" hidden>
        <h4><?php _e('Contact was saved.', 'cliptakes'); ?></h4>
      </div>
      <table id="ctadmin-contact-list" class="wp-list-table widefat striped table-view-list">
        <thead>
        <th scope="col"><b><?php _e('Name', 'cliptakes'); ?></b></th>
        <th scope="col"><b><?php _ex('Handle', 'Unique shorthand for a contact', 'cliptakes'); ?></b></th>
        <th scope="col"><b><?php _e('Email', 'cliptakes'); ?></b></th>
        <th scope="col"><b><?php _e('URL-Parameter', 'cliptakes'); ?></b></th>
        </thead>
        <tbody></tbody>
      </table>
      <div id="ctadmin-contact-list-no-items" class="ctadmin-loading-spinner"><span><b><?php _e('No contacts available.', 'cliptakes'); ?></b></span></div>
    <?php endif; ?>
</div>
