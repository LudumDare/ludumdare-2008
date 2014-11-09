<?php
defined('ABSPATH') or die("No.");
// - ----------------------------------------------------------------------------------------- - //
// Check if APCu is available (memory caching) //
global $has_apcu;
$has_apcu = function_exists('apcu_fetch');
// - ----------------------------------------------------------------------------------------- - //
global $ld_table_prefix;
$ld_table_prefix = "ld_";
global $ld_vars_table_name;
$ld_vars_table_name = $ld_table_prefix . "vars";

global $ldvar;
$ldvar = NULL;
// - ----------------------------------------------------------------------------------------- - //
// LD Variable Cache - APCU //
if ( $has_apcu ) {
	$ldvar_cache_name = "ld_vars";
	function ld_get_vars_cache() {
		global $ldvar_cache_name;
		return apcu_fetch( $ldvar_cache_name );
	}
	function ld_put_vars_cache( $vars ) {
		global $ldvar_cache_name;
		apcu_store( $ldvar_cache_name, $vars );
	}
}
// LD Variable Cache - None //
else {
	function ld_get_vars_cache() {
		return NULL;
	}
	function ld_put_vars_cache( $vars ) {
	}	
}
// LD Variable Cache //
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
function ld_get_vars() {
	global $ldvar;
	$ldvar = ld_get_vars_cache();
	if ( $ldvar ) {
		return;
	}	

	$ldvar = ld_get_vars_table();
	ld_put_vars_cache( $ldvar );
}
ld_get_vars();	// Call Immediately //
// - ----------------------------------------------------------------------------------------- - //
function ld_set_var( $key, $value ) {
	// store database
	
	$ldvar[$key] = $value;
	ld_put_vars_cache( $ldvar );
}
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
function ld_init_vars() {
	if ( !ld_has_vars_table() ) {
		ld_new_vars_table();
	}

	ld_get_vars();
}
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
function ld_has_vars_table() {
	global $ld_vars_table_name;
	error_log("Shem Mike: " .  $ld_vars_table_name );
	return lddb_does_table_exist( $ld_vars_table_name );
}
function ld_new_vars_table() {
	global $ld_vars_table_name;
	error_log("Whoa Mike: " .  $ld_vars_table_name );

	// Create Table //
	lddb_query( 
		"CREATE TABLE {$ld_vars_table_name} (
			name VARCHAR(32) NOT NULL UNIQUE,
			value TEXT NOT NULL
		) ENGINE=InnoDB;"
	);
					
	// Populate with some default values //
	// CurrentEvent = this one;
	
}
function ld_get_vars_table() {
	global $ld_vars_table_name;
	error_log("Hey Mike: " .  $ld_vars_table_name );
	return lddb_get( "SELECT * FROM {$ld_vars_table_name};" );
}
// - ----------------------------------------------------------------------------------------- - //


//$ldata_prefix = 'ldata_';
//function ldata_fetch( $key ) {
//	if ( $has_apcu ) {
//		
//	}
//	
//	return NULL;
//}
//
//function ldata_store( $key, $value ) {
//	if ( $has_apcu ) {
//		
//	}	
//}

?>