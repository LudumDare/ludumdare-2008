<?php


function compo2_options_menu() {
	add_options_page('Compo2 Options', 'Compo2', 'manage_options', 'compo2-settings-options', 'compo2_options');
}

function compo2_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

    if(isset($_POST["submit"])) {
    	update_option('c2_navigation_enable', $_POST['enable'] == 'on');
    	update_option('c2_navigation_slug', $_POST['slug']);
    	update_option('c2_navigation_name', $_POST['name']);
    	update_option('c2_navigation_name_url', $_POST['name_url']);
    	$showSavedMessage = true;

		if (function_exists('apcu_delete')) {
			apcu_delete('c2_navigation_cache');
		}
	}

	$enable = get_option('c2_navigation_enable');
	$slug = get_option('c2_navigation_slug');
	$name = get_option('c2_navigation_name');
	$name_url = get_option('c2_navigation_name_url');

	echo '<h1>Compo2 Options</h1>';
	if (isset($showSavedMessage)) {
    	echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
    }

	?>

	<h3 class="title">Navigation block</h3>
	<p>These settings control the homepage block featuring handy links to the user entry and the event in general. It should be enabled from the kickoff of the event to the end of the rating phase.</p>

	<form name="compo2" method="post" action="">

		<table class="form-table">
		<tbody>
		<tr>
			<th scope="row">Event page ID</th>
			<td><input type="text" name="slug" value="<?php echo $slug; ?>" placeholder="e.g. ludum-dare-XX" class="regular-text code" /></td>
		</tr>
		<tr>
			<th scope="row">Event name</th>
			<td><input type="text" name="name" value="<?php echo $name; ?>" placeholder="e.g. Ludum Dare XX" class="regular-text" /></td>
		</tr>
		<tr>
			<th scope="row">Announcement post URL</th>
			<td><input type="text" name="name_url" value="<?php echo $name_url; ?>" placeholder="e.g. /compo/2000/01/01/welcome-to-ludum-dare-xx/" class="regular-text code" style="width: 90%" /></td>
		</tr>
		</tbody>
		</table>

		<p>
			<input type="checkbox" name="enable" id="enable" <?php checked($enable, 1); ?> />
			<label for="enable">Enable navigation block</label>
		</p>
		
		<?php submit_button(); ?>

	</form>

	</div>
	<?php

}
?>
