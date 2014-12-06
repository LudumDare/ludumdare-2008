<?php
// Sorry for the generic file name. I need a clean space to work. //
// This is a collection of things for the Compo system, that are  //
// hopefully never needed again come mid next year. //

function c2_navigation($slug,$name,$name_url) {
	if ( current_user_can('edit_others_posts') ) {
		if ( !is_paged() ) {
?>
		<div class="event">
			<div class="info">
				<div class="navigation"><a href="/compo/<?php echo $slug?>/?action=edit"><strong>Submit</strong></a> | <a href="/compo/<?php echo $slug?>/?action=preview">View All</a></div>
				<div class="name">Now: <strong><a href="<?php echo $name_url; ?>"><?php echo $name; ?></a></strong></div>
			</div>
		</div>
<?php
		}
	}
	else {
?>
		<div class="event">
			<div class="info">
				<div class="navigation"><a href="/compo/<?php echo $slug?>/?action=edit"><strong>Submit</strong></a> | <a href="/compo/<?php echo $slug?>/?action=preview">View All</a></div>
				<div class="name">Now: <strong><a href="<?php echo $name_url; ?>"><?php echo $name; ?></a></strong></div>
			</div>
		</div>
<?php
	}	
}

?>