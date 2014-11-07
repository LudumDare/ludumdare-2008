<?php
defined('ABSPATH') or die("No.");

$has_apcu = function_exists('apcu_fetch');
$ldata_prefix = 'ldata_';

function ldata_fetch( $key ) {
	if ( $has_apcu ) {
		
	}
	
	return NULL;
}

function ldata_store( $key, $value ) {
	if ( $has_apcu ) {
		
	}	
}

?>