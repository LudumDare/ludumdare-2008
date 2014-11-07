<?php
defined('ABSPATH') or die("No.");

function ldjam_is_admin() {
	return current_user_can( 'manage_options' );
}

?>