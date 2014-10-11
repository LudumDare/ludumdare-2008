<?php

// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	echo "Clever girl.\n";
	exit(1);
}


// Get Wordpress Setup Variables //
require "../../../wp-config.php";

require "fetch-streams.php";


// MAIN //
{
	if ( count($argv) < 3 ) {
		echo "\nUsage: " . $argv[0] . " Game+Name time_in_minutes\n";
		echo "  Game+Name: name of the Twitch game. i.e. Ludum+Dare or Diablo+III\n";
		echo "  time_in_minutes: time in minutes since the last call. i.e. 10 or 15\n";
		echo "\nSample: php ". $argv[0] . " Ludum+Dare 10\n\n";
		exit(1);
	}
	$game_name = $argv[1];
	$update_time = intval($argv[2]);
	if ( $update_time < 1 ) {
		echo "ERROR: Bad update time \"".$argv[2]."\"\n";
		exit(1);
	}
	
	// * * * //
	
	$twitch_streams = twitch_streams_get( $game_name );
	print_r( $twitch_streams );
	
	if ( $twitch_streams === NULL ) {
		echo "ERROR: Unable to get Twitch stream data.\n";
		exit(1);
	}
	
	// * * * //
	
	$hitbox_streams = hitbox_streams_get( $game_name );
	print_r( $hitbox_streams );
	
	if ( $hitbox_streams === NULL ) {
		echo "ERROR: Unable to get Hitbox stream data.\n";
		//exit(1);
	}
	
	// * * * //
	
	// TODO:
	// - Save Streamer Data (twitch_streams table)
	// - Save Current Twitch Stats (twitch_info table)
	
	
	// Open Database //	
	$db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	
	if ( $db ) {
		$streams_table_name = $table_prefix . "broadcast_streams";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$streams_table_name."'") ) == 0) {
			//echo "No Table!\n";
			
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE " . $streams_table_name . " (
					service_id TINYINT UNSIGNED NOT NULL,
					user_id BIGINT UNSIGNED NOT NULL,
					PRIMARY KEY ID (service_id,user_id),
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
						INDEX (timestamp),
					
					name VARCHAR(32) NOT NULL,
					display_name VARCHAR(32) NOT NULL,					
					site_id BIGINT UNSIGNED NOT NULL,
					followers BIGINT UNSIGNED NOT NULL,
					avatar TEXT NOT NULL,
					url TEXT NOT NULL,
					mature BOOLEAN NOT NULL,
					
					media_id BIGINT UNSIGNED NOT NULL,
					media_viewers BIGINT UNSIGNED NOT NULL,
					
					units BIGINT UNSIGNED NOT NULL
				);";

//					ID SERIAL PRIMARY KEY,

			// service_id: 1. Twitch, 2. Hitbox, 3. ???
			// name: slug version of name (Twitch has a 26 character limit as of 2014)
			// display_name: printed version of name.
			// user_id: the user/channel ID.
			// site_id: the local website ID of the user
			// followers: number of followers.
			// avatar: URL to an image.
			// url: URL to channel.
			// mature: channel contains mature content.
			
			// media_id: stream/media ID. Most services have a 2nd ID.
			// media_viewers: how many people are viewing the stream/media.

			// units: how many minutes the user has streamed our game.
						
			if ( mysqli_query($db,$query) ) {
				//echo "Table Created.\n";
			}
			else {
				echo "Error Creating Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		else {
			//echo "Got it\n";
		}

		foreach ( $twitch_streams['streams'] as $value ) {
			$query = 
				"INSERT INTO " . $streams_table_name . "
					(service_id,user_id, name)
					VALUES (" .
						1,
						intval($value['channel']['_id']),
						trim($value->channel->name)
					")
					ON DUPLICATE KEY UPDATE 
					name=VALUES(name)";
			
			if ( mysqli_query($db,$query) ) {
			}
			else {
				echo "Error Inserting in to Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
						
//		INSERT INTO table (Value, UserID, VoteID)
//		VALUES (100, 600, 78)
//		ON DUPLICATE KEY UPDATE Value = 100
		
		

		$activity_table_name = $table_prefix . "broadcast_activity";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$activity_table_name."'") ) == 0) {
			// Does not exist //
			$query = 
				"CREATE TABLE " . $activity_table_name . " (
					timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP PRIMARY KEY,
					
					service_id TINYINT UNSIGNED NOT NULL,
					streams BIGINT UNSIGNED NOT NULL,
					viewers BIGINT UNSIGNED NOT NULL
				);";
			
			// service_id: 1. Twitch, 2. Hitbox, 3. ??? (Azubu, MLG, YouTube)
			// streams: Total Streams
			// viewers: Total Viewers of all Streams
			
			// TODO (Maybe): Index Stream and Viewers tables, to speed up finding records for those stats.
						
			if ( mysqli_query($db,$query) ) {
				// Table Created //
			}
			else {
				echo "Error Creating Activity Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		else {
			// Table Exists //
		}

		
		
		
		// Stream Preview Images (defaults: 80x50, 320x200, 640x400)
		// http://static-cdn.jtvnw.net/previews-ttv/live_user_{name}-{width}x{height}.jpg
		
		// Twitch URL
		// http://www.twitch.tv/{name}		
		
		
		// Hitbox //
		// http://edge.hitbox.tv/static/img/channel/povrazor_54389b72a3583_large.png
		
		
		// Do Stuff //

		mysqli_close($db);
	}
}

?>