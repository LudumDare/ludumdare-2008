<?php

function compo_cloud() {
    global $wpdb;
    $topurl = get_bloginfo("url");
    $start = 10; $total = 24;

    $query = "SELECT $wpdb->terms.term_id, $wpdb->terms.name, $wpdb->term_taxonomy.count, $wpdb->terms.slug FROM (($wpdb->term_relationships INNER JOIN $wpdb->posts ON $wpdb->term_relationships.object_id = $wpdb->posts.ID) INNER JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) INNER JOIN $wpdb->terms ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id WHERE ((($wpdb->term_taxonomy.taxonomy)='post_tag') AND (($wpdb->posts.post_status)='publish')) GROUP BY $wpdb->terms.name ORDER BY count DESC, $wpdb->terms.name"; # LIMIT $start,$total";
    $terms = $wpdb->get_results($query);
    
    shuffle($terms);
    $data = array();
    foreach ($terms as $e) {
        if ($e->count < 2) { continue; }
        $data[] = "{$e->count}|{$e->name}|{$e->slug}";
        if (count($data) >= $total) { break; }
    }
    natcasesort($data);
    
    $out = array();
    $n = 0;
    foreach ($data as $v) {
        $z = intval(8+$n*0.5);
        list($x,$name,$slug) = explode("|",$v);
//         $name = htmlentities($name);
        $out[] = "<a href='$topurl/tag/$slug' style='font-size:{$z}px'>$name</a>";
        $n += 1;
    }
    shuffle($out);
    echo implode(" ",$out);
}

?>