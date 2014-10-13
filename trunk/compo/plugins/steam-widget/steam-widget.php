<?php
/*
Plugin Name: steam-widget
Plugin URI: http://ludumdare.com/
Description: Steam Group and Curator widget
Version: 1.0
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: BSD
*/
// - ----------------------------------------------------------------------------------------- - //
// Store the current directory part of the requested URL (for building paths to files) //
@$http_dir = dirname($_SERVER["REQUEST_URI"]);
$http_request_time = $_SERVER['REQUEST_TIME'];
chdir(dirname(__FILE__));	// Change Working Directory to where I am (for my local paths) //
// - ----------------------------------------------------------------------------------------- - //
function wp_steam_info_get( $more_query = "" ) {
	global $wpdb;
	$results = $wpdb->get_results("
		SELECT *
		FROM `wp_steam_info`
		{$more_query};
	", ARRAY_A);
	
	$ret = Array();
	
	foreach ( $results as $pair ) {
		$ret[$pair['name']] = $pair['value'];
	}
	
	return $ret;
}
// - ----------------------------------------------------------------------------------------- - //
function wp_steam_games_get( $more_query = "" ) {
	global $wpdb;
	return $wpdb->get_results("
		SELECT *
		FROM `wp_steam_games`
		{$more_query};
	", ARRAY_A);	
}
// - ----------------------------------------------------------------------------------------- - //
class SteamWidget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'steam-widget', // Base ID //
			'Steam Widget', // Name //
			array( 'description' => __( 'Steam Group and Curator Widget', 'text_domain' ), ) // ARGS //
		);
	}

	public function widget( $args, $instance ) {
		$steam_info = wp_steam_info_get();
		$steam_games = wp_steam_games_get();// "ORDER BY RAND() LIMIT 3" );
		
//		print_r($steam_games);
		
		echo "
			<div class='steambox'>
				<div class='header'></div>
				<div class='content nobottom'>
					<div class='logo'></div>
					<div class='avatar'></div>
					<div class='headline_small'>STEAM CURATOR</div>
					<div class='headline_big'>Ludum Dare</div>
				</div>
				<div class='content nobottom'>Steam games created during Ludum Dare events. <strong>Follow us!</strong> Help share LD with everyone!</div>
				<div class='content overflow'>
					<div class='left'><div class='countbox' style='color:#8bc53f'>
						<div class='count'>{$steam_info['curator_games']}</div>
						<div class='label'>GAMES</div>
					</div></div>
					<div class='right'><div class='countbox' style='color:#62a7e3'>
						<span class='right'>
							<div class='follow_button' style='margin-left:12px;margin-top:5px'>Follow</div>
						</span>
						<span class='left'>
							<div class='count'>{$steam_info['curator_followers']}</div>
							<div class='label'>FOLLOWERS</div>
						</span>
					</div></div>
				</div>
				<div class='rule'></div>
				<div class='content center'>
		";
		
		global $http_request_time;

		foreach( $steam_games as $game ) {
			$release = strtotime($game['released']);
			echo $release . " " . $game['released'];
			$banner_class = "";
			if ( $release <= 0 ) {
				$banner_class = "soon";
			}
			else if ( $release > $http_request_time ) {
				$banner_class = "soon";
			}
			else if ( $release > ($http_request_time - 60*24*60*60) ) {
				$banner_class = "new";
			}
				
			echo "	<div class='banner {$banner_class}'>
						<a href='{$game['url']}' title='{$game['name']}' target='_blank'><img src='{$game['banner']}' /></a>
					</div>";
		}

		echo "
				</div>
				<div class='rule'></div>
				<div class='content nobottom'>
					<span class='right'>
						<div class='follow_button' style='float:right'>Join</div>
					</span>
					<div class='avatar_small'></div>
					<div class='headline_small'>STEAM GROUP</div>
					<div class='headline_big'>Ludum Dare</div>
				</div>
				<div class='content nobottom'>Dev together. Play together.</div>
				<div class='content overflow'>
					<div class='left'><div class='countbox' style='color:#9a9a9a'>
						<div class='count'>{$steam_info['group_members']}</div>
						<div class='label'>MEMBERS</div>
					</div></div>
					<div class='right'><div class='countbox' style='color:#62a7e3'>
						<div class='count'>{$steam_info['group_members_online']}</div>
						<div class='label'>ONLINE</div>
					</div></div>
					<div class='center'><div class='countbox' style='color:#8bc53f'>
						<div class='count'>{$steam_info['group_members_in_game']}</div>
						<div class='label'>IN-GAME</div>
					</div></div>
				</div>
				<div class='footer'></div>
			</div>
		";
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}
// - ----------------------------------------------------------------------------------------- - //
add_action('widgets_init',
     create_function('', 'return register_widget("SteamWidget");')
);
// - ----------------------------------------------------------------------------------------- - //

// - ----------------------------------------------------------------------------------------- - //
add_action( 'wp_enqueue_scripts', 'steam_add_my_stylesheet' );
function steam_add_my_stylesheet() {
    wp_register_style( 'steam-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'steam-style' );
}
// - ----------------------------------------------------------------------------------------- - //
?>
