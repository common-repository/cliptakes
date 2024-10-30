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

<div id="ctadmin-interview-display">
    <h2><?php _e('Recorded Interviews', 'cliptakes') ?></h2>
    <details>
      <summary><h4><?php _e('More information', 'cliptakes') ?></h4></summary>
      <p>
        <?php _e('The below is a list of all interviews that have been recorded within the last 6 months. After 6 months, recordings are automatically deleted.', 'cliptakes') ?><br>
        <br>
        <b><?php _e('Download Videos', 'cliptakes') ?>:</b> <?php _e('Should you require the video for longer than 6 months you can find it in the list below and click the “Download” button. This will open the video in a new tab. Click the 3 dots in the bottom right-hand corner of the video player and click download.', 'cliptakes') ?><br>
        <br>
        <b><?php _e('Embed Videos', 'cliptakes') ?>:</b> <?php _e('To embed a video into a page on your own website, find the video below and click the “Embed (New Page)” button. This will open the video in an unpublished page on your website for you to review, design and publish.', 'cliptakes') ?><br>
        <br>
        <b><?php _ex('Expiry', 'Section explaining how long Videos are stored before deletion', 'cliptakes') ?>:</b> <?php _e('Should you wish to see how long a video is playable for, find it in the list below, hover over it and in the date column you will see the remaining days available before deletion.', 'cliptakes') ?><br>
        <br>
        <b><?php _e('Views', 'cliptakes') ?>:</b> <?php _e('The "views" column shows you the amount of times each video has been viewed (not necessarily played through).', 'cliptakes') ?>
      </p>
		</details>
    <br>
    <?php if ($subscription_status): ?>
      <div id="ctadmin-recording-statistics" hidden></div>
      <br>
      <form id="ctadmin-interview-list" method="get">
        <p class="ctadmin-search-box">
          <label class="screen-reader-text" for="ctadmin-interview-search"><?php _e('Search Interviews', 'cliptakes') ?></label>
          <input type="search" id="ctadmin-interview-search" name="search"/>
          <button id="ctadmin-interview-search-submit" class="button"><?php _e('Search Interviews', 'cliptakes') ?></button>
        </p>
        <div id="ctadmin-interview-list-table" hidden>
        </div>
        <div id="ctadmin-interview-list-loading" class="ctadmin-loading-spinner">
        </div>
      </form>
    <?php else: ?>
      <br>
      <hr>
      <div>
        <p><?php _e('No interviews have been recorded.', 'cliptakes') ?></p>
      </div>
    <?php endif; ?>
</div>
