#!/usr/bin/php
<?php

// Only allow script to execute if via PHP-CLI (i.e. Cron Job) //
if (php_sapi_name() !== "cli") {
	// Jurassic Park //
	echo "Clever girl.\n";
	exit(1);
}

echo "Greetings!\n";

?>