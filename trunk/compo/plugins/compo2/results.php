<?php

function _compo2_results($params) {
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        return _compo2_results_results($params);
    } elseif ($action == "preview") {
        return _compo2_preview($params);
    } elseif ($action == "top") {
        return _compo2_results_top($params);
    } elseif ($action == "rate") {
        header("Location: ./?action=preview&uid=".intval($_REQUEST["uid"])); die;
    } elseif ($action == "edit") {
        return _compo2_active_form($params);
    } elseif ($action == "save") {
        return _compo2_active_save($params);
    }
}

function _compo2_results_sort($a,$b) {
    return ($b["v"] - $a["v"])*1000;
}

function _compo2_results_sort2($rr) {
    $r = array();
    foreach ($rr as $ce) {
        $key = sprintf("%08d",intval((1000+$ce["v"])*1000))."|{$ce["uid"]}";
        $r[$key] = $ce;
    }
    krsort($r);
    return $r;
}
    

function _compo2_get_results($params) {
//     if (($cres=compo2_cache_read($params["cid"],$ckey="get_results"))!==false) { return unserialize($cres); }

    global $compo2;
    $compo2["log.enabled"] = false;
    $tm = microtime(true);

    $r = compo2_query("select * from c2_entry where cid = ? and active = 1",array($params["cid"]));
    $total = 0;
    foreach ($r as $k=>$ce) {
        $r[$k]["results"] = unserialize($ce["results"]);
        $r[$k]["user"] = unserialize($ce["get_user"]);
        $r[$k]["values"] = array();
        $r[$k]["places"] = array();
        $total += intval($ce["is_judged"]!=0);
    }
    
    // HACK: add in Coolness
    $cat = $params["cats"][] = "Coolness";
    foreach ($r as $k=>$ce) {
        $r[$k]["results"][$cat] = round(100*$ce["rate_out"]/(max($total,2)-1));
    }
    
    foreach ($params["cats"] as $cat) {
        foreach ($r as $k=>$ce) {
            $r[$k]["v"] = $ce["results"][$cat];
        }
        $r = _compo2_results_sort2($r);
//         usort($r,"_compo2_results_sort");

        $myurl = get_bloginfo("url")."/wp-content/plugins/compo2/images";
        $n = 0; $t = 1; $p = -1;
        foreach ($r as $k=>$ce) {
            if ($cat != "Coolness" && !$ce["is_judged"]) { continue; }
            
            $v = $ce["v"];
            if ($v != $p) { $n = $t; }
            $p = $v;
            $t += 1;
            
            $vv = compo2_number_format($v);
            // HACK: for coolness
            if ($cat == "Coolness") {
                if ($v >= 25) { $n = 3; }
                if ($v >= 50) { $n = 2; }
                if ($v >= 75) { $n = 1; }
                $vv = intval($v)."%";
            }
            $r[$k]["values"][$cat] = $vv;
            $r[$k]["places"][$cat] = (strcmp($vv,"-")!=0?$n:0);
            
        }
    }
    
    foreach ($r as $k=>$ce) {
        if (($cres=compo2_cache_read($params["cid"],$ukey="get_results:{$ce["uid"]}"))==false) {
            compo2_cache_write($params["cid"],$ukey,serialize($ce));
        }
    }
    
//     compo2_cache_write($params["cid"],$ckey,serialize($r));
    
    $compo2["log.enabled"] = true;
    compo2_log("_compo2_get_results",microtime(true)-$tm);

    return $r;
}

