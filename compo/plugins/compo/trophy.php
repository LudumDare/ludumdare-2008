<?php

function compo_trophy($uid) {
    global $compo;
    
    $table = $compo["trophy.table"];
    $action = (isset($_REQUEST["compo_action"])?$_REQUEST["compo_action"]:"default");
    $topurl = get_bloginfo("url");
    $auth = get_userdata($uid);
    $cur = wp_get_current_user();
    
    // insert the new trophies
    if ($action == "add")
    if (is_user_logged_in() && isset($_FILES["image"])) {
        if (!strlen($_REQUEST["title"])) {
            echo "<p>Description too short!</P>";
            return;
        }
        if ($_FILES["image"]["error"]) {
            echo "<p>Error with upload!</p>";
            return;
        }
        list($w,$h) = getimagesize($_FILES["image"]["tmp_name"]);
        if ($w!=64 || $h!=64) {
            echo "<p>Image must be <b>64x64</b>!</p>";
            return;
        }
        $fname = time().".png";
        $dest = dirname(__FILE__)."/../../compo/$fname";
        $cmd = "convert -resize 64x64 ".escapeshellarg($_FILES["image"]["tmp_name"])."  ".escapeshellarg($dest);
        `$cmd`;
        if (!file_exists($dest)) {
            echo "<p>Error with image!</p>";
            return;
        }
        compo_query("insert into $table (to_uid,from_uid,time,title,img) values (?,?,?,?,?)",array($uid,$cur->ID,time(),$_REQUEST["title"],$fname)); 
        header("Location: .");
    }
    
    if ($action == "delete") {
        compo_query("delete from $table where to_uid = ? and from_uid = ? and time = ?",array($uid,$cur->ID,intval($_REQUEST["time"])));
        header("Location: .");
    }
    
    // display existing trophies
    if ($action == "default") {
        $limit = intval(isset($_REQUEST["trophy_limit"])?$_REQUEST["trophy_limit"]:6);
        $r = compo_query("select * from $table where to_uid = ? order by time desc limit $limit",array($uid));
        if (count($r)) {
            $n = 0;
            echo "<table cellspacing=16>";
            foreach ($r as $e) {
                echo ($n++%3==0?"<tr>":"");
                echo "<td align=center valign=top>";
                compo_trophy_show($e);
                if ($e["from_uid"] == $cur->ID) {
                    echo "<div>";
                    echo "<form method=post action='?compo_action=delete'>";
                    echo "<input type='hidden' name='time' value='{$e["time"]}'>";
                    echo "<input type='submit' value='Remove'>";
                    echo "</form>";
                    echo "</div>";
                }
            }
            echo "</table>";
            if (count($r) == $limit) {
                echo "<form method='post'><input type='hidden' name='trophy_limit' value='12345'><input type='submit' value='See all Trophies!'></form>";
            }
        } else { /* echo "<p>No trophies yet.  Why don't you ..</p>"; */ }
//         echo "<p><a href='$topurl/author/{$auth->user_nicename}/?compo_action=form'>Award a trophy!</a></p>";
        echo "<form method='post'><input type='hidden' name='compo_action' value='form'><input type='submit' value='Award a Trophy!'></form>";
    }
    
    if ($action == "form")
    if (is_user_logged_in()) {
        // let people submit new trophies!
        echo "<h3>Award a trophy!</h3>";
        echo "<form method=post action='?compo_action=add' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='compo_action' value='add'>";
        echo "<table><tr><th>Description";
        echo "<td><input type='text' name='title' size=40><br><i>(e.g. The Chestival Memorial Award)</i>";
        echo "<tr><th>Image";
        echo "<td><input type='file' name='image'> (image must be <b>64x64</b>)";
        echo "<tr><td><td><input type='submit' value='Continue'>";
        echo "</table>";
        echo "</form>";
    }
}

function compo_trophy_sidebar() {
    global $compo;
    $table = $compo["trophy.table"];
    $topurl = get_bloginfo("url");
    $r = compo_query("select * from $table order by time desc limit 5");
    echo "<table cellspacing=16>";
    foreach ($r as $e) {
        echo "<tr><td align=center>";
        $auth = get_userdata($e["to_uid"]);
        echo "<div><a href='$topurl/author/{$auth->user_nicename}/'>{$auth->display_name}</a></div>";
        compo_trophy_show($e);
    }
    echo "</table>";
}

function compo_trophy_show($e) {
    echo "<div><img height=64 src='".get_bloginfo("url")."/wp-content/compo/{$e["img"]}'></div>";
    echo "<div><b>".htmlentities($e["title"])."</b></div>";
    $from = get_userdata($e["from_uid"]);
    $link = get_bloginfo("url")."/author/{$from->user_nicename}/";
    echo "<div>Awarded by <a href='$link'>{$from->display_name}</a> on ".date("F j, Y",$e["time"])."</div>";
}

?>