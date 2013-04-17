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
		
	}
	
//	function RandomPostWidget() {
//		$widget_ops = array('classname' => 'Twidget', 'description' => 'Twitch.tv widget' );
//		$this->WP_Widget('Twidget', 'Twitch.tv widget', $widget_ops);
//	}
//	
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		return $instance;
	}
	
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		
		if (!empty($title))
		  echo $before_title . $title . $after_title;;
		
		// WIDGET CODE GOES HERE
		echo "<h1>This is my new widget!</h1>";
		
		echo $after_widget;
	}
}

add_action( 'widgets_init', create_function('', 'return register_widget("Twidget");') );?>

?>