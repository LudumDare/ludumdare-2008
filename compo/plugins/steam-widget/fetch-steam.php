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
	// Get the main HTML file for Avatar and Num Followers //
	// http://store.steampowered.com/curator/537829/
	$main_url = "http://store.steampowered.com/curator/". $curator_id ."/";
	$main_html = file_get_html( $main_url );
	
	$ret = array();
	$ret['followers'] = $main_html->find('.num_followers', 0)->plaintext;
	$ret['avatar'] = $main_html->find('.curator_avatar', 0)->src;

	// Use AJAX
	// http://store.steampowered.com/curators/ajaxgetcuratorrecommendations/537829//?query=&start=0&count=20
	$game_url = "http://store.steampowered.com/curators/ajaxgetcuratorrecommendations/". $curator_id ."//?query=&start=0&count=20";
	$game_json = json_decode(file_get_contents($game_url));
	print_r( $game_json );

	//$game_html = file_get_html( "http://store.steampowered.com/curators/ajaxgetcuratorrecommendations/". $curator_id ."//?query=&start=0&count=20" );

/*	
	// http://store.steampowered.com/apphoverpublic/201040?l=english&pagev6=true
	
	$ret['games'] = array();
	foreach( $game_html->find('.recommendation') as $elm ) {
		//$ret['games'][] = $elm->attr['data-ds-appid'];
		$ret['games'][] = Array(
			'appid' => $elm->attr['data-ds-appid'],
			'banner' => $elm->find('.recommendation_app_small_cap',0)->src,
			'url' => $elm->find('a',0)->href,
			'desc' => trim($elm->find('.recommendation_desc',0)->plaintext),
			'read_url' => $elm->find('.recommendation_readmore',0)->find('a',0)->href
		);
	}
*/	
	return $ret;
}

?>