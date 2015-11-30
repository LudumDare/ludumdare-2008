<?php
// Check wordpress credentials //
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";

function FetchHash($id) {
	$url = $GLOBALS['LEGACY_HASH_URL'];
	$query = [
		'action' => "GET_HASH",
		'id' => $id,
		'ip' => isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : "0.0.0.0"
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
	$result = @file_get_contents($url, false, $context);
	
	return $result;
}

// User is known, so fetch it //
$user = wp_get_current_user();

if ( isset($user->ID) && ($user->ID > 0) ) {
	$id = $user->ID;
	$data = FetchHash($id);
	
	$cookie_length = 6*60*60; //2*24*60*60;

	// Make Cookies (Yummy) - The dot prefix (.) means it's an error //	
	if ( $data ) {
		$decoded = json_decode($data);
		
		if ( empty($decoded) ) {
			setcookie( "lusha", ".".$id.".empty", time()+$cookie_length, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
		}
		else {
			setcookie( "lusha", $id.".".$decoded->hash, time()+$cookie_length, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
		}
	}
	else {
		setcookie( "lusha", ".".$id.".fetch_failed", time()+$cookie_length, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
	}
	
	$RETURN_URL = "http://theme.ludumdare.com";
	if ( isset($_GET['beta']) )
		$RETURN_URL .= "/?beta";

	// Redirect //
	header("Location: ".$RETURN_URL);
	
	//echo '<!doctype html>';
	//echo '<html><head><meta http-equiv="Location" content="http://example.com/"></head>';
	//echo '<body><a href="http://theme.ludumdare.com">http://theme.ludumdare.com</a></body></html>';

	die();
}
else {
	header("Location: http://ludumdare.com/compo/wp-login.php?".
		http_build_query(['redirect_to' => "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}".(isset($_GET['beta'])?"&beta":"")])
	);
	die();	
}
