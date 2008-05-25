<?php
/*
Plugin Name: MyThumb
Plugin URI: http://www.imitationpickles.org/
Description: Capture thumbnails of all images within posts.  Thumbnails
    act as navigation at top of categories, tags, etc.
Version: 1.0
Author: Phil Hassey
Author URI: http://www.philhassey.com/
*/
/*
Inspired by Post-Thumb by Alakhnor.  
Some snippets of code were grabbed from that plugin.
Plugin URI: http://www.alakhnor.com/post-thumb
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

require_once dirname(__FILE__)."/install.php";

global $mythumb;
$mythumb = array_merge(mythumb_config(array(
    "lightbox.enable"=>1,
    "sort.enable"=>1,
    "tags.enable"=>1,
    
    "wordpress.thumb_size"=>350,
    "nav.size"=>"64x64",
    "nav.cols"=>7,
    
    "gallery.cols"=>4,
    "gallery.size"=>"96x96",
    "story.size"=>"350x350",
    )),array(
    "plugin"=>__FILE__,
    "version.key"=>"mythumb_version",
    "datadir"=>dirname(__FILE__)."/../../mythumb",
    "dataurl"=>get_bloginfo("url")."/wp-content/mythumb",
    "myurl"=>get_bloginfo("url")."/wp-content/plugins/mythumb",
));

require_once dirname(__FILE__)."/fncs.php";
require_once dirname(__FILE__)."/sort.php";
require_once dirname(__FILE__)."/tags.php";
require_once dirname(__FILE__)."/nav.php";
require_once dirname(__FILE__)."/lightbox.php";
require_once dirname(__FILE__)."/options.php";


add_filter('the_content','mythumb_the_content');
function mythumb_the_content($v) {
    global $mythumb;
    if ($mythumb["tags.enable"]) { $v = mythumb_tags($v); }
    if ($mythumb["lightbox.enable"]) { $v = mythumb_lightbox($v); }
    return $v;
}
function mythumb_size($v) { return $GLOBALS["mythumb"]["wordpress.thumb_size"]; }
add_filter('wp_thumbnail_max_side_length','mythumb_size');
add_filter('wp_upload_tabs', 'mythumb_upload_tabs');
function mythumb_upload_tabs($tabs) {
    global $mythumb;
    if ($mythumb["sort.enable"]) { 
        $pid = $GLOBALS["post_id"];
        if ($pid) {
            $tabs['sort'] = array(__('Sort Photos'), 'upload_files', "mythumb_sort", 0);
        }
    }
    return $tabs;
}
function mythumb_add_pages() {
    add_options_page('mythumb', 'mythumb', 8,"mythumb_options","mythumb_options");
}
add_action('admin_menu', 'mythumb_add_pages');
?>