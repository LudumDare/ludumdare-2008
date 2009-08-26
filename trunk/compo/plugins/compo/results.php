<?php
function compo_number_format($v) {
    if (!strlen($v)) { return "-"; }
    return number_format($v,2);
}
function compo_results($cid) {
    global $compo;
    $cid = intval($cid);
    
    $cat = get_category($cid);
    $state = get_option("compo-$cid-state");
    if ($state == "") { return; } 
    
    $users = compo_get_finalists($cid);
    
    $topurl = get_bloginfo("url");
    $cats = get_option("compo-$cid-cats");
        
/*    $user = wp_get_current_user();
    print_r($user);*/
    
    $user = wp_get_current_user();
    if (isset($_REQUEST["compo_recalc"]) && $user->user_level == 10) {
        foreach ($users as $uid) {
            compo_calc($cid,$uid);
        }
        echo "<p>Recalculated results...</p>";
    }
    
    if ($state == "active") {
        $user = wp_get_current_user();
        echo "<h3>Compo Active</h3>";
/*        echo "<p>";
        echo "Your entry wi
        foreach (explode(",",$cats) as $k) { echo ucfirst($k)." "; }
        echo "</p>";*/
        echo "<p>";
        foreach ($users as $final_id) {
            $auth = get_userdata($final_id);
            echo "<a href='$topurl/?category_name={$cat->slug}&author_name={$auth->user_nicename}'>{$auth->display_name}</a> ";
        }
        echo "</p>";
    } elseif ($state == "rate") {
        $r = compo_query("select to_uid post_author,count(*) cnt from {$compo["rate.table"]} where cid = ? and from_uid != 0 and to_uid in (".implode(",",$users).") group by to_uid order by cnt desc",array($cid));
        /*
        // sort by cnt
        $data = array();
        foreach ($users as $final_id) {
            $data[$final_id] = "0|$final_id";
        }
        foreach ($r as $e) {
            $data[$e["post_author"]] = "{$e["cnt"]}|{$e["post_author"]}";
        }
        sort($data);
        */
        
        // sort by random (but diff rand per user)
        $data = array();
        $user = wp_get_current_user();
        foreach ($users as $final_id) {
            $key = md5("{$user->ID}|$final_id");
            $cnt = 0;
            $data[$key] = "$cnt|$final_id";
        }
        foreach ($r as $e) {
            $final_id = $e["post_author"];
            $cnt = $e["cnt"];
            $key = md5("{$user->ID}|$final_id");
            $data[$key] = "$cnt|$final_id";
        }
        ksort($data);
        
        $user = wp_get_current_user();
        if (!compo_can_rate($cid,$user->ID)) { return; }
        echo "<h3>Your Ratings</h3>";
        echo "<p><table>";
        echo "<tr><td><th>C";
        foreach (explode(",",$cats) as $k) { echo "<th>".ucfirst(substr($k,0,3)); }
//         foreach ($r as $e) {
        $total = 0;
        foreach ($data as $dd) {
            list($cnt,$final_id) = explode("|",$dd);
            echo "<tr>";
//             $auth = get_userdata($e["post_author"]);
            $auth = get_userdata($final_id);
            echo "<td><a href='$topurl/?category_name={$cat->slug}&author_name={$auth->user_nicename}'>{$auth->display_name}</a> ($cnt)";
            $r = compo_query("select * from {$compo["rate.table"]} where cid = ? and from_uid = ? and to_uid = ?",array($cid,$user->ID,$auth->ID));
            echo "<td>"; if (strlen($r[0]["comment"])) { echo "X"; }
            $re = (count($r)?unserialize($r[0]["data"]):array());
            foreach (explode(",",$cats) as $k) {
                $v = isset($re[$k])?$re[$k]:"";
                echo "<td align=center>$v";
            }
            $total += 1;
            if ($total > 20 && !isset($_REQUEST["compo_results_all"])) { break; }
        }
        echo "</table></p>";
        if (!isset($_REQUEST["compo_results_all"])) {
            echo "<p><form action='?compo_results_all=1'><input type='submit' value='Show all Entries'></form></p>";
        }
    } elseif ($state == "results") {
//         $r = compo_query($sql,array($cid));
        
        $vdata = array();
        $cdata = array();
        $total=0;
        foreach ($users as $final_id) {
            $auth = get_userdata($final_id);
            $rr = compo_query("select * from {$compo["rate.table"]} where cid = ? and from_uid = ? and to_uid = ?",array($cid,0,$auth->ID));
            if (!count($rr)) { continue; }
            $re = (count($rr)?unserialize($rr[0]["data"]):array());
//             $e["auth"] = $auth;
            foreach (explode(",",$cats) as $k) {
                $v = isset($re[$k])?$re[$k]:"";
                $v = compo_number_format($v,2);
                if ($v!="") { $vdata[$k][]= $v; $cdata[$k][] = "$v:{$auth->user_nicename}:{$auth->display_name}"; }
                $e[$k] = $v;
            }
            $total += 1;
//             $data[] = $e;
        }
        
        foreach ($cdata as $k=>$lst) { rsort($cdata[$k]); }
        foreach ($vdata as $k=>$lst) { rsort($vdata[$k]); }
        
        echo "<h3>Final Results</h3>";
        
        $limit = (isset($_REQUEST["compo_limit"])?$_REQUEST["compo_limit"]:0);
        if (!$limit) {
            echo "<p><a href='?compo_limit=$total'>Show full results</a></p>";
        }
//         echo "<p><a href='?compo_page=who_voted_prize'>See who did the most voting</a></p>";
        _compo_show_results($cid,$cats,$cdata,$vdata,max(5,min($total,$limit)));
    
    }
}

