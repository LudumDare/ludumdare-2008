<?php
/*
Plugin Name: Twidget
Plugin URI: http://www.ludumdare.com/compo/
Description: Twitch.tv widget for Wordpress
Version: 1.0
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: BSD
*/

$plugin_dir = '/compo/wp-content/plugins/twidget/';

$TwidgetHasRun = false;

class Twidget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'twidget', // Base ID //
			'Twidget', // Name //
			array( 'description' => __( 'Twitch.tv widget', 'text_domain' ), ) // ARGS //
		);
	}
	
	function widget($args, $instance) {
		extract($args);
		
		global $plugin_dir;
		
		$apikey = $instance['apikey'];
		$game = $instance['game'];
		$faqurl = $instance['faqurl'];

		echo $before_widget;
		
//		error_reporting(-1);
		
		echo '<div id="TTV">';
			echo '<div class="Widget">';
				echo '<div id="TTV_Video" class="Head"></div>';
				echo '<div id="TTV_Streams" class="Body">Loading...</div>';
				echo '<div class="FarEdge"></div>';
			echo '</div>';
			echo '<div class="Foot">';
				echo '<div class="FootBody">';
					echo '<span class="FootImg">';
						echo '<object data="' . $plugin_dir . 'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml"></object>';
					echo '</span>';
					echo '&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="http://www.twitch.tv/directory/game/' . rawurlencode($game) . '" target="_blank"> View All Streams</a>';
					echo '</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="' . $faqurl . '">FAQ</a>';
					echo '</span>';
					echo '<span id="TTV_Standby_Container" class="FootImg2">';
//						echo '<object id="TTV_Standby" data="' . $plugin_dir . 'ImgStandby.svg" width="22" height="22" type="image/svg+xml"></object>';
					echo '</span>';
				echo '</div>';
				echo '<div class="FootEdge"></div>';
			echo '</div>';
//			echo '<br />';
		echo '</div>';
		
		echo '<script>';
		echo 'var TwitchTV_APIKey = "' . $apikey . '";';
		echo 'var TwitchTV_Game = "' . $game . '";';
		echo 'var TwitchTV_FAQ = "' . $faqurl . '";';
		echo 'var TwitchTV_BaseDir = "' . $plugin_dir . '";';
		echo '</script>';

		global $TwidgetHasRun;
		$TwidgetHasRun = true;
		
		echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['apikey'] = strip_tags( $new_instance['apikey'] );
		$instance['game'] = strip_tags( $new_instance['game'] );
		$instance['faqurl'] = strip_tags( $new_instance['faqurl'] );
		return $instance;
	}

	function form($instance) {
		if ( isset( $instance[ 'apikey' ] ) ) {
			$apikey = $instance[ 'apikey' ];
		}
		else {
			$apikey = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'game' ] ) ) {
			$game = $instance[ 'game' ];
		}
		else {
			$game = __( 'Diablo III', 'text_domain' );
		}
		
		if ( isset( $instance[ 'faqurl' ] ) ) {
			$faqurl = $instance[ 'faqurl' ];
		}
		else {
			$faqurl = __( 'streaming-faq/', 'text_domain' );
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'apikey' ); ?>"><?php _e( 'Twitch API Key:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'apikey' ); ?>" name="<?php echo $this->get_field_name( 'apikey' ); ?>" type="text" value="<?php echo esc_attr( $apikey ); ?>" />

		<label for="<?php echo $this->get_field_id( 'game' ); ?>"><?php _e( 'Game Name:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'game' ); ?>" name="<?php echo $this->get_field_name( 'game' ); ?>" type="text" value="<?php echo esc_attr( $game ); ?>" />

		<label for="<?php echo $this->get_field_id( 'faqurl' ); ?>"><?php _e( 'FAQ URL:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'faqurl' ); ?>" name="<?php echo $this->get_field_name( 'faqurl' ); ?>" type="text" value="<?php echo esc_attr( $faqurl ); ?>" />
		</p>
		<?php 
	}
}

function AddTTVScripts() {
	global $TwidgetHasRun, $plugin_dir;
	if ( $TwidgetHasRun == true ) {
//	//	echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.min.css" />';
		echo '<script src="https://ttv-api.s3.amazonaws.com/twitch.min.js"></script>';
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'twidget.js"></script>';
		echo '<script src="' .$plugin_dir. 'twidget.min.js"></script>';
		echo '<script>';
		echo '	setTimeout( function(){';
		echo '			InitTwitchTV();';
		echo '		}, 200 );';
		echo '</script>';	
	}
}

