<?php

function _compo2_rate($params) {
    if (!$params["uid"]) {
        echo "<p class='message'>You must sign in to vote.</p>";
        return _compo2_preview($params);
    }

    // handle non-competitors ..
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if (!intval($params["pubvote"]))
    if ((!$ce["id"]) || (!$ce["active"])) {
        $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"preview";
        if ($action == "default") { $action = "preview"; }
        if ($action == "edit") {
            return _compo2_active_form($params);
        } elseif ($action == "save") {
            return _compo2_active_save($params);
        } elseif ($action == "preview") {
            echo "<p class='message'>Voting is only available to participants.</p>";
            if (!$params["locked"]) {
                echo "<p><a href='?action=edit'>Create an Entry</a></p>";
            }
            return _compo2_preview($params);
        } elseif ($action == "rate") {
            header("Location: ./?action=preview&uid=".intval($_REQUEST["uid"])); die;
        }
        return;
    }

    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    if ($action == "default") {
        return _compo2_rate_list($params);
    } elseif ($action == "preview") {
        echo "<p><a href='?action=default'>Back to Rate Entries</a></p>";
        return _compo2_preview($params,"?action=rate");
    } elseif ($action == "comments") {
        return _compo2_rate_comments($params);
    } elseif ($action == "rate") {
        return _compo2_rate_rate($params);
    } elseif ($action == "submit") {
        return _compo2_rate_submit($params);
    } elseif ($action == "edit") {
        return _compo2_active_form($params);
    } elseif ($action == "save") {
        return _compo2_active_save($params);
    }
}

// this shows the archive of old comments before we created the c2_comments table to store comments
function _compo2_show_comments($cid,$uid) {
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ? and length(comments) > 1 order by ts asc",array($cid,$uid));
    if (!count($r)) { return; }
    echo "<h3>Comments (archive)</h3>";
    foreach ($r as $ve) if (strlen(trim($ve["comments"]))) {
        $user = compo2_get_user($ve["from_uid"]);
        echo "<h4>{$user->display_name} says ...</h4>";
        echo "<p>".str_replace("\n","<br/>",htmlentities($ve["comments"]))."</p>";
    }
}

function _compo2_rate_comments($params) {
//     return _compo2_rate_rate($params,$params["uid"]);
    header("Location: ?action=preview&uid={$params["uid"]}"); die;
}


function _compo2_rate_sort($a,$b) {
    return strcmp($a["s"],$b["s"]);
}

function _compo2_rate_sort_by_rate_in($a,$b) {
    return $a["rate_in"] - $b["rate_in"];
}
function _compo2_rate_sort_by_rate_out($a,$b) {
    return $b["rate_out"] - $a["rate_out"];
}

