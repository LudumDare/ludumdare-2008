<?php
// Check wordpress credentials //
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";


function GetHash($id) {
	$url = $GLOBALS['LEGACY_HASH_URL'];
	$data = [
		'action' => "GET_HASH",
		'id' => $id
	];

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);
	
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	if ( $result !== false ) {
		
		var_dump($result);
		return json_decode($result);
	}	
	return false;
}

// User is known, so fetch it //
$user = wp_get_current_user();

if ( isset($user->ID) && ($user->ID > 0) ) {
	$id = $user->ID;
	$hash = GetHash($data['id'])->hash;
	
	// Set Cookie //
	setcookie( "lusha", $id.".".$hash, time()+2*24*60*60, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
	
	// Redirect //
	header("Location: http://theme.ludumdare.com");
	
	//echo '<!doctype html>';
	//echo '<html><head><meta http-equiv="Location" content="http://example.com/"></head>';
	//echo '<body><a href="http://theme.ludumdare.com">http://theme.ludumdare.com</a></body></html>';

	die();
}
else {
	// TODO: Include data for WP-login redirect to send it back here after logging in //
	header("Location: http://ludumdare.com/compo/wp-login.php");
	die();	
}
