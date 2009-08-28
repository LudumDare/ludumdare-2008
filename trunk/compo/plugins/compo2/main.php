<?php

function _compo2_main($m) {
    $parts = explode(":",html_entity_decode($m[1]));
    $state = $parts[0];
    $jcat = $parts[1];
    $opts = $parts[2];
    $opts = explode(";",str_replace(" ","",$opts));
    $pid = intval($GLOBALS["post"]->ID);
    $cur = wp_get_current_user(); $uid = $cur->ID;
    if ($_REQUEST["admin"]) { $state = "admin"; }
    $mode = 0;
    $ce = compo2_entry_load($pid,$uid);
    $params = array(
        "cid"=>$pid,
        "cats"=>$opts,
        "user"=>$cur,
        "uid"=>$uid,
        "jcat"=>$jcat,
        "state"=>$state,
        "has_entry"=>$ce["id"]!=0,
    );
    
    ob_start();
    if ($state == "active") { _compo2_active($params); }
    elseif ($state == "rate") { _compo2_rate($params); }
    elseif ($state == "results") { _compo2_results($params); }
    elseif ($state == "admin") { _compo2_admin($params); }
    else { compo2_error("compo2 - Invalid state: $state"); }
    
    $user = compo2_get_user($params["uid"]);
    if ($user->user_level >= 10) {
        echo "<p><a href='?admin=1'>Enter admin mode</a></p>";
    }
    
    $r = ob_get_contents();
    ob_end_clean();
    
    return "<div class='compo2'>$r</div>";
    
    
}


function compo2_main($content) {
    $content = preg_replace_callback("/\[compo2\:(.*?)\]/","_compo2_main",$content);
    return $content;
}
?>