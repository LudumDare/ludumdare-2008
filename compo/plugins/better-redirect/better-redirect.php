/**
 * Redirect non-admins to the homepage after logging into the site.
 *
 * @since 	1.0
 */
function acme_login_redirect( $redirect_to, $request, $user  ) {
	if ( $request )
		return $request;
	return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
}
add_filter( 'login_redirect', 'acme_login_redirect', 10, 3 );

