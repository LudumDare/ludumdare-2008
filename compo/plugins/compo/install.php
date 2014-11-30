<?php
function compo_install() {
    global $wpdb, $compo;
    $key = $compo["version.key"];
    $cur = get_option($key);
    
    $version = 1;
    if ($cur < $version) {
        $sql = "CREATE TABLE {$compo["trophy.table"]} (".
            "to_uid int, ".
            "from_uid int, ".
            "time bigint, ".
            "title varchar(255), ".
            "img varchar(255) ".
            ")";
        $wpdb->query($sql);
        update_option($key,$version);
    }
    
    $version = 2;
    if ($cur < $version) {
        $sql = "create table {$compo["rate.table"]} (cid int, to_uid int, from_uid int, data blob)";
        $wpdb->query($sql);
        update_option($key,$version);
    }
    
    $version = 3;
    if ($cur < $version) { 
        $sql = "create table {$compo["vote.table"]} (pid int, uid int, name varchar(255), value int default 0)";
        $wpdb->query($sql);
        update_option($key,$version);
    }
    
    $version = 4;
    if ($cur < $version) { 
        $sql = "alter table {$compo["rate.table"]} add comment text";
        $wpdb->query($sql);
        update_option($key,$version);
    }
    
    /*
    $version = 3;
    if ($cur < $version) {
        $sql = "create table {$compo["ibox.table"]} (id int not null auto_increment primary key, uid int, title varchar(255), img varchar(255), time bigint)";
        $wpdb->query($sql);
        
        $sql = "create table {$compo["ibox_tags.table"]} (ibox_id int, value varchar(32))";
        update_option($key,$version);
    }
    */

    $version = 5;
    if ($cur < $version) { 
        $sql = "create index idx_{$compo["vote.table"]}_pid on {$compo["vote.table"]} (pid)";
        $wpdb->query($sql);
        $sql = "create index idx_{$compo["vote.table"]}_uid on {$compo["vote.table"]} (uid)";
        $wpdb->query($sql);

        $sql = "create index idx_{$compo["trophy.table"]}_to_uid on {$compo["trophy.table"]} (to_uid)";
        $wpdb->query($sql);

        $sql = "create index idx_{$compo["rate.table"]}_cid on {$compo["rate.table"]} (cid)";
        $wpdb->query($sql);
        $sql = "create index idx_{$compo["rate.table"]}_to_uid on {$compo["rate.table"]} (to_uid)";
        $wpdb->query($sql);

        update_option($key,$version);
    }

    $version = 6;
    if ($cur < $version) { 
        $sql = "create index idx_{$compo["trophy.table"]}_time on {$compo["trophy.table"]} (time)";
        $wpdb->query($sql);
        update_option($key,$version);
    }
/*
	$version = 7;
	if ($cur < $version) {
		// Remove NULL //
		$wpdb->query("
			ALTER TABLE {$compo['rate.table']}
			ALTER COLUMN NOT NULL DEFAULT 0
		");
		
		update_option($key,$version);
	}*/
	
	// PID and UID
	// - Set defaults to 0
	// - set NOT NULL
	// - changed to BIGINT
	
	// NAME
	// - set default to "" (nothing)
	// - set NOT NULL
	// - collation to utf8_unicode_ci
	// - set to 100 wide (not 255)
	
	// VALUE
	// - set NOT NULL
	
	
	// Renamed PID index to "pid_and_uid"
	// - set both PID and UID as the index
}
register_activation_hook($GLOBALS["compo"]["plugin"],"compo_install");
?>