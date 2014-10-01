<?PHP


# Better response code PHP Shim, from here: http://stackoverflow.com/a/12018482
// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code')) {
	function http_response_code($newcode = NULL) {
		static $code = 200;
		if($newcode !== NULL) {
			header('X-PHP-Response-Code: '.$newcode, true, $newcode);
			if(!headers_sent())
			$code = $newcode;
		}       
		return $code;
	}
}
# PHP Shim End #


function rest_put($request) {
	
	
	// 200 - OK (everything fine) //
	// 201 - Created (okay and I did something)
	// 202 - Accepted (okay but unprocessed)
	http_response_code(201);
	echo 'thanks bro\n';
}

function rest_head($request) {
	http_response_code(200);
}
function rest_get($request) {
	rest_head($request);
	// ... //
	echo 'do something bro\n';
}

function rest_error($request) {
	http_response_code(400);
}


$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

$rest_func = 'rest_'.strtolower($_SERVER['REQUEST_METHOD']);
if (function_exists($rest_func)) {
	call_user_func($rest_func, $request);
}

?>