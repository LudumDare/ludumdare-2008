<?php

function _mythumb_gallery($m) {
    global $wpdb, $mythumb;
    ob_start();
    $pid = intval($GLOBALS["post"]->ID);
    $r = mythumb_query("select * from $wpdb->posts where post_parent = ? and post_mime_type like 'image/%' order by menu_order",array($pid));
    
    if (!count($r)) { return ''; }
    $cols = $mythumb["gallery.cols"];$n = 0;
    $size = $mythumb["gallery.size"];
    echo "<table align=center>";
    foreach ($r as $e) {
        if (!($name=mythumb_build($e["guid"],$size))) { continue; }
        echo (($n++%$cols)==0?"<tr>":"");
        echo "<td align=center>";
        $link = $e["guid"];
        $fname = "{$mythumb["datadir"]}/$name";
        list($w,$h) = getimagesize($fname);
        $thumb = htmlentities("{$mythumb["dataurl"]}/$name");
        $title = htmlentities("{$e["post_title"]}");
        echo "<a href=\"$link\" title=\"$title\"><img src=\"$thumb\" height=\"$h\" width=\"$w\" alt=\"$title\" title=\"$title\"></a>";
    }
    echo "</table>";
    $r = ob_get_contents();
    ob_end_clean();
    return $r;
}

function _mythumb_story($m) {
    global $wpdb, $mythumb;
    ob_start();
    $pid = intval($GLOBALS["post"]->ID);
    $r = mythumb_query($sql="select * from $wpdb->posts where post_parent = ? and post_mime_type like 'image/%' order by menu_order",array($pid));
    
    if (!count($r)) { return; }
    $size = $mythumb["story.size"];
    foreach ($r as $e) {
        if (!($name=mythumb_build($e["guid"],$size))) { continue; }
        $title = htmlentities("{$e["post_title"]}");
        echo "<h3>$title</h3>";
        $link = $e["guid"];
        $fname = "{$mythumb["datadir"]}/$name";
        list($w,$h) = getimagesize($fname);
        $thumb = htmlentities("{$mythumb["dataurl"]}/$name");
        echo "<a href=\"$link\"><img src=\"$thumb\" height=\"$h\" width=\"$w\" alt=\"$title\" title=\"$title\"></a>";
        echo "<p>".str_replace("\n","<br/>",htmlentities($e["post_content"]))."</p>";
    }
    $r = ob_get_contents();
    ob_end_clean();
    return $r;

}

function mythumb_tags($content) {
    $patt = preg_quote("[mythumbs-gallery]","/[]");
    $content = preg_replace_callback("/$patt/","_mythumb_gallery",$content);
    $patt = preg_quote("[mythumbs-story]","/[]");
    $content = preg_replace_callback("/$patt/","_mythumb_story",$content);
    return $content;
}

?>