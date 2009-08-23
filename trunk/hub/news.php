<?php

require_once 'magpierss2/rss.php';

$rss = fetch_rss($newsfeed);

$NewsItemCount = 0;

foreach ($rss->items as $item) {
	$title = $item[title];
	$url   = $item[link];
	if ($newsprefiximage != '')
		echo "<img src='$newsprefiximage'>";
	echo "<a href=$url>$title</a>";
	if ($newssuffiximage != '')
		echo "<img src='$newssuffiximage'>";
	echo "<br />";

	$NewsItemCount++;
	if ($NewsItemCount == $newsitems)
		break;
}

?>