<?php
function mythumb_install() {
    global $wpdb, $mythumb;
    $key = $mythumb["version.key"];
    $cur = get_option($key);
    
/*    $version = 1;
    if ($cur < $version) {
        $sql = "CREATE TABLE {$mythumb["table"]} (".
            "id int not null auto_increment primary key, ".
            "post_id int, ".
            "title varchar(255), ".
            "src varchar(255) ".
            ")";
        $wpdb->query($sql);

        $sql = "CREATE INDEX {$mythumb["table"]}_post_id_index ON {$mythumb["table"]} (post_id)";
        $wpdb->query($sql);
        update_option($key,$version);
    }*/
    
}
register_activation_hook($GLOBALS["mythumb"]["plugin"],"mythumb_install");

function mythumb_config($r) {
    foreach ($r as $k=>$v) {
        $key = "mythumb-$k";
        $value = get_option($key);
        if (strlen($value)) { $v = $value; }
        update_option($key,$v);
        $r[$k] = $v;
    }
    return $r;
}
?>