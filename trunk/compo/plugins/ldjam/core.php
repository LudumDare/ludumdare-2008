<?php
defined('ABSPATH') or die("No.");
// - ----------------------------------------------------------------------------------------- - //
require_once "lib.php";		// Helper Functions //
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
global $ld_table_prefix;
global $ldvar;
// - ----------------------------------------------------------------------------------------- - //
$ld_table_prefix = "ld_";
$ldvar = NULL;
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
static $ld_vars_table_name;
$ld_vars_table_name = $ld_table_prefix . "vars";
// - ----------------------------------------------------------------------------------------- - //
// LD Variable Cache - APCU //
global $has_apcu;
if ( $has_apcu ) {
	static $ldvar_cache_name = "ld_vars";
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
// - ----------------------------------------------------------------------------------------- - //
function ld_set_var_table( $key, $value ) {
	global $ld_vars_table_name;
	
	// store in database //
	lddb_query("
		INSERT INTO {$ld_vars_table_name} (
			name,
			value
		)
		VALUES (
			\"{$key}\",
			\"{$value}\"
		)
		ON DUPLICATE KEY UPDATE
			value=VALUES(value)
	;");
}	
// - ----------------------------------------------------------------------------------------- - //
function ld_set_var( $key, $value ) {
	ld_set_var_table($key,$value);

	global $ldvar;
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
	return lddb_does_table_exist( $ld_vars_table_name );
}
function ld_new_vars_table() {
	global $ld_vars_table_name;

	// Create Table //
	lddb_query( 
		"CREATE TABLE {$ld_vars_table_name} (
			name VARCHAR(32) NOT NULL UNIQUE,
			value TEXT NOT NULL
		) ENGINE=InnoDB;"
	);
					
	// Populate with some default values //
	// CurrentEvent = this one;
	
	ld_set_var_table("event","root");
	ld_set_var_table("event_active","true");
}
function ld_get_vars_table() {
	global $ld_vars_table_name;
	$vars = lddb_get( "SELECT * FROM {$ld_vars_table_name};" );
	$ret = [];
	foreach ( $vars as $var ) {
		$ret[$var['name']] = $var['value'];
	}
	return $ret;
}
// - ----------------------------------------------------------------------------------------- - //
?>