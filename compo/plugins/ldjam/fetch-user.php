<?php
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";

// User is NOT known //

// Shorthand function for checking a configuration whitelist //
function core_OnWhitelist( $ip, $list ) {
	if ( is_string($list) ) {
		$list = [$list];
	}
	
	foreach ( $list as $item ) {
		if ( $item == $ip )
			return true;
	}
	
	return false;
}

//if (core_OnWhitelist($_SERVER['REMOTE_ADDR'],$IP_WHITELIST)) {

$data = [];

$data['am_i_real'] = false;
$data['whitelist'] = $IP_WHITELIST;
$data['you'] = $_SERVER['REMOTE_ADDR'];

header('Content-Type: application/json');
echo json_encode($data);
die();

?>