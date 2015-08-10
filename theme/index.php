<?php

//if (!isset($_GET['shite'])) {
//	echo "Thanks Everyone! You did great!";
//	die;
//}


/* Copy "settings-example.php" to "settings.php", and make your changes */
include 'settings.php';

$do_logging = false;
$log_file = 'logs/log.txt';

$killvote_weight = 3;

/*
CREATE DATABASE ludum_theme;
CREATE USER 'ludum_theme'@'localhost' IDENTIFIED BY 'MYPASSWD';
// The other part I did inside the control panel, sorry //

DROP TABLE IF EXISTS `themes`;

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `theme` tinytext NOT NULL,
  `up` int(11) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `kill` int(11) NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

LOAD DATA LOCAL INFILE '/home/username/www/theme/ld26.txt' INTO TABLE themes LINES TERMINATED BY '\r\n';
// The above didn't work for us. LOAD DATA wasn't enabled by our MySQL install. //

// I ended up importing directly in to the table as a CSV file, but set my delimeters all to " (which it wouldn't find). //
// The ' is way too common. Also, I imported it in to the 'theme' field of the table by making that my fields string. //

// NOTE: haha, I was getting an error on LD27. Problem was that there was a theme suggestion "NULL".
//   I also removed all the "s from the theme suggestions replacing them 's. The real problem was NULL though.
*/

/*
REMOVING 

SELECT * FROM `themes` 
	WHERE `id`<800000 AND (`up`-`down`-(`kill`*3))<-100;


UPDATE `themes` 
	SET `id`=`id`+800000 
	WHERE `id`<800000 AND (`up`-`down`-(`kill`*3))<-100;


*/

