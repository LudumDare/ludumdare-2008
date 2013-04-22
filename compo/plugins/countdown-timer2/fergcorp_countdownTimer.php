<?php
/*
Plugin Name: Countdown Timer 2
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Description: Use shortcodes and a widget to count down or up to the years, months, weeks, days, hours, minutes, and/or seconds to a particular event.
Version: 3.0.5
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Use shortcodes and a widget to count down the years, months, weeks, days, hours, and minutes to a particular event
Copyright (c) 2005-2013 Andrew Ferguson

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.


@package Countdown_Timer
@author Andrew Ferguson
@since
@access private
{@internal Missing}
@param		type		$varname	Description
@return		type					Description
@todo

*/

/**
 * Main class for Countdown related activities
 *
 * @package Countdown_Timer
 * @author Andrew Ferguson
 * @since 3.0
 * @access public
 */
class Fergcorp_Countdown_Timer{

	//Per instance
	private $eventList;
	private $eventsPresent;
	private $jsUID = array();

	//Settings
	private $deleteOneTimeEvents;
	private $timeFormat;
	private $showYear;
	private $showMonth;
	private $showWeek;
	private $showDay;
	private $showHour;
	private $showMinute;
	private $showSecond;
	private $stripZero;
	private $enableJS;
	private $timeSinceTime;
	private $titleSuffix;
	private $enableShortcodeExcerpt;

	private $version;

	/**
	 * Load settings
	 *
	 * @since 3.0.4
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function loadSettings(){

		$this->version = get_option("fergcorp_countdownTimer_version");
		$this->deleteOneTimeEvents = get_option("fergcorp_countdownTimer_deleteOneTimeEvents");
		$this->timeFormat = get_option("fergcorp_countdownTimer_timeFormat");
		$this->showYear = get_option("fergcorp_countdownTimer_showYear");
		$this->showMonth = get_option("fergcorp_countdownTimer_showMonth");
		$this->showWeek = get_option("fergcorp_countdownTimer_showWeek");
		$this->showDay = get_option("fergcorp_countdownTimer_showDay");
		$this->showHour = get_option("fergcorp_countdownTimer_showHour");
		$this->showMinute = get_option("fergcorp_countdownTimer_showMinute");
		$this->showSecond = get_option("fergcorp_countdownTimer_showSecond");
		$this->stripZero = get_option("fergcorp_countdownTimer_stripZero");
		$this->enableJS = get_option("fergcorp_countdownTimer_enableJS");
		$this->timeSinceTime = get_option("fergcorp_countdownTimer_timeSinceTime");
		$this->titleSuffix = get_option("fergcorp_countdownTimer_titleSuffix");
		$this->enableShortcodeExcerpt = get_option("fergcorp_countdownTimer_enableShortcodeExcerpt");

		$this->eventList  = get_option("fergcorp_countdownTimer_oneTimeEvent"); //Get the events from the WPDB to make sure a fresh copy is being used
	}



	/**
	 * Default construct to initialize settings required no matter what
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function __construct(){
		// Load settings
		$this->loadSettings();

		if(version_compare($this->version, "3.0.5", "<")){
			add_action('admin_init', array( &$this, 'install' ) );
			add_action('admin_init', array( &$this, 'loadSettings' ) );

		}

		// Register scripts for the countdown timer
		wp_register_script('webkit_sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/" . 'webtoolkit.sprintf.js'), FALSE, $this->version);
		wp_register_script('fergcorp_countdowntimer', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/". 'fergcorp_countdownTimer_java.js'), array('jquery','webkit_sprintf'), $this->version, TRUE );

		if($this->enableJS) {
			add_action('wp_footer', array ( &$this, 'json' ) );
		}


		if($this->enableShortcodeExcerpt) {
			add_filter('the_excerpt', 'do_shortcode');
		}

		//Priority needs to be set to 1 so that the scripts can be enqueued before the scripts are printed, since both actions are hooked into the wp_head action.
		add_action('wp_head', array( &$this, 'print_countdown_scripts' ), 1);

		//Admin hooks
		add_action('admin_init', array( &$this, 'register_settings' ) );			//Initialized the options
		add_action('admin_menu', array( &$this, 'register_settings_page' ) );	//Add Action for adding the options page to admin panel

		add_shortcode('fergcorp_cdt_single', array ( &$this, 'shortcode_singleTimer' ) );
		add_shortcode('fergcorp_cdt', array ( &$this, 'shortcode_showTimer' ) );



		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", array( &$this, 'settings_link' ) );
		
		$tz = get_option('timezone_string');
		if ( $tz ){ //Get and check if we have a valid time zone...
			date_default_timezone_set($tz); //...if so, use it
		}
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @since 3.0
	 * @access public
	 * @author c.bavota (http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/)
	 * @codeCoverageIgnore
	 */
	public function settings_link($links) {
		  $settings_link = '<a href="options-general.php?page=fergcorp_countdownTimer.php">Settings</a>';
		  array_unshift($links, $settings_link);
		  return $links;
	}

