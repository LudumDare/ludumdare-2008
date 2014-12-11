<?php

function _compo2_active($params) {
	if (!$params["uid"]) {
		echo "<p class='message'>You must sign in to submit an entry.</p>";
		return _compo2_preview($params);
	}

	$action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
	
	if ($action == "default") {
		$ce = compo2_entry_load($params["cid"],$params["uid"]);
		$action = "edit";
		if ($ce["id"]) { $action = "preview"; }
	}
	
	if ($action == "edit") {
		return _compo2_active_form($params);
	} elseif ($action == "save") {
		return _compo2_active_save($params);
	} elseif ($action == "me") {
		_compo2_preview_me($params);
	} elseif ($action == "preview") {
		return _compo2_preview($params);
	}
}

function _compo2_active_form($params,$uid="",$is_admin=0) {
	if (!$uid) {
		$uid = $params["uid"];
	}
	$ce = compo2_entry_load($params["cid"],$uid);
/*
	if ( current_user_can('edit_others_posts') ) {
		echo "Hey team. Just ignore this for now. Only you can see it. Thanks!<br /><br />";
		var_dump( $ce );
	}
*/
	
	if ($params["locked"] && !$ce["id"]) {
		echo "<p class='warning'>This competition is locked.  No new entries are being accepted.</p>";
		return;
	}
	
	// TODO: Make just one (which means make code to iterate, add "add new" button)
	$links = unserialize($ce["links"]);
	if (!$ce["id"]) {
		$links = array(
			0=>array("title"=>"Web","url"=>""),
			1=>array("title"=>"Windows","url"=>""),
			2=>array("title"=>"OS/X","url"=>""),
			3=>array("title"=>"Linux","url"=>""),
			4=>array("title"=>"Source","url"=>""),
		);
	}

	$settings = unserialize($ce["settings"]);
	
	if (!$is_admin) {
		echo "<p><a href='?action=preview'>Browse entries.</a></p>";
	}
	
	$star = "<span style='color:#f00;font-weight:bold;'>*</span>";
	
	if (!$is_admin) {
		echo "<h3>Edit your Entry</h3>";
	} else {
		echo "<h3>Edit this Entry</h3>";
	}
	
	echo "
	<style>
		.edit form {
			text-align:left;
		}
		.edit h2 {
			margin-top:10px;
			margin-bottom:0;
		}
		
		.edit .hidden {
			display:none;
		}
		
		.edit .button {
			font-size:24px;
			font-weight:bold;
		}
	</style>
	";
	
	echo "<div class='edit'>";
	
	if ($ce["disabled"]) {
		echo "<div class='warning'>This entry is disabled.</div>";
	} else {
		if ($ce["id"] != "" && !$ce["active"]) {
			echo "<div class='warning'>Your entry is not complete.</div>";
		}
	}

	$link = "?action=save";
	if ($is_admin) { $link .= "&admin=1&uid=$uid"; }

	// TIPS //
	echo "<div>".$params["tips"]."</div>";


	echo "<form method='post' action='$link' enctype='multipart/form-data'>";
	echo "<input type='hidden' name='formdata' value='1'>";
	
	
	echo "<h2>Name {$star}</h2>";    
	echo "<input type='text' name='title' value=\"".htmlentities($ce["title"])."\" size=60>";
	
	////////////////////////////////////////////////////////////////////////////
	// Handle the entry type.
	////////////////////////////////////////////////////////////////////////////
	/*
	@$etype = $ce["etype"];
	$opts = false; $default = "";
	if ($params["gamejam"] == 1 || $params["init"] != 0) { // if we're in a gamejam
		if ($params["state"] == "active") { // and we're active, show the options
			$opts = true;
		} else { // but if we're not active, the default is gamejam
			$default = "gamejam";
		}
	} else { // non-gamejam, we're always compo
		$default = "compo";
	}
	if ($is_admin) { $opts = true; }
	if (!strlen($etype)) { $etype = $default; } // set the default
	*/
	
	$opts = true;
	$divs = $params["opendivs"];
	
	// Since parsing can be a little wonky, lets make sure there are no blank events //
	foreach( $divs as $key => $val ) {
		if ($val === "" ) {
			unset($divs[$key]);
		}
	}
	array_values($divs);

	@$etype = $ce["etype"];
	if (strlen($etype)) {
		if (!in_array($etype,$divs)) {
			array_unshift($divs,$etype);
		}
	}
	if ($is_admin) { $divs = $params["divs"]; }
	
	echo '
	<script>
		function c2_addclass( el, className ) {
			if (el.classList)
				el.classList.add(className);
			else
				el.className += " " + className;
		}
		
		function c2_removeclass( el, className ) {
			if (el.classList)
				el.classList.remove(className);
			else
				el.className = el.className.replace(new RegExp("(^|\\b)" + className.split(" ").join("|") + "(\\b|$)", "gi"), " ");
		}
	
		// Toggles the disabled property of an id by checkboxes with same basename //
		function c2_edit_typechange( name ) {
			var target = document.getElementById("etype_"+name);
			var requirement = document.querySelectorAll("." + name + "_REQ");
			
			var disable = false;
			
			for ( var idx = 0; idx < requirement.length; idx++ ) {
				if ( !requirement[idx].checked ) {
					disable = true;
					break;
				}
			}
			
			target.disabled = disable;
		}
		
		// Returns which radio button is set, matching a class //
		function c2_which_radio( classname ) {
			var radio = document.querySelectorAll("." + classname);
			
			for ( var idx = 0; idx < radio.length; idx++ ) {
				if ( radio[idx].checked ) {
					return radio[idx];
				}
			}
			return null;
		}
		
		function c2_on_submission_type_changed( e ) {
//			console.log(e);
//			console.log( c2_which_radio("etype") );
//			console.log(e.getAttribute("value"));
			
			c2_show_optouts( e.getAttribute("value") );
		}
		
		function c2_show_optouts( ootype ) {
			var radio = document.querySelectorAll(".optout-type");
			for ( var idx = 0; idx < radio.length; idx++ ) {
				c2_addclass( radio[idx], "hidden" );
			}
			
			var el;
			if ( ootype ) {
				el = document.getElementById(ootype+"-submission-type");
			}
			else {
				el = document.getElementById("no-submission-type");
			}

			if ( el ) {
				c2_removeclass( el, "hidden" );
			}
		}
	</script>
	';
	
//     $rules = isset($params["rules"])?$params["rules"]:"#";
	if ($opts) {
		echo "<h2>Submission Type {$star}</h2>";
		foreach ($divs as $div) {
			$requirement = [];
			if ( $params[$div."_req"] ) {
				$requirement = explode(";",$params[$div."_req"]);
			}
			
			$selected = (strcmp($etype,$div)==0?"checked":"");
			$disabled = (count($requirement) > 0) && ($selected === "") ? "disabled" : "";
			
			// Radio Button //
			echo "<input type='radio' name='etype' id='etype_{$div}' class='etype' value='{$div}' onchange='c2_on_submission_type_changed(this);' {$selected} {$disabled} /> ".$params["{$div}_title"]."</input>";
			// Summary //
			echo "<div><i>".$params["{$div}_summary"]."</i></div>";
			
			$idx = 0;
			foreach ($requirement as $req) {
				echo "<input type='checkbox' class='{$div}_REQ' name='REQ[{$div}][{$idx}]' value='1' onchange='c2_edit_typechange(\"{$div}\");' ".($selected !== "" ? "checked" : "").">{$req}</input><br />";
				$idx++;           	
			}
			
			if ( $idx > 0 ) {
				echo "<div style='margin-top:10px;margin-bottom:10px;color:#F00'><strong>IMPORTANT:</strong> You must click all checkboxes.</div>";
			}
			else {
				echo "<br />";
			}
		}
		
		echo "<h2>I would like to be judged in these categories</h2>";
		echo "If you feel your game doesn't deserve to be judged in a category, deselect it.<br /><br />";
		//echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
		//echo "You will <strong>not</strong> be rated in these categories.<br /><br />";
		//echo "Opting out is <strong>NOT</strong> required. Do it <strong>*ONLY*</strong> if you don't want to be rated in a category.<br /><br />";

		$hidden = $etype ? "hidden" : "";
		echo "<div id='no-submission-type' class='optout-type {$hidden}'>Please select a Submission Type</div>";
		foreach ($divs as $div) {
			$hidden = strcmp($etype,$div)==0 ? "" : "hidden";
			echo "<div id='{$div}-submission-type' class='optout-type {$hidden}'>";
			foreach ($params["{$div}_cats"] as $catname) {
				$checked = isset($settings["OPTOUT"][$div][$catname])?"":"checked";
				echo "<input type='checkbox' class='' name='OPTOUT[{$div}][{$catname}]' value='1' {$checked}>".$catname."</input><br />";
			}
			echo "</div>";
		}
		
//		echo "<div style='color:#F00;margin-top:10px'><strong>IMPORTANT:</strong> Opt out <strong>*ONLY*</strong> if you <strong>DON'T</strong> want to be rated in a category.</div>";

//        echo "<h2>Opt Out</h2>";
//        
//        // cats: all
//        // open_cats: jam
//        // compo_cats: compo
//        foreach ($params["open_cats"] as $k) {
//            echo "<div>".$k."</div>";
//        }
//    

		echo "<h2>Content Rating</h2>";
		//echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
		echo "<input type='checkbox' class='' name='SETTING[NSFW]' value='1' ".($settings["NSFW"]?"checked":"").">My entry may not be suitable for kids.</input><br />";
		echo "<input type='checkbox' class='' name='SETTING[NSFL]' value='1' ".($settings["NSFL"]?"checked":"").">My entry contains material or subject matter that may be offensive.</input>";
		echo "<div style='margin-top:10px;margin-bottom:10px'><strong>NOTE:</strong> We may enable these if we get complaints about an entry.<br />The first lets people omit kid-unfriendly games, and the 2nd brings up a warning.</div>";

		echo "<h2>Settings</h2>";
		//echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
		echo "<input type='checkbox' class='' name='SETTING[ANONYMOUS]' value='1' ".($settings["ANONYMOUS"]?"checked":"").">I would like to allow anonymous feedback. I understand this means my entry will be criticized more harshly, and I can take it.</input><br />";

	} else {
		echo "<input type='hidden' name='etype' value='$etype'>";
	}
	
	////////////////////////////////////////////////////////////////////////////
	
	echo "<h2>Description {$star}</h2>";
	echo "<textarea name='notes' rows=12 cols=80>".htmlentities($ce["notes"])."</textarea>";
	
	echo "<h2>Screenshot(s) {$star}</h2>";    
	echo "You must include <i>at least</i> one screenshot in <strong>PNG</strong>, <strong>JPEG</strong> or <strong>GIF</strong> formats.<br /><br />";
	
	$shots = unserialize($ce["shots"]);
//     print_r($shots);

	$shot_count = count($shots);
	if ( $shot_count < 5 ) {
		$shot_count = 5;
	}
	
	echo "<table>";
	for ($i=0; $i < $shot_count; $i++) {
		$k = "shot$i";
		echo "<tr><td>".($i+1).".";
		echo "<td><input type='file' name='$k'>";
		if ($i==0) { echo "<td>(Primary Screenshot)"; }
		if (isset($shots[$k])) {
			echo "<tr><td><td align=left><img src='".c2_thumb($shots[$k],180,140)."' width='180' height='140'>";
		}
	}
	echo "</table>";


	echo "<h2>Customize Thumbnail</h2>";    
	echo "Game thumbnails are 180x140. If you use a <strong>GIF</strong>, it will animate on hover.<br /><br />";

	echo "<table>";
	{
		echo "<tr><td>".($i+1).".<td>";
		echo "<td><input type='file' name='SETTING[thumb]'>";
		if (isset($settings['thumb'])) {
			echo "<tr><td><td align=left><img src='".c2_thumb($settings['thumb'],180,140)."' width='180' height='140'>";
		}
	}
	echo "</table><br />";
	
	echo "<h2>Downloads and Links {$star}</h2>";
	echo "You must host your downloads elsewhere. Need a host? <a href='http://ludumdare.com/compo/faq/' target='_blank'>See the <strong>FAQ</strong></a>.<br />";
	echo "<br />";
	
	$links_count = count($links);
	if ( $links_count < 5 ) {
		$links_count = 5;
	}
	
	echo "<table>";
	echo "<tr><th><th>Link Name<th><th>URL (don't forget the http://)";
	for ($i=0; $i < $links_count; $i++) {
		echo "<tr><td>";
		echo "<td><input type='text' name='links[$i][title]' size=15 value=\"".htmlentities($links[$i]["title"])."\">";
		echo "<td>";
		echo "<td><input type='text' name='links[$i][link]' size=45 value=\"".htmlentities($links[$i]["link"])."\">";
	}
	echo "</table>";

	echo "<h2>Embed URL</h2>";
	//echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
	echo "For web games, we can embed your game in an HTML iframe for play right on your entry page. <br />";
	echo "<br />";


	$embed_url = "";
	$embed_width = 800;
	$embed_height = 450;
	
	if ( isset($settings["EMBED"]["url"]) ) $embed_url = $settings["EMBED"]["url"];
	if ( isset($settings["EMBED"]["width"]) ) $embed_width = $settings["EMBED"]["width"];
	if ( isset($settings["EMBED"]["height"]) ) $embed_height = $settings["EMBED"]["height"];
	
	echo "<table>";
	echo "<tr><th><th>URL (don't forget the http://)<th><th>Width<th><th>Height";
	echo "<tr><td>";
	echo "<td><input type='text' name='SETTING[EMBED][url]' size=45 value=\"".$embed_url."\">";
	echo "<td>";
	echo "<td><input type='text' name='SETTING[EMBED][width]' size=5 value=\"".$embed_width."\">";
	echo "<td>";
	echo "<td><input type='text' name='SETTING[EMBED][height]' size=5 value=\"".$embed_height."\">";
	echo "<tr><td>";
	echo "<td>";
	echo "<td>";
	echo "<td>Max: 900";
	echo "<td>";
	echo "<td>Max: 800";
	echo "<td>";
	echo "</table>";
	echo "<input type='checkbox' class='' name='SETTING[EMBED][fullscreen]' value='1' ".($settings['EMBED']['fullscreen']?"checked":"").">Enable Fullscreen Button.<br/>People have a variety of screen sizes, so games running Fullscreen should be able to support a variety of 16:9, 16:10, and 4:3 resolutions.</input><br />";
//	echo "<input type='checkbox' class='' name='SETTING[EMBED][nocontrols]' value='1' ".($settings['EMBED']['nocontrols']?"checked":"").">Disable all Controls. No more Reset, Power, or other helper buttons. Just a Raw Embed.</input><br />";
		

/*    echo "<br />";*/
/*
	echo "<h2>Automatic Embed</h2>";
	echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
	echo "YouTube video? ";
	echo "Alternatively, we can embed a YouTube video. <strong>NOTE:</strong> If you set an Embed URL, we will use that instead.<br />";
	echo "<br />";

	echo "<table>";
	echo "<tr><th><th>URL (don't forget the http://)";
	echo "<tr><td>";
	echo "<td><input type='text' name='SETTING[EMBED][youtube]' size=45 value=\"".""."\">";
	echo "</table>";
*/
	echo "<br />";
	
/*    echo "<h2>Extra Stuff</h2>";
	// this is non-functional
	echo "<table>";
	echo "<tr><td><input type='checkbox' name='data[pov_toggle]'>";
	echo "<td>Checkbox that PoV wanted for reasons unbeknownst to us.";
	echo "</table>";*/
	
	if ($is_admin) {
		echo "<table>";
		echo "<tr><td>Disabled";
		echo "<td>"; compo2_select("disabled",array("0"=>"No","1"=>"Yes"),$ce["disabled"]);
		echo "</table>";
	}
	
	echo "<p>";
	echo "<input type='submit' class='button' value='Save Changes'>";
	echo "</p>";
	
	echo "</form>";
	
	echo "<p>$star - required field</p>";
	
	echo "</div>";
}

