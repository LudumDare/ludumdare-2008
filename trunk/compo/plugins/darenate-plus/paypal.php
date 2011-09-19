<?php
	/*
	//************************************************************
	//************************************************************
	//**  Bugs fixed by...										**
	//**  														**
	//**  Copyright Encentra 2011								**
	//**  www.encentra.se										**
	//**  consultant: Johan Rufus Lagerström					**
	//************************************************************
	//************************************************************
	*/

#########################################################
#                                                       #
#  File            : PayPal.php                         #
#  Version         : 1.9                                #
#  Last Modified   : 12/15/2005                         #
#  Copyright       : This program is free software; you #
# can redistribute it and/or #modify it under the terms #
# of the GNU General Public License as published by the #
# Free Software Foundation								#
# See the #GNU General Public License for more details. #
#    			DO NOT REMOVE LINK 						#
# Visit: http://www.belahost.com for updates/scripts    #
#########################################################
#    THIS SCRIPT IS FREEWARE AND IS NOT FOR RE-SALE     #
#########################################################

require("../../../wp-blog-header.php");
global $wpdb;
$dplus = get_option('DonatePlus');
$email_IPN_results = get_option('IPN_email'); 
$tmp_nl = "\r\n";

if(class_exists('DonatePlus'))$donateplus = new DonatePlus();

#1 = Live on PayPal Network 
#2 = Testing with www.BelaHost.com/pp
#3 = Testing with the PayPal Sandbox
$verifymode 	= $dplus['testing_mode']; # be sure to change value for testing/live!
# Send notifications to here
$send_mail_to 	= $dplus['ty_email'];
# subject of messages
$sysname 		= "Donate Plus - Paypal IPN Transaction";
# Your primary PayPal e-mail address
//$paypal_email 	= $dplus['paypal_email'];
# Your sendmail path
//$mailpath 		= "/usr/sbin/sendmail -t";
#the name you wish to see the messages from
//$from_name 		= $dplus['ty_name'];
#the emails will be coming from
//$from_email 	= $dplus['ty_email'];


# Convert Super globals For backward compatibility
if(phpversion() <= "4.0.6") {$_POST=($HTTP_POST_VARS);}

# Check for IPN post if non then return 404 error.
if (!$_POST['txn_type']){
	if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [ERROR - 404]","IPN Fail: 404 error!","",__LINE__);
	header("Status: 404 Not Found");
	die();
}else{
	header("Status: 200 OK");
}

# Now we Read the Posted IPN
$postvars = array();

//print_r($_POST);
foreach ($_POST as $ipnvars => $ipnvalue){
	$postvars[] = $ipnvars; $postvals[] = $ipnvalue;
}
# Now we ADD "cmd=_notify-validate" for Post back Validation
$postipn 	= 'cmd=_notify-validate'; 
$orgipn 	= '<b>Posted IPN variables in order received:</b><br><br>';
# Prepare for validation
for($x=0; $x < count($postvars); $x++){ 
	$y			= $x+1; 
	$postkey 	= $postvars[$x]; 
	$postval 	= $postvals[$x]; 
	$postipn	.= "&".$postkey."=".urlencode($postval);  
	$orgipn		.= "<b>#".$y."</b> Key: ".$postkey." <b>=</b> ".$postval."<br>";
}


