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
<div id="ctadmin-get-started">
    <div id="ctadmin-get-started-header">
        <h2><?php _e('Use Cliptakes to add a one-way video interview platform to your own website', 'cliptakes') ?></h2>
    </div>
    <div id="ctadmin-sign-up-content">
        <h3><?php _e('Sign up below to start your FREE subscription to Cliptakes', 'cliptakes') ?></h3>
        <span><?php _e('A free subscription will allow you to take an average of 5 video interviews per month and will include 3 months storage per video.', 'cliptakes')?></span>
        <br><br>
        <span><b><?php _e('Your email address will be used to create a free account with Cliptakes and will also be the default email address where completed video interview links are sent to.', 'cliptakes')?></b></span>
        <br><br>
        <form id="ctadmin-create-account-form">
            <div class="ctadmin-form-row">
                <label for="ctadmin-create-account-email"><?php _e('Email', 'cliptakes') ?>:</label>
                <input type="text" id="ctadmin-create-account-email" name="email" required>
            </div>
            <div class="ctadmin-form-row">
                <label for="ctadmin-create-account-password"><?php _e('Password', 'cliptakes') ?>:</label>
                <input type="password" id="ctadmin-create-account-password" name="password" required>
                <input type="checkbox" id="ctadmin-create-account-show-password" class="ctadmin-desktop-only">
                <label for="ctadmin-create-account-show-password" class="ctadmin-desktop-only"> <?php _e('Show Password', 'cliptakes') ?></label>
            </div>
            <div class="ctadmin-form-row">
                <input type="checkbox" id="ctadmin-create-account-accept-terms" name="accept-terms" required>
                <label for="ctadmin-create-account-accept-terms"><?php printf(_x('I have read and agree to Cliptakes %sterms and conditions%s', 'I have read and agree to Cliptakes <START_LINK>terms and conditions<END_LINK>', 'cliptakes'), '<a href="https://cliptakes.com/terms-and-conditions/" target="_blank">', '</a>') ?> </label>
            </div>
            <div class="ctadmin-form-row">
                <input id="ctadmin-submit-create-account" class="ctadmin-pretty-button" type="submit" value="<?php _e('Sign Up', 'cliptakes') ?>">
            </div>
        </form>
        <div id="ctadmin-create-account-loading" class="ctadmin-loading-spinner" style="display: none;"></div>
    </div>
    <div id="ctadmin-skip-registration-button-container">
        <button id="ctadmin-btn-skip-registration" class="ctadmin-pretty-button" onclick="location.href='?page=cliptakes-api-settings'" type="button"><?php _e('I Already Have an Account', 'cliptakes') ?></button>
    </div>
</div>