function _compo2_active_save($params,$uid="",$is_admin=0) {
	if (!$uid) { $uid = $params["uid"]; }
	$ce = compo2_entry_load($params["cid"],$uid);
	
	if ($params["locked"] && !$ce["id"]) {
		echo "<p class='warning'>This competition is locked.  No new entries are being accepted.</p>";
		return;
	}
/*	
	if ( current_user_can('edit_others_posts') ) {
		echo "Hey team. Just ignore this for now. Only you can see it. Thanks!<br /><br />";
		var_dump( $_REQUEST );
		echo "<br /><br />";
		var_dump( $ce );
	}
*/	
	$active = true; $msg = "";
	
	if (!$_REQUEST["formdata"]) {
		$active = false;
		$msg = "ERROR: Entry not updated. Something is wrong.<br /><br />Usually this means your images are too large (many MB), or they're in an unsupported or non-standard format (NO BMP's).<br />In some rare cases, something is stopping HTTP POSTs from working correctly.<br />Try disabling adblock, noscript, or similar plugins. Or, try another browser.<br/>If you get a chance, let <a href='/compo/contact' target='_blank'>Mike</a> know what your problem was, or contact him for help.";
	} else {    
		$ce["title"] = compo2_strip($_REQUEST["title"]);
		if (!strlen(trim($ce["title"]))) { $active = false; $msg = "Entry name is a required field."; }
		
		
		if ( isset($_REQUEST["etype"]) && $_REQUEST["etype"] !== "" ) {
			$ce["etype"] = $_REQUEST["etype"];
		}
		
		if ($params["init"] == 0) {
			$ce["is_judged"] = intval(strcmp($ce["etype"],"compo") == 0);
		} else {
			$ce["is_judged"] = 1; // now we judge all entries
		}
		
		if (!strlen($ce["etype"])) {
			$active = false;
			$msg = "You must select an Entry Type.";
		}
		
		$ce["notes"] = compo2_strip($_REQUEST["notes"]);
		
		$shots = unserialize($ce["shots"]);
		if ($shots == null) { 
			$shots = array();
		}
		
		$shots_count = count($shots);
		if ( $shots_count > 20 ) { 
			$shots_count = 20; 
		}
		
		for ($i=0; $i < $shots_count; $i++) {
			$k = "shot$i"; 
			$fe = $_FILES[$k];
			
			// Reject empty filename (i.e. no change) //
			if (!trim($fe["tmp_name"])) { continue; }
			
			list($w,$h) = getimagesize($fe["tmp_name"]);

			// Reject Bad Dimensions //
			if (!$w) { continue; } if (!$h) { continue; }
			$ext = array_pop(explode(".",$fe["name"]));

			// Reject File Formats //
			if (!in_array(strtolower($ext),array("png","gif","jpg"))) { continue; }

			$cid = $params["cid"];
			$ts = time();
			$fname = "$cid/$uid-$k-$ts.$ext";
			$dname = dirname(__FILE__)."/../../compo2";
			@mkdir("$dname/$cid");
			$dest = "$dname/$fname";
			
			$old_file = $dname.'/'.$shots[$k];
			if ( file_exists($old_file) ) {
				//unlink( $old_file ); // Delete One File //
				array_map('unlink', glob($old_file."*")); // Delete all files with same base //
			}
			
			move_uploaded_file( $fe["tmp_name"] ,$dest );
			$shots[$k] = $fname;
		}
		$ce["shots"] = serialize($shots);
		if (!count($shots)) { $active = false; $msg = "You must include at least one screenshot."; }
		
		foreach ($_REQUEST["links"] as $k=>$le) {
			$_REQUEST["links"][$k] = array(
				"title"=>compo2_strip($le["title"]),
				"link"=>compo2_strip($le["link"]),
			);
		}
		$ce["links"] = serialize($_REQUEST["links"]);
		$ok = false; foreach ($_REQUEST["links"] as $le) {
			if (strlen(trim($le["title"])) && strlen(trim($le["link"]))) { $ok = true; }
		}
		if (!$ok) { $active = false; $msg = "You must include at least one link."; }
		
		if ($is_admin) { 
			$ce["disabled"] = $_REQUEST["disabled"];
		}
		if ($ce["disabled"]) { $active = false; $msg = "This entry has been disabled."; }
		
	//     $ce["data"] = serialize($_REQUEST["data"]);
		$ce["active"] = intval($active);
		
		$user = compo2_get_user($uid);
		$ce["get_user"] = serialize(array(
			"display_name"=>$user->display_name,
			"user_nicename"=>$user->user_nicename,
			"user_email"=>$user->user_email,
		));
		
		// MK START //
		// Build Settings //
		$settings = [];

		//$safename = sanitize_title_with_dashes($catname);

		{
			// Opt-Outs //
			foreach ( $params["divs"] as $div ) {
				foreach ( $params[$div."_cats"] as $cat ) {
					if ( !isset($_REQUEST["OPTOUT"][$div][$cat]) ) {
						$settings["OPTOUT"][$div][$cat] = 1;
					}
				}
			}
			
			// Parental Settings and other Settings //	
			$settings["NSFW"] = isset($_REQUEST["SETTING"]["NSFW"]) ? 1 : 0;
			$settings["NSFL"] = isset($_REQUEST["SETTING"]["NSFL"]) ? 1 : 0;
			$settings["ANONYMOUS"] = isset($_REQUEST["SETTING"]["ANONYMOUS"]) ? 1 : 0;
			
			
			// Embedded Game Player //
			$embed_width = 800;
			$embed_height = 450;
			$embed_url = "";
			$embed_fullscreen = false;
			$embed_nocontrols = false;
			
			if ( isset($_REQUEST["SETTING"]["EMBED"]["width"]) ) {
				$width = intval($_REQUEST["SETTING"]["EMBED"]["width"]);
				if ( $width > 900 ) $width = 900;
				if ( $width < 16 ) $width = 16;

				$embed_width = $width;
			}
			
			if ( isset($_REQUEST["SETTING"]["EMBED"]["height"]) ) {
				$height = intval($_REQUEST["SETTING"]["EMBED"]["height"]);
				if ( $height > 800 ) $height = 800;
				if ( $height < 9 ) $height = 9;
				
				$embed_height = $height;
			}
			
			if ( isset($_REQUEST["SETTING"]["EMBED"]["url"]) ) {
				$embed_url = esc_url($_REQUEST["SETTING"]["EMBED"]["url"]);	
			}
			
			if ( isset($_REQUEST["SETTING"]["EMBED"]["fullscreen"]) ) {
				$embed_fullscreen = intval($_REQUEST["SETTING"]["EMBED"]["fullscreen"]) ? true : false;
			}

			if ( isset($_REQUEST["SETTING"]["EMBED"]["nocontrols"]) ) {
				$embed_nocontrols = intval($_REQUEST["SETTING"]["EMBED"]["nocontrols"]) ? true : false;
			}
			
			$settings["EMBED"]["width"] = $embed_width;
			$settings["EMBED"]["height"] = $embed_height;
			$settings["EMBED"]["url"] = $embed_url;
			$settings["EMBED"]["fullscreen"] = $embed_fullscreen;
			$settings["EMBED"]["nocontrols"] = $embed_nocontrols;
		}
		
		$ce["settings"] = serialize($settings);
		
		$ce["stamp"] = date("Y-m-d H:i:s");
		// MK END //
		
		unset($ce["results"]);
		if (!$ce["id"]) {
			$ce["cid"] = $params["cid"];
			$ce["uid"] = $uid;
			$ce["ts"] = date("Y-m-d H:i:s");
			compo2_insert("c2_entry",$ce);
		} else {
			compo2_update("c2_entry",$ce);
		}
		
		echo "<h3>Entry Saved</h3>";
	}
	
	if (!$active) {
		echo "<p class='error'>$msg</p>";
	}
	
	if (!$is_admin) {
		echo "<p><a href='?action=edit'>Edit your entry</a> | <a href='?action=default'>Browse entries</a> | <a href='?action=preview&uid={$params["uid"]}'>View your entry</a></p>";
	} else {
		echo "<p><a href='?action=default&admin=1'>Browse entries</a></p>";
	}
//     header("Location: ?action=default"); die;
}
?>