<?php
/*
Plugin Name: Darenate Plus
Plugin URI: http://code.google.com/ludumdare/
Description: A fork of "Donate Plus" 1.85 by M. Fitzpatrick (http://devbits.ca)
Author: Mike Kasprzak
Version: 1.00
Author URI: http://www.ludumdare.com

 Copyright 2011 Mike Kasprzak and M. Fitzpatrick

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$dplus_db_version = "1.0";
include_once('dplus-widget.php');
include_once('manage-dp.php');
include_once('manage-expenses.php');
$currency = array( 	'USD' => array( 'type' => __('U.S. Dollar', 'dplus'), 			'symbol' => '$'	), 
					'AUD' => array( 'type' => __('Australian Dollar', 'dplus'), 	'symbol' => '$'	), 
					'CAD' => array( 'type' => __('Canadian Dollar', 'dplus'), 		'symbol' => '$'	), 
					'CHF' => array( 'type' => __('Swiss Franc', 'dplus'), 			'symbol' => ''	) , 
					'CZK' => array( 'type' => __('Czech Koruna', 'dplus'), 			'symbol' => ''	), 
					'DKK' => array( 'type' => __('Danish Krone', 'dplus'), 			'symbol' => ''	), 
					'EUR' => array( 'type' => __('Euro', 'dplus'), 					'symbol' => '€'	), 
					'GBP' => array( 'type' => __('Pound Sterling','dplus'), 		'symbol' => '£'	), 
					'HKD' => array( 'type' => __('Hong Kong Dollar', 'dplus'), 		'symbol' => '$'	), 
					'HUF' => array( 'type' => __('Hungarian Forint', 'dplus'), 		'symbol' => ''	), 
					'ILS' => array( 'type' => __('Israeli New Shekel', 'dplus'), 	'symbol' => ''	), 
					'JPY' => array( 'type' => __('Japanese Yen', 'dplus'), 			'symbol' => '¥'	),
					'MXN' => array( 'type' => __('Mexican Peso', 'dplus'), 			'symbol' => '$' ),
					'NOK' => array( 'type' => __('Norwegian Krone', 'dplus'), 		'symbol' => ''	), 
					'NZD' => array( 'type' => __('New Zealand Dollar', 'dplus'), 	'symbol' => '$'	), 
					'PLN' => array( 'type' => __('Polish Zloty', 'dplus'), 			'symbol' => ''	), 
					'SEK' => array( 'type' => __('Swedish Krona', 'dplus'), 		'symbol' => ''	), 
					'SGD' => array( 'type' => __('Singapore Dollar', 'dplus'), 		'symbol' => '$'	) 
				);

wp_enqueue_script('jquery'); 

if( !class_exists('DarenatePlus') ):
	class DarenatePlus{
		function DarenatePlus() { //constructor
			//ACTIONS
				#Add Settings Panel
				add_action( 'admin_menu', array($this, 'AddPanel') );
				add_action( 'admin_head', array($this, 'icon_css') );
				#Update Settings on Save
				if( $_POST['action'] == 'dplus_update' )
					add_action( 'init', array($this,'SaveSettings') );
				#Save Default Settings
					add_action( 'init', array($this, 'DefaultSettings') );
				#Uninstall Donate Plus
				if( $_POST['action'] == 'dplus_delete' )
					add_action( 'init', array($this,'UninstallDP') );
				#Comment Box Limit
					add_action( 'wp_head', array($this, 'TextLimitJS') );
			//SHORTCODES
				#Add Form Shortcode
				add_shortcode('donateplus', array($this, 'DonatePage') );
				#Add Wall Shortcode
				add_shortcode('donorwall', array($this, 'DonorWall') );
				add_shortcode('expensewall', array($this, 'ExpenseWall') );
				#Add Total Donations Count Shortcode
				add_shortcode('donatetotal', array($this, 'DonateTotal') );
				add_shortcode('expensetotal', array($this, 'ExpenseTotal') );
				add_shortcode('fundstotal', array($this, 'FundsTotal') );
				add_shortcode('expensedate', array($this, 'ExpenseDate') );
			//LOCALIZATION
				#Place your language file in the plugin folder and name it "wpfrom-{language}.mo"
				#replace {language} with your language value from wp-config.php
				load_plugin_textdomain( 'dplus', '/wp-content/plugins/darenate-plus' );
			//INSTALL TABLE
				#Runs the database installation for the wp_donations table
				register_activation_hook( __FILE__, array($this, 'DarenatePlusInstall') );

		}
		
		function AddPanel(){
			global $manageDP;
			global $manageDPExpenses;
			add_menu_page( __("Darenate Plus",'dplus'), __("Darenate Plus",'dplus'), 10, 'DarenatePlus', array($manageDP, 'Manage'), 'div' );
			add_submenu_page( 'DarenatePlus', 'Expenses', 'Expenses', 10, 'darenateplusExpenses', array($manageDPExpenses, 'Manage') );
			add_submenu_page( 'DarenatePlus', 'Add Expense', 'Add Expense', 10, 'darenateplusAddExpense', array($manageDPExpenses, 'AddPage') );
			add_submenu_page( 'DarenatePlus', 'Add Proxy Donor', 'Add Proxy Donor', 10, 'darenateplusAddProxyDonor', array($manageDP, 'AddPage') );
			add_submenu_page( 'DarenatePlus', 'Settings', 'Settings', 10, 'darenateplusSettings', array($this, 'Settings') );
		}
		function icon_css(){
			echo '<style type="text/css">
			#toplevel_page_DarenatePlus div.wp-menu-image {
			  background:transparent url("'.trailingslashit(get_option('siteurl')).'wp-content/plugins/darenate-plus/dplus-menu.png") no-repeat center -32px;
			} 
			#toplevel_page_DarenatePlus:hover div.wp-menu-image, #toplevel_page_DarenatePlus.current div.wp-menu-image, #toplevel_page_DarenatePlus.wp-has-current-submenu div.wp-menu-image {
			  background:transparent url("'.trailingslashit(get_option('siteurl')).'wp-content/plugins/darenate-plus/dplus-menu.png") no-repeat center 0px;
			}
			</style>';
		
		}
		
		function DefaultSettings () {
			$default = array( 
								'paypal_email'		=> get_option('admin_email'),
								'paypal_currency'	=> 'USD',
								'testing_mode'		=> 1,
								'donate_desc'		=> 'Donation to '.get_option('blogname'),
								'default_value'		=> 10,
								'button_img'		=> 1,
								'custom_button'		=> 'http://',
								'subscribe'			=> array('1'),
								'duration'			=> 1,
								'enable_wall'		=> 1,
								'wall_url'			=> get_option('blogname'),
								'wall_max'			=> 0,
								'ty_msg'			=> 'Thanks for the donation! You rule!',
								'enable_ty'			=> 1,
								'ty_name'			=> get_option('blogname'),
								'ty_email'			=> get_option('admin_email'),
								'ty_subject'		=> 'Thank you from '.get_option('blogname'),
								'ty_emailmsg'			=> "{donor},\n Thank you for your support of ".get_option('blogname').". Your donation of {amount} was truly outstanding!  If you opted for recognition, your name and comments have been posted on the <a href='{donorwall}'>Donor Wall</a>, so all can see how truly great and noble you really are.  Thanks again!\n\n".get_option('blogname')
							);
			if( !get_option('DarenatePlus') ): #Set Defaults if no values exist
				add_option( 'DarenatePlus', $default );
			else: #Set Defaults if new value does not exist
				$dplus = get_option( 'DarenatePlus' );
				foreach( $default as $key => $val ):
					if( !$dplus[$key] ):
						$dplus[$key] = $val;
						$new = true;
					endif;
				endforeach;
				if( $new )
					update_option( 'DarenatePlus', $dplus );
			endif;
		}
		
		function SaveSettings(){
			check_admin_referer('dplus-update-options');
			$update = get_option( 'DarenatePlus' );
			$update["paypal_email"] = $_POST['paypal_email'];
			$update["paypal_currency"] = $_POST['paypal_currency'];
			$update["testing_mode"] = $_POST['testing_mode'];
			$update["donate_desc"] = $_POST['donate_desc'];
			$update["default_value"] = $_POST['default_value'];
			$update["button_img"] = $_POST['button_img'];
			$update["custom_button"] = $_POST['custom_button'];
			$update["subscribe"] = $_POST['subscribe'];
			$update["duration"] = $_POST['duration'];
			$update["enable_wall"] = $_POST['enable_wall'];
			$update["wall_url"] = $_POST['wall_url'];
			$update["wall_max"] = $_POST['wall_max'];
			$update["ty_msg"] = $_POST['ty_msg'];
			$update['enable_ty'] = $_POST['enable_ty'];
			$update["ty_subject"] = $_POST['ty_subject'];
			$update["ty_email"] = $_POST['ty_email'];
			$update["ty_name"] = $_POST['ty_name'];
			$update["ty_emailmsg"] = $_POST['ty_emailmsg'];
			$update["IPN_email"] = $_POST['IPN_email'];
			update_option( 'DarenatePlus', $update );
			$_POST['notice'] = __('Settings Saved', 'dplus');
		}
		
		function CurrencySelect($sel){
			global $currency;			
			foreach( $currency as $key => $cur ):
				$output .= "<option value='$key'";
				if( $sel == $key ) $output .= " selected='selected'";
				$output .= ">".$cur['type']."</option>\n";
			endforeach;	
			return $output;
		}
		
		function PageSelect($sel){
			global $wpdb;
			$pages = $wpdb->get_results("SELECT ID, post_status, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_status DESC, menu_order ASC, post_title ASC");
			$output .= '<option value="sidebar"';
			if( $sel == 'sidebar' ) $output .= ' selected="selected"';
			$output .= '>SideBar</option>'."\n"; 
			
			foreach( $pages as $page ):
				if( $page->post_status == 'draft') $draft = '{draft} '; else $draft = '';
				$output .= '<option value="'.$page->ID.'"';
				if( $sel == $page->ID ) $output .= ' selected="selected"';
				$output .= '>'.$draft.$page->post_title.'</option>'."\n";
			endforeach;
			return $output;
		}
		
		function RecurringSelect($sel=false){
			$values = array('1'=>'Once', 'D'=>'Daily', 'W'=>'Weekly', 'M'=>'Monthly', 'Y'=>'Yearly');
			foreach($values as $key=>$label):
				$output .= "<option value='$key'";
				if($sel == $key) $output .= " selected='selected'";
				$output .= ">$label</option>\n";
			endforeach;
			return $output;
		}
		function RecurringArray($sel=false, $name=false){
			$values = array('1'=>__('Only Once (no recurring)','dplus'), 'D'=>__('Daily','dplus'), 'W'=>__('Weekly','dplus'), 'M'=>__('Monthly','dplus'), 'Y'=>__('Yearly','dplus'));
			foreach($values as $key=>$label):
				$output .= "<label><input name='".$name."[]' value='$key'";
				if(is_array($sel)):
					if(in_array($key, $sel)) $output .= " checked='checked'";
				endif;
				$output .= "type='checkbox' /> $label </label> \n";
			endforeach;	
			return $output;
		}
		function TestingSelect($sel=false){
			$values = array('1'=>'Live with PayPal', '2'=>'Testing with www.belahost.com/pp', '3'=>'Testing with PayPal Sandbox');
			foreach( $values as $key=>$label):
				$output .= "<option value='$key'";
				if( $sel == $key ) $output .= " selected='selected'";
				$output .= ">$label</option>\n";
			endforeach;
			return $output;
		}
				
		function Settings(){
			$dplus = get_option( 'DarenatePlus' );
			if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
			?>
             <div class="wrap">
            	<h2><?php _e('Darenate Plus Settings', 'dplus')?></h2>
				<?php
				
				global $wpdb;
				
 				$table_name = $wpdb->prefix . "donations";
				if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
					echo '<strong>ERROR:</strong> Donations Table Does Not Exist!!</br>';
				}
 				$table_name = $wpdb->prefix . "expenses";
				if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
					echo '<strong>ERROR:</strong> Expenses Table Does Not Exist!!</br>';
				}
              	
              	?>
                <form method="post" action="">
                	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'dplus-update-options'); ?>
                    <table class="form-table">
                        <tbody>
                        	<tr valign="top">
                       			 <th scope="row"><label for="paypal_email"><?php _e('PayPal Email Address', 'dplus');?></label></th>
                        		<td><input type="text" name="paypal_email" id="paypal_email" value="<?php echo stripslashes($dplus['paypal_email']);?>" /><br>
<small><?php _e('This is the address associated with your PayPal account.','dplus');?></small></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="paypal_currency"><?php _e('PayPal Currency', 'dplus');?></label></th>
                        		<td><select name="paypal_currency" id="paypal_currency">
									<?php echo $this->CurrencySelect($dplus['paypal_currency']);?>
                                    </select></td>
                        	</tr>  
                            <tr valign="top">
                       			 <th scope="row"><label for="testing_mode"><?php _e('Testing Mode', 'dplus');?></label></th>
                        		<td><select name="testing_mode" id="testing_mode">
									<?php echo $this->TestingSelect($dplus['testing_mode']);?>
                                    </select></td>
                        	</tr> 
                            <tr valign="top">
                       			 <th scope="row"><label for="IPN_email"><?php _e('Send IPN results', 'dplus');?></label></th>
                        		<td><label><input type="checkbox" name="IPN_email" id="IPN_email" value="1" 
									<?php if($dplus['IPN_email']) echo 'checked="checked"';?> /> Enable IPN debugging results to be sent to you via email</label></td>
                        	</tr>   
                        	<tr valign="top">
                       			 <th scope="row"><label for="donate_desc"><?php _e('Donation Description', 'dplus');?></label></th>
                        		<td><input type="text" name="donate_desc" id="donate_desc" value="<?php echo stripslashes($dplus['donate_desc']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="default_value"><?php _e('Default Donation Value', 'dplus');?></label></th>
                        		<td><input type="text" name="default_value" id="default_value" value="<?php echo stripslashes($dplus['default_value']);?>" /></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="button_img"><?php _e('Button Image', 'dplus');?></label></th>
                        		<td><label><input type="radio" name="button_img" id="button_img" value="1" <?php if($dplus['button_img'] == 1) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" /></label>
                        		<label><input type="radio" name="button_img" value="2" <?php if($dplus['button_img'] == 2) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" /></label>
                        		<label><input type="radio" name="button_img" value="3" <?php if($dplus['button_img'] == 3) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" /></label>
                        		<br />
                        		<label><input type="radio" name="button_img" value="4" <?php if($dplus['button_img'] == 4) echo 'checked="checked"';?> /> <?php _e('Custom Image URL','dplus');?></label> <input type="text" name="custom_button" id="custom_button" value="<?php echo stripslashes($dplus['custom_button']);?>" /></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="subscribe"><?php _e('Enable Recurring Donations', 'dplus');?></label></th>
                        		<td><?php echo $this->RecurringArray($dplus['subscribe'], 'subscribe');?>
                        			</td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="enable_wall"><?php _e('Enable Recognition Wall', 'dplus');?></label></th>
                        		<td><input type="hidden" name="enable_wall" value="2" /><input type="checkbox" name="enable_wall" id="enable_wall" value="1" <?php if( $dplus['enable_wall'] == 1) echo 'checked="checked"';?> /></td>
                        	</tr>  
                            <tr valign="top">
                       			 <th scope="row"><label for="wall_url"><?php _e('Donation/Recognition Wall Location', 'dplus');?></label></th>
                        		<td><select name="wall_url" id="wall_url">
									<?php echo $this->PageSelect($dplus['wall_url']);?>
                                    </select><br><small><?php _e('The default location of your Donation/Recognition Page that contains the <code>[donateplus]</code> shortcode.','dplus');?></small></td>
                        	</tr> 
                        	<tr valign="top">
                       			 <th scope="row"><label for="wall_max"><?php _e('Maximum Donors on Wall', 'dplus');?></label></th>
                        		<td><input type="text" name="wall_max" id="wall_max" value="<?php echo stripslashes($dplus['wall_max']);?>" /><br><small><?php _e('Enter 0 to show all donors. <em>Pagination coming soon</em>','dplus');?></small></td>
                        	</tr>     
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_msg"><?php _e('Website Thank You Message', 'dplus');?></label></th>
                        		<td><textarea name="ty_msg" id="ty_msg" cols="40" rows="10" style="width:80%;height:150px;"><?php echo stripslashes($dplus['ty_msg']);?></textarea></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="enable_ty"><?php _e('Enable Thank You Email', 'dplus');?></label></th>
                        		<td><input type="hidden" name="enable_ty" value="2" /><input type="checkbox" name="enable_ty" id="enable_ty" value="1" <?php if( $dplus['enable_ty'] == 1) echo 'checked="checked"';?> /></td>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_name"><?php _e('Thank You From Name', 'dplus');?></label></th>
                        		<td><input type="text" name="ty_name" id="ty_name" value="<?php echo stripslashes($dplus['ty_name']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_email"><?php _e('Thank You Email Address', 'dplus');?></label></th>
                        		<td><input type="text" name="ty_email" id="ty_email" value="<?php echo stripslashes($dplus['ty_email']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_subject"><?php _e('Thank You Email Subject', 'dplus');?></label></th>
                        		<td><input type="text" name="ty_subject" id="ty_subject" value="<?php echo stripslashes($dplus['ty_subject']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_emailmsg"><?php _e('Thank You Email Message', 'dplus');?></label></th>
                        		<td><textarea name="ty_emailmsg" id="ty_emailmsg" cols="40" rows="10" style="width:80%;height:150px;"><?php echo stripslashes($dplus['ty_emailmsg']);?></textarea><br>
<strong><?php _e('Replacement Codes', 'dplus');?></strong> <small><?php _e('Email Message Only', 'dplus');?></small><br /><?php _e('<code>{donor}</code> = Donor Name<br \><code>{amount}</code> = Donation Amount<br \><code>{donorwall}</code> = Donor Wall URL','dplus');?></td>
                        	</tr>           
                         </tbody>
                     </table>
                    <p class="submit"><input name="Submit" value="<?php _e('Save Changes','dplus');?>" type="submit" />
                    <input name="action" value="dplus_update" type="hidden" /></p>
                </form>       
                 <h2><?php _e('Shortcodes', 'dplus');?></h2>
               <p><code>[donateplus]</code><br /><?php _e('This shortcode will display the Darenate Plus donation form', 'dplus'); ?></p>
               <p><code>[donorwall]</code><br /><?php _e('This shortcode will display the Donor Recognition Wall. <em>Optional attribute:</em> <code>title</code> is wrapped within a <code>&lt;h2&gt;</code> tag.  Usage is <code>[donorwall title=\'Donor Recognition Wall\']', 'dplus'); ?></p>
               <p><code>[donatetotal]</code> <br /><?php _e('This shortcode will display the total donations received. <em>Optional attributes:</em> <code>prefix</code> is the currency symbol (ie. $), <code>suffix</code> is the currency code (ie. USD), <code>type</code> is the english description (ie. U.S. Dollar). Usage is <code>[donatetotal prefix=\'1\', suffix=\'1\', type=\'0\']</code>. 1 will show, 0 will hide.', 'dplus'); ?></p>
               <p><code>[expensetotal]</code> <br /></p>
               <p><code>[fundstotal]</code> <br /></p>
               <p><code>[expensewall]</code> <br /></p>
               <p><code>[expensedate]</code> <br /></p>
               <h2><?php _e('Instant Payment Notification URL', 'dplus');?></h2>
               <p><code><?php echo str_replace(ABSPATH, trailingslashit(get_option('siteurl')), dirname(__FILE__)).'/paypal.php';?></code><br /><?php _e('This is your IPN Notification URL.  If you have issues with your site receiving your PayPal payments, be sure to manually set this URL in your PayPal Profile IPN settings.  You can also view your ', 'dplus');?> <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_display-ipns-history"><?php _e('IPN History on PayPal','dplus');?></a></p>
                <h2><?php _e('Uninstall Darenate Plus Tables and Options', 'dplus'); ?></h2>
                <form method="post" action="">
                	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'dplus-delete'); ?>  
                    <p><?php _e('<strong>WARNING:</strong> Uninstalling the Darenate Plus tables and option settings will remove all donation data related to this plugin.  This data will not be recoverable.','dplus');?></p>
                    <p class="submitdelete" style="text-align:center"><input name="Submit" value="<?php _e('Uninstall Darenate Plus','dplus');?>" type="submit" /><input name="action" value="dplus_delete" type="hidden" /></p>
                    
                   
            </div>
            <?php
		}
		
		function DonateTotal($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'prefix' => true, 'suffix' => true, 'type' => false ), $atts ) );
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'donations';
			$donors = $wpdb->get_results("SELECT amount FROM $table WHERE status='Completed'");
			$total = '';
			foreach( $donors as $donor ):
				$total = $total + $donor->amount;
			endforeach;
			if( !$total ) $total = '0';
			$thecur = $dplus['paypal_currency'];
			$symbol = $currency[$thecur]['symbol'];
			$thetype = $currency[$cur]['type'];
			if( $prefix ) $output .= $symbol;
			$output .= $total;
			if( $suffix ) $output .= ' '.$thecur;
			if( $type ) $output .= ' '.$type;
			return $output;			
		}
		
		function ExpenseTotal($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'prefix' => true, 'suffix' => true, 'type' => false ), $atts ) );
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'expenses';
			$donors = $wpdb->get_results("SELECT amount FROM $table WHERE status='Completed'");
			$total = '';
			foreach( $donors as $donor ):
				$total = $total + $donor->amount;
			endforeach;
			if( !$total ) $total = '0';
			$thecur = $dplus['paypal_currency'];
			$symbol = $currency[$thecur]['symbol'];
			$thetype = $currency[$cur]['type'];
			if( $prefix ) $output .= $symbol;
			$output .= $total;
			if( $suffix ) $output .= ' '.$thecur;
			if( $type ) $output .= ' '.$type;
			return $output;
		}

		
		function FundsTotal($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'prefix' => true, 'suffix' => true, 'type' => false ), $atts ) );
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'expenses';
			$donors = $wpdb->get_results("SELECT amount FROM $table WHERE status='Completed'");
			
			$exp_total = '';
			foreach( $donors as $donor ):
				$exp_total = $exp_total + $donor->amount;
			endforeach;
			if( !$exp_total ) $exp_total = '0';

			$table = $wpdb->prefix . 'donations';
			$donors = $wpdb->get_results("SELECT amount FROM $table WHERE status='Completed'");
			
			$don_total = '';
			foreach( $donors as $donor ):
				$don_total = $don_total + $donor->amount;
			endforeach;
			if( !$don_total ) $don_total = '0';
			
			$total = $don_total - $exp_total;
			
			$thecur = $dplus['paypal_currency'];
			$symbol = $currency[$thecur]['symbol'];
			$thetype = $currency[$cur]['type'];
			if( $prefix ) $output .= $symbol;
			$output .= $total;
			if( $suffix ) $output .= ' '.$thecur;
			if( $type ) $output .= ' '.$type;
			return $output;
		}
		
		function ExpenseDate($atts=false) {
			global $wpdb;
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'expenses';
			$expenses = $wpdb->get_results("SELECT date FROM $table WHERE status='Completed' ORDER BY ID DESC LIMIT 1");
			
			$output = '';
			foreach( $expenses as $expense ):
				$output .= date('M j, Y', strtotime($expense->date) );
			endforeach;
			return $output;
		}
						
		function DonorWall($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'title' => '' ), $atts ) );
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'donations';
			if($dplus['wall_max'] > 0)
				$limit = "ORDER BY ID DESC, display ASC, amount DESC, name ASC LIMIT ".$dplus['wall_max'];
			else
				$limit = "ORDER BY ID DESC, display ASC, amount DESC, name ASC";
			$donors = $wpdb->get_results("SELECT * FROM $table WHERE status='Completed' AND display!=0 $limit");
			//print_r($donors);
			$output .= '<div id="donorwall">';
			if( $donors && $title )
				$output .= '<h2>'.$title.'</h2>';
			foreach( $donors as $donor ):
				$symbol = $currency[$donor->currency]['symbol'];
				if($donor->display == 1) $donation = '(<span class="amount">'.$symbol.number_format($donor->amount, 2, '.', ',').' <small class="currency">'.$donor->currency.'</small></span>)';
				else $donation = '';
				
				$date = strtotime($donor->date);
				$datetime = date('M j, Y \a\t g:i a', $date);
				$output .= '<div class="donorbox"><p><small class="date time"><a href="#donor-'.$donor->ID.'">'.$datetime.'</a></small><br /><cite><strong><a href="'.$donor->url.'" rel="external" class="name url">'.$donor->name.'</a></strong> '.$donation.'</cite> '.__('Said:','dplus').'<blockquote class="comment">'.nl2br($donor->comment).'</blockquote></p></div>';
			endforeach;
			$output .= '</div>';
			return $output;
		}

		function ExpenseWall($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'title' => '' ), $atts ) );
			$dplus = get_option( 'DarenatePlus' );
			$table = $wpdb->prefix . 'expenses';
			if($dplus['wall_max'] > 0)
				$limit = "ORDER BY ID DESC, display ASC, amount DESC, name ASC LIMIT ".$dplus['wall_max'];
			else
				$limit = "ORDER BY ID DESC, display ASC, amount DESC, name ASC";
			$donors = $wpdb->get_results("SELECT * FROM $table WHERE status='Completed' AND display!=0 $limit");
			//print_r($donors);
			$output .= '<div id="expensewall">';
			if( $donors && $title )
				$output .= '<h2>'.$title.'</h2>';
			foreach( $donors as $donor ):
				$symbol = $currency[$donor->currency]['symbol'];
				if($donor->display == 1) $donation = '(<span class="amount">'.$symbol.number_format($donor->amount, 2, '.', ',').' <small class="currency">'.$donor->currency.'</small></span>)';
				else $donation = '';
				
				$date = strtotime($donor->date);
				$datetime = date('M j, Y \a\t g:i a', $date);
				$output .= '<div class="donorbox"><p><cite><strong><a href="'.$donor->url.'" rel="external" class="name url">'.$donor->name.'</a></strong> '.$donation.' - Added <span class="date time"><a href="#expense-'.$donor->ID.'">'.$datetime.'</a></span></cite>.<blockquote class="comment">'.nl2br($donor->comment).'</blockquote></p></div>';
			endforeach;
			$output .= '</div>';
			return $output;
		}
				
		function DonatePage($atts=false) {
			global $currency, $user_ID;
			get_currentuserinfo();
			$dplus = get_option( 'DarenatePlus' );
			$repeat = array('D'=>'Days', 'W'=>'Weeks', 'M'=>'Months', 'Y'=>'Years');
			if( isset($_GET['thankyou']) )
				$thankyou = $dplus['ty_msg'];
			if( $thankyou ):
				$output = '<p class="donate_ty">'.nl2br(stripslashes($thankyou)).'</p>';
			else:
			$cur = $dplus['paypal_currency'];
			$symbol = $currency[$dplus['paypal_currency']]['symbol'];
			$notify = str_replace(ABSPATH, trailingslashit(get_option('siteurl')), dirname(__FILE__)).'/paypal.php';
			$img_urlz = array( '1'=>'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif', '2'=>'https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif', '3'=>'https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif', '4'=>$dplus['custom_button']);
			$button = $img_urlz[$dplus['button_img']];
			if( $dplus['wall_url'] == 'sidebar') $wall = get_option('siteurl');
			else $wall = get_permalink($dplus['wall_url']);
			if( strpos($wall, '?') === false )
				$tyurl = $wall.'?thankyou=true';
			else
				$tyurl = $wall.'&amp;thankyou=true';
				
			$verifyurlz = array( '1' => 'https://www.paypal.com/cgi-bin/webscr', '2'=> 'http://www.belahost.com/pp/', '3'=>'https://www.sandbox.paypal.com/cgi-bin/webscr');
			
			$output = '<form id="darenateplusform" action="'.$verifyurlz[$dplus['testing_mode']].'" method="post">';

				$output .='<input type="hidden" id="cmd" name="cmd" value="_donations">
			<p class="donate_amount"><label for="amount">'.__('Donation Amount', 'dplus').':</label><br /><input type="text" name="amount" id="amount" value="'.$dplus['default_value'].'" /> <small>('.__('Currency: ','dplus').$cur.')</small></p>';

		
			if( in_array('D',$dplus['subscribe']) || in_array('W',$dplus['subscribe']) || in_array('M',$dplus['subscribe']) || in_array('Y',$dplus['subscribe']) ):
				$output .= '
<input type="hidden" name="a3" id="a3" value="" />
<p class="donate_recur"><label for="recur">Repeat Donation:</label>
<select name="t3" id="t3">';
 			if( in_array('1', $dplus['subscribe']))
 				$output .= '<option value="0">Do not repeat</option>';
 			if( in_array('D', $dplus['subscribe']))
 				$output .= '<option value="D">Daily</option>';
 			if( in_array('W', $dplus['subscribe']))
 				$output .= '<option value="W">Weekly</option>';
 			if( in_array('M', $dplus['subscribe']))
 				$output .= '<option value="M">Monthly</option>';
 			if( in_array('Y', $dplus['subscribe']))
 				$output .= '<option value="Y">Yearly</option>';
$output .= '</select> x 
<input name="p3" id="p3" value="'.$dplus['duration'].'" type="text" style="width:10px;" />
</p>';
			endif;		
			
			if( $dplus['enable_wall'] == 1 ):
				$output .= '
			<p class="recognition_wall"><label><input type="checkbox" id="recognize" name="recognize" value="1" /> '.__('Put my Donation on the Recognition Wall','dplus').'</label></p>
			<div id="wallinfo">
			<p class="show_onwall" id="wallops"><label for="show_onwall">'.__('Show on Wall', 'dplus').':</label><br /><select name="item_number">
				<option value="0:'.$user_ID.'">'.__('Do not show any information','dplus').'</option>
				<option value="1:'.$user_ID.'">'.__('Donation Amount, User Details &amp; Comments','dplus').'</option>
				<option value="2:'.$user_ID.'">'.__('User Details &amp; Comments Only','dplus').'</option>
			</select></p>
			<p class="donor_name"><label for="donor_name">'.__('Name', 'dplus').':</label><br /><input type="text" name="on0" id="donor_name" /></p>
			<p class="donor_email"><label for="donor_email">'.__('Email', 'dplus').':</label><br /><input type="text" name="os0" id="donor_email" /></p>
			<p class="donor_url"><label for="donor_url">'.__('Website', 'dplus').':</label><br /><input type="text" name="on1" id="donor_url" /></p>
			<p  class="donor_comment"><label for="donor_comment">'.__('Comments', 'dplus').':</label><br /><textarea name="os1" id="donor_comment" rows="4" cols="45" style="width:90%"></textarea><br /><span id="charinfo">'.__('Write your comment within 199 characters.','dplus').'</span> </p></div>';
			endif;

			$output .= '
<input type="hidden" name="notify_url" value="'.$notify.'">
<input type="hidden" name="item_name" value="'.$dplus['donate_desc'].'">
<input type="hidden" name="business" value="'.$dplus['paypal_email'].'">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="1">
<input type="hidden" name="return" value="'.$tyurl.'">
<input type="hidden" name="currency_code" value="'.$dplus['paypal_currency'].'">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
<p class="submit"><input type="image" src="'.$button.'" border="0" name="submit" alt="">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"></p>
</form>';
			endif;
			return $output;
		}
		
		
		function TextLimitJS(){
			global $currency, $user_ID;
			get_currentuserinfo();
			$dplus = get_option( 'DarenatePlus' );
			if( $dplus['wall_url'] == 'sidebar') $wall = get_option('siteurl');
			else $wall = get_permalink($dplus['wall_url']);
			$wall = str_replace(get_option('siteurl'), '', $wall);
			echo '<!-- WALL='.$wall.' -->';
			if( $_SERVER['REQUEST_URI'] == $wall || $dplus['wall_url'] == 'sidebar' ):
		?>
<?php /*<script type="text/javascript" src="<?php echo trailingslashit(get_option('siteurl'));?>wp-includes/js/jquery/jquery.js"></script>*/ ?>
<script type="text/javascript">
function limitChars(textid, limit, infodiv)
{
	var text = jQuery('#'+textid).val();	
	var textlength = text.length;
	if(textlength > limit)
	{
		jQuery('#' + infodiv).html('<?php _e("You cannot write more then '+limit+' characters!","dplus");?>');
		jQuery('#'+textid).val(text.substr(0,limit));
		return false;
	}
	else
	{
		jQuery('#' + infodiv).html('<?php _e("You have '+ (limit - textlength) +' characters left.","dplus");?>');
		return true;
	}
}
function displayVals() {
      var t3 = jQuery("#t3").val();
      var amount = jQuery("#amount").val();
      if(t3 != 0){
	    jQuery('#a3').val(amount);
	    jQuery('#p3').val(1);
		jQuery('#cmd').val('_xclick-subscriptions')
	  }else{
	  	jQuery('#a3').val(0);
	  	jQuery('#p3').val(0);
	  	jQuery('#cmd').val('_donations');
	  }
	  if( !t3 ) jQuery('#cmd').val('_donations');
	  
}

