<?php
require_once '../../../wp-config.php';

global $wpdb;
$post_ID = $_POST['id'];
$ip = $_SERVER['REMOTE_ADDR'];
$like = get_post_meta($post_ID, '_liked', true);

if($post_ID != '') {
	$voteStatusByIp = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."ilikethis_votes WHERE post_id = '$post_ID' AND ip = '$ip'");
	
    if (!isset($_COOKIE['liked-'.$post_ID]) && $voteStatusByIp == 0) {
		$likeNew = $like + 1;
		update_post_meta($post_ID, '_liked', $likeNew);

		setcookie('liked-'.$post_ID, time(), time()+3600*24*365, '/');
		$wpdb->query("INSERT INTO ".$wpdb->prefix."ilikethis_votes VALUES ('', NOW(), '$post_ID', '$ip')");

		echo $likeNew;
	}
	else {
		echo $like;
	}
}
?>