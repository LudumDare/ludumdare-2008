<?php
/*
Plugin Name: DKIM Fix
Plugin URI: 
Version: v0.1
Author: Mike Kasprzak
Description: Fix for broken DKIM headers
*/

//add_action( 'phpmailer_init', 'phpmailer_dkim_fix' );
//function phpmailer_dkim_fix( $phpmailer ) {
//	if (strlen($phpmailer->Sender)==0) {
//		$phpmailer->Sender = $phpmailer->From;
//		$phpmailer->AddReplyTo($phpmailer->From);
//	}
//}


function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	// Ignoring Attachments //
	
	error_log("here we go!");
 
    // Headers
    if ( empty( $headers ) ) {
        $headers = array();
    } else {
        if ( !is_array( $headers ) ) {
            // Explode the headers out, so this function can take both
            // string headers and an array of headers.
            $tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
        } else {
            $tempheaders = $headers;
        }
        $headers = array();
        $cc = array();
        $bcc = array();
 
        // If it's actually got contents
        if ( !empty( $tempheaders ) ) {
            // Iterate through the raw headers
            foreach ( (array) $tempheaders as $header ) {
                if ( strpos($header, ':') === false ) {
                    if ( false !== stripos( $header, 'boundary=' ) ) {
                        $parts = preg_split('/boundary=/i', trim( $header ) );
                        $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
                    }
                    continue;
                }
                // Explode them out
                list( $name, $content ) = explode( ':', trim( $header ), 2 );
 
                // Cleanup crew
                $name    = trim( $name    );
                $content = trim( $content );
 
                switch ( strtolower( $name ) ) {
                    // Mainly for legacy -- process a From: header if it's there
                    case 'from':
                        $bracket_pos = strpos( $content, '<' );
                        if ( $bracket_pos !== false ) {
                            // Text before the bracketed email is the "From" name.
                            if ( $bracket_pos > 0 ) {
                                $from_name = substr( $content, 0, $bracket_pos - 1 );
                                $from_name = str_replace( '"', '', $from_name );
                                $from_name = trim( $from_name );
                            }
 
                            $from_email = substr( $content, $bracket_pos + 1 );
                            $from_email = str_replace( '>', '', $from_email );
                            $from_email = trim( $from_email );
 
                        // Avoid setting an empty $from_email.
                        } elseif ( '' !== trim( $content ) ) {
                            $from_email = trim( $content );
                        }
                        break;
                    case 'content-type':
                        if ( strpos( $content, ';' ) !== false ) {
                            list( $type, $charset_content ) = explode( ';', $content );
                            $content_type = trim( $type );
                            if ( false !== stripos( $charset_content, 'charset=' ) ) {
                                $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
                            } elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
                                $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
                                $charset = '';
                            }
 
                        // Avoid setting an empty $content_type.
                        } elseif ( '' !== trim( $content ) ) {
                            $content_type = trim( $content );
                        }
                        break;
                    case 'cc':
                        $cc = array_merge( (array) $cc, explode( ',', $content ) );
                        break;
                    case 'bcc':
                        $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
                        break;
                    default:
                        // Add it to our grand headers array
                        $headers[trim( $name )] = trim( $content );
                        break;
                }
            }
        }
    }

//    if ( !isset( $from_name ) )
//        $from_name = 'WordPress';
//
//    if ( !isset( $from_email ) ) {
//        // Get the site domain and get rid of www.
//        $sitename = strtolower( $_SERVER['SERVER_NAME'] );
//        if ( substr( $sitename, 0, 4 ) == 'www.' ) {
//            $sitename = substr( $sitename, 4 );
//        }
// 
//        $from_email = 'wordpress@' . $sitename;
//    }

	echo(
		$to,
		$subject,
		$message,
		$headers
	);
}
