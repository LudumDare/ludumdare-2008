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

require_once "lib.php";				// Helper Functions //
require_once "core.php";			// LDJam Core //


function shortcode_ldjam( $atts ) {
	ld_get_vars();	// Populate the $ldvar global //
	global $ldvar;
	
	print_r($_GET);
	echo( to_slug($_GET['u']) );
	
	return "I am very important";
}
add_shortcode( 'ldjam', 'shortcode_ldjam' );


function shortcode_ldjam_root( $atts ) {
	ld_get_vars();	// Populate the $ldvar global //
	global $ldvar;
	
	if ( ld_is_admin() ) {
		$out = "";

		if ( strtolower($_SERVER['REQUEST_METHOD']) === "post" ) {
			print_r($_POST);
			if ( isset($_POST['event_active']) ) {
				ld_set_var('event_active', !to_bool($_POST['event_active']) ? "true" : "false" );
			}
		}
		
		$out .= '
			<form method="post">
				<input type="hidden" name="event_active" value="'.$ldvar['event_active'].'">
				<input type="submit" value="'.$ldvar['event_active'].'">
			</form>';
		
		return $out;
	}
	else {
		return "";
	}
}
add_shortcode( 'ldjam-root', 'shortcode_ldjam_root' );


/* This goes in the theme, so a shortcode isn't possible */
function ldjam_show_bar() {
	ld_get_vars();	// Populate the $ldvar global //
	global $ldvar;
	
	if ( to_bool($ldvar['event_active']) ) {
		return "On Now: <strong>{$ldvar['event']}</strong>";
	}
	
	// No bar //
	return "";
}


function ldjam_activate() {
	ld_init_vars();
}
register_activation_hook( __FILE__, "ldjam_activate");

?>