<?php session_start(); ?>
<html>
	<head>
    	<?php if ($_SESSION["uoparams"]["css"] != ""): ?>
    		<link rel="stylesheet" type="text/css" href="<?php echo($_SESSION["uoparams"]["css"]); ?>" />
        <?php endif; ?>
    	<script src="../js/UnityObject.js" type="text/javascript"></script> 
    </head>
	<body>
    	<div>

			<?php
            
                // Build and display the image string
				$tImageString =  "<a href=\"WP_Content.php\">";
                $tImageString .= "<img src=\"" . $_SESSION["uoparams"]["altimage"] . "\" width=\"" . $_SESSION["uoparams"]["width"] . "\" height=\"" . $_SESSION["uoparams"]["height"] . "\" border=\"0\" />";
				$tImageString .= "</a><br />";
				$tImageString .= "<div style=\"text-align:left; font-size:9px\">This content will require the Unity Web Player</div>.";
                echo($tImageString);
            
            ?>
        
		</div>
	</body>
</html>