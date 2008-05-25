<?php
/*
Plugin Name: Compo System
Plugin URI: http://www.imitationpickles.org/
Description: Give trophies to members, rate their entries, vote on themes.
Version: 1.0
Author: Phil Hassey
Author URI: http://www.philhassey.com/
*/
/*
Copyright 2007 Phil Hassey (philhassey@yahoo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $compo;
global $wpdb;
$compo = array(
    "version.key"=>"compo_version",
    "trophy.table"=>"{$wpdb->prefix}compo_trophy",
    "plugin"=>__FILE__,
    "rate.table"=>"{$wpdb->prefix}compo_rate",
    "results.table"=>"{$wpdb->prefix}compo_results",
    "vote.table"=>"{$wpdb->prefix}compo_vote",
);

function compo_query($sql,$params=array()) {
    global $wpdb;
    
    $parts = explode("?",$sql);
    $sql = array_shift($parts);
    foreach ($parts as $v) {
        $sql .= "'".$wpdb->escape(array_shift($params))."'";
        $sql .= $v;
    }

/*    foreach ($params as $v) {
        $sql = preg_replace("/\?/","'".$wpdb->escape($v)."'",$sql,1);
    }*/
    
    $r = $wpdb->get_results($sql,ARRAY_A);
    if (!$r) { return array(); }
    return $r;
}
function compo_select($k,$r,$v) {
    echo "<select name='$k'>";
    foreach ($r as $kk=>$vv) {
        echo "<option value='$kk' ".(strcmp($kk,$v)==0?"selected":"").">$vv";
    }
    echo "</select>";
}

require_once dirname(__FILE__)."/install.php";
require_once dirname(__FILE__)."/results.php";
require_once dirname(__FILE__)."/trophy.php"; // trophy tool
require_once dirname(__FILE__)."/options.php"; // options
require_once dirname(__FILE__)."/rate.php";
require_once dirname(__FILE__)."/cloud.php";
require_once dirname(__FILE__)."/vote.php";

function compo_add_pages() {
    add_options_page('Compo', 'Compo', 8,"compo_options","compo_options");
}
add_action('admin_menu', 'compo_add_pages');
function compo_upload_mimes($r) {
    foreach ($r as $k=>$v) {
        if (strpos($v,"image/")===0) { continue; }
        unset($r[$k]);
    }
//     print_r($r);
    return $r;
}
add_filter('upload_mimes', 'compo_upload_mimes');

add_filter('the_content','compo_the_content');
function compo_the_content($v) {
    $v = compo_vote($v);
    return $v;
}

function compo_the_editor($v) {
    $v.="<p style='font-size:20px;font-style:italic;'>If this is your final entry (or an old entry), be sure to mark it with the <b>\"final\"</b> tag.</p>";
    return $v;
}
add_filter("the_editor","compo_the_editor");

/*add_filter('posts_where','compo_posts_where');
function compo_posts_where($v) {
    echo $v;
    return $v;
}
add_filter('posts_join','compo_posts_join');
function compo_posts_join($v) {
    echo $v;
    return $v;
}*/

/*add_filter("user_has_cap","compo_user_has_cap");
function compo_user_has_cap($v) {
    print_r($v);
    return $v;
}*/

add_filter("get_comment_author_IP","compo_filter_hidden");
add_filter("get_comment_author_email","compo_filter_hidden");
add_filter("author_email","compo_filter_hidden");
add_filter("comment_email","compo_filter_hidden");
add_filter("the_author_email","compo_filter_hidden");
function compo_filter_hidden($v) { return "(sekrit)"; }
?>