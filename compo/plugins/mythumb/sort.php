<?php

function mythumb_sort() {
    global $mythumb, $wpdb;
    
    $pid = $GLOBALS["post_id"];
    
    echo '<div>&nbsp;</div>';

    if (isset($_REQUEST["order"])) {
        $n = 1;
        foreach (explode("&",$_REQUEST["order"]) as $part) {
            list($k,$id) = explode("=",$part);
            mythumb_query($sql="update $wpdb->posts set menu_order = ? where ID = ? and post_parent = ?",array($n,$id,$pid));
//             echo "$sql<br>";
            $n += 1;
        }
        echo "<p>Order Saved!</p>";
    }

    mythumb_query("update $wpdb->posts set menu_order = 1000 where menu_order = 0 and post_parent = ?",array($pid));
    $r = mythumb_query("select * from $wpdb->posts where post_parent = ? order by menu_order",array($pid));
    wp_enqueue_script('scriptaculous-dragdrop');
    wp_print_scripts();
    
    echo "<form method=post>";
    echo "<ul id='mylist'>";
    foreach ($r as $e) {
        $name = mythumb_build($e["guid"],"32x32");
        $thumb = "{$mythumb["dataurl"]}/$name";
        $title = htmlentities(strlen($e["post_title"])?$e["post_title"]:basename($e["guid"]));
        echo "<li id='mylist_{$e["ID"]}'><img src='$thumb' align=absmiddle> $title"; 
    }
    echo "</ul>";
    echo "<input type='hidden' size='60' name='order' id='order'>";
    echo '<script type="text/javascript">
    function cb (e) { document.getElementById("order").value=Sortable.serialize("mylist",{name:"x"}); }
    Sortable.create("mylist",{onChange:cb});
    cb(0);
    </script>';
    echo "<input type='submit' value='Save'>";
    echo "</form>";
    
        
}
?>