if($verifymode == 1){		//1 = Live on PayPal Network
	## Verify Mode 1: This will post the IPN variables to the Paypal Network for Validation
	$port = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	//$port = fsockopen ("paypal.com", 80, $errno, $errstr, 30);
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n".
	"Host: www.paypal.com\r\n".
	"Content-Type: application/x-www-form-urlencoded\r\n".
	"Content-Length: ".strlen($postipn)."\r\n\r\n";
}elseif ($verifymode == 2){	//2 = Testing with www.BelaHost.com/pp
	## Verify Mode 2: This will post the IPN variables to Belahost Test Script for validation
	## Located at www.belahost.com/pp/index.php
	$port = fsockopen ("www.belahost.com", 80, $errno, $errstr, 30);
	$header = "POST /pp/ HTTP/1.0\r\n".
	"Host: www.belahost.com\r\n".
	"Content-Type: application/x-www-form-urlencoded\r\n".
	"Content-Length: ".strlen($postipn)."\r\n\r\n";        
}elseif ($verifymode == 3){	//3 = Testing with the PayPal Sandbox
	$port = fsockopen ("ssl://www.sandbox.paypal.com", 443, $errno, $errstr, 30);
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n".
	"Host: www.sandbox.paypal.com\r\n".
	"Content-Type: application/x-www-form-urlencoded\r\n".
	"Content-Length: ".strlen($postipn)."\r\n\r\n";
}else{ 
	$error=1; 
	//echo "CheckMode: ".$verifymode." is invalid!";
	if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [ERROR - Misc]","Fail: CheckMode: ".$verifymode." is invalid!","",__LINE__);
	die(); 
}