function get_ip() { 
	$ip; 
	if (getenv("HTTP_CLIENT_IP")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
	else if(getenv("HTTP_X_FORWARDED_FOR")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if(getenv("REMOTE_ADDR")) 
		$ip = getenv("REMOTE_ADDR"); 
	else 
		$ip = "UNKNOWN";
	return $ip; 
}

function is_bot($user_agent)
{
  //if no user agent is supplied then assume it's a bot
  if($user_agent == "")
    return 1;

  //array of bot strings to check for
  $bot_strings = Array(  "google",     "bot",
            "yahoo",     "spider",
            "archiver",   "curl",
            "python",     "nambu",
            "twitt",     "perl",
            "sphere",     "PEAR",
            "java",     "wordpress",
            "radian",     "crawl",
            "yandex",     "eventbox",
            "monitor",   "mechanize",
            "facebookexternal"
          );
  foreach($bot_strings as $bot)
  {
    if(strpos($user_agent,$bot) !== false)
    { return 1; }
  }
  
  return 0;
}

if (is_bot($_SERVER['HTTP_USER_AGENT'])) die;

$bans = file('ban.txt');
foreach ($bans as $b)
{
	if (trim($b)==get_ip())
	{
		echo '<H1>FUCK YOU!</h1>';
		ECHO '<H1>Sincerely, Sos ( just.sos.it@gmail.com )</h1>';
		die;
	}
	
}

function get_db()
{
	global $login, $password, $database;
	
	$link = mysql_connect('localhost', $login, $password);
	if (!$link) die('Could not connect: ' . mysql_error());
	if (!mysql_select_db($database)) die('Could not select database');
	return $link;
}

$themes = array();
$link = get_db();
$query = 'SELECT * FROM `themes` WHERE `id`<800000 ORDER BY rand() LIMIT 1;';
$c=0;
$result = mysql_query($query);
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
{
	$themes[$c]=$line;
	$c++;
}
/*
$total = array();
$query = 'SELECT * FROM `themes` WHERE `id`=888888;';
$result2 = mysql_query($query);
while ($line = mysql_fetch_array($result2, MYSQL_ASSOC)) 
{
	$total=$line;
}

$target=500000;
$pixs = ($total['up'])/($target/100);
//if ($pixs>100) $pixs=100;
*/
//echo'<center style="font-family:sans-serif;"><br/><br/><h1>IT ENDED, the slaughter!</H1></CENTER>';
//echo'<center style="font-family:sans-serif;"><br/><br/><h1>'.$total['up'].' votes were given</H1></CENTER>';
//echo'<center style="font-family:sans-serif;"><br/><br/><h1>I am too sleepy to do post results tonight.</H1></CENTER>';

//$_GET['shite']='all';
if (isset($_GET['shite']))
{
	global $killvote_weight;
	//$number = ($_GET['view']='all');
	mysql_free_result($result);
	$sort = '(`up`-`down`-(`kill`*'.strval($killvote_weight).')) DESC';
//	$sort = '(`up`-`down`-(`kill`*3) DESC';
	if (isset($_GET['sort']))
	{
		//if (($_GET['sort'])=='0') $sort = '(`up`-`down`) DESC';
		if (($_GET['sort'])=='1') $sort = '(`theme`)';
		if (($_GET['sort'])=='2') $sort = '(`up`) DESC';
		if (($_GET['sort'])=='3') $sort = '(`down`) DESC';
		if (($_GET['sort'])=='4') $sort = '(`kill`) DESC';
		if (($_GET['sort'])=='5') $sort = '(`up`+`down`+`kill`) DESC';
		if (($_GET['sort'])=='6') $sort = '(`up`-`down`) DESC';
		if (($_GET['sort'])=='7') $sort = '(`up`-`down`-`kill`) DESC';
	}
	$query = 'SELECT * FROM `themes` WHERE `id`<800000 ORDER BY '.$sort.' '.(($_GET['shite']=='all') ? '' : 'LIMIT 250').';';
	$c=0;
	$result = mysql_query($query);
	if (!$result) die('Query error: ' . mysql_error());
	echo '<h1 style="color:red;font-family:sans-serif;text-align:center;">THEME KILLING RESULTS!</h1>';              
//	echo '<b style="color:#48f;font-family:sans-serif;text-align:center;display:block;">'.$total['up'].' votes given</b>';  
	echo '<b style="color:#48f;font-family:sans-serif;text-align:center;display:block;">Killvote Weight: '.$killvote_weight.'</b>';  

	echo '<table style="width:90%;font-family:sans-serif;">';
	echo '
	<tr>
		<td width=40><b><a href="?shite='.$_GET['shite'].'&sort=0">RANK</a></b></td>
		<td width=250><b><a href="?shite='.$_GET['shite'].'&sort=1">THEME</a></b></td>
		<td width=300><b><a href="?shite='.$_GET['shite'].'&sort=2">UP VOTES</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=3">DOWN</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=4">KILL</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=5">SUM</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=6">UP-DOWN</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=7">WEIGHTLESS</a></b></td>
		<td><b><a href="?shite='.$_GET['shite'].'&sort=0">TOTAL <font size="-2">(WEIGHTED)</font></a></b></td>
	</tr>
	';
	$c=0;
	$ups=0;
	$downs=0;
	$kills=0;
	
	global $killvote_weight;
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$votes = $line['up'];
		$downvotes = $line['down'];
		$killvotes = $line['kill'];
		$sum = $votes + $downvotes + $killvotes;
		$updown = $votes - $downvotes;
		$updownkill = $votes - $downvotes - $killvotes;
		
		if ( $line['id'] < 800000 )
			echo '<tr style="background:'. (($c&1) ? '#eee' : '#ddd').';">';
		else
			echo '<tr style="background:'. (($c&1) ? '#ecc' : '#dbb').';">';

		echo '
			<td width=40><center><b>'.($c+1).'.</b></center></td>
			<td width=200>'.$line['theme'].'</td>
			<td><div style="display:inline-block;background-color:green;width:'.(($votes > 250 ) ? 250 : $votes).'px;height:20px;"></div>&nbsp;'.$votes.'</td>
			<td><div style="display:inline-block;background-color:#A00;width:'.(($downvotes > 60 ) ? 60 : $downvotes).'px;height:20px;"></div>&nbsp;'.$downvotes.'</td>
			<td><div style="display:inline-block;background-color:#F00;width:'.(($killvotes > 60 ) ? 60 : $killvotes).'px;height:20px;"></div>&nbsp;'.$killvotes.'</td>
			<td>&nbsp;'.$sum.'</td>
			<td>&nbsp;'.$updown.'</td>
			<td>&nbsp;'.$updownkill.'</td>
			<td><center><b>'.($votes-$downvotes-($killvotes*$killvote_weight)).'</b></center></td>
		</tr>
		';

//			<td><img src="'.(($votes > 500 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($votes > 500 ) ? 500 : $votes).'" height="20"/>&nbsp;'.$votes.'</td>
//			<td><img src="'.(($downvotes > 100 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($downvotes > 100 ) ? 100 : $downvotes).'" height="20"/>&nbsp;'.$downvotes.'</td>
//			<td><img src="'.(($killvotes > 100 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($killvotes > 100 ) ? 100 : $killvotes).'" height="20"/>&nbsp;'.$killvotes.'</td>

		$c++;
		$ups+=$line['up'];
		$downs+=$line['down'];
		$kills+=$line['kill'];
	}
	echo '</table>';
	echo '<b style="color:#4f8;font-family:sans-serif;text-align:center;display:block;">'.$ups.' upvotes given</b>';	
	echo '<b style="color:#f84;font-family:sans-serif;text-align:center;display:block;">'.$downs.' downvotes given</b>';	
	echo '<b style="color:#f84;font-family:sans-serif;text-align:center;display:block;">'.$kills.' killvotes given</b>';	
		mysql_free_result($result);
//		mysql_free_result($result2);
		mysql_close($link);
		die;
}
$agent = "BOT BOT BOT BOT BOT BOT";
if (isset($_SERVER['HTTP_USER_AGENT'])) $agent = $_SERVER['HTTP_USER_AGENT'];
if (isset($_GET['up']))
{
	//die;
	$up = strval(intval( mysql_real_escape_string($_GET['up']) ));

	$query = 'UPDATE `themes` SET `up`=`up`+1, `time`='.time().' WHERE `id`='.$up.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
//	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
//	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging,$log_file;
	if ( $do_logging == true ) {
		$ff = fopen($log_file,'a');
		fwrite($ff,'IP: '.get_ip().' | UP: '.$up.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}

if ( isset($_GET['down']))
{
	//die;
	$down = strval(intval( mysql_real_escape_string($_GET['down']) ));

	$query = 'UPDATE `themes` SET `down`=`down`+1, `time`='.time().' WHERE `id`='.$down.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
//	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
//	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging,$log_file;
	if ( $do_logging == true ) {
		$ff = fopen($log_file,'a');
		fwrite($ff,'IP: '.get_ip().' | DOWN: '.$down.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}

if ( isset($_GET['kill']))
{
	//die;
	$kill = strval(intval( mysql_real_escape_string($_GET['kill']) ));

	$query = 'UPDATE `themes` SET `kill`=`kill`+1, `time`='.time().' WHERE `id`='.$kill.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
//	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
//	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging,$log_file;
	if ( $do_logging == true ) {
		$ff = fopen($log_file,'a');
		fwrite($ff,'IP: '.get_ip().' | KILL: '.$kill.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}
echo'<style>a { text-decoration:none; } a:hover { text-decoration:underline; }</style>';
echo'<style>input { text-decoration:none; border:none; background: none; cursor: pointer; display: in-line; margin: 0px; padding: 0px; } input:hover { text-decoration:underline; }</style>';
echo'<center style="font-family:sans-serif;"><img src="slaughter.gif"><br/><br /><table style="border:0px solid #555;font-size:250%;font-family:sans-serif;text-align:center;width:760px;">';
echo'<tr><td style="border:0px solid #555;padding:16px;text-align:center;font-weight:bold;font-size:125%;" colspan=2><a target="_blank" style="color:#4b7aa0;" href="https://www.google.com/search?q='.urlencode($themes[0]['theme']).'">'.$themes[0]['theme'].'</a></td></tr>';

echo'<tr><td style="border:1px solid #555;padding:16px;text-align:center;width:50%;"><a style="color:#080;" href="?up='.$themes[0]['id'].'">GOOD</a></td>';
echo'<td style="border:1px solid #555;padding:16px;text-align:center;width:50%;"><a style="color:#800;" href="?down='.$themes[0]['id'].'">BAD</a></td></tr>';
echo'<tr><td style="border:1px solid #555;padding:16px;text-align:center;" colspan=2;><a style="color:#f00;" href="?kill='.$themes[0]['id'].'">SLAUGHTER</a></td></tr>';

//echo'<tr><td style="border:1px solid #555;padding:16px;text-align:center;width:50%;"><form method="post"><input type="hidden" name="up" value="'.$themes[0]['id'].'" /><input style="color:#080;" type="submit" value="GOOD" /></form></td>';
//echo'<td style="border:1px solid #555;padding:16px;text-align:center;width:50%;"><a style="color:#800;" href="?down='.$themes[0]['id'].'">BAD</a></td></tr>';
//echo'<tr><td style="border:1px solid #555;padding:16px;text-align:center;" colspan=2;><a style="color:#f00;" href="?kill='.$themes[0]['id'].'">SLAUGHTER</a></td></tr>';

//echo '<tr><td style="border:1px solid #555;padding:20px;text-align:center;" colspan=2>';
//echo '<b>Slaughter progress:</b> '.sprintf("%1.4f",$pixs).'%<br/>';
//echo '<i style="font-size:50%">Target kill count: <del>100000</del> '.$target.'</i><br/>';
//echo '<div style="text-align:left;border:1px solid black; width:100%;"><img src="greenbar.png" width="'.$pixs.'%" height="32"></div>';
//echo '</td>';
echo '</tr>';
echo '</table>';
echo '<br/><font size="+2"><b>How this works:</b></font><br />';
echo '
You get a theme, and click <b>GOOD</b> or <b>BAD</b>!<br />
If you feel a theme is inappropriate (or just hate it), click <b>SLAUGHTER</b><br />
Repeat. Every click helps!<br />';
//<b>no hacking plz!</b><br/>';
//<br>
//<b style="color:#248;font-size:250%;">NOTE:</b><br/>
//<span style="color:#048;font-size:150%;">Stuff like <i>\'2-bit art\' or \'one button controls\' or \'racing game\'</i><br/>and any other implying genre, technical or any other limitations are <B>NOT THEMES</b><br/>Please vote them down. I will remove them regardless of votes anyways.</span>
//';
echo '<br />
Special thanks to <a href="http://twitter.com/Sosowski">Sos</a> for creating the Slaughter';
mysql_free_result($result);
//mysql_free_result($result2);
mysql_close($link);
?>