function _compo2_results_results($params) {
    if (isset($_REQUEST["uid"])) { return _compo2_results_show($params,intval($_REQUEST["uid"])); }
    
    //more=1 is the one alternate
    $more = intval(strlen($_REQUEST["more"])!=0);
    if (($cres=compo2_cache_read($params["cid"],$ckey="results_results:$more"))!==false) { echo $cres; return; }
    ob_start();
    
    $r = _compo2_get_results($params);
    
    echo "<h3>Results</h3>";
    echo "<p><a href='?action=top'>Show top entries</a></p>";
    $cols = 4; $n = 0;
    echo "<table class='results'>";
    $cat = $params["cats"][] = "Coolness";
    foreach ($params["cats"] as $cat) {
        foreach ($r as $k=>$ce) {
            $r[$k]["v"] = -$ce["places"][$cat];
            $r[$k]["place"] = $ce["places"][$cat];
            $r[$k]["value"] = $ce["values"][$cat];
        }
        $r = _compo2_results_sort2($r);
//         usort($r,"_compo2_results_sort");
//     foreach ($r as $k => $res) {
        if (($n%$cols)==0) { echo "<tr>"; } $n += 1;
        echo "<td valign=top>";
        _compo2_results_cat($params,$cat,$r);
    
    }
    echo "</table>";
    
    echo "<p>";
    echo "<a href='?action=top'>Show top entries</a> | ";
    if (!strlen($_REQUEST["more"])) {
        echo "<a href='?more=1'>Show all results.</a> | ";
    }
    echo "<a href='?action=preview'>Show all Entries</a> | ";
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if ($ce["id"]) { echo "<a href='?action=edit'>Edit your entry.</a>"; }
    echo "</p>";
    
    $cres = ob_get_contents();
    ob_end_clean();
    compo2_cache_write($params["cid"],$ckey,$cres);
    
    echo $cres;
}