	/**
	 * Loads the appropriate scripts when in the admin page
	 *
	 * @since 2.2
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function print_admin_script() {
	    wp_enqueue_script('postbox'); //These appear to be new functions in WP 2.5
	}

	/**
	 * Loads the appropriate scripts for running the timer
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function print_countdown_scripts(){
		if($this->enableJS) {
			wp_enqueue_script('fergcorp_countdowntimer');
			wp_enqueue_script('webkit_sprintf');

		}
		else{
			wp_dequeue_script('fergcorp_countdowntimer');
			wp_dequeue_script('webkit_sprintf');
		}
	}

	/**
	 * Adds the management page in the admin menu
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function register_settings_page(){
		$settings_page = add_options_page(__('Countdown Timer Settings', 'fergcorp_countdownTimer'), __('Countdown Timer', 'fergcorp_countdownTimer'), 'manage_options', basename(__FILE__),  array(&$this, "settings_page"));
		add_action( 'admin_print_scripts-' . $settings_page, array( &$this, 'print_admin_script' ) );
		add_action( 'admin_print_scripts-' . $settings_page, array( &$this, 'print_countdown_scripts' ) );
	}

	/**
	 * Settings page
	 *
	 * @since 3.0
	 * @access private
	 * @author Andrew Ferguson
	 */
	public function settings_page(){ ?>

		<script type="text/javascript">
		// <![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

				// postboxes setup
				postboxes.add_postbox_toggles('fergcorp-countdown-timer'); //For WP2.7 and above

			});

			function clearField(eventType, fieldNum){ //For deleting events without reloading
				var agree=confirm('<?php _e('Are you sure you wish to delete', 'fergcorp_countdownTimer'); ?> '+document.getElementsByName(eventType+'['+fieldNum+'][text]').item(0).value+'?');
				if(agree){
					var inputID = eventType + '_table' + fieldNum;
					document.getElementById(inputID).style.display = 'none';
					document.getElementsByName(eventType+'['+fieldNum+'][date]').item(0).value = '';
					document.getElementsByName(eventType+'['+fieldNum+'][text]').item(0).value = '';
					document.getElementsByName(eventType+'['+fieldNum+'][link]').item(0).value = '';
					document.getElementsByName(eventType+'['+fieldNum+'][timeSince]').item(0).value = '';
					}
				else
					return false;
			}

			function showHideContent(id, show){ //For hiding sections
				var elem = document.getElementById(id);
				if (elem){
					if (show){
						elem.style.display = 'block';
						elem.style.visibility = 'visible';
					}
					else{
						elem.style.display = 'none';
						elem.style.visibility = 'hidden';
					}
				}
			}
		// ]]>
		</script>

		<div class="wrap" id="fergcorp_countdownTimer_div">
			<h2>Countdown Timer</h2>
			<div id="poststuff">

			<?php

				add_meta_box('fergcorp_countdownTimer_resources', 				__('Resources', 'fergcorp_countdownTimer'), 					array ( &$this, 'resources_meta_box'), 					'fergcorp-countdown-timer');
				?>

	            <form method="post" action="options.php">

				<input type="hidden" name="fergcorp_countdownTimer_noncename" id="fergcorp_countdownTimer_noncename" value="<?php wp_create_nonce( plugin_basename(__FILE__) );?>" />

				<?php

                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				settings_fields('fergcorp_countdownTimer_options');


            	add_meta_box('fergcorp_countdownTimer_installation', 			__('Installation and Usage Notes', 'fergcorp_countdownTimer'),	array ( &$this, 'installation_meta_box' ), 				'fergcorp-countdown-timer', 'advanced', 'default');
			   	add_meta_box("fergcorp_countdownTimer_events", 					__('One Time Events', 'fergcorp_countdownTimer'),				array ( &$this, 'events_meta_box' ), 					"fergcorp-countdown-timer");
				add_meta_box("fergcorp_countdownTimer_management", 				__('Management', 'fergcorp_countdownTimer'), 					array ( &$this, "management_meta_box" ), 				"fergcorp-countdown-timer");
				add_meta_box("fergcorp_countdownTimer_display_options", 		__('Countdown Time Display', 'fergcorp_countdownTimer'), 		array ( &$this, "display_options_meta_box" ), 			"fergcorp-countdown-timer");
				add_meta_box("fergcorp_countdownTimer_onHover_time_format", 	__('onHover Time Format', 'fergcorp_countdownTimer'), 			array ( &$this, "onHover_time_format_meta_box" ), 		"fergcorp-countdown-timer");
				add_meta_box("fergcorp_countdownTimer_display_format_options", 	__('Display Format Options', 'fergcorp_countdownTimer'), 		array ( &$this, "display_format_options_meta_box" ), 	"fergcorp-countdown-timer");
				add_meta_box("fergcorp_countdownTimer_example_display", 		__('Example Display', 'fergcorp_countdownTimer'), 				array ( &$this, "example_display_meta_box" ), 			"fergcorp-countdown-timer");
				do_meta_boxes('fergcorp-countdown-timer','advanced',null);

			?>

			<div>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'fergcorp_countdownTimer'); ?>&raquo;" />
				</p>
			</div>
			</form>
	</div>

        </div>
		<?php

	}

   		/**
		 * Creates and defines the metabox for the resources box
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @internal 3.0
		 * @access private
		 * @codeCoverageIgnore
		 *
		 */
		function resources_meta_box(){
			?>
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><ul><li><a href="http://andrewferguson.net/wordpress-plugins/countdown-timer/" target="_blank"><?php _e('Plugin Homepage','fergcorp_countdownTimer'); ?></a></li></ul></td>
                    <td><ul><li><a href="http://wordpress.org/tags/countdown-timer" target="_blank"><?php _e('Support Forum','fergcorp_countdownTimer'); ?></a></li></ul></td>
                    <td><ul><li><a href="http://www.amazon.com/gp/registry/registry.html?ie=UTF8&amp;type=wishlist&amp;id=E7Q6VO0I8XI4" target="_blank"><?php _e('Amazon Wishlist','fergcorp_countdownTimer'); ?></a></li></ul></td>
                    <td><ul><li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=38923"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Donate"/></a></li></ul></td>
                  </tr>
                </table>
				<p><?php _e("I've coded and supported this plugin for several years now, however I am a full-time engineer with a real, full-time job and really only do this programming thing on the side for the love of it. If you would like to continue to see updates, please consider donating above.", 'fergcorp_countdownTimer'); ?></p>
			<?php
		}

		/**
		 * Creates and defines the metabox for the options box
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @internal 3.0
		 * @access private
		 *
		 */
		function display_options_meta_box(){
			?>
			<p><?php _e('This setting controls what units of time are displayed.', 'fergcorp_countdownTimer'); ?></p>
			<ul>
				<li><?php echo __('Years:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showYear", $this->showYear); ?></li>
			  	<li><?php echo __('Months:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showMonth", $this->showMonth); ?></li>
			  	<li><?php echo __('Weeks:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showWeek", $this->showWeek); ?></li>
			  	<li><?php echo __('Days:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showDay", $this->showDay); ?></li>
			  	<li><?php echo __('Hours:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showHour", $this->showHour); ?></li>
			  	<li><?php echo __('Minutes:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showMinute", $this->showMinute); ?></li>
			  	<li><?php echo __('Seconds:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_showSecond", $this->showSecond); ?></li>
				<li><?php echo __('Strip non-significant zeros:', 'fergcorp_countdownTimer') . $this->build_yes_no("fergcorp_countdownTimer_stripZero", $this->stripZero); ?></li>
			</ul>
			<?php
		}

		/**
		 * Creates and defines the metabox for the events box
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @internal 3.0
		 * @access public
		 *
		 */
		public function events_meta_box(){

		?>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr align="center">
					<td><strong><?php _e('Delete', 'fergcorp_countdownTimer'); ?></strong></td>
					<td><?php _e('Event Date', 'fergcorp_countdownTimer'); ?></td>
					<td><?php _e('Event Title', 'fergcorp_countdownTimer'); ?></td>
					<td><?php _e('Link', 'fergcorp_countdownTimer'); ?></td>
					<td><?php _e('Display "Time since"', 'fergcorp_countdownTimer'); ?></td>
				</tr>
				<?php

				$event_count = 0;
					if ( is_array( $this->eventList ) ) {
						foreach ( $this->eventList as $thisEvent ) {
						//If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL
						if ( ( $this->deleteOneTimeEvents ) && ( $thisEvent->getTimestamp() <= time() ) && ( !$thisEvent->getTimeSince() ) ) {
							$thisEvent = NULL;
						}
						else{
							?>
							<tr id="fergcorp_countdownTimer_oneTimeEvent_table<?php echo $event_count; ?>" align="center">
							<td><a href="javascript:void(0);" onclick="javascript:clearField('fergcorp_countdownTimer_oneTimeEvent','<?php echo $event_count; ?>');">X</a></td>
							<?php
							echo "<td>".$this->build_input(array(
														"type" => "text",
														"size" => 30,
														"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][date]",
														"value" => ($thisEvent->date("D, d M Y H:i:s T"))
														)
													)."</td>";

							echo "<td>".$this->build_input(array(
														"type" => "text",
														"size" => 20,
														"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][text]",
														"value" => htmlspecialchars(stripslashes($thisEvent->getTitle()))
														)
													)."</td>";

							echo "<td>".$this->build_input(array(
														"type" => "text",
														"size" => 15,
														"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][link]",
														"value" => $thisEvent->getURL()
														)
													)."</td>";

							echo "<td>".$this->build_input(array(
														"type" => "checkbox",
														"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][timeSince]",
														"value" => 1,
														),
													checked("1", $thisEvent->getTimeSince(), false)
													)."</td>";
							?>
							</tr>
							<?php
							$event_count++;
						}
					}
				}
				?>
				<tr align="center">
					<td></td>
					<?php
					echo "<td>".$this->build_input(array(
												"type" => "text",
												"size" => 30,
												"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][date]",
												)
											)."</td>";

					echo "<td>".$this->build_input(array(
												"type" => "text",
												"size" => 20,
												"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][text]",
												)
											)."</td>";

					echo "<td>".$this->build_input(array(
												"type" => "text",
												"size" => 15,
												"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][link]",
												)
											)."</td>";

					echo "<td>".$this->build_input(array(
												"type" => "checkbox",
												"name" => "fergcorp_countdownTimer_oneTimeEvent[{$event_count}][timeSince]",
												"value" => 1,
												)
											)."</td>";
					?>
				</tr>
			</table>
			<?php echo '<input type="hidden" name="event_count" value="'.($event_count+1).'" />';
			echo "<p>".__("Automatically delete 'One Time Events' after they have occured?", 'fergcorp_countdownTimer');
			//Yes
			echo $this->build_input(array(
								"type"  => "radio",
								"name"  => "fergcorp_countdownTimer_deleteOneTimeEvents",
								"value" => "1",
								),
							checked("1", $this->deleteOneTimeEvents, false)
							);
			_e('Yes', 'fergcorp_countdownTimer');
			echo " :: ";
			//...or No
			echo $this->build_input(array(
								"type"  => "radio",
								"name"  => "fergcorp_countdownTimer_deleteOneTimeEvents",
								"value" => "0",
								),
							checked("0", $this->deleteOneTimeEvents, false)
							);
			_e('No', 'fergcorp_countdownTimer');
			echo "</p>";
        }

        /**
		 * Creates and defines the metabox for the installation box
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @internal 3.0
		 * @access private
		 *
		 */
		function installation_meta_box () { ?>

        <p><?php printf(__("Countdown timer uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>
					<p><?php _e('Examples of some (but not all) valid dates', 'fergcorp_countdownTimer'); ?>:</p>
					<ul style="list-style:inside circle; font-size:11px; margin-left: 20px;">
								<li>now</li>
								<li>31 january 1986</li>
								<li>+1 day</li>
								<li>next thursday</li>
								<li>last monday</li>
					</ul>

        <p><?php printf(__("To insert the Countdown Timer into your sidebar, you can use the <a %s>Countdown Timer Widget</a>.", 'fergcorp_countdownTimer'), "href='".admin_url('widgets.php')."'"); ?></p>

                    <p><?php printf(__("If you want to insert the Countdown Timer into a page or post, you can use the following <abbr %s %s>shortcodes</abbr> to return all or a limited number of Countdown Timers, respectively:", 'fergcorp_countdownTimer'), "title='".__('A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.', 'fergcorp_countdownTimer')."'", "style='cursor:pointer; border-bottom:1px black dashed'" ); ?></p>
					<p>
               			<code>
								[fergcorp_cdt]<br /><br />
                                [fergcorp_cdt max=##]
						</code>
                    </p>
                    <p><?php _e("Where <em>##</em> is maximum number of results to be displayed - ordered by date.", 'fergcorp_countdownTimer'); ?></p>
					<p><?php _e("If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:", 'fergcorp_countdownTimer'); ?></p>
					<p>
						<code><?php _e("Time until my birthday:", 'fergcorp_countdownTimer'); ?><br />
								[fergcorp_cdt_single date="<em>ENTER_DATE_HERE</em>"]
						</code>
					</p>
					<p><?php printf(__("Where <em>ENTER_DATE_HERE</em> uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>
        <?php
		}

        /**
		 * Creates and defines the metabox for the management box
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @internal 3.0
		 * @access private
		 *
		 */
		function management_meta_box(){
			?>
			<p><?php _e("How long the timer remain visable if \"Display 'Time Since'\" is ticked:", 'fergcorp_countdownTimer'); ?><br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e("Seconds:", 'fergcorp_countdownTimer');

            echo $this->build_input(array(
            					"type"  => "text",
            					"size"  => "10",
            					"name"  => "fergcorp_countdownTimer_timeSinceTime",
            					"value" => $this->timeSinceTime
            					));

            _e("(0 = infinite; 86400 seconds = 1 day; 604800 seconds = 1 week)", "fergcorp_countdownTimer"); ?></p>

			<p><?php _e('Enable JavaScript countdown:', 'fergcorp_countdownTimer');

			echo $this->build_yes_no("fergcorp_countdownTimer_enableJS", $this->enableJS);

			?>
			</p>

            <p><?php _e('By default, WordPress does not parse shortcodes that are in excerpts. If you want to enable this functionality, you can do so here. Note that this will enable the parsing of <em>all</em> shortcodes in the excerpt, not just the ones associated with Countdown Timer.', 'fergcorp_countdownTimer'); ?></p>
            <p><?php _e('Enable shortcodes in the_excerpt:', 'fergcorp_countdownTimer');

			echo $this->build_yes_no("fergcorp_countdownTimer_enableShortcodeExcerpt", $this->enableShortcodeExcerpt);

			?>
			</p>
			<?php
		}


	/**
	 * Creates and defines the metabox for the onHover time format box
	 *
	 * @package Countdown_Timer
	 * @author Andrew Ferguson
	 * @internal 3.0
	 * @access private
	 *
	 */
	function onHover_time_format_meta_box(){
		?>
		<p><?php printf(__("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is. onHover Time Format uses <a %s>PHP's Date() function</a>.", 'fergcorp_countdownTimer'), "href='http://us2.php.net/date' target='_blank'"); ?></p>
		<p><?php _e('Examples', 'fergcorp_countdownTimer'); ?>:</p>
		<ul>
			<li>"<em>j M Y, G:i:s</em>" <?php _e('goes to', 'fergcorp_countdownTimer'); ?> "<strong>17 Mar 2008, 14:50:00</strong>"</li>
			<li>"<em>F jS, Y, g:i a</em>" <?php _e('goes to', 'fergcorp_countdownTimer'); ?> "<strong>March 17th, 2008, 2:50 pm</strong>"</li>
		</ul>
		<p><?php _e('onHover Time Format', 'fergcorp_countdownTimer');
		echo $this->build_input(array(
							"type"  => "text",
							"size"  => "20",
							"name"  => "fergcorp_countdownTimer_timeFormat",
							"value" => $this->timeFormat
							));


		echo "</p>";

	}


	/**
	 * Creates and defines the metabox for the display format options box
	 *
	 * @package Countdown_Timer
	 * @author Andrew Ferguson
	 * {@internal since}
	 * @access private
	 *
	 */
	function display_format_options_meta_box(){
		?>
		<p><?php _e('This setting allows you to customize how each event is styled and wrapped.', 'fergcorp_countdownTimer'); ?></p>
		<p><?php _e('<strong>Title Suffix</strong> sets the content that appears immediately after title and before the timer.', 'fergcorp_countdownTimer'); ?></p>
		<p><?php _e('Examples/Defaults', 'fergcorp_countdownTimer'); ?>:</p>
		<ul>
			<li><em><?php _e('Title Suffix', 'fergcorp_countdownTimer'); ?>:</em> <code>:&lt;br /&gt;</code></li>
		</ul>
		<p><?php _e('Title Suffix', 'fergcorp_countdownTimer');

       	echo $this->build_input(array(
							"type"  => "text",
							"size"  => "20",
							"name"  => "fergcorp_countdownTimer_titleSuffix",
							"value" => htmlspecialchars(stripslashes($this->titleSuffix))
		));
    	echo "</p>";
	}


	/**
	 * Creates and defines the metabox for the example display box
	 *
	 * @package Countdown_Timer
	 * @author Andrew Ferguson
	 * @internal 3.0
	 * @access private
	 *
	 */
	function example_display_meta_box(){
		echo "<ul>";
		$this->showTimer();
		echo "</ul>";
		if($this->enableJS) {
            $this->json();
		}
	}


	/**
	 * Creates a PHP-based one-off time for use outside the loop
	 *
	 * @param $date string Any string parsable by PHP's strtotime function
	 * @since 2.2
	 * @access public
	 * @author Andrew Ferguson
	*/
	public function singleTimer( $date ){

		return $this->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( $date ) ) , TRUE );

	}

	/**
	 * Returns/echos the formated output for the countdown
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output boolean If TRUE, will echo the results with no return; If FALSE, will return the results
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 * @return string Formated output ready for display
	*/
	public function showTimer($eventLimit = -1, $output = TRUE){
		$this->eventsPresent = FALSE;
		$toReturn = "";

		//Make sure there's something to count
		if($this->eventList){
			$this->eventsPresent = TRUE;
		}

		$eventCount = count($this->eventList);
		if($eventLimit != -1)	//If the eventLimit is set
			$eventCount = min($eventCount, $eventLimit);

		//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
		if($this->eventsPresent){
			$this->eventsPresent = FALSE; //Reset the test
			for($i = 0; $i < $eventCount; $i++){
				if( ( '' == ( $thisTimer = $this->formatEvent($this->eventList[$i], FALSE ) ) ) && ( $eventCount < count( $this->eventList ) ) ) {
					$eventCount++;
				}
				else{
					$toReturn .= $thisTimer;
				}
			}
		}

		if(!$this->eventsPresent){
				$toReturn = "<li>".__('No dates present', 'fergcorp_countdownTimer')."</li>";
		}

		//Echo or return
		if($output)
			echo $toReturn;
		else
			return $toReturn;
	}


	/**
	 * Returns an individual countdown element
	 *
	 * @param $text string Text associated with the countdown event
	 * @param $time int Unix time of the event
	 * @param $offset int Server offset of the time
	 * @param $timeSince int If the event should be displayed if it has already passed
	 * @param $timeSinceTime int If $timeSince is set, how long should it be displayed for in seconds
	 * @param $link string Link associated with the countdown event
	 * @param $timeFormat string Forming of the onHover time display
	 * @param $standalone If true, don't output the li-element
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function formatEvent($thisEvent, $standAlone = FALSE){
		$time_left = $thisEvent->getTimestamp() - time();

		$content = '';

		if(!$standAlone)
			$content = "<li class = 'fergcorp_countdownTimer_event_li'>";

		$eventTitle = "<span class = 'fergcorp_countdownTimer_event_title'>".($thisEvent->getURL()==""?$thisEvent->getTitle():"<a href=\"".$thisEvent->getURL()."\" class = 'fergcorp_countdownTimer_event_linkTitle'>".$thisEvent->getTitle()."</a>").'</span>'.$this->titleSuffix."\n";

		if ($this->timeFormat == "") {
			$this->timeFormat = get_option('date_format') . ", " . get_option('time_format');
		}
		$timePrefix = "";//"<abbr title = \"".date_i18n($this->timeFormat, $thisEvent->getTimestamp()+(3600*(get_option('gmt_offset'))), TRUE)."\" id = '".$thisEvent->getUID()."' class = 'fergcorp_countdownTimer_event_time'>";

		if ( 	( $time_left <= 0 ) &&
					( ( ( $thisEvent->getTimeSince() ) &&
						( ( ( $time_left + (int) $this->timeSinceTime ) > 0 ) || ( 0 == (int) $this->timeSinceTime ) ) )
				|| ( $standAlone) ) ) {

					//If the event has already passed and we still want to display the event

			$this->eventsPresent = TRUE; //Set to TRUE so we know there's an event to display
			if ( $thisEvent->getTitle() ) {
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("%s ago", 'fergcorp_countdownTimer'), $this->fuzzyDate( time(), $thisEvent->getTimestamp() ) )."</abbr>";
		}
		elseif($time_left > 0){ //If the event has not yet happened yet
			$this->eventsPresent = TRUE; //Set to TRUE so we know there's an event to display

			if($thisEvent->getTitle()){
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("in %s", 'fergcorp_countdownTimer'), $this->fuzzyDate($thisEvent->getTimestamp(), time() ) )."</abbr>";
		}
		else{
			return NULL;
		}

		array_push($this->jsUID, $thisEvent);

		if(!$standAlone)
			$content .= "</li>\r\n";
		return $content;
	}

	/**
	 * Returns the numerical part of a single countdown element
	 *
	 * @param $targetTime
	 * @param $nowTime
	 * @param $realTargetTime
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function fuzzyDate ( $targetTime, $nowTime ) {

		$timeDelta = new Fergcorp_DeltaTime($targetTime, $nowTime);

		$rollover = 0;
		$s = '';
		$sigNumHit = false;

		if($timeDelta->s < 0){
			$timeDelta->i--;
			$timeDelta->s = 60 + $timeDelta->s;
		}

		if($timeDelta->i < 0){
			$timeDelta->h--;
			$timeDelta->i = 60 + $timeDelta->i;
		}

		if($timeDelta->h < 0){
			$timeDelta->d--;
			$timeDelta->h = 24 + $timeDelta->h;
		}

		if($timeDelta->d < 0){
			$timeDelta->m--;
			$timeDelta->d = $timeDelta->d + cal_days_in_month(CAL_GREGORIAN, $timeDelta->nowMonth, $timeDelta->nowYear); //Holy crap! When did they introduce this function and why haven't I heard about it??
		}

		if($timeDelta->m < 0){
			$timeDelta->y--;
			$timeDelta->m = $timeDelta->m + 12;
		}

		//Year
		if($this->showYear){
			if($sigNumHit || !$this->stripZero || $timeDelta->y){
				$s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d year,", "%d years,", $timeDelta->y, "fergcorp_countdownTimer"), $timeDelta->y)."</span> ";
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $timeDelta->y*31536000;
		}

		//Month
		if($this->showMonth){
			if($sigNumHit || !$this->stripZero || intval($timeDelta->m + ($rollover/2628000)) ){
				$timeDelta->m = intval($timeDelta->m + ($rollover/2628000));
				$s .= '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d month,", "%d months,", $timeDelta->m, "fergcorp_countdownTimer"), $timeDelta->m)."</span> ";
				$rollover = $rollover - intval($rollover/2628000)*2628000; //(12/31536000)
				$sigNumHit = true;
			}
		}
		else{
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)
			//If we showed years, but not months, we need to account for those.
			if($this->showYear){
				$timeDelta->delta = $timeDelta->delta - $timeDelta->y*31536000;
			}

			//Re calculate the resultant times
			$timeDelta->w = intval( $timeDelta->delta/(86400*7) );
			$timeDelta->d = intval( $timeDelta->delta/86400 );
			$timeDelta->h = intval( ($timeDelta->delta - $timeDelta->d*86400)/3600 );
			$timeDelta->i = intval( ($timeDelta->delta - $timeDelta->d*86400 - $timeDelta->h*3600)/60 );
			$timeDelta->s = intval( ($timeDelta->delta - $timeDelta->d*86400 - $timeDelta->h*3600 - $timeDelta->i*60) );

			//and clear any rollover time
			$rollover = 0;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if($this->showWeek){
			if($sigNumHit || !$this->stripZero || ( ($timeDelta->d + intval($rollover/86400) )/7)){
				$timeDelta->w = $timeDelta->w + intval($rollover/86400)/7;
				$s .= '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d week,", "%d weeks,", (intval( ($timeDelta->d + intval($rollover/86400) )/7)), "fergcorp_countdownTimer"), (intval( ($timeDelta->d + intval($rollover/86400) )/7)))."</span> ";
				$rollover = $rollover - intval($rollover/86400)*86400;
				$timeDelta->d = $timeDelta->d - intval( ($timeDelta->d + intval($rollover/86400) )/7 )*7;
				$sigNumHit = true;
			}
		}

		//Day
		if($this->showDay){
			if($sigNumHit || !$this->stripZero || ($timeDelta->d + intval($rollover/86400)) ){
				$timeDelta->d = $timeDelta->d + intval($rollover/86400);
				$s .= '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d day,", "%d days,",  $timeDelta->d, "fergcorp_countdownTimer"), $timeDelta->d)."</span> ";
				$rollover = $rollover - intval($rollover/86400)*86400;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $timeDelta->d*86400;
		}

		//Hour
		if($this->showHour){
			if($sigNumHit || !$this->stripZero || ($timeDelta->h + intval($rollover/3600)) ){
				$timeDelta->h = $timeDelta->h + intval($rollover/3600);
				$s .= '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d hour,", "%d hours,", $timeDelta->h, "fergcorp_countdownTimer"), $timeDelta->h)."</span> ";
				$rollover = $rollover - intval($rollover/3600)*3600;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $timeDelta->h*3600;
		}

		//Minute
		if($this->showMinute){
			if($sigNumHit || !$this->stripZero || ($timeDelta->i + intval($rollover/60)) ){
				$timeDelta->i = $timeDelta->i + intval($rollover/60);
				$s .= '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d minute,", "%d minutes,", $timeDelta->i, "fergcorp_countdownTimer"), $timeDelta->i)."</span> ";
				$rollover = $rollover - intval($rollover/60)*60;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $timeDelta->i*60;
		}

		//Second
		if($this->showSecond){
			$timeDelta->s = $timeDelta->s + $rollover;
			$s .= '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d second,", "%d seconds,", $timeDelta->s, "fergcorp_countdownTimer"), $timeDelta->s) . "</span> ";
		}

		//Catch blank statements
		if($s==""){
			 // @codeCoverageIgnoreStart
			if($this->showSecond){
				$s = '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d second,", "%d seconds,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			elseif($this->showMinute){
				$s = '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d minute,", "%d minutes,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			elseif($this->showHour){
				$s = '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d hour,", "%d hours,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			elseif($this->showDay){
				$s = '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d day,", "%d days,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			elseif($this->showWeek){
				$s = '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d week,", "%d weeks,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			elseif($this->showMonth){
				$s = '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d month,", "%d months,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
			 // @codeCoverageIgnoreEnd
			else{
				$s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' . sprintf(_n("%d year,", "%d years,", "0", "fergcorp_countdownTimer"), "0") . "</span> ";
			}
		}
		return preg_replace("/(, ?<\/span> *)$/is", "</span>", $s);
	}

		/**
	 * Processes [fergcorp_cdt max=##] shortcode
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer(s)
	*/
	function shortcode_showTimer($atts) {
		extract(shortcode_atts(array(
								'max' => '-1',
								),
								$atts));

		return $this->showTimer($max, FALSE);
	}

	/**
	 * Processes [fergcorp_cdt_single date="_DATE_"] shortcode
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer
	*/
	function shortcode_singleTimer($atts) {
		extract(shortcode_atts(array(
			'date' => '-1',
		), $atts));

		return $this->singleTimer( $date );
	}


	/**
	 * Initialized the options
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function register_settings(){	// Init plugin options to white list our options
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_deleteOneTimeEvents');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_timeFormat');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showYear');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showMonth');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showWeek');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showDay');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showHour');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showMinute');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_showSecond');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_stripZero');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_enableJS');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_timeSinceTime');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_titleSuffix');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_enableShortcodeExcerpt');
		register_setting('fergcorp_countdownTimer_options', 'fergcorp_countdownTimer_oneTimeEvent', array (&$this, 'sanitize'));
	}

	public function compare($adate, $bdate)
					{
					    if($adate < $bdate){
					        return -1;
					    }else if($adate == $bdate){
					        return 0;
					    }else{
					        return 1;
					    }
					} 

	/**
	 * Sanitize the callback
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	 */
	public function sanitize($input){

				$event_object_array = array();

				foreach($input as $event){
					if("" != $event["date"]){
						if(!isset($event["timeSince"])){ //Checkmark boxes are only set if they are checked, this sets the value to 0 if it isn't set at all
							$event["timeSince"] = 0;
						}
						array_push($event_object_array, new Fergcorp_Countdown_Timer_Event(strtotime($event["date"]), $event["text"], $event["link"], $event["timeSince"]));
					}
				}

				/*Begin sorting events by time*/
				usort($event_object_array, array($this,'compare'));
		return $event_object_array;
	}


	function json(){
		$params = array(
						"showYear"		=>$this->showYear,
						"showMonth"		=>$this->showMonth,
						"showWeek"		=>$this->showWeek,
						"showDay"		=>$this->showDay,
						"showHour"		=>$this->showHour,
						"showMinute"	=>$this->showMinute,
						"showSecond"	=>$this->showSecond,
						"stripZero"		=>$this->stripZero,
					);

		$js_events = array();
		foreach($this->jsUID as $event){
				$js_events[$event->getUID()] = $event->getTimestamp();
		}

		$js_lang = array(
						"year" 	=> addslashes( _n( "%d year,", "%d years,", 1, "fergcorp_countdownTimer" )),
						"years"	=> addslashes( _n( "%d year,", "%d years,", 2, "fergcorp_countdownTimer" )),

						"month" 	=> addslashes( _n( "%d month,", "%d months,", 1, "fergcorp_countdownTimer" )),
						"months"	=> addslashes( _n( "%d month,", "%d months,", 2, "fergcorp_countdownTimer" )),

						"week" 	=> addslashes( _n( "%d week,", "%d weeks,", 1, "fergcorp_countdownTimer" )),
						"weeks"	=> addslashes( _n( "%d week,", "%d weeks,", 2, "fergcorp_countdownTimer" )),

						"day" 	=> addslashes( _n( "%d day,", "%d days,", 1, "fergcorp_countdownTimer" )),
						"days"	=> addslashes( _n( "%d day,", "%d days,", 2, "fergcorp_countdownTimer" )),

						"hour" 	=> addslashes( _n( "%d hour,", "%d hours,", 1, "fergcorp_countdownTimer" )),
						"hours"	=> addslashes( _n( "%d hour,", "%d hours,", 2, "fergcorp_countdownTimer" )),

						"minute" 	=> addslashes( _n( "%d minute,", "%d minutes,", 1, "fergcorp_countdownTimer" )),
						"minutes"	=> addslashes( _n( "%d minute,", "%d minutes,", 2, "fergcorp_countdownTimer" )),

						"second" 	=> addslashes( _n( "%d second,", "%d seconds,", 1, "fergcorp_countdownTimer" )),
						"seconds"	=> addslashes( _n( "%d second,", "%d seconds,", 2, "fergcorp_countdownTimer" )),


						"agotime" 	=> addslashes(__('%s ago', 'fergcorp_countdownTimer')),
						"intime"	=> addslashes(__('in %s', 'fergcorp_countdownTimer')),
					);
		wp_localize_script( 'fergcorp_countdowntimer', 'fergcorp_countdown_timer_js_lang', $js_lang);
		wp_localize_script( 'fergcorp_countdowntimer', 'fergcorp_countdown_timer_jsEvents', $js_events );
		wp_localize_script( 'fergcorp_countdowntimer', 'fergcorp_countdown_timer_options', $params );
	}

	/**
	 * Sets the defaults for the timer
	 *
	 * @since 3.0
	 * @access public
	 * @author Andrew Ferguson
	*/
	public static function install(){
		$plugin_data = get_plugin_data(__FILE__);

		//Move widget details from old option to new option only if the new option does not exist
		if( ( $oldWidget = get_option( "widget_fergcorp_countdown" ) ) && (!get_option( "widget_fergcorp_countdown_timer_widget" ) ) ) {
			update_option("widget_fergcorp_countdown_timer_widget",  array(	"title" 		=> $oldWidget["title"],
																			"countLimit"	=> $oldWidget["count"],
																			)
			);
			delete_option("widget_fergcorp_countdown");

			global $sidebars_widgets;
			//check to see if the old widget is being used
			$i=0;
			$j=0;
			foreach($sidebars_widgets as $sidebar => $widgets){
				$thisSidebar = $sidebar;
				if( 'wp_inactive_widgets' == $sidebar )
					continue;

				if ( is_array($widgets) ) {
			 		foreach ( $widgets as $widget ) {
						if( "fergcorp_countdowntimer" == $widget ){
							$sidebars_widgets[$thisSidebar][$j] = "fergcorp_countdown_timer_widget-2"; //not sure why the ID has to be 2, but it does
						}
						$j++;
					}
				}
			}
		wp_set_sidebars_widgets($sidebars_widgets);
		wp_get_sidebars_widgets();



		}
		//If the old option exist and the new option exists (becuase of the above logic test), don't update the new option and just remove the old option
		elseif( $oldWidget ){
			delete_option("widget_fergcorp_countdown");
		}

		//Move timeFormat data from old option to new option only if the new option does not exist
		if( ( $timeOffset = get_option( "fergcorp_countdownTimer_timeOffset" ) ) && (!get_option( "fergcorp_countdownTimer_timeFormat" ) ) ) {
			update_option( 'fergcorp_countdownTimer_timeFormat', $timeOffset);
			delete_option("fergcorp_countdownTimer_timeOffset");
		}
		//If the old option exist and the new option exists (becuase of the above logic test), don't update the new option and just remove the old option
		elseif( $timeOffset ){
			delete_option("fergcorp_countdownTimer_timeOffset");
		}

		$oneTimeEvent = get_option("fergcorp_countdownTimer_oneTimeEvent");
		if( ( $oneTimeEvent )  && ( gettype($oneTimeEvent[0]) == "array") ) {
			$event_object_array = array();
			foreach( $oneTimeEvent as $event ) {
				array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($event["date"], $event["text"], $event["link"], $event["timeSince"]));
			}
			update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		}

		//Install the defaults
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'deleteOneTimeEvents', '0');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'timeFormat', 'F jS, Y, g:i a');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showYear', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showMonth', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showWeek', '0');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showDay', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showHour', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showMinute', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'showSecond', '0');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'stripZero', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'enableJS', '1');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'timeSinceTime', '0');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'titleSuffix', ':<br />');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'enableShortcodeExcerpt', '0');
		Fergcorp_Countdown_Timer::install_option('fergcorp_countdownTimer_', 'oneTimeEvent', '0');

		//Update version number...last thing
		update_option("fergcorp_countdownTimer_version", $plugin_data["Version"]);
	}

		/**
		 * Checks to see if an option exists in either the old or new database location and then sets the value to a default if it doesn't exist
		 *
		 * @param $prefix string Prefix for the option i.e. fergcorp_countdownTimer_
		 * @param $option string Actual option
		 * @param $default string What the default value should be if it doesn't exist
		 * @since 2.4
		 * @access private
		 * @author Andrew Ferguson
		 * @return string The content of the post with the appropriate dates inserted (if any)
		*/
		function install_option($prefix, $option, $default){
			if(get_option($prefix.$option) != NULL){
				return false;
			}
			else{
				update_option($prefix.$option, $default);
				return true;
			}
		}

		/**
		 * Builds <input> HTML
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @since 3.0
		 * @access public
		 * @param array		$inputArray
		 * @param string 	$inputString
		 * $return string HMTL code
		 */
		public function build_input($inputArray, $inputString=''){
			$attributes = "";
			foreach ($inputArray as $key => $value) {
				$attributes .= "$key=\"$value\" ";
			}
			return " <input ".trim($attributes." ".$inputString)." />";
		}
		/**
		 * Builds Yes/No <input> HTML
		 *
		 * @package Countdown_Timer
		 * @author Andrew Ferguson
		 * @since 3.0
		 * @access public
		 * @param string	$name
		 * @param string 	$option
		 * $return string HMTL code
		 */
		public function build_yes_no($name, $option){
			//Yes
			$output = $this->build_input(array(
								"type"  => "radio",
								"name"  => $name,
								"value" => "1"
								),
							checked("1", $option, false)
							);
			$output .= __('Yes', 'fergcorp_countdownTimer');
			$output .= " :: ";
			//...or No
			$output .= $this->build_input(array(
								"type"  => "radio",
								"name"  => $name,
								"value" => "0"
								),
							checked("0", $option, false)
							);
			$output .= __('No', 'fergcorp_countdownTimer');

			return $output;
		}
}

class Fergcorp_Countdown_Timer_Event extends DateTime {
	private $title;
	private $time;
	private $url;
	private $timeSince;
	private $UID;

	public function __construct ($time, $title = NULL, $url = NULL, $timeSince = NULL){
		$this->setTitle($title);
		$this->setTime($time);
		$this->setURL($url);
		$this->setTimeSince($timeSince);
		$this->UID = "x".md5(rand());
		parent::__construct("@".$time);
	}

	public function getTimestamp() {
         return method_exists('DateTime', 'getTimestamp') ? parent::getTimestamp() : $this->time;
    }

	public function setTitle ( $title ) {
		$this->title = (string)$title;
	}

	public function setTime ( $time ) {
		$this->time = $time;
	}

	public function setURL ( $url ) {
		$this->url = $url;
	}

	public function setTimeSince ( $timeSince ) {
		$this->timeSince = $timeSince;
	}

	public function getTitle () {
		return $this->title;
	}

	public function getTime () {
		return $this->time;
	}

	public function getURL () {
		return $this->url;
	}

	public function getTimeSince () {
		return $this->timeSince;
	}

	public function getUID () {
		return $this->UID;
	}

	public function date ( $format ) {
		return date($format, $this->getTimestamp());
	}
}
/**
 * Widget class for Countdown Timer
 *
 * @since 3.0
 * @access public
 * @author Andrew Ferguson
 */
class Fergcorp_Countdown_Timer_Widget extends WP_Widget{

	public function __construct(){
		global $fergcorp_countdownTimer_init;
		parent::__construct(
					'fergcorp_countdown_timer_widget', // Base ID
					'Countdown Timer', // Name
					array( 'description' => __('Adds the Countdown Timer', 'fergcorp_countdownTimer' ), ) // Args
		);
	}

	public function form( $instance){

		if ( $instance ) {
			$title = esc_attr( $instance['title'] );
			$countLimit = $instance['countLimit'];
		}
		else {
			$title = __( 'Countdown Timer', 'fergcorp_countdownTimer');
			$countLimit = -1;
		}

		?>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'fergcorp_countdownTimer'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		<label for="<?php echo $this->get_field_id( 'countLimit' ); ?>"><?php _e('Maximum # of events to show:', 'fergcorp_countdownTimer'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'countLimit' ); ?>" name="<?php echo $this->get_field_name( 'countLimit' ); ?>" type="text" value="<?php echo $countLimit; ?>" size="5"/>
		<small><strong><?php _e('Notes:', 'fergcorp_countdownTimer'); ?></strong> <?php _e("Set 'Maximum # of events' to '-1' if you want no limit.", 'fergcorp_countdownTimer'); ?></small>
						<?php
	}

	public function update( $new_instance, $old_instance ){
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['countLimit'] = intval($new_instance['countLimit']);
		return $instance;

	}

	public function widget( $args, $instance ){
		global $fergcorp_countdownTimer_init;
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'];
			echo esc_html( $instance['title'] ); //$instance['title']
			echo $args['after_title'];
		}
		echo "<ul>";
		$fergcorp_countdownTimer_init->showTimer($instance['countLimit']);
		echo "</ul>";

		echo $args['after_widget'];

	}
}

