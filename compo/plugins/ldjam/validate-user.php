<?php
// Check wordpress credentials //

// Set Cookie //
setcookie( "lusha", "1234", 2*24*60*60, "/", "theme.ludumdare.com" );

// Redirect //
//header("Location: http://theme.ludumdare.com");

echo '<!doctype html>';
echo '<html><head><meta http-equiv="Location" content="http://example.com/"></head>';
echo '<body><a href="http://theme.ludumdare.com">http://theme.ludumdare.com</a></body></html>';
die();
