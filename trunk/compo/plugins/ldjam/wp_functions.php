<?php
defined('ABSPATH') or die("No.");

function ld_is_admin() {
	return current_user_can( 'manage_options' );
}

function ld_redirect( $url ) {
	wp_redirect( $url ); 
	exit;
}

// Database Functions //
function lddb_does_table_exist( $name ) {
	global $wpdb;
	return $wpdb->get_var("SHOW TABLES LIKE '{$name}';") === $name;
	//return mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '{$ld_vars_table_name}'")) !== 0;
}

function lddb_query( $query ) {
	global $wpdb;
	return $wpdb->query($query);
}
function lddb_get( $query ) {
	global $wpdb;
	return $wpdb->get_results($query, ARRAY_A);
}


?>