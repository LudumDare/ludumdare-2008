<?php
/*
Plugin Name: Twidget
Plugin URI: http://www.ludumdare.com/compo/
Description: Twitch.tv widget for Wordpress
Version: 1.0
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: BSD
*/

$plugin_dir = '/compo/wp-content/plugins/twidget/';

$TwidgetHasRun = false;

class Twidget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'twidget', // Base ID //
			'Twidget', // Name //
			array( 'description' => __( 'Twitch.tv widget', 'text_domain' ), ) // ARGS //
		);
	}
	
	function widget($args, $instance) {
		extract($args);
		
		global $plugin_dir;
		
		$apikey = $instance['apikey'];
		$game = $instance['game'];
		$faqurl = $instance['faqurl'];

		echo $before_widget;
		
//		error_reporting(-1);
		
		echo '<div id="TTV">';
			echo '<div class="Widget">';
				echo '<div id="TTV_Video" class="Head"></div>';
				echo '<div id="TTV_Streams" class="Body">Loading...</div>';
				echo '<div class="FarEdge"></div>';
			echo '</div>';
			echo '<div class="Foot">';
				echo '<div class="FootBody">';
					echo '<span class="FootImg">';
						echo '<object data="' . $plugin_dir . 'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml"></object>';
					echo '</span>';
					echo '&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="http://www.twitch.tv/directory/game/' . rawurlencode($game) . '" target="_blank"> View All Streams</a>';
					echo '</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="' . $faqurl . '">FAQ</a>';
					echo '</span>';
					echo '<span id="TTV_Standby_Container" class="FootImg2">';
//						echo '<object id="TTV_Standby" data="' . $plugin_dir . 'ImgStandby.svg" width="22" height="22" type="image/svg+xml"></object>';
					echo '</span>';
				echo '</div>';
				echo '<div class="FootEdge"></div>';
			echo '</div>';
//			echo '<br />';
		echo '</div>';
		
		echo '<script>';
		echo 'var TwitchTV_APIKey = "' . $apikey . '";';
		echo 'var TwitchTV_Game = "' . $game . '";';
		echo 'var TwitchTV_FAQ = "' . $faqurl . '";';
		echo 'var TwitchTV_BaseDir = "' . $plugin_dir . '";';
		echo '</script>';

		global $TwidgetHasRun;
		$TwidgetHasRun = true;
		
		echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['apikey'] = strip_tags( $new_instance['apikey'] );
		$instance['game'] = strip_tags( $new_instance['game'] );
		$instance['faqurl'] = strip_tags( $new_instance['faqurl'] );
		return $instance;
	}

	function form($instance) {
		if ( isset( $instance[ 'apikey' ] ) ) {
			$apikey = $instance[ 'apikey' ];
		}
		else {
			$apikey = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'game' ] ) ) {
			$game = $instance[ 'game' ];
		}
		else {
			$game = __( 'Diablo III', 'text_domain' );
		}
		
		if ( isset( $instance[ 'faqurl' ] ) ) {
			$faqurl = $instance[ 'faqurl' ];
		}
		else {
			$faqurl = __( 'streaming-faq/', 'text_domain' );
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'apikey' ); ?>"><?php _e( 'Twitch API Key:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'apikey' ); ?>" name="<?php echo $this->get_field_name( 'apikey' ); ?>" type="text" value="<?php echo esc_attr( $apikey ); ?>" />

		<label for="<?php echo $this->get_field_id( 'game' ); ?>"><?php _e( 'Game Name:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'game' ); ?>" name="<?php echo $this->get_field_name( 'game' ); ?>" type="text" value="<?php echo esc_attr( $game ); ?>" />

		<label for="<?php echo $this->get_field_id( 'faqurl' ); ?>"><?php _e( 'FAQ URL:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'faqurl' ); ?>" name="<?php echo $this->get_field_name( 'faqurl' ); ?>" type="text" value="<?php echo esc_attr( $faqurl ); ?>" />
		</p>
		<?php 
	}
}

function AddTTVScripts() {
	global $TwidgetHasRun, $plugin_dir;
	if ( $TwidgetHasRun == true ) {
//	//	echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.min.css" />';
		echo '<script src="https://ttv-api.s3.amazonaws.com/twitch.min.js"></script>';
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'twidget.js"></script>';
		echo '<script src="' .$plugin_dir. 'twidget.min.js"></script>';
		echo '<script>';
		echo '	setTimeout( function(){';
		echo '			InitTwitchTV();';
		echo '		}, 200 );';
		echo '</script>';	
	}
}

// * * * //

function broadcast_list_func( $attr ) {
	// Default Attributes (Arguments) //
	$attr = shortcode_atts( Array(
		'hours' => 24
	), $attr );
	
	// * * * //

	$out = "";

	global $wpdb;
	$result = $wpdb->get_results("
		SELECT *, 
			(timestamp > (NOW() - INTERVAL 9 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS last_online
		FROM `wp_broadcast_streams`
		WHERE timestamp > (NOW() - INTERVAL {$attr['hours']} HOUR) 
		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
			units DESC;
	", ARRAY_A);
		
	$out .= "<div class='broadcast_table'>";
		$out .= "<div class='row header'>";
			$out .= "<div class='service'></div>";
			//$out .= "<div class='name'>Name</div>";
			$out .= "<div class='name'>Name</div>";
			$out .= "<div class='last_online'>Online</div>";
			$out .= "<div class='viewers'>Viewers</div>";
			$out .= "<div class='status'>Status</div>";
			$out .= "<div class='units'>Time Streaming</div>";
		$out .= "</div>";

		foreach( $result as $row ) {
			// Figure out when we were last online //
			$last_online_time = intval($row['last_online']);
			if ( $last_online_time <= 9 ) {
				$last_online = "NOW";
			}
			else if ( $last_online_time >= 60 ) {
				$hours = floor($last_online_time / 60);
				$last_online = "{$hours} hour".($hours > 1 ? "s":"")." ago";
			}
			else {
				$minutes = floor($last_online_time);
				$last_online = "{$minutes} minutes ago";	// Always Greater than 9 )
			}
			
			$units_value = intval($row['units']);
			$units = floor($units_value/60) . ":" . str_pad($units_value%60, 2, '0', STR_PAD_LEFT);

			// Build Page //
			$out .= "<div class='row" . ($row['live'] ? " live" : "") ."'>";
				$out .= "<div class='service{$row['service_id']}'></div>";
				//$out .= "<div class='name'>{$row['display_name']}</div>";
				$out .= "<div class='name'><a href='{$row['url']}'>{$row['display_name']}</a> [{$row['followers']}]".($row['mature']?" <span class='mature'>[M]</span>":"")."</div>";
				$out .= "<div class='last_online'>{$last_online}</div>";
				$out .= "<div class='viewers'>{$row['viewers']}</div>";
				$out .= "<div class='status'>{$row['status']}</div>";
				$out .= "<div class='units'>{$units}</div>";
			$out .= "</div>";
		}
	$out .= "</div>";
	
	// * * * //
	
	return $out;
}
add_shortcode( 'broadcast_list', 'broadcast_list_func' );


// Add Local Style Sheet style.css //
add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );
function prefix_add_my_stylesheet() {
    wp_register_style( 'broadcast-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'broadcast-style' );
}


add_action( 'widgets_init', create_function( '', 'register_widget( "twidget" );' ) );
add_action( 'wp_footer', 'AddTTVScripts', 500 );

?>