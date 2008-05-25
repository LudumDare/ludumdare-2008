<?php

function compo_options() {
    $action = (isset($_REQUEST["action"])?$_REQUEST["action"]:"");
    
    
    if ($action == 'submit') {
        foreach ($_REQUEST as $k=>$v) {
            if (strpos($k,"compo-")!==0) { continue; }
            $prev = get_option($k);
            $v = str_replace(" ","",$v);
            update_option( $k,$v,"","no");
        }
        echo '<div id="message" class="updated fade"><p>Options Updated</p></div>';
    }

    echo "<div class='wrap'>";
    echo "<h2>Compo Options</h2>";
    $a=str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
    echo "<form method='post' action='$a'>";
    echo "<input type='hidden' name='action' value='submit'>";
    echo "<table>";
    echo "<tr><th><th>State<th>Categories (e.g. innovation,fun,production,overall)";
    $cats = get_categories();
    foreach ($cats as $e) {
        echo "<tr>";
        echo "<th>{$e->name}";
        
        echo "<td>";
        $k = "compo-{$e->term_id}-state";
        $v = get_option($k);
        compo_select($k,array(""=>"","active"=>"Active","rate"=>"Rate","results"=>"Results"),$v);
        
        echo "<td>";
        $k = "compo-{$e->term_id}-cats";
        $v = get_option($k);
        echo "<input type='text' name='$k' value='".htmlentities($v)."' size=80>";
    }
    echo "</table>";
    echo "<input type='submit' value='Update Options'>";
    echo "</form>";
    echo "</div>";
}


?>