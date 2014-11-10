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

// - ----------------------------------------------------------------------------------------- - //
// http://programanddesign.com/php/base62-encode/
// MK: Removed 'iuIU' to make it harder to generate F-bombs, S-bombs, and C-bombs //
// - ----------------------------------------------------------------------------------------- - //
/**
 * Converts a base 10 number to any other base.
 * 
 * @param int $val   Decimal number
 * @param int $base  Base to convert to. If null, will use strlen($chars) as base.
 * @param string $chars Characters used in base, arranged lowest to highest. Must be at least $base characters long.
 * 
 * @return string    Number converted to specified base
 */
function base_encode($val, $base=58, $chars='0123456789abcdefghjklmnopqrstvwxyzABCDEFGHJKLMNOPQRSTVWXYZ') {
    if(!isset($base)) $base = strlen($chars);
    $str = '';
    do {
        $m = bcmod($val, $base);
        $str = $chars[$m] . $str;
        $val = bcdiv(bcsub($val, $m), $base);
    } while(bccomp($val,0)>0);
    return $str;
}
// - ----------------------------------------------------------------------------------------- - //
/**
 * Convert a number from any base to base 10
 * 
 * @param string $str   Number
 * @param int $base  Base of number. If null, will use strlen($chars) as base.
 * @param string $chars Characters use in base, arranged lowest to highest. Must be at least $base characters long.
 * 
 * @return int    Number converted to base 10
 */
function base_decode($str, $base=58, $chars='0123456789abcdefghjklmnopqrstvwxyzABCDEFGHJKLMNOPQRSTVWXYZ') {
    if(!isset($base)) $base = strlen($chars);
    $len = strlen($str);
    $val = 0;
    $arr = array_flip(str_split($chars));
    for($i = 0; $i < $len; ++$i) {
        $val = bcadd($val, bcmul($arr[$str[$i]], bcpow($base, $len-$i-1)));
    }
    return $val;
}
// - ----------------------------------------------------------------------------------------- - //
?>
