<?php

function _compo2_admin($params) {
    $user = $params["user"];
    if ($user->user_level < 7) { compo2_error("admin"); }

    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"preview";
    if ($action == "default") { $action = "preview"; }
    
    if ($action == "edit") {
        return _compo2_active_form($params,$_REQUEST["uid"],1);
    } elseif ($action == "save") {
        return _compo2_active_save($params,$_REQUEST["uid"],1);
    } elseif ($action == "preview") {
        return _compo2_preview($params,"?admin=1&action=edit");
    } elseif ($action == "ratelist") {
        return _compo2_rate_list($params);
    } elseif ($action == "results") {
        return _compo2_results_results($params);
    } elseif ($action == "top") {
        return _compo2_results_top($params);
    } elseif ($action == "recalc") {
        return _compo2_admin_recalc($params);
    } elseif ($action == "resetcache") {
        return _compo2_admin_resetcache($params);
    } elseif ($action == "get_user") {
        return _compo2_admin_get_user($params);
    } elseif ($action == "cron") {
    	return _compo2_mike_cron($params);
    }
}

function _compo2_admin_get_user($params) {
    echo "<h3>Resetting get_user data ...</h3>";
    $r = compo2_query("select id,uid from c2_entry");
    foreach ($r as $ce) {
        $user = compo2_get_user($ce["uid"]);
        $ce["get_user"] = serialize(array(
            "display_name"=>$user->display_name,
            "user_nicename"=>$user->user_nicename,
            "user_email"=>$user->user_email,
        ));
        compo2_update("c2_entry",$ce);
    }
    echo "<p>Done.</p>";
}
    

function _compo2_admin_resetcache($params) {
    echo "<h3>Resetting Cache ...</h3>";
    compo2_query("delete from c2_cache where cid = ?",array($params["cid"]));
    echo "<p>Done.</p>";
}

function _compo2_admin_recalc($params) {
    echo "<h3>Recaculating Results ...</h3>";
    $r = compo2_query("select uid from c2_entry where cid = ? and active = 1",array($params["cid"]));
    global $compo2;
    $compo2["log.enabled"] = false;
    foreach ($r as $ce) {
        $uid = $ce["uid"];
        _compo2_rate_recalc($params,$uid);
    }
    $compo2["log.enabled"] = true;
    echo "<p>Done.</p>";
    echo "<hr/>";
    
    _compo2_admin_resetcache($params);
    echo "<hr/>";
    
    _compo2_results_results($params);
}

?>