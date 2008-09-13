<?php
/*
Plugin Name: SimpleMap
Plugin URI: http://www.codedojo.com
Description: Let users add themselves to a world map.
Version: 0.1
Author: Seth A. Robinson	
Author URI: http://www.codedojo.com
*/

/*
Copyright 2008 Seth A. Robinson	 (seth@rtsoft.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once dirname(__FILE__)."/options.php";

add_action('admin_menu', 'rt_simplemap_add_pages');
register_activation_hook(__FILE__,'rt_simplemap_activate');


function GetMapHeader()
{
return '
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . get_option('rt_simplemap_api_key') . '"
    type="text/javascript"></script>
    <script src="/wp-content/plugins/rt_simplemap/js/elabel.js" type="text/javascript"></script>
	<script type="text/javascript">

    //<![CDATA[
	//SimpleMap by Seth A. Robinson (www.codedojo.com)
  var map = null;
  var geocoder = null;

 function createMarker(point,html) 
 {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function()
		 {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }
';

}

function GetUserOptionsScript()
{

return '
 function showAddress(address, theForm) 
 {
 	  if (geocoder) 
	  {
	    geocoder.getLatLng(
          address,
          function(point) 
		  {
            if (!point) 
			{
	              alert(address + " not found.  Maybe just try entering your city, state, and country?");
            } else 
			{
				theForm.latitude.value=point.y; 	
				theForm.longitude.value=point.x; 
				//fake way to trigger the submit, the real way wont wait for the google look-up
				theForm.submit();
		   }
          }
        );
      }
    }
	';
}

function GetUserOptions()
{
	$cur = wp_get_current_user();
	$uid = $cur->ID;
	if (!$uid) 
	{
		return "You will need to logon to add your own location.";
	} else
	{
	$formData = "";
	
	if ( isset($_POST['submitted']) ) 
	{
		//for debugging
		//print_r($_POST);
		$lat = $_POST['latitude'];
		$lon = $_POST['longitude'];
	  	global $wpdb;
   		$table_name = $wpdb->prefix . "simplemap";
		$sql = "REPLACE INTO $table_name (id, lat, lon) VALUES ($cur->ID , $lat, $lon)";
	    $results = $wpdb->query( $sql );
		//$formData .= "<p>Your info was saved/updated.  (Btw, you live at Lat: $lat Lon: $lon)</p>";
	}
		
 		$action_url = $_SERVER['REQUEST_URI'];	
$formData .= "<form name=\"mapInput\" action=\"$action_url\" method=\"post\" onsubmit=\"showAddress(this.address.value, this); return false\">
<p>
<input type=\"hidden\" name=\"submitted\" value=\"1\" /> 
<input type=\"text\" size=\"60\" name=\"address\" value=\"(enter your address here to add/update your position)\" />
<input type=\"submit\" value=\"Update your position!\" />
<input name=\"latitude\" type=\"hidden\" id=\"latitude\" />  
<input name=\"longitude\" type=\"hidden\" id=\"longitude\" />  
</p>
</form>";

	return $formData;
	 }
}


function AddName($name, $url, $long, $lat)
{
$opacity = 60;	
	  return 'var label = new ELabel(new GLatLng(' . $long . ',' . $lat . '), "<div style=\"background-color: #f2efe9; padding: 0px;\"><font size=\"-2\">' . '<a href=\"' . $url . '\">' . $name . '</a>' . '</font></div>", null, null, '. $opacity . ', true);
      map.addOverlay(label);
	  ';
}

function AddMapScript()
{
 $temp = '
 function load() 
	{
      if (GBrowserIsCompatible()) 
	  {
        map = new GMap2(document.getElementById("map"));
	    geocoder = new GClientGeocoder();
		//map.addControl(new GLargeMapControl());
	    //map.addControl(new GMapTypeControl());
	    map.enableScrollWheelZoom();
		map.setMapType(G_SATELLITE_MAP);
        map.setCenter(new GLatLng(37.4419, -122.1419), 1);
';
	
	//add all names here...
 	
	global $wpdb;
	$table_name = $wpdb->prefix . "simplemap";
	$sql = "SELECT * FROM $table_name";
	$results = $wpdb->get_results( $sql );
    $topurl = get_bloginfo("url");
 
	foreach ($results as $rec)
	{
		$user_info = get_userdata($rec->id);
		if ($user_info)
		{
			$temp .= AddName($user_info->display_name, $topurl . '/authors/' . strtolower($user_info->user_login) . '/' , $rec->lat, $rec->lon);
		}
	}

$temp .= '}
}
';	

return $temp;
}

function rt_simplemap_display($atts) 
{
	
	//we don't really use any shortcode parms yet, but if we did...
	extract(shortcode_atts(array(
		'foo' => 'no foo',
		'bar' => 'default bar',
	), $atts));


$final = GetMapHeader();
$mapInit = '';
$final .= GetUserOptionsScript();
$belowMap = GetUserOptions(); //this also handles processing form input from a previous request and must go before AddMapScript
$final .= AddMapScript();

$final .= '
    //]]>
    </script>
	<div id="map" style="width: 500px; height: 300px"></div>
' . $belowMap;
return $final;
}

add_shortcode('simplemap', 'rt_simplemap_display');
?>
