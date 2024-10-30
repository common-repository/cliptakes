<?php

/**
 * Cliptakes Interview Template Editor
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin/partials
 */
?>

<div id="ctadmin-interview-editor">
    <h2><?php _e('Interview Templates', 'cliptakes') ?></h2>
    <details>
      <summary><h4><?php _e('How to set up an interview', 'cliptakes') ?></h4></summary>
      <div>
        <p><?php _e('Defining a new video interview template can be achieved in four simple steps:', 'cliptakes') ?></p>
        <ul>
        <li>
          <h4><?php _e('Create a new template', 'cliptakes') ?></h4>
          <p><?php _e('Click "Add Interview Template" and set a template name.', 'cliptakes') ?></p>
        </li>
        <li>
          <h4><?php _e('Set questions', 'cliptakes') ?></h4>
          <p>
          <?php _e('Set your questions by filling in one question per box. To add more questions, simply click "Add Question". It’s best to keep your questions short as viewers will have limited time to read the question before it transitions into the recorded answer.', 'cliptakes') ?>
          </p>
        </li>
        <li>
          <h4><?php _e('Customise the intro and call-to-action', 'cliptakes') ?></h4>
          <p>
          <?php printf(__('The intro slide contains the first text viewers will see before starting your video. By default, Cliptakes sets this to: %s. You can amend this using the placeholders as desired.', 'cliptakes'), __('Video Introduction', 'cliptakes') . ': [FirstName] [LastName]') ?><br>
          <br>
          <?php _e('The final call-to-action text is your opportunity to tell viewers the next steps (how to get in touch), or alternatively it’s a good place to add your company slogan.', 'cliptakes') ?>
          </p>
        </li>
        <li>
          <h4><?php _e('Add the video interview to your website', 'cliptakes') ?></h4>
          <p>
          <?php printf(__('Once you are happy with your interview template click "Save Template" and copy the unique shortcode that is generated at the top of the page. Paste the shortcode into a page of your choice (example: %s) and click "preview changes" to view your new interview platform.', 'cliptakes'), '<i>www.yourwebsite.com/interview</i>') ?> 
          </p>
        </li>
        </ul>
      </div>
		</details>
    <br>
    <p>
      <select id="ctadmin-template-select" name="template-select">
        <option selected disabled hidden><?php _e('Select Interview Template', 'cliptakes') ?></option>
        <option disabled><?php _e('No Templates Found.', 'cliptakes') ?></option>
        <!-- options added in cliptakes-admin.js -->
      </select>
      <button id="ctadmin-add-template-button" class="button"><?php _e('Add Interview Template', 'cliptakes') ?></button>
    </p>
    <?php
      if ($subscription_status) {
        echo '<div id="ctadmin-template-loading-indicator" class="ctadmin-loading-spinner"></div>';
      }
    ?>
    <div id="ctadmin-template-editor" hidden>
        <div>
          <h4><?php _e('Template Name', 'cliptakes') ?>:</h4>
          <input id="ctadmin-template-name" type="text">
        </div>
        <div id="ctadmin-template-shortcode-section">
          <h4><?php _e('Template Shortcode', 'cliptakes') ?>:</h4>
          <input type="text" id="ctadmin-template-shortcode" readonly></br>
          <button id="ctadmin-copy-shortcode-button" class="button ctadmin-button"><?php _e('Copy Shortcode', 'cliptakes') ?></button>
        </div>
        <h4><?php _e('Template Questions', 'cliptakes') ?>:</h4>
        <div id="ctadmin-template-questions"></div>
        <button id="ctadmin-add-question-button" class="button ctadmin-button"><?php _e('Add Question', 'cliptakes') ?></button>
        
        <div><h4><?php _e('Intro Slide Text', 'cliptakes') ?>:</h4>
        <details>
          <summary><h4><?php _e('Intro Slide Shortcodes', 'cliptakes') ?></h4></summary>
		      <div>
            <p><?php _e('This will be the opening slide on your final video. The available shortcodes for this section simply allow you to add the candidate’s first and, or last name:', 'cliptakes') ?></p>
            <pre><b>[FirstName]</b></pre>
            <pre><b>[LastName]</b></pre>
            <br>
            <?php _e('As a default, Cliptakes sets this to:', 'cliptakes') ?>
            <pre><?php _e('Video Introduction', 'cliptakes') ?>: <br>[FirstName] [LastName]</pre>
          </div>
        </details><br>
        <textarea id="ctadmin-template-first" rows="3"></textarea></div>

        <div><h4><?php _e('Call-to-Action Slide Text', 'cliptakes') ?>:</h4> <textarea id="ctadmin-template-last" rows="3"></textarea></div>

        <div id="ctadmin-save-template-success" class="ctadmin-message" hidden><p><?php _e('Template saved successfully!', 'cliptakes') ?></p></div>
        <div id="ctadmin-template-editor-actions">
          <button id="ctadmin-save-template-button" class="button button-primary ctadmin-button"><?php _e('Save Template', 'cliptakes') ?></button>
          <button id="ctadmin-delete-template-button" class="button ctadmin-button ctadmin-button-red"><?php _e('Delete Template', 'cliptakes') ?></button>
        </div>
    </div>
</div>
