<?php

$url = "http://steamcommunity.com/groups/ludum/memberslistxml/?xml=1";
$xml = simplexml_load_file($url);
print_r($xml);

?>