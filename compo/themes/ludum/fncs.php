<?php

function my_get_buttons() {
        // check if we're on a "final" post page ..
        $my_final = 0;
        foreach (get_the_tags() as $e) { if ($e->slug == "final") { $my_final = 1; } }
        $my_auth = get_the_author_meta('login');
        $my_cat = array_pop(get_the_category())->slug;
//         echo "$my_auth : $my_cat : $is_final";
        $my_link = get_option('home')."/?category_name=".urlencode($my_cat)."&author_name=".urlencode($my_auth);
        ob_start();
        if ($my_final) {
            echo "<p><form method=post action='$my_link'><input type='submit' value='Vote on this Entry'></form></p>";
        }
        $my_buttons = ob_get_contents();
        ob_end_clean();
        return $my_buttons;
}