<?php
/*
Plugin Name: Compo2 System
Plugin URI: http://www.imitationpickles.org/
Description: New compo judging system.
Version: 1.0
Author: Phil Hassey
Author URI: http://www.philhassey.com/
*/

global $compo2;
$compo2 = array(
    "version.key"=>"compo2_version",
    "plugin"=>__FILE__,
);

function compo2_error($msg) {
    trigger_error($msg,E_USER_ERROR);
}



function compo2_query($sql,$params=array()) {
    global $wpdb;
    
    $parts = explode("?",$sql);
    $sql = array_shift($parts);
    foreach ($parts as $v) {
        $sql .= "'".$wpdb->escape(array_shift($params))."'";
        $sql .= $v;
    }
    
//     echo "<p>compo2 - Debug: ".htmlentities($sql)."</p>";

    $r = $wpdb->get_results($sql,ARRAY_A);
    if ($r===false) {
        compo2_error("compo2 - Error in query: $sql");
    }
    if (!$r) { return array(); }
    return $r;
}

function compo2_entry_load($cid,$uid) {
    return array_pop(compo2_query("select * from c2_entry where cid = ? and uid = ?",array($cid,$uid)));
}

function compo2_insert($table,$e,$key="id") {
    $keys = array_keys($e);
    $values = array_values($e);
    $keys_ = "(".implode(",",$keys).")";
    $values_ = array(); foreach ($values as $k) { $values_[] = "?"; }
    $values_ = "(".implode(",",$values_).")";
    compo2_query("insert into $table $keys_ values $values_",$values);
    if (isset($e[$key])) {
        $r = $e[$key];
    } else { // HACK: ...
        $rr = compo2_query('SELECT LAST_INSERT_ID() as lid');
        $r = $rr[0]["lid"];
    }
    return $r;
}

function compo2_update($table,$e,$key="id") {
    $r = $id = $e[$key];
    $sets = array();
    foreach ($e as $k=>$v) { $sets[] = $k." = ?"; }
    $sets_ = implode(",",$sets);
    $values = array_values($e);
    $values[] = $id;
    return compo2_query("update $table set $sets_ where $key = ?",$values);
}

function compo2_select($k,$r,$v) {
    echo "<select name='$k'>";
    foreach ($r as $kk=>$vv) {
        echo "<option value='$kk' ".(strcmp($kk,$v)==0?"selected":"").">$vv";
    }
    echo "</select>";
}

function compo2_thumb($_fname,$width,$height,$itype="jpg",$quality=85) {
    $topdir = dirname(__FILE__)."/../../compo2";
    $fname = "$topdir/$_fname";
    list($w,$h) = getimagesize($fname);
    if ($w < $width && $h < $height) {
        // don't scale it up ..
        return get_bloginfo("url")."/wp-content/compo2/$_fname";
    }

    $dst = md5("thumb $fname $width $height $quality ".filesize($fname)).".$itype";
    @mkdir("$topdir/thumb");
    $dest = "$topdir/thumb/$dst";
    if (!file_exists($dest)) {
        $cmd = "/usr/bin/convert -quality $quality ".escapeshellarg($fname)." -flatten -resize {$width}x{$height} +profile \"*\" ".escapeshellarg($dest);
        `$cmd`;
    }
    return get_bloginfo("url")."/wp-content/compo2/thumb/$dst";
}

function compo2_get_user($uid) {
    return get_userdata($uid);
}

function compo2_number_format($v) {
    if (!strlen($v)) { return "-"; }
    return number_format($v,2);
}

    
    
require_once dirname(__FILE__)."/install.php";
require_once dirname(__FILE__)."/main.php";

require_once dirname(__FILE__)."/active.php";
require_once dirname(__FILE__)."/rate.php";
require_once dirname(__FILE__)."/results.php";
require_once dirname(__FILE__)."/preview.php";
require_once dirname(__FILE__)."/admin.php";
require_once dirname(__FILE__)."/misc.php";
require_once dirname(__FILE__)."/closed.php";

add_filter('the_content','compo2_the_content');
function compo2_the_content($v) {
    $v = compo2_main($v);
    return $v;
}
?>