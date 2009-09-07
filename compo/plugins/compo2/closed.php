<?php

function _compo2_closed($params) {
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        return _compo2_preview($params);
    } elseif ($action == "preview") {
        return _compo2_preview($params);
    } elseif ($action == "edit") {
        return _compo2_active_form($params);
    } elseif ($action == "save") {
        return _compo2_active_save($params);
    }
}


?>