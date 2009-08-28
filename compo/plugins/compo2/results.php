<?php

function _compo2_results($params) {
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        return _compo2_results_results($params);
    } elseif ($action == "preview") {
        return _compo2_preview($params);
    } elseif ($action == "edit") {
        return _compo2_active_form($params);
    } elseif ($action == "save") {
        return _compo2_active_save($params);
    }
}

function _compo2_results_results($params) {
    if (isset($_REQUEST["uid"])) { return _compo2_results_show($params,intval($_REQUEST["uid"])); }

    $r = compo2_query("select * from c2_entry where cid = ? and active = 1",array($params["cid"]));
    foreach ($r as $k=>$ce) {
        $r[$k]["results"] = unserialize($ce["results"]);
        $r[$k]["user"] = compo2_get_user($ce["uid"]);
    }
    
    // HACK: add in Coolness
    $cat = $params["cats"][] = "Coolness";
    foreach ($r as $k=>$ce) {
        $r[$k]["results"][$cat] = round(100*$ce["rate_out"]/(count($r)-1));
    }
    
    echo "<h3>Results</h3>";
    $cols = 3; $n = 0;
    echo "<table width=600>";
    foreach ($params["cats"] as $k) {
        if (($n%$cols)==0) { echo "<tr>"; } $n += 1;
        echo "<td>";
        _compo2_results_cat($params,$r,$k);
    
    }
    echo "</table>";
    
    echo "<p>";
    if (!strlen($_REQUEST["more"])) {
        echo "<a href='?more=1'>Show all entries.</a> | ";
    }
    echo "<a href='?action=preview'>View all Screenshots</a> | ";
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if ($ce["id"]) { echo "<a href='?action=edit'>Edit your entry.</a>"; }
    echo "</p>";
    
}

function _compo2_results_sort($a,$b) {
    return ($b["v"] - $a["v"])*1000;
}

function _compo2_results_cat($params,$r,$cat) {

    foreach ($r as $k=>$ce) {
        $r[$k]["v"] = $ce["results"][$cat];
    }
    usort($r,"_compo2_results_sort");

    echo "<table>";
    echo "<tr><th colspan=3>$cat";
    
    $myurl = get_bloginfo("url")."/wp-content/plugins/compo2/images";
    $n = 0; $t = 0; $p = -1;
    foreach ($r as $ce) {
        $v = $ce["v"];
        if ($v != $p) { $n += 1; } $p = $v;
        $img = "inone.gif";
        $vv = compo2_number_format($v);
        // HACK: for coolness
        if ($cat == "Coolness") {
            if ($v >= 25) { $n = 3; }
            if ($v >= 50) { $n = 2; }
            if ($v >= 75) { $n = 1; }
            $vv = intval($v)."%";
        }
        if ($n <= 3) {
            $map = array("1"=>"igold.gif","2"=>"isilver.gif","3"=>"ibronze.gif");
            $img = $map[$n];
        }
        if ($n <= 5) {
            $vv = "<b>$vv</b>";
        }
        echo "<tr>";
        echo "<td><img src='$myurl/$img'>";
        echo "<td align=right>$vv";
        echo "<td><a href='?uid={$ce["uid"]}'>{$ce["user"]->display_name}</a>";
        
        $t += 1;
        if ($t >= 5 && !strlen($_REQUEST["more"])) { break; }
    }
    
    echo "</table>";
}

function _compo2_results_show($params,$uid) {
    echo "<p><a href='?action=default'>Back to Results</a></p>";
    _compo2_preview_show($params,$uid);
    _compo2_show_comments($params["cid"],$uid);
}
    
function _compo2_results_ratings($params,$uid) {
    $ce = compo2_entry_load($params["cid"],$uid);
    $cid = $params["cid"];
    $ce = compo2_entry_load($params["cid"],$uid);
    $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($cid,$uid));
    $r2 = $r;
    echo "<h3>Ratings</h3>";
    
    echo "<table><tr>";
    foreach ($params["cats"] as $k) { echo "<th>".substr($k,0,3); }
    shuffle($r);
    foreach ($r as $ve) {
        echo "<tr>";
        $data = unserialize($ve["data"]);
        foreach ($params["cats"] as $k) {
            echo "<td align=center>".(strlen($data[$k])?intval($data[$k]):"-");
        }
    }
    echo "<tr>";
    $data= unserialize($ce["results"]);
    foreach ($params["cats"] as $k) {
        echo "<th align=center>".(compo2_number_format($data[$k]));
    }
    echo "</table>";

}
?>