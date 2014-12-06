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
    
    if ($params["locked"] && !$ce["id"]) {
        echo "<p class='warning'>This competition is locked.  No new entries are being accepted.</p>";
        return;
    }
    
    // TODO: Make just one (which means make code to iterate, add "add new" button)
    // TODO: __embed for embedded url
    // TODO: __src for source
    // TODO: __src_user for source protected with a user account
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
    	.edit h4 {
    		margin-bottom:0;
    	}
    	
    	.edit .hidden {
    		display:none;
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

    echo "<form method='post' action='$link' enctype='multipart/form-data'>";
    echo "<input type='hidden' name='formdata' value='1'>";
    
    
    echo "<h4>Name</h4>";    
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
    @$etype = $ce["etype"];
    if (strlen($etype)) {
        if (!in_array($etype,$divs)) {
            array_unshift($divs,$etype);
        }
    }
    if ($is_admin) { $divs = $params["divs"]; }
    
    echo '
    <script>
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
			console.log(e);
			console.log( c2_which_radio("etype") );
		}
    </script>
    ';
    
//     $rules = isset($params["rules"])?$params["rules"]:"#";
    if ($opts) {
        echo "<h4>Submission Type</h4>";
        foreach ($divs as $div) {
            $requirement = [];
            if ( $params[$div."_req"] ) {
	            $requirement = explode(";",$params[$div."_req"]);
	        }
			
            $selected = (strcmp($etype,$div)==0?"checked":"");
            $disabled = count($requirement) > 0 ? "disabled" : "";
            // Radio Button //
            echo "<input type='radio' name='etype' id='etype_{$div}' class='etype' value='{$div}' onchange='c2_on_submission_type_changed(this);' {$selected} {$disabled} /> ".$params["{$div}_title"]."</input>";
            // Summary //
            echo "<div><i>".$params["{$div}_summary"]."</i></div>";
            
            $idx = 0;
            foreach ($requirement as $req) {
	            echo "<input type='checkbox' class='{$div}_REQ' name='{$div}_REQ_{$idx}' value='{$div}_REQ_{$idx}' onchange='c2_edit_typechange(\"{$div}\");'>{$req}</input><br />";
	            $idx++;           	
            }
            // MK: END NIGHT
            /*
	        foreach ($params[$div."_cats"] as $k) {
	            echo "<input type='checkbox' name='' value='OPT_OUT_'>Opt-out of \"".$k."\"</div>";
	        }
	        */
            echo "<br />";
        }
        
        echo "<h4>I would like to opt-out of (optional)</h4>";
		echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
		
		echo "You will <strong>not</strong> be rated in these categories.<br /><br />";
		echo "<div id='no-submission-type'>Please select a Submission Type</div>";
		foreach ($divs as $div) {
			echo "<div id='{$div}-submission-type'>";
	        foreach ($params["{$div}_cats"] as $catname) {
	            echo "<input type='checkbox' class='' name='' value='OPT_OUT_'>".$catname."</input><br />";
	        }			
			echo "</div>";
		}
//        echo "<h4>Opt Out</h4>";
//        
//        // cats: all
//        // open_cats: jam
//        // compo_cats: compo
//        foreach ($params["open_cats"] as $k) {
//            echo "<div>".$k."</div>";
//        }
//    
        echo "<h4>Settings</h4>";
		echo "<span style='color:#F0F;'><strong>*WORK IN PROGRESS*</strong></span> This feature is unfinished. Come back later to set these.<br />";
		echo "<input type='checkbox' class='' name='' value='SETTING_NSFW'>My game may not be suitable for kids</input><br />";
		echo "<input type='checkbox' class='' name='' value='SETTING_ANONYMOUS'>I would like to allow anonymous feedback. I understand this means my game will be criticized more harshly, and I can take it.</input><br />";

    } else {
        echo "<input type='hidden' name='etype' value='$etype'>";
    }
    
    ////////////////////////////////////////////////////////////////////////////
    
    echo "<h4>Description</h4>";
    echo "<textarea name='notes' rows=8 cols=60>".htmlentities($ce["notes"])."</textarea>";
    
    echo "<h4>Screenshot(s)</h4>";    
    echo "You must include one screenshot. {$star}<br />";
    
    $shots = unserialize($ce["shots"]);
//     print_r($shots);
    
    echo "<table>";
    for ($i=0; $i<5; $i++) {
        $k = "shot$i";
        echo "<tr><td>".($i+1).".<td>";
        //if ($i==0) { echo "$star "; }
        echo "<td><input type='file' name='$k'>";
        if ($i==0) { echo "<td>(Primary Screenshot)"; }
        if (isset($shots[$k])) {
            echo "<tr><td><td align=left><img src='".compo2_thumb($shots[$k],120,80)."'>";
        }
    }
    echo "</table>";
    
    echo "<h4>Links</h4>";
    
    echo "<p>This is where you link to your entry downloads (Don't forget the <b>http://</b>). You must include <i>at least</i> one link. Field Names can be edited.</p>";
    
    echo "<table>";
    echo "<tr><th><th>Name<th><th>URL";
    for ($i=0; $i<5; $i++) {
        echo "<tr><td>";
        if ($i==0) { echo " $star"; }
        echo "<td><input type='text' name='links[$i][title]' size=15 value=\"".htmlentities($links[$i]["title"])."\">";
        echo "<td>";
        if ($i==0) { echo " $star"; }
        echo "<td><input type='text' name='links[$i][link]' value=\"".htmlentities($links[$i]["link"])."\" size=45>";
    }
    echo "</table>";
    
/*    echo "<h4>Extra Stuff</h4>";
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
    echo "<input type='submit' value='Save your Entry'>";
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
    
    $active = true; $msg = "";
    
    if (!$_REQUEST["formdata"]) {
        $active = false;
        $msg = "Invalid request.  Entry not updated.";
    } else {
    
        $ce["title"] = compo2_strip($_REQUEST["title"]);
        if (!strlen(trim($ce["title"]))) { $active = false; $msg = "Entry name is a required field."; }
        
        
        $ce["etype"] = $_REQUEST["etype"];
        
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
        if ($shots == null) { $shots = array(); }
        for ($i=0; $i<5; $i++) {
            $k = "shot$i"; $fe = $_FILES[$k];
            if (!$fe["tmp_name"]) { continue; }
            list($w,$h) = getimagesize($fe["tmp_name"]);
            if (!$w) { continue; } if (!$h) { continue; }
    //         unset($shots[$k]);
            $ext = array_pop(explode(".",$fe["name"]));
            if (!in_array(strtolower($ext),array("png","gif","jpg","bmp"))) { continue; }
            $cid = $params["cid"];
            $ts = time();
    //         $fname = "$cid/$uid-$ts.$ext";
            $fname = "$cid/$uid-$k.$ext";
            $dname = dirname(__FILE__)."/../../compo2";
            @mkdir("$dname/$cid");
            $dest = "$dname/$fname";
            move_uploaded_file  ( $fe["tmp_name"] ,$dest );
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