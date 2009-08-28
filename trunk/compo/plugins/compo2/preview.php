<?php


function _compo2_preview_sort($a,$b) {
    return strcmp($a["title"],$b["title"]);
}

function _compo2_preview($params,$is_admin=0) {
    if (isset($_REQUEST["uid"])) { return _compo2_preview_show($params,intval($_REQUEST["uid"]),1); }

    echo "<h3>All Entries</h3>";

    $r = compo2_query("select * from c2_entry where cid = ? ".(!$is_admin?" and active=1":""),array($params["cid"]));
    usort($r,"_compo2_preview_sort");
    $cols = 4;
    $n = 0;
    echo "<table>";
    foreach ($r as $e) {
        if (($n%$cols)==0) { echo "<tr>"; } $n += 1;
        
        echo "<td valign=bottom align=center>";
        if (!$is_admin) {
            $link = "?action=preview&uid={$e["uid"]}";
        } else {
            $link = "?admin=1&action=edit&uid={$e["uid"]}";
        }
        if (!$e["active"]) { echo "<i>inactive</i>"; }
        echo "<a href='$link'>";
        $shots = unserialize($e["shots"]);
        echo "<img src='".compo2_thumb($shots["shot0"],120,120)."'>";
        echo "<br/>";
        echo "<i>".htmlentities($e["title"])."</i>";
        echo "<br/>";
        echo compo2_get_user($e["uid"])->display_name;
        echo "</a>";
    }
    echo "</table>";

    if (!$is_admin) {
        $ce = compo2_entry_load($params["cid"],$params["uid"]);
        if ($ce["id"]) { echo "<p><a href='?action=edit'>Edit your entry.</a></p>"; }
    }

}

function compo2_strip($v) {
    return stripslashes($v);
}

function _compo2_preview_show($params,$uid,$back=0,$comments=1) {
    $ce = compo2_entry_load($params["cid"],$uid);
    $user = compo2_get_user($ce["uid"]);
    
    if ($back) { echo "<p><a href='?action=preview'>Back to View all Entries</a></p>"; }
    
    echo "<h3>".htmlentities($ce["title"])." - {$user->display_name}</h3>";
    
    echo "<p>";
    $pre = "";
    foreach (unserialize($ce["links"]) as $le) {
        if (!strlen($le["title"])) { continue; }
        $link = $le["link"];
        if (strpos($link,"javascript:") === 0) { continue; }
        if (strpos($link,"?") === 0) { continue; }
        if (!preg_match("/^\w+\:\/\//",$link)) { continue; }
        echo "$pre<a href=\"".htmlentities($link)."\" target='_blank'>".htmlentities($le["title"])."</a>";
        $pre = " | ";
    }
    echo "</p>";
    
    echo "<p>".str_replace("\n","<br/>",htmlentities($ce["notes"]))."</p>";
    
    $shots = unserialize($ce["shots"]);
    $fname = array_shift($shots);
        
    
    echo "<table>";
    $cols = 4; $n = 0;
    $link = get_bloginfo("url")."/wp-content/compo2/$fname";
    echo "<tr><td colspan=$cols align=center><a href='$link' target='_blank'><img src='".compo2_thumb($fname,450,450)."'></a>";
    foreach ($shots as $fname) {
        if (($n%$cols)==0) { echo "<tr>"; } $n += 1;
        $link = get_bloginfo("url")."/wp-content/compo2/$fname";
        echo "<td><a href='$link' target='_blank'><img src='".compo2_thumb($fname,120,120)."'></a>";
    }
    echo "</table>";
    
    if ($params["jcat"]) {
        $link = get_bloginfo("url")."/?category_name={$params["jcat"]}&author_name={$user->user_nicename}";
        echo "<p><a href='$link' target='_blank'>View {$user->display_name}'s journal.</a></p>";
    }
    
    if ($comments) {
        $r = compo2_query("select * from c2_rate where cid = ? and to_uid = ?",array($params["cid"],$uid));
        _compo_show_comments($r);
    }
}

?>