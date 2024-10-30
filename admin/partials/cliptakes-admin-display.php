<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="ctadmin-main-container" class="wrap">
    <h1><?php 'Cliptakes ' . _e('Settings', 'cliptakes'); ?></h1>    
    <?php 
        $active_tab = isset($this->active_tab) ? $this->active_tab : 'general_settings';
    ?>
    <h2 class="nav-tab-wrapper">
        <?php 
            if (!($this->subscription['status'])) {
                echo ('<a href="?page=cliptakes-settings&get-started=1" class="nav-tab ' . ($active_tab == 'get_started' ? 'nav-tab-active' : '') . '">' . _x('Get Started', '"Get Started" Menu Item', 'cliptakes') .'</a>');
            }
            if ( current_user_can( 'manage_cliptakes-general-settings' ) ) {
				echo ('<a href="?page=cliptakes-settings" class="nav-tab ' . ($active_tab == 'general_settings' ? 'nav-tab-active' : '') . '">' . _x('General', 'General Settings', 'cliptakes') . '</a>');
			}
            if ( current_user_can( 'manage_cliptakes-interview-templates' ) ) {
				echo ('<a href="?page=cliptakes-templates" class="nav-tab ' . ($active_tab == 'templates' ? 'nav-tab-active' : '') . '">' . __('Interview Templates', 'cliptakes') . '</a>');
			}
            if ( current_user_can( 'manage_cliptakes-email-notifications' ) ) {
				echo('<a href="?page=cliptakes-contacts" class="nav-tab ' . ($active_tab == 'contacts' ? 'nav-tab-active' : '') . '">' . __('Email Notifications', 'cliptakes') . '</a>');
			}
            if ( current_user_can( 'manage_cliptakes-recorded-interviews' ) ) {
				echo ('<a href="?page=cliptakes-interviews" class="nav-tab ' . ($active_tab == 'interviews' ? 'nav-tab-active' : '') . '">' . __('Recorded Interviews', 'cliptakes') . '</a>');
			}
            if ( current_user_can( 'manage_cliptakes-api-settings' ) ) {
				echo('<a href="?page=cliptakes-api-settings" class="nav-tab ' . ($active_tab == 'api_settings' ? 'nav-tab-active' : '') . '">' . __('API Settings', 'cliptakes') . '</a>');
			}
        ?>
    </h2>
    <?php 
        $subscription_status = $this->subscription['status'];
        $subscription_product = $this->subscription['product'];

        if ($active_tab != 'api_settings' && !$subscription_status): 
    ?>
        <div id="cliptakes-check-subscription-error">
            <p>
                <b><?php _e('No active subscription', 'cliptakes'); ?></b><br>
                <?php printf( _x('Please check your %s or %s.', 'Please check your API settings or sign up for free here.', 'cliptakes'), '<a href="?page=cliptakes-api-settings">' . __('API Settings', 'cliptakes') . '</a>', '<a href="?page=cliptakes-settings&get-started=1">' . _x('sign up for free here', '[Please check your API settings or] sign up for free here', 'cliptakes') . '</a>'); ?>
            </p>
            <hr>
        </div>
    <?php endif; ?>
    <div id="ctadmin-reached-recording-limit"></div>
    <?php
        if ( $active_tab == 'interviews' ) {
            include(plugin_dir_path(__FILE__) . 'cliptakes-interview-display.php');
        } else {
            echo '<form method="post" action="options.php" enctype="multipart/form-data">';
            if ( $active_tab == 'general_settings' ) {
                include(plugin_dir_path(__FILE__) . 'cliptakes-general-settings.php');
            } elseif ( $active_tab == 'templates' ) {
                include(plugin_dir_path(__FILE__) . 'cliptakes-template-editor.php');
            } elseif ( $active_tab == 'contacts' ) {
                include(plugin_dir_path(__FILE__) . 'cliptakes-contact-editor.php');
            } elseif ( $active_tab == 'api_settings' ) {
                include(plugin_dir_path(__FILE__) . 'cliptakes-api-settings.php');
            }
            echo '</form>';
        }
    ?>
</div>
