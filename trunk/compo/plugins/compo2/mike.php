<?php
// Sorry for the generic file name. I need a clean space to work. //
// This is a collection of things for the Compo system, that are  //
// hopefully never needed again come mid next year. //

function c2_navigation($slug) {
	if ( current_user_can('edit_others_posts') ) {
		echo "hello. testing: " + $slug;
	}
	
}


?>