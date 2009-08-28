<?php

function _compo2_rate($params) {
    if (!$params["uid"]) {
        echo "<p class='message'>You must sign in to vote.</p>";
        return _compo2_preview($params);
    }

    // handle non-competitors ..
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if ((!$ce["id"]) || (!$ce["active"])) {
        $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"preview";
        if ($action == "edit") {
            return _compo2_active_form($params);
        } elseif ($action == "save") {
            return _compo2_active_save($params);
        } elseif ($action == "preview") {
            echo "<p class='message'>Voting is only available to participants.</p>";
            return _compo2_preview($params);
        }
        return;
    }

    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    if ($action == "default") {
        return _compo2_rate_list($params);
    } elseif ($action == "preview") {
        return _compo2_preview($params);
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

function _compo_show_comments($r) {
    foreach ($r as $ve) if (strlen(trim($ve["comments"]))) {
        $user = compo2_get_user($ve["from_uid"]);
        echo "<h4>{$user->display_name} says ...</h4>";
        echo "<p>".str_replace("\n","<br/>",htmlentities($ve["comments"]))."</p>";
    }
}

function _compo2_rate_comments($params) {
    $cid = $params["cid"]; $uid = $params["uid"];
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    
    echo "<p><a href='?action=default'>Back to Rate Entries</a></p>";
    
    echo "<h3>Comments on your Entry</h3>";
    
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($cid,$uid));
    _compo_show_comments($r);
}


function _compo2_rate_sort($a,$b) {
    return strcmp($a["s"],$b["s"]);
}

function _compo2_rate_list($params) {
    $r = compo2_query("select * from c2_entry where cid = ? and active = 1",array($params["cid"]));
    foreach ($r as $k=>$ce) {
        $r[$k]["s"] = md5("{$params["uid"]}|{$ce["cid"]}|{$ce["uid"]}");
    }
    usort($r,"_compo2_rate_sort");
    
    echo "<h3>Rate Entries</h3>";
    $n=0;
    echo "<table>";
    echo "<tr><th><th>C";
    foreach ($params["cats"] as $k) { echo "<th>".substr($k,0,3); }
    foreach ($r as $ce) {
        if ($ce["uid"] == $params["uid"]) { continue; }
        $ve = array_pop(compo2_query("select * from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"])));
        $ue = compo2_get_user($ce["uid"]);
        echo "<tr>";
        echo "<td><a href='?action=rate&uid={$ce["uid"]}'>".htmlentities($ue->display_name)."</a>";
        echo "<td>".(strlen($ve["comments"])?"x":"-");
        $data = unserialize($ve["data"]);
        foreach ($params["cats"] as $k) {
            echo "<td align=center>".(strlen($data[$k])?intval($data[$k]):"-");
        }
        $n += 1;
        if ($n >= 20 && !strlen($_REQUEST["more"])) { break; }
    }
    echo "</table>";
    
    echo "<p>";
    if (!strlen($_REQUEST["more"])) {
        echo "<a href='?more=1'>Show all entries</a> | ";
    }
    echo "<a href='?action=preview'>View all Screenshots</a> | ";
    echo "<a href='?action=edit'>Edit your entry</a> | ";
    echo "<a href='?action=comments'>See comments on your entry</a>";
    echo "</p>";
}

function _compo2_rate_rate($params) {
    $uid = intval($_REQUEST["uid"]);
    echo "<p><a href='?action=default'>Back to Rate Entries</a></p>";
    _compo2_preview_show($params,$uid);
    $ce = compo2_entry_load($params["cid"],$uid);
    $ve = array_pop(compo2_query("select * from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"])));
    echo "<h3>Rate this Entry</h3>";
    echo "<form method=post action='?action=submit&uid=$uid'>";
    echo "<p>";
    echo "<table>";
    $data = unserialize($ve["data"]);
    foreach ($params["cats"] as $k) {
        echo "<tr><th>".htmlentities($k);
        echo "<td>";
        $v = $data[$k];
        compo2_select("data[$k]",array(""=>"n/a","5"=>"5 - Best","4"=>"4","3"=>"3","2"=>"2","1"=>"1 - Worst"),$v);
    }
    echo "</table>";
    echo "</p>";
    echo "<h4>Comments (non-anonymous)</h4>";
    echo "<textarea name='comments' rows=4 cols=60>".htmlentities($ve["comments"])."</textarea>";
    echo "<p><input type='submit' value='Save'></p>";
    echo "</form>";
    
    echo "<hr/>";
    
    echo "<h4>Other user's comments</h4>";
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($params["cid"],$ce["uid"]));
    _compo_show_comments($r);

}

function _compo2_rate_submit($params) {
    $uid = intval($_REQUEST["uid"]);
    $ce = compo2_entry_load($params["cid"],$uid);
    compo2_query("delete from c2_rate where cid = ? and to_uid = ? and from_uid = ?",array($params["cid"],$ce["uid"],$params["uid"]));
    $data = array();
    foreach ($_REQUEST["data"] as $k=>$v) {
        $data[$k] = strlen($v)?intval($v):"";
    }
    
    compo2_insert("c2_rate",array(
        "cid"=>$params["cid"],
        "to_uid"=>$ce["uid"],
        "from_uid"=>$params["uid"],
        "data"=>serialize($data),
        "comments"=>compo2_strip($_REQUEST["comments"]),
    ));
    
    _compo2_rate_recalc($params,$ce["uid"]);
    header("Location: ?action=default"); die;
}


function _compo2_rate_recalc($params,$uid) {
    $cid = $params["cid"];
    $ce = compo2_entry_load($params["cid"],$uid);
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($cid,$uid));
    
    $data = array();
    foreach ($params["cats"] as $k) {
        $value = 0;
        $total = 0;
        foreach ($r as $ve) {
            if ($ve["from_uid"] == $uid) { continue; } // no voting for self
            $dd = unserialize($ve["data"]);
            if (!strlen($dd[$k])) { continue; }
            $value += intval($dd[$k]);
            $total += 1;
        }
        $data[$k] = ($total>=5?round($value/$total,2):"");
    }
    compo2_update("c2_entry",array(
        "id"=>$ce["id"],
        "results"=>serialize($data),
    ));
}

?>