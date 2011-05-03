<?php
/*
Plugin Name: I Like This
Plugin URI: http://www.my-tapestry.com/i-like-this/
Description: This plugin allows your visitors to simply like your posts instead of commment it.
Version: 1.7.1
Author: Benoit "LeBen" Burgener
Author URI: http://benoitburgener.com

Copyright 2009  BENOIT LEBEN BURGENER  (email : CONTACT@BENOITBURGENER.COM)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

#### LOAD TRANSLATIONS ####
load_plugin_textdomain('i-like-this', 'wp-content/plugins/i-like-this/lang/', 'i-like-this/lang/');
####


#### INSTALL PROCESS ####
$ilt_dbVersion = "1.0";

function setOptionsILT() {
	global $wpdb;
	global $ilt_dbVersion;
	
	$table_name = $wpdb->prefix . "ilikethis_votes";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			time TIMESTAMP NOT NULL,
			post_id BIGINT(20) NOT NULL,
			ip VARCHAR(15) NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("ilt_dbVersion", $ilt_dbVersion);
	}
	
	add_option('ilt_jquery', '1', '', 'yes');
	add_option('ilt_onPage', '1', '', 'yes');
	add_option('ilt_textOrImage', 'image', '', 'yes');
	add_option('ilt_text', 'I like This', '', 'yes');
}

register_activation_hook(__FILE__, 'setOptionsILT');

function unsetOptionsILT() {
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."ilikethis_votes");

	delete_option('ilt_jquery');
	delete_option('ilt_onPage');
	delete_option('ilt_textOrImage');
	delete_option('ilt_text');
	delete_option('most_liked_posts');
	delete_option('ilt_dbVersion');
}

register_uninstall_hook(__FILE__, 'unsetOptionsILT');
####


#### ADMIN OPTIONS ####
function ILikeThisAdminMenu() {
	add_options_page('I Like This', 'I Like This', '10', 'ILikeThisAdminMenu', 'ILikeThisAdminContent');
}
add_action('admin_menu', 'ILikeThisAdminMenu');

function ILikeThisAdminRegisterSettings() { // whitelist options
	register_setting( 'ilt_options', 'ilt_jquery' );
	register_setting( 'ilt_options', 'ilt_onPage' );
	register_setting( 'ilt_options', 'ilt_textOrImage' );
	register_setting( 'ilt_options', 'ilt_text' );
}
add_action('admin_init', 'ILikeThisAdminRegisterSettings');

function ILikeThisAdminContent() {
?>
<div class="wrap">
	<h2>"I Like This" Options</h2>
	<br class="clear" />
			
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div id="ilikethisoptions" class="postbox">
		<h3><?php _e('Configuration'); ?></h3>
			<div class="inside">
			<form method="post" action="options.php">
			<?php settings_fields('ilt_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="ilt_jquery"><?php _e('jQuery framework', 'i-like-this'); ?></label></th>
					<td>
						<select name="ilt_jquery" id="ilt_jquery">
							<?php echo get_option('ilt_jquery') == '1' ? '<option value="1" selected="selected">'.__('Enabled', 'i-like-this').'</option><option value="0">'.__('Disabled', 'i-like-this').'</option>' : '<option value="1">'.__('Enabled', 'i-like-this').'</option><option value="0" selected="selected">'.__('Disabled', 'i-like-this').'</option>'; ?>
						</select>
						<span class="description"><?php _e('Disable it if you already have the jQuery framework enabled in your theme.', 'i-like-this'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><legend><?php _e('Image or text?', 'i-like-this'); ?></legend></th>
					<td>
						<label for="ilt_textOrImage" style="padding:3px 20px 3px 0; margin-right:20px; background: url(<?php echo WP_PLUGIN_URL.'/i-like-this/css/add.png'; ?>) no-repeat right center;">
						<?php echo get_option('ilt_textOrImage') == 'image' ? '<input type="radio" name="ilt_textOrImage" id="ilt_textOrImage" value="image" checked="checked">' : '<input type="radio" name="ilt_textOrImage" id="ilt_textOrImage" value="image">'; ?>
						</label>
						<label for="ilt_text">
						<?php echo get_option('ilt_textOrImage') == 'text' ? '<input type="radio" name="ilt_textOrImage" id="ilt_textOrImage" value="text" checked="checked">' : '<input type="radio" name="ilt_textOrImage" id="ilt_textOrImage" value="text">'; ?>
						<input type="text" name="ilt_text" id="ilt_text" value="<?php echo get_option('ilt_text'); ?>" />
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><legend><?php _e('Automatic display', 'i-like-this'); ?></legend></th>
					<td>
						<label for="ilt_onPage">
						<?php echo get_option('ilt_onPage') == '1' ? '<input type="checkbox" name="ilt_onPage" id="ilt_onPage" value="1" checked="checked">' : '<input type="checkbox" name="ilt_onPage" id="ilt_onPage" value="1">'; ?>
						<?php _e('<strong>On all posts</strong> (home, archives, search) at the bottom of the post', 'i-like-this'); ?>
						</label>
						<p class="description"><?php _e('If you disable this option, you have to put manually the code', 'i-like-this'); ?><code>&lt;?php if(function_exists(getILikeThis)) getILikeThis('get'); ?&gt;</code> <?php _e('wherever you want in your template.', 'i-like-this'); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'i-like-this'); ?>" /></th>
					<td></td>
				</tr>
			</table>
			</form>
			</div>
		</div>
	</div>
	
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div id="ilikethisoptions" class="postbox">
		<h3><?php _e('You like this plugin?'); ?></h3>
			<div class="inside">
				<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="benoit.burgener@gmail.com">
				<input type="hidden" name="item_name" value="Wordpress plugin">
				<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit">
				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
####


#### WIDGET ####
function most_liked_posts($numberOf, $before, $after, $show_count) {
	global $wpdb;

    $request = "SELECT ID, post_title, meta_value FROM $wpdb->posts, $wpdb->postmeta";
    $request .= " WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id";
    $request .= " AND post_status='publish' AND post_type='post' AND meta_key='_liked'";
    $request .= " ORDER BY $wpdb->postmeta.meta_value+0 DESC LIMIT $numberOf";
    $posts = $wpdb->get_results($request);

    foreach ($posts as $post) {
    	$post_title = stripslashes($post->post_title);
    	$permalink = get_permalink($post->ID);
    	$post_count = $post->meta_value;
    	
    	echo $before.'<a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow">' . $post_title . '</a>';
		echo $show_count == '1' ? ' ('.$post_count.')' : '';
		echo $after;
    }
}

function add_widget_most_liked_posts() {
	function widget_most_liked_posts($args) {
		extract($args);
		$options = get_option("most_liked_posts");
		if (!is_array( $options )) {
			$options = array(
			'title' => 'Most liked posts',
			'number' => '5',
			'show_count' => '0'
			);
		}
		$title = $options['title'];
		$numberOf = $options['number'];
		$show_count = $options['show_count'];
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo '<ul class="mostlikedposts">';

		most_liked_posts($numberOf, '<li>', '</li>', $show_count);
		
		echo '</ul>';
		echo $after_widget;
	}	
	register_sidebar_widget('Most liked posts', 'widget_most_liked_posts');
	
	function options_widget_most_liked_posts() {
		$options = get_option("most_liked_posts");
		
		if (!is_array( $options )) {
			$options = array(
			'title' => 'Most liked posts',
			'number' => '5',
			'show_count' => '0'
			);
		}
		
		if ($_POST['mlp-submit']) {
			$options['title'] = htmlspecialchars($_POST['mlp-title']);
			$options['number'] = htmlspecialchars($_POST['mlp-number']);
			$options['show_count'] = $_POST['mlp-show-count'];
			if ( $options['number'] > 15) { $options['number'] = 15; }
			
			update_option("most_liked_posts", $options);
		}
		?>
		<p><label for="mlp-title"><?php _e('Title:', 'i-like-this'); ?><br />
		<input class="widefat" type="text" id="mlp-title" name="mlp-title" value="<?php echo $options['title'];?>" /></label></p>
		
		<p><label for="mlp-number"><?php _e('Number of posts to show:', 'i-like-this'); ?><br />
		<input type="text" id="mlp-number" name="mlp-number" style="width: 25px;" value="<?php echo $options['number'];?>" /> <small>(max. 15)</small></label></p>
		
		<p><label for="mlp-show-count"><input type="checkbox" id="mlp-show-count" name="mlp-show-count" value="1"<?php if($options['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show post count', 'i-like-this'); ?></label></p>
		
		<input type="hidden" id="mlp-submit" name="mlp-submit" value="1" />
		<?php
	}
	register_widget_control('Most liked posts', 'options_widget_most_liked_posts');
} 

add_action('init', 'add_widget_most_liked_posts');
####


#### FRONT-END VIEW ####
function getILikeThis($arg) {
	global $wpdb;
	$post_ID = get_the_ID();
	$ip = $_SERVER['REMOTE_ADDR'];
	
    $liked = get_post_meta($post_ID, '_liked', true) != '' ? get_post_meta($post_ID, '_liked', true) : '0';
	$voteStatusByIp = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."ilikethis_votes WHERE post_id = '$post_ID' AND ip = '$ip'");
		
    if (!isset($_COOKIE['liked-'.$post_ID]) && $voteStatusByIp == 0) {
    	if (get_option('ilt_textOrImage') == 'image') {
    		$counter = '<a onclick="likeThis('.$post_ID.');" class="image">'.$liked.'</a>';
    	}
    	else {
    		$counter = $liked.' <a onclick="likeThis('.$post_ID.');">'.get_option('ilt_text').'</a>';
    	}
    }
    else {
    	$counter = $liked;
    }
    
    $iLikeThis = '<div id="iLikeThis-'.$post_ID.'" class="iLikeThis">';
    	$iLikeThis .= '<span class="counter">'.$counter.'</span>';
    $iLikeThis .= '</div>';
    
    if ($arg == 'put') {
	    return $iLikeThis;
    }
    else {
    	echo $iLikeThis;
    }
}

if (get_option('ilt_onPage') == '1') {
	function putILikeThis($content) {
		if(!is_feed() && !is_page()) {
			$content.= getILikeThis('put');
		}
	    return $content;
	}

	add_filter('the_content', putILikeThis);
}

function enqueueScripts() {
	if (get_option('ilt_jquery') == '1') {
	    wp_enqueue_script('iLikeThis', WP_PLUGIN_URL.'/i-like-this/js/i-like-this.js', array('jquery'));	
	}
	else {
	    wp_enqueue_script('iLikeThis', WP_PLUGIN_URL.'/i-like-this/js/i-like-this.js');	
	}
}

function addHeaderLinks() {
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/i-like-this/css/i-like-this.css" media="screen" />'."\n";
	echo '<script type="text/javascript">var blogUrl = \''.get_bloginfo('wpurl').'\'</script>'."\n";
}

add_action('init', enqueueScripts);
add_action('wp_head', addHeaderLinks);
?>