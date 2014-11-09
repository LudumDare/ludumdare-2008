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


ld_get_vars();	// Populate the $ldvar global //

function shortcode_ldjam( $atts ) {
	return "I am very important";	
}
add_shortcode( 'ldjam', 'shortcode_ldjam' );


function shortcode_ldjam_root( $atts ) {
	global $ldvar;
	if ( ld_is_admin() ) {
		$out = "";

		if ( strtolower($_SERVER['REQUEST_METHOD']) === "post" ) {
			print_r($_POST);
			if ( isset($_POST['event_active']) ) {
				$state = strtoupper($_POST['event_active']) === "FALSE" ? false : true;
				if ( $state ) { $state = (bool)$_POST['event_active']; }
				ld_set_var('event_active', !$state ? "true" : "false" );
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
	global $ldvar;
	$event_active = strtoupper($ldvar['event_active']) === "FALSE" ? false : true;
	if ( $event_active ) { $event_active = (bool)$_POST['event_active']; }


	if ( $event_active ) {
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