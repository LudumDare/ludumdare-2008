<?php

/*function mythumb_add_pages() {
    add_options_page('MyThumb', 'MyThumb', 8,"mythumb_options","mythumb_options");
}
add_action('admin_menu', 'mythumb_add_pages');*/

/*add_action('publish_post',"mythumb_update");
add_action('edit_post',"mythumb_update");
add_action('save_post',"mythumb_update");
add_action('wp_insert_post',"mythumb_update");
add_action('wp_insert_post',"mythumb_update");
add_action('private_to_published',"mythumb_update");
add_action('delete_post',"mythumb_update");*/


// function mythumb_select($k,$r,$v) {
//     echo "<select name='$k'>";
//     foreach ($r as $kk=>$vv) {
//         echo "<option value='$kk' ".($kk==$v?"selected":"").">$vv";
//     }
//     echo "</select>";
// }

// function mythumb_update($pid) {
//     global $mythumb;
// 
//     if ( !current_user_can('edit_post', $pid) ) { return $pid; }
// 
//     // Retrieve post content as list
//     $post = &get_post($pid);
// 
//     // skip drafts
//     if ( !($post->post_status == 'publish' || $post->post_status == 'static' OR $post->post_status == 'future')) {
//         return $pid;
//     }
// 
//     mythumb_query("delete from {$mythumb["table"]} where post_id = ?",array($pid));
//     
//     $ne = "([^>]*?)";
//     $qs = "['\"]";
//     $nq = "([^'\"]*)";
//     
//     // Saves post data
//     if (!preg_match_all("/<img{$ne}src={$qs}{$nq}.(bmp|jpg|jpeg|gif|png){$qs}{$ne}>/i",
//         $post->post_content, $ms,PREG_SET_ORDER)) { return; }
//         
//     foreach ($ms as $m) {
//         $src = "{$m[2]}.{$m[3]}";
//         $patt = "/<a{$ne}href={$qs}{$nq}.(bmp|jpg|jpeg|gif|png){$qs}{$ne}>(\s*?)<img{$ne}src={$qs}".preg_quote($src,"/")."{$qs}{$ne}>/i";
//         if (preg_match($patt,$post->post_content,$m)) {
//             $src = "{$m[2]}.{$m[3]}";
//         }
//         $name = mythumb_build($pid,$src);
//         if ($name) {
//             mythumb_query("insert into {$mythumb["table"]} (post_id,src) values (?,?)",array($pid,$src));
//         }
//         
//     }
// }
?>