// * * * //

function broadcast_query_func( $query ) {
	$out = "";

	global $wpdb;
	$result = $wpdb->get_results($query, ARRAY_A);
	
	$services = Array(
		0=>'null',
		1=>'twitch',
		2=>'hitbox',
		3=>'youtube',
		4=>'beam',
		5=>'',
		6=>'twitch-gamedev'
	);
	
	$modes = Array(
		0=>"",
		1=>"DEV",
		2=>"PLAY"
	);
	
	$dev_patterns = Array(
		"dev","developing","deving","code","coding","create","creating",
		"make","making","art","draw","compose","composing","program",
		"unity","java","c++","c#","python","html","october challenge"
	);
	$play_patterns = Array(
		"play","playing"
	);
		
	$out .= "<div class='broadcast_table'>";
		$out .= "<div class='header row'>";
			$out .= "<div class='service_header' title='Service'>SV</div>";
			$out .= "<div class='avatar_header' title='Avatar'>A</div>";
			$out .= "<div class='name_header'>Name</div>";
			$out .= "<div class='online_header'>Online</div>";
			$out .= "<div class='viewers_header'>Viewers</div>";
			$out .= "<div class='mode_header'>Mode</div>";
			$out .= "<div class='status_header'>Status</div>";
			$out .= "<div class='units_header' title='Total Minutes (in Hours:Minutes)'>Total</div>";
		$out .= "</div>";

		foreach( $result as $row ) {
			// Figure out when we were last online //
			$online_time = intval($row['online']);
			if ( $row['live'] ) {
				$online = "NOW";
			}
			else if ( $online_time >= 60 ) {
				$hours = floor($online_time / 60);
				$online = "{$hours} hour".($hours > 1 ? "s":"")." ago";
			}
			else {
				$minutes = floor($online_time);
				$online = "{$minutes} minutes ago";	// Always Greater than 9 )
			}
			
			$score = intval($row['score']);

			$units_value = intval($row['units']);
			$units = floor($units_value/60) . ":" . str_pad($units_value%60, 2, '0', STR_PAD_LEFT);
			if ( intval($row['service_id']) === 5 ) {
				$value = $score;
				if ( $score > 0 ) {
					$units = floor($value/60) . ":" . str_pad($value%60, 2, '0', STR_PAD_LEFT);
				}
				else {
					$units = "--";
				}
			}
			
			$status = $row['status'];
			$status_lower = strtolower($status);
			$mode = 0;
			// Force DEV or PLAY mode //
			if ( strpos($status,"[PLAY]") !== FALSE ) {
				$mode = 2;
			}
			else if ( strpos($status,"[DEV]") !== FALSE ) {
				$mode = 1;
			}
			// Detect DEV or PLAY mode //
			if ( $mode === 0 ) {
				foreach( $play_patterns as $word ) {
					if ( strpos($status_lower,$word) !== FALSE ) {
						$mode = 2;
						break;
					}
				}
			}
			if ( $mode === 0 ) {
				foreach( $dev_patterns as $word ) {
					if ( strpos($status_lower,$word) !== FALSE ) {
						$mode = 1;
						break;
					}
				}
			}
			
			// Build Page //
			$out .= "<div class='" . ($row['live'] ? "live service".$row['service_id']." " : "") ."row'>";
				$out .= "<div class='service'><div class='service-icon{$row['service_id']}'></div></div>";
				$out .= "<div class='avatar'>".($row['avatar']?"<img src='{$row['avatar']}'>":"")."</div>";
				$out .= "<div class='name'><a href='{$row['url']}' title='{$row['user_id']}'>{$row['display_name']}</a> <span class='followers' title='Followers'>[{$row['followers']}]</span>".($row['mature']?" <span class='mature' title='Mature'>[M]</span>":"")."</div>";
				$out .= "<div class='online'>{$online}</div>";
				$out .= "<div class='viewers'>{$row['viewers']}</div>";
				$out .= "<div class='mode'>{$modes[$mode]}</div>";
				$out .= "<div class='status'>{$status}</div>";
				$out .= "<div class='units'>{$units}</div>";
			$out .= "</div>";
		}
	$out .= "</div>";
	
	// * * * //
	
	return $out;
}

