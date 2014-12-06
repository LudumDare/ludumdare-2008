<?php
// Sorry for the generic file name. I need a clean space to work. //
// This is a collection of things for the Compo system, that are  //
// hopefully never needed again come mid next year. //

function c2_navigation($slug) {
	if ( current_user_can('edit_others_posts') ) {
?>
		<div class="event">
			<div class="info">
				<div class="navigation">Navigation</div>
				<div class="name">Now: <strong><?php echo $slug; ?></strong></div>
			</div>
		</div>
<?php
	}
	
}


?>