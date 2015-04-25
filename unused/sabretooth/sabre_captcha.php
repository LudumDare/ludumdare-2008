<?php
/*
QuickCaptcha 1.0 - A bot-thwarting text-in-image web tool.
Copyright (c) 2006 Web 1 Marketing, Inc.
*/

/* 
Description : A function with a very simple but powerful xor method to encrypt 
              and/or decrypt a string with an unknown key. Implicitly the key is 
              defined by the string itself in a character by character way. 
              There are 4 items to compose the unknown key for the character 
              in the algorithm 
              1.- The ascii code of every character of the string itself 
              2.- The position in the string of the character to encrypt 
              3.- The length of the string that include the character 
              4.- Any special formula added by the programmer to the algorithm 
                  to calculate the key to use 
*/ 
FUNCTION Encrypt_Decrypt($Str_Message) { 
//Function : encrypt/decrypt a string message v.1.0  without a known key 
//Author   : Aitor Solozabal Merino (spain) 
//Email    : aitor-3@euskalnet.net 
//Date     : 01-04-2005 
    $Len_Str_Message=STRLEN($Str_Message); 
    $Str_Encrypted_Message=""; 
    FOR ($Position = 0;$Position<$Len_Str_Message;$Position++){ 
        // long code of the function to explain the algoritm 
        //this function can be tailored by the programmer modifyng the formula 
        //to calculate the key to use for every character in the string. 
        $Key_To_Use = (($Len_Str_Message+$Position)+1); // (+5 or *3 or ^2) 
        //after that we need a module division because can´t be greater than 255 
        $Key_To_Use = (255+$Key_To_Use) % 255; 
        $Byte_To_Be_Encrypted = SUBSTR($Str_Message, $Position, 1); 
        $Ascii_Num_Byte_To_Encrypt = ORD($Byte_To_Be_Encrypted); 
        $Xored_Byte = $Ascii_Num_Byte_To_Encrypt ^ $Key_To_Use;  //xor operation 
        $Encrypted_Byte = CHR($Xored_Byte); 
        $Str_Encrypted_Message .= $Encrypted_Byte; 
        
        //short code of  the function once explained 
        //$str_encrypted_message .= chr((ord(substr($str_message, $position, 1))) ^ ((255+(($len_str_message+$position)+1)) % 255)); 
    } 
    RETURN $Str_Encrypted_Message; 
} //end function 

$cnum = Encrypt_Decrypt(base64_decode($_GET['sabre_id']));
$acceptedChars = $_GET['acceptedChars'];
$stringlength = $_GET['stringlength'];
$contrast = $_GET['contrast'];
$num_polygons = $_GET['num_polygons']; // Number of triangles to draw.  0 = none
$num_ellipses = $_GET['num_ellipses'];  // Number of ellipses to draw.  0 = none
$num_lines = $_GET['num_lines'];  // Number of lines to draw.  0 = none
$num_dots = $_GET['num_dots'];  // Number of dots to draw.  0 = none
$min_thickness = $_GET['min_thickness'];  // Minimum thickness in pixels of lines
$max_thickness = $_GET['max_thickness'];  // Maximum thickness in pixles of lines
$min_radius = $_GET['min_radius'];  // Minimum radius in pixels of ellipses
$max_radius = $_GET['max_radius'];  // Maximum radius in pixels of ellipses
$object_alpha = $_GET['object_alpha']; // How opaque should the obscuring objects be. 0 is opaque, 127 is transparent.
$white_bg = $_GET['white_bg']; // White background. false = black, true = white


/*------------------------------------------------*/
$min_thickness = max(1,$min_thickness);
$max_thickness = min(20,$max_thickness);
$min_radius *= 3;// Make radii into height/width
$max_radius *= 3;// Make radii into height/width
$contrast = 255 * ($contrast / 100.0);
$o_contrast = 1.3 * $contrast;
$width = 20 * imagefontwidth (5);
$height = 4 * imagefontheight (5);
$image = imagecreatetruecolor ($width, $height);
imagealphablending($image, true);
if ($white_bg == 'true') {
$white = imagecolorallocatealpha($image,255,255,255,0);
imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $white);
	} else $black = imagecolorallocatealpha($image,0,0,0,0);