function broadcast_list_func( $attr ) {
	// Default Attributes (Arguments) //
	$attr = shortcode_atts( Array(
		'hours' => 24
	), $attr );
	
	// * * * //

	$query = "
		SELECT *, 
			(timestamp > (NOW() - INTERVAL 6 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
		FROM `wp_broadcast_streams`
		WHERE service_id < 5 AND timestamp > (NOW() - INTERVAL {$attr['hours']} HOUR)
		    OR service_id >= 5 AND timestamp > (NOW() - INTERVAL 6 MINUTE)
		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
			CASE 
				WHEN service_id < 5 THEN score
				WHEN service_id >= 5 AND followers >= 50 AND score > 240 THEN score
			END DESC,
			viewers DESC;
	";

//		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
//			units DESC;

	return broadcast_query_func( $query );
}
add_shortcode( 'broadcast_list', 'broadcast_list_func' );

function broadcast_top_func( $attr ) {
	// Default Attributes (Arguments) //
	$attr = shortcode_atts( Array(
		'count' => 20
	), $attr );
	
	// * * * //

	$query = "
		SELECT *, 
			(timestamp > (NOW() - INTERVAL 9 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
		FROM `wp_broadcast_streams`
		WHERE service_id < 5
		ORDER BY units DESC
		LIMIT ${attr['count']};
	";

	return broadcast_query_func( $query );
}
add_shortcode( 'broadcast_top', 'broadcast_top_func' );


function broadcast_widget_func() {
	global $wpdb;
	
	$has_apcu = function_exists('apcu_fetch');
	$apcu_timeout = 2*60;

	$total_streams = FALSE;
	$total_viewers = FALSE;

	if ( $has_apcu ) {
		if ( !isset($_GET["cache"]) ) {
			$total_streams = apcu_fetch( 'broadcast_total_streams' );
			$total_viewers = apcu_fetch( 'broadcast_total_viewers' );
		}
	}
	
	if ( $total_streams === FALSE ) {
		$query = "
			SELECT sum(viewers) AS total_viewers,
				count(*) AS total_streams
			FROM `wp_broadcast_streams`
			WHERE timestamp > (NOW() - INTERVAL 6 MINUTE);
		";
		$result = $wpdb->get_results($query, ARRAY_A);
		
		$total_streams = $result[0]['total_streams'];
		$total_viewers = $result[0]['total_viewers'];

		apcu_store( 'broadcast_total_streams', $total_streams, $apcu_timeout );
		apcu_store( 'broadcast_total_viewers', $total_viewers, $apcu_timeout );
	}
		
	
	$result = NULL;
	
	if ( $has_apcu ) {
		if ( !isset($_GET["cache"]) ) {
			$result = apcu_fetch( 'broadcast_query' );
		}
	}
	
	if ( !$result ) {
		$query = "
			SELECT *,
				(timestamp > (NOW() - INTERVAL 6 MINUTE)) AS live,
				(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
			FROM `wp_broadcast_streams`
			WHERE timestamp > (NOW() - INTERVAL 6 MINUTE)
			ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
				CASE 
					WHEN service_id < 5 THEN score
					WHEN service_id >= 5 AND followers >= 50 AND score > 240 THEN score
				END DESC,
				viewers DESC
			LIMIT 20;
		";
		
		$result = $wpdb->get_results($query, ARRAY_A);
		
		apcu_store( 'broadcast_query', $result, $apcu_timeout );
	}		
?>
<style>

.left { float:left; }
.right { float:right; }
.hidden { display:none; }

.topleft { position:absolute; top:0; left:0; }
.botleft { position:absolute; bottom:0; left:0; }
.topright { position:absolute; top:0; right:0; }
.botright { position:absolute; bottom:0; right:0; }
.midmid { position:absolute; top:50%; left:50%; -ms-transform:translate(-50%,-50%);
	-webkit-transform:translate(-50%,-50%); transform:translate(-50%,-50%); }
.midleft { position:absolute; top:50%; left:0; -ms-transform:translate(0,-50%);
	-webkit-transform:translate(0,-50%); transform:translate(0,-50%); }
.midright { position:absolute; top:50%; right:0; -ms-transform:translate(0,-50%);
	-webkit-transform:translate(0,-50%); transform:translate(0,-50%); }
.midtop { position:absolute; top:0; left:50%; -ms-transform:translate(-50%,0);
	-webkit-transform:translate(-50%,0); transform:translate(-50%,0); }
.midbot { position:absolute; bottom:0; left:50%; -ms-transform:translate(-50%,0);
	-webkit-transform:translate(-50%,0); transform:translate(-50%,0); }

.fiveside { padding-left:5px; padding-right:5px; }
.hoveralpha { opacity:0.5; }
.hoveralpha:hover { opacity:1.0; }

.fivepad { padding:5px; }
.tenpad { padding:10px; }

/* ---- TVBOX ------------------------- */

.tvbox {
  background:#BBC;/*#445;*/
  border-radius:10px;
  font-size:12px;
  overflow:hidden;
}

/* IE SVG Fix */
.tvbox img {
	max-width:100%;
	max-height:100%;
}

/* ---- SCREEN ------------------------- */

.tvbox .screen {
  background:#445;/*#A98;/*#88A;*/
  position:relative;
  width:292px;
  height:219px;
}

.tvbox .black {
  background:#000;
}

/* ---- View ------------------------- */

.tv .view {
	font-size:18px;
	line-height:24px;
	vertical-align:middle;
}

.tvpop .view {
	/*width:300px;*/
	overflow:auto;
}
	
.tvbox .view {
	width:250px;

	position:absolute;
	top:50%;
	left:50%;
	-ms-transform: translate(-50%,-50%);
	-webkit-transform: translate(-50%,-50%);
	transform: translate(-50%,-50%);
}

.tvbox .view .reg {
/*	background:#BBC;*/
	color:#BBC;/*#445;*/
}
.tvbox .view .inv {
	background:#445;
	color:#BBC;
}

.tvpop .view .reg {
/*	background:#BBC;*/
	color:#445;/*#889;*/	
}
.tvpop .view .inv {
	background:#889;
	color:#BBC;
}


.tv .view .filler {
	opacity:0.4;
}


.tv .view .header {
	text-align:center;
	border-radius:10px 10px 0 0;
}
.tv .view .item {
	overflow:auto;
	position:relative;
	padding:2px;
	cursor:pointer;
}
.tv .view .footer {
	text-align:center;
	border-radius:0 0 10px 10px;
	padding-top:10px;
}

.tv .view .footer .more {
	background:#889;/*#445;*/
	color:#8CF;/*#BBC;*/
	padding:4px 10px;
	border-radius:30px;
	cursor:pointer;
}
.tv .view .footer .more:hover {
	background:#44F;/*#C46;*/
	color:#FFF;/*#FCA;*/
}

.tv .view .item:hover {
	background:#44F;/*#F63;/*#44F;/*#8CF;*/
	color:#FFF;
	opacity:1;
}


.tv .view .item .avatar {	
}
.tv .view .item .name {
	padding-left:6px;
	overflow:hidden;
	display:block;
	color:#CCE;	/* hack */
	text-align:center; /* hack */
}
.tv .view .item .service {	
}
.tv .view .item .viewers {
	padding:0 4px;
}

/* ---- Close ------------------------- */

.tv .close {
	font-size:10px;
	padding:6px;

	color:#000;
	cursor:pointer;
}

.tv .close:hover {
	color:#F00;
}

/* ---- Frame ------------------------- */

.tvbox .screen .frame {
	width:270px;
	height:190px;
	
	border:1px solid #000;

	position:absolute;
	top:50%;
	left:50%;
	-ms-transform: translate(-50%,-50%);
	-webkit-transform: translate(-50%,-50%);
	transform: translate(-50%,-50%);
}

.tvbox .screen .frame iframe {
	width:100%;
	height:100%;
}

/* ---- Border ------------------------- */

.tvbox .screen .border {
	position:absolute;
	left:0;
	top:0;
	pointer-events:none;
	
	width:292px;
	height:219px;
}

/* ---- BAR ------------------------- */

.tvbox .bar {
	padding:0 10px;
	padding-top:5px;
	overflow:auto;
	position:relative;
	cursor:pointer;
}

.tvbox .bar .logo {
  background:#445;/*#889;*/
  float:left;
  
  width: 43px;
  height: 25px;
}

.tvbox .bar .logo:hover {
  background:#44F;/*#ACF;*/
}

.tvbox .bar .label {
  background:#889;
  color:#BBC;
  font-weight:bold;
  padding:5px;
  border-radius:10px;
  float:left;
}

.tvbox .bar .number {
  color:#445;/*#BBC;*/
  font-size:16px;
  line-height:25px;
  padding:0 5px;
  float:left;
}

/* ---- TV Pop ------------------------- */

.tvpop {
	position:relative;
}

.tvpop .box {
	position:absolute;
	left:-660px;
	top:-60px;
	width:620px;
/*	height:380px;*/
	
	background:#BBC;/*#445;*/
	border-radius:10px;
	padding:10px;
/*	opacity:0.95;*/

/*	border:3px solid #445;*/
	
	z-index:1000;
	
	box-shadow:0 0 6px #000;
}

.tvpop .leftside {
	width:300px;
	float:left;
}
.tvpop .rightside {
	width:300px;
	float:right;
}

.tvpop .botside {
	padding-top:10px;
	font-size:12px;
	color:#445;
}

.tvpop .botside a {
	font-weight:bold;
	color:#FFF;
}
.tvpop .botside a:hover {
	color:#44F;
}

.tvpop .view .guide {
	background:#445;
	display:inline-block;
	margin-bottom:8px;
	width:236px;
	height:48px;
}

.tvpop .view .guide:hover {
	background:#44F;	
}

</style>
<script>
	function js_remove_class( el, className ) {
		if (el.classList)
			el.classList.remove(className);
		else
			el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
	}
	function js_add_class( el, className ) {
		if (el.classList)
			el.classList.add(className);
		else
			el.className += ' ' + className;
	}	
	
	function broadcast_set( toembed ){
		var tv = document.querySelectorAll('#tv-frame')[0];
		tv.innerHTML = '<iframe src="' + toembed + '" frameborder="0" scrolling="no" allowfullscreen></iframe>';
		js_remove_class( tv, "hidden" );
		
		var view = document.querySelectorAll('#tv-view')[0];
		js_add_class( view, "hidden" );
		
		var border = document.querySelectorAll('#tv-screen')[0];
		js_add_class( border, "black" );

		var close = document.querySelectorAll('#tv-close')[0];
		js_remove_class( close, "hidden" );
	}

	function broadcast_clear(){
		var tv = document.querySelectorAll('#tv-frame')[0];
		tv.innerHTML = "";
		js_add_class( tv, "hidden" );
		
		var view = document.querySelectorAll('#tv-view')[0];
		js_remove_class( view, "hidden" );
		
		var border = document.querySelectorAll('#tv-screen')[0];
		js_remove_class( border, "black" );

		var close = document.querySelectorAll('#tv-close')[0];
		js_add_class( close, "hidden" );
	}

	var tvpop = false;
	
	function broadcast_tvpop_toggle() {
		if ( tvpop ) {
			broadcast_hide_tvpop();
		}
		else {
			broadcast_tvpop();
		}
	}
	
	function broadcast_tvpop() {
		var close = document.querySelectorAll('#tvpop-box')[0];
		js_remove_class( close, "hidden" );
		tvpop = true;
	}
	
	function broadcast_hide_tvpop() {
		var close = document.querySelectorAll('#tvpop-box')[0];
		js_add_class( close, "hidden" );
		tvpop = false;
	}
</script>

<?php
		$img_prefix = "/compo/wp-content/plugins/twidget/";

		$service_img = Array(
			0=>'',
			1=>$img_prefix.'service_twitch.png',
			2=>$img_prefix.'service_hitbox.png',
			3=>$img_prefix.'service_youtube.png',
			4=>$img_prefix.'service_beam.png',
			5=>'',
			6=>$img_prefix.'service_twitch_gamedev.png'
		);
?>

<div class="tv">
<div class="tvpop">
	<div class="box hidden" id="tvpop-box">
		<div class="view">
			<div class="leftside">
				<div class="guide"><a href="http://ludumdare.com/compo/tv/"><img src="http://ludumdare.com/compo/wp-content/themes/ludum/ld2014/ldtv-guide-inv.png"></a></div>
<?php
		$count = count($result);
		for ( $idx = 0; $idx < $count; $idx++ ) {
			if ($idx === 9) {
?>
			</div>
			<div class="rightside">
<?php				
			}
?>
	    		<div class="item reg<?php echo ( $result[$idx]['score'] < 240 ? " filler" : ""); ?>" onclick="broadcast_set('<?php echo $result[$idx]['embed_url']; ?>');">
	    			<span class="service right"><img src="<?php echo $service_img[$result[$idx]['service_id']]; ?>" width="24" height="24" /></span>
	    			<span class="viewers right"><strong><?php echo $result[$idx]['viewers']; ?></strong></span>
	    			<span class="avatar left"><img src="<?php echo $result[$idx]['avatar']; ?>" width="24" height="24" /></span>
	    			<span class="name"><?php echo $result[$idx]['display_name']; ?></span>
	    		</div>
<?php
		}
?>
			</div>
		</div>
		<div class="botside">
			<div class="botright tenpad">
				<a class="left fiveside hoveralpha" href="http://www.twitch.tv/directory/game/Ludum%20Dare" target="_blank"><img src="http://ludumdare.com/compo/wp-content/uploads/2014/10/twitch24.png"></a>
				<a class="left fiveside hoveralpha" href="http://www.twitch.tv/directory/game/Game%20Development"><img src="http://ludumdare.com/compo/wp-content/plugins/twidget/service_twitch_gamedev.png"></a>
				<a class="left fiveside hoveralpha" href="http://www.hitbox.tv/game/ludum-dare" target="_blank"><img src="http://ludumdare.com/compo/wp-content/uploads/2014/10/hitbox24.png"></a>
				<a class="left fiveside hoveralpha" href="https://beam.pro/browse?type=ludum-dare" target="_blank"><img src="http://ludumdare.com/compo/wp-content/plugins/twidget/service_beam.png"></a>
				<a class="left fiveside hoveralpha" href="https://www.youtube.com/results?filters=live&lclk=live&search_sort=video_view_count&search_query=ludum+dare" target="_blank"><img src="http://ludumdare.com/compo/wp-content/uploads/2014/10/youtube24.png"></a>
				<span class="left close" onclick="broadcast_hide_tvpop();"><strong>X</strong></span>
			</div>
			<div>To stream, set your Game to <strong>"Ludum Dare"</strong>.</div>
			<div>
				<a href="/compo/streaming-faq/">Streaming FAQ</a> | 
				<a href="/compo/tv/">Stats</a>
			</div>
		</div>
	</div>
</div>

<div class="tvbox">
  <div class="screen" id="tv-screen">
    <div class="view" id="tv-view">
<?php
		$count = count($result);
		if ( $count > 5 ) {
			$count = 5;
		}
		for ( $idx = 0; $idx < $count; $idx++ ) {
?>
	    	<div class="item reg<?php echo ( $result[$idx]['score'] < 240 ? " filler" : ""); ?>" onclick="broadcast_set('<?php echo $result[$idx]['embed_url']; ?>');">
	    		<span class="service right"><img src="<?php echo $service_img[$result[$idx]['service_id']]; ?>" width="24" height="24" /></span>
	    		<span class="viewers right"><strong><?php echo $result[$idx]['viewers']; ?></strong></span>
	    		<span class="avatar left"><img src="<?php echo $result[$idx]['avatar']; ?>" width="24" height="24" /></span>
	    		<span class="name"><?php echo $result[$idx]['display_name']; ?></span>
	    	</div>
<?php
		}
?>
    	<div class="item">
    		<span class="name"><strong>Hello!</strong> TV is offline due to</span>
    	</div>
    	<div class="item">
    		<span class="name">"changes". I'm hoping to have</span>
    	</div>
    	<div class="item">
    		<span class="name">something better soon.</span>
    	</div>
    	<div class="item">
    		<span class="name">&nbsp;</span>
    	</div>
    	<div class="footer" onclick="broadcast_tvpop_toggle();"><span class="more">More <strong>LIVE</strong> GameDev</span></div>
    </div>
    <div class="frame hidden" id="tv-frame">
	</div>
    <div class="border">
    	<img src="/compo/wp-content/themes/ludum/ld2014/tv-inv.png">
    </div>
    <div class="close topright hidden" id="tv-close" onclick="broadcast_clear();"><strong>X</strong></div>
  </div>
  <div class="bar" onclick="broadcast_tvpop_toggle();">
    <div style="float:right"><span class="label" style="background:#C46;color:#FCA">LIVE</span> <span class="number"><?php echo $total_streams; ?></span> <span class="label">VIEWERS</span> <span class="number" style="padding-right:0"><?php echo $total_viewers; ?></span></div><!--<div class="logo"><img src="/compo/wp-content/themes/ludum/ld2014/ldtv-inv-sm.png"></div>-->
  </div>
  <div style="padding-bottom:10px"></div>
</div>
</div>
<?php
	// NOTE: This function is supposed to return, but I don't care //
	return "";
}
add_shortcode( 'broadcast_widget', 'broadcast_widget_func' );


// Add Local Style Sheet style.css //
add_action( 'wp_enqueue_scripts', 'broadcast_add_my_stylesheet' );
function broadcast_add_my_stylesheet() {
    wp_register_style( 'broadcast-style', plugins_url('style.css?1.1', __FILE__) );
    wp_enqueue_style( 'broadcast-style' );
}


add_action( 'widgets_init', create_function( '', 'register_widget( "twidget" );' ) );
add_action( 'wp_footer', 'AddTTVScripts', 500 );

?>