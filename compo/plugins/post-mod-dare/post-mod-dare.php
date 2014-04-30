<?php
/*
Plugin Name: Post-Mod-Dare
Plugin URI: 
Version: v1.0
Author: Mike Kasprzak
Description: Inlined Pending Post Moderation (Spammers have driven us to this). 

*/

/* http://wordpress.stackexchange.com/questions/103938/how-to-display-pending-posts-on-the-homepage-only-for-editors */
function allow_pending_posts_wpse_103938($qry) {
  if (!is_admin() && current_user_can('edit_others_posts')) {
    $qry->set('post_status', array('publish','pending'));
  }
}
add_action('pre_get_posts','allow_pending_posts_wpse_103938');

?>