$rotated = imagecreatetruecolor (70, 70);
$x = 0;

for ($i = 0; $i < $stringlength; $i++) {
	$buffer = imagecreatetruecolor (20, 20);
	$buffer2 = imagecreatetruecolor (40, 40);
	
	// Get a random color
	$red = mt_rand(0,255);
	$green = mt_rand(0,255);
	$blue = 255 - sqrt($red * $red + $green * $green);
	$color = imagecolorallocate ($buffer, $red, $green, $blue);
	
	// Create character
	imagestring($buffer, 5, 0, 0, $cnum{$i}, $color);
	
	// Resize character
	imagecopyresized ($buffer2, $buffer, 0, 0, 0, 0, 25 + mt_rand(0,12), 25 + mt_rand(0,12), 20, 20);
	
	// Rotate characters a little
	if (function_exists('imagerotate'))
		$rotated = imagerotate($buffer2, mt_rand(-25, 25),imagecolorallocatealpha($buffer2,0,0,0,0));
	else
		imagecopymerge ($rotated, $buffer2, 15, 15, 0, 0, 40, 40, 100); 
	imagecolortransparent ($rotated, imagecolorallocatealpha($rotated,0,0,0,0));
	
	// Move characters around a little
	$y = mt_rand(1, 3);
	$x += mt_rand(2, 6); 
	imagecopymerge ($image, $rotated, $x, $y, 0, 0, 40, 40, 100);
	$x += 22;

	imagedestroy ($buffer); 
	imagedestroy ($buffer2); 
}

imagedestroy ($rotated);
if ($num_polygons > 0) for ($i = 0; $i < $num_polygons; $i++) {
	$vertices = array (
		mt_rand(-0.25*$width,$width*1.25),mt_rand(-0.25*$width,$width*1.25),
		mt_rand(-0.25*$width,$width*1.25),mt_rand(-0.25*$width,$width*1.25),
		mt_rand(-0.25*$width,$width*1.25),mt_rand(-0.25*$width,$width*1.25)
	);
	$color = imagecolorallocatealpha ($image, mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), $object_alpha);
	imagefilledpolygon($image, $vertices, 3, $color);  
}

if ($num_ellipses > 0) for ($i = 0; $i < $num_ellipses; $i++) {
	$x1 = mt_rand(0,$width);
	$y1 = mt_rand(0,$height);
	$color = imagecolorallocatealpha ($image, mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), $object_alpha);
//	$color = imagecolorallocate($image, mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), mt_rand(0,$o_contrast));
	imagefilledellipse($image, $x1, $y1, mt_rand($min_radius,$max_radius), mt_rand($min_radius,$max_radius), $color);  
}

if ($num_lines > 0) for ($i = 0; $i < $num_lines; $i++) {
	$x1 = mt_rand(-$width*0.25,$width*1.25);
	$y1 = mt_rand(-$height*0.25,$height*1.25);
	$x2 = mt_rand(-$width*0.25,$width*1.25);
	$y2 = mt_rand(-$height*0.25,$height*1.25);
	$color = imagecolorallocatealpha ($image, mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), $object_alpha);
	imagesetthickness ($image, mt_rand($min_thickness,$max_thickness));
	imageline($image, $x1, $y1, $x2, $y2 , $color);  
}

if ($num_dots > 0) for ($i = 0; $i < $num_dots; $i++) {
	$x1 = mt_rand(0,$width);
	$y1 = mt_rand(0,$height);
	$color = imagecolorallocatealpha ($image, mt_rand(0,$o_contrast), mt_rand(0,$o_contrast), mt_rand(0,$o_contrast),$object_alpha);
	imagesetpixel($image, $x1, $y1, $color);
}

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
  
?>