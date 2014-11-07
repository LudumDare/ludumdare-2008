<?php
defined('ABSPATH') or die("No.");

function ld_is_admin() {
	return current_user_can( 'manage_options' );
}

?>