jQuery(function(){
 	jQuery('#donor_comment').keyup(function(){
 		limitChars('donor_comment', 199, 'charinfo');
 	})

 	jQuery("#amount").change(displayVals);
 	jQuery("#t3").change(displayVals);
 	displayVals();
 	
 	var WallOps1 = '<?php echo '<p class="show_onwall" id="wallops"><input type="hidden" name="item_number" value="0:'.$user_ID.'" /></p>';?>';
 	var WallOps2 = '<?php echo '<p class="show_onwall" id="wallops"><label for="show_onwall">'.__('Show on Wall', 'dplus').':</label><br /><select name="item_number"><option value="1:'.$user_ID.'">'.__('Donation Amount, User Details &amp; Comments','dplus').'</option><option value="2:'.$user_ID.'">'.__('User Details &amp; Comments Only','dplus').'</option></select></p>';?>';

 	if( jQuery('#recognize').is(':checked') == false){ 
 		jQuery('#wallinfo').hide();
 		jQuery("#wallops").html(WallOps1);
 	}
 	
 	jQuery("#recognize").click(function(){
 		jQuery("#wallinfo").toggle('slow');
 		if(jQuery('#wallops input').val() == '0:<?php echo $user_ID;?>'){ 
 			jQuery("#wallops").html(WallOps2);
 		}else{
 			jQuery("#wallops").html(WallOps1);
 		}
 	})
 	
		
});

