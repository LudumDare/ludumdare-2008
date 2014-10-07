<?php do_action("compo2_cache_begin"); ?>
<?php
ob_start(); // start the ob_cache so that things work magictastically
require_once dirname(__FILE__)."/fncs.php"; // load up our custom function goodies

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('|',true,'right'); bloginfo('name'); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@ludumdare" />
<meta name="twitter:title" content="Ludum Dare" />
<meta name="twitter:description" content="The worlds largest online game jam event. Join us every April, August, and December." />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?1" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css' />

<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar or not
//if ( !$withcomments && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/povimg/LDBack.png") repeat-y top; border: none; }
<?php/* } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/povimg/LDBackWide.png") repeat-y top; border: none; }
<?php }*/ ?>

</style>

<?php wp_head(); ?>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2932135-5']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</head>
<body>


<div id="page">


<div id="header">
	<div id="headerimg">
		<a href="<?php echo get_option('home'); ?>/"><img src="/compo/wp-content/themes/ludum/images/blank.gif" width="900" height="104" border="0" /></a></br />
	</div>
</div>
<div id="compo-navigation">
	<center><a href="/compo/about/"><strong>About</strong></a> | <a href="/compo/rules/"><strong>Rules and Guide</strong></a> | <a href="/compo/wp-login.php"><strong>Sign In/Create Account</strong></a> | <a href="/compo/wp-admin/post-new.php"><strong>Write a Post</strong></a></center>
</div>
<div id="compo-status"><?php
global $wpdb;
$e = array_pop(compo_query("select * from {$wpdb->posts} where post_name = ? and post_type =?",array("status","page")));

echo apply_filters('the_content',str_replace("\n","<br>",$e["post_content"]));

?></div>

<hr />

