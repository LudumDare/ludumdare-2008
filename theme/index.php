<?php

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
) ENGINE=InnoDB AUTO_INCREMENT=888889 DEFAULT CHARSET=utf8;

LOAD DATA LOCAL INFILE '/home/username/www/theme/ld26.txt' INTO TABLE themes LINES TERMINATED BY '\r\n';
// The above didn't work for us. LOAD DATA wasn't enabled by our MySQL install. //

// I ended up importing directly in to the table as a CSV file, but set my delimeters all to " (which it wouldn't find). //
// The ' is way too common. Also, I imported it in to the 'theme' field of the table by making that my fields string. //

*/

$do_logging = false;

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
	$link = mysql_connect('localhost', 'ludum_theme', 'heyYOUGUYS!400ThanksForTEHDB');
	if (!$link) die('Could not connect: ' . mysql_error());
	if (!mysql_select_db('ludum_theme')) die('Could not select database');
	return $link;
}
$themes = array();
$link = get_db();
$query = 'SELECT * FROM `themes` WHERE `id`<808080 ORDER BY rand() LIMIT 1;';
$c=0;
$result = mysql_query($query);
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
{
	$themes[$c]=$line;
	$c++;
}
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

//echo'<center style="font-family:sans-serif;"><br/><br/><h1>IT ENDED, the slaughter!</H1></CENTER>';
//echo'<center style="font-family:sans-serif;"><br/><br/><h1>'.$total['up'].' votes were given</H1></CENTER>';
//echo'<center style="font-family:sans-serif;"><br/><br/><h1>I am too sleepy to do post results tonight.</H1></CENTER>';

