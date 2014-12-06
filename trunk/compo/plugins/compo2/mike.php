<?php
// Sorry for the generic file name. I need a clean space to work. //
// This is a collection of things for the Compo system, that are  //
// hopefully never needed again come mid next year. //



// This Function is called in the style sheet to display Navigation //
function c2_navigation($slug,$name,$name_url) {
	if ( current_user_can('edit_others_posts') ) {
		if ( !is_paged() ) {
			$user_id = get_current_user_id();
			$event_id = 0;
			$underscore_slug = str_replace( '-', '_', $slug );
			if ( function_exists('apcu_fetch') && !isset($_GET["cache"]) ) {
				$event_id = apcu_fetch('c2_slug_cache_'.$underscore_slug);
			}
			if ( !$event_id ) {
				$page = get_posts(array('name'=> $slug,'post_type'=>'page'))[0];
				
				//print_r($page);
				
				$event_id = $page->ID;
				
				if ( function_exists('apcu_store') ) {
					apcu_store('c2_slug_cache_'.$underscore_slug, $event_id);
				}
			}
			
			echo $event_id . " vs " . $user_id;
			
?>
		<div class="event">
			<div class="info">
				<div class="navigation">
<?php			if ( is_user_logged_in() ) { ?>
					<a href="/compo/<?php echo $slug?>/?action=edit"><strong>Submit</strong></a> | 
<?php			} ?>
					<a href="/compo/<?php echo $slug?>/?action=preview">View All</a>
				</div>
				<div class="name">On Now: <strong><a href="<?php echo $name_url; ?>"><?php echo $name; ?></a></strong></div>
			</div>
		</div>
<?php
		}
	}
	else {
		if ( !is_paged() ) {
?>
		<div class="event">
			<div class="info">
				<div class="navigation">
<?php			if ( is_user_logged_in() ) { ?>
					<a href="/compo/<?php echo $slug?>/?action=edit"><strong>Submit</strong></a> | 
<?php			} ?>
					<a href="/compo/<?php echo $slug?>/?action=preview">View All</a>
				</div>
				<div class="name">On Now: <strong><a href="<?php echo $name_url; ?>"><?php echo $name; ?></a></strong></div>
			</div>
		</div>
<?php
		}
	}	
}

?>