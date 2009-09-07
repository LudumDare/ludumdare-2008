<?php

session_start(); 

/*

Plugin Name: WP_UnityObject
Plugin URI: http://unity3d.com/support/resources/assets/wp_unityobject
Description: A plugin that provides the ability to added Unity Web Player content to your WordPress blog posts. (v1.0 | May 10, 2009)
Version: 1.0
Author: Tom Higgins
Author URI: http://blogs.unity3d.com/author/tom/

Copyright 2009 Unity Technologies - http://unity3d.com/
Released under the GNU General Public License - http://www.gnu.org/licenses/gpl.html

*/

function WP_ParseEntry ($aEntry) {

	// Find/replace all UnityObject tags in the entry
	$tPattern = "/(\[WP_UnityObject.*\/\])/";
	$tNewEntry = preg_replace_callback($tPattern,"WP_WriteTags",$aEntry);
	
	// Return the new entry string
	return $tNewEntry;

}

function WP_WriteTags ($aMatchArray) {

	// Initialize the UnityObject parameter data with default values
	$_SESSION["uoparams"]["src"] = "";
	$_SESSION["uoparams"]["id"] = "Unity";
	$_SESSION["uoparams"]["width"] = "320";
	$_SESSION["uoparams"]["height"] = "240";
	$_SESSION["uoparams"]["version"] = "2";
	$_SESSION["uoparams"]["altimage"] = "";
	$_SESSION["uoparams"]["backgroundcolor"] = "";
	$_SESSION["uoparams"]["bordercolor"] = "";
	$_SESSION["uoparams"]["textcolor"] = "";
	$_SESSION["uoparams"]["logoimage"] = "";
	$_SESSION["uoparams"]["progressbarimage"] = "";
	$_SESSION["uoparams"]["progressframeimage"] = "";
	$_SESSION["uoparams"]["installimage"] = "";
	$_SESSION["uoparams"]["disablecontextmenu"] = "";
	$_SESSION["uoparams"]["disableexternalcall"] = "";
	$_SESSION["uoparams"]["disablefullscreen"] = "";
	$_SESSION["uoparams"]["css"] = "";
				
	// Pull the UnityObject string from the matches array and split it into a parameters array
	$tUOString = $aMatchArray[0];
	$tUOString = str_replace("[WP_UnityObject", "", $tUOString);
	$tUOString = str_replace("/]", "", $tUOString);
	$tUOString = trim($tUOString);
	$tUOParams = split(" ", $tUOString);

	// Walk the UnityObject parameters and set any matching tag parameter values
	for ($i = 0; $i < count($tUOParams); $i++) {
	
		// Pull the current parameter string
		$tParamString = $tUOParams[$i];
		$tParamChunks = explode("=", $tParamString);
		$tParamName = trim($tParamChunks[0]);
		$tParamValue = trim($tParamChunks[1]);
		$tParamValue = str_replace("\"", "", $tParamValue);
		$_SESSION["uoparams"][$tParamName] = $tParamValue;
	
	}
	
	// Check if this is currently a feed display or not
	if (!preg_match("/(\/\?feed=|\/feed)/i",$_SERVER['REQUEST_URI'])) {
	
		// Initialize the iframe src value
		if ($_SESSION["uoparams"]["altimage"] == "") {
			$tSrc = get_settings('siteurl') . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . "/php/WP_Content.php";
		} else {
			$tSrc = get_settings('siteurl') . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . "/php/WP_Image.php";
		}
		
		// Initialize the display string
		$tHeight = $_SESSION["uoparams"]["height"];
		if ($_SESSION["uoparams"]["altimage"] != "") { $tHeight = (string)((int)$_SESSION["uoparams"]["height"] + 15); }
		$tWidth = $_SESSION["uoparams"]["width"];
		$tDisplayString = "<iframe id=\"" . $tID . "\" name=\"" . $tID . "\" src=\"" . $tSrc . "\" marginheight=\"0\" frameborder=\"0\" height=\"" . $tHeight . "\" width=\"" . $tWidth . "\" scrolling=\"no\"></iframe>";
	
	} else {
	
		// Display a placeholder string
		$tDisplayString = "<p><em>Please view the full post to see the Unity content.</em></p>";
	
	}

	
	// Return the display string
	return $tDisplayString;
	
}

// Enable a filter to find/replace each UnityObject block
add_filter("the_content", "WP_ParseEntry", 1, 1);

?>