//$_GET['shit']='all';
if (isset($_GET['shit']))
{
	//$number = ($_GET['view']='all');
	mysql_free_result($result);
	$sort = '(`up`-`down`-`kill`-`kill`-`kill`) DESC';
	if (isset($_GET['sort']))
	{
		//if (($_GET['sort'])=='0') $sort = '(`up`-`down`) DESC';
		if (($_GET['sort'])=='1') $sort = '(`theme`)';
		if (($_GET['sort'])=='2') $sort = '(`up`) DESC';
		if (($_GET['sort'])=='3') $sort = '(`down`) DESC';
		if (($_GET['sort'])=='4') $sort = '(`kill`) DESC';
	}
	$query = 'SELECT * FROM `themes` WHERE `id`<808080 ORDER BY '.$sort.' '.(($_GET['shit']=='all') ? '' : 'LIMIT 150').';';
	$c=0;
	$result = mysql_query($query);
	if (!$result) die('Query error: ' . mysql_error());
	echo '<h1 style="color:red;font-family:sans-serif;text-align:center;">THEME KILLING RESULTS!</h1>';              
	echo '<b style="color:#48f;font-family:sans-serif;text-align:center;display:block;">'.$total['up'].' votes given</b>';  

	echo '<table style="width:90%;font-family:sans-serif;">';
	echo '<tr>
<td width=40><b><a href="?shit='.$_GET['shit'].'&sort=0">RANK</a></b></td>
<td width=200><b><a href="?shit='.$_GET['shit'].'&sort=1">THEME</a></b></td>
<td width=600><b><a href="?shit='.$_GET['shit'].'&sort=2">VOTES</a></b></td>
<td><b><a href="?shit='.$_GET['shit'].'&sort=3">DOWN VOTES</a></b></td>
<td><b><a href="?shit='.$_GET['shit'].'&sort=4">KILL VOTES</a></b></td></tr>';
	$c=0;
	$ups=0;
	$downs=0;
	$kills=0;
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$votes = $line['up'];
		$downvotes = $line['down'];
		$killvotes = $line['kill'];
			
		echo '<tr style="background:'. (($c&1) ? '#eee' : '#ddd').';">
		<td width=40><center><b>'.($c+1).'.</b></center></td>
		<td width=200>'.$line['theme'].'</td>
		<td><img src="'.(($votes > 500 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($votes > 500 ) ? 500 : $votes).'" height="20"/>&nbsp;'.$votes.'</td>
		<td><img src="'.(($downvotes > 100 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($downvotes > 100 ) ? 100 : $downvotes).'" height="20"/>&nbsp;'.$downvotes.'</td>
		<td><img src="'.(($killvotes > 100 ) ? 'redbar.png' : 'greenbar.png').'" width="'.(($killvotes > 100 ) ? 100 : $killvotes).'" height="20"/>&nbsp;'.$killvotes.'</td></tr>';
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
		mysql_free_result($result2);
		mysql_close($link);
		die;
}
$agent = "BOT BOT BOT BOT BOT BOT";
if (isset($_SERVER['HTTP_USER_AGENT'])) $agent = $_SERVER['HTTP_USER_AGENT'];
if (isset($_GET['up']))
{
	//die;
	$up = mysql_real_escape_string($_GET['up']);

	$query = 'UPDATE `themes` SET `up`=`up`+1, `time`='.time().' WHERE `id`='.$up.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging;
	if ( $do_logging == true ) {
		$ff = fopen('log.txt','a');
		fwrite($ff,'IP: '.get_ip().' | UP: '.$up.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}

if ( isset($_GET['down']))
{
	//die;
	$down = mysql_real_escape_string($_GET['down']);

	$query = 'UPDATE `themes` SET `down`=`down`+1, `time`='.time().' WHERE `id`='.$down.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging;
	if ( $do_logging == true ) {
		$ff = fopen('log.txt','a');
		fwrite($ff,'IP: '.get_ip().' | DOWN: '.$down.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}

if ( isset($_GET['kill']))
{
	//die;
	$kill = mysql_real_escape_string($_GET['kill']);

	$query = 'UPDATE `themes` SET `kill`=`kill`+1, `time`='.time().' WHERE `id`='.$kill.' AND `time`<'.(time()-20).';';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	$query = 'UPDATE `themes` SET `up`=`up`+1 WHERE `id`=888888;';
	if (!mysql_query($query)) die('Query error: ' . mysql_error());
	
	global $do_logging;
	if ( $do_logging == true ) {
		$ff = fopen('log.txt','a');
		fwrite($ff,'IP: '.get_ip().' | KILL: '.$kill.' | TIME: '.date('d-m-y H:i:s').' | ' . $agent . "\n");
		fclose($ff);
	}
}

echo'<center style="font-family:sans-serif;"><br/><br/><img src="slaughter.gif"><br/><br/><table style="border:1px solid #555;font-size:250%;font-family:sans-serif;text-align:center;width:700px;">';
echo'<tr><td style="border:1px solid #555;padding:20px;text-align:center;" colspan=2><a target="_blank" style="color:#4b7aa0;" href="https://www.google.com/search?q='.urlencode($themes[0]['theme']).'">'.$themes[0]['theme'].'</a></td></tr>';
echo'<tr><td style="border:1px solid #555;padding:20px;text-align:center;width:50%;"><a style="color:#080;" href="?up='.$themes[0]['id'].'">GOOD</a></td>';
echo'<td style="border:1px solid #555;padding:20px;text-align:center;width:50%;"><a style="color:#800;" href="?down='.$themes[0]['id'].'">BAD</a></td></tr>';
echo'<tr><td style="border:1px solid #555;padding:20px;text-align:center;" colspan=2;><a style="color:#f00;" href="?kill='.$themes[0]['id'].'">SLAUGHTER</a></td></tr>';
echo '<tr><td style="border:1px solid #555;padding:20px;text-align:center;" colspan=2>';
echo '<b>Slaughter progress:</b> '.sprintf("%1.4f",$pixs).'%<br/>';
echo '<i style="font-size:50%">Target kill count: <del>100000</del> '.$target.'</i><br/>';
echo '<div style="text-align:left;border:1px solid black; width:100%;"><img src="greenbar.png" width="'.$pixs.'%" height="32"></div>';
echo '</td></tr>';
echo '</table>';
echo '<br/><br/><b>How does it work:</b><br/>';
echo '
You get a theme clickie <b>GOOD</b> or <b>BAD</b>!<br/>
If it\'s not a theme, hit <b>SLAUGHTER</b><br/>
<b>no hacking plz!</b><br/>';
//<br>
//<b style="color:#248;font-size:250%;">NOTE:</b><br/>
//<span style="color:#048;font-size:150%;">Stuff like <i>\'2-bit art\' or \'one button controls\' or \'racing game\'</i><br/>and any other implying genre, technical or any other limitations are <B>NOT THEMES</b><br/>Please vote them down. I will remove them regardless of votes anyways.</span>
//';
mysql_free_result($result);
mysql_free_result($result2);
mysql_close($link);
?>