# Error at this point: If at this point you need to check your Firewall or your Port restrictions?
if (!$port && !$error){
	//echo "Problem: Error Number: ".$errno." Error String: ".$errstr;
	#Here is a small email notification so you know if your system ever fails			
	if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [ERROR - Misc]","Your Paypal System failed due to $errno and string $errstr","",__LINE__);			
	die();
}else{
	# If No Errors to this point then we proceed with the processing.
	# Open port to paypal or test site and post Varibles.
	fputs ($port, $header.$postipn);
	while (!feof($port)){
        $reply = fgets ($port, 1024);
        $reply = trim ($reply);
	}
	
	# Prepare a Debug Report
	$ipnreport = $orgipn."<br><b>"."IPN Reply: ".$reply."</b>";

	# Buyer Information
	$address_city 			= $_POST['address_city'];
	$address_country 		= $_POST['address_country'];
	$address_country_code 	= $_POST['address_country_code'];
	$address_name 			= $_POST ['address_name'];
	$address_state 			= $_POST['address_state'];
	$address_status 		= $_POST['address_status'];
	$address_street 		= $_POST['address_street'];
	$address_zip 			= $_POST['address_zip'];
	$first_name 			= $_POST['first_name'];
	$last_name 				= $_POST['last_name'];
	$payer_business_name 	= $_POST['payer_business_name'];
	$payer_email 			= $_POST['payer_email'];
	$payer_id 				= $_POST['payer_id'];
	$payer_status 			= $_POST['payer_status'];
	$residence_country 		= $_POST['residence_country'];
	# Below Instant BASIC Payment Notifiction Variables
	$business 				= $_POST['business'];
	$item_name 				= $_POST['item_name'];
	$item_number 			= $_POST['item_number'];
	$quantity 				= $_POST['quantity'];
	$receiver_email 		= $_POST['receiver_email'];
	$receiver_id 			= $_POST['receiver_id'];
	#Advanced and Customer information
	$custom 				= $_POST['custom'];
	$invoice 				= $_POST['invoice'];
	$memo 					= $_POST['memo'];
	$option_name1 			= $_POST['option_name1'];		//name
	$option_name2 			= $_POST['option_name2'];
	$option_selection1 		= $_POST['option_selection1'];	//email
	$option_selection2 		= $_POST['option_selection2'];	//comment
	$tax 					= $_POST['tax'];
	#Website Payment Pro and Other IPN Variables
	$auth_id 				= $_POST['auth_id'];
	$auth_exp 				= $_POST['auth_exp'];
	$auth_amount 			= $_POST['auth_amount'];
	$auth_status 			= $_POST['auth_status'];
	# Shopping Cart Information
	$mc_gross 				= $_POST['mc_gross'];
	$mc_handling 			= $_POST['mc_handling'];
	$mc_shipping 			= $_POST['mc_shipping'];
	$num_cart_items 		= $_POST['num_cart_items'];
	# Other Transaction Information
	$parent_txn_id 			= $_POST['parent_txn_id'];
	$payment_date 			= $_POST['payment_date'];
	$payment_status 		= $_POST['payment_status'];
	$payment_type 			= $_POST['payment_type'];
	$pending_reason 		= $_POST['pending_reason'];
	$reason_code			= $_POST['reason_code'];
	$remaining_settle		= $_POST['remaining_settle'];
	$transaction_entity		= $_POST['transaction_entity'];
	$txn_id					= $_POST['txn_id'];
	$txn_type				= $_POST['txn_type'];
	# Currency and Exchange Information
	$exchange_rate			= $_POST['exchange_rate'];
	$mc_currency			= $_POST['mc_currency'];
	$mc_fee					= $_POST['mc_fee'];
	$payment_fee			= $_POST['payment_fee'];
	$payment_gross			= $_POST['payment_gross'];
	$settle_amount			= $_POST['settle_amount'];
	$settle_currency		= $_POST['settle_currency'];
	# Auction Information 
	$for_auction			= $_POST['for_auction'];
	$auction_buyer_id		= $_POST['auction_buyer_id'];
	$auction_closing_date	= $_POST['auction_closing_date'];
	$auction_multi_item		= $_POST['auction_multi_item'];
	# Below are Subscription - Instant Payment Notifiction Variables
	$subscr_date			= $_POST['subscr_date'];
	$subscr_effective		= $_POST['subscr_effective'];
	$period1				= $_POST['period1'];
	$period2				= $_POST['period2'];
	$period3				= $_POST['period3'];
	$amount1				= $_POST['amount1'];
	$amount2				= $_POST['amount2'];
	$amount3				= $_POST['amount3'];
	$mc_amount1				= $_POST['mc_amount1'];
	$mc_amount2				= $_POST['mc_amount2'];
	$mc_amount3				= $_POST['mc_amount3'];
	$recurring				= $_POST['recurring'];
	$reattempt				= $_POST['reattempt'];
	$retry_at				= $_POST['retry_at'];
	$recur_times			= $_POST['recur_times'];
	$username				= $_POST['username'];
	$password				= $_POST['password'];
	$subscr_id				= $_POST['subscr_id'];
	# Complaint Variables Used when paypal logs a complaint
	$case_id				= $_POST['case_id'];
	$case_type				= $_POST['case_type'];
	$case_creation_date		= $_POST['case_creation_date'];
	#Last but not least
	$notify_version			= $_POST['notify_version'];
	$verify_sign			= $_POST['verify_sign'];


	#There are certain variables that we will not store for cart since they are dynamic                    
	#such as mc_gross_x as they will be forever changing/increasing your script must check these

	#IPN was Confirmed as both Genuine and VERIFIED
	if(!strcmp($reply, "VERIFIED")){
		/* Now that IPN was VERIFIED below are a few things which you may want to do at this point.
		 1. Check that the "payment_status" variable is: "Completed"
		 2. If it is Pending you may want to wait or inform your customer?
		 3. You should Check your datebase to ensure this "txn_id" or "subscr_id" is not a duplicate. txn_id is not sent with subscriptions!
		 4. Check "payment_gross" or "mc_gross" matches match your prices!
		 5. You definately want to check the "receiver_email" or "business" is yours. 
		 6. We have included an insert to database for table paypal_ipn
		 */
		$split = explode(':', $item_number);
		$display = $split[0];
		$uID = $split[1];
		$url = 'http://'.str_replace('http://','',$option_name2);

		if(!$mc_gross)$mc_gross = $payment_gross;
		$table_name = $wpdb->prefix."donations";
		
		//kontrollera om transaktionen redan finns sparad
		$tmp_txn_id 	= $wpdb->escape($txn_id);
		$tmp_count 		= $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE txn_id='$tmp_txn_id';")); 
		
		
		//if($payment_status	== 'Completed'){
		if($tmp_count == 0){
			//all pin:s som kommer in tolkar vi som complete
			$tmp_payment_status = "Completed";
			//USE SECURE INSERT!
			$wpdb->query(
				$wpdb->prepare("INSERT INTO $table_name
				( name, email, url, comment, display, amount, currency, date, user_id, status, txn_id )
				VALUES ( %s, %s, %s, %s, %d, %s, %s, %s, %d, %s, %s )", 
			    $option_name1, $option_selection1, $url, strip_tags($option_selection2), $display, $mc_gross, $mc_currency, date('Y-m-d H:i:s'), $uID, $tmp_payment_status, $txn_id )
		    );
			//send payer thank you email about where to download
			global $currency;
			$subject 	= stripslashes($dplus['ty_subject']);
			$prefix 	= $currency[$mc_currency]['symbol'];
			$amount 	= $prefix.$mc_gross.' '.$mc_currency;
			$payer_msg 	= nl2br($donateplus->TagReplace(stripslashes($dplus['ty_emailmsg']), $option_name1, $amount));
			//$payer_msg = utf8_encode($payer_msg);
			//echo '<br />'.$payer_msg;
			$headers  = 'MIME-Version: 1.0'."\r\n";
			//$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= 'From: '.$dplus['ty_name'].' <'.$dplus['ty_email'].'>'."\r\n";
			wp_mail($option_selection1, $subject, $payer_msg, $headers);
			//wp_mail($notify_email, 'New Donation Recieved!', "Donation from $option_name1 for $payment_amount");
			//echo $postquery;
			if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [PIN - Completed]","\n Verified IPN Transaction [Completed] \n \n$ipnreport\n","",__LINE__);
		}else{
			//not "Completed"
			//tar bort nedan, annars trilalr det in 10 mail per donation
			//send_mail($send_mail_to,$sysname." [PIN - $payment_status]","\n Verified IPN Transaction [$payment_status] \n \n$ipnreport\n","",__LINE__);
		}

			
	}elseif(!strcmp($reply, "INVALID")){	# IPN was Not Validated as Genuine and is INVALID
		/* Now that IPN was INVALID below are a few things which you may want to do at this point.
		 1. Check your code for any post back Validation problems!
		 2. Investigate the Fact that this Could be an attack on your script IPN!
		 3. If updating your DB, Ensure this "txn_id" is Not a Duplicate!
		*/

		# Remove Echo line below when live
		//echo $ipnreport;
		if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [ERROR - Invalid]","Invalid IPN Transaction".$tmp_nl.$tmp_nl.$ipnreport,"",__LINE__);
	
	}else{	#ERROR
		# If your script reaches this point there is a problem. Communication from your script to test/paypal pages could be 1 reason.
		echo $ipnreport;
		if( $email_IPN_results ) send_mail($send_mail_to,$sysname." [ERROR - Misc]","FATAL ERROR No Reply at all".$tmp_nl.$tmp_nl.$ipnreport,"",__LINE__);
	}


	#Terminate the Socket connection and Exit
	fclose ($port); 
	die();
}

      
      
      
      
      
      
