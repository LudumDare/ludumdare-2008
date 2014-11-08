<?php
defined('ABSPATH') or die("No.");

//$ldvar = NULL;



function ld_has_vars_table() {
	$table_name = $ld_vars_table_name;
	return mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '{$table_name}'")) !== 0;
}
function ld_new_vars_table() {
	// Create Table //
	$query = 
		"CREATE TABLE " . $ld_vars_table_name . " (
			name VARCHAR(32) NOT NULL UNIQUE,
			value TEXT NOT NULL
		) ENGINE=InnoDB;";
					
	// Populate with some default values //
	// CurrentEvent = this one;
	
}


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


function ld_get_vars() {
	$ret = ld_get_vars_cache();
	if ( $ret ) {
		return $ret;
	}	
	else if ( ld_has_vars_table() ) {
		//$ret = ld_get_vars_table();
		ld_put_vars_cache( $ret );
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