<?php
/*
Plugin Name: FreeCodes
Plugin URI: 
Version: v0.1
Author: Mike Kasprzak
Description: For distributing free codes

*/

// Give a user a code //
function assign_freecodes( $user, $slug ) {
	
}

// Retrieve a users code //
function get_freecodes( $user, $slug ) {
	global $wpdb;
	return $wpdb->get_results( 'SELECT * FROM ld_freecodes WHERE uid = {$user} AND slug = {$slug} LIMIT 1', ARRAY_A );
}


function show_freecodes(){
	global $post;
		
	echo '<div class="freecodes">';
	
	if ( is_user_logged_in() ) {
		$slug = get_post( $post )->post_name;
		$user = get_current_user_id();		
		
		$code = get_freecodes($user,$slug);
		
		if ( $code ) {
			print_r($code);
//			echo 'My Code: ' . $code['code'];
		}
		else {
			echo 'I need a code';
			echo '
				<form action="" method="POST" name="get_code" class="freecodes-get">
					<input id="uid" type="hidden" name="pid" value="{$user}" />
					<input id="GET_CODE" type="hidden" name="GET_CODE" value="GET_CODE" />
					<input id="submit" type="submit" name="submit" value="Get a Code" class="freecodes-button" />
				</form>
			';
		}		
//		//only print fi admin
//		if (current_user_can('edit_others_posts')){
//			echo '
//			<form action="" method="POST" name="get_code" class="freecodes-get">
//				<input id="uid" type="hidden" name="pid" value="{$user}" />
//				<input id="GET_CODE" type="hidden" name="GET_CODE" value="GET_CODE" />
//				<input id="submit" type="submit" name="submit" value="Get a Code" class="freecodes-button" />
//			</form>';
//		}
	}
	else {
		echo 'You must be logged in to get a code.';
	}
	
	echo '</div>';
}
add_shortcode( 'freecodes', 'show_freecodes' );


function init_freecodes() {	
	if ( is_user_logged_in() ) {
		if (isset($_POST['GET_CODE']) && $_POST['GET_CODE'] == 'GET_CODE'){
			if (isset($_POST['uid']) && !empty($_POST['uid'])){z
				$slug = get_post( $post )->post_name;
				$user = get_current_user_id();		

				if ( $_POST['uid'] === $user ) {
					echo 'Yup: '.$user.' '.$slug;
					return;
				}
			}
			echo 'Nope';
		}
	}
}
add_action('plugins_loaded','init_freecodes');

?>
