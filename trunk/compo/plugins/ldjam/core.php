<?php
defined('ABSPATH') or die("No.");
// - ----------------------------------------------------------------------------------------- - //
require_once "lib.php";				// Helper Functions //
require_once "wp_functions.php";	// WordPress Database Functions //
//require_once "ld_functions.php";	// General Database Functions //
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
global $ldvar;
global $ld_url_cache;
global $ld_table_prefix;
// - ----------------------------------------------------------------------------------------- - //
$ldvar = NULL;
$ld_url_cache = NULL;
$ld_table_prefix = "ld_";
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
static $ld_vars_table_name;
$ld_vars_table_name = $ld_table_prefix . "vars";
static $ld_url_cache_table_name;
$ld_url_cache_table_name = $ld_table_prefix . "url_cache";
// - ----------------------------------------------------------------------------------------- - //
static $ld_content_table_name;
$ld_content_table_name = $ld_table_prefix . "content";
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
// LD Variable Cache - APCU //
global $has_apcu;
if ( $has_apcu ) {
	function ld_get_vars_cache() {
		global $ld_vars_table_name;
		return apcu_fetch( $ld_vars_table_name );
	}
	function ld_put_vars_cache( $vars ) {
		global $ld_vars_table_name;
		apcu_store( $ld_vars_table_name, $vars );
	}
	function ld_get_url_cache_cache() {
		global $ld_url_cache_table_name;
		return apcu_fetch( $ld_url_cache_table_name );
	}
	function ld_put_url_cache_cache( $vars ) {
		global $ld_url_cache_table_name;
		apcu_store( $ld_url_cache_table_name, $vars );
	}
}
// LD Variable Cache - None //
else {
	function ld_get_vars_cache() { return NULL; }
	function ld_put_vars_cache( $vars ) { }	
	function ld_get_url_cache_cache() { return NULL; }
	function ld_put_url_cache_cache( $vars ) { }	
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
		global $ld_vars_table_name;
	
		// Create Table //
		lddb_query( 
			"CREATE TABLE {$ld_vars_table_name} (
				name VARCHAR(32) NOT NULL UNIQUE,
				value TEXT NOT NULL
			) ENGINE=InnoDB;"
		);
						
		// Populate with some default values //
		ld_set_var_table("event","root");
		ld_set_var_table("event_active","true");
	}
}
// - ----------------------------------------------------------------------------------------- - //
function ld_has_vars_table() {
	global $ld_vars_table_name;
	return lddb_does_table_exist( $ld_vars_table_name );
}
// - ----------------------------------------------------------------------------------------- - //
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




// - ----------------------------------------------------------------------------------------- - //
function ld_get_url_cache() {
	global $ld_url_cache;
	$ld_url_cache = ld_get_url_cache_cache();
	if ( $ld_url_cache ) {
		return;
	}	

	$ld_url_cache = ld_get_url_cache_table();
	ld_put_url_cache_cache( $ld_url_cache );
}
// - ----------------------------------------------------------------------------------------- - //
function ld_set_url_cache_table( $url, $id ) {
	global $ld_url_cache_table_name;
	
	// store in database //
	lddb_query("
		INSERT INTO {$ld_url_cache_table_name} (
			url,
			content_id
		)
		VALUES (
			\"{$url}\",
			\"{$id}\"
		)
		ON DUPLICATE KEY UPDATE
			id=VALUES(id)
	;");
}	
// - ----------------------------------------------------------------------------------------- - //
function ld_set_url_cache( $url, $id ) {
	ld_set_url_cache_table($url,$id);

	global $ld_url_cache;
	$ld_url_cache[$url] = $id;
	ld_put_url_cache_cache( $ld_url_cache );
}
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
function ld_init_url_cache() {
	if ( !ld_has_url_cache_table() ) {
		global $ld_url_cache_table_name;
	
		// Create Table //
		lddb_query( 
			"CREATE TABLE {$ld_url_cache_table_name} (
				url VARCHAR(260) NOT NULL UNIQUE,
				content_id BIGINT UNSIGNED NOT NULL
			) ENGINE=InnoDB;"
		);
	}
}
// - ----------------------------------------------------------------------------------------- - //
function ld_has_url_cache_table() {
	global $ld_url_cache_table_name;
	return lddb_does_table_exist( $ld_url_cache_table_name );
}
// - ----------------------------------------------------------------------------------------- - //
function ld_get_url_cache_table() {
	global $ld_url_cache_table_name;
	$urls = lddb_get( "SELECT * FROM {$ld_url_cache_table_name};" );
	$ret = [];
	foreach ( $urls as $url ) {
		$ret[$url['url']] = $var['content_id'];
	}
	return $ret;
}
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
// - ----------------------------------------------------------------------------------------- - //

?>