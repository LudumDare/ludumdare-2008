<?php

	global $wpdb;

	$this->auto_clean();
	$sabre_opt = $this->get_option('sabre_opt');

	// Check the news
	$new_spams = $this->get_new_spam();
	if ($new_spams)
		$new_spams = " ($new_spams)";
	else
		$new_spams = "";

	$new_approved = $this->get_new_users();
	if ($new_approved)
		$new_approved = " ($new_approved)";
	else
		$new_approved = "";

	$new_confirmed = $this->get_new_confirm();
	if ($new_confirmed)
		$new_confirmed = " ($new_confirmed)";
	else
		$new_confirmed = "";

	$sabre_tabs = array ("option" => __("General Options", 'sabre'), "spam" => __("Blocked Registrations", 'sabre') . $new_spams, "approved" => __("Approved Registrations", 'sabre') . $new_approved, "confirm" => __("Registrations to confirm", 'sabre') . $new_confirmed, "about" => __("About", 'sabre'));

	if (isset($_REQUEST['sabre_action']) && !empty($sabre_tabs[$_REQUEST['sabre_action']]))
		$cur_tab = $_REQUEST['sabre_action'];
	else
		$cur_tab = "option";

	echo '<ul id="sabre_menu">';
	// $url = esc_url($_SERVER['PHP_SELF']) . "?page=sabre" . "&amp;sabre_action=";
	$url = "tools.php?page=sabre" . "&amp;sabre_action=";
	foreach ($sabre_tabs as $tab => $name) {
		if ($cur_tab == $tab)
			echo "<li class=\"current\">$name</li>";
		else
			echo "<li><a href=\"" . $url . $tab . "\">$name</a></li>";
	}

	echo '</ul>';

	switch ($cur_tab)
	{
			case 'spam'  :
			case 'approved' :
			case 'confirm' :

			$regmsg = '';

			if ($_REQUEST['add_regs'] && $_REQUEST['userid'])
				$regmsg = $this->add_reg_user($_REQUEST['userid']);

			if ($_REQUEST['add_regs'] && $_REQUEST['all_users'])
				$regmsg = $this->add_all_users();

			if ($_REQUEST['del_regs'] && $_REQUEST['list_sel'])
				$regmsg = $this->del_reg_user($_REQUEST['list_sel']);

			if ($_REQUEST['del_unregs']) {
				$regmsg = $this->del_unreg_user($_REQUEST['del_days']);
				if (isset($_REQUEST['del_auto'])) 
					$sabre_opt['purge_days'] = (int)$_REQUEST['del_days'];
				}

			if ($_REQUEST['confirm_regs'] && $_REQUEST['list_sel'])
				$regmsg = $this->confirm_reg_user($_REQUEST['list_sel']);

			if ($_REQUEST['unconfirm_regs'] && $_REQUEST['list_sel'])
				$regmsg = $this->unconfirm_reg_user($_REQUEST['list_sel']);

			if ($cur_tab == 'spam') {
				$where = "`status` = 'ko'";
				$sabre_opt['last_spam_check'] = current_time('timestamp', 0);
				$title = __("Invalid registration list", 'sabre');
			}
			elseif ($cur_tab == 'approved') {
				$where = "`status` = 'ok'";
				$sabre_opt['last_approved_check'] = current_time('timestamp', 0);
				$title = __("Valid registration list", 'sabre');
			}
			else {
				$where = "`status` = 'to confirm'";
				$sabre_opt['last_confirm_check'] = current_time('timestamp', 0);
				$title = __("To confirm registration list", 'sabre');
			}

			$query_limit_str = $query_limit = max(20, @$_REQUEST['rows_per_page']);
			if (@$_REQUEST['skip_rows'])
				$query_limit_str = $_REQUEST['skip_rows'] . ", " . ($_REQUEST['skip_rows'] + $query_limit);

			$query = "SELECT `id`, `user`, `email`, `user_IP`, `last_mod`, `msg`, `user_id`, `invite` FROM `" . SABRE_TABLE . "` WHERE " . $where . " ORDER BY `last_mod` DESC LIMIT " . $query_limit_str;
			$spam_rows = $wpdb->get_results($query);

			echo '<div class="wrap sabre_notopmargin">';

			if (!empty($regmsg)) {
				echo '<div id="message" class="updated fade"><p>' . $regmsg . '</p></div>';
				}

			echo '<h2>' . $title . '</h2>';

			echo '<p class="sabre_form"><form id="sabre_list_form" name="sabre_list_form" method="get" action="tools.php">';

			if (function_exists('wp_nonce_field'))
				wp_nonce_field('sabre-manage_registration');

			if ($cur_tab == 'approved') {
				echo '<h3>' . __("Manual registration", 'sabre') . '</h3>';
				echo '<div class="tablenav">';
				echo '<input type="submit" name="add_regs" id="add_regs" value="' . __("Add", 'sabre') . '" class="button-secondary" />';
				echo '<input type="text" id="userid" name="userid" size="25"/> ' . __("Enter WordPress user name or check the box to add all existing WordPress users ", 'sabre');
				echo '<input type="checkbox" name="all_users" id="all_users" value="yes" />';
				echo '</div>';

				echo '<h3>' . __("Manual deregistration", 'sabre') . '</h3>';
				echo '<div class="tablenav">';
				echo '<input type="submit" name="del_regs" id="del_regs" value="' . __("Unregister", 'sabre') . '" class="button-secondary" />' . __("Unregister selected users", 'sabre');
				echo '</div>';
			}

			if ($cur_tab == 'spam') {
				echo '<h3>' . __("Purge log", 'sabre') . '</h3>';
				echo '<div class="tablenav">';
				echo '<input type="submit" name="del_unregs" id="del_unregs" value="' . __("Delete", 'sabre') . '" class="button-secondary" /> ';
				echo __("Log older than", 'sabre');
				echo ' <input type="text" value="' . $sabre_opt['purge_days'] . '" id="del_days" name="del_days" size="3"/> ' . __("days", 'sabre');
				echo ' (' . __('And do it automatically now', 'sabre') . '<input type="checkbox" name="del_auto" id="del_auto" value="yes" checked />)';
				echo '</div>';
			}

			if ($cur_tab == 'confirm') {
				echo '<h3>' . __("Warning!", 'sabre') . '</h3>';
				echo '<div class="tablenav">';
				if ($sabre_opt['enable_confirm'] == 'user')
					_e("You set Sabre's behaviour to ask for user's confirmation of his registration. Normally, you don't have to intervene here but you can confirm or refuse it manually if needed.", 'sabre');
				elseif ($sabre_opt['enable_confirm'] == 'admin')
					_e("You set Sabre's behaviour to ask for your confirmation of user's registration. You have to confirm or refuse it manually to complete the process.", 'sabre');
				else _e("You set Sabre's behaviour to register the user without confirmation. This screen will always be empty.", 'sabre');
				echo '</div>';

				if ($sabre_opt['enable_confirm'] <> 'none') {	
					echo '<h3>' . __("Confirm registration", 'sabre') . '</h3>';
					echo '<div class="tablenav">';
					echo '<input type="submit" name="confirm_regs" id="confirm_regs" value="' . __("Confirm", 'sabre') . '" class="button-secondary" />' . __("Validate selected registrations", 'sabre');
					echo '</div>';

					echo '<h3>' . __("Refuse registration", 'sabre') . '</h3>';
					echo '<div class="tablenav">';
					echo '<input type="submit" name="unconfirm_regs" id="unconfirm_regs" value="' . __("Refuse", 'sabre') . '" class="button-secondary" />' . __("Refuse selected registrations", 'sabre');
					echo '</div>';
					}
			}

			echo '<h3>' . __("Browse", 'sabre') . '</h3>';
			echo '<div class="tablenav">';
			echo '<input type="hidden" name="page" id="page" value="sabre" />';
			echo '<input type="hidden" name="sabre_action" id="sabre_action" value="' . $cur_tab . '" />';
			echo '<input type="submit" name="display_regs" id="display_regs" value="' . __("Display", 'sabre') . '" class="button-secondary" />';
			echo '<input type="text" id="rows_per_page" name="rows_per_page" value="' . $query_limit . '" size="3"/> ' . __("lines per page, skipping first: ", 'sabre');
			echo '<input type="text" id="skip_rows" name="skip_rows" value="' . intval(@$_REQUEST['skip_rows']) . '" size="3" />';
			echo '</div>';

			echo '<br class="clear" />';

			echo '<table id="sabre_spam_list" class="widefat">';
			echo '<thead><tr><th><strong>' . __('Id', 'sabre') . '</strong></th><th><strong>' . __('User name', 'sabre') . '</strong></th><th><strong>' . __('User E-mail', 'sabre') . '</strong></th><th><strong>' . __('User IP', 'sabre')  . '</strong></th><th>' . __('When', 'sabre') . '</strong></th>' . ($cur_tab == 'spam' ? '' : '<th><strong>' . __('Invitation', 'sabre') . '</strong></th>') . '<th><strong>' . ($cur_tab == 'spam' ? __('Errors', 'sabre') : __('User ID', 'sabre')) . '</strong></th></tr></thead><tbody>';

			if (is_array($spam_rows)) {
				$rowid = 0;
				foreach ($spam_rows as $row) {
					$rowid += 1;
					echo '<tr valign="middle"' . ($rowid % 2 ? '>' : ' class="alternate">');
					if ($cur_tab == 'spam')
						echo '<td>' . $row->id . '</td>';
					else
						echo '<th scope="row" class="check-column"><input type="checkbox" name="list_sel[' . $row->id . ']" id="list_sel[' . $row->id . ']" value="' . $row->id . '" />' . $row->id . '</th>';
					echo '<td>';
					echo $row->user;
					echo '</td><td>';
					echo $row->email;
					echo '</td><td>';
					echo $row->user_IP;
					echo '</td><td>';
					echo $row->last_mod;
					echo '</td><td>';
					if ($cur_tab == 'spam') {
						$msgs= "";
						if (!empty($row->msg)) {
							$msgs_array = unserialize($row->msg);
							if (is_array($msgs_array))
								foreach ($msgs_array as $msg_type => $msg) {
									$msgs .= $msg;
									$msgs .= '<br />';
								}
						}
						echo $msgs;
					}
					else {
						echo $row->invite;
						echo '</td><td>';
						if ($row->user_id > 0)
							echo '<a href="' . get_bloginfo('wpurl') . '/wp-admin/user-edit.php?user_id=' . $row->user_id . '" >' . $row->user_id . '</a>';
					}
					echo '</td></tr>';
				}
			}

			echo '</tbody></table>';
			echo '</form></p>';
			echo '</div>';

			$this->update_option('sabre_opt', $sabre_opt);

			break;

		case 'logs' :

			echo '<div class="wrap sabre_notopmargin">';
			echo '<h2>' . __("Events history", 'sabre') . '</h2>';

			echo '</div>';

			break;

		case 'about' :

			require_once(SABREPATH . "/classes/sabre_class_about.php");

			break;


		case 'option':
		default:

			$total_pending = $wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status` = 'to confirm'");

			echo '<div class="wrap sabre_notopmargin">';

			if ($this->save_options($_POST))
				echo '<div id="message" class="updated fade"><p>' . __('Options successfully saved.', 'sabre') . '</p></div>';
			if (!empty($_POST['active_option'])) 
				$active_option = sanitize_text_field($_POST['active_option']);
			else
				$active_option = 'captcha_table';

			echo '<h2>' . __("Quick figures", 'sabre') . '</h2>';
			echo '<ul>';
			echo '<li>' . __('Total number of registrations stopped:', 'sabre') . '  <strong>' . (int)$sabre_opt['total_stopped'] . '</strong></li>';
			echo '<li>' . __('Total number of registrations accepted:', 'sabre') . '  <strong>' . (int)$sabre_opt['total_accepted'] . '</strong></li>';
			echo '<li>' . __('Number of pending confirmation:', 'sabre') . '  <strong>' . (int)$total_pending . '</strong></li>';
			echo '</ul>';
			echo '</div>';

			$sabre_opt = $this->get_option('sabre_opt');
			if (is_array($sabre_opt))
				extract ($sabre_opt, EXTR_OVERWRITE) ;

			echo '<div class="wrap sabre_notopmargin">';
			echo '<h2>' . __("General Options", 'sabre') . '</h2>';
			
			echo '<form id="sabre_option_form" name="sabre_option_form" method="post" action="tools.php?page=sabre&sabre_action=option">';

			if (function_exists('wp_nonce_field'))
				wp_nonce_field('sabre-manage_option');

			echo '<input type="hidden" name="active_option" id="active_option" value="' . $active_option . '" />';
			echo '<div id="sabre_opt_accordion">';
			echo '<h3><a href="#">' . __("Captcha options", 'sabre') . '</a></h3>';
			echo '<table id="captcha_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_captcha">' . __('Enable captcha test:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_captcha, 'true') . ' name="sabre_enable_captcha" id="sabre_enable_captcha" value="true" /></td><td>' . __('(Turn captcha generation on/off)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_white_bg">' . __('Use white background:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($white_bg, 'true') . ' name="sabre_white_bg" id="sabre_white_bg" value="true" /></td><td>' . __('(Captcha is displayed on a white background. Black background if not checked)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_acceptedChars">' . __('Accepted characters:', 'sabre') . '</label></th><td><input type="text" size="50" name="sabre_acceptedChars" id="sabre_acceptedChars" value="' . $acceptedChars . '" /></td><td>' . __('(List of valid characters for code generation)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_stringlength">' . __('String length:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_stringlength" id="sabre_stringlength" value="' . $stringlength . '" /></td><td>' . __('(Number of characters for code)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_contrast">' . __('Contrast:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_contrast" id="sabre_contrast" value="' . $contrast . '" /></td><td>' . __('(The higher the number, the better the contrast)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_num_polygons">' . __('Number of polygons:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_num_polygons" id="sabre_num_polygons" value="' . $num_polygons . '" /></td><td>' . __('(Number of triangles to draw.  0 = none)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_num_ellipses">' . __('Number of ellipses:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_num_ellipses" id="sabre_num_ellipses" value="' . $num_ellipses . '" /></td><td>' . __('(Number of ellipses to draw.  0 = none)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_num_lines">' . __('Number of lines:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_num_lines" id="sabre_num_lines" value="' . $num_lines . '" /></td><td>' . __('(Number of lines to draw.  0 = none)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_num_dots">' . __('Number of dots:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_num_dots" id="sabre_num_dots" value="' . $num_dots . '" /></td><td>' . __('(Number of dots to draw.  0 = none)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_min_thickness">' . __('Min. thickness:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_min_thickness" id="sabre_min_thickness" value="' . $min_thickness . '" /></td><td>' . __('(Minimum thickness of lines in pixels)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_max_thickness">' . __('Max. thickness:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_max_thickness" id="sabre_max_thickness" value="' . $max_thickness . '" /></td><td>' . __('(Maximum thickness of lines in pixels)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_min_radius">' . __('Min. radius:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_min_radius" id="sabre_min_radius" value="' . $min_radius . '" /></td><td>' . __('(Minimum radius of ellipses)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_max_radius">' . __('Max. radius:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_max_radius" id="sabre_max_radius" value="' . $max_radius . '" /></td><td>' . __('(Maximum radius of ellipses)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_object_alpha">' . __('Object alpha:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_object_alpha" id="sabre_object_alpha" value="' . $object_alpha . '" /></td><td>' . __('(How opaque should the obscuring objects be. 0 is opaque, 127 is transparent)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			echo '<h3><a href="#">' . __("Math options", 'sabre') . '</a></h3>';
			echo '<table id="math_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_math">' . __('Enable math test:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_math, 'true') . ' name="sabre_enable_math" id="sabre_enable_math" value="true" /></td><td>' . __('(Turn math control on/off)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_math_ops">' . __('Accepted operations:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_math_ops" id="sabre_math_ops" value="' . $math_ops . '" /></td><td>' . __('(List of valid operations for math test)', 'sabre') . '</td></tr>';
			echo '</table>';

			echo '<h3><a href="#">' . __("Text captcha options", 'sabre') . '</a></h3>';
			echo '<table id="text_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_text">' . __('Enable text test:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_text, 'true') . ' name="sabre_enable_text" id="sabre_enable_text" value="true" /></td><td>' . __('(Turn text challenge on/off - The user will be asked to type the nth letter of a random word)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			echo '<h3><a href="#">' . __("Sequence of tests", 'sabre') . '</a></h3>';
			echo '<table id="seq_table" class="form-table">';
			echo '<tr><th><label for="sabre_test_seq">' . __('Tests to perform:', 'sabre') . '</label></th><td><select size="1" name="sabre_test_seq" id="sabre_test_seq">';
			echo '<option value="All" ' . $this->selected($sabre_seq, 'All') . ' >' . __('All', 'sabre') . '</option>';
			echo '<option value="Random" ' . $this->selected($sabre_seq, 'Random') . ' >' . __('Randomly', 'sabre') . '</option>';
			echo '</select></td><td>' . __('(How do we use the above tests?)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			echo '<h3><a href="#">' . __("Stealth options", 'sabre') . '</a></h3>';
			echo '<table id="stealth_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_stealth">' . __('Enable stealth test:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_stealth, 'true') . ' name="sabre_enable_stealth" id="sabre_enable_stealth" value="true" /></td><td>' . __('(Turn silent control on/off)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_enable_js">' . __('Block if Javascript unsupported:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($js_support, 'true') . ' name="sabre_enable_js" id="sabre_enable_js" value="true" /></td><td>' . __('(Consider that a lack of Javascript capabilities is a spambot\'s footprint)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_timeout">' . __('Session time out:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_timeout" id="sabre_timeout" value="' . $session_timeout . '" /></td><td>' . __('(Max. time in seconds before session expires)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_speed">' . __('Speed limit:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_speed" id="sabre_speed" value="' . $speed_limit . '" /></td><td>' . __('(Min. time in seconds to fill the registration form)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_banned_IP">' . __('Check DNS Blacklists:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($check_banned_IP, 'true') . ' name="sabre_banned_IP" id="sabre_banned_IP" value="true" /></td><td>' . __('(Control if user IP address is blacklisted on DNSBL servers)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			if (!is_multisite()) { // Use built-in confirmation in multisite mode
			echo '<h3><a href="#">' . __("Confirmation options", 'sabre') . '</a></h3>';
			echo '<table id="confirmation_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_confirm">' . __('Enable confirmation:', 'sabre') . '</label></th><td><select size="1" name="sabre_enable_confirm" id="sabre_enable_confirm">';
			echo '<option value="none" ' . $this->selected($enable_confirm, 'none') . ' >' . __('None', 'sabre') . '</option>';
			echo '<option value="user" ' . $this->selected($enable_confirm, 'user') . ' >' . __('By user', 'sabre') . '</option>';
			echo '<option value="admin" ' . $this->selected($enable_confirm, 'admin') . ' >' . __('By admin', 'sabre') . '</option>';
			echo '</select></td><td>' . __('(Ask user or site\'s admin to confirm registration)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_confirm_period">' . __('Number of days:', 'sabre') . '</label></th><td><input type="text" size="2" name="sabre_confirm_period" id="sabre_confirm_period" value="' . (1 > (int)$period ? 3 : (int)$period) . '" /></td><td>' . __('(Period of time for the user to confirm his registration)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_no_entry">' . __('Deny early sign-in:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($no_entry, 'true') . ' name="sabre_no_entry" id="sabre_no_entry" value="true" /></td><td>' . __("(Don't allow users to sign in before their confirmation)", 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_mail_confirm">' . __('Send mail when confirmed:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($mail_confirm, 'true') . ' name="sabre_mail_confirm" id="sabre_mail_confirm" value="true" /></td><td>' . __("(Send a mail to the administrator upon user's confirmation)", 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_delete_user">' . __('Suppress unregistered users:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($delete_user, 'true') . ' name="sabre_delete_user" id="sabre_delete_user" value="true" /></td><td>' . __("(Delete user account when registration is canceled)", 'sabre') . '</td></tr>';
			echo '</table>';
			}
			
			echo '<h3><a href="#">' . __("Policy options", 'sabre') . '</a></h3>';
			echo '<table id="policy_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_policy">' . __('Enable policy agreement:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_policy, 'true') . ' name="sabre_enable_policy" id="sabre_enable_policy" value="true" /></td><td>' . __('(Ask for user\'s acceptance of site\'s policy)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_policy_name">' . __('Policy name:', 'sabre') . '</label></th><td><input type="text" size="20" name="sabre_policy_name" id="sabre_policy_name" value="' . stripslashes($policy_name) . '" /></td><td>' . __('(Name of agreement like Disclaimer, Licence agreement, General policy, etc...Will be shown on the registration form)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_policy_link">' . __('Policy link:', 'sabre') . '</label></th><td><input type="text" size="20" name="sabre_policy_link" id="sabre_policy_link" value="' . $policy_link . '" /></td><td>' . __('(URL of the policy text. If not blank, the name of the agreement above will turn into a link to that URL on the registration form. Can be a WordPress page or an external html file.)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_policy_text">' . __('Policy text:', 'sabre') . '</label></th><td><textarea rows="10" cols="40" name="sabre_policy_text" id="sabre_policy_text" >' . stripslashes($policy_text) . '</textarea></td><td>' . __('(Enter the text to be displayed on the registration form)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			echo '<h3><a href="#">' . __("Invitation options", 'sabre') . '</a></h3>';
			echo '<table id="invitation_table" class="form-table">';
			echo '<tr><th><label for="sabre_enable_invite">' . __('Enable invitation:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($enable_invite, 'true') . ' name="sabre_enable_invite" id="sabre_enable_invite" value="true" /></td><td>' . __('(User must provide an invitation code to register)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_invite_codes">' . __('Invitation codes:', 'sabre') . '</label></th><td><ul><li><input type="text" size="60" value="" class="unseen" disabled /></li><li><input type="text" size="15" name="text_invite_code" value="' . __('Code', 'sabre') . '" class="unseen" disabled /><input type="text" size="6" name="text_invite_number" value="' . __('Usage', 'sabre') . '" class="unseen" disabled /><input type="text" size="10" name="text_invite_date" value="' . __('Validity', 'sabre') . '" class="unseen" disabled /></li>' . $this->display_invite_codes() . '</ul></td><td>' . __('(List of valid invitation codes - Usage is the number of time the code can be used - Validity is the expiration date in YYYY-MM-DD format)', 'sabre') . '</td></tr>';
			echo '</table>';
			
			echo '<h3><a href="#">' . __("Miscellaneous options", 'sabre') . '</a></h3>';
			echo '<table id="misc_table" class="form-table">';
			if (!is_multisite()) { // Use built-in confirmation in multisite mode
			echo '<tr><th><label for="sabre_user_pwd">' . __('User\'s password:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($user_pwd, 'true') . ' name="sabre_user_pwd" id="sabre_user_pwd" value="true" /></td><td>' . __('(User can choose his own password during registration)', 'sabre') . '</td></tr>';
			}
			echo '<tr><th><label for="sabre_mail_from_name">' . __('Sender\'s name:', 'sabre') . '</label></th><td><input type="text" size="50" name="sabre_mail_from_name" id="sabre_mail_from_name" value="' . $mail_from_name . '" /></td><td>' . __('(Name used in mails sent by Sabre - Blog\'s name used if left blank)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_mail_from_mail">' . __('Sender\'s email:', 'sabre') . '</label></th><td><input type="text" size="50" name="sabre_mail_from_mail" id="sabre_mail_from_mail" value="' . $mail_from_mail . '" /></td><td>' . __('(Mail address in mails sent by Sabre - Administrator\'s mail used if left blank)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_show_banner">' . __('Show banner:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($show_banner, 'true') . ' name="sabre_show_banner" id="sabre_show_banner" value="true" /></td><td>' . __('(Mention Sabre at the bottom of the registration form)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_show_dashboard">' . __('Show on dashboard:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($show_dashboard, 'true') . ' name="sabre_show_dashboard" id="sabre_show_dashboard" value="true" /></td><td>' . __('(Include Sabre statistics on the dashboard)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_show_user">' . __('Show in profile:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($show_user, 'true') . ' name="sabre_show_user" id="sabre_show_user" value="true" /></td><td>' . __('(Include Sabre informations in the user profile)', 'sabre') . '</td></tr>';
			echo '<tr><th><label for="sabre_suppress_sabre">' . __('Suppress Sabre:', 'sabre') . '</label></th><td><input type="checkbox" ' . $this->checked($suppress_sabre, 'true') . ' name="sabre_suppress_sabre" id="sabre_suppress_sabre" value="true" /></td><td>' . __('(Delete all Sabre stuff when deactivating the plugin - Use with caution!)', 'sabre') . '</td></tr>';
			echo '</table>';

			echo '<h3><a href="#"></a></h3>';
			echo '<table class="form-table">';
			echo '</table>';

			echo '</div>'; // End of id="sabre_opt_accordion"

			echo '<p class="submit">';
			echo '<input type="submit" name="sabre_option_save" value="'. __("Save options", 'sabre') . '" class="button" />';
			echo '</p>';
			echo '</form>';

			echo '</div>';
			break;
	}
?>