class Fergcorp_DeltaTime{

	public $nowYear;
	public $nowMonth;
	public $nowDay;
	public $nowHour ;
	public $nowMinute;
	public $nowSecond;

	public $targetYear;
	public $targetMonth;
	public $targetDay;
	public $targetHour ;
	public $targetMinute;
	public $targetSecond;

	public $y;
	public $m;
	public $d;
	public $h;
	public $i;
	public $s;

	public $w;

	public $delta;

	public function __construct($targetTime, $nowTime){
		$this->nowYear = date("Y", $nowTime);
		$this->nowMonth = date("m", $nowTime);
		$this->nowDay = date("d", $nowTime);
		$this->nowHour = date("H", $nowTime);
		$this->nowMinute = date("i", $nowTime);
		$this->nowSecond = date("s", $nowTime);

		$this->targetYear = date("Y", $targetTime);
		$this->targetMonth = date("m", $targetTime);
		$this->targetDay = date("d", $targetTime);
		$this->targetHour = date("H", $targetTime);
		$this->targetMinute = date("i", $targetTime);
		$this->targetSecond = date("s", $targetTime);

		$this->y = $this->targetYear - $this->nowYear;
		$this->m  = $this->targetMonth - $this->nowMonth;
		$this->d = $this->targetDay - $this->nowDay;
		$this->h  = $this->targetHour - $this->nowHour;
		$this->i = $this->targetMinute - $this->nowMinute;
		$this->s = $this->targetSecond - $this->nowSecond;

		$this->delta = $targetTime - $nowTime;
	}
}

function fergcorp_countdown_timer_register_widgets() {
	register_widget( 'Fergcorp_Countdown_Timer_Widget' );
}

function fergcorp_countdownTimer($countLimit = -1) {
	global $fergcorp_countdownTimer_init;
	$fergcorp_countdownTimer_init->showTimer($countLimit, TRUE);
}

function fergcorp_countdownTimer_init() {
	global $fergcorp_countdownTimer_init;
			// Load localization domain
	load_plugin_textdomain( 'fergcorp_countdownTimer', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	$fergcorp_countdownTimer_init = new Fergcorp_Countdown_Timer();
}

register_activation_hook( __FILE__, array('Fergcorp_Countdown_Timer', 'install') );
add_action( 'init', 'fergcorp_countdownTimer_init', 5 );
add_action( 'widgets_init', 'fergcorp_countdown_timer_register_widgets' );

?>
