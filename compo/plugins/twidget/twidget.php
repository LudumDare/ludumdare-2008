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
		
		$plugin_dir = '/compo/wp-content/plugins/twidget/';
		
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
	global $TwidgetHasRun;
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

add_action( 'widgets_init', create_function( '', 'register_widget( "twidget" );' ) );
add_action( 'wp_footer', 'AddTTVScripts', 500 );

?>