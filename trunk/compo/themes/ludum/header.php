<?php
ob_start(); // start the ob_cache so that things work magictastically
require_once dirname(__FILE__)."/fncs.php"; // load up our custom function goodies

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php
if (isset($_REQUEST["auto_refresh"])) {
echo "<meta http-equiv='refresh' content='300'/>";
}
?>

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?1" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
        $myurl = get_bloginfo("url")."/wp-content/plugins/compo2";
        echo "<script type='text/javascript' src='$myurl/starry/prototype.lite.js'></script>";
        echo "<script type='text/javascript' src='$myurl/starry/stars.js'></script>";
        echo "<link rel='stylesheet' href='$myurl/starry/stars.css' type='text/css' />";
?>

<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar or not
if ( !$withcomments && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/povimg/LDBack.png") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/povimg/LDBackWide.png") repeat-y top; border: none; }
<?php } ?>

</style>

<?php wp_head(); ?>
</head>
<body>


<div id="page">


<div id="header">
	<div id="headerimg">
		<h1 style="text-align:left; font-size:70px"><a href="<?php echo get_option('home'); ?>/"><?php /* bloginfo('name'); */ echo str_repeat("&nbsp;",30); ?></a></h1>
		<!--<div class="description"><?php bloginfo('description'); ?></div>-->
	</div>
</div>
<hr />

<div id='compo-status'><?php
global $wpdb;
$e = array_pop(compo_query("select * from {$wpdb->posts} where post_name = ? and post_type =?",array("status","page")));

echo apply_filters('the_content',str_replace("\n","<br>",$e["post_content"]));

?></div>

<hr />

