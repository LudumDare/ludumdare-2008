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
		
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo __( 'Hello, World!', 'text_domain' );
		echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}

add_action( 'widgets_init', function(){register_widget("Twidget");} );

?>