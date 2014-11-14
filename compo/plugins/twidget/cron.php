#!/bin
<?php
// - ----------------------------------------------------------------------------------------- - //
// Store the current directory part of the requested URL (for building paths to files) //
@$http_dir = dirname($_SERVER["REQUEST_URI"]);
chdir(dirname(__FILE__));	// Change Working Directory to where I am (for my local paths) //
// - ----------------------------------------------------------------------------------------- - //
// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	echo "Clever girl (".php_sapi_name().")\n";
	exit(1);
}
// - ----------------------------------------------------------------------------------------- - //
// Get Wordpress Setup Variables (optional... sort of) //
@include "../../../wp-config.php";
// - ----------------------------------------------------------------------------------------- - //
// My Stream Fetching Library //
require "fetch-streams.php";
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
// MAIN //
{
	if ( count($argv) < 3 ) {
		echo "\nUsage: " . $argv[0] . " Game+Name time_in_minutes [YouTubeKey] [TwitchKey] [Alt+Game+Name]\n";
		echo "  Game+Name: name of the Twitch/Hitbox game, and YouTube Search Q\n";
		echo "  time_in_minutes: time in minutes since the last call. i.e. 10 or 15\n";
		echo "  YouTubeKey: API key for the Youtube APIs\n";
		echo "  TwitchKey: API key for the Twitch API\n";
		echo "  Alt+Game+Name: Another game to collect data on\n";
		echo "\nSample: php ". $argv[0] . " Ludum+Dare 10\n\n";
		exit(1);
	}
	$game_name = $argv[1];
	$update_time = intval($argv[2]);
	if ( $update_time < 1 ) {
		echo "WARNING: Update time \"".$argv[2]."\"\n";
	}
	$youtube_key = NULL;
	if ( count($argv) > 3 ) {
		$youtube_key = $argv[3];
	}
	$twitch_key = NULL;
	if ( count($argv) > 4 ) {
		$twitch_key = $argv[4];
	}
	$alt_game_name = NULL;
	if ( count($argv) > 5 ) {
		$alt_game_name = $argv[5];
	}
	
	// * * * //
	
	$twitch_streams = twitch_streams_get( $game_name, $twitch_key );
	
	$hitbox_streams = hitbox_streams_get( $game_name );

	$youtube_streams = youtube_streams_get( $game_name, $youtube_key );

	$alt_twitch_streams = twitch_streams_get( $alt_game_name, $twitch_key );
	
	echo "Alt: " . $alt_game_name . "\n";
	echo "Argv: " . $argv . "\n";
	
	// * * * //
	
	// Special case: if update_time is ever 0, then don't do any database work //
	if ( $update_time === 0 ) {
		echo "Twitch:\n";
		print_r( $twitch_streams );
		echo "Hitbox:\n";
		print_r( $hitbox_streams );
		echo "YouTube:\n";
		print_r( $youtube_streams );
		exit(0);
	}
		
	// * * * //
	
	// Bail if no DB_NAME is set //
	if ( !defined('DB_NAME') ) {
		echo "WARNING: No DB_NAME is set. Are you running local?\n";
		exit(0);
	}
	
	// Open Database //	
	$db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	mysqli_set_charset($db,"utf8");
	
	if ( $db ) {
		$streams_table_name = $table_prefix . "broadcast_streams";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$streams_table_name."'") ) == 0) {
			//echo "No Table!\n";
			
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE " . $streams_table_name . " (
					service_id TINYINT UNSIGNED NOT NULL,
					user_id VARCHAR(32) NOT NULL,
					PRIMARY KEY ID (service_id,user_id),
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
						INDEX (timestamp),
					
					name VARCHAR(32) NOT NULL,
					display_name VARCHAR(32) NOT NULL,					
					site_id BIGINT UNSIGNED NOT NULL,
					media_id VARCHAR(32) NOT NULL,
					followers BIGINT UNSIGNED NOT NULL,
					viewers BIGINT UNSIGNED NOT NULL,
					avatar TEXT NOT NULL,
					url TEXT NOT NULL,
					embed_url TEXT NOT NULL,
					status TEXT NOT NULL,
					mature BOOLEAN NOT NULL,
										
					units BIGINT UNSIGNED NOT NULL
				);";

//					ID SERIAL PRIMARY KEY,

			// service_id: 1. Twitch, 2. Hitbox, 3. ???
			// user_id: the user/channel ID.

			// name: slug version of name (Twitch has a 26 character limit as of 2014)
			// display_name: printed version of name.
			// site_id: the local website ID of the user
			// media_id: stream/media ID. Most services have a 2nd ID.
			// followers: number of followers.
			// viewers: how many people are viewing the stream/media.
			// avatar: URL to an image.
			// url: URL to channel.
			// mature: channel contains mature content.
			
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


		// Update Twitch Streams //
		if ( $twitch_streams !== NULL ) {
			foreach ( $twitch_streams['streams'] as $value ) {
				$service_id = 1;	// Twitch.tv //
				$channel_id = intval($value['channel']['_id']);
				$channel_name = mysqli_real_escape_string($db,trim($value['channel']['name']));
				$channel_display_name = mysqli_real_escape_string($db,trim($value['channel']['display_name']));
				$media_id = intval($value['_id']);
				$channel_followers = intval($value['channel']['followers']);
				$media_viewers = intval($value['viewers']);
				$channel_avatar = mysqli_real_escape_string($db,trim($value['channel']['logo']));
				$channel_url = mysqli_real_escape_string($db,trim($value['channel']['url']));
				$channel_embed_url = mysqli_real_escape_string($db,"http://www.twitch.tv/{$channel_name}/embed");
				$channel_status = mysqli_real_escape_string($db,trim($value['channel']['status']));
				$channel_mature = intval($value['channel']['mature']);
				
				// http://stackoverflow.com/questions/7825739/epoch-time-and-mysql-query
				// http://stackoverflow.com/a/1677388 - strtotime understands TZ formatted dates
				// http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_utc-timestamp
				// http://stackoverflow.com/questions/5331026/is-it-possible-to-create-a-column-with-a-unix-timestamp-default-in-mysql
				//$some_date = strtotime(
				
				$units = $update_time;
				
				$query = 
					"INSERT INTO " . $streams_table_name . " (
							service_id,
							user_id,
							
							name,
							display_name,
							media_id,
							followers,
							viewers,
							avatar,
							url,
							embed_url,
							status,
							mature,
							
							units
						)
						VALUES (
							{$service_id},
							\"{$channel_id}\",
							\"{$channel_name}\",
							\"{$channel_display_name}\",
							\"{$media_id}\",
							{$channel_followers},
							{$media_viewers},
							\"{$channel_avatar}\",
							\"{$channel_url}\",
							\"{$channel_embed_url}\",
							\"{$channel_status}\",
							{$channel_mature},
							{$units}
						)
						ON DUPLICATE KEY UPDATE 
							name=VALUES(name),
							display_name=VALUES(display_name),
							media_id=VALUES(media_id),
							followers=VALUES(followers),
							viewers=VALUES(viewers),
							avatar=VALUES(avatar),
							url=VALUES(url),
							embed_url=VALUES(embed_url),
							status=VALUES(status),
							mature=VALUES(mature),
							units=units+VALUES(units)
						";
				
				if ( mysqli_query($db,$query) ) {
				}
				else {
					echo "Error Inserting Twitch in to Streams Table:\n". mysqli_error($db) ."\n";
					exit(1);
				}
			}
		}


		// Update Hitbox Streams //
		if ( $hitbox_streams !== NULL ) {
			foreach ( $hitbox_streams['livestream'] as $value ) {
				$service_id = 2;	// Hitbox.tv //
				$channel_id = intval($value['channel']['user_id']);
				$channel_name = mysqli_real_escape_string($db,trim($value['media_name']));
				$channel_display_name = mysqli_real_escape_string($db,trim($value['media_user_name']));
				$media_id = intval($value['media_id']);
				$channel_followers = intval($value['channel']['followers']);
				$media_viewers = intval($value['media_views']);
				$channel_avatar = mysqli_real_escape_string($db,'http://edge.hitbox.tv' . trim($value['channel']['user_logo']));
				$channel_url = mysqli_real_escape_string($db,trim($value['channel']['channel_link']));
				$channel_embed_url = mysqli_real_escape_string($db,"http://hitbox.tv/#!/embed/{$channel_name}");
				$channel_status = mysqli_real_escape_string($db,trim($value['media_status']));
				$channel_mature = 0;
				
				$units = $update_time;
				
				$query = 
					"INSERT INTO " . $streams_table_name . " (
							service_id,
							user_id,
							
							name,
							display_name,
							media_id,
							followers,
							viewers,
							avatar,
							url,
							embed_url,
							status,
							mature,
							
							units
						)
						VALUES (
							{$service_id},
							\"{$channel_id}\",
							\"{$channel_name}\",
							\"{$channel_display_name}\",
							\"{$media_id}\",
							{$channel_followers},
							{$media_viewers},
							\"{$channel_avatar}\",
							\"{$channel_url}\",
							\"{$channel_embed_url}\",
							\"{$channel_status}\",
							{$channel_mature},
							{$units}
						)
						ON DUPLICATE KEY UPDATE 
							name=VALUES(name),
							display_name=VALUES(display_name),
							media_id=VALUES(media_id),
							followers=VALUES(followers),
							viewers=VALUES(viewers),
							avatar=VALUES(avatar),
							url=VALUES(url),
							embed_url=VALUES(embed_url),
							status=VALUES(status),
							mature=VALUES(mature),
							units=units+VALUES(units)
						";
	
				if ( mysqli_query($db,$query) ) {
				}
				else {
					echo "Error Inserting Hitbox in to Streams Table:\n". mysqli_error($db) ."\n";
					exit(1);
				}	
			}
		}


		// Update YouTube Streams //
		if ( $youtube_streams !== NULL ) {
			foreach ( $youtube_streams['items'] as $value ) {
				$service_id = 3;	// YouTube //
				$channel_id = trim($value['snippet']['channelId']);
				$channel_name = mysqli_real_escape_string($db,trim($value['snippet']['channelTitle']));
				$channel_display_name = mysqli_real_escape_string($db,trim($value['channel']['snippet']['title']));
				$media_id = trim($value['id']['videoId']);
				$channel_followers = intval($value['channel']['statistics']['subscriberCount']);
				$media_viewers = intval($value['liveStreamingDetails']['concurrentViewers']);
				$channel_avatar = mysqli_real_escape_string($db,trim($value['channel']['snippet']['thumbnails']['high']['url']));
				$channel_url = mysqli_real_escape_string($db,"http://youtube.com/" . $channel_name);
				//www.youtube.com/embed/SNzlfSIBb8k?rel=0
				$channel_embed_url = mysqli_real_escape_string($db,"//www.youtube.com/embed/{$media_id}?rel=0");
				$channel_status = mysqli_real_escape_string($db,trim($value['snippet']['title']));
				$channel_mature = 0;
				// https://developers.google.com/youtube/v3/docs/videos#contentDetails.contentRating.ytRating
				if ( array_key_exists("contentRating", $value['contentDetails']) ) {
					if ( array_key_exists("ytRating", $value['contentDetails']['contentRating']) ) {
						$channel_mature = ($value['contentDetails']['contentRating']['ytRating'] === 'ytAgeRestricted');
					}
				}
				
				$units = $update_time;
				
				$query = 
					"INSERT INTO " . $streams_table_name . " (
							service_id,
							user_id,
							
							name,
							display_name,
							media_id,
							followers,
							viewers,
							avatar,
							url,
							embed_url,
							status,
							mature,
							
							units
						)
						VALUES (
							{$service_id},
							\"{$channel_id}\",
							\"{$channel_name}\",
							\"{$channel_display_name}\",
							\"{$media_id}\",
							{$channel_followers},
							{$media_viewers},
							\"{$channel_avatar}\",
							\"{$channel_url}\",
							\"{$channel_embed_url}\",
							\"{$channel_status}\",
							{$channel_mature},
							{$units}
						)
						ON DUPLICATE KEY UPDATE 
							name=VALUES(name),
							display_name=VALUES(display_name),
							media_id=VALUES(media_id),
							followers=VALUES(followers),
							viewers=VALUES(viewers),
							avatar=VALUES(avatar),
							url=VALUES(url),
							embed_url=VALUES(embed_url),
							status=VALUES(status),
							mature=VALUES(mature),
							units=units+VALUES(units)
						";
	
				if ( mysqli_query($db,$query) ) {
				}
				else {
					echo "Error Inserting YouTube in to Streams Table:\n". mysqli_error($db) ."\n";
					exit(1);
				}	
			}
		}


		// Update Alt Twitch Streams //
		if ( $alt_twitch_streams !== NULL ) {
			foreach ( $alt_twitch_streams['streams'] as $value ) {
				$service_id = 4;	// Twitch.tv GameDev //
				$channel_id = intval($value['channel']['_id']);
				$channel_name = mysqli_real_escape_string($db,trim($value['channel']['name']));
				$channel_display_name = mysqli_real_escape_string($db,trim($value['channel']['display_name']));
				$media_id = intval($value['_id']);
				$channel_followers = intval($value['channel']['followers']);
				$media_viewers = intval($value['viewers']);
				$channel_avatar = mysqli_real_escape_string($db,trim($value['channel']['logo']));
				$channel_url = mysqli_real_escape_string($db,trim($value['channel']['url']));
				$channel_embed_url = mysqli_real_escape_string($db,"http://www.twitch.tv/{$channel_name}/embed");
				$channel_status = mysqli_real_escape_string($db,trim($value['channel']['status']));
				$channel_mature = intval($value['channel']['mature']);
				
				// http://stackoverflow.com/questions/7825739/epoch-time-and-mysql-query
				// http://stackoverflow.com/a/1677388 - strtotime understands TZ formatted dates
				// http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_utc-timestamp
				// http://stackoverflow.com/questions/5331026/is-it-possible-to-create-a-column-with-a-unix-timestamp-default-in-mysql
				//$some_date = strtotime(
				
				$units = $update_time;
				
				$query = 
					"INSERT INTO " . $streams_table_name . " (
							service_id,
							user_id,
							
							name,
							display_name,
							media_id,
							followers,
							viewers,
							avatar,
							url,
							embed_url,
							status,
							mature,
							
							units
						)
						VALUES (
							{$service_id},
							\"{$channel_id}\",
							\"{$channel_name}\",
							\"{$channel_display_name}\",
							\"{$media_id}\",
							{$channel_followers},
							{$media_viewers},
							\"{$channel_avatar}\",
							\"{$channel_url}\",
							\"{$channel_embed_url}\",
							\"{$channel_status}\",
							{$channel_mature},
							{$units}
						)
						ON DUPLICATE KEY UPDATE 
							name=VALUES(name),
							display_name=VALUES(display_name),
							media_id=VALUES(media_id),
							followers=VALUES(followers),
							viewers=VALUES(viewers),
							avatar=VALUES(avatar),
							url=VALUES(url),
							embed_url=VALUES(embed_url),
							status=VALUES(status),
							mature=VALUES(mature),
							units=units+VALUES(units)
						";
				
				if ( mysqli_query($db,$query) ) {
				}
				else {
					echo "Error Inserting Twitch in to Streams Table:\n". mysqli_error($db) ."\n";
					exit(1);
				}
			}
		}


		// * * * * * * * * * * //		


		$activity_table_name = $table_prefix . "broadcast_activity";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$activity_table_name."'") ) == 0) {
			// Does not exist //
			$query = 
				"CREATE TABLE " . $activity_table_name . " (
					timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,					
					service_id TINYINT UNSIGNED NOT NULL,
					PRIMARY KEY ID (service_id,timestamp),

					streams INT UNSIGNED NOT NULL,
					viewers INT UNSIGNED NOT NULL
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



		// Store Twitch Snapshot //
		if ( $twitch_streams !== NULL ) {
			$service_id = 1;	// Twitch.tv //
			$streams = intval($twitch_streams['_total']);
			$viewers = 0;

			foreach ( $twitch_streams['streams'] as $value ) {
				$viewers += intval($value['viewers']);
			}
			
			$query = 
				"INSERT INTO " . $activity_table_name . " (
						service_id,

						streams,
						viewers
					)
					VALUES (
						{$service_id},

						{$streams},
						{$viewers}
					)
					ON DUPLICATE KEY UPDATE 
						streams=VALUES(streams),
						viewers=VALUES(viewers)
					";

			if ( mysqli_query($db,$query) ) {
			}
			else {
				echo "Error Inserting Twitch in to Activity Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}

		// Store Hitbox Snapshot //
		if ( $hitbox_streams !== NULL ) {
			$service_id = 2;	// Hitbox.tv //
			$streams = count($hitbox_streams['livestream']);
			$viewers = 0;

			foreach ( $hitbox_streams['livestream'] as $value ) {
				$viewers += intval($value['media_views']);
			}
			
			$query = 
				"INSERT INTO " . $activity_table_name . " (
						service_id,

						streams,
						viewers
					)
					VALUES (
						{$service_id},

						{$streams},
						{$viewers}
					)
					ON DUPLICATE KEY UPDATE 
						streams=VALUES(streams),
						viewers=VALUES(viewers)
					";

			if ( mysqli_query($db,$query) ) {
			}
			else {
				echo "Error Inserting Hitbox in to Activity Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}	
		}
		
		
		
		// Stream Preview Images (defaults: 80x50, 320x200, 640x400)
		// http://static-cdn.jtvnw.net/previews-ttv/live_user_{name}-{width}x{height}.jpg
		
		// Twitch URL
		// http://www.twitch.tv/{name}		
		
		
		// Hitbox //
		// http://edge.hitbox.tv/static/img/channel/povrazor_54389b72a3583_large.png
		
		
		// Do Stuff //
		
		// http://dev.mysql.com/doc/refman/5.5/en/multiple-column-indexes.html

		mysqli_close($db);
	}
}

?>