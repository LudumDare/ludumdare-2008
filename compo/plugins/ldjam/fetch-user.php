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

$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (core_OnWhitelist($_SERVER['REMOTE_ADDR'],$IP_WHITELIST)) {
		$id = intval($_POST['id']);
		$user = get_user_by('id',$id);
		
		if ( $user ) {
			// Data is good. Extract the pieces we want. //
			$data = [
				'id' => intval($user->ID),
				'register_date' => $user->data->user_registered,
				'name' => $user->data->display_name,
//				'slug' => $user->data->user_nicename,
//				'login' => $user->data->user_login,
//				'mail' => $user->data->user_email,
				'gravatar' => md5(strtolower(trim( $user->data->user_email ))),   
			];

			{
				// Do a Query to figure out how many Ludum Dare entries a user has //
				global $wpdb;
				$result = $wpdb->get_results(
					"SELECT uid,cid,results FROM c2_entry WHERE
					uid=".$data['id']." AND active=1;",
					ARRAY_A
				);
				
				$data['num_events'] = 0;
				
				foreach ($result as $item) {
					if ( $item[''] !== null )
						$data['num_events']++;
				}
			}
			
			

			//$user['caps']['administrator']
			//$user['roles'] // Array of strings including 'administrator'
		}
		else {
			// If we're unable to fetch, return a null Id.
			$data['id'] = 0;
		}
	}
}

header('Content-Type: application/json');
echo json_encode($data);
die();

?>