<?php 
do_action("compo2_cache_begin");
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

	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?2.96" type="text/css" media="screen" />
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
	(function() {
		// Zero Pad Digits //
		function PadZero( num ) {
			if ( num < 10 ) {
				return "0" + num;
			}
			return num;
		}
		
		// Subtract B - A //
		function DateDiff( a, b ) {
			return b.getTime() - a.getTime();
		}

		var timerHandle = null;

		var clockElm = null;

		var serverTime = <?php echo $_SERVER['REQUEST_TIME']; ?>;
		var serverClock = new Date(serverTime*1000);
		var localClock = new Date();

//		// If Clock is Bad //
//		var clockDiff = DateDiff(localClock,serverClock);
//		if ( Math.abs(clockDiff) > 45*60*1000 ) {	// If 45 minutes off (cache safe) then Error //
//			window.addEventListener("load", function(e) {
//				clockElm = document.getElementsByClassName('clock');
//				for (var idx = 0; idx < clockElm.length; idx++ ) {
//					clockElm[idx].innerHTML = '<a href="/compo/faq/">Clock Error</a>';
//				}
//			});
//		}
//		// If Clock is Okay //
//		else 
		{
			// Store it in Window (global scope) //
			window.mkClocksUpdate = function(e) {
				clockElm = document.getElementsByClassName('clock');
				
				// If no clocks, bail //
				if ( clockElm.length === 0 ) {
					return;
				}
				
				window._mkClocksFunc = function(){
					var nowClock = new Date();
					
					for (var idx = 0; idx < clockElm.length; idx++ ) {
						var diff = DateDiff(nowClock,new Date(clockElm[idx].getAttribute('title')));
						
						if ( diff >= 0 ) {
							var oneSecond = 1000;
							var oneMinute = 60*1000;
							var oneHour = 60*60*1000;
							var oneDay = 24*60*60*1000;
		
							var diffMS = Math.floor(diff % oneSecond);
							var diffSeconds = Math.floor(diff / oneSecond) % 60;
							var diffMinutes = Math.floor(diff / oneMinute) % 60;
							var diffHours = Math.floor(diff / oneHour) % 24;
							var diffDays = Math.floor(diff / oneDay);
							
							var sep = ":";
							if ( diffMS >= 500 ) {
								sep = ";";
							}
							
							var dayText = diffDays + " Days" + ((diffDays <= 7*5) ? ", " : "");
							if ( diffDays == 1 ) {
								dayText = "1 Day, ";
							}
							else if ( diffDays == 0 ) {
								dayText = "";
							}
							
							var prefixText = clockElm[idx].getAttribute('prefix');
							if ( prefixText === null ) {
								prefixText = "";
							}
							else {
								var prefixStyle = clockElm[idx].getAttribute('prefix-style');
								if ( prefixStyle ) {
									prefixText = '<span style="' + prefixStyle + '">' + prefixText + "</span>";
								}
							}
							
							clockElm[idx].innerHTML = 
								prefixText +
								dayText +
								((diffDays <= 7*5) ? 
									PadZero(diffHours) + sep +
									PadZero(diffMinutes) + sep +
									PadZero(diffSeconds)
								: "" );
						}
						else {
							/* Flash on Completion */
							//if ( Math.abs(diff % 1000) >= 500 ) {
								clockElm[idx].innerHTML = clockElm[idx].getAttribute('msg');
//							}
//							else {
//								clockElm[idx].innerHTML = "";
//							}
						}
					}
				};

				if ( timerHandle === null ) {
					timerHandle = setInterval( window._mkClocksFunc,500);
				}
			};

			window.mkClocksFocus = function(e) {
				if ( timerHandle === null ) {
					timerHandle = setInterval( window._mkClocksFunc,500);
				}
			};
			window.mkClocksBlur = function(e) {
				if ( timerHandle ) {
					clearInterval( timerHandle );
					timerHandle = null;
				}
			};
			
			window.addEventListener("load", window.mkClocksUpdate);
			/* In case people want us to be less wasteful */
			//window.addEventListener("focus", window.mkClocksFocus);
			//window.addEventListener("blur", window.mkClocksBlur);
		}
	})();
	</script>
</head>
<body>
	<div id="page">
		<div id="header">
			<div class="body">
<?php		$current_user = wp_get_current_user(); ?>
<?php		if ( 0 == $current_user->ID ) { ?>
				<!-- Not Logged In -->
				<div class="login-not">
					<div class="headline"><a href="/compo/wp-login.php"><strong>Login</strong></a> | <a href="/compo/wp-login.php?action=register">Create Account</a></div>
				</div>
<?php		} else { ?>
				<!-- Logged In -->
				<div class="login-nav">
					<div class="login">
						<div class="login-user">
							<div class="avatar" title="Edit your Avatar on Gravatar (Click!)"><a href="http://www.gravatar.com/" target="_blank"><?php echo get_avatar( $current_user->user_email, 52 ); ?></a></div>
							<div class="info">
								<div class="headline">Welcome <a href="/compo/wp-admin/profile.php" title="Edit your Profile"><strong><?php echo $current_user->display_name; ?></strong></a></div>
								<div class="action"><a href="/compo/wp-admin/post-new.php" title="Make a new Blog Post">+<strong>New POST</strong></a></div>
							</div>
						</div>
					</div>
					<div class="login-search">
						<!--<form action="/compo/" method="get" class="searchbar"><input class="searchbar-text" name="s" id="searchbar-text" type="text" value="" maxlength="100"><input type="submit" class="searchbar-button" value="" title="Search"></form>-->
						<a href="/compo/wp-admin/index.php" title="Dashboard"><img src="<?php echo get_template_directory_uri(); ?>/ld2014/dashboard.png" /></a>
					</div>
				</div>
<?php		} ?>
				<a href="<?php echo get_option('home'); ?>/" title="Home"><img src="/compo/wp-content/themes/ludum/povimg/LDLogo2015.png" width="386" height="64" /></a>
			</div>
		</div>
		
		<div id="status">
<?php /* BEGIN */
			$out = FALSE;
			
			if ( function_exists('apcu_fetch') ) {
				if ( !isset($_GET["cache"]) ) {
					$out = apcu_fetch('mk_Header_cache');
				}
			}
		
			if ( $out === FALSE ) {
				global $wpdb;
				$e = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE post_name='status' AND post_type='page';",ARRAY_A);
				//$e = array_pop(compo_query("select * from {$wpdb->posts} where post_name = ? and post_type =?",array("status","page")));
				$out = $e["post_content"];
			
				if ( function_exists('apcu_store') ) {
					apcu_store('mk_Header_cache', $out, 120);	// Store for 2 minutes //
				}
			}
		
			$out = apply_filters('the_content',$out);
			echo $out;
/* END */ ?>
		</div>
		<!-- Force Clock Update -->
		<script type="text/javascript">mkClocksUpdate();</script>
