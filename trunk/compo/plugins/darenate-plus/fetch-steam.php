<?php

echo "XML Grab\n";
{
	$url = "http://steamcommunity.com/groups/ludum/memberslistxml/?xml=1";
	$xml = simplexml_load_file($url);
	print_r($xml);
}

require "simple_html_dom.php"

echo "HTML Grab\n";
{
	$html = file_get_html( "http://store.steampowered.com/curator/537829/" );
	
	print_r( $html->find('div.num_followers') );
}

//echo "HTTP Grab\n";
//{
//	$response = http_get( "http://store.steampowered.com/curator/537829/", array("timeout"=>2), $info );
//	
//	preg_match('/<div >(.*?)<\/div>/s', $response, $matches);
//	
//	print_r($matches);
//}

?>