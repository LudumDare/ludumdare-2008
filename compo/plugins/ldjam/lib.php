<?php
defined('ABSPATH') or die("No.");
// - ----------------------------------------------------------------------------------------- - //
// My little collection of helper functions //
// - ----------------------------------------------------------------------------------------- - //
global $has_apcu;
$has_apcu = function_exists('apcu_fetch');	// Check if APCu is available (memory caching) //
// - ----------------------------------------------------------------------------------------- - //
function to_bool( $value ) {
	$ret = strtoupper($value) === "FALSE" ? false : true;
	if ( $ret ) {
		return (bool)$value;
	}
	return $ret;
}
// - ----------------------------------------------------------------------------------------- - //
function to_slug( $str, $delimiter='-' ) {
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
//	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	
	return $clean;
}
// - ----------------------------------------------------------------------------------------- - //
?>
