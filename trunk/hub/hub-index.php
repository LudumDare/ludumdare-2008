<html>
<head>
<title>Ludum Dare dot com</title>
<STYLE TYPE="text/css">
<!--
BODY
{
font-family:arial,verdana,sans-serif;
}
-->
</STYLE>
</head>
<body bgcolor="#5f4f43" text="#ffffff" link="#fbda81" alink="#ffffff" vlink="#fbda81" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
<?php
// Change Magpie's cache directory //
define('MAGPIE_CACHE_DIR', 'hub/cache');
?>
<table height="99%" width="100%" cellpadding="0" border="0">
	<td valign="middle" align="right" width="100%">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<td align="center">
				<table cellspacing="0" cellpadding="0" border="0" width="707">
					<tr>
						<td align="center" bgcolor="#90745e">
							<img src="hub/hub-header.jpg"/>
						</td>
					</tr>
				</table>

				<table cellspacing="0" cellpadding="0" border="0" width="707">
					<tr>
						<td align="center" valign="top" bgcolor="#90745e">
							<font size="-1">		
							<a href="compo/"><img src="hub/hub-compo.png" border="0"></a><br />
							Home of the Ludum Dare game making competition<br />
							<br />
							<a href="planet/"><img src="hub/hub-planet.png" border="0"></a><br />
							Syndicated blogs from the Ludum Dare community<br />
							<br />
							<a href="wiki/"><img src="hub/hub-wiki.png" border="0"></a><br />
							Competition rules and more<br />
							<br />
							</font>
						</td>
						<td align="center" valign="top" bgcolor="#90745e">
							<font size="-1">	
							<a href="compo/"><img src="hub/hub-about.png" border="0"></a><br />
							What is Ludum Dare, and what is it about?<br />
							<br />
							<a href="http://www.gamecompo.com"><img src="hub/hub-mailinglist.png" border="0"></a><br />
							The latest news delivered to your mailbox<br />
							<br />
							<a href="compo/"><img src="hub/hub-irc.png" border="0"></a><br />
							Join us live in #ludumdare on irc.afternet.org<br />
							<br />
							</font>
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="6" border="0" width="707">
					<tr>
						<td align="center" bgcolor="#a4866a">
							Next Event - <strong>Ludum Dare 12 - TBD 2008</strong><br />
							<font size="-1">TODO: Hamumu's Monthly Thing</font>
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="16" border="0" width="707">
					<tr>
						<td align="left" valign="top" bgcolor="#90745e" width="50%">
							<font size="-1">
							<?php
							$news = 'http://www.ludumdare.com/compo/author/news/';
							$newsfeed = 'http://www.ludumdare.com/compo/author/news/feed/';
							$newsitems = 5;
							$newsprefiximage = '/hub/hub-dot.png';
							
							echo "<a href='$news'>";
							echo "<img src='hub/hub-headlines.png' border='0'>";
							echo "</a>";
							
							echo "<img src='hub/hub-blank.png' width='14'>";
							
							echo "<a href='$newsfeed'>";
							echo "<img src='hub/hub-rss.png' border='0'>";
							echo "</a>";							
							
							echo "<br />";

							require 'hub/news.inc';
							?>
							</font>
						</td>
						<td align="left" valign="top" bgcolor="#90745e" width="50%">
							<font size="-1">
							<?php
							$news = 'http://www.ludumdare.com/compo/';
							$newsfeed = 'http://www.ludumdare.com/compo/feed/';
							$newsitems = 5;
							$newsprefiximage = '/hub/hub-dot.png';
							
							echo "<a href='$news'>";
							echo "<img src='hub/hub-latest.png' border='0'>";
							echo "</a>";
							
							echo "<img src='hub/hub-blank.png' width='14'>";
							
							echo "<a href='$newsfeed'>";
							echo "<img src='hub/hub-rss.png' border='0'>";
							echo "</a>";							
							
							echo "<br />";

							require 'hub/news.inc';
							?>
							</font>
						</td>
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="0" width="707">
					<tr>
						<td align="center" bgcolor="#a4866a">
						Copyright Notice
						</td>
					</tr>
				</table>
			</td>
		</table>
	</td>
</table>
</body>
</html>
