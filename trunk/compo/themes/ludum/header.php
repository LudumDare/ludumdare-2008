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

	<?php wp_head(); ?>
	
	<!-- Google Analytics -->
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
	
	<!-- Countdown Clocks -->
	<script type="text/javascript">
		var cdClock = document.querySelectorAll('div.clock');
		console.log("HOY: " + cdClock.length );
//		var cdClock_time = cdClock
	</script>
</head>
<body>
	<div id="page">
		<div id="header">
			<div class="body">
					<?php
						$current_user = wp_get_current_user();
						if ( 0 == $current_user->ID ) {
							// Not logged in.
							echo '<div class="login_not">';
								echo '<div class="headline"><a href="/compo/wp-login.php"><strong>Login</strong></a> | <a href="/compo/wp-login.php?action=register">Create Account</a></div>';
							echo '</div>';
						} else {
							// Logged in //
							echo '<div class="login">';
								echo '<div class="avatar" title="Edit your Avatar on Gravatar (Click!)"><a href="http://www.gravatar.com/" target="_blank">' . get_avatar( $current_user->user_email, 52 ) . "</a></div>";
								echo '<div class="info">';
									echo "<div class=\"headline\">Welcome <a href=\"/compo/wp-admin/profile.php\" title=\"Edit your Profile\"><strong>{$current_user->display_name}</strong></a>!</div>";
									echo '<div class="action"><a href="/compo/wp-admin/post-new.php" title="Make a new Blog Post">+<strong>New POST</strong></a></div>';
								echo '</div>';
							echo '</div>';
						}
					?>
				<a href="<?php echo get_option('home'); ?>/" title="Home"><img src="/compo/wp-content/themes/ludum/povimg/LDLogo2015.png" width="386" height="64" /></a>
			</div>
		</div>
		
		<div id="status"><?php
			$out = FALSE;
			
			if ( function_exists('apcu_fetch') ) {
				$out = apcu_fetch('mk_Header_cache');
			}
		
			if ( $out === FALSE ) {
				global $wpdb;
				$e = array_pop(compo_query("select * from {$wpdb->posts} where post_name = ? and post_type =?",array("status","page")));
				
		//		$out = apply_filters('the_content',str_replace("\n","<br>",$e["post_content"]));
				$out = str_replace("\n","<br>",$e["post_content"]);
			
				if ( function_exists('apcu_store') ) {
					apcu_store('mk_Header_cache', $out, 120);	// Store for 2 minutes //
				}
			}
		
			$out = apply_filters('the_content',$out);
			echo $out;
		?></div>
		
		<hr />
