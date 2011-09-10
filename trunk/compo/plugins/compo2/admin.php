<?php

function _compo2_admin($params) {
    $user = $params["user"];
    if ($user->user_level < 10) { compo2_error("admin"); }

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
    }
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