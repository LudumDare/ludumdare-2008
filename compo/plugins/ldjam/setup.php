<?php
defined('ABSPATH') or die("No.");

require_once "config.php";

//$ldvar = NULL;


// ??? //
function ld_setup() {
	//if ( 
	// ??? //
}


function ld_has_vars_table() {
//	return ld_db_
}
function ld_new_vars_table() {
	// Create Table //
	
	// Populate with some default values //
	// CurrentEvent = this one;
	
}
function ld_get_vars_cache() {
//	return ld_db_
}

function ld_get_vars() {
	$ret = ld_get_vars_cache();
	if ( $ret !== NULL ) {
		return $ret;
	}	
	else if ( ld_has_vars_table() ) {
		//$ret = ld_get_vars_table();
		//ld_put_vars_cache( $ret );
	}
	else {
		ld_new_vars_table();
		//$ret = ld_get_vars_table();
		//ld_put_vars_cache( $ret );
	}
	reutrn $ret;
}

function ld_set_var( $key, $value ) {
	// store database
	//$ldvar[$key] = $value;
	//ld_put_vars_cache( $ldvar );
}



function ld_has_content_table() {
}
function ld_new_content_table() {	
}

//		$ld_table_prefix = "ld_";
//		$content_table_name = $ld_table_prefix . "content";
			

?>