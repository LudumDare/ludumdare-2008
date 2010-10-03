<?php

require_once 'simplepie/simplepie.inc';

function show_news($newsfeed,$newsitems,$newsprefiximage,$newssuffiximage) {
	$rss = new SimplePie($newsfeed, dirname(__FILE__) . '/cache');
	$rss->set_cache_duration ( 60*5 );
	$rss->init();
	$rss->handle_content_type();
	
	if ($rss->error()) {
	   echo htmlentities($rss->error());
	   return;
    }
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