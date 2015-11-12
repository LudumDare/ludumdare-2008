<?php
require_once __DIR__."/config.php";

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

echo '{"isreal":true}';

?>