<?php
// Check wordpress credentials //
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";


function FetchHash($id) {
	$url = $GLOBALS['LEGACY_HASH_URL'];
	$query = [
		'action' => "GET_HASH",
		'id' => $id
	];

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($query),
	    ),
	);
	
	$context = stream_context_create($options);
	return file_get_contents($url, false, $context);

//	$result = file_get_contents($url, false, $context);
//
//	if ( $result !== false ) {
//		$decoded = json_decode($result);
//		if ( empty($decoded) )
//			return false;
//		return $decoded->hash;
//	}	
//	return false;
}

// User is known, so fetch it //
$user = wp_get_current_user();

if ( isset($user->ID) && ($user->ID > 0) ) {
	$id = $user->ID;
	$data = FetchHash($id);

	// Make Cookies (Yummy) - The dot prefix (.) means it's an error //	
	if ( $data ) {
		$decoded = json_decode($data);
		
		if ( empty($decoded) ) {
			setcookie( "lusha", ".".$id.".empty", time()+2*24*60*60, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
		}
		else {
			setcookie( "lusha", $id.".".$decoded->hash, time()+2*24*60*60, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
		}
	}
	else {
		setcookie( "lusha", ".".$id.".fetch_failed", time()+2*24*60*60, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
	}

	// Redirect //
	header("Location: http://theme.ludumdare.com");
	
	//echo '<!doctype html>';
	//echo '<html><head><meta http-equiv="Location" content="http://example.com/"></head>';
	//echo '<body><a href="http://theme.ludumdare.com">http://theme.ludumdare.com</a></body></html>';

	die();
}
else {
	// TODO: Include data for WP-login redirect to send it back here after logging in //
	header("Location: http://ludumdare.com/compo/wp-login.php?".
		http_build_query({'redirect_to',"//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"})
	);
	die();	
}
