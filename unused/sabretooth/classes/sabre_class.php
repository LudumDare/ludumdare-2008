<?php

include(SABREPATH.'classes/wordgen_class.php');

class Sabre {

var $DB_VERSION = 7;

var $VERSION = '1.2.2';

var $custom_logo;

/***********************************************************************/
/* Initialization                                                      */
/***********************************************************************/

function init() {

	load_plugin_textdomain('sabre', false, SABREDIR . '/languages');

}

/***********************************************************************/
/* Add Sabre page in the Admin/Manage menu                             */
/***********************************************************************/

function options() {

	if ((!is_multisite() && current_user_can( 'manage_options' )) || (is_multisite() && is_super_admin()))
	add_management_page(__('Sabre Options', 'sabre'), 'Sabre', 'administrator', "sabre", array(&$this, 'option_page'));
}

/***********************************************************************/
/* Handling of Sabre in the Admin/Manage menu                          */
/***********************************************************************/

function option_page() {

	require_once(SABREPATH . 'classes/sabre_class_admin.php');

}

/**********************************************************************/
/* Get Sabre options                                                  */
/**********************************************************************/

function get_option($key, $default=false) {

	if (is_multisite())
		return get_site_option($key, $default);
	else
		return get_option($key, $default);
}

/**********************************************************************/
/* Update Sabre options                                               */
/**********************************************************************/

function update_option($key, $value) {

	if (is_multisite())
		return update_site_option($key, $value);
	else
		return update_option($key, $value);
}

/**********************************************************************/
/* Delete Sabre options                                               */
/**********************************************************************/

function delete_option($key) {

	if (is_multisite())
		return delete_site_option($key);
	else
		return delete_option($key);
}

/***********************************************************************/
/* Add tests to registration form                                      */
/***********************************************************************/

function change_registration_form($errors='') {
	global $wpdb;

	if ( !is_wp_error($errors) )
		$errors = new WP_Error();

	$sabre_opt = $this->get_option('sabre_opt');
	if (is_array($sabre_opt))
		extract ($sabre_opt, EXTR_OVERWRITE) ;


	// Choose the sequence of tests
	if ($sabre_seq == 'Random') {
		$arr1 = array();
		$arr2 = array(	'enable_captcha' => 'false',
				'enable_math' => 'false',
				'enable_text' => 'false' );
		if ($enable_captcha == 'true')
			$arr1[] = 'enable_captcha';
		if ($enable_math == 'true')
			$arr1[] = 'enable_math';
		if ($enable_text == 'true')
			$arr1[] = 'enable_text';
		$dice = mt_rand(0,(count($arr1) - 1));
		$arr2[$arr1[$dice]] = 'true';
		extract ($arr2, EXTR_OVERWRITE);
	}

	if ($enable_captcha == 'true') {
		$max = strlen($acceptedChars)-1;
		for($i=0; $i < $stringlength; $i++)
			$password .= $acceptedChars{mt_rand(0, $max)};
	}
	if ($enable_math == 'true') {
		$max = strlen($math_ops)-1;
		$mathop = $math_ops{mt_rand(0, $max)};
		$nb1 = mt_rand(1, 20);
		$nb2 = mt_rand(1, 20);
		switch ($mathop) {
			case '-' :
				if ($nb1 > $nb2) {
					$mathcode = $nb1 - $nb2;
					$mathstring = $nb1 . ' - ' . $nb2;
					}
				else {
					$mathcode = $nb2 - $nb1;
					$mathstring = $nb2 . ' - ' . $nb1;
					}
				break;
			case '*' :
				$mathcode = $nb1 * $nb2;
				$mathstring = $nb1 . ' * ' . $nb2;
				break;
			case '+' :
			default  :
				$mathcode = $nb1 + $nb2;
				$mathstring = $nb1 . ' + ' . $nb2;
				break;
			}
	}
	if ($enable_text == 'true') {
		$ord = array('first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'last');
		$myWord = new wordGenerator();
		$t_word = strtoupper($myWord->create(5,10,true));
		$t_index = mt_rand(0, strlen($t_word)-1);
		$t_letter = $t_word[$t_index];
		$t_ord = __($ord[($t_index==strlen($t_word)-1 ? 9 : $t_index)], 'sabre');
		unset($myWord);
		unset($ord);
	}

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	@$wpdb->query("INSERT INTO `" . SABRE_TABLE . "` SET `user_IP` = '" . $_SERVER['REMOTE_ADDR'] . "', `first_mod` = '" . $stordate . "', `last_mod` = '" . $stordate . "'" . (isset($password) ? ", `captcha` = '" . $password . "'" : "") . (isset($mathcode) ? ", `math` = " . $mathcode : "") . (isset($t_letter) ? ", `letter` = '" . $t_letter . "'" : ""));
	$id = $wpdb->insert_id;

	if ($user_pwd == 'true') {
		echo '<p id="sabre_pwd">';
		echo '<label>' . __('Please enter your password twice:', 'sabre') . '<br />';
		if ( $errmsg = $errors->get_error_message('sabre_no_user_pwd') )
			echo '<p class="error">' .$errmsg .'</p>';
		if ( $errmsg = $errors->get_error_message('sabre_mismatch_user_pwd') )
			echo '<p class="error">' .$errmsg .'</p>';
		if ( $errmsg = $errors->get_error_message('sabre_short_user_pwd') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="password" name="user_pwd1" id="user_pwd1" class="input" value="" size="20" tabindex="25" /></label>';
		echo '<input type="password" name="user_pwd2" id="user_pwd2" class="input" value="" size="20" tabindex="26" /></label><br />';
		echo '<strong>' . __('Strength indicator', 'sabre') . '</strong>';
		echo '<label id="pass-strength-result">' . __('Too short', 'sabre') . '</label><br />' . __('Hint: Your password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'sabre');
		echo '</p>';
	}

	if ($enable_captcha == 'true') {
		$sabre_id = $sabre_opt;
		$sabre_id['captcha'] = $password;
		echo '<p>';
		echo '<img src="' . SABREURL . 'sabre_captcha.php?sabre_id=' . base64_encode($this->Encrypt_Decrypt($password)) . '&acceptedChars=' . $acceptedChars . '&stringlength=' . $stringlength . '&contrast=' . $contrast . '&num_polygons=' . $num_polygons . '&num_ellipses=' . $num_ellipses . '&num_lines=' . $num_lines . '&num_dots=' . $num_dots . '&min_thickness=' . $min_thickness . '&max_thickness=' . $max_thickness . '&min_radius=' . $min_radius . '&max_radius=' . $max_radius . '&object_alpha=' . $object_alpha . '&white_bg=' . $white_bg . '" border="0" alt="captcha image" /><br />';
		echo '<label>' . __('Please enter the code shown above:', 'sabre') . '<br />';
		if ( $errmsg = $errors->get_error_message('sabre_captcha') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="text" name="captcha" id="captcha" class="input" value="" size="25" tabindex="30" /></label>';
		echo '</p>';
	}
	if ($enable_math == 'true') {
		echo '<p>';
		echo '<label>' . sprintf(__('Please enter the result: %s', 'sabre'), $mathstring) . '<br />';
		if ( $errmsg = $errors->get_error_message('sabre_math') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="text" name="math" id="math" class="input" value="" size="10" tabindex="40" /></label>';
		echo '</p>';
	}
	if ($enable_text == 'true') {
		echo '<p>';
		echo '<label>' . sprintf(__('Please enter the %s letter of the word %s', 'sabre'), $t_ord, $t_word) . '<br />';
		if ( $errmsg = $errors->get_error_message('sabre_text') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="text" name="letter" id="letter" class="input" value="" size="2" tabindex="42" /></label>';
		echo '</p>';
	}
	if ($enable_policy == 'true') {
		echo '<p><label>';
		if (empty($policy_link))
			echo htmlentities(stripslashes($policy_name), ENT_QUOTES, 'UTF-8') . '<br />';
		else
			echo '<a href="' . $policy_link . '" target="_blank">' . htmlentities(stripslashes($policy_name), ENT_QUOTES, 'UTF-8') . '</a><br />';
		if (!empty($policy_text))
			echo '<textarea rows="10" cols="25" readonly>' . htmlentities(stripslashes($policy_text), ENT_QUOTES, 'UTF-8') . '</textarea><br />';
		if ( $errmsg = $errors->get_error_message('sabre_no_policy') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="checkbox" name="policy" id="policy" value="yes" tabindex="43" />';
		_e('I agree', 'sabre');
		echo '</label></p>';
	}
	if ($enable_invite == 'true') {
		echo '<p>';
		echo '<label>' . __('Please enter our hashtag:', 'sabre') . '<br />';
		if ( $errmsg = $errors->get_error_message('sabre_invitation') )
			echo '<p class="error">' .$errmsg .'</p>';
		echo '<input type="text" name="invite_code" id="invite_code" class="input" value="" size="20" tabindex="45" /></label>';
		echo '</p>';
	}

	// Add anti-bot fields
	if ($enable_stealth == 'true') {
		// Verify fake user
		$fakeField = "X" . md5($id . $magic_seed . $_SERVER['REMOTE_ADDR'] . $curdate);
		echo '<p id="sabre_spectre">';
		echo '<label>' . __("Please don't modify this field:", 'sabre') . '<br />';
		echo "<input type=\"text\" name=\"$fakeField\" id=\"$fakeField\" class=\"input\" value=\"\" size=\"10\" tabindex=\"50\" /></label>";
		echo '</p>';

		// Verify Javascript capabilities
		$max = rand(5, 9);
		$tot = $str = 1;

		for ($i = 0; $i < $max; $i++) {
			$op = rand(0, 8);
			$num = rand(1, 42);

			switch ($op) {
				case 0:
				case 8:
					$str = "(" . $str . " + " . $num . ")";
					$tot += $num;
				break;
				case 1:
					$str = "(" . $str . " - " . $num . ")";
					$tot -= $num;
				break;
				case 2:
					$str = "(" . $str . " * " . $num . ")";
					$tot *= $num;
				break;
				case 3:
					$str = "Math.round ( Math.abs(" . $str . " / " . $num . "))";
					$tot = round(abs($tot / $num));
				break;
				case 4:
					$str = "Math.min(" . $str . ", " . $num . ")";
					$tot = min($tot, $num);
				break;
				case 5:
					$str = "Math.max(" . $str . ", " . $num . ")";
					$tot = max($tot, $num);
				break;
				case 6:
					$str = "Math.round ( Math.abs(" . $str . " % " . $num . "))";
					$tot = round(abs($tot % $num));
				break;
				case 7:
					$str = "(" . $str . " + Math.round( Math.abs(100*Math.sin(" . $num . ")) ) )";
					$tot = $tot + round(abs(100*sin($num)));
				break;
				}
			}

		$js_command = "Math.round ( Math.abs(" . $str . "))" ;
		$tot = round(abs($tot));

		$check1 = $this->magic_seed(10);
		$check2 = md5($tot . $check1 . $magic_seed);

		echo '<input type="hidden" id="sabre_js_check1" name="sabre_js_check1" value="' . $check1 . '" />';
		echo '<input type="hidden" id="sabre_js_check2" name="sabre_js_check2" value="' . $check2 . '" />';
		echo "\n<script type=\"text/javascript\">";
		echo "\n<!--";
		echo "\ndocument.write('<input type=\"hidden\" id=\"sabre_js_payload\" name=\"sabre_js_payload\" value=\"');";
		echo "\ndocument.write($js_command);";
		echo "\ndocument.write('\" />');";
		echo "\n-->";
		echo "\n</script>";
	}

	echo '<input type="hidden" name="sabre_id" id="sabre_id" class="input" value="' . $id . '" />';

	if ( $errmsg = $errors->get_error_message('sabre_generic') )
			echo '<p class="error">' .$errmsg .'</p>';

	// Add Sabre banner
	if ($show_banner == 'true')
		echo '<p id="sabre_banner">' . __('Protected by', 'sabre') . ' <a id="sabre_link" href="http://wordpress.org/extend/plugins/sabre">Sabre</a>.' . ($total_stopped ? sprintf(__ngettext(' %s alien stopped.', ' %s aliens stopped.', $total_stopped, 'sabre'), $total_stopped) : '') . '</p>';
}


/***********************************************************************/
/* Test the info entered by the user                                   */
/***********************************************************************/

function check_entry($stuff) {
	global $wpdb;

	if (!is_multisite())
		$errors = $stuff;
	else
		extract($stuff);

	// To speed up things, check the user entry only if no error was detected before calling Sabre

if ( !$errors->get_error_code()) {

	$sabre_opt = $this->get_option('sabre_opt');
	if (is_array($sabre_opt))
		extract ($sabre_opt, EXTR_OVERWRITE) ;

	$sabre_errors = array();
	$error_head_text = __('<strong>ERROR</strong>: ', 'sabre');

	$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . SABRE_TABLE . "` WHERE `id` = %d", (int)$_POST['sabre_id']));

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	if (empty($result->id)) {
		$error_msg_text = __('Unknown session.', 'sabre');
		$errors->add('sabre_session', $error_head_text . $error_msg_text);
		$sabre_errors['sabre_session'] = $error_msg_text;
		}

	if ($result->status != 'pending') {
		$error_msg_text = __('Invalid session status.', 'sabre');
		$errors->add('sabre_session_status', $error_head_text . $error_msg_text);
		$sabre_errors['sabre_session_status'] = $error_msg_text;
		}

	if ($enable_stealth == 'true') {
		// Check user IP
		if ($result->user_IP <> $_SERVER['REMOTE_ADDR']) {
			$error_msg_text = __('Invalid IP address.', 'sabre');
			$errors->add('sabre_IP', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_IP'] = $error_msg_text;
			}
		// Check banned IP
		if ($check_banned_IP == 'true') {
			$error_msg_text = __('Banned IP address.', 'sabre');
			$spammer_IP = $_SERVER['REMOTE_ADDR'];
			$reverse_IP = array_reverse(explode('.', $spammer_IP));
			$checked_domaine = implode('.', $reverse_IP) . '.' . 'zen.spamhaus.org';
			if ($checked_domaine != gethostbyname($checked_domaine)) {
				$errors->add('sabre_banIP', $error_head_text . $error_msg_text);
				$sabre_errors['sabre_banIP'] = $error_msg_text;
				}
			$checked_domaine = implode('.', $reverse_IP) . '.' . 'l1.spews.dnsbl.sorbs.net';
			if ($checked_domaine != gethostbyname($checked_domaine)) {
				$errors->add('sabre_banIP', $error_head_text . $error_msg_text);
				$sabre_errors['sabre_banIP'] = $error_msg_text;
				}
			}
		// Check session time out
		if (strtotime($result->last_mod) + (int)$session_timeout < $curdate) {
			$error_msg_text = __('Session timed out.', 'sabre');
			$errors->add('sabre_timeout', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_timeout'] = $error_msg_text;
			}
		// Check excessive speed
		if (strtotime($result->last_mod) + (int)$speed_limit > $curdate) {
			$error_msg_text = __('Speedy Gonzales was here.', 'sabre');
			$errors->add('sabre_speed', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_speed'] = $error_msg_text;
			}
		// Check false fields
		$fakeField = "X" . md5($result->id . $magic_seed . $result->user_IP . strtotime($result->last_mod));
		if (!isset($_POST[$fakeField]) || !empty($_POST[$fakeField])) {
			$error_msg_text = __('Fake user.', 'sabre');
			$errors->add('sabre_fake_user', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_fake_user'] = $error_msg_text;
			}
		// Check Javascript capabilities
		if (empty($_POST['sabre_js_payload']) || empty($_POST['sabre_js_check1'])) {
			if ($js_support == 'true') {
				$error_msg_text = __('No Javascript capabilities.', 'sabre');
				$errors->add('sabre_js', $error_head_text . $error_msg_text);
				$sabre_errors['sabre_js'] = $error_msg_text;
				}
			}
		else {
			if ($_POST['sabre_js_check2'] != md5($_POST['sabre_js_payload'] . $_POST['sabre_js_check1'] . $magic_seed)) {
				$error_msg_text = __('Fake Javascript capabilities.', 'sabre');
				$errors->add('sabre_fake_js', $error_head_text . $error_msg_text);
				$sabre_errors['sabre_fake_js'] = $error_msg_text;
				}
			}
		}

	if ($user_pwd == 'true') {
		if(empty($_POST['user_pwd1']) || $_POST['user_pwd1'] == '' || empty($_POST['user_pwd2']) || $_POST['user_pwd2'] == ''){
			$error_msg_text = __('Missing password.', 'sabre');
			$errors->add('sabre_no_user_pwd', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_no_user_pwd'] = $error_msg_text;
		}elseif($_POST['user_pwd1'] !== $_POST['user_pwd2']){
			$error_msg_text = __('Mismatch between password fields.', 'sabre');
			$errors->add('sabre_mismatch_user_pwd', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_mismatch_user_pwd'] = $error_msg_text;
		}elseif(strlen($_POST['user_pwd1'])<6){
			$error_msg_text = __('Password length is less than 6 characters.', 'sabre');
			$errors->add('sabre_short_user_pwd', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_short_user_pwd'] = $error_msg_text;
		}
	}

	if ($enable_policy == 'true') {
		if($_POST['policy'] != 'yes'){
			$error_msg_text = __('Policy not accepted.', 'sabre');
			$errors->add('sabre_no_policy', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_no_policy'] = $error_msg_text;
		}
	}

	if ($result->captcha != NULL) {
		$string = strtoupper($result->captcha);
		$userstring = strtoupper($_POST['captcha']);

		if (($string <> $userstring) || (strlen($userstring) <> $stringlength)) {
			$error_msg_text = __('Invalid code.', 'sabre');
			$errors->add('sabre_captcha', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_captcha'] = $error_msg_text;
		}
	}

	if ($result->math != NULL) {
		if ($result->math <> $_POST['math']) {
			$error_msg_text = __('Invalid math result.', 'sabre');
			$errors->add('sabre_math', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_math'] = $error_msg_text;
		}
	}

	if ($result->letter != NULL) {
		if ($result->letter <> strtoupper($_POST['letter'])) {
			$error_msg_text = __('Invalid letter.', 'sabre');
			$errors->add('sabre_text', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_text'] = $error_msg_text;
		}
	}

	if ($enable_invite == 'true') {
		for ($i=0; $i < count($invite_codes); $i++)
			$invite_arr[$i] = $invite_codes[$i]['code'];
		if(!in_array(strtoupper($_POST['invite_code']), $invite_arr)){
			$error_msg_text = __('Invalid Hashtag (TIP: Hashtag is not the IRC channel).', 'sabre');
			$errors->add('sabre_invitation', $error_head_text . $error_msg_text);
			$sabre_errors['sabre_invitation'] = $error_msg_text;
		}
		else {
			$key = array_keys($invite_arr, strtoupper($_POST['invite_code']));
			if (!empty($invite_codes[$key[0]]['date'])) {
				if ($invite_codes[$key[0]]['date'] < $curdate) {
					$error_msg_text = __('Invalid Hashtag (TIP: Hashtag is not the IRC channel).', 'sabre');
					$errors->add('sabre_invitation', $error_head_text . $error_msg_text);
					$sabre_errors['sabre_invitation'] = $error_msg_text;
				}
			}
			if (is_numeric($invite_codes[$key[0]]['number']) && empty($sabre_errors)) {
				--$invite_codes[$key[0]]['number'];
				if ($invite_codes[$key[0]]['number'] < 0) {
					$invite_codes[$key[0]]['number'] = 0;
					$error_msg_text = __('Invalid Hashtag (TIP: Hashtag is not the IRC channel).', 'sabre');
					$errors->add('sabre_invitation', $error_head_text . $error_msg_text);
					$sabre_errors['sabre_invitation'] = $error_msg_text;
				}
				$sabre_opt['invite_codes'] = $invite_codes;
			}
		}
	}

	if (!empty($sabre_errors)) {
		$error_msg_text = __('Registration stopped.', 'sabre');
		$errors->add('sabre_generic', $error_head_text . $error_msg_text);
		@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `user` = %s, `email` = %s, `msg` = '" . maybe_serialize($sabre_errors) . "', `invite` = %s, `last_mod` = '" . $stordate ."', `status` = 'ko', `md5_id` = %s WHERE `id` = %d", !is_multisite() ? $_POST['user_login'] : $_POST['user_name'], $_POST['user_email'], $_POST['invite_code'], md5($_POST['sabre_id']), (int)$_POST['sabre_id']));
		$sabre_opt['total_stopped'] += 1;
		}
	elseif ($enable_confirm != 'none') {
		@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `user` = %s, `email` = %s,  `invite` = %s, `msg` = '', `last_mod` = '" . $stordate ."', `status` = 'to confirm', `md5_id` = %s  WHERE `id` = %d", !is_multisite() ? $_POST['user_login'] : $_POST['user_name'], $_POST['user_email'], $_POST['invite_code'], md5($_POST['sabre_id']), (int)$_POST['sabre_id']));
		}
	else {
		@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `user` = %s, `email` = %s, `invite` = %s, `msg` = '', `last_mod` = '" . $stordate ."', `status` = 'ok', `md5_id` = %s WHERE `id` = %d", !is_multisite() ? $_POST['user_login'] : $_POST['user_name'], $_POST['user_email'], $_POST['invite_code'], md5($_POST['sabre_id']), (int)$_POST['sabre_id']));
		$sabre_opt['total_accepted'] += 1;
		do_action('sabre_accepted_registration');
		}
	$this->update_option('sabre_opt', $sabre_opt);
	}

	if (!is_multisite())
		$stuff = $errors;
	else
		$stuff = array('user_name' => $user_name, 'orig_username' => $orig_username, 'user_email' => $user_email, 'errors' => $errors);

	return $stuff;
}

/***********************************************************************/
/* Check the login header                                              */
/***********************************************************************/

function login_head() {
	global $error, $wpdb;

	if (isset($_REQUEST['sabre_confirm']) && !empty($_REQUEST['sabre_confirm'])) {
		$sabre_opt = $this->get_option('sabre_opt');
		if (is_array($sabre_opt))
			extract ($sabre_opt, EXTR_OVERWRITE) ;
		$sabre_errors = array();
		$error_head_text = __('<strong>ERROR</strong>: ', 'sabre');

		$curdate = current_time('timestamp', 0);
		$stordate = date("Y-m-d H:i:s", $curdate);

		if ($enable_confirm == 'user') {
			$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . SABRE_TABLE . "` WHERE `md5_id` = %s", $_REQUEST['sabre_confirm']));
			if ((strtotime($result->last_mod) + (int)$period*24*60*60 >= $curdate) && ('to confirm' == $result->status)) {
				$error = __('Registration confirmed. You can now use your credentials to enter in this site.', 'sabre');
				@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `last_mod` = '" . $stordate ."', `status` = 'ok' WHERE `md5_id` = %s", $_REQUEST['sabre_confirm']));
				$sabre_opt['total_accepted'] += 1;
				$this->update_option('sabre_opt', $sabre_opt);
				do_action('sabre_accepted_registration');
				if ($mail_confirm == 'true')
					$this->new_user_confirmation($result->user_id);
				}
			else {
				if ('to confirm' == $result->status) {
					if ($delete_user == 'true') wp_delete_user($result->user_id);
					$error_msg_text = __('Exceeded period for confirmation of registration.', 'sabre');
					$error = $error_head_text . $error_msg_text;
					$sabre_errors['sabre_confirm'] = $error_msg_text;
					@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `msg` = '" . maybe_serialize($sabre_errors) . "', `last_mod` = '" . $stordate ."', `status` = 'ko' WHERE `md5_id` = %s", $_REQUEST['sabre_confirm']));
					$sabre_opt['total_stopped'] += 1;
					$this->update_option('sabre_opt', $sabre_opt);
					}
				else
					$error = $error_head_text . __('Unexpected confirmation of registration.', 'sabre');
				}
			}
		}
}

/***********************************************************************/
/* Check the login data                                                */
/***********************************************************************/

function check_login($user, $pass) {
	global $wpdb;

		$sabre_opt = $this->get_option('sabre_opt');
		if (is_array($sabre_opt))
			extract ($sabre_opt, EXTR_OVERWRITE) ;

		$curdate = current_time('timestamp', 0);
		$stordate = date("Y-m-d H:i:s", $curdate);

		if ($enable_confirm != 'none') {
			$sabre_errors = array();
			$error_head_text = __('<strong>ERROR</strong>: ', 'sabre');
			$cu = new WP_User($user->ID);

			if (!$cu->has_cap('edit_users')) {
				$result = $wpdb->get_row("SELECT * FROM `" . SABRE_TABLE . "` WHERE `user_id` = " . (int)$user->ID);
				if ('ok' != $result->status) {
					if ('to confirm' == $result->status) {
						if ($enable_confirm == 'user') {
							if (strtotime($result->last_mod) + (int)$period*24*60*60 < $curdate) {
								$error_msg_text = __('Exceeded period for confirmation of registration.', 'sabre');
								$sabre_errors['sabre_login'] = $error_msg_text;
								@$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `msg` = '" . maybe_serialize($sabre_errors) . "', `last_mod` = '" . $stordate ."', `status` = 'ko' WHERE `id` = " . (int)$result->id);
								$sabre_opt['total_stopped'] += 1;
								$this->update_option('sabre_opt', $sabre_opt);
								if ($delete_user == 'true')
									wp_delete_user($user->ID);
								return new WP_Error('sabre_login', $error_head_text . $error_msg_text);
								}
							elseif ($no_entry == 'true') {
								$error_msg_text = __('Confirmation pending. See registration e-mail.', 'sabre');
								$sabre_errors['sabre_login'] = $error_msg_text;
								return new WP_Error('sabre_login', $error_head_text . $error_msg_text);
								}
							}
						else {
							$error_msg_text = __('Registration not yet validated by the site\'s administrator. Wait for confirmation e-mail.', 'sabre');
							$sabre_errors['sabre_login'] = $error_msg_text;
							return new WP_Error('sabre_login', $error_head_text . $error_msg_text);
							}
						}
					else {
						$error_msg_text = __('Invalid registration status.', 'sabre');
						$sabre_errors['sabre_login'] = $error_msg_text;
						return new WP_Error('sabre_login', $error_head_text . $error_msg_text);
						}
					}
				}
			}
		return $user;
}

/***********************************************************************/
/* New user creation                                                   */
/***********************************************************************/

function new_user_created($user_id) {
	global $wpdb;

	$user = new WP_User( (int)$user_id);
	@$wpdb->query($wpdb->prepare("UPDATE `" . SABRE_TABLE . "` SET `user_id` = %d WHERE `user` = %s and `status` in ('ok', 'to confirm')", (int)$user->ID, $user->user_login));
	}

/***********************************************************************/
/* User confirmation function                                          */
/***********************************************************************/

function new_user_confirmation($user_id) {
	global $wpdb;

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	$sabre_opt = $this->get_option('sabre_opt');

	$mail_from = "From: ";
	$mail_from .= (!empty($sabre_opt['mail_from_name']) ? $sabre_opt['mail_from_name'] : get_option('blogname')) . " <";
	$mail_from .= (!empty($sabre_opt['mail_from_mail']) ? $sabre_opt['mail_from_mail'] : get_option('admin_email')) . ">";

	$message  = sprintf(__('New user registration confirmed on your site %s:', 'sabre'), get_option('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'sabre'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s', 'sabre'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration Confirmation', 'sabre'), get_option('blogname')), $message, $mail_from);
}

/***********************************************************************/
/* Admin confirmation function                                          */
/***********************************************************************/

function new_admin_confirmation($user_id) {
	global $wpdb;

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	$sabre_opt = $this->get_option('sabre_opt');

	$mail_from = "From: ";
	$mail_from .= (!empty($sabre_opt['mail_from_name']) ? $sabre_opt['mail_from_name'] : get_option('blogname')) . " <";
	$mail_from .= (!empty($sabre_opt['mail_from_mail']) ? $sabre_opt['mail_from_mail'] : get_option('admin_email')) . ">";

	$message  = sprintf(__('Your registration on %s is now confirmed. You can freely sign in using the credentials given in a previous mail. Thank you for your interest.', 'sabre'), get_option('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'sabre'), $user_login) . "\r\n\r\n";
	$message .= get_option('siteurl') . "/wp-login.php\r\n";

	@wp_mail($user_email, sprintf(__('[%s] New User Registration Confirmation', 'sabre'), get_option('blogname')), $message, $mail_from);
}

/***********************************************************************/
/* Option checked/unchecked                                            */
/***********************************************************************/

function magic_seed($size) {

	$core = "0123456789abcdefghijklmnopqrstuvwxyz";
	for ($i=0; $i < $size; $i++)
		$SeedOfLove .= substr($core, rand(0, strlen($core)-1), 1);

	return $SeedOfLove;
}

/***********************************************************************/
/* Option checked/unchecked                                            */
/***********************************************************************/

function checked($var1, $var2) {

if ($var1 == $var2)
   return 'checked';
else
   return '';
}

/***********************************************************************/
/* Option selected/unselected                                          */
/***********************************************************************/

function selected($var1, $var2) {

if ($var1 == $var2)
   return 'selected';
else
   return '';
}

/***********************************************************************/
/* Register manually a user                                            */
/***********************************************************************/

function add_reg_user($reguser) {
	global $wpdb;

	check_admin_referer('sabre-manage_registration');

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	$user_info = new WP_User($reguser);
	if($user_info->user_login == $reguser) {
		if (!$user_info->has_cap('edit_users')) {
			if (!$wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status` in ('ok', 'to confirm') AND `user_id` = '" . $user_info->ID . "'")) {
				@$wpdb->query("INSERT INTO `" . SABRE_TABLE . "` SET `user_IP` = 'none', `first_mod` = '" . $stordate ."', `last_mod` = '" . $stordate ."', `status` = 'ok', `user` = '" . $user_info->user_login . "', `email` = '" . $user_info->user_email . "', `user_id` = '" . $user_info->ID . "'");
				if (!mysql_error()) {
					$id = $wpdb->insert_id;
					@$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `md5_id` = '" . md5($id) . "' WHERE `id` = " . (int)$id);
					if (!mysql_error()) {
						$msg = __('WordPress user successfully registered.', 'sabre');
						do_action('sabre_accepted_registration');
						}
					else
						$msg = __('Unexpected SQL error. Current operation failed.', 'sabre');
					}
				else $msg = __('Unexpected SQL error. Current operation failed.', 'sabre');
				}
			else $msg = __('WordPress user is already registered. Manual registration ineffective.', 'sabre');
			}
		else $msg = __("WordPress user doesn't need to confirm registration. Manual registration ineffective.", 'sabre');
		}
	else $msg = __('Unknown WordPress user. Manual registration failed.', 'sabre');
	return $msg;
}

/***********************************************************************/
/* Register manually all existing users                                */
/***********************************************************************/

function add_all_users() {

	check_admin_referer('sabre-manage_registration');

	$users = get_users_of_blog();
	$total = 0;
	$added = 0;
	$msg = __('WordPress user successfully registered.', 'sabre');
	foreach ($users as $user) {
		$total += 1;
		$ret = $this->add_reg_user($user->user_login);
		if ($ret == $msg) $added += 1;
		}

	return sprintf(__ngettext('%s/%s WordPress user successfully registered.','%s/%s WordPress users successfully registered.',  $added, 'sabre'), $added, $total);
}

/***********************************************************************/
/* Unregister manually a user                                            */
/***********************************************************************/

function del_reg_user($list) {
	global $wpdb;

	check_admin_referer('sabre-manage_registration');

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	$error[0] = __("Manually unregistered", 'sabre');
	$sabre_opt = $this->get_option('sabre_opt');

	foreach ($list as $selid => $delid) {
		if ($sabre_opt['delete_user'] == 'true') {
			$user = $wpdb->get_var("SELECT `user_id` FROM `" . SABRE_TABLE . "` WHERE `id` = " . (int)$delid);
			if (isset($user)) wp_delete_user($user);
			}
		$removed += @$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `status` = 'ko', `last_mod` = '" . $stordate ."', `user_id` = NULL, `msg` = '" . maybe_serialize($error) . "' WHERE `id` = " . (int)$delid);
		do_action('sabre_cancelled_registration');
		}
	if (!mysql_error())
		return sprintf(__ngettext('%s user manually unregistered.', '%s users manually unregistered.', $removed, 'sabre'), $removed);
	else
		return __('Unexpected SQL error. Current operation failed.', 'sabre');
}

/***********************************************************************/
/* Purge unregistered user log                                         */
/***********************************************************************/

function del_unreg_user($days) {
	global $wpdb;

	check_admin_referer('sabre-manage_registration');

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	$removed = $wpdb->query("DELETE FROM `" . SABRE_TABLE . "` WHERE `status` = 'ko' AND `last_mod` < DATE_SUB('$stordate', INTERVAL $days DAY)");
	if (!mysql_error())
		return sprintf(__ngettext('%s record deleted.', '%s records deleted.', $removed, 'sabre'), $removed);
	else
		return __('Unexpected SQL error. Current operation failed.', 'sabre');
}

/***********************************************************************/
/* Refuse manually a registration                                     */
/***********************************************************************/

function unconfirm_reg_user($list) {
	global $wpdb;

	check_admin_referer('sabre-manage_registration');

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	$error[0] = __("Registration refused by admin", 'sabre');
	$sabre_opt = $this->get_option('sabre_opt');

	foreach ($list as $selid => $delid) {
		if ($sabre_opt['delete_user'] == 'true') {
			$user = $wpdb->get_var("SELECT `user_id` FROM `" . SABRE_TABLE . "` WHERE `id` = " . (int)$delid);
			if (isset($user)) wp_delete_user($user);
			}
		$removed += @$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `status` = 'ko', `last_mod` = '" . $stordate ."', `user_id` = NULL, `msg` = '" . maybe_serialize($error) . "' WHERE `id` = " . (int)$delid);
		}
	if (!mysql_error()) {
		$sabre_opt['total_stopped'] += $removed;
		$this->update_option('sabre_opt', $sabre_opt);
		return sprintf(__ngettext('%s registration refused.', '%s registrations refused.', $removed, 'sabre'), $removed);
		}
	else
		return __('Unexpected SQL error. Current operation failed.', 'sabre');
}

/***********************************************************************/
/* Confirm manually a registration                                     */
/***********************************************************************/

function confirm_reg_user($list) {
	global $wpdb;

	check_admin_referer('sabre-manage_registration');

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	$error[0] = __("Registration confirmed by admin", 'sabre');
	$sabre_opt = $this->get_option('sabre_opt');

	foreach ($list as $selid => $delid) {
		$user = $wpdb->get_var("SELECT `user_id` FROM `" . SABRE_TABLE . "` WHERE `id` = " . (int)$delid);
		if (isset($user)) $this->new_admin_confirmation($user);
		$confirmed += @$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `status` = 'ok', `last_mod` = '" . $stordate ."', `msg` = '" . maybe_serialize($error) . "' WHERE `id` = " . (int)$delid);
		do_action('sabre_accepted_registration');
		}
	if (!mysql_error()) {
		$sabre_opt['total_accepted'] += $confirmed;
		$this->update_option('sabre_opt', $sabre_opt);
		return sprintf(__ngettext('%s registration confirmed.', '%s registrations confirmed.', $confirmed, 'sabre'), $confirmed);
		}
	else
		return __('Unexpected SQL error. Current operation failed.', 'sabre');
}

/***********************************************************************/
/* Save Sabre options                                                  */
/***********************************************************************/

function save_options($form_values) {

	$invite_gen = array_search(__("Gen", 'sabre'), $form_values);
	$invite_del = array_search(__("Del", 'sabre'), $form_values);
	if ($invite_gen || $invite_del)
		$this->check_invite_codes($invite_gen, $invite_del, $form_values);

	if (isset($form_values['sabre_option_save'])) {

		check_admin_referer('sabre-manage_option');

		$sabre_opt = $this->get_option('sabre_opt');

		$sabre_opt['enable_captcha'] = (isset($form_values['sabre_enable_captcha']) ? 'true' : 'false');
		$sabre_opt['white_bg'] = (isset($form_values['sabre_white_bg']) ? 'true' : 'false');
		$sabre_opt['acceptedChars'] = (!empty($form_values['sabre_acceptedChars']) ? $form_values['sabre_acceptedChars'] : 'ABCEFGHJKMNPRSTVWXYZ123456789');
		$sabre_opt['stringlength'] = (!empty($form_values['sabre_stringlength']) ? (int)$form_values['sabre_stringlength'] : 5);
		$sabre_opt['contrast'] = (int)$form_values['sabre_contrast'];
		$sabre_opt['num_polygons'] = (int)$form_values['sabre_num_polygons'];
		$sabre_opt['num_ellipses'] = (int)$form_values['sabre_num_ellipses'];
		$sabre_opt['num_lines'] = (int)$form_values['sabre_num_lines'];
		$sabre_opt['num_dots'] = (int)$form_values['sabre_num_dots'];
		$sabre_opt['min_thickness'] = (int)$form_values['sabre_min_thickness'];
		$sabre_opt['max_thickness'] = (int)$form_values['sabre_max_thickness'];
		$sabre_opt['min_radius'] = (int)$form_values['sabre_min_radius'];
		$sabre_opt['max_radius'] = (int)$form_values['sabre_max_radius'];
		$sabre_opt['object_alpha'] = (int)$form_values['sabre_object_alpha'];

		$sabre_opt['enable_math'] = (isset($form_values['sabre_enable_math']) ? 'true' : 'false');
		$sabre_opt['math_ops'] = (!empty($form_values['sabre_math_ops']) ? $form_values['sabre_math_ops'] : '+-*');

		$sabre_opt['enable_text'] = (isset($form_values['sabre_enable_text']) ? 'true' : 'false');

		$sabre_opt['enable_confirm'] = (isset($form_values['sabre_enable_confirm']) ? $form_values['sabre_enable_confirm'] : 'none');
		$sabre_opt['period'] = (1 > (int)$form_values['sabre_confirm_period'] ? 1 : (int)$form_values['sabre_confirm_period']);
		$sabre_opt['no_entry'] = (isset($form_values['sabre_no_entry']) ? 'true' : 'false');
		$sabre_opt['delete_user'] = (isset($form_values['sabre_delete_user']) ? 'true' : 'false');
		$sabre_opt['mail_confirm'] = (isset($form_values['sabre_mail_confirm']) ? 'true' : 'false');

		$sabre_opt['sabre_seq'] = $form_values['sabre_test_seq'];

		$sabre_opt['enable_stealth'] = (isset($form_values['sabre_enable_stealth']) ? 'true' : 'false');
		$sabre_opt['js_support'] = (isset($form_values['sabre_enable_js']) ? 'true' : 'false');
		$sabre_opt['session_timeout'] = (int)$form_values['sabre_timeout'];
		$sabre_opt['speed_limit'] = (int)$form_values['sabre_speed'];
		$sabre_opt['check_banned_IP'] = (isset($form_values['sabre_banned_IP']) ? 'true' : 'false');

		$sabre_opt['user_pwd'] = (isset($form_values['sabre_user_pwd']) ? 'true' : 'false');
		$sabre_opt['show_banner'] = (isset($form_values['sabre_show_banner']) ? 'true' : 'false');
		$sabre_opt['show_dashboard'] = (isset($form_values['sabre_show_dashboard']) ? 'true' : 'false');
		$sabre_opt['show_user'] = (isset($form_values['sabre_show_user']) ? 'true' : 'false');
		$sabre_opt['suppress_sabre'] = (isset($form_values['sabre_suppress_sabre']) ? 'true' : 'false');
		$sabre_opt['enable_policy'] = (isset($form_values['sabre_enable_policy']) ? 'true' : 'false');
		$sabre_opt['policy_name'] = $form_values['sabre_policy_name'];
		$sabre_opt['policy_link'] = $form_values['sabre_policy_link'];
		$sabre_opt['policy_text'] = $form_values['sabre_policy_text'];
		$sabre_opt['enable_invite'] = (isset($form_values['sabre_enable_invite']) ? 'true' : 'false');
		$sabre_opt['invite_codes'] = $this->shrink_invite_codes($form_values);
		$sabre_opt['mail_from_name'] = $form_values['sabre_mail_from_name'];
		$sabre_opt['mail_from_mail'] = $form_values['sabre_mail_from_mail'];

		$this->update_option('sabre_opt', $sabre_opt);

		return TRUE;
	}
	return FALSE;
}

/***********************************************************************/
/* Load CSS for Sabre admin page                                       */
/***********************************************************************/

function sabre_css ()
{
	if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'sabre')
		include_once(SABREPATH . 'sabre_css.php');
}

/***********************************************************************/
/* Load CSS for login form                                             */
/***********************************************************************/

function login_css ()
{
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'register' && !is_multisite()) {
		wp_register_script('password-strength-meter', '/wp-admin/js/password-strength-meter.js', array('jquery'), '20070405');
		wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
				'short' => __('Too short', 'sabre'),
				'bad' => __('Bad', 'sabre'),
				'good' => __('Good', 'sabre'),
				'strong' => __('Strong', 'sabre'),
				'mismatch' => __('Mismatch', 'sabre')
			) );
		wp_print_scripts('password-strength-meter');
		?>
		<script type="text/javascript">
		function check_pass_strength ( ) {

		var pass = jQuery('#user_pwd1').val();
		var user = jQuery('#user_login').val();
		var pass2 = jQuery('#user_pwd2').val();

		// get the result as an object, i'm tired of typing it
		var res = jQuery('#pass-strength-result');

		var strength = passwordStrength(pass, user, pass2);

		jQuery(res).removeClass('short bad good strong');

		if ( strength == 2 ) {
			jQuery(res).addClass('bad');
			jQuery(res).html( pwsL10n.bad );
		}
		else if ( strength == 3 ) {
			jQuery(res).addClass('good');
			jQuery(res).html( pwsL10n.good );
		}
		else if ( strength == 4 ) {
			jQuery(res).addClass('strong');
			jQuery(res).html( pwsL10n.strong );
		}
		else if ( strength == 5 ) {
			jQuery(res).addClass('short');
			jQuery(res).html( pwsL10n.mismatch );
		}
		else {
			// this catches 'Too short' and the off chance anything else comes along
			jQuery(res).addClass('short');
			jQuery(res).html( pwsL10n.short );
		}

		}

		jQuery(document).ready(function($) {
			$('#user_pwd1').keyup( check_pass_strength );
			$('#user_pwd2').keyup( check_pass_strength );
		$('.color-palette').click(function(){$(this).siblings('input[name=admin_color]').attr('checked', 'checked')});
		} );

		jQuery(document).ready( function() {
			jQuery('#user_pwd1,#user_pwd2').attr('autocomplete','off');
    		});
		</script>
<?php

	}
	
	echo "<link rel=\"stylesheet\" href=\"" . SABREURL . "sabre_login.css" . "\" type=\"text/css\" />\r\n\r\n";

	if ($this->custom_logo && !is_multisite())
		echo '<style type="text/css"> h1 a {background: url(' . SABREURL . 'images/sabre-login.gif) no-repeat; width: 292px; height: 66px; text-indent: -9999px; overflow: hidden; padding-bottom: 15px; display: block;} </style>';
}

/***********************************************************************/
/* Change default header url in login form                             */
/***********************************************************************/

function header_url ($header_url) {

	return get_bloginfo('url');
}

/***********************************************************************/
/* Change default header title in login form                           */
/***********************************************************************/

function header_title ($header_title) {

	return get_bloginfo('name');
}

/***********************************************************************/
/* Automatic cleanup                                                   */
/***********************************************************************/

function auto_clean () {
	global $wpdb;

	// Disable unconfirmed registration
	$sabre_opt = $this->get_option('sabre_opt');
	if (is_array($sabre_opt))
		extract ($sabre_opt, EXTR_OVERWRITE) ;

	$curdate = current_time('timestamp', 0);
	$stordate = date("Y-m-d H:i:s", $curdate);

	if ($enable_confirm == 'user') {
		$error[0] = __('Exceeded period for confirmation of registration.', 'sabre');
		$days = (int)$period;
		if ($delete_user == 'true') {
			$users = $wpdb->get_results("SELECT `user_id` FROM `" . SABRE_TABLE . "` WHERE `status` = 'to confirm' AND `last_mod` < DATE_SUB('$stordate', INTERVAL $days DAY)");
			if ($users) {
				foreach ($users as $user) {
					wp_delete_user($user->user_id);
					}
				}
			}

		@$wpdb->query("UPDATE `" . SABRE_TABLE . "` SET `status` = 'ko', `last_mod` = '" . $stordate ."', `user_id` = NULL, `msg` = '" . maybe_serialize($error) . "' WHERE `status` = 'to confirm' AND `last_mod` < DATE_SUB('$stordate', INTERVAL $days DAY)");
	}

	// Clean the table
	$purge = (int)$purge_days;
	if ($purge > 0) {
		$query = "DELETE FROM `" . SABRE_TABLE . "` WHERE `last_mod` < DATE_SUB('$stordate', INTERVAL $purge DAY) AND `status` not in ('ok','to confirm')";
		$removed = $wpdb->query($query);
	}

}

/***********************************************************************/
/* Get new spam blocked since last visit                               */
/***********************************************************************/

function get_new_spam () {
global $wpdb;

	$sabre_opt = $this->get_option('sabre_opt');
	return $wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status`= 'ko' AND `last_mod` > " . strftime("'%Y-%m-%d %H:%M:%S'", (int)$sabre_opt['last_spam_check']));

}

/***********************************************************************/
/* Get new registrations accepted since last visit                     */
/***********************************************************************/

function get_new_users () {
global $wpdb;

	$sabre_opt = $this->get_option('sabre_opt');
	return $wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status`= 'ok' AND `last_mod` > " . strftime("'%Y-%m-%d %H:%M:%S'", (int)$sabre_opt['last_approved_check']));

}

/***********************************************************************/
/* Get new registrations to confirm since last visit                     */
/***********************************************************************/

function get_new_confirm () {
global $wpdb;

	$sabre_opt = $this->get_option('sabre_opt');
	return $wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status`= 'to confirm' AND `last_mod` > " . strftime("'%Y-%m-%d %H:%M:%S'", (int)$sabre_opt['last_confirm_check']));

}

/***********************************************************************/
/* Display the list of invitation codes in the option form             */
/***********************************************************************/

function display_invite_codes () {

	$sabre_opt = $this->get_option('sabre_opt');
	$invite_codes = $sabre_opt['invite_codes'];
	If (!is_array($invite_codes))
		$invite_codes = array();

	for ($i=0; $i < count($invite_codes); $i++) {
		$invite_string .= '<li><input type="text" size="15" name="sabre_invite_code[]" id="sabre_invite_code_' . $i . '" value="' . $invite_codes[$i]['code'] . '" /><input type="text" size="6" name="sabre_invite_number[]" id="sabre_invite_number_' . $i .'" value="' . $invite_codes[$i]['number'] . '" /><input type="text" size="10" name="sabre_invite_date[]" id="sabre_invite_date_' . $i . '" value="' . (is_numeric($invite_codes[$i]['date']) ? date('Y-m-d', $invite_codes[$i]['date']) : '') . '" /><input type="submit" name="sabre_invite_action-gen-' . $i . '" value="'. __("Gen", 'sabre') . '" class="button" /><input type="submit" name="sabre_invite_action-del-' . $i . '" value="'. __("Del", 'sabre') . '" class="button" /></li>';
		}

	$invite_string .= '<li><input type="text" size="15" name="sabre_invite_code[]" id="sabre_invite_code_' . $i . '" value="" /><input type="text" size="6" name="sabre_invite_number[]" id="sabre_invite_number_' . $i .'" value="" /><input type="text" size="10" name="sabre_invite_date[]" id="sabre_invite_date_' . $i . '" value="" /><input type="submit" name="sabre_invite_action-gen-' . $i . '" value="'. __("Gen", 'sabre') . '" class="button" /></li>';

	return $invite_string;
}

/***********************************************************************/
/* Check the list of invitation codes in the option form               */
/***********************************************************************/

function check_invite_codes($gen, $del, $form_values) {

	if ($gen) {
		$gen_arr = explode('-', $gen);
		$gen_id = $gen_arr[2];
		$myWord = new wordGenerator();
		$form_values['sabre_invite_code'][$gen_id] = strtoupper($myWord->create(5,10,true));
		unset($myWord);
		}

	if ($del) {
		$del_arr = explode('-', $del);
		$del_id = $del_arr[2];
		unset($form_values['sabre_invite_code'][$del_id]);
		unset($form_values['sabre_invite_number'][$del_id]);
		unset($form_values['sabre_invite_date'][$del_id]);
		}

	$sabre_opt = $this->get_option('sabre_opt');
	$sabre_opt['invite_codes'] = $this->shrink_invite_codes($form_values);
	$this->update_option('sabre_opt', $sabre_opt);
}

/***********************************************************************/
/* Prepare the list of invitation codes in the option form                */
/***********************************************************************/

function shrink_invite_codes($form_values) {

	$i=0;
	$invite_codes = '';
	foreach ($form_values['sabre_invite_code'] as $k => $v) {
		if (!empty($v)) {
			$invite_codes[$i]['code'] = $v;
			$invite_codes[$i]['number'] = (is_numeric($form_values['sabre_invite_number'][$k]) ? intval($form_values['sabre_invite_number'][$k]) : '');
			$dt = explode('-', $form_values['sabre_invite_date'][$k]);
			$invite_codes[$i]['date'] = ($dt[0] ? mktime(0, 0, 0, $dt[1], $dt[2], $dt[0]) : '');
			$i++;
			}
		}
	return $invite_codes;
}


/*
Description : A function with a very simple but powerful xor method to encrypt
              and/or decrypt a string with an unknown key. Implicitly the key is
              defined by the string itself in a character by character way.
              There are 4 items to compose the unknown key for the character
              in the algorithm
              1.- The ascii code of every character of the string itself
              2.- The position in the string of the character to encrypt
              3.- The length of the string that include the character
              4.- Any special formula added by the programmer to the algorithm
                  to calculate the key to use
*/
function Encrypt_Decrypt($Str_Message) {
//Function : encrypt/decrypt a string message v.1.0  without a known key
//Author   : Aitor Solozabal Merino (spain)
//Email    : aitor-3@euskalnet.net
//Date     : 01-04-2005
    $Len_Str_Message=STRLEN($Str_Message);
    $Str_Encrypted_Message="";
    FOR ($Position = 0;$Position<$Len_Str_Message;$Position++){
        // long code of the function to explain the algoritm
        //this function can be tailored by the programmer modifyng the formula
        //to calculate the key to use for every character in the string.
        $Key_To_Use = (($Len_Str_Message+$Position)+1); // (+5 or *3 or ^2)
        //after that we need a module division because can´t be greater than 255
        $Key_To_Use = (255+$Key_To_Use) % 255;
        $Byte_To_Be_Encrypted = SUBSTR($Str_Message, $Position, 1);
        $Ascii_Num_Byte_To_Encrypt = ORD($Byte_To_Be_Encrypted);
        $Xored_Byte = $Ascii_Num_Byte_To_Encrypt ^ $Key_To_Use;  //xor operation
        $Encrypted_Byte = CHR($Xored_Byte);
        $Str_Encrypted_Message .= $Encrypted_Byte;

        //short code of  the function once explained
        //$str_encrypted_message .= chr((ord(substr($str_message, $position, 1))) ^ ((255+(($len_str_message+$position)+1)) % 255));
    }
    RETURN $Str_Encrypted_Message;
} //end function



/***********************************************************************/
/* Create/Update SQL tables                                            */
/***********************************************************************/

function DB_update () {
	global $wpdb;

	$sabre_opt = $this->get_option('sabre_opt');
	$cur_DB = (int)$sabre_opt['DB_version'];
	if ($cur_DB < $this->DB_VERSION) {
		$success = true;
		if ($cur_DB == 0) {
			$query = "CREATE TABLE IF NOT EXISTS `" . SABRE_TABLE . "` (
	 			`id` bigint(20) NOT NULL auto_increment,
				`user_id` bigint(20) NOT NULL default -1,
	 			`user` tinytext,
	 			`email` varchar(100),
	 			`user_IP` varchar(100) NOT NULL,
				`first_mod` datetime NOT NULL default '0000-00-00 00:00:00',
	 			`last_mod` datetime NOT NULL default '0000-00-00 00:00:00',
	 			`msg` text,
				`status` enum('ok','ko','pending','to confirm') NOT NULL default 'pending',
				`captcha` varchar(100),
				`md5_id` varchar(50),
				PRIMARY KEY (`id`)
				) TYPE=MyISAM;";

			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 1;
		}
		if ($cur_DB == 1) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD INDEX `last_mod` ( `last_mod` )";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else {
				$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD INDEX `md5_id` ( `md5_id` )";
				$wpdb->query($query);
				if (mysql_error())
					$success = false;
				else
					$cur_DB = 2;
			}
		}
		if ($cur_DB == 2) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD INDEX `user_id` ( `user_id` )";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 3;
		}
		if ($cur_DB == 3) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD COLUMN `math` smallint";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 4;
		}
		if ($cur_DB == 4) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` MODIFY `user` tinytext, MODIFY `email` varchar(100), MODIFY `msg` text";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 5;
		}
		if ($cur_DB == 5) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD COLUMN `invite` varchar(50)";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 6;
		}
		if ($cur_DB == 6) {
			$query = "ALTER TABLE `" . SABRE_TABLE . "` ADD COLUMN `letter` char(1)";
			$wpdb->query($query);
			if (mysql_error())
				$success = false;
			else
				$cur_DB = 7;
		}

		$sabre_opt['DB_version'] = $cur_DB;
		$this->update_option('sabre_opt', $sabre_opt);
	}
}

/***********************************************************************/
/* Create/Update options                                               */
/***********************************************************************/

function options_update () {

/***********************************************************************/
/* Default parameters                                                  */
/***********************************************************************/

	$sabre_opt_opt['DB_version'] = 0;
	$sabre_opt_opt['total_accepted'] = 0;
	$sabre_opt_opt['total_stopped'] = 0;
	$sabre_opt_opt['show_banner'] = 'false';
	$sabre_opt_opt['purge_days'] = 20;
	$sabre_opt_opt['suppress_sabre'] = 'false';
	$sabre_opt_opt['show_dashboard'] = 'false';
	$sabre_opt_opt['show_user'] = 'false';
	$sabre_opt_opt['user_pwd'] = 'false';
	$sabre_opt_opt['enable_policy'] = 'false';
	$sabre_opt_opt['policy_name'] = '';
	$sabre_opt_opt['policy_link'] = '';
	$sabre_opt_opt['policy_text'] = '';
	$sabre_opt_opt['enable_invite'] = 'false';
	$sabre_opt_opt['invite_codes'] = array();
	$sabre_opt_opt['mail_from_name'] = '';
	$sabre_opt_opt['mail_from_mail'] = '';

	$sabre_opt_opt['enable_captcha'] = 'false';
	$sabre_opt_opt['white_bg'] = 'false';
	$sabre_opt_opt['acceptedChars'] = 'ABCEFGHJKMNPRSTVWXYZ123456789';
	$sabre_opt_opt['stringlength'] = 5;
	$sabre_opt_opt['contrast'] = 60;
	$sabre_opt_opt['num_polygons'] = 3; // Number of triangles to draw.  0 = none
	$sabre_opt_opt['num_ellipses'] = 6;  // Number of ellipses to draw.  0 = none
	$sabre_opt_opt['num_lines'] = 0;  // Number of lines to draw.  0 = none
	$sabre_opt_opt['num_dots'] = 0;  // Number of dots to draw.  0 = none
	$sabre_opt_opt['min_thickness'] = 2;  // Minimum thickness in pixels of lines
	$sabre_opt_opt['max_thickness'] = 8;  // Maximum thickness in pixles of lines
	$sabre_opt_opt['min_radius'] = 5;  // Minimum radius in pixels of ellipses
	$sabre_opt_opt['max_radius'] = 15;  // Maximum radius in pixels of ellipses
	$sabre_opt_opt['object_alpha'] = 100; // How opaque should the obscuring objects be. 0 is opaque, 127 is transparent.

	$sabre_opt_opt['enable_math'] = 'false';
	$sabre_opt_opt['math_ops'] = '+-*';

	$sabre_opt_opt['enable_text'] = 'false';

	$sabre_opt_opt['enable_confirm'] = 'none';
	$sabre_opt_opt['period'] = 3;
	$sabre_opt_opt['no_entry'] = 'false';
	$sabre_opt_opt['delete_user'] = 'false';
	$sabre_opt_opt['mail_confirm'] = 'false';

	$sabre_opt_opt['sabre_seq'] = 'All';

	$sabre_opt_opt['enable_stealth'] = 'false';
	$sabre_opt_opt['session_timeout'] = 300;
	$sabre_opt_opt['speed_limit'] = 5;
	$sabre_opt_opt['js_support'] = 'false';
	$sabre_opt_opt['magic_seed'] = $this->magic_seed(10);
	$sabre_opt_opt['check_banned_IP'] = 'false';

/***********************************************************************/
/* Update parameters                                                   */
/***********************************************************************/

	$sabre_opt = $this->get_option('sabre_opt');

	if (!is_multisite()) {
		$sabre_captcha = get_option('sabre_captcha');
		if (!empty($sabre_captcha)) {
			foreach ($sabre_captcha as $k => $v)
				$sabre_opt[$k] = $v;
			delete_option('sabre_captcha');
			}

		$sabre_math = get_option('sabre_math');
		if (!empty($sabre_math)) {
			foreach ($sabre_math as $k => $v)
				$sabre_opt[$k] = $v;
			delete_option('sabre_math');
			}

		$sabre_confirm = get_option('sabre_confirm');
		if (!empty($sabre_confirm)) {
			foreach ($sabre_confirm as $k => $v)
				$sabre_opt[$k] = $v;
			delete_option('sabre_confirm');
			}

		$sabre_seq = get_option('sabre_seq');
		if (!empty($sabre_seq)) {
			$sabre_opt['sabre_seq'] = $sabre_seq;
			delete_option('sabre_seq');
			}

		$sabre_stealth = get_option('sabre_stealth');
		if (!empty($sabre_stealth)) {
			foreach ($sabre_stealth as $k => $v)
				$sabre_opt[$k] = $v;
			delete_option('sabre_stealth');
			}
		}

	if (is_multisite() && empty($sabre_opt)) {
		$monosite_opt = get_option('sabre_opt');
		if (!empty($monosite_opt) && is_array($monosite_opt)) {
			$monosite_opt['enable_confirm'] = $sabre_opt_opt['enable_confirm'];
			$monosite_opt['period'] = $sabre_opt_opt['period'];
			$monosite_opt['no_entry'] = $sabre_opt_opt['no_entry'];
			$monosite_opt['delete_user'] = $sabre_opt_opt['delete_user'];
			$monosite_opt['mail_confirm'] = $sabre_opt_opt['mail_confirm'];
			$monosite_opt['user_pwd'] = $sabre_opt_opt['user_pwd'];
			$sabre_opt = $monosite_opt;
			delete_option('sabre_opt');
			}
		}

	if (empty($sabre_opt))
		$sabre_opt = $sabre_opt_opt;
	else
		$sabre_opt = array_merge($sabre_opt_opt, $sabre_opt);

	$invite_codes = $sabre_opt['invite_codes'];
	if (!empty($invite_codes) && !is_array($invite_codes)) {
		$invite_arr = explode("\n", $invite_codes);
		$invite_codes = array();
		foreach ($invite_arr as $k => $v) {
			$invite_codes[$k]['code'] = trim($v);
			$invite_codes[$k]['number'] = '';
			$invite_codes[$k]['date'] = '';
			}
		}

	$this->update_option('sabre_opt', $sabre_opt);

}


/***********************************************************************/
/* Show Sabre data in profile                                          */
/***********************************************************************/

function show_user_profile($user) {
	global $wpdb;

	if ((!is_multisite() && current_user_can( 'manage_options' )) || (is_multisite() && is_super_admin())) {
		$result = $wpdb->get_row("SELECT * FROM `" . SABRE_TABLE . "` WHERE `user_id` = " . (int)$user->ID);
?>
<h3><?php _e("Registration's informations from Sabre", 'sabre') ?></h3>
<table class="form-table">
<tr>
<th><label for="sabre_status"><?php _e("Status", 'sabre'); ?></label></th>
<td><input type="text" name="sabre_status" id="sabre_status" value="<?php echo $result->status; ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e("State of registration.", 'sabre'); ?></span></td>
</tr>
<tr>
<th><label for="sabre_date"><?php _e("Date", 'sabre'); ?></label></th>
<td><input type="text" name="sabre_date" id="sabre_date" value="<?php echo $result->last_mod; ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e("Last modification date.", 'sabre'); ?></span></td>
</tr>
<tr>
<th><label for="sabre_invite"><?php _e("Invite code", 'sabre'); ?></label></th>
<td><input type="text" name="sabre_invite" id="sabre_invite" value="<?php echo $result->invite; ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e("Invitation code used.", 'sabre'); ?></span></td>
</tr>
</table>
<?php
	}
}


/***********************************************************************/
/* Plugin additional actions                                                      */
/***********************************************************************/

function add_plugin_actions($links, $file) {
	static $this_plugin;

	if ((!is_multisite() && current_user_can( 'manage_options' )) || (is_multisite() && is_super_admin())) {
		if( ! $this_plugin ) $this_plugin = SABREDIR . '/sabre.php';

		if( $file == $this_plugin ) {
			$settings_link = '<a href="edit.php?page=sabre" title="' . __("Set Sabre's parameters", 'sabre') . '">' . __("Configure", 'sabre') . '</a>';
			$links[] = $settings_link;
			}
		}
	return $links;
}

/***********************************************************************/
/* Plugin activation procedure                                         */
/***********************************************************************/

function activate() {
	global $wp_version;

	if (version_compare($wp_version, '3.0', '<')) {
		deactivate_plugins('sabre'); // Deactivate ourself
                wp_die(__("Sorry, but you can't run this plugin, it requires WordPress 3.0 or higher.", 'sabre'));
        }

	if (is_multisite() && !is_super_admin()) {
		deactivate_plugins('sabre'); // Deactivate ourself
                wp_die(__("Sorry, but you have no authority to manage this plugin.", 'sabre'));
        }

	$this->options_update();
	$this->DB_update();

	// Remove obsolete script files
	if (file_exists(SABREPATH . 'sabre_about.php'))
		unlink(SABREPATH . 'sabre_about.php');
	if (file_exists(SABREPATH . 'sabre_admin.php'))
		unlink(SABREPATH . 'sabre_admin.php');
	if (file_exists(SABREPATH . 'sabre_misc.php'))
		unlink(SABREPATH . 'sabre_misc.php');
	if (file_exists(SABREPATH . 'sabre_define.php'))
		unlink(SABREPATH . 'sabre_define.php');
}

/***********************************************************************/
/* Plugin deactivation procedure                                       */
/***********************************************************************/

function deactivate() {
	global $wpdb;

	if (is_multisite() && !is_super_admin()) {
		activate_plugins('sabre'); // Reactivate ourself
                wp_die(__("Sorry, but you have no authority to manage this plugin.", 'sabre'));
        }

	$sabre_opt = $this->get_option('sabre_opt');
	if ($sabre_opt['suppress_sabre'] == 'true') {
			$query = "DROP TABLE `" . SABRE_TABLE . "` CASCADE";
			$wpdb->query($query);

			$this->delete_option('sabre_opt');
			}
}

/***********************************************************************/
/* Plugin dashboard widget                                             */
/***********************************************************************/

function dashboard_output($sidebar_args) {
	global $wpdb;

		$sabre_opt = $this->get_option('sabre_opt');
		$new_spams = $this->get_new_spam();
		if ($new_spams)
			$new_spams = "/($new_spams)";
		else
			$new_spams = "";
		$new_approved = $this->get_new_users();
		if ($new_approved)
			$new_approved = "/($new_approved)";
		else
			$new_approved = "";
		$new_confirmed = $this->get_new_confirm();
		if ($new_confirmed)
			$new_confirmed = "/($new_confirmed)";
		else
			$new_confirmed = "";
		$total_pending = $wpdb->get_var("SELECT COUNT(*) FROM `" . SABRE_TABLE . "` WHERE `status` = 'to confirm'");

		$sabre_parent_page = '/wp-admin/tools.php';

		echo '<ul>';
		echo '<li>' . __('Total number of registrations stopped:', 'sabre') . '  <strong><a href="' . get_bloginfo('wpurl') . $sabre_parent_page . '?page=sabre&amp;sabre_action=spam" >' . (int)$sabre_opt['total_stopped'] . $new_spams . '</a></strong></li>';
		echo '<li>' . __('Total number of registrations accepted:', 'sabre') . '  <strong><a href="' . get_bloginfo('wpurl') . $sabre_parent_page . '?page=sabre&amp;sabre_action=approved" >' . (int)$sabre_opt['total_accepted'] . $new_approved . '</a></strong></li>';
		echo '<li>' . __('Number of pending confirmation:', 'sabre') . '  <strong><a href="' . get_bloginfo('wpurl') . $sabre_parent_page . '?page=sabre&amp;sabre_action=confirm" >' . (int)$total_pending . $new_confirmed . '</a></strong></li>';
		echo '</ul>';

	}

function dashboard_setup() {

	if ((!is_multisite() && current_user_can( 'manage_options' )) || (is_multisite() && is_super_admin()))
	wp_add_dashboard_widget( 'dashboard_sabre', __('Sabre quick figures', 'sabre'), array(&$this, 'dashboard_output'));
	}


/***********************************************************************/
/* Let's go for the party !                                            */
/***********************************************************************/

function Sabre() {

	$this->__construct();
	}

function __construct() {

	if (file_exists(SABREPATH . 'images/sabre-login.gif'))
		$this->custom_logo = TRUE;
	else
		$this->custom_logo = FALSE;
	if ($this->custom_logo) {
		add_filter('login_headerurl', array(&$this, 'header_url'));
		add_filter('login_headertitle', array(&$this, 'header_title'));
		}

	$sabre_opt = $this->get_option('sabre_opt');

	add_action('init', array(&$this, 'init'));
	add_action('login_head', array(&$this, 'login_css'));
	add_action('login_head', array(&$this, 'login_head'));
	add_action('admin_head', array(&$this, 'sabre_css'));
	add_action('admin_menu', array(&$this, 'options'));
	add_filter( 'plugin_action_links', array(&$this, 'add_plugin_actions'), 10, 2);
	if ($sabre_opt['show_user'] == 'true') {
		add_action('show_user_profile', array(&$this,'show_user_profile'));
		add_action('edit_user_profile', array(&$this,'show_user_profile'));
		}
	if ($sabre_opt['show_dashboard'] == 'true') {
		add_action('wp_dashboard_setup', array(&$this, 'dashboard_setup'));
		}
	add_action('register_form', array(&$this, 'change_registration_form'));
	add_filter('registration_errors', array(&$this, 'check_entry'));
	add_filter('wp_authenticate_user', array(&$this, 'check_login'), 10, 2);
	add_action('user_register', array(&$this, 'new_user_created'));
	// Multisite specific actions
	add_action('signup_header', array(&$this, 'login_css'));
	add_action('signup_extra_fields', array(&$this, 'change_registration_form'));
	add_filter('wpmu_validate_user_signup', array(&$this, 'check_entry'));
	// Plugin activation/deactivation
	register_activation_hook(SABREPATH . 'sabre.php', array(&$this, 'activate'));
	register_deactivation_hook(SABREPATH . 'sabre.php', array(&$this, 'deactivate'));

	}
}
?>