function _compo2_results_cat($params,$cat,$r) {

    echo "<table class='results-cat'>";
//     echo "<tr><th colspan=3>$cat";
//     $admin = ($params["state"]=="admin"?"&admin=1":"");
    echo "<tr><th colspan=3><a href='?action=top&cat=$cat'>$cat</a>";
    
    $t = 0;
    $myurl = get_bloginfo("url")."/wp-content/plugins/compo2/images";
    $pn = 0;
    foreach ($r as $ce) {
        $vv = $ce["value"];
        $n = $ce["place"];
        if ($n==0) { continue; }
        
        if ($t >= 25 && $pn != $n) { break; } $pn = $n;
        
        $img = "inone.gif";
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
        echo "<td><a href='?uid={$ce["uid"]}'>{$ce["user"]["display_name"]}</a>";
        
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

/*
function _compo2_results_ratings_old($params,$uid) {
    $ce = compo2_entry_load($params["cid"],$uid);
    $cid = $params["cid"];
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
*/

function _compo2_results_ratings($params,$uid) {
    $myurl = get_bloginfo("url")."/wp-content/plugins/compo2/images";
    
    echo "<h3>Ratings</h3>";
    
    $key = "get_results:$uid";
    $cres = compo2_cache_read($params["cid"],$key);
    $e = unserialize($cres);
    
    echo "<p>";
    asort($e["places"]);
    echo "<table cellpadding=5>";
    foreach ($e["places"] as $cat=>$nn) if ($nn!=0) {
        $img = "inone.gif";
        echo "<tr>";
        if ($nn <= 3) {
            $map = array("1"=>"igold.gif","2"=>"isilver.gif","3"=>"ibronze.gif");
            $img = $map[$nn];
            echo "<td align=center><img src='$myurl/$img' align=absmiddle>";
        } else {
            echo "<td align=center>#$nn";
        }
        $val = $e["values"][$cat];
        echo "<td>$cat<td align=right>$val";
    }
    echo "</table>";
    echo "</p>";

}


/*
function _compo2_get_top($params) {
    $r = _compo2_get_results($params);
    
    $rr = array();
    foreach ($r as $cat=>$res) {
        foreach ($res as $ce) {
            $uid = $ce["uid"];
            if (!isset($rr[$uid])) {
                $rr[$uid] = array(
                    "info"=>$ce,
                    "places" => array(),
                    "values"=>array(),
                    "v"=>0,
                );
            }
            $rr[$uid]["places"][$cat] = $ce["place"];
            $rr[$uid]["values"][$cat] = $ce["value"];
            $rr[$uid]["v"] += max(0,11-$ce["place"]);
        }
    }
    usort($rr,"_compo2_results_sort");
    return $rr;
}
*/

function _compo2_results_top($params) {
    $cat = $_REQUEST["cat"];
    $params["cats"][] = "Coolness";
    if (!in_array($cat,$params["cats"])) { $cat = $params["topcat"]; } // HACK: why overall? who knows!
    
    // CACHING ///////////////
    unset($_REQUEST["more"]);
    if (($cres=compo2_cache_read($params["cid"],$ckey="results_top:$cat"))!==false) { echo $cres; return; }
    ob_start();
    
//     $r = _compo2_get_top($params);
    $r = _compo2_get_results($params);

    // also, this now ignores the nice counting of trophes done earlier ..
    $_cat = $cat; // backup for later
    
    /*
    if (strlen($cat)) {
        foreach ($r as $k=>$e) {
            $r[$k]["v"] = -$e["places"][$cat];
        }
        usort($r,"_compo2_results_sort");
    }
    */
    
    foreach ($r as $k=>$ce) {
        $r[$k]["v"] = -$ce["places"][$cat];
        $r[$k]["place"] = $ce["places"][$cat];
        $r[$k]["value"] = $ce["values"][$cat];
    }
    $r = _compo2_results_sort2($r);
//     usort($r,"_compo2_results_sort");

    
    echo "<p><a href='./'>Back to Results</a></p>";
    
    echo "<h3>Top Entries - ".htmlentities($cat)."</h3>";
    $myurl = get_bloginfo("url")."/wp-content/plugins/compo2/images";
    
    $t = 1;
    $ties = array();
    foreach ($r as $e) {
        $n = intval($e["places"][$_cat]);
        @$ties[$n] += 1;
    }
    
    $last = -1;
    echo "<table class='results-top'>";
    $row = 0;
    foreach ($r as $e) {
//         $ce = $e["info"];
        $ce = $e;
        $shots = unserialize($ce["shots"]);
        $fname = array_shift($shots);
        $link = "?uid={$ce["uid"]}";
        
//         $n = intval($e["places"][$_cat]);
        $n = $e["place"];
        if ($n == 0) { continue; } // get rid of un-judged items
        if ($last != -1 && $last != $n) { break; } // allow several last-places to show up
        
        $klass = "class='alt-".(1+(($row++)%2))."'";
        
        echo "<tr>";
        echo "<td valign=top align=center $klass><b>$n.</b>"; if ($ties[$n]>1) { echo "<br/><i>TIE</i>"; }
        echo "<td align=center valign=top $klass>";
        echo "<a href='$link'><img src='".compo2_thumb($fname,160,160)."' class='screenshot'></a>";
        echo "<td valign=top $klass>";
        echo "<div><a href='$link'><b>".htmlentities($ce["title"])."</b> - ".htmlentities($ce["user"]["display_name"])."</a></div>";
        echo "<p>"; _compo2_preview_show_links($ce); echo "</p>";
        echo "<div class='notes'>".str_replace("\n","<br/>",htmlentities(substr($ce["notes"],0,140)))." ...</div>";
        echo "<td valign=top $klass>";
        asort($e["places"]);
        foreach ($e["places"] as $cat=>$nn) if ($nn <= 50 && $nn !=0) {
            $img = "inone.gif";
            echo "<div><nobr>";
            if ($nn <= 3) {
                $map = array("1"=>"igold.gif","2"=>"isilver.gif","3"=>"ibronze.gif");
                $img = $map[$nn];
                echo "<img src='$myurl/$img' align=absmiddle> - $cat";
            } else {
                echo "#$nn - $cat";
            }
            echo "</nobr></div>";
        }


        if ($t >= 50 && !strlen($_REQUEST["more"])) { $last = $n; }
        $t += 1;
//         echo "<tr><td>&nbsp;";
    }
    echo "</table>";
    
    echo "<p>";
    $cat = urlencode($_cat);
//     echo "<a href='?action=top&cat=$cat&more=1'>Show all Entries</a> | ";
    echo "<a href='./'>Back to Results</a>";
    echo "</p>";
    
    $cres = ob_get_contents();
    ob_end_clean();
    compo2_cache_write($params["cid"],$ckey,$cres);
    
    echo $cres;

    
}
?>