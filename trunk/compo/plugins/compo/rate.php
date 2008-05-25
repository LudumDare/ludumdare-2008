<?php

function compo_get_finalists($cid) {
    $sql = "select wp_posts.post_author, count(*) as cnt ".
        " from wp_posts, wp_term_relationships, wp_term_taxonomy, wp_terms ".
        " where ".
            " wp_posts.ID = wp_term_relationships.object_id ".
            " and wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id ".
            " and wp_term_taxonomy.term_id = wp_terms.term_id ".
            " and ( ".
                "(wp_term_taxonomy.taxonomy = 'category' and wp_term_taxonomy.term_id = ?) ".
                " or (wp_term_taxonomy.taxonomy = 'post_tag' and wp_terms.slug = 'final')".
                ") ".
        " group by wp_posts.ID";
    $sql = "select * from ($sql) t1 where t1.cnt = 2";
    
    $r = compo_query($sql,array($cid));
    $users = array(); foreach ($r as $e) { $users[$e["post_author"]] = $e["post_author"]; }
    return array_values($users);
}
    

function compo_can_rate($cid,$uid) {
    global $compo;
    if (!is_user_logged_in()) { return; }
    
    $users = compo_get_finalists($cid);
    if (!in_array($uid,$users)) { return; }
    return true;
}

function compo_rate($cid,$uid) {
    $user = wp_get_current_user();
    $k = "compo-$cid-state";
    $v = get_option($k);
    
    if ($v == "") { return; }
    
    if ($v == "rate") {
        if (!compo_can_rate($cid,$user->ID)) {
            compo_trophy($uid);
            return;
        }

        compo_rate_rate($cid,$uid);
        
        compo_show_comments($cid,$uid);
        /*
        $topurl = get_bloginfo("url");
        $auth = get_userdata($uid);
        $name = $auth->user_nicename;
        echo "<p><a href='$topurl/author/$name/'>See {$name}'s trophies!</a></p>"; 
        */
    } elseif ($v == "results") {
//         compo_rate_results($cid,$uid);
        compo_trophy($uid);
        compo_show_details($cid,$uid);
        compo_show_comments($cid,$uid);
        
//         echo "<p>TODO: compo_rate.results</p>";
    }
}

function compo_show_comments($cid,$uid) {
    global $compo;
    $r = compo_query("select * from {$compo["rate.table"]} where cid = ? and  to_uid = ? and from_uid != 0",array($cid,$uid));
    
    foreach ($r as $e) {
        $user = get_userdata($e["from_uid"]);
        $comment = $e["comment"];
        if (!strlen($comment)) {
//             echo "<h3>{$user->display_name} says <i>Nothing at all.</i>  <b>What a slacker!</b></h3>";
        } else {
            echo "<h3>{$user->display_name} says ...</h3>";
            echo "<p>".str_replace("\n","<br/>",htmlentities($comment))."</p>";
        }
    }
}



