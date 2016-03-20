<?php
/*
Plugin Name: DKIM Fix
Plugin URI: 
Version: v0.1
Author: Mike Kasprzak
Description: Fix for broken DKIM headers
*/

add_action( 'phpmailer_init', 'phpmailer_dkim_fix' );
function phpmailer_dkim_fix( $phpmailer ) {
	if (strlen($phpmailer->Sender)==0) {
		$phpmailer->Sender = $phpmailer->From;
		$phpmailer->AddReplyTo($phpmailer->From);
	}
}
