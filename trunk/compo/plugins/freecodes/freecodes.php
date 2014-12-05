<?php
/*
Plugin Name: FreeCodes
Plugin URI: 
Version: v0.1
Author: Mike Kasprzak
Description: For distributing free codes

*/

function show_freecodes(){
	global $post;
	
	echo '<div class="freecodes">';
	
	if ( is_user_logged_in() ) {
//		//only print fi admin
//		if (current_user_can('edit_others_posts')){
//			echo '
//			<form action="" method="POST" name="get_code" class="freecodes-get">
//				<input id="pid" type="hidden" name="pid" value="'.$post->ID.'" />
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
	if (isset($_POST['GET_CODE']) && $_POST['GET_CODE'] == 'GET_CODE'){
		if (isset($_POST['pid']) && !empty($_POST['pid'])){
			//change_post_status((int)$_POST['pid'],'publish');
		}
	}
	
}
add_action('plugins_loaded','init_freecodes');

?>
