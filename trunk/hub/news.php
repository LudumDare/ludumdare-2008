<?php

require_once 'simplepie/simplepie.inc';

function show_news($newsfeed,$newsitems,$newsprefiximage,$newssuffiximage) {
	$rss = new SimplePie($newsfeed, $_SERVER['DOCUMENT_ROOT'] . '/hub/cache');
	foreach ($rss->get_items(0, $newsitems) as $item) {
        $title = $item->get_title();
        $url   = $item->get_permalink();
        if ($newsprefiximage != '')
            echo "<img src='$newsprefiximage'>";
        echo "<a href=$url>$title</a>";
        if ($newssuffiximage != '')
            echo "<img src='$newssuffiximage'>";
        echo "<br />";
    }
}

?>