function _compo2_rate_list($params) {
    @$q = $_REQUEST["q"];

    if (!strlen($q)) {
        $_r = compo2_query("select uid,cid,rate_in,get_user from c2_entry where cid = ? and active = 1 and is_judged = 1",array($params["cid"]));
    } else {
        $_r = compo2_query("select uid,cid,rate_in,get_user from c2_entry where (notes like ? OR links like ? OR get_user like ?) and cid = ? and active = 1 and is_judged = 1",array("%$q%","%$q%","%$q%",$params["cid"]));
    }
    
//     srand($params["cid"]*256 + $params["uid"]);
//     shuffle($r);
/*    foreach ($r as $n=>$e) {
        if ($e["uid"] != $params["uid"]) { continue; }
        $r = array_merge(array_slice($r,$n),array_slice($r,0,$n));
        break;
    }*/
    
    $r = array();
    @$sortby = $_REQUEST["sortby"];
    
    foreach ($_r as $k=>$ce) {
        if ($sortby == "rate_in") { $key = sprintf("%05d|%s",$ce["rate_in"],$ce["uid"]); }
        else {
            $key = md5("{$params["uid"]}|{$ce["cid"]}|{$ce["uid"]}")."|{$ce["uid"]}";
        }
        $r[$key] = $ce;
    }
    ksort($r); // Much faster than usort.
    
/*    @$sortby = $_REQUEST["sortby"];
    if ($sortby == "rate_in") {
        usort($r,"_compo2_rate_sort_by_rate_in");
    } elseif ($sortby == "rate_out") {
        usort($r,"_compo2_rate_sort_by_rate_out");
    } else {
        usort($r,"_compo2_rate_sort");
    }*/
    
    
    echo "<h3>Rate Entries</h3>";
    
    echo "<form style='text-align:left'>";
//     echo "<input type='hidden' name='action' value=''>";
    echo "<input type='text' name='q' value='".htmlentities($q)."'>";
    echo " <input type='submit' value='Search'>";
    echo "</form>";
    
    $n=0;
    echo "<table>";
    echo "<tr><th><th>";
    $total = 0;
    foreach ($params["cats"] as $k) { echo "<th>".substr($k,0,3); }
    echo "<th>Txt";
    $myurl = get_bloginfo("url")."/wp-content/plugins/compo2";
    
    $r_rate = array();
    foreach (compo2_query("select * from c2_rate where cid = ? and from_uid = ?",array($params["cid"],$params["uid"])) as $ve) {
        $r_rate[$ve["to_uid"]] = $ve;
    }
    
    foreach ($r as $ce) {
        if ($ce["uid"] == $params["uid"] && !strlen($_REQUEST["more"])) { continue; }
        
//         $ve = array_pop(compo2_query("select * from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"])));
        $ve = $r_rate[$ce["uid"]];
        $ue = unserialize($ce["get_user"]);
        echo "<tr>";
        $img = "inone.gif";
        $v = round(100*$ce["rate_out"]/max(1,(count($r)-1)));
        if ($v >= 25) { $img = "ibronze.gif"; }
        if ($v >= 50) { $img = "isilver.gif"; }
        if ($v >= 75) { $img = "igold.gif"; }
//         if ($v >= 100) { $img = "star.gif"; }
        echo "<td><img src='$myurl/images/$img' title='$v% Coolness'>";
        if ($ce["uid"] != $params["uid"]) {
            $name = $ue["display_name"];
            if (!strlen($name)) { $name = "?"; }
            echo "<td><a href='?action=rate&uid={$ce["uid"]}'>".htmlentities($name)."</a>";
        } else {
            echo "<td>".htmlentities($ue["display_name"]);
        }
        if ($ce["rate_in"]) { echo " ({$ce["rate_in"]})"; }
        
        $data = unserialize($ve["data"]);
        foreach ($params["cats"] as $k) {
            echo "<td align=center>".(strlen($data[$k])?intval($data[$k]):"-");
        }
        echo "<td align=center>".(strlen($ve["comments"])?"x":"-");
        
        $ok = false; if (strlen($ve["comments"])) { $ok = true; }
        foreach ($params["cats"] as $k) { if (strlen($data[$k])) { $ok = true; } }
        if ($ok) { $total += 1; }
        
        $n += 1;
        if ($n >= max(20,$total+5) && !strlen($_REQUEST["more"])) { break; }
    }
    echo "</table>";
    
    echo "<p>";
    if (!strlen($_REQUEST["more"])) {
        echo "<a href='?more=1&q=".urlencode($q)."'>Show all entries</a> | ";
    }
    echo "<a href='?sortby=rate_in&q=".urlencode($q)."'>Sort by least ratings</a> | ";
//     echo "<a href='?sortby=rate_out'>Sort by most coolness</a>";
//     echo "</p><p>";
    echo "<a href='?action=preview'>View all Screenshots</a> | ";
    echo "<a href='?action=edit'>Edit your entry</a> | ";
    echo "<a href='?action=comments'>See comments on your entry</a>";
    echo "</p>";
}

function _compo2_rate_rate($params,$uid = "") {
    if (!$uid) { $uid = intval($_REQUEST["uid"]); }
    
    echo "<p><a href='?action=default'>Back to Rate Entries</a></p>";

    if ($params["uid"] == $uid) {
        _compo2_preview_show($params,$uid,true);
        return;
    }
    
    $ce = compo2_entry_load($params["cid"],$uid);
    
    if (!$ce["id"]) { compo2_error("invalid entry: uid=$uid"); }
    
    if (!$ce["is_judged"]) {
        _compo2_preview_show($params,$uid,true);
        return;
    }
    
    $div = $ce["etype"];

    _compo2_preview_show($params,$uid,false);
    
    $ve = array_pop(compo2_query("select * from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"])));
    
    if ($params["uid"] != $uid) {
        echo "<h3>Rate this {$params["{$div}_title"]} Entry</h3>";
            
        
        $myurl = get_bloginfo("url")."/wp-content/plugins/compo2";
        echo "<script type='text/javascript' src='$myurl/starry/prototype.lite.js'></script>";
        echo "<script type='text/javascript' src='$myurl/starry/stars.js'></script>";
        echo "<link rel='stylesheet' href='$myurl/starry/stars.css' type='text/css' />";
    
        echo "<form method=post action='?action=submit&uid=$uid'>";
        echo "<p>";
        if (isset($params["{$div}_cats"])) {
            echo "<table>";
            $data = unserialize($ve["data"]);
            foreach ($params["{$div}_cats"] as $k) {
                echo "<tr><th>".htmlentities($k);
                echo "<td>";
                $v = intval($data[$k]);
                echo "<script>new Starry('data[$k]',{name:'data[$k]',sprite:'$myurl/starry/newstars.gif',width:20,height:20,startAt:$v});</script>";
        //         compo2_select("data[$k]",array(""=>"n/a","5"=>"5 - Best","4"=>"4","3"=>"3","2"=>"2","1"=>"1 - Worst"),$v);
            }
            echo "</table>";
        } else {
            echo "<i>This division does not have any voting categories.  Please leave comments for the author.</i>";
        }
        echo "</p>";
        echo "<h4>Comments (non-anonymous)</h4>";
        $ve["comments"]="";
        echo "<textarea name='comments' rows=4 cols=60>".htmlentities($ve["comments"])."</textarea>";
        echo "<p><input type='submit' value='Save'></p>";
        echo "</form>";
        
        echo "<hr/>";
    }
    _compo2_preview_comments($params,$uid,$form=true);
    _compo2_show_comments($params["cid"],$ce["uid"]);

}

