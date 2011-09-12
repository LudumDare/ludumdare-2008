<?php


function _compo2_preview_sort($a,$b) {
    return strcasecmp($a["title"],$b["title"]);
}

function _compo2_preview($params,$_link="?action=preview") {
    if (isset($_REQUEST["uid"])) {
        echo "<p><a href='?action=preview'>Back to View all Entries</a></p>";
        _compo2_preview_show($params,intval($_REQUEST["uid"]));
        _compo2_show_comments($params["cid"],intval($_REQUEST["uid"]));
        return;
    }
    $cats = array(""=>"All Entries");
    foreach ($params["divs"] as $div) {
        $cats[$div] = "{$params["{$div}_title"]} Entries";
    }

    $etype = $_REQUEST["etype"];
    @$q = $_REQUEST["q"];
    $limit = 24;
    $start = 0;
    if (isset($_REQUEST["start"])) { $start = intval($_REQUEST["start"]); }
    $start = intval($start); $limit = intval($limit);
    
    if (($cres=compo2_cache_read($params["cid"],$ckey="_compo2_preview:$etype:$q:$start",15*60))!==false) { echo $cres; return; }
    ob_start();
    
    if (!strlen($q)) {
        $cnte = array_pop(compo2_query("select count(*) _cnt from c2_entry where etype like ? and cid = ? ".(!($params["state"]=="admin")?" and active=1":""),array("%$etype%",$params["cid"])));
    } else {
        $cnte = array_pop(compo2_query("select count(*) _cnt from c2_entry where (notes like ? OR links like ? OR get_user like ?) and  etype like ? and cid = ? ".(!($params["state"]=="admin")?" and active=1":""),array("%$q%","%$q%","%$q%","%$etype%",$params["cid"])));
    }

    $cnt = $cnte["_cnt"];
    
    
    if (!strlen($q)) {
        $r = compo2_query("select * from c2_entry where etype like ? and cid = ? ".(!($params["state"]=="admin")?" and active=1":"")." limit $start,$limit",array("%$etype%",$params["cid"]));
    } else {
        $r = compo2_query("select * from c2_entry where (notes like ? OR links like ? OR get_user like ?) and etype like ? and cid = ? ".(!($params["state"]=="admin")?" and active=1":"")." limit $start,$limit",array("%$q%","%$q%","%$q%","%$etype%",$params["cid"]));
    }
    usort($r,"_compo2_preview_sort");

    echo "<h3>".htmlentities($cats[$etype])." ($cnt)</h3>";
    
    ob_start();
    echo "<p>";
    $pre = "";
    if (count($params["divs"]) > 1) {
        foreach ($cats as $kk=>$vv) {
            echo "$pre<a href='?action=preview&etype=$kk'>$vv</a>"; $pre = " | ";
        }
    }
    $ce = compo2_entry_load($params["cid"],$params["uid"]);
    if ($ce["id"]) { echo "$pre<a href='?action=edit'>Edit your entry</a> | <a href='?action=preview&uid={$ce["uid"]}'>View your entry</a>"; }
    echo "</p>";
    $links = ob_get_contents();
    ob_end_clean();
    echo $links;
    
    echo "<form style='text-align:left;margin:0px;'>";
    echo "<input type='hidden' name='action' value='preview'>";
    echo "<input type='text' name='q' value='".htmlentities($q)."'>";
    echo " <input type='submit' value='Search'>";
    echo "</form>";

    if (!$cnt) {
        echo "<p>No entries found.</p>";
    } else {

        ob_start();
        echo "<p>";
        if ($start > 0) {
            $i = max(0,$start-$limit);
            echo "<a href='?action=preview&q=".urlencode($q)."&etype=".urlencode($etype)."&start=$i'>Previous</a> ";
        }
        echo " [ ";
        $n=1;
        for ($i=0; $i<$cnt; $i+=$limit) {
            if ($i == $start) { echo "<b>$n</b> "; } else {
                echo "<a href='?action=preview&q=".urlencode($q)."&etype=".urlencode($etype)."&start=$i'>$n</a> ";
            }
            $n += 1;
        }
        echo " ] ";
        if ($start < ($cnt-$limit)) {
            $i = $start+$limit;
            echo "<a href='?action=preview&q=".urlencode($q)."&etype=".urlencode($etype)."&start=$i'>Next</a> ";
        }
        echo "</p>";
        $paging = ob_get_contents();
        ob_end_clean();
        
        echo $paging;
    
    
        $cols = 6;
        $n = 0;
        $row = 0;
        echo "<table class='preview'>";
        foreach ($r as $e) {
            if (($n%$cols)==0) { echo "<tr>"; $row += 1; } $n += 1;
            $klass = "class='alt-".(1+(($row)%2))."'";
            echo "<td valign=bottom align=center $klass>";
            $link = "$_link&uid={$e["uid"]}";
            echo "<div><a href='$link'>";
            $shots = unserialize($e["shots"]);
            echo "<img src='".compo2_thumb($shots["shot0"],120,90)."'>";
            echo "<div class='title'><i>".htmlentities($e["title"])."</i></div>";
            $ue = unserialize($e["get_user"]);
            echo $ue["display_name"];
            echo "</a></div>";
            if ($e["disabled"]) { echo "<div><i>disabled</i></div>"; }
            else { if (!$e["active"]) { echo "<div><i>inactive</i></div>"; } }
        }
        echo "</table>";
    
        echo $paging;
    }

    echo $links;

    $cres = ob_get_contents();
    ob_end_clean();
    compo2_cache_write($params["cid"],$ckey,$cres);
    
    echo $cres;

}

