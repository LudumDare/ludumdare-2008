<?php
/*
Plugin Name: better-redirect
Plugin URI: http://ludumdare.com/
Description: A better redirect plugin. No stupid stuff. Actually aware of WP. 
Version: 1.0
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: BSD
*/
// - ----------------------------------------------------------------------------------------- - //
function acme_login_redirect( $redirect_to, $request, $user  ) {
	if ( $request )
		return $request;
	return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
}
add_filter( 'login_redirect', 'acme_login_redirect', 10, 3 );

