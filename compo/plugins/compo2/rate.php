<?php

function _compo2_rate($params) {
    if (!$params["uid"]) { return _compo2_preview($params); }

    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        return _compo2_rate_list($params);
    } elseif ($action == "preview") { // HACK: so this action works
        return _compo2_rate_list($params);
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
    
    if (!strlen($_REQUEST["more"])) {
        echo "<p><a href='?more=1'>Show all entries.</a></p>";
    }
    
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if ($ce["id"]) { echo "<p><a href='?action=edit'>Edit your entry.</a></p>"; }
}

function _compo2_rate_rate($params) {
    $uid = intval($_REQUEST["uid"]);
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
    die;
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