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

// Float Sleep //
function fsleep( $val ) {
	usleep( $val * 1000000.0 );
}
// Random Sleep //
function rsleep( $val, $pre = 0.1 ) {
	usleep( ($pre * 1.0) + (((rand()*1.0)/(getrandmax()*1.0)) * ($val*1.0)) );
}

// NO API, so an HTTP Get //
function steam_curator_get( $curator_id ) {
	// Get the main HTML file for Avatar and Num Followers //
	// http://store.steampowered.com/curator/537829/
	$main_url = "http://store.steampowered.com/curator/". $curator_id ."/";
	$main_html = file_get_html( $main_url );
	
	$ret = array();
	$ret['followers'] = $main_html->find('.num_followers', 0)->plaintext;
	$ret['avatar'] = $main_html->find('.curator_avatar', 0)->src;
	
	rsleep( 0.3 );

	// Use AJAX to get a 'next page' response, but of the first page w/ 20 elements //
	// http://store.steampowered.com/curators/ajaxgetcuratorrecommendations/537829//?query=&start=0&count=20
	$game_url = "http://store.steampowered.com/curators/ajaxgetcuratorrecommendations/". $curator_id ."//?query=&start=0&count=20";
	$game_json = json_decode(file_get_contents($game_url));
	$game_html = str_get_html( $game_json->results_html );

	$ret['games'] = array();
	foreach( $game_html->find('.recommendation') as $elm ) {
		$appid = $elm->attr['data-ds-appid'];
		
		rsleep( 0.2 );
		// http://store.steampowered.com/apphoverpublic/201040?l=english&pagev6=true
		$more_url = "http://store.steampowered.com/apphoverpublic/" . $appid . "?l=english&pagev6=true";
		$more_html = file_get_html( $more_url );
		 
		$ret['games'][] = Array(
			'appid' => $appid,
			'banner' => $elm->find('.recommendation_app_small_cap',0)->src,
			'url' => $elm->find('a',0)->href,
			'info' => trim($elm->find('.recommendation_desc',0)->plaintext),
			'read_url' => $elm->find('.recommendation_readmore',0)->find('a',0)->href,
			'name' => $more_html->find('h4',0)->plaintext,
			'released' => trim($more_html->find('.hover_release',0)->plaintext),
			'desc' => trim($more_html->find('#hover_desc',0)->plaintext)
		);
	}
	
	return $ret;
}

?>