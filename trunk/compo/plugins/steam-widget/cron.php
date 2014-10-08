#!/usr/bin/php
<?php

// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	// Jurassic Park //
	echo "Clever girl.\n";
	echo "<br /><br /><img src='http://img1.wikia.nocookie.net/__cb20140408111011/jurassicpark/images/5/53/Raptor_-_Clever_Girl.gif' />";
//	echo "<br /><br /><img src='/compo/wp-content/plugins/steam-widget/hacking.gif' />";
	exit(1);
}

// My Steam Data Fetching Library //
require "fetch-steam.php";

// Get Wordpress Setup Variables //
require "../../../wp-config.php";

{
	
	$steam_group = steam_group_get( "ludum" );
	$steam_curator = steam_curator_get( "537829" );
	
	print_r( $steam_group );
	print_r( $steam_curator );
	
	$db = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	
	if ( $db ) {
		$table_name = $table_prefix . "steam_info";
		// Check if Table exists //
		if( mysqli_num_rows(mysqli_query($db,"SHOW TABLES LIKE '".$table_name."'") ) == 0) {
			echo "Nope!\n";
			
			// Does not exist, so create it //
			$query = 
				"CREATE TABLE " . $table_name . " (
					ID bigint NOT NULL AUTO_INCREMENT,
					PRIMARY KEY (ID),
					
					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						ON UPDATE CURRENT_TIMESTAMP,
					
					name VARCHAR(64) NOT NULL,
					value text NOT NULL
				);";
			
			// NOTE: name is NOT indexed, since this table will almost always be fully queried. //
			// NOTE: 'key' is a reserved word in SQL. Need to use backticks `key` to get it, but meh //
			//   http://stackoverflow.com/a/2889884 //
			
			if ( mysqli_query($db,$query) ) {
				echo "Table Created.\n";
			}
			else {
				echo "Error Creating Table:\n". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		else {
			echo "Got it\n";
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