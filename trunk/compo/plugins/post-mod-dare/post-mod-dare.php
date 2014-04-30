<?php
/*
Plugin Name: Post-Mod-Dare
Plugin URI: 
Version: v1.0
Author: Mike Kasprzak
Description: Inlined Pending Post Moderation (Spammers have driven us to this). 

*/

require_once( ABSPATH . "wp-includes/pluggable.php" );

//function to print publish button
function show_publish_button(){
	Global $post;
	//only print fi admin
	if (current_user_can('edit_others_posts')){
		echo '
		<form action="" method="POST" name="front_end_publish"><input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
		<input id="FE_PUBLISH" type="hidden" name="FE_PUBLISH" value="FE_PUBLISH" />
		<input id="submit" type="submit" name="submit" value="Publish Post" /></form>';

		echo '
		<form action="" method="POST" name="front_end_trash"><input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
		<input id="FE_TRASH" type="hidden" name="FE_TRASH" value="FE_TRASH" />
		<input id="submit" type="submit" name="submit" value="Remove" /></form>';
	}
}
	
//function to update post status
function change_post_status($post_id,$status){
	$current_post = get_post( $post_id, 'ARRAY_A' );
	$current_post['post_status'] = $status;
	wp_update_post($current_post);
}
	
if (isset($_POST['FE_PUBLISH']) && $_POST['FE_PUBLISH'] == 'FE_PUBLISH'){
	if (isset($_POST['pid']) && !empty($_POST['pid'])){
		change_post_status((int)$_POST['pid'],'publish');
	}
}
if (isset($_POST['FE_TRASH']) && $_POST['FE_TRASH'] == 'FE_TRASH'){
	if (isset($_POST['pid']) && !empty($_POST['pid'])){
		change_post_status((int)$_POST['pid'],'trash');
	}
}


/* http://wordpress.stackexchange.com/questions/103938/how-to-display-pending-posts-on-the-homepage-only-for-editors */
function allow_pending_posts_wpse_103938($qry) {
  if (!is_admin() && current_user_can('edit_others_posts')) {
    $qry->set('post_status', array('publish','pending'));
  }
}
add_action('pre_get_posts','allow_pending_posts_wpse_103938');

?>
