<?php

// HTTP GET response //
function rest_get($request) {
	rest_head($request);
	
	// ... //
	
	echo "do something bro\n";
}
// - ----------------------------------------------------------------------------------------- - //
// HTTP ERROR response //
function rest_error($request) {
	http_response_code(400);
}
// - ----------------------------------------------------------------------------------------- - //


// - ----------------------------------------------------------------------------------------- - //
// START! //
// - ----------------------------------------------------------------------------------------- - //
// http://stackoverflow.com/a/897311
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

$rest_func = 'rest_'.strtolower($_SERVER['REQUEST_METHOD']);
if (function_exists($rest_func)) {
	// Call the appropriate HTTP response function //
	call_user_func($rest_func, $request);
}
// - ----------------------------------------------------------------------------------------- - //

?>