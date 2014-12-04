<?php
/*
Plugin Name: Post-Mod-Dare
Plugin URI: 
Version: v1.0
Author: Mike Kasprzak
Description: Inlined Pending Post Moderation (Spammers have driven us to this). 

*/

// NEVER DO THIS AS IT BREAKS PLUGGABLES (function overloading. Sabre tweaks e-mails sent with it) //
//require_once( ABSPATH . "wp-includes/pluggable.php" );

function show_publish_buttons(){
	Global $post;
	//only print fi admin
	if (current_user_can('edit_others_posts')){
		echo '
		<form action="" method="POST" name="front_end_publish" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_PUBLISH" type="hidden" name="FE_PUBLISH" value="FE_PUBLISH" />
			<input id="submit" type="submit" name="submit" value="Publish Post" class="promobutton" onclick="return confirm(\'Are you sure you want to Publish this Post?\')" />
		</form>';

		echo ' | ';

		echo '
		<form action="" method="POST" name="front_end_trash" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_TRASH" type="hidden" name="FE_TRASH" value="FE_TRASH" />
			<input id="submit" type="submit" name="submit" value="remove" class="promobutton" onclick="return confirm(\'Are you sure you want to Remove this Post?\')" />
		</form>';
	}
}

function show_promote_buttons(){
	Global $post;
	//only print if admin
	if (current_user_can('edit_others_posts')){
		echo '
		<form action="" method="POST" name="front_end_promote_publish" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_USER_PROMOTE_PUBLISH" type="hidden" name="FE_USER_PROMOTE_PUBLISH" value="FE_USER_PROMOTE_PUBLISH" />
			<input id="submit" type="submit" name="submit" value="Promote and Publish" class="promobutton2" onclick="return confirm(\'Are you sure you want to Promote this user to an Author and Publish the Post?\')" />
		</form>';

		echo ' | ';
		
		echo '
		<form action="" method="POST" name="front_end_promote" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_USER_PROMOTE" type="hidden" name="FE_USER_PROMOTE" value="FE_USER_PROMOTE" />
			<input id="submit" type="submit" name="submit" value="Promote to Author" class="promobutton2" onclick="return confirm(\'Are you sure you want to Promote this user to an Author?\')" />
		</form>';

		echo ' | ';
		
		echo '
		<form action="" method="POST" name="front_end_demote" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_USER_DEMOTE" type="hidden" name="FE_USER_DEMOTE" value="FE_USER_DEMOTE" />
			<input id="submit" type="submit" name="submit" value="QUARANTINE" class="promobutton2" onclick="return confirm(\'Are you sure you want to QUARANTINE this user?\')" />
		</form>';
		
		echo ' | delete';
	}
}

function show_murder_buttons(){
	Global $post;
	//only print if admin
	if (current_user_can('delete_users')){	
		echo '
		<form action="" method="POST" name="front_end_trash" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_TRASH" type="hidden" name="FE_TRASH" value="FE_TRASH" />
			<input id="submit" type="submit" name="submit" value="Delete Post" class="promobutton" onclick="return confirm(\'Are you sure you want to Remove this Post?\')" />
		</form>';

		echo ' | ';

		echo '
		<form action="" method="POST" name="front_end_demote" class="promoform">
			<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
			<input id="FE_USER_DEMOTE" type="hidden" name="FE_USER_DEMOTE" value="FE_USER_DEMOTE" />
			<input id="submit" type="submit" name="submit" value="QUARANTINE" class="promobutton2" onclick="return confirm(\'Are you sure you want to QUARANTINE this user?\')" />
		</form>';
		
		echo ' | delete';
	}
}

// Update the post status
function change_post_status($post_id,$status){
	$current_post = get_post( $post_id, 'ARRAY_A' );
	$current_post['post_status'] = $status;
	wp_update_post($current_post);
}
// Update the user level //
function change_user_level($user_id,$status){
	$newuser = new WP_User( $user_id /*$user->ID*/ );
	$newuser->set_role( $status );
}


function init_postmoddare() {	
	// Responses to Post Status Changes //	
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
	
	// Responses to User Level Changes //
	if (isset($_POST['FE_USER_PROMOTE_PUBLISH']) && $_POST['FE_USER_PROMOTE_PUBLISH'] == 'FE_USER_PROMOTE_PUBLISH'){
		if (isset($_POST['pid']) && !empty($_POST['pid'])) {
			$current_post = get_post( (int)$_POST['pid'], 'ARRAY_A' );		
			change_user_level( $current_post['post_author'], 'author' );
			change_post_status((int)$_POST['pid'],'publish');
		}
	}
	if (isset($_POST['FE_USER_PROMOTE']) && $_POST['FE_USER_PROMOTE'] == 'FE_USER_PROMOTE'){
		if (isset($_POST['pid']) && !empty($_POST['pid'])) {
			$current_post = get_post( (int)$_POST['pid'], 'ARRAY_A' );		
			change_user_level( $current_post['post_author'], 'author' );
		}
	}
	if (isset($_POST['FE_USER_DEMOTE']) && $_POST['FE_USER_DEMOTE'] == 'FE_USER_DEMOTE'){
		if (isset($_POST['pid']) && !empty($_POST['pid'])) {
			$current_post = get_post( (int)$_POST['pid'], 'ARRAY_A' );		
			change_user_level( $current_post['post_author'], 'subscriber' );
		}
	}
	if (isset($_POST['FE_USER_RESET']) && $_POST['FE_USER_RESET'] == 'FE_USER_RESET'){
		if (isset($_POST['pid']) && !empty($_POST['pid'])) {
			$current_post = get_post( (int)$_POST['pid'], 'ARRAY_A' );		
			change_user_level( $current_post['post_author'], 'contributor' );
		}
	}
}
// Call the above function after plugins have loaded (to make sure we have all the relied upon functions) //
add_action('plugins_loaded','init_postmoddare');


function custom_login_message() {
	$message = '';
	//$message = '<p class="message">Registration/Login Problems? A bug with user registrations was introduced earlier this week. It was fixed late Friday May 2nd, but we had to remove all users affected by the bug. If that was you, simply re-register. A confirmation e-mail with link should arrive within a few minutes.</p>';
	return $message;
}
add_filter('login_message', 'custom_login_message');


/* MK: Had to add a check if not preview. Input $query is different depending on who call this. */
// If the user is of high enough level, modify the query to return both pending and published posts // 
function allow_pending_posts_wpse_103938($query) {
	if (!is_admin() && current_user_can('edit_others_posts') && !$query->get('preview')) {
		$query->set('post_status', array('publish','pending'));
	}
}
add_action('pre_get_posts','allow_pending_posts_wpse_103938');

?>
