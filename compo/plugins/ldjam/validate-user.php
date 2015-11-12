<?php
// Check wordpress credentials //
require_once __DIR__."/../../../wp-load.php";
require_once __DIR__."/config.php";

// TODO: check if not logged in 

$wp_user = wp_get_current_user();

$data = [
	'id' => $wp_user['ID'],
//	'name' => $wp_user['data']['display_name'],
//	'slug' => $wp_user['data']['user_nicename'],
//	'mail' => $wp_user['data']['user_email'],
	'gravatar' => md5(strtolower(trim( $wp_user['data']['user_email'] ))),
//	'register_date' => $wp_user['data']['user_registered'],
//	'login' => $wp_user['data']['user_login'],
];

//$wp_user['caps']['administrator']
//$wp_user['roles'] // Array of strings including 'administrator'


//	$query = [
//		'action'=>"LEGACY_FETCH",
//		'id'=>$id
//	];
//	
//	$result = http_post_fields(LEGACY_FETCH_URL,$query);
//	
//	if ( $result !== false ) {
//		var_dump($result);
//		return json_decode($result);
//	}

var_dump($data);

// Set Cookie //
//setcookie( "lusha", "1234", time()+2*24*60*60, "/", ".ludumdare.com" );

// Redirect //
//header("Location: http://theme.ludumdare.com");

//echo '<!doctype html>';
//echo '<html><head><meta http-equiv="Location" content="http://example.com/"></head>';
//echo '<body><a href="http://theme.ludumdare.com">http://theme.ludumdare.com</a></body></html>';

die();
