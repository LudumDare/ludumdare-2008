<?php
// Check wordpress credentials //

// Set Cookie //
setcookie( "lusha", "1234", 2*24*60*60, "/", "theme.ludumdare.com" );

// Redirect //
header("Location: http://theme.ludumdare.com");
die();
