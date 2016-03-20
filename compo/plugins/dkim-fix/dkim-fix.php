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
	$phpmailer->DKIM_domain = 'ludumdare.com';
	$phpmailer->DKIM_private = '/mnt/data/lsws/dkim.key';
	$phpmailer->DKIM_selector = 'mail';
	$phpmailer->DKIM_passphrase = '';
	$phpmailer->DKIM_identity = $phpmailer->From;
	
//	mail(
//		"mikekasprzak@gmail.com",
//		"output",
//		json_encode([
//			'from' => $phpmailer->From,
//			'subject' => $phpmailer->Subject,
//			'message' => $phpmailer->Body,
//		])
//	);
	
//	if (strlen($phpmailer->Sender)==0) {
//		$phpmailer->Sender = $phpmailer->From;
//		$phpmailer->AddReplyTo($phpmailer->From);
//	}
}
