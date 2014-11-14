<?php

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
function twitch_streams_get( $game_name, $api_key ) {
	if ( $game_name === NULL )
		return NULL;
	
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


function hitbox_streams_get( $game_name ) {
	$api_url = "http://api.hitbox.tv/media?game=" . $game_name;
	$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

	// Sadly, Hitbox 404's on no livestreams, rather than confirming a transaction. //
	if ( $api_response === FALSE ) {
		return Array( 'livestream' => Array() );
	}
	
	// Decode the Data //
	$json_data = json_decode($api_response, true);
	
	return $json_data;
}


function youtube_streams_get( $game_name, $api_key ) {
	$limit = 50;		// Number of streams we request per query (Max 50) //
	
	// I can't do my job without an API key //
	if ( $api_key === NULL )
		return NULL;
	
	// 1. Search Query //
	$api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&order=date&eventType=live&type=video" .
		"&maxResults=" . $limit . 
		"&q=" . $game_name . 
		"&key=" . $api_key;
	$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

	if ( $api_response === FALSE ) {
		echo "ERROR: Unable to retieve streams via YouTube API\n";
		return NULL;
	}
	
	// Decode the Data //
	$ret_data = json_decode($api_response, true);

	// Bail if there are no streams //
	if ( count($ret_data['items']) === 0 )
		return $ret_data;

	// * * * //
	
	$video_ids = Array();
	$channel_ids = Array();
	foreach ( $ret_data['items'] as $item ) {
		$video_ids[] = $item['id']['videoId'];	// ZWp-n7fNA2c //
		$channel_ids[] = $item['snippet']['channelId']; // UCpS5yDJb_8b4rZX8poIAaCw //
	}

	// * * * //
	
	// 2. Video Query //
	
	$api_url = "https://www.googleapis.com/youtube/v3/videos?part=contentDetails%2Cstatistics%2CliveStreamingDetails" .
		"&maxResults=" . $limit . 
		"&id=" . implode(",",$video_ids) . 
		"&key=" . $api_key;
	$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

	if ( $api_response === FALSE ) {
		echo "ERROR: Unable to retieve videos by id via YouTube API\n";
		return NULL;
	}
	
	// Decode the Data //
	$json_data = json_decode($api_response, true);
	
	// * * * //
	
	$video_count = count( $json_data['items'] );
	for ( $idx = 0; $idx < $video_count; $idx++ ) {
		$ret_data['items'][$idx]['contentDetails'] = $json_data['items'][$idx]['contentDetails'];
		$ret_data['items'][$idx]['statistics'] = $json_data['items'][$idx]['statistics'];
		$ret_data['items'][$idx]['liveStreamingDetails'] = $json_data['items'][$idx]['liveStreamingDetails'];
	}
	
	// * * * //

	// 3. Channel Query //
	
	$api_url = "https://www.googleapis.com/youtube/v3/channels?part=snippet%2Cstatistics" .
		"&maxResults=" . $limit . 
		"&id=" . implode(",",$channel_ids) . 
		"&key=" . $api_key;
	$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

	if ( $api_response === FALSE ) {
		echo "ERROR: Unable to retieve channels by id via YouTube API\n";
		return NULL;
	}
	
	// Decode the Data //
	$json_data = json_decode($api_response, true);
	
	// * * * //
	
	$channel_count = count( $json_data['items'] );
	for ( $idx = 0; $idx < $channel_count; $idx++ ) {
		$ret_data['items'][$idx]['channel'] = $json_data['items'][$idx];
	}
	
	// * * * //

	return $ret_data;
}

?>
