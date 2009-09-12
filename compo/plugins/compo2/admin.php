<?php

function _compo2_admin($params) {
    $user = compo2_get_user($params["uid"]);
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
    }
}

?>