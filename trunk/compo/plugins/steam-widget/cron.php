#!/usr/bin/php
<?php

// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	// Jurassic Park //
	echo "Clever girl.\n";
	echo "<br /><br /><img src='http://img1.wikia.nocookie.net/__cb20140408111011/jurassicpark/images/5/53/Raptor_-_Clever_Girl.gif' />";
	exit(1);
}

echo "Greetings!\n";

?>