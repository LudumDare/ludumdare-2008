<?php
// Sorry for the generic file name. I need a clean space to work. //
// This is a collection of things for the Compo system, that are  //
// hopefully never needed again come mid next year. //

function c2_get_game($event_id,$user_id) {
	global $wpdb;
	$ret = array_pop($wpdb->get_results( "SELECT * FROM c2_entry WHERE cid = {$event_id} AND uid = {$user_id} LIMIT 1", ARRAY_A ));
	
	if ( $ret ) {
		// Unserialize the Data //
		foreach ($ret as $key => $val) {
			$out = null;
			if ( $out = unserialize($val) ) {//is_string($val) ) {
				$ret[$key] = $out;//unserialize($val);
			}
		}
	}
	
	return $ret;
}

function c2_set_game($event_id,$user_id,$game) {
	// Serialize the Data //
	foreach ($game as $key => $val) {
		if ( is_array($val) ) {
			$game[$key] = serialize($val);
		}
	}
	
	var_dump($game);	
	
//	global $wpdb;
//	$ret = array_pop($wpdb->get_results( "SELECT * FROM c2_entry WHERE cid = {$event_id} AND uid = {$user_id} LIMIT 1", ARRAY_A ));
	
}

// This Function is called in the style sheet to display Navigation //
function c2_navigation($slug,$name,$name_url) {
	//if ( !is_paged() ) { // First Page Only // 
	{
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
		
		$game = c2_get_game($event_id,$user_id);

		if ( current_user_can('edit_others_posts') ) {
			// MK: END NIGHT
			var_dump($game);
			/*
			echo "<br/><br/>";
			c2_set_game($event_id,$user_id,$game);
			*/
			
			//echo $event_id . " vs " . $user_id;
		}

?>
	<div class="event">
		<div class="info">
			<div class="navigation" style="">
<?php			if ( is_user_logged_in() ) {
					if ( $game ) {?>
						<a href="/compo/<?php echo $slug?>/"><strong>Play and Rate Games</strong></a> | 
						<a href="/compo/<?php echo $slug?>/?action=edit">Edit</a> | 
<?php					}
				else {?>
						<a style="font-size:15px" href="/compo/<?php echo $slug?>/?action=edit"><strong>SUBMIT NOW</strong></a> | 
<?php				}
				} ?>
				<a href="/compo/<?php echo $slug?>/?action=preview">View All</a>
			</div>
			<div class="name">On Now: <strong><a href="<?php echo $name_url; ?>"><?php echo $name; ?></a></strong></div>
<?php		if ( is_user_logged_in() ) {
				if ( $game ) {?>
					<div class="name" style="text-align:right;margin-top:10px;"><strong><a href="/compo/<?php echo $slug?>/?action=preview&uid=<?php echo $game['uid'];?>"><?php echo $game['title']; ?></a></strong></div>
					<div class="name" style="text-align:right;">by <?php echo $game['get_user']['display_name'];?> (<?php echo $game['etype'];?>)</div>
					<div class="name" style="text-align:right;">Votes: <strong><?php echo $game['rate_in']; ?></strong> Comments: <strong>?</strong></div>
					<div class="name" style="text-align:right;">Coolness: <strong><?php echo $game['rate_out']; ?></strong> Bonus: <strong>?</strong></div>
					<div class="name" style="text-align:right;display:none">Game Love: <strong><?php echo $game['love']; ?></strong> Comment Love: <strong>?</strong></strong></div>

<?php				$total_cat = 8-count($game['settings']['OPTOUT'][$game['etype']]);?>
					<div class="name" style="text-align:right;">You are being judged in <strong><?php echo $total_cat; ?></strong> categories</div>
<?php				if ( isset($game['settings']['OPTOUT'][$game['etype']]) ) {
						foreach ( $game['settings']['OPTOUT'][$game['etype']] as $cat ) {
							if ( strpos($cat,"Overall") === 0 ) {?>
								<div class="name" style="text-align:right;"><strong>WARNING</strong>: You have opted-out of Overall!<br />Is this correct?</div>
<?php							break;
							}
						}
					} ?>
					
<?php				if ( $game['rate_in'] < 20 ) { ?>
						<div class="name" style="text-align:right;margin-top:10px;"><strong>You need more votes!</strong> Rate more games!</div>
<?php				} ?>
<?php			}
			} ?>
		</div>
	</div>
<?php
	}
}

?>