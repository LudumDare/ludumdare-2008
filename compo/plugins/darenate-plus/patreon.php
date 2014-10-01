<?php
// - ----------------------------------------------------------------------------------------- - //
# Shim for PHP <= 5.4.0, from here: http://stackoverflow.com/a/12018482
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
// - ----------------------------------------------------------------------------------------- - //
# Shim for PHP < 5.3, from http://php.net/manual/en/function.str-getcsv.php#98088
if (!function_exists('str_getcsv')) { 
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') { 
        if (is_string($input) && !empty($input)) { 
            $output = array(); 
            $tmp    = preg_split("/".$eol."/",$input); 
            if (is_array($tmp) && !empty($tmp)) { 
                while (list($line_num, $line) = each($tmp)) { 
                    if (preg_match("/".$escape.$enclosure."/",$line)) { 
                        while ($strlen = strlen($line)) { 
                            $pos_delimiter       = strpos($line,$delimiter); 
                            $pos_enclosure_start = strpos($line,$enclosure); 
                            if ( 
                                is_int($pos_delimiter) && is_int($pos_enclosure_start) 
                                && ($pos_enclosure_start < $pos_delimiter) 
                                ) { 
                                $enclosed_str = substr($line,1); 
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure); 
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end); 
                                $output[$line_num][] = $enclosed_str; 
                                $offset = $pos_enclosure_end+3; 
                            } else { 
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) { 
                                    $output[$line_num][] = substr($line,0); 
                                    $offset = strlen($line); 
                                } else { 
                                    $output[$line_num][] = substr($line,0,$pos_delimiter); 
                                    $offset = ( 
                                                !empty($pos_enclosure_start) 
                                                && ($pos_enclosure_start < $pos_delimiter) 
                                                ) 
                                                ?$pos_enclosure_start 
                                                :$pos_delimiter+1; 
                                } 
                            } 
                            $line = substr($line,$offset); 
                        } 
                    } else { 
                        $line = preg_split("/".$delimiter."/",$line); 
    
                        /* 
                         * Validating against pesky extra line breaks creating false rows. 
                         */ 
                        if (is_array($line) && !empty($line[0])) { 
                            $output[$line_num] = $line; 
                        }  
                    } 
                } 
                return $output; 
            } else { 
                return false; 
            } 
        } else { 
            return false; 
        } 
    } 
}
# PHP Shim End #
// - ----------------------------------------------------------------------------------------- - //

// If standalone, we need a config //
#require "config.php";

// Otherwise, use Wordpress DB access //


// - ----------------------------------------------------------------------------------------- - //
// HTTP POST response function //
function rest_post($request) {
	$datafile = NULL;
	$data = NULL;
	
	if ( $_FILES['uploadedfile']['error'] == UPLOAD_ERR_OK ) {
		if ( is_uploaded_file($_FILES['uploadedfile']['tmp_name'])) {
			$datafile = file_get_contents($_FILES['uploadedfile']['tmp_name']);
			$data = str_getcsv($datafile);
		}
	}
	
	if ( $data ) {
		// 200 - OK (everything fine) //
		// 201 - Created (okay and I did something)
		// 202 - Accepted (okay but unprocessed)
		http_response_code(202); // 202 because we process below //
		
		// * * * //

		$keys = $data[0];	// First row contains key names //
		
		// Build a list of keys (in case the CSV changes) //
		$key_name = array_search('Name',$keys);
		$key_email = array_search('Email',$keys);
		$key_pledge = array_search('Pledge',$keys);
		$key_lifetime = array_search('Lifetime',$keys);
		$key_status = array_search('Status',$keys);
		$key_twitter = array_search('Twitter',$keys);
		$key_shipping = array_search('Shipping',$keys);
		$key_start = array_search('Start',$keys);
		$key_maxamount = array_search('MaxAmount',$keys);
		$key_complete = array_search('Complete',$keys);

		$data_size = count($data);
		for ($idx = 1; $idx < $data_size; $idx++ ) {
			if ( count($data[$idx]) > 3 ) {
				echo $idx . " - " . $data[$idx][$key_name] . " - " . $data[$idx][$key_pledge] . "\n";
			}
		}
		
		// * * * //
	
		echo "thanks bro\n";
		
		echo hash( "sha512", "coolstorybro" );
	}
	else {
		http_response_code(400);
	}
	
	// Cleanup //
	if ( $datafile ) {
		// delete temp file //
	}
}
// - ----------------------------------------------------------------------------------------- - //
// HTTP HEAD response //
function rest_head($request) {
	http_response_code(200);
}
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