function _compo2_rate_submit($params) {
//     print_r($_REQUEST); die;
    $uid = intval($_REQUEST["uid"]);
    $ce = compo2_entry_load($params["cid"],$uid);
    
    if (!$ce["id"]) { compo2_error("invalid entry: uid=$uid"); }
    
    if ($uid == $params["uid"]) { compo2_error("can't vote on your own entry"); }
    
    $data = array();
    $total = 0;
    foreach ($_REQUEST["data"] as $k=>$v) {
//         $data[$k] = strlen($v)?intval($v):""; // worked for old method
        $data[$k] = intval($v)?intval($v):""; // works for new javascript starry
        $total += $data[$k];
    }
    
    $comments = trim(compo2_strip($_REQUEST["comments"]));
    
    $e=array(
            "cid"=>$params["cid"],
            "to_uid"=>$ce["uid"],
            "from_uid"=>$params["uid"],
            "data"=>serialize($data),
            "ts"=>date("Y-m-d H:i:s"),
        );
    $total += strlen($comments);
    if (strlen($comments)) {
        $user = compo2_get_user($params["uid"]);
        compo2_insert("c2_comments",array(
            "cid"=>$params["cid"],
            "to_uid"=>$uid,
            "from_uid"=>$params["uid"],
            "ts"=>date("Y-m-d H:i:s"),
            "content"=>$comments,
            "get_user"=>serialize(array(
                "display_name"=>$user->display_name,
                "nicename"=>$user->nicename,
                "user_email"=>$user->user_email,
            )),
        ));
    }
    $r = compo2_query("select * from c2_comments where cid = ? and to_uid = ? and from_uid = ?",array(
        "cid"=>$params["cid"],
        "to_uid"=>$uid,
        "from_uid"=>$params["uid"],
        ));
    $e["comments"] = intval(count($r)!=0);
    
    if ($total) {
        compo2_query("delete from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"]));
        compo2_insert("c2_rate",$e);
    }
    
    _compo2_rate_recalc($params,$ce["uid"]);
    _compo2_rate_io_calc($params,$ce["uid"]);
    _compo2_rate_io_calc($params,$params["uid"]);
    header("Location: ?action=default"); die;
}

function _compo2_rate_io_calc($params,$uid) {
    $cid = $params["cid"];
    $ce = compo2_entry_load($params["cid"],$uid);
    $cc = array_pop(compo2_query("select count(*) cnt from c2_rate where cid = ? and to_uid = ?",array($cid,$uid)));
    $in = $cc["cnt"];
    $cc = array_pop(compo2_query("select count(*) cnt from c2_rate where cid = ? and from_uid = ?",array($cid,$uid)));
    $out = $cc["cnt"];
    
    compo2_update("c2_entry",array(
        "id"=>$ce["id"],
        "rate_in"=>$in,
        "rate_out"=>$out,
    ));
}

function _compo2_rate_recalc($params,$uid) {
    $cid = $params["cid"];
    $ce = compo2_entry_load($params["cid"],$uid);
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($cid,$uid));
    
    $data = array();
    foreach ($params["cats"] as $k) {
        $value = 0;
        $total = 0;
        $values = array();
        foreach ($r as $ve) {
            if ($ve["from_uid"] == $uid) { continue; } // no voting for self
            $dd = unserialize($ve["data"]);
            if (!strlen($dd[$k])) { continue; }
            $values[] = intval($dd[$k]);
        }
        sort($values);
        for($i=0;$i<$params["calc_droplow"];$i++) { array_shift($values); }
        for($i=0;$i<$params["calc_drophigh"];$i++) { array_pop($values); }
        foreach($values as $v) { $value += $v; $total += 1; }
        
        $data[$k] = ($total>=$params["calc_reqvote"]?round($value/$total,2):"");
    }
    compo2_update("c2_entry",array(
        "id"=>$ce["id"],
        "results"=>serialize($data),
    ));
}

?>