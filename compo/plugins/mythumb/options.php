<?php

function mythumb_options() {
    global $mythumb;
    $action = (isset($_REQUEST["action"])?$_REQUEST["action"]:"");
    
    if ($action == 'submit') {
        foreach ($_REQUEST as $k=>$v) {
            if (strpos($k,"mythumb-")!==0) { continue; }
            $v = str_replace(" ","",$v);
            $k = str_replace("_",".",$k);
            update_option($k,$v);
//             echo "$k=$v<br>";
        }
        echo '<div id="message" class="updated fade"><p>Options Updated</p></div>';
    }

    echo "<div class='wrap'>";
    echo "<h2>mythumb Options</h2>";
//     $a=str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
//     echo "<form method='post' action='$a'>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='submit'>";
    echo "<table>";
    echo "<tr><th>Option<th>Value";
    foreach ($mythumb as $k=>$v) {
        $key = "mythumb-$k";
        $v = get_option($key);
        if (!strlen($v)) { continue; }
        echo "<tr>";
        echo "<th>$k";
        
        echo "<td>";
        echo "<input type='text' name='$key' value='".htmlentities($v)."' size=80>";
    }
    echo "</table>";
    echo "<input type='submit' value='Update Options'>";
    echo "</form>";
    echo "</div>";
}

?>