function _compo_show_results($cid,$cats,$cdata,$vdata,$limit) {
    $cats = explode(",",$cats);
    $cols = 4;
    $n =0;
    for ($i=0; $i<count($cats); $i+=$cols) {
        _compo_show_some_results($cid,array_slice($cats,$i,$cols),$cdata,$vdata,$limit);
    }
}

function _compo_show_some_results($cid,$cats,$cdata,$vdata,$limit) {
    $cat = get_category($cid);
    $topurl = get_bloginfo("url");

    echo "<p><table>";
    echo "<tr>";
    foreach ($cats as $k) { echo "<th align=center colspan=3 width=125>".ucfirst($k); }
    for ($i =0; $i<$limit; $i++) {
        echo "<tr>";
        foreach ($cats as $k) {
            list($v,$name,$dname) = explode(":",$cdata[$k][$i]);
            $ulink = "<a href='$topurl/?category_name={$cat->slug}&author_name=$name'>$dname</a>";
            
            $img = "inone.gif";
            $n = array_search($v,$vdata[$k])+1;
            $vv = $v;
            if ($n <= 3) {
                $map = array("1"=>"igold.gif","2"=>"isilver.gif","3"=>"ibronze.gif");
                $img = $map[$n];
            }
            if ($n <= 5) {
                $vv = "<b>$v</b>";
            }
    
            
//             echo "<nobr><img src='$topurl/wp-content/plugins/compo/images/$img' align=absmiddle> $vv $ulink </nobr>";
            echo "<td><img src='$topurl/wp-content/plugins/compo/images/$img'><td align=center>$vv<td>$ulink";
        }
    }
    echo "</table></p>";

}


/*
        echo "<p><table>";
        echo "<tr><td>";
        foreach (explode(",",$cats) as $k) { echo "<th align=center>".ucfirst($k); }
        foreach ($data as $e) {
            $auth = $e["auth"];
            echo "<tr>";
            $ulink = "<a href='$topurl/?category_name={$cat->slug}&author_name={$auth->user_nicename}'>{$auth->user_nicename}</a>";
            echo "<td>";
            foreach (explode(",",$cats) as $k) {
                $v = $e[$k];
                $n = array_search($v,$cdata[$k])+1;
                echo "<td align=left>";
                $img = "inone.gif";
                if ($n <= 3) {
                    $map = array("1"=>"igold.gif","2"=>"isilver.gif","3"=>"ibronze.gif");
                    $img = $map[$n];
                }
                echo "<nobr><img src='$topurl/wp-content/plugins/compo/images/$img' align=absmiddle> <b>$v</b> $ulink </nobr>"; 
            }
        }
        echo "</table></p>";
*/
?>