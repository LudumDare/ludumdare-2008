<?php

function _compo2_misc($params) {
    $action = isset($_REQUEST["action"])?$_REQUEST["action"]:"default";
    
    if ($action == "default") {
        // uh, this shouldn't happen ..
    } elseif ($action == "misc_links") {
        return _compo2_misc_links($params);
    }
}

function _compo2_misc_links($params) {
    $r = compo2_query("select * from c2_entry where cid = ? and active = 1",array($params["cid"]));
    usort($r,"_compo2_preview_sort");
    
    echo "<p><a href='?action=default'>Back ...</a></p>";
    
    echo "<table><tr><th>Entry<th>User<th>Links ...<th># Votes<th>Coolness<th>Entry Type\n";
    foreach ($r as $ce) {
        $ue = compo2_get_user($ce["uid"]);
        echo "<tr>";
        echo "<td><a href='?uid={$ce["uid"]}'>".htmlentities($ce["title"])."</a>";
        echo "<td>".htmlentities($ue->display_name);
        echo "<td>";
        _compo2_preview_show_links($ce);
        echo "<td>".htmlentities($ce["rate_in"]);
        echo "<td>".htmlentities($ce["rate_out"]);
        echo "<td>".htmlentities($ce["rules_ok"]);
        echo "\n";
    }
    echo "</table>";
        
}


?>