function compo_rate_rate($cid,$uid) {
    global $compo;
    
    $cat = get_category($cid);
    $topurl = get_bloginfo("url");

    
    $k = "compo-$cid-cats";
    $cats = get_option($k);
    
    $action = (isset($_REQUEST["compo_action"])?$_REQUEST["compo_action"]:"default");
    $user = wp_get_current_user();
    
    if ($user->ID == $uid) {
//         echo "<p>You don't get to rate your own entry, silly!</p>";
        compo_trophy($uid);
        return;
    }
    
    if ($action == "submit") {
        $e = array();
        foreach (explode(",",$cats) as $k) {
            $v = $_REQUEST[$k];
            if (strlen($v)) { $e[$k]=max(1,min(5,intval($v))); }
        }
        compo_query("delete from {$compo["rate.table"]} where cid = ? and from_uid = ? and to_uid = ?",array($cid,$user->ID,$uid));
        compo_query("insert into {$compo["rate.table"]} (cid,from_uid,to_uid,data,comment) values (?,?,?,?,?)",array($cid,$user->ID,$uid,serialize($e),$_REQUEST["comment"]));
        
        compo_calc($cid,$uid);
//         return;
//         echo "<p><b>Ratings saved.</b></p>";
        header("Location: $topurl/category/{$cat->slug}/"); die;
    }

    echo "<h3>Rate this entry</h3>";
    echo "<p>Rate on a 1-5 scale where 1 is lowest and 5 is highest.  Mark n/a if the category is not applicable to the entry, or you are not able to rate that category.</p>";
    echo "<form method=post action='{$_SERVER['REQUEST_URI']}'>";
    echo "<input type='hidden' name='compo_action' value='submit'>";
    echo "<table>";
    $r = compo_query("select * from {$compo["rate.table"]} where cid = ? and from_uid = ? and to_uid = ?",array($cid,$user->ID,$uid));
    $ee = $r[0];
//     print_r($ee);
    $e = (count($r)?unserialize($ee["data"]):array());
    foreach (explode(",",$cats) as $k) {
        echo "<tr>";
        echo "<th>".ucfirst($k);
        echo "<td>";
        $v = (isset($e[$k])?$e[$k]:"");
        compo_select($k,array(""=>"n/a","5"=>"5","4"=>"4","3"=>"3","2"=>"2","1"=>"1"),$v);
    }
    echo "</table>";
    echo "<p>Comments:<br><textarea cols=50 rows=5 name='comment'>".htmlentities($ee["comment"])."</textarea></p>";
    echo "<input type='submit' value='Save'>";
    echo "</form>";
}

function compo_show_details($cid,$uid) {
    global $compo;
    $user = wp_get_current_user();
    $cats = get_option("compo-$cid-cats");
    
    $r = compo_query("select * from {$compo["rate.table"]} where cid = ? and to_uid = ?",array($cid,$uid));
    shuffle($r); // don't want who voted for what to be obvious

    echo "<h3>Ratings</h3>";
    echo "<p><table>";
    echo "<tr><th>";
    foreach (explode(",",$cats) as $k) { echo "<th>".ucfirst(substr($k,0,3)); }
    foreach ($r as $e) {
        if ($e["from_uid"] == "0") { $totals = $e; continue; }
        echo "<tr>";
        echo "<th>";
        $tp = "td";
        if ($e["from_uid"] == $user->ID) { echo "You";$tp="th"; }
        $re = unserialize($e["data"]);
        foreach (explode(",",$cats) as $k) {
            $v = isset($re[$k])?$re[$k]:"";
            echo "<$tp align=center>$v";
        }
    }
    $e = $totals;
    echo "<tr><th>";
    $re = unserialize($e["data"]);
    foreach (explode(",",$cats) as $k) {
        $v = isset($re[$k])?$re[$k]:"";
        $v = compo_number_format($v);
        echo "<th align=center>$v";
    }
    echo "</table></p>";
}

function compo_calc($cid,$uid) {
    global $compo;
    $r = compo_query("select * from {$compo["rate.table"]} where cid = ? and to_uid = ? and from_uid != 0",array($cid,$uid));
    $total = array();
    $count = array();
    foreach ($r as $ee) {
        $e = unserialize($ee["data"]);
        foreach ($e as $k=>$v) {
            if (!strlen($v)) { continue; }
            $total[$k] += $v;
            $count[$k] += 1;
        }
    }
    
    $e = $total;
    foreach ($total as $k=>$v) {
        if ($count[$k] < 5) {
            $e[$k] = ""; // no score for you!
            continue;
        }
        $e[$k] = floatval($v) / floatval($count[$k]);
    }
    
    compo_query("delete from {$compo["rate.table"]} where cid = ? and from_uid = ? and to_uid = ?",array($cid,0,$uid));
    compo_query("insert into {$compo["rate.table"]} (cid,from_uid,to_uid,data) values (?,?,?,?)",array($cid,0,$uid,serialize($e)));
}

?>