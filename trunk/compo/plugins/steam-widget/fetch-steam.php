<?php

// http://upskill.co.in/content/how-convert-simplexml-object-array-php
function __xml2array($xml) {
	$arr = array();

	foreach ($xml->children() as $r) {
		$t = array();

		if (count($r->children()) == 0) {
			$arr[$r->getName()] = strval($r);
		}
		else {
			$arr[$r->getName()][] = __xml2array($r);
		} 
	}
	return $arr;
}


// Using OLD (depricated) Steam Groups API (no replacement yet) //
function steam_group_get( $group_id ) {
	$url = "http://steamcommunity.com/groups/" . $group_id . "/memberslistxml/?xml=1";
	$xml = simplexml_load_file($url);
	$arr = __xml2array($xml);
	
	$group = &$arr['groupDetails'][0];
			
	$ret = array();
	$ret['member_count'] = $group['memberCount'];
	$ret['members_in_game'] = $group['membersInGame'];
	$ret['members_online'] = $group['membersOnline'];
	
	return $ret;
}


// http://simplehtmldom.sourceforge.net/manual.htm
require "simple_html_dom.php";


// NO API, so an HTTP Get //
function steam_curator_get( $curator_id ) {
	$html = file_get_html( "http://store.steampowered.com/curator/". $curator_id ."/" );
	
	$ret = array();
	$ret['followers'] = $html->find('.num_followers', 0)->plaintext;
	$ret['avatar'] = $html->find('.curator_avatar', 0)->src;
	
	$ret['games'] = array();
	foreach( $html->find('.recommendation') as $element ) {
		$ret['games'][] = $element->data-ds-appid;
	}
	
	return $ret;
}

?>