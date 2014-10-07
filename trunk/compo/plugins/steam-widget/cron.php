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

// Get Wordpress Setup Variables //
require "../../../wp-config.php";

{
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
					
					
					key VARCHAR(64) NOT NULL,
					value text NOT NULL
				);";
			
			// NOTE: key is NOT indexed, since this table will almost always be fully queried. //
//					timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
//						ON UPDATE CURRENT_TIMESTAMP,
			
			if ( mysqli_query($db,$query) ) {
				echo "Table Created.\n";
			}
			else {
				echo "Error Creating Table: ". mysqli_error($db) ."\n";
				exit(1);
			}
		}
		else {
			echo "Got it\n";
		}
		
		$ret = mysqli_query($db,"SELECT * FROM " . $table_name );
		print_r( mysqli_fetch_array($ret) );
		
//		while($oot = mysqli_fetch_array($ret)) {
//			print_r($oot);
//			echo "\n";
//		}
//		//print_r( $ret );
		
		mysqli_close($db);
	}
}

?>