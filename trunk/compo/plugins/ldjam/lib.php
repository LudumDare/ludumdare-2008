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
function to_slug( $str, $delimiter='-' /*, $maxlength=260*/ ) {
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);			// Convert and discard non-ascii characters //
	$clean = str_replace('\\', '/', $clean);							// Unix Slashes //
	$clean = preg_replace("/[^a-zA-Z0-9\/-]/", '-', $clean);			// Change everything else to dashes //
	$clean = strtolower(trim($clean));									// Trim and lower the case //
	$clean = preg_replace("/[-]+/", $delimiter, $clean);				// Replace sets of -'s with a single dash //
//	$clean = substr($clean,0,$maxlength);								// Maximum String Length
	
	return $clean;
}
// - ----------------------------------------------------------------------------------------- - //
?>
