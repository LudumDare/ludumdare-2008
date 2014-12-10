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
    "entry_load_cache"=>array(),
    "log"=>array(),
    "log.enabled"=>true,
);

function compo2_error($msg) {
    trigger_error($msg,E_USER_ERROR);
}



function compo2_log($fnc,$tm,$msg="") {
    global $compo2;
    if (!$compo2["log.enabled"]) { $msg = "..."; }
    if (strlen($msg)>1024) { $msg = "..."; }
    $key = "$fnc|$msg";
    $e = array("fnc"=>$fnc,"tm"=>$tm,"msg"=>$msg,"hits"=>1);
    if (isset($compo2["log"][$key])) {
        $ee = $compo2["log"][$key];
        $e["tm"] += $ee["tm"];
        $e["hits"] += $ee["hits"];
    }
    $compo2["log"][$key] = $e;
}

function compo2_query($sql,$params=array()) {
    $tm = microtime(true);

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
    
    compo2_log("compo2_query",microtime(true)-$tm,$sql);
    
    if (!$r) { return array(); }
    return $r;
}

function compo2_entry_load($cid,$uid) {
    global $compo2;
    $key = "$cid-$uid";
    if (!isset($compo2["entry_load_cache"][$key])) {
        $compo2["entry_load_cache"][$key] = array_pop(compo2_query("select * from c2_entry where cid = ? and uid = ?",array($cid,$uid)));
    }
    return $compo2["entry_load_cache"][$key];
}

function compo2_cache_read($cid,$name,$ts=-1) {
    if (isset($_REQUEST["cache"])) {
        $user = wp_get_current_user();
        if ($user->user_level >= 7) { return false; }
    }
/*/
	// MK: APC Cache //
	if ( function_exists('apcu_fetch') ) {
		return apcu_fetch('c2_'.$cid.$name);
	}
/*/	
    if ($ts==-1) {
        $r = compo2_query("select * from c2_cache where id = ?",array("$cid|$name"));
    } else {
        $r = compo2_query("select * from c2_cache where id = ? and ts > ?",array("$cid|$name",date("Y-m-d H:i:s",time()-$ts)));
    }
    if (!count($r)) { return false; }
    $e = array_pop($r);
    return $e["data"];
/**/
}

function compo2_cache_write($cid,$name,$data) {
/*/
	// MK: APC Cache //
	if ( function_exists('apcu_store') ) {
		apcu_store('c2_'.$cid.$name, $data, 180);	// Store for 3 minutes //
	}
/**/

    compo2_query("replace into c2_cache (id,cid,name,data,ts) values (?,?,?,?,?)",array("$cid|$name",$cid,$name,$data,date("Y-m-d H:i:s")));
/**/
}