function compo2_strip($v) {
    return stripslashes($v);
}

function _compo2_preview_show_links($ce) {
    $pre = "";
    foreach (unserialize($ce["links"]) as $le) {
        if (!strlen($le["title"])) { continue; }
        $link = $le["link"];
        if (strpos($link,"javascript:") === 0) { continue; }
        if (strpos($link,"?") === 0) { continue; }
        if (!preg_match("/^\w+\:\/\//",$link)) { continue; }
        echo "$pre<a href=\"".htmlentities($link)."\" target='_blank'>".htmlentities($le["title"])."</a>";
        $pre = " | ";
    }
}

function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}


function _compo2_preview_comments($params,$uid,$form=true) {
    if ($form) {
        if ($params["uid"]) {
            $comments = trim(compo2_strip($_REQUEST["comments"]));
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
                header("Location: ?action=preview&uid=$uid"); die;
            }
        }
    }
            
    $r = compo2_query("select * from c2_comments where cid = ? and to_uid = ? order by ts asc",array($params["cid"],$uid));
    
    echo "<h3>Comments</h3>";
    $pe = array();
    foreach ($r as $e) if (strlen(trim($e["content"]))) {
        // get rid of double posts.
        if (strcmp($e["from_uid"],$pe["from_uid"])==0 &&
            strcmp($e["content"],$pe["content"])==0) { continue; }
        $pe = $e;
        $user = unserialize($e["get_user"]);
        echo "<div class = 'comment'>";
        echo get_gravatar($user["user_email"],48,'mm','g',true,array("align"=>"right","class"=>"gravatar"));
        echo "<div><strong>{$user["display_name"]} says ...</strong></div>";
        echo "<div><small>".date("M j, Y @ g:ia",strtotime($e["ts"]))."</small></div>";
        echo "<p>".str_replace("\n","<br/>",htmlentities(trim($e["content"])))."</p>";
        echo "</div>";
    }
    if ($form) {
        if ($params["uid"]) {
            echo "<form method='post' action='?action=preview&uid=$uid'>";
            echo "<textarea name='comments' rows=4 cols=60></textarea>";
            echo "<p><input type='submit' value='Submit Comment'></p>";
        } else {
            echo "<p>You must sign in to comment.</p>";
        }
    }
}
        

function _compo2_preview_show($params,$uid,$comments=true) {
    $ce = compo2_entry_load($params["cid"],$uid);
    $user = unserialize($ce["get_user"]);
    
    echo "<h3>".htmlentities($ce["title"])." - {$user["display_name"]}";
    $div = $ce["etype"];
    echo " - <i>{$params["{$div}_title"]} Entry</i>";
    echo "</h3>";
    
    echo "<p class='links'>";
    _compo2_preview_show_links($ce);
    echo "</p>";
    
    echo "<p>".str_replace("\n","<br/>",htmlentities($ce["notes"]))."</p>";
    
    $shots = unserialize($ce["shots"]);
    $fname = array_shift($shots);
        
    echo "<table>";
    $cols = 4; $n = 0;
    $link = get_bloginfo("url")."/wp-content/compo2/$fname";
    echo "<tr><td colspan=$cols align=center><a href='$link' target='_blank'><img src='".compo2_thumb($fname,450,450)."'></a>";
    foreach ($shots as $fname) {
        if (($n%$cols)==0) { echo "<tr>"; } $n += 1;
        $link = get_bloginfo("url")."/wp-content/compo2/$fname";
        echo "<td><a href='$link' target='_blank'><img src='".compo2_thumb($fname,120,120)."'></a>";
    }
    echo "</table>";
    
    if ($params["jcat"]) {
        $link = get_bloginfo("url")."/?category_name={$params["jcat"]}&author_name={$user["user_nicename"]}";
        echo "<p><a href='$link' target='_blank'>View {$user["display_name"]}'s journal.</a></p>";
    }
    
    if ($params["state"] == "results" || $params["state"] == "admin") {
        _compo2_results_ratings($params,$uid);
    }
    
    if ($comments) {
        _compo2_preview_comments($params,$uid,true);
    }
}

?>
