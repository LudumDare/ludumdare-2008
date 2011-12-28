<?php

// find out if a user is logged into wordpress
function _compo2_fcache_logged_in() {
    foreach ($_COOKIE as $k=>$v) {
        if (strstr($k,"wordpress_logged_in_")===false) { continue; }
        return true;
    }
    return false;
}

function _compo2_fcache_fname($key) {
    return dirname(__FILE__)."/../../compo2/fcache/".md5($key);
}

function compo2_fcache_read($key,$ts=-1) {
    $fname = _compo2_fcache_fname($key);
    if (!file_exists($fname)) { return false; }
    if ($ts != -1 && (time()-filemtime($fname)) > $ts) { return false; }
    return file_get_contents($fname);
}

function compo2_fcache_write($key,$value) {
    $fname = _compo2_fcache_fname($key);
    @mkdir(dirname($fname));
    file_put_contents($fname,$value);
}

function compo2_fcache_begin() {
    if (_compo2_fcache_logged_in()) { return; }
    if (count($_POST)) { return; }
    
    $ckey = $_SERVER["REQUEST_URI"];
    
    if (($cres=compo2_fcache_read($ckey,5*60))!==false) { echo $cres; echo "<p>[fcache: using cached page]</p>"; die; }
    ob_start();
}

function compo2_fcache_gc() {
    $ts = 60*60;
    
    $dname = dirname(_compo2_fcache_fname("x"));
    
    if ($d = opendir($dname)) {
        while (($name = readdir($d)) !== false) {
            $fname = "$dname/$name";
            if (!is_file($fname)) { continue; }
            if ((time()-filemtime($fname)) <= $ts) { continue; }
            unlink($fname);
        }
    }
}

function compo2_fcache_end() {
    if (_compo2_fcache_logged_in()) {
        echo "<p>[fcache: unable to cache, user logged in]</p>";
        return;
    }
    if (count($_POST)) {
        echo "<p>[fcache: unable to cache, POST data submitted]</p>";
        return;
    }
    
    $ckey = $_SERVER["REQUEST_URI"];
    $cres = ob_get_contents();
    compo2_fcache_write($ckey,$cres);
    ob_end_clean();
    echo $cres;
    echo "<p>[fcache: storing page]</p>";
    
    // 1 in 1000 hits, do garbage collection in cache
    if ((rand()%1000)==0) {
        compo2_fcache_gc();
    }
}

compo2_fcache_begin();
?>