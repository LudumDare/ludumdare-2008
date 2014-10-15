<?php
// - ----------------------------------------------------------------------------------------- - //
// Store the current directory part of the requested URL (for building paths to files) //
@$http_dir = dirname($_SERVER["REQUEST_URI"]);
chdir(dirname(__FILE__));	// Change Working Directory to where I am (for my local paths) //
// - ----------------------------------------------------------------------------------------- - //

{
	https://cdn.syndication.twimg.com/widgets/timelines/408705501921173504

	$api_url = "https://cdn.syndication.twimg.com/widgets/timelines/408705501921173504";
	$api_response = @file_get_contents($api_url); // @ surpresses PHP error: http://stackoverflow.com/a/15685966

	// If we didn't get a correct response, then don't attempt to json decode. //
	if ( $api_response === FALSE ) {
		echo "Error\n";
		exit(1);
	}
	
	// Decode the Data //
	$json_data = json_decode($api_response, true);
	
	echo $json_data['body'];

}

?>
