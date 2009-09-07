<?php session_start();  ?>
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
				
				// Verify that a src value was provided
				if ($_SESSION["uoparams"]["src"] != "") {
				
					// Initialize the content string
					$tContentString =  "<script type=\"text/javascript\">";
					$tContentString .= "  var tUnityObject = new UnityObject(";
					$tContentString .= "\"" . $_SESSION["uoparams"]["src"] . "\"";
					$tContentString .= ", \"" . $_SESSION["uoparams"]["id"] . "\"";
					$tContentString .= ", \"" . $_SESSION["uoparams"]["width"] . "\", \"" . $_SESSION["uoparams"]["height"] . "\"";
					$tContentString .= ", \"" . $_SESSION["uoparams"]["version"] . "\"";
					if ($_SESSION["uoparams"]["installimage"] != "") { $tContentString .= ", \"\", \"" . $_SESSION["uoparams"]["installimage"] . "\""; }
					$tContentString .= ");";
					
					if ($_SESSION["uoparams"]["backgroundcolor"] != "") { $tContentString .= "tUnityObject.addParam(\"backgroundcolor\", \"" . $_SESSION["uoparams"]["backgroundcolor"] . "\");"; }
					if ($_SESSION["uoparams"]["bordercolor"] != "") { $tContentString .= "tUnityObject.addParam(\"bordercolor\", \"" . $_SESSION["uoparams"]["bordercolor"] . "\");"; }
					if ($_SESSION["uoparams"]["textcolor"] != "") { $tContentString .= "tUnityObject.addParam(\"textcolor\", \"" . $_SESSION["uoparams"]["textcolor"] . "\");"; }
					
					if ($_SESSION["uoparams"]["logoimage"] != "") { $tContentString .= "tUnityObject.addParam(\"logoimage\", \"" . $_SESSION["uoparams"]["logoimage"] . "\");"; }
					if ($_SESSION["uoparams"]["progressbarimage"] != "") { $tContentString .= "tUnityObject.addParam(\"progressbarimage\", \"" . $_SESSION["uoparams"]["progressbarimage"] . "\");"; }
					if ($_SESSION["uoparams"]["progressframeimage"] != "") { $tContentString .= "tUnityObject.addParam(\"progressframeimage\", \"" . $_SESSION["uoparams"]["progressframeimage"] . "\");"; }
					
					if ($_SESSION["uoparams"]["installimage"] != "") { $tContentString .= "tUnityObject.addParam(\"installimage\", \"" . $_SESSION["uoparams"]["installimage"] . "\");"; }
					
					if ($_SESSION["uoparams"]["disablecontextmenu"] != "") { $tContentString .= "tUnityObject.addParam(\"disablecontextmenu\", \"" . $_SESSION["uoparams"]["disablecontextmenu"] . "\");"; }
					if ($_SESSION["uoparams"]["disableexternalcall"] != "") { $tContentString .= "tUnityObject.addParam(\"disableexternalcall\", \"" . $_SESSION["uoparams"]["disableexternalcall"] . "\");"; }
					if ($_SESSION["uoparams"]["disablefullscreen"] != "") { $tContentString .= "tUnityObject.addParam(\"disablefullscreen\", \"" . $_SESSION["uoparams"]["disablefullscreen"] . "\");"; }
					
					$tContentString .= "  tUnityObject.write();";
					$tContentString .= "</script><br />";
					
					if ($_SESSION["uoparams"]["altimage"] != "") {
						$tContentString .= "<div style=\"text-align:right; font-size:9px\">Click <a href=\"WP_Image.php\">here</a> to unload the Unity Web Player content.</div>";
					}
					
					$tContentString .= "<script language=\"text/javascript\">parent.document.getElementById(window.name).focus();</script>";

					// Write out the content string
					echo($tContentString);
				
				}

			?>
        
		</div>
	</body>
</html>