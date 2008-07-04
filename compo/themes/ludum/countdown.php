<?php
// countdown function
// parameters: (year, month, day, hour, minute)
// LD 10
// countdown(2007,12,14,12+8,0);
// countdown(2007,12,16,12+8,0);
// countdown(2007,12,30,12+8,0);

// LD 10.5
// countdown(2008,2,22,12+8,0);
// countdown(2008,2,24,12+8,0);

// LD 11
// countdown(2008,4,18,12+8,0);
// countdown(2008,4,20,12+8,0);
// countdown(2008,5,4,12+8,0);

// MINI LD 1
//countdown(2008,6,8,12+8,0);

// MINI LD 2
countdown(2008,7,4,12+5,0); // Start Time //
//countdown(2008,7,6,12+5,0);

//--------------------------
// author: Louai Munajim
// website: www.elouai.com
//
// Note:
// Unix timestamp limitations 
// Date range is from 
// the year 1970 to 2038
//--------------------------
function countdown($year, $month, $day, $hour, $minute)
{
  // make a unix timestamp for the given date
  $the_countdown_date = mktime($hour, $minute, 0, $month, $day, $year, -1);

  // get current unix timestamp
  $today = time();

  $difference = $the_countdown_date - $today;
  if ($difference < 0) $difference = 0;

  $days_left = floor($difference/60/60/24);
  $hours_left = floor(($difference - $days_left*60*60*24)/60/60);
  $minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);
  
  // OUTPUT
//   echo "Today's date ".date("F j, Y, g:i a")."<br/>";
//   echo "Countdown date ".date("F j, Y, g:i a",$the_countdown_date)."<br/>";
  echo $days_left." days ".$hours_left." hours ".$minutes_left." minutes left";
}
?>