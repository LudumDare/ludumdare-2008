<?php

function _compo2_main($m) {
    list($state,$opts) = explode(":",html_entity_decode($m[1]));
    $opts = explode(";",str_replace(" ","",$opts));
    $pid = intval($GLOBALS["post"]->ID);
    $cur = wp_get_current_user(); $uid = $cur->ID;
    $params = array(
        "cid"=>$pid,
        "cats"=>$opts,
        "user"=>$cur,
        "uid"=>$uid,
    );
    
    ob_start();
    if ($state == "active") { _compo2_active($params); }
    elseif ($state == "rate") { _compo2_rate($params); }
    elseif ($state == "results") { _compo2_results($params); }
    else { compo2_error("compo2 - Invalid state: $state"); }
    $r = ob_get_contents();
    ob_end_clean();
    
    return "<div class='compo2'>$r</div>";
}


function compo2_main($content) {
    $content = preg_replace_callback("/\[compo2\:(.*?)\]/","_compo2_main",$content);
    return $content;
}
?>