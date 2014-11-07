<?php
defined('ABSPATH') or die("No.");
/*
Plugin Name: LDJam
Plugin URI: http://ludumdare.com/
Description: Ludum Dare Game Jam Website
Version: 0.1
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: TBD
*/

require_once "wp_functions.php";
require_once "ld_functions.php";


function shortcode_ldjam( $atts ) {
	return "I am very important";	
}
add_shortcode( 'ldjam', 'shortcode_ldjam' );


function shortcode_ldjam_root( $atts ) {
	if ( ldjam_is_admin() ) {
		return "Thanks Chiefy";
	}
	else {
		return "";
	}
}
add_shortcode( 'ldjam-root', 'shortcode_ldjam_root' );


/* This goes in the theme, so a shortcode isn't possible */
function ldjam_show_bar() {
	return "On Now: <strong>I don't know. I'm testing here!</strong>";
}


		$ld_table_prefix = "ld_";



/*
		$content_table_name = $ld_table_prefix . "content";
			
			$query = 
				"CREATE TABLE " . $content_table_name . " (
					ID SERIAL,
					parent_id BIGINT UNSIGNED NOT NULL,
					owner_id BIGINT UNSIGNED NOT NULL,
					type VARCHAR(16) NOT NULL,
					slug VARCHAR(64) NOT NULL,
					published TIMESTAMP NOT NULL,
					updated TIMESTAMP NOT NULL,
					
					
				);";
*/				
			// ID - Unique ID of this object.
			// parent_id - Object 
			// owner_id
			// type - TYPES SHOULD BE 10 CHARACTERS OR LESS (so we can append 'Draft-')


// event (
// ld32 (Event) - Ludum Dare 32. Child of Events?
// unity (Tool) - Unity 3D. Child of Root?
// 

// All root level things have parent of "root" (ID=1) //
// Ludum Dare Events, "ex" (external events), "user", "tool"


// root
//   - ld
//   - mini
//   - ex

// reserved words (also include plural:
// root, post, game, team, league, warmup, artjam, jam, compo, 

// O = Node
// * = Smart Node. One of the levels is an actual node, but its children are generated.

// O ludumdare.com/ld32/my-horrible-xmas
// O ludumdare.com/oc2014/batman-2
// O ludumdare.com/ex/ggj2014/the-day-i-ate-hell

//   ludumdare.com/p/2014/08/14/ima-here-to-shoot-you-dead
// * ludumdare.com/user/pov (generated, contains USER table data, not typical nodes)
// * ludumdare.com/team/death-blow (a search of all teams named 'Death Blow')

//   ludumdare.com/ld32/texas-masquerade
//   ludumdare.com/ld32/ld2b-s2/episode-04
//   ludumdare.com/ld/32/texas-masquerade
//   ludumdare.com/ld/32/ld2b-s2/episode-04
//   ludumdare.com/ld/32/warmup/bathtub-simulator

//   ludumdare.com/mld/65/shotgun-house-arrest
//   ludumdare.com/minild/81/spam-was-mother
//   ludumdare.com/mini/81/southern-hemispheroid
//   ludumdare.com/mini/feb2015/southern-hemispheroid

// O ludumdare.com/ld32/team/death-blow
//   ludumdare.com/ld32/p/pov/son-of-a-big-wide-dog-2-0


//   ldj.am/cB3eE    (one slash = node)
//   ldj.am/u/Ueg7   (two slash = 

// IN (ID1,ID2,ID3,etc).
// SELECT * FROM `shirts` WHERE `color` IN (‘red’,’white’,’blue’)

//$colors = array('red','white','blue');
//$sql = "SELECT * FROM `shirts` WHERE `color` IN ('" . implode("','", $colors) . "')" ;

/*
					service_id TINYINT UNSIGNED NOT NULL,
					user_id VARCHAR(32) NOT NULL,
					PRIMARY KEY ID (service_id,user_id),
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
						INDEX (timestamp),
					
					name VARCHAR(32) NOT NULL,
					display_name VARCHAR(32) NOT NULL,					
					site_id BIGINT UNSIGNED NOT NULL,
					media_id VARCHAR(32) NOT NULL,
					followers BIGINT UNSIGNED NOT NULL,
					viewers BIGINT UNSIGNED NOT NULL,
					avatar TEXT NOT NULL,
					url TEXT NOT NULL,
					embed_url TEXT NOT NULL,
					status TEXT NOT NULL,
					mature BOOLEAN NOT NULL,
										
					units BIGINT UNSIGNED NOT NULL
				);";
*/


?>