</script>
        <?php	
			endif;
		}
		
		function TagReplace($in, $donor, $amount){
			
			$dplus = get_option( 'DarenatePlus' );
			
			if( $dplus['wall_url'] == 'sidebar') $wall = get_option('siteurl').'#donorwall';
			else $wall = get_permalink($dplus['wall_url']).'#donorwall';
			$out = str_replace('{donor}', $donor, $in);
			$out = str_replace('{amount}', $amount, $out);
			$out = str_replace('{donorwall}', $wall, $out);
			return $out;
		}
		
		function DarenatePlusInstall () {
   			global $wpdb, $dplus_db_version;
   			{
				$table_name = $wpdb->prefix . "donations";
				if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) :
					$sql = "CREATE TABLE $table_name  (
						  ID bigint(20) NOT NULL AUTO_INCREMENT,
						  name tinytext NOT NULL,
						  email VARCHAR(100) NOT NULL,
						  url VARCHAR(200) NOT NULL,
						  comment text NOT NULL,
						  display int(11) NOT NULL DEFAULT 0,
						  amount bigint(200) NOT NULL DEFAULT 0,
						  currency VARCHAR(200) NOT NULL,
						  date datetime DEFAULT '000-00-00 00:00:00',
						  user_id bigint(20) NOT NULL DEFAULT 0,
						  status VARCHAR(100) NOT NULL,
						  txn_id VARCHAR(100) NOT NULL,		  
						  UNIQUE KEY ID (ID)
						);";
					
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql);
					
					add_option("dplus_db_version", $dplus_db_version);
				endif;
			}
			
			{
				$table_name = $wpdb->prefix . "expenses";
				if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) :
					$sql = "CREATE TABLE $table_name  (
						  ID bigint(20) NOT NULL AUTO_INCREMENT,
						  name tinytext NOT NULL,
						  email VARCHAR(100) NOT NULL,
						  url VARCHAR(200) NOT NULL,
						  comment text NOT NULL,
						  display int(11) NOT NULL DEFAULT 0,
						  amount bigint(200) NOT NULL DEFAULT 0,
						  currency VARCHAR(200) NOT NULL,
						  date datetime DEFAULT '000-00-00 00:00:00',
						  user_id bigint(20) NOT NULL DEFAULT 0,
						  status VARCHAR(100) NOT NULL,
						  txn_id VARCHAR(100) NOT NULL,		  
						  UNIQUE KEY ID (ID)
						);";
					
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql);
					
					add_option("dplus_db_version", $dplus_db_version);
				endif;
			}
		}
		

		function UninstallDP() {
			global $wpdb;
			
			$plugin_file = 'darenate-plus/darenate-plus.php';

			$table_name = $wpdb->prefix . "donations";
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) :
				$sql = "DROP TABLE $table_name";
				$wpdb->query($sql);
			endif;
			
			$table_name = $wpdb->prefix . "expenses";
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) :
				$sql = "DROP TABLE $table_name";
				$wpdb->query($sql);
			endif;
			
			delete_option("dplus_db_version");
			delete_option("DarenatePlus");
			$deactivate = wp_nonce_url('plugins.php?action=deactivate&plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file);
			$nonce = explode('_wpnonce', $deactivate);
			$nonce = '&_wpnonce'.$nonce[1];
			$location = trailingslashit(get_option('siteurl')).'wp-admin/plugins.php?action=deactivate&plugin=' . $plugin_file. $nonce;
			header( 'Location:'.$location );
		}
	}//END Class DarenatePlus
endif;

if( class_exists('DarenatePlus') )
	$darenateplus = new DarenatePlus();
	
function DarenatePlusForm(){
	global $darenateplus;
	echo $darenateplus->DonatePage();
}
function DarenatePlusWall(){
	global $darenateplus;
	echo $darenateplus->DonorWall();
}
function DarenateExpenseWall(){
	global $darenateplus;
	echo $darenateplus->ExpenseWall();
}
function DarenatePlusTotal(){
	global $darenateplus;
	echo $darenateplus->DonateTotal();
}
function DarenatePlusTotalExpenses(){
	global $darenateplus;
	echo $darenateplus->ExpenseTotal();
}
function DarenatePlusTotalFunds(){
	global $darenateplus;
	echo $darenateplus->FundsTotal();
}
