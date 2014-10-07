#!/usr/bin/php
<?php

if (php_sapi_name() == "cli") {
	echo "Greetings\n";
}
else {
	echo "Nope!\n";
}

?>