// custom limited cache
// cache only caches for anonymous users
// cache only works on non-POST responses
// cache only caches for 5*60 seconds
function compo2_cache_begin() {
    if (function_exists("compo2_fcache_begin")) { return; }
    
    $user = wp_get_current_user(); $uid = $user->ID; if ($uid) { return; }
    if (count($_POST)) { return; }
    
    $ckey = substr(md5($_SERVER["REQUEST_URI"]),0,30); // truncated because of 32 char limit of ckey
    if (($cres=compo2_cache_read("0",$ckey,5*60))!==false) { echo $cres; echo "<p>[cache: using cached page]</p>"; die; }
    ob_start();
}
function compo2_cache_end() {
    if (function_exists("compo2_fcache_end")) { return compo2_fcache_end(); }
    
    $user = wp_get_current_user(); $uid = $user->ID; if ($uid) {
        echo "<p>[cache: unable to cache, user logged in]</p>";
        return;
    }
    if (count($_POST)) {
        echo "<p>[cache: unable to cache, POST data submitted]</p>";
        return;
    }
    
    $ckey = substr(md5($_SERVER["REQUEST_URI"]),0,30); // truncated because of 32 char limit of ckey
    $cres = ob_get_contents();
    compo2_cache_write("0",$ckey,$cres);
    ob_end_clean();
    echo $cres;
    echo "<p>[cache: storing page]</p>";
    
    // 1 in 1000 hits, auto clear out all 1-hour old cache data in the "0" cache
    if ((rand()%1000)==0) {
        $ts = 60*60; 
        compo2_query("delete from c2_cache where cid = ? and ts < ?",array("0",date("Y-m-d H:i:s",time()-$ts)));
    }
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
    $tm = microtime(true);

    $topdir = dirname(__FILE__)."/../../compo2";
    $fname = "$topdir/$_fname";
    
    $dst = md5("thumb $fname $width $height $quality ".filesize($fname)).".$itype";
    $dest = "$topdir/thumb/$dst";
    
    if (!file_exists($dest)) {
        list($w,$h) = getimagesize($fname);
        if ($w < $width && $h < $height) {
            // scale to exact same size
            $width = $w; $height = $h;
            
            // just don't scale at all
//             return get_bloginfo("url")."/wp-content/compo2/$_fname";
        }

        @mkdir("$topdir/thumb");
        $cmd = "/usr/bin/convert -quality $quality ".escapeshellarg($fname)." -flatten -resize {$width}x{$height} +profile \"*\" ".escapeshellarg($dest);
        `$cmd`;
    }
    
    compo2_log("compo2_thumb",microtime(true)-$tm);

    return get_bloginfo("url")."/wp-content/compo2/thumb/$dst";
}

// Mike's improved version of thumbnail generation. Uses entirely PHP functions (GD), no ImageMagick //
function c2_thumb( $_filename, $width, $height, $itype="jpg", $quality=90) {
	$sysdir = dirname(__FILE__)."/../../compo2";
	$baseurl = get_bloginfo("url")."/wp-content/compo2/";
	
	$thumbnail_filename = $sysdir .'/'. $_filename .'-'.$width.'-'.$height.'.'.$itype;

	echo $thumbnail_filename;
	
	if ( !file_exists($thumbnail_filename) {
		echo "<br />Must Generate";
	}
}


function compo2_get_user($uid) {
    $tm = microtime(true);
    $r = get_userdata($uid);
    compo2_log("compo2_get_user",microtime(true)-$tm);
    return $r;
}

function compo2_calc_coolness($votes,$total) {
    $votes = max(0,min(100,$votes));
    $total = max(0,min(100,$total-1));
    $v = sqrt($votes * 100 / max(1,$total)) * 100 / 10;
    return intval(round($v));
}

/*
function compo2_get_user($uid) {
    $tm = microtime(true);
// display_name
// nicename
// user_email

    $topdir = dirname(__FILE__)."/../../compo2";
    $fname = "$topdir/get_user/$uid";
//     print_r($fname); die;
    if (!($f=fopen($fname,"rb"))) {
        $r = get_userdata($uid);
        $f = fopen($fname,"wb");
        fwrite($f,serialize($r));
        fclose($f);
    } else {
        $r = unserialize(fread($f,99999));
        fclose($f);
    }
    
    compo2_log("compo2_get_user",microtime(true)-$tm);
    return $r;
//     return get_userdata($uid);
}
*/


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

require_once dirname(__FILE__)."/mike.php";

//add_filter('the_content','compo2_the_content');
//add_action('wp_head', 'compo2_wp_head');
add_action('compo2_cache_begin', 'compo2_cache_begin');
add_action('compo2_cache_end', 'compo2_cache_end');
/*function compo2_the_content($v) {
    $v = compo2_main($v);
    return $v;
}*/
/*function compo2_wp_head() {
    $url = get_bloginfo("url")."/wp-content/plugins/compo2/style.css";
    echo '<link rel="stylesheet" type="text/css" media="all" href="'.$url.'" />';
}*/

// Add Local Style Sheet style.css //
add_action( 'wp_enqueue_scripts', 'compo2_add_my_stylesheet' );
function compo2_add_my_stylesheet() {
    wp_register_style( 'compo2-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'compo2-style' );
}

add_shortcode( 'compo2', 'compo2_main' );


?>