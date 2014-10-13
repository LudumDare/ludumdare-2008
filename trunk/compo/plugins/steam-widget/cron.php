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
		$table_name = $table_prefix . "steam_info";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$table_name."'") ) == 0) {
			//echo "No Table!\n";
			
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE " . $table_name . " (
					ID SERIAL PRIMARY KEY,
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
					
					name VARCHAR(32) NOT NULL,
					value text NOT NULL
				);";
			
			// NOTE: name is NOT indexed, since this table will almost always be fully queried. //
			// NOTE: 'key' is a reserved word in SQL. Need to use backticks `key` to get it, but meh //
			//   http://stackoverflow.com/a/2889884 //
			
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
				
		$ret = mysqli_query($db,"SELECT * FROM " . $table_name );
		$data = mysqli_fetch_array($ret);
		echo "Size: " . count($data) . "\n";
		print_r( $data );
		
		$ds = array();
		
		// Build Associative List //
//		{
//			$ds[] = 
//		}

		
		
//		while($oot = mysqli_fetch_array($ret)) {
//			print_r($oot);
//			echo "\n";
//		}
//		//print_r( $ret );
		
		mysqli_close($db);
	}
}

?>