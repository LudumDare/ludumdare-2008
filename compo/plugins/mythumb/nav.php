<?php

function mythumb_nav() {
    global $mythumb;
    global $wpdb;
    $sql = str_replace("SQL_CALC_FOUND_ROWS","",$GLOBALS["wp_query"]->request);
    $sql = preg_replace("/limit.*$/i","",$sql);
//     echo $sql;
    $r = mythumb_query("select t2.guid src,t2.post_title title,t2.post_parent post_id,t1.guid,t1.post_title from $wpdb->posts t2, ($sql) t1 where t2.post_parent = t1.ID and t2.post_type = 'attachment' and t2.post_mime_type like 'image/%'");
    
    if (!count($r)) { return; }
    shuffle($r);
    global $mythumb;
    echo "<table>";
    $cols = $mythumb["nav.cols"];$n = 0;
    $size = $mythumb["nav.size"];
    foreach ($r as $e) {
        echo (($n++%$cols)==0?"<tr>":"");
        echo "<td align=center width={$mythumb["width"]} height={$mythumb["height"]}>";
//         $link = htmlentities($e["src"]);
        $link = $e["guid"];
        if (!($name=mythumb_build($e["src"],$size,"png"))) { continue; }
        $fname = "{$mythumb["datadir"]}/$name";
        list($w,$h) = getimagesize($fname);
        $thumb = htmlentities("{$mythumb["dataurl"]}/$name");
        
        $title = htmlentities("{$e["post_title"]} - {$e["title"]}");
        echo "<a href=\"$link\"><img src=\"$thumb\" height=$h width=$w alt=\"$title\" title=\"$title\"></a>";
    }
    echo "</table>";
}
?>