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
    
    $version = 22;
    if ($cur < $version) {
        compo2_query("alter table c2_rate add ts datetime");
        compo2_query("alter table c2_entry add ts datetime");
        update_option($key,$version);
    }
    
    $version = 23;
    if ($cur < $version) {
        compo2_query("create table c2_comments (id int not null auto_increment primary key, cid int, to_uid int, from_uid int, content text, ts datetime)");
        compo2_query("create index idx_c2_comments_cid on c2_comments (cid)");
        compo2_query("create index idx_c2_comments_to_uid on c2_comments (to_uid)");
        update_option($key,$version);
    }
    
    $version = 24;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add rules_ok int default 1");
        update_option($key,$version);
    }
    
    $version = 25;
    if ($cur < $version) {
        compo2_query("alter table c2_entry change rules_ok is_judged int");
        compo2_query("alter table c2_entry add etype varchar(32)");
        compo2_query("update c2_entry set etype = 'compo' where is_judged = 1");
        compo2_query("update c2_entry set etype = 'gamejam' where is_judged = 0");
        update_option($key,$version);
    }
    
    $version = 28;
    if ($cur < $version) {
        compo2_query("alter table c2_entry add get_user blob");
        update_option($key,$version);
    }
    $version = 30;
    if ($cur < $version) {
        $r = compo2_query("select id,uid from c2_entry");
        foreach ($r as $ce) {
            $user = compo2_get_user($ce["uid"]);
            compo2_query("update c2_entry set get_user = ? where id = ?",array(
                serialize(array(
                    "display_name"=>$user->display_name,
                    "user_nicename"=>$user->user_nicename,
                    "user_email"=>$user->user_email,
                )),
                $ce["id"],
            ));
        }
        update_option($key,$version);
    }
    $version = 31;
    if ($cur < $version) {
        compo2_query("alter table c2_comments add get_user blob");
        update_option($key,$version);
    }
    $version = 33;
    if ($cur < $version) {
        $r = compo2_query("select id,from_uid from c2_comments where get_user is null");
        foreach ($r as $ce) {
            $user = compo2_get_user($ce["from_uid"]);
            compo2_query("update c2_comments set get_user = ? where id = ?",array(
                serialize(array(
                    "display_name"=>$user->display_name,
                    "user_nicename"=>$user->user_nicename,
                    "user_email"=>$user->user_email,
                )),
                $ce["id"],
            ));
        }
        update_option($key,$version);
    }
    
    $version = 34;
    if ($cur < $version) {
        compo2_query("create table c2_cache (id varchar(32) primary key, cid int, name varchar(64), data longblob, ts datetime)");
        compo2_query("create index idx_c2_cache_cid on c2_cache (cid)");
        compo2_query("create index idx_c2_cache_name on c2_cache (name)");
        update_option($key,$version);
    }
    
    $version = 35;
    if ($cur < $version) {
        compo2_query("create index idx_c2_cache_ts on c2_cache (ts)");
        update_option($key,$version);
    }
    
    // Added int 'love', default 0 //
    // Added index 'cid_love' //
}

register_activation_hook($GLOBALS["compo2"]["plugin"],"compo2_install");
?>