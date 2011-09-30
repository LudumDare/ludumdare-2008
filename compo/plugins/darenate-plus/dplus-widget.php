<?php

function widget_darenateplus_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_darenateplusform($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_darenateplusform');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		DarenatePlusForm();
		echo $after_widget;
	}
	function widget_darenateplustotal($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_darenateplustotal');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		echo '<p>';DarenatePlusTotal(); echo '</p>';
		echo $after_widget;
	}
	function widget_darenatepluswall($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_darenatepluswall');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		DarenatePlusWall();
		echo $after_widget;
	}

	function widget_darenateplushighmonthlywall($args) {
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_darenatepluswall');
		//$title = $options['title'];
		$title = 'Highest Donors (last month)';

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		DarenatePlusHighMonthlyWall();
		echo '<center>To make a donation, <a href="/compo/donations/">click here</a></center>';
		echo $after_widget;
	}

	function widget_darenateplusform_control() {
		$options = get_option('widget_darenateplusform');
		if ( !is_array($options) )
			$options = array('title'=>'Donate');
		if ( $_POST['darenateplusf-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['darenateplusf-title']));
			update_option('widget_darenateplusform', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="darenateplusf-title">' . __('Title:') . ' <input style="width: 200px;" id="darenateplusf-title" name="darenateplusf-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="darenateplusf-submit" name="darenateplusf-submit" value="1" />';
	}
	function widget_darenateplustotal_control() {
		$options = get_option('widget_darenateplustotal');
		if ( !is_array($options) )
			$options = array('title'=>'Total Donations');
		if ( $_POST['darenateplust-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['darenateplust-title']));
			update_option('widget_darenateplustotal', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="darenateplust-title">' . __('Title:') . ' <input style="width: 200px;" id="darenateplust-title" name="darenateplust-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="darenateplust-submit" name="darenateplust-submit" value="1" />';
	}
	function widget_darenatepluswall_control() {
		$options = get_option('widget_darenatepluswall');
		if ( !is_array($options) )
			$options = array('title'=>'Recognition Wall');
		if ( $_POST['darenateplusw-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['darenateplusw-title']));
			update_option('widget_darenatepluswall', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="darenateplusw-title">' . __('Title:') . ' <input style="width: 200px;" id="darenateplusw-title" name="darenateplusw-title" type="text" value="'.$title.'" /></label></p>';
		echo '<input type="hidden" id="darenateplusw-submit" name="darenateplusw-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('Darenate Plus Form', 'widgets'), 'widget_darenateplusform');
	register_sidebar_widget(array('Darenate Plus Total', 'widgets'), 'widget_darenateplustotal');
	register_sidebar_widget(array('Darenate Plus Wall', 'widgets'), 'widget_darenatepluswall');
	register_sidebar_widget(array('Darenate Plus High Monthly Wall', 'widgets'), 'widget_darenateplushighmonthlywall');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('Darenate Plus Form', 'widgets'), 'widget_darenateplusform_control', 300, 100);
	register_widget_control(array('Darenate Plus Total', 'widgets'), 'widget_darenateplustotal_control', 300, 100);
	register_widget_control(array('Darenate Plus Wall', 'widgets'), 'widget_darenatepluswall_control', 300, 100);
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_darenateplus_init');
?>