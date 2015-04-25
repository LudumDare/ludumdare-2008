<?php
/**
 * Convert some attrs ' x="5" y="3" j="n" ' to an array.  Must have quotes.
 * @param attrs
 * @return array
 */
function xmlhack_attrs2array($data) {
    $r = array();
//     preg_match_all("/([a-zA-Z0-9_]+)\s*=\s*[']([^']*)[']/s",$data,$m);
//     foreach ($m[1] as $n=>$k) { $r[$k] = $m[2][$n]; }
    preg_match_all("/([a-zA-Z0-9_]+)\s*=\s*[\"]([^\"]*)[\"]/s",$data,$m);
    foreach ($m[1] as $n=>$k) { $r[$k] = $m[2][$n]; }
    return ($r);
}

function _compo2_log_sort($a,$b) {
    return intval(($b["tm"]*1000) - ($a["tm"]*1000));
}

//function _compo2_main($m) {
function compo2_main($params) {
    global $compo2;
    $tm = microtime(true);

    /* old parsing
    $parts = explode(":",html_entity_decode($m[1]));
    $state = $parts[0];
    $jcat = $parts[1];
    $opts = $parts[2];
    $opts = explode(";",str_replace(" ","",$opts));
    */
    
    /** Params **
    @cats   List of judging categories: "Innovation;Fun;Production"
    @jcat   Wordpress Journal category
    @state  State of compo (active,rate,results,closed)
    */
    
/* MK
    $params = xmlhack_attrs2array($m[1]);
*/
    @$params["init"] = intval($params["init"]);
    
    if ($params["init"] == 0) {
        $params["divs"] = "compo";
        if (isset($params["gamejam"])) {
            $params["divs"] .= ";gamejam";
        }
        $params["compo_cats"] = $params["cats"];
        $params["compo_title"] = "Competition";
        $params["compo_summary"] = "My entry follows all the rules and I want it to be judged.";
        $params["compo_link"] = "#";
        $params["gamejam_title"] = "Game Jam";
        $params["gamejam_summary"] = "My entry doesn't follow the rules or I don't want it to be judged.";
    }
    if (!isset($params["opendivs"])) { $params["opendivs"] = $params["divs"]; }
    $params["divs"] = explode(";",str_replace(" ","",$params["divs"]));
    $params["opendivs"] = explode(";",str_replace(" ","",$params["opendivs"]));
    if ($params["locked"]) { $params["opendivs"] = array(); }

    $cats = array();
    foreach ($params["divs"] as $div) {
        if (isset($params["{$div}_cats"])) {
            $params["{$div}_cats"] = explode(";",str_replace(" ","",$params["{$div}_cats"]));
            foreach ($params["{$div}_cats"] as $v) {
                if (!in_array($v,$cats)) { $cats[]= $v; }
            }
        }
    }

    $params["cats"] = $cats;
    if (!isset($params["topcat"])) { $params["topcat"] = "Overall"; }
    foreach (array("calc_droplow"=>0,"calc_drophigh"=>0,"calc_reqvote"=>5) as $k=>$v) {
        $params[$k] = isset($params[$k])?intval($params[$k]):$v;
    }
    
    // some other auto-calculated stuff
    // @cat Contenst id (taken from page ID)
    $params["cid"] = intval($GLOBALS["post"]->ID);
    
    // @uid User ID
    // @user WP-User object
    $user = wp_get_current_user(); $uid = $user->ID;
    $params["uid"] = $uid;
    $params["user"] = $user;
    
    // @has_entry True if the current user has an entry in this compo
    $ce = compo2_entry_load($params["cid"],$uid);
    $params["has_entry"] = ($ce["id"]!=0);
    
    // State is changed to admin if ?admin=1 is in the URL
    if ($_REQUEST["admin"]) { $params["state"] = "admin"; }
    
    // State is changed to misc, if we're accessing a misc_ page
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    if (in_array($action,array("misc_links"))) {
        $params["state"] = "misc";
    }
    
    // If we're in debug mode, display our params
//     if (strlen($_REQUEST["debug"])) { echo "<pre>";print_r($params);echo "</pre>"; }
    
    $nsfw = "<div id='compo-nsfw' class='hidden' onclick='c2_addclass( document.getElementById(\"compo-nsfw\"), \"hidden\"); c2_removeclass( document.getElementById(\"compo-body\"), \"hidden\");'><h2>WARNING!</h2><p>This entry may contain subject matter that you or those around you may find offensive. You have been warned.</p><h2>I understand. Let me see it.</h2></div><script>if (typeof window.compo_game_nsfw !== \"null\") { c2_addclass( document.getElementById(\"compo-body\"), \"hidden\"); c2_removeclass( document.getElementById(\"compo-nsfw\"), \"hidden\"); }</script>";
    
    // dispatch according to the current state
    ob_start();
    $state = $params["state"];
    if ($state == "active") { _compo2_active($params); }
    elseif ($state == "rate") { echo "<div id='compo-body'>"; _compo2_rate($params); echo "</div>" . $nsfw; }
    elseif ($state == "results") { _compo2_results($params); }
    elseif ($state == "admin") { _compo2_admin($params); }
    elseif ($state == "misc") { _compo2_misc($params); }
    elseif ($state == "closed") { _compo2_closed($params); }
    else { compo2_error("compo2 - Invalid state: $state"); }
    if ($user->user_level >= 7) {
        echo "<p><a href='?admin=1'>Enter admin mode</a></p>";
    }
    $r = ob_get_contents();
    ob_end_clean();
    
    // output the content
    compo2_log("_compo2_main",microtime(true)-$tm);
/*    
	// The red logging display at the bottom of all Compo2 pages //
    if (1 && $user->user_level >= 7) {
        ob_start();
        
        $log = $compo2["log"];
        usort($log,"_compo2_log_sort");
        echo "<table border=1>";
        echo "<tr><th>ms<th>fnc<th>hits<th>msg";
        foreach ($log as $e) {
            echo "<tr>";
            echo "<td align=right>".intval($e["tm"]*1000);
            echo "<td>".htmlentities($e["fnc"]);
            echo "<td align=right>".htmlentities($e["hits"]);
            echo "<td>".htmlentities($e["msg"]);
        }
        echo "</table>";
        
        $rlog = ob_get_contents();
        ob_end_clean();
        $r .= "<div class='error'>$rlog</div>";
        $v = get_option($compo2["version.key"]);
        $r .= "<p>compo2: $v</p>";
    }
*/   
    
    return "<div id='compo2'>$r</div>";
}

/*
function compo2_main($content) {
    $content = preg_replace_callback("/\[compo2\s(.*?)\]/","_compo2_main",$content);
    return $content;
}
*/
?>