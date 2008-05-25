<?php
function mythumb_query($sql,$params=array()) {
    global $wpdb;
    foreach ($params as $v) {
        $sql = preg_replace("/\?/","'".$wpdb->escape($v)."'",$sql,1);
    }
    $r = $wpdb->get_results($sql,ARRAY_A);
    return $r;
}


function mythumb_build($src,$size,$ext="jpg") {
    global $mythumb;
    $cmd = "convert -quality 85 -resize $size ".escapeshellarg($src);
    $name = md5($cmd).".$ext";
    $fname = "{$mythumb["datadir"]}/$name";
    $cmd .= " ".escapeshellarg($fname);
    if (!file_exists($fname)) { `$cmd`; }
    return (file_exists($fname)?$name:false);
}

?>