<?php

function my_get_buttons() {
        // check if we're on a "final" post page ..
        $my_final = 0;
        $tags = get_the_tags(); if (!$tags) { return; }
        foreach ($tags as $e) { if ($e->slug == "final") { $my_final = 1; } }
        $my_auth = get_the_author_meta('user_nicename');
        $uid = get_the_author_meta('user_ID');
        $cat = array_pop(get_the_category());
        $my_cat = $cat->slug;
//         print_r($cat);
        $cid = $cat->cat_ID;
//         echo "$my_auth : $my_cat : $is_final";
        $my_link = get_option('home')."/?category_name=".urlencode($my_cat)."&author_name=".urlencode($my_auth);
        $state = get_option("compo-$cid-state");
        
        if (!$my_final) { return; }
        
        ob_start();
        if ($state == "rate") {
            if (!is_user_logged_in()) { return; }
            $cuid = wp_get_current_user()->ID;
            if ($cuid == $uid) { return; }
            if (compo_can_rate($cid,$cuid)) {
                echo "<p style='text-align:left;clear:both;'><form method=post action='$my_link'><input type='submit' value='Vote on this Entry'></form></p>";
            }
        }
        if ($state == "results") {
            echo "<p style='text-align:left;clear:both;'><form method=post action='$my_link'><input type='submit' value='View voting results for this entry'></form></p>";
        }
        $my_buttons = ob_get_contents();
        ob_end_clean();
        return $my_buttons;
}