/* =================================
         Below are functions
   ================================= */
   
# Email function
function send_mail($to, $subj, $body, $headers="",$tmp_line=0){
	//global
	global $tmp_nl;	

	//var_dump till en sträng
	$posts = var_export($_POST, true);
	
	//body
	$tmp_body 	= "===================================".$tmp_nl.
				$subj." [line: $tmp_line]".$tmp_nl.
				"===================================".$tmp_nl.
				$body.$tmp_nl.
				$tmp_nl.
				"===================================".$tmp_nl.
				$posts;

	//skickar mail
	wp_mail($to, $subj, $tmp_body, $headers);
	
	/*
    global $from_name, $from_email, $mailpath;

# E-mail Configuration
	$announce_subject = "$subj";
	$announce_from_email = "$from_email";
	$announce_from_name = "$from_name";
	$announce_to_email = "$to";
	$MP = "$mailpath"; 
	$spec_envelope = 1;
	# End email config
# Access Sendmail
# Conditionally match envelope address
	if(isset($spec_envelope))
	{
	$MP .= " -f $announce_from_email";
	}
	$fd = popen($MP,"w"); 
	fputs($fd, "To: $announce_to_email\n"); 
	fputs($fd, "From: $announce_from_name <$announce_from_email>\n");
	fputs($fd, "Subject: $announce_subject\n"); 
	fputs($fd, "X-Mailer: MyPayPal_Mailer\n");
	fputs($fd, "Content-Type: text/html\n");
	fputs($fd, $body); # $body will be sent when the function is used
	pclose($fd);
	*/
}

?>