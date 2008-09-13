<?php
function rt_simplemap_activate()
{
	rt_simplemap_create_db(false); 
	
}
function rt_simplemap_add_pages() {
    // Add a new submenu under Options:
    add_options_page('SimpleMap Options', 'SimpleMap', 8, 'rt_simplemap', 'rt_simplemap_options_page');

    // Add a new submenu under Manage:
    add_management_page('SimpleMap Manage', 'SimpleMap', 8, 'rt_simplemap', 'rt_simplemap_manage_page');
}

function rt_simplemap_manage_page()
{
  // variables for the field and option names 
    $hidden_field_name = 'rt_simplemap_submit_hidden';
    $data_field_name = 'mt_simplemap_api_key';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
       
	 if ($opt_val == "reset")
		{
		rt_simplemap_create_db(true);
		?>
	<div class="updated"><p><strong><?php _e('SimpleMap database created.', 'mt_trans_domain' ); ?></strong></p></div>
<?php
		} else	
		{
		
		?>
<div class="updated"><p><strong><?php _e('Unknown SimpleMap command.', 'mt_trans_domain' ); ?></strong></p></div>
<?php
		}
	
        // Put an options updated message on the screen
}

    // Now display the options editing screen

    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'SimpleMap Plugin Manager', 'mt_trans_domain' ) . "</h2>";
    // options form
    ?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Enter command (valid commands: reset) :", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="10">
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Send command', 'mt_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php
}


function rt_simplemap_options_page() {

    // variables for the field and option names 
    $opt_name = 'rt_simplemap_api_key';
    $hidden_field_name = 'rt_simplemap_submit_hidden';
    $data_field_name = 'mt_simplemap_api_key';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php
    }
    // Now display the options editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'SimpleMap Plugin Options', 'mt_trans_domain' ) . "</h2>";
    // options form
    
    ?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("GoogleMap API Key:", 'mt_trans_domain' ); ?>
  <input type="text" name="<?php echo $data_field_name; ?>2" value="<?php echo $opt_val; ?>" size="120">
</p>
<hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>
</div>
<?php
 
}

function rt_simplemap_create_db($deleteIfExists)
{
	global $wpdb;
   $table_name = $wpdb->prefix . "simplemap";

if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) 
{
	if ($deleteifExists == true)
	{
	//database exists
	rt_simplemap_reset_db();
	} else
	{
		return;
	}
}

$sql = "CREATE TABLE " . $table_name . " (
	  id int(11),
	  lat decimal(12,8) NOT NULL,
	  lon decimal(12,8) NOT NULL,
	  PRIMARY KEY (id)
	);";
	
	add_option("rt_simplemap_db_version", "1.0");
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	echo "<p><strong>Creating SimpleMap database...</strong></p>";
}

function rt_simplemap_reset_db()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "simplemap";
	$sql = "DROP TABLE " . $table_name;
	$wpdb->query($sql);
?>
<p><strong>Deleting SimpleMap database...</strong></p>
<?php
}
?>