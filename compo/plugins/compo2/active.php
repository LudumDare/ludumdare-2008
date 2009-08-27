<?php

function _compo2_active($params) {
    if (!$params["uid"]) { return _compo2_preview($params); }

    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        return _compo2_active_form($params);
    } elseif ($action == "save") {
        return _compo2_active_save($params);
    } elseif ($action == "preview") {
        return _compo2_preview($params);
    }
}

function _compo2_active_form($params) {
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    $links = unserialize($ce["links"]);
    
    echo "<p><a href='?action=preview'>View all entries.</a></p>";
    
    if ($ce["id"] != "" && !$ce["active"]) {
        echo "<div class='warning'>Your entry is not complete.</div>";
    }

    echo "<form method='post' action='?action=save' enctype='multipart/form-data'>";
    echo "<h3>Name of Entry</h3>";
    
    echo "<input type='text' name='title' value=\"".htmlentities($ce["title"])."\" size=60>";
    
    echo "<h3>Notes</h3>";
    
    echo "<textarea name='notes' rows=8 cols=60>".htmlentities($ce["notes"])."</textarea>";
    
    echo "<h3>Screenshots</h3>";
    
    echo "<p>You must include at least one screenshot.</p>";
    
    $shots = unserialize($ce["shots"]);
//     print_r($shots);
    
    echo "<table>";
    for ($i=0; $i<5; $i++) {
        $k = "shot$i";
        echo "<tr><td>".($i+1).".<td><input type='file' name='$k'>";
        if ($i==0) { echo "<td>(Primary Screenshot)"; }
        if (isset($shots[$k])) {
            echo "<tr><td><td align=left><img src='".compo2_thumb($shots[$k],120,80)."'>";
        }
    }
    echo "</table>";
    
    echo "<h3>Links</h3>";
    
    echo "<p>This is where you link to your entry downloads.  Please use names like: win32, linux, osx, web, flash, src, etc ... You must include at least one link.</p>";
    
    echo "<table>";
    echo "<tr><th>Name<th>URL";
    for ($i=0; $i<5; $i++) {
        echo "<tr><td><input type='text' name='links[$i][title]' size=15 value=\"".htmlentities($links[$i]["title"])."\">";
        echo "<td><input type='text' name='links[$i][link]' value=\"".htmlentities($links[$i]["link"])."\" size=45>";
    }
    echo "</table>";
    
    echo "<h3>Extra Stuff</h3>";
    // this is non-functional
    echo "<table>";
    echo "<tr><td><input type='checkbox' name='data[pov_toggle]'>";
    echo "<td>Checkbox that PoV wanted for reasons unbeknownst to us.";
    echo "</table>";
    
    echo "<p>";
    echo "<input type='submit' value='Submit your Entry'>";
    echo "</p>";
    
    echo "</form>";
}

function _compo2_active_save($params) {
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    $active = true;
    
    $ce["title"] = $_REQUEST["title"];
    if (!strlen($ce["title"])) { $active = false; }
    
    $ce["notes"] = $_REQUEST["notes"];
    
    $shots = unserialize($ce["shots"]);
    foreach ($_FILES as $k=>$fe) {
        list($w,$h) = getimagesize($fe["tmp_name"]);
        if (!$w) { continue; } if (!$h) { continue; }
        unset($shots[$k]);
        $ext = array_pop(explode(".",$fe["name"]));
        $cid = $params["cid"];
        $uid = $params["uid"];
        $ts = time();
        $fname = "$cid/$uid-$ts.$ext";
        $dname = dirname(__FILE__)."/../../compo2";
        @mkdir("$cid/$dname");
        $dest = "$dname/$fname";
        move_uploaded_file  ( $fe["tmp_name"] ,$dest );
        $shots[$k] = $fname;
    }
    $ce["shots"] = serialize($shots);
    if (!count($shots)) { $active = false; }
    
    $ce["links"] = serialize($_REQUEST["links"]);
    $ok = false; foreach ($_REQUEST["links"] as $le) {
        if (strlen($le["title"]) && strlen($le["link"])) { $ok = true; }
    }
    if (!$ok) { $active = false; }
    
    $ce["data"] = serialize($_REQUEST["data"]);
    $ce["active"] = intval($active);
    unset($ce["results"]);
    if (!$ce["cid"]) {
        $ce["cid"] = $params["cid"];
        $ce["uid"] = $params["uid"];
        compo2_insert("c2_entry",$ce);
    } else {
        compo2_update("c2_entry",$ce);
    }
    
//     header("Location: ?action=default"); die;
}
?>