<?php

// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	echo "Clever girl.\n";
	exit(1);
}


// Get Wordpress Setup Variables //
require "../../../wp-config.php";


// Float Sleep //
function fsleep( $val ) {
	usleep( $val * 1000000.0 );
}
// Random Sleep //
function rsleep( $val, $pre = 0.1 ) {
	usleep( ($pre * 1.0) + (((rand()*1.0)/(getrandmax()*1.0)) * ($val*1.0)) );
}


// Scan through $headers for a $header. Returns the value, or NULL. //
function http_find_header($headers,$header) {
	foreach ( $headers as $key => $r) {
		if (stripos($r, $header) !== FALSE) {
			$var = explode(":", $r);
			return trim($var[1]);
		}
	}
	return NULL;
}


// TODO: Send Client-ID (to make sure Twitch doesn't rate limit us) //
function twitch_streams_get( $game_name ) {
	$limit = 50;				// Number of streams we request per query (Max 100) //
	$max_loops = 100;			// Maximum number of loops before this code fails. //

	$loops = 0;
	$offset = 0;
	$ret_data = NULL;
	do {
		$retry = 4;
		$json_data = NULL;
		do {
			$retry--;
			// Assuming it's not the first loop, delay for 0.5-1.5 seconds, to play nice //
			if ( $loops === 0 ) {
				rsleep(1.0,0.5);
			}
			// NOTE: If we ever reach 5,000 streamers, this error will trigger. //
			else if ( $loops === $max_loops ) {
				echo "ERROR: Safe Twitch stream request limit exceeded. Are there nearly ".($limit*$loops)." streams? If so, you need to up the limit.\n";
				return NULL;
			}
			$loops++;
	
			// API only supports 100 streams per request //
			$api_url = "https://api.twitch.tv/kraken/streams/?game=" . $game_name . "&limit=" . $limit . "&offset=" . $offset;
			$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

			// If we didn't get a correct response, then don't attempt to json decode. //
			if ( $api_response === FALSE ) {
				continue;
			}
			
			// Decode the Data //
			$json_data = json_decode($api_response, true);		
		} while ( ($json_data === NULL) && ($retry !== 0) );

		if ( $json_data === NULL ) {
			// If we can't get a complete set of Twitch streams, assume Twitch is down. //
			echo "ERROR: Unable to retieve streams via Twitch API (".$loops.")\n";
			return NULL;
		}
		
		// If we currently have no data //
		if ( $ret_data === NULL ) {
			{
				// Confirm that the response matches our expected API version. Stored in the response header. //
				$expected_api_version = 3;
				$api_version = intval(http_find_header($http_response_header,'API-Version'));

				if ( $api_version !== $expected_api_version ) {
					echo "WARNING: Twitch API version mismatch. Expected: " . $expected_api_version . " Got: " . $api_version . "\n";
				}
			}
			
			// Copy all Data //
			$ret_data = $json_data;
			// Keep a count of the number of requests that were needed to complete this query. //
			$ret_data['requests'] = 1;
		}
		// If we have some data //
		else {
			// Update the total //
			$ret_data['_total'] = $json_data['_total'];
			// Append our list of streams to the returns list //
			$ret_data['streams'] = array_merge( $ret_data['streams'], $json_data['streams'] );
			// One more request was made //			
			$ret_data['requests']++;
		}
		
		// Always increment our offset
		$offset += $limit;
	} while ( $ret_data['_total'] > count($ret_data['streams']) );

	return $ret_data;
}


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
	
	$streams = twitch_streams_get( $game_name );
	print_r( $streams );
	
	if ( $streams === NULL ) {
		echo "ERROR: Unable to get Twitch stream data.\n";
		exit(1);
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
					ID bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
						INDEX (timestamp),
					
					service_id SHORT NOT NULL,
					name VARCHAR(32) NOT NULL,
					display_name VARCHAR(32) NOT NULL,
					user_id BIGINT NOT NULL,
					followers BIGINT NOT NULL,
					avatar TEXT NOT NULL,
					url TEXT NOT NULL,
					mature BOOLEAN NOT NULL,
					
					media_id BIGINT NOT NULL,
					media_viewers BIGINT NOT NULL,
					
					units BIGINT NOT NULL,
				);";

			// service_id: 1. Twitch, 2. Hitbox, 3. ???
			// name: slug version of name (Twitch has a 26 character limit as of 2014)
			// display_name: printed version of name.
			// user_id: the user/channel ID.
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
		

		$activity_table_name = $table_prefix . "broadcast_activity";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$activity_table_name."'") ) == 0) {
			// Does not exist //
			$query = 
				"CREATE TABLE " . $activity_table_name . " (
					timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP PRIMARY KEY,
					
					service_id SHORT NOT NULL,
					streams BIGINT NOT NULL,
					viewers BIGINT NOT NULL
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
		
		
		
		// Do Stuff //

		mysqli_close($db);
	}
}

?>