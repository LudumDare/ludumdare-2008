<?php

/*
Plugin Name: SABRE
Plugin URI: http://wordpress.org/extend/plugins/sabre
Description: Simple Anti Bot Registration Engine
Version: 1.5.0
Author: Didier Lorphelin
Author URI: 
*/

/*  Copyright 2011  Didier Lorphelin  (email : )

	Was Sable 1.2.2, but unsupported.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists("Sabre")) {
	global $wpdb;

	if (isset($wpdb->base_prefix))
		define('SABRE_TABLE', $wpdb->base_prefix . 'sabre_table');
	else
		define('SABRE_TABLE', 'sabre_table');

	define('SABREDIR', dirname(plugin_basename(__FILE__)));
	define('SABREPATH', WP_CONTENT_DIR . '/plugins/' . SABREDIR . '/');
	define('SABREURL', WP_CONTENT_URL . '/plugins/' . SABREDIR . '/');

	require_once(ABSPATH . 'wp-admin/includes/user.php');
	require_once(SABREPATH.'classes/sabre_class.php');
}

if (class_exists("Sabre")) {
	global $mySabre;
	$mySabre = new Sabre;
}

/***********************************************************************/
/* Redefine user notification function                                 */
/***********************************************************************/



if ( !function_exists('wp_new_user_notification') ) :
/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since 2.0.0
 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
 *
 * @param int    $user_id User ID.
 * @param string $notify  Optional. Type of notification that should happen. Accepts 'admin' or an empty
 *                        string (admin only), or 'both' (admin and user). The empty string value was kept
 *                        for backward-compatibility purposes with the renamed parameter. Default empty.
 */
function wp_new_user_notification( $user_id, $notify = '' ) {
	global $wpdb;
	$user = get_userdata( $user_id );
	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
	if ( 'admin' === $notify || empty( $notify ) ) {
		return;
	}
	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );
	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );
	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
	$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . "\r\n\r\n";
//	$message .= wp_login_url() . "\r\n";
	$message .= __('Make sure you click the RESET PASSWORD button to save your password.') . "\r\n\r\n";
	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}
endif;


/*
if ( !function_exists('wp_new_user_notification') && isset($mySabre) ) :
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	global $wpdb, $mySabre;

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	$message  = sprintf(__('New user registration on your site %s:', 'sabre'), get_option('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'sabre'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s', 'sabre'), $user_email) . "\r\n";

	$sabre_opt = $mySabre->get_option('sabre_opt');

	$mail_from = "From: ";
	$mail_from .= (!empty($sabre_opt['mail_from_name']) ? $sabre_opt['mail_from_name'] : get_option('blogname')) . " <";
	$mail_from .= (!empty($sabre_opt['mail_from_mail']) ? $sabre_opt['mail_from_mail'] : get_option('admin_email')) . ">";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'sabre'), get_option('blogname')), $message, $mail_from);

	if ( empty($plaintext_pass) )
		return;

	if ($sabre_opt['user_pwd'] == 'true') {
		$plaintext_pass = $_POST['user_pwd1'];
		wp_set_password($plaintext_pass, $user_id);
		delete_user_setting('default_password_nag', $user_id);
		update_user_option($user_id, 'default_password_nag', false, true);
		}

	$message  = sprintf(__('Thank you for registering on %s', 'sabre'), get_option('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'sabre'), $user_login) . "\r\n";
	if ($sabre_opt['user_pwd'] == 'true')
		$message .= __('Use the password defined during your registration', 'sabre') . "\r\n\r\n";
	else $message .= sprintf(__('Password: %s', 'sabre'), $plaintext_pass) . "\r\n\r\n";
	if ($sabre_opt['enable_confirm'] == 'user') {
		$message .= sprintf(__ngettext('You must confirm your registration within %s day by following the link below', 'You must confirm your registration within %s days by following the link below', $sabre_opt['period'], 'sabre'), $sabre_opt['period']) . "\r\n\r\n";
		$message .= get_option('siteurl') . "/wp-login.php?sabre_confirm=" . md5($_POST['sabre_id']) . "\r\n";
	}
	elseif ($sabre_opt['enable_confirm'] == 'admin') {
		$message .= __('Your registration has to be validated by the administrator before you can sign on the site. You will be advised by e-mail upon completion.', 'sabre') . "\r\n\r\n";
		$message .= get_option('siteurl') . "/wp-login.php\r\n";
	}
	else {
		$message .= get_option('siteurl') . "/wp-login.php\r\n";
	}

	wp_mail($user_email, sprintf(__('[%s] - Your registration information', 'sabre'), get_option('blogname')), $message, $mail_from);

}
endif;
*/
?>
