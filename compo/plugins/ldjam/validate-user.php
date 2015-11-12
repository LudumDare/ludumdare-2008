<?php
// Check wordpress credentials //
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";


function GetHash($id) {
	$url = LEGACY_HASH_URL;
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
	
//	$data = [
//		'action' => "GET_HASH",
//		'id' => $id
//	];
//	$result = http_post_fields(LEGACY_HASH_URL,$query);
	
	if ( $result !== false ) {
		return json_decode($result);
	}	
	return false;
}

// User is known, so fetch it //
$user = wp_get_current_user();

if ( $user ) {
	$data = [
		'id' => $user->ID,
		'hash' => "unknown_hash",
	//	'name' => $user['data']['display_name'],
	//	'slug' => wp_user['data']['user_nicename'],
	//	'mail' => wp_user['data']['user_email'],
		'gravatar' => md5(strtolower(trim( $user->data->user_email ))),
	//	'register_date' => $user['data']['user_registered'],
	//	'login' => $user['data']['user_login'],
	];
	
	//$user['caps']['administrator']
	//$user['roles'] // Array of strings including 'administrator'
	
	//$data['hash'] = GetHash($data['id'])['hash'];
	var_dump($data,GetHash($data['id']));
	
	// Set Cookie //
	//setcookie( "lusha", $data['id'].".".$data['hash'], time()+2*24*60*60, "/", str_replace("theme","",$_SERVER['SERVER_NAME']) );
	
	// Redirect //
	//header("Location: http://theme.ludumdare.com");
	
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
