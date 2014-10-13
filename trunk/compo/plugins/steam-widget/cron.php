<?php
// - ----------------------------------------------------------------------------------------- - //
// Store the current directory part of the requested URL (for building paths to files) //
@$http_dir = dirname($_SERVER["REQUEST_URI"]);
chdir(dirname(__FILE__));	// Change Working Directory to where I am (for my local paths) //
// - ----------------------------------------------------------------------------------------- - //
// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	echo "Clever girl.\n";
	echo "<br /><br /><img src='" . $http_dir . "/hacking.gif' />";
	exit(1);
}
// - ----------------------------------------------------------------------------------------- - //
// Get Wordpress Setup Variables (optional... sort of) //
@include "../../../wp-config.php";
// - ----------------------------------------------------------------------------------------- - //
// My Steam Data Fetching Library //
require "fetch-steam.php";
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
{
	$group_name = "ludum";
	$curator_id = "537829";
/*
	if ( count($argv) < 2 ) {
		echo "\nUsage: " . $argv[0] . " group_name curator_id\n";
		echo "  group_name: name of the Steam group\n";
		echo "  curator_id: \n";
		echo "\nSample: php ". $argv[0] . " ludum 537829\n\n";
		exit(1);
	}
	$group_name = trim($argv[1]);
	$curator_id = trim($argv[2]);
*/

	// * * * //

	// Fetch Steam Data //
	$steam_group = steam_group_get( $group_name );
	$steam_curator = steam_curator_get( $curator_id );
	
	if ( $steam_group === NULL ) {
		echo "Failed to fetch Steam Group data\n";
		exit(1);
	}
	if ( $steam_curator === NULL ) {
		echo "Failed to fetch Steam Curator data\n";
		exit(1);
	}
	
	// * * * //

	// Bail if no DB_NAME is set //
	if ( !defined('DB_NAME') ) {
		echo "WARNING: No DB_NAME is set. Assuming local.\n";
		echo "Steam Group ({$group_name}):\n";
		print_r( $steam_group );
		echo "Steam Curator ({$curator_id}):\n";
		print_r( $steam_curator );
		exit(0);
	}

	// Open Database //	
	$db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	
	if ( $db ) {
		$info_table = $table_prefix . "steam_info";
		// Check if Table exists, and Create //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '{$info_table}'")) === 0) {
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE {$info_table} (
					name VARCHAR(32) UNIQUE NOT NULL,
					value text NOT NULL
				);";
			
			// NOTE: name is NOT indexed, since this table will almost always be fully queried. //
			// NOTE: 'key' is a reserved word in SQL. Need to use backticks `key` to get it, but meh //
			//   http://stackoverflow.com/a/2889884 //
			
			if ( !mysqli_query($db,$query) ) {
				echo "Error Creating Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}

		// Function to simplify Key/Value setting //
		function SetInfo( $name, $value ) {
			global $db;
			global $info_table;
			
			$query = 
				"INSERT INTO {$info_table} (
						name,
						value
					)
					VALUES (
						\"{$name}\",
						\"{$value}\"
					)
					ON DUPLICATE KEY UPDATE 
						value=VALUES(value)
					";

			if ( !mysqli_query($db,$query) ) {
				echo "Error setting/updating {$name} in to Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		
		// Store Group Values //
		SetInfo( "group_members", $steam_group['memberCount'] );
		SetInfo( "group_members_in_game", $steam_group['membersInGame'] );
		SetInfo( "group_members_online", $steam_group['membersOnline'] );
		SetInfo( "group_avatar", $steam_group['avatarFull'] );
		
		// Store Curator Values //
		SetInfo( "curator_followers", $steam_curator['followers'] );
		SetInfo( "curator_avatar", $steam_curator['avatar'] );

		// * * * //

		$game_table = $table_prefix . "steam_games";
		// Check if Table exists, and Create //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '{$game_table}'")) === 0) {
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE {$game_table} (
					appid VARCHAR(32) UNIQUE NOT NULL,
					name text NOT NULL,
					released datetime NOT NULL,
					info text NOT NULL,
					url text NOT NULL,
					banner text NOT NULL
				);";

//					ratings int NOT NULL,
//					comments int NOT NULL
						
			if ( !mysqli_query($db,$query) ) {
				echo "Error Creating Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		
		foreach( $steam_curator['games'] as $game ) {
			$info = addslashes($game['info']);
			
			$query = 
				"INSERT INTO {$game_table} (
						appid,
						name,
						released,
						info,
						url,
						banner
					)
					VALUES (
						\"{$game['appid']}\",
						\"{$game['name']}\",
						\"{$game['released']}\",
						\"{$info}\",
						\"{$game['url']}\",
						\"{$game['banner']}\"
					)
					ON DUPLICATE KEY UPDATE 
						name=VALUES(name),
						released=VALUES(released),
						info=VALUES(info),
						url=VALUES(url),
						banner=VALUES(banner)
					";

//						ratings,
//						comments
//						{$game['rateup']},
//						{$game['comments']}
//						ratings=VALUES(ratings),
//						comments=VALUES(comments)

			if ( !mysqli_query($db,$query) ) {
				echo "Error setting/updating Game Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}

				
		$result = mysqli_query($db,"SELECT * FROM {$game_table}" );
		$data = Array();
		while ( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ) {	// I don't care about MYSQLI_BOTH
			$data[] = $row;
		}
		echo "Size: " . count($data) . "\n";
		print_r( $data );
//		
//		$ds = array();
//		
//		// Build Associative List //
////		{
////			$ds[] = 
////		}
//
		
		
//		while($oot = mysqli_fetch_array($ret)) {
//			print_r($oot);
//			echo "\n";
//		}
//		//print_r( $ret );
		
		mysqli_close($db);
	}
}

?>