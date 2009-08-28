<?php
function compo2_install() {
    global $compo2;
    $key = $compo2["version.key"];
    $cur = get_option($key);
    
    
    $version = 1;
    if ($cur < $version) {
        compo2_query("create table c2_rate (cid int, to_uid int, from_uid int, data blob)");
        update_option($key,$version);
    }
    $version = 2;
    if ($cur < $version) {
        compo2_query("alter table c2_rate add comments text");
        update_option($key,$version);
    }
    
    $version = 7;
    if ($cur < $version) {
        compo2_query("create table c2_entry (id int not null auto_increment primary key, cid int, uid int, notes text, links blob, data blob)");
        update_option($key,$version);
    }
    $version = 9;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add results blob");
        update_option($key,$version);
    }
    $version = 11;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add active int default 0");
        update_option($key,$version);
    }
    
    $version = 12;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add title varchar(255)");
        update_option($key,$version);
    }
    
    $version = 13;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add shots blob");
        update_option($key,$version);
    }
    $version = 19;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add disabled int default 0");
        update_option($key,$version);
    }
    $version = 20;
    if ($cur < $version) {
        compo2_query("create index idx_c2_entry_uid on c2_entry (uid)");
        compo2_query("create index idx_c2_entry_cid on c2_entry (cid)");
        compo2_query("create index idx_c2_rate_cid on c2_rate (cid)");
        compo2_query("create index idx_c2_rate_to_uid on c2_rate (to_uid)");
        compo2_query("create index idx_c2_rate_from_uid on c2_rate (from_uid)");
        update_option($key,$version);
    }
    $version = 21;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add rate_in int default 0");
        compo2_query("alter table c2_entry add rate_out int default 0");
        update_option($key,$version);
    }
}

register_activation_hook($GLOBALS["compo2"]["plugin"],"compo2_install");
?>