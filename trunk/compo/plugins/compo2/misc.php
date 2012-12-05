<?php

function _compo2_misc($params) {
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        // uh, this shouldn't happen ..
    } elseif ($action == "misc_links") {
        return _compo2_misc_links($params);
    }
}

function _compo2_misc_links($params) {
    $r = compo2_query("select * from c2_entry where cid = ? and active = 1",array($params["cid"]));
    usort($r,"_compo2_preview_sort");
    
    echo "<p><a href='?action=default'>Back ...</a></p>";
    
    echo "<table><tr><th>Entry<th>User<th>Links ...<th># Votes<th>Coolness<th>Entry Type\n";
    foreach ($r as $ce) {
        $ue = unserialize($ce["get_user"]);
        echo "<tr>";
        echo "<td><a href='?action=preview&uid={$ce["uid"]}'>".htmlentities($ce["title"])."</a>";
        echo "<td>".htmlentities($ue["display_name"]);
        echo "<td>";
        _compo2_preview_show_links($ce);
        echo "<td>".htmlentities($ce["rate_in"]);
        echo "<td>".htmlentities($ce["rate_out"]);
        echo "<td>".htmlentities($ce["rules_ok"]);
        echo "\n";
    }
    echo "</table>";
        
}

function compo2_theme_author($uid) {
    
    $r = compo2_query("select * from c2_entry where uid = ? and active = 1 and disabled = 0 order by cid desc",array($uid));
    
    if (!count($r)) { return; }
    
    echo '<h2 class="pagetitle">Entries</h2>';
    echo "<div class='post' id='compo2'>";
    
        $cols = 4;
        $n = 0;
        $row = 0;
        echo "<table class='preview'>";
        foreach ($r as $e) {
            $pe = array_pop(compo2_query("select * from wp_posts where ID = ?",array($e["cid"])));
            $_link = "../../{$pe["post_name"]}/?action=preview";
            
            if (($n%$cols)==0) { echo "<tr>"; $row += 1; } $n += 1;
            $klass = "class='alt-".(1+(($row)%2))."'";
            echo "<td valign=bottom align=center $klass>";
            $link = "$_link&uid={$e["uid"]}";
            echo "<div><a href='$link'>";
            $shots = unserialize($e["shots"]);
            echo "<img src='".compo2_thumb($shots["shot0"],120,90)."'>";
            echo "<div class='title'><i>".htmlentities($e["title"])."</i></div>";
            echo "</a></div>";
            echo "<div class='title'>".htmlentities($pe["post_title"])."</div>";
        }
        echo "</table>";

    
    echo "</div>";
    
}

?>