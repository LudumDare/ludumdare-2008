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
	global $wpdb;
	$code = $wpdb->get_results( "SELECT * FROM ld_freecodes WHERE uid = 0 AND slug = \"{$slug}\" LIMIT 1", ARRAY_A );

	if ( count($code) > 0 ) {
		$wpdb->update(
			"ld_freecodes", 
			array( 'uid' => $user ),
			array( 'ID' => $code[0]['ID'] )
		);
	}
}

/*
function count_unused_freecodes( $slug ) {
	global $wpdb;
	return $wpdb->get_results( "SELECT count(*) FROM ld_freecodes WHERE uid = 0 AND slug = \"{$slug}\"", ARRAY_A );
}
*/

// Retrieve a users code //
function get_freecodes( $user, $slug ) {
	global $wpdb;
	return $wpdb->get_results( "SELECT * FROM ld_freecodes WHERE uid = {$user} AND slug = \"{$slug}\" LIMIT 1", ARRAY_A );
}


function show_freecodes(){
	global $post;
	
	
	echo '
		<style>
			.freecodes .code {
				font-weight: bold;
			}
		</style>
		';
		
	echo '<div class="freecodes">';
	//print_r($post);
	
	if ( is_user_logged_in() ) {
		$slug = $post->post_name;
		$user = get_current_user_id();		
		
		$code = get_freecodes($user,$slug);
		
		if ( ($code) && (count($code) > 0) ) {
			echo 'Your Code is: <span class="code">' . $code['code'] . '</span>';
			print_r($code);
		}
		else {
			echo '
				<form action="" method="POST" name="get_code" class="form">
					<input id="uid" type="hidden" name="uid" value="'.$user.'" />
					<input id="slug" type="hidden" name="slug" value="'.$slug.'" />
					<input id="GET_CODE" type="hidden" name="GET_CODE" value="GET_CODE" />
					<input id="submit" type="submit" name="submit" value="Get a Code" class="button" />
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
			$user = get_current_user_id();
			if (isset($_POST['uid']) && !empty($_POST['uid'])){
				if ( intval($_POST['uid']) === $user ) {
					$slug = sanitize_title_with_dashes($_POST['slug']);

					echo 'Yup: '.$user.' '.$slug;
					
					$code = get_freecodes($user,$slug);
					
					// If no existing code, assign one //
					if ( count($code) === 0 ) {
						assign_freecodes($user,$slug);
					}
					
					return;
				}
			}
			echo 'Nope: '.$_POST['uid'].' vs '.$user;
		}
	}
}
add_action('plugins_loaded','init_freecodes');

?>
