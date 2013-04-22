<?php
require_once('fergcorp_countdownTimer.php');
echo 'Current PHP version: ' . phpversion() . "\n";

Class Test_Init_Fergcorp_Countdown_Timer extends WP_UnitTestCase{

	public $plugin_slug = 'countdown_timer';
	private $plugin;
	private $pluginData;

	public function setUp() {

	    parent::setUp();

		$this->pluginData = get_plugin_data("fergcorp_countdownTimer.php");

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
	    	$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();

		//$this->plugin->__construct();
    }

	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);

	}

	/**
	 * @group guest
	 * @group init
	 */
	public function test_fergcorp_countdownTimer(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is");
		fergcorp_countdownTimer();
	}
	/**
	 * @group guest
	 * @group init
	 */
	public function test_fergcorp_countdown_timer_register_widgets(){
		fergcorp_countdown_timer_register_widgets();
		$this->assertArrayHasKey("Fergcorp_Countdown_Timer_Widget", $GLOBALS["wp_widget_factory"]->widgets);
	}

	/**
	 * @group guest
	 * @group init
	 */
	public function test_fergcorp_countdownTimer_init(){
		fergcorp_countdownTimer_init();
		$this->assertTrue(isset($GLOBALS["fergcorp_countdownTimer_init"]));
		$this->assertInstanceOf("Fergcorp_Countdown_Timer", $GLOBALS["fergcorp_countdownTimer_init"]);
	}

}



Class Test_Install_Fergcorp_Countdown_Timer extends WP_UnitTestCase{

	public $plugin_slug = 'countdown_timer';
	private $plugin;
	private $pluginData;

	public function setUp() {

	    parent::setUp();

		define('WP_ADMIN', true);
		$user1_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user1_id );

		$this->pluginData = get_plugin_data("fergcorp_countdownTimer.php");

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
	    $this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		//$this->plugin->install();

		//$this->plugin->__construct();
    }

	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);

	}

	/**
	 * @group install
	 * @covers Fergcorp_Countdown_Timer::install
	 */
	public function test_install_fresh(){
		$this->plugin->install();

		$defaultInstallValues = array(
										'deleteOneTimeEvents' => '0',
										'timeFormat' => 'F jS, Y, g:i a',
										'showYear' => '1',
										'showMonth' => '1',
										'showWeek' => '0',
										'showDay' => '1',
										'showHour' => '1',
										'showMinute' => '1',
										'showSecond' => '0',
										'stripZero' => '1',
										'enableJS' => '1',
										'timeSinceTime' => '0',
										'titleSuffix' => ':<br />',
										'enableShortcodeExcerpt' => '0',
										'oneTimeEvent' => '0',
										'version' => $this->pluginData["Version"]
									);

		foreach($defaultInstallValues as $key => $value){
			$this->assertEquals($value, get_option("fergcorp_countdownTimer_".$key));
		}
	}
	
	/**
	 * @group install
	 * @covers Fergcorp_Countdown_Timer::install
	 */
	public function test_install_v2_4_3(){
		$oneTimeEvent = array(
			array(
					"date" 		=>	1356998400,
					"text" 		=>	"First of the year",
					"timeSince"	=>	"1",
					"link"		=> 	"http://google.com",
				),
			array(
					"date" 		=>	1451001600,
					"text" 		=>	"Christmas 2015",
					"timeSince"	=>	NULL,
					"link"		=> 	"",
				),
		);
		
		$sidebars_widgets = array(
									"wp_inactive_widgets"	=> array(),
									"sidebar-1" 			=> array(	"fergcorp_countdowntimer",
																		"search-2",
																		"recent-posts-2",
																		"recent-comments-2",
																		"archives-2",
																		"categories-2",
																		"meta-2",
																	),
									"sidebar-2"				=> array(),
									"sidebar-3" 			=> array(),
									"array_version" => 3
								);
		wp_set_sidebars_widgets($sidebars_widgets);
        wp_get_sidebars_widgets();
		
		$installValues = array(
								'widget_fergcorp_countdown'							=>	'a:2:{s:5:"title";s:18:"My Countdown Timer";s:5:"count";i:5;}',
								'fergcorp_countdownTimer_deleteOneTimeEvents'		=>	'0',
								'fergcorp_countdownTimer_timeOffset'				=>	'j M Y, G:i:s',
								'fergcorp_countdownTimer_showYear'					=>	'0',
								'fergcorp_countdownTimer_showMonth'					=>	'1',
								'fergcorp_countdownTimer_showWeek'					=>	'0',
								'fergcorp_countdownTimer_showDay'					=>	'1',
								'fergcorp_countdownTimer_showHour'					=>	'0',
								'fergcorp_countdownTimer_showMinute'				=>	'0',
								'fergcorp_countdownTimer_showSecond'				=>	'0',
								'fergcorp_countdownTimer_stripZero'					=>	'1',
								'fergcorp_countdownTimer_enableJS'					=>	'1',
								'fergcorp_countdownTimer_timeSinceTime'				=>	'0',
								'fergcorp_countdownTimer_titleSuffix'				=>	'::',
								'fergcorp_countdownTimer_enableShortcodeExcerpt'	=>	'0',
								'fergcorp_countdownTimer_version'					=>	'2.4.3',
								'fergcorp_countdownTimer_oneTimeEvent'				=>	$oneTimeEvent,
		);
		
		foreach($installValues as $key => $value){
			update_option($key, $value);
		}
		
		$updatedValues = array(
										'deleteOneTimeEvents' => '0',
										'timeFormat' => 'j M Y, G:i:s',
										'showYear' => '0',
										'showMonth' => '1',
										'showWeek' => '0',
										'showDay' => '1',
										'showHour' => '0',
										'showMinute' => '0',
										'showSecond' => '0',
										'stripZero' => '1',
										'enableJS' => '1',
										'timeSinceTime' => '0',
										'titleSuffix' => '::',
										'enableShortcodeExcerpt' => '0',

										'version' => $this->pluginData["Version"]
									);	
		
		$this->plugin->install();
		
		foreach($updatedValues as $key => $value){
			$this->assertEquals($value, get_option("fergcorp_countdownTimer_".$key));
		}
		
		$updatedOneTimeEvent = get_option("fergcorp_countdownTimer_oneTimeEvent");
		
		for($i=0; $i < count($oneTimeEvent); $i++){
			$this->assertEquals($oneTimeEvent[$i]["date"], $updatedOneTimeEvent[$i]->getTimestamp());
			$this->assertEquals($oneTimeEvent[$i]["text"], $updatedOneTimeEvent[$i]->getTitle());
			$this->assertEquals($oneTimeEvent[$i]["timeSince"], $updatedOneTimeEvent[$i]->getTimeSince());
			$this->assertEquals($oneTimeEvent[$i]["link"], $updatedOneTimeEvent[$i]->getURL());
		}
	}

	/**
	 * @group install
	 * @covers Fergcorp_Countdown_Timer::install
	 */
	public function test_install_partial_v2_v_3(){
		$oneTimeEvent = array(
			array(
					"date" 		=>	1356998400,
					"text" 		=>	"First of the year",
					"timeSince"	=>	"1",
					"link"		=> 	"http://google.com",
				),
			array(
					"date" 		=>	1451001600,
					"text" 		=>	"Christmas 2015",
					"timeSince"	=>	NULL,
					"link"		=> 	"",
				),
		);
		
		$sidebars_widgets = array(
									"wp_inactive_widgets"	=> array(),
									"sidebar-1" 			=> array(	"fergcorp_countdowntimer",
																		"search-2",
																		"recent-posts-2",
																		"recent-comments-2",
																		"archives-2",
																		"categories-2",
																		"meta-2",
																	),
									"sidebar-2"				=> array(),
									"sidebar-3" 			=> array(),
									"array_version" => 3
								);
		wp_set_sidebars_widgets($sidebars_widgets);
        wp_get_sidebars_widgets();
		
		$installValues = array(
								'widget_fergcorp_countdown_timer_widget'			=>	'blahblah', //dummy values
								'widget_fergcorp_countdown'							=>	'a:2:{s:5:"title";s:18:"My Countdown Timer";s:5:"count";i:5;}',
								'fergcorp_countdownTimer_deleteOneTimeEvents'		=>	'0',
								'fergcorp_countdownTimer_timeOffset'				=>	'j M Y, G:i:s',
								'fergcorp_countdownTimer_timeFormat'				=>	'F jS, Y, g:i a',
								'fergcorp_countdownTimer_showYear'					=>	'0',
								'fergcorp_countdownTimer_showMonth'					=>	'1',
								'fergcorp_countdownTimer_showWeek'					=>	'0',
								'fergcorp_countdownTimer_showDay'					=>	'1',
								'fergcorp_countdownTimer_showHour'					=>	'0',
								'fergcorp_countdownTimer_showMinute'				=>	'0',
								'fergcorp_countdownTimer_showSecond'				=>	'0',
								'fergcorp_countdownTimer_stripZero'					=>	'1',
								'fergcorp_countdownTimer_enableJS'					=>	'1',
								'fergcorp_countdownTimer_timeSinceTime'				=>	'0',
								'fergcorp_countdownTimer_titleSuffix'				=>	'::',
								'fergcorp_countdownTimer_enableShortcodeExcerpt'	=>	'0',
								'fergcorp_countdownTimer_version'					=>	'2.4.3',
								'fergcorp_countdownTimer_oneTimeEvent'				=>	$oneTimeEvent,
		);
		
		foreach($installValues as $key => $value){
			update_option($key, $value);
		}
		
		$updatedValues = array(
										'deleteOneTimeEvents' => '0',
										'showYear' => '0',
										'showMonth' => '1',
										'showWeek' => '0',
										'showDay' => '1',
										'showHour' => '0',
										'showMinute' => '0',
										'showSecond' => '0',
										'stripZero' => '1',
										'enableJS' => '1',
										'timeSinceTime' => '0',
										'titleSuffix' => '::',
										'enableShortcodeExcerpt' => '0',

										'version' => $this->pluginData["Version"]
									);	
		
		$this->plugin->install();
		
		foreach($updatedValues as $key => $value){
			$this->assertEquals($value, get_option("fergcorp_countdownTimer_".$key));
		}
		
		$this->assertEquals(get_option("fergcorp_countdownTimer_timeFormat"), "F jS, Y, g:i a");
		
		$updatedOneTimeEvent = get_option("fergcorp_countdownTimer_oneTimeEvent");
		
		for($i=0; $i < count($oneTimeEvent); $i++){
			$this->assertEquals($oneTimeEvent[$i]["date"], $updatedOneTimeEvent[$i]->getTimestamp());
			$this->assertEquals($oneTimeEvent[$i]["text"], $updatedOneTimeEvent[$i]->getTitle());
			$this->assertEquals($oneTimeEvent[$i]["timeSince"], $updatedOneTimeEvent[$i]->getTimeSince());
			$this->assertEquals($oneTimeEvent[$i]["link"], $updatedOneTimeEvent[$i]->getURL());
		}		
		
	}

	/**
	 * @group install
	 * @covers Fergcorp_Countdown_Timer::install_option
	 */
	public function test_install_option(){
		$prefix = "test";
		$option = "option";
		$default = "default";

		$this->assertTrue($this->plugin->install_option($prefix, $option, $default));
		$this->assertFalse($this->plugin->install_option($prefix, $option, $default));
		$this->assertEquals(get_option($prefix.$option), $default);
	}

}
       /**
        * @group TZ
	    * @group admin
        */
class TZ_Admin_Fergcorp_Countdown_Timer_Test extends Test_Admin_Fergcorp_Countdown_Timer{
	public $timezone_string;

    public function setUp() {
    	$this->timezone_string = 'America/Denver';
        parent::setUp();
    }

}

       /**
        * @group GMT
	    * @group admin
        */
class GMT_Admin_Fergcorp_Countdown_Timer_Test extends Test_Admin_Fergcorp_Countdown_Timer{
	public $timezone_string;

    public function setUp() {
    	$this->timezone_string = 'UTC-2';
        parent::setUp();
    }

}


class HTML_Events_Test_Admin_Fergcorp_Countdown_Timer extends HTML_Test_Admin_Fergcorp_Countdown_Timer{

	public function setUp() {
		parent::setUp();
		$event_object_array = array();
		for($i = -5; $i < 5; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		update_option('fergcorp_countdownTimer_deleteOneTimeEvents', '1');
		update_option('timezone_string', "America/Denver");
		$this->plugin->loadSettings();
    }

}

class HTML_Test_Admin_Fergcorp_Countdown_Timer extends WP_UnitTestCase{
	public $plugin_slug = 'countdown_timer';
	public $plugin;

	public 	$docType = "XHTML 1.0 Transitional";
	public 	$htmlStart = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title>I AM YOUR DOCUMENT TITLE REPLACE ME</title><meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" /><meta http-equiv="Content-Style-Type" content="text/css" /></head><body><div>';
	public $htmlEnd = "</div></body></html>";

	public function setUp() {
	parent::setUp();
		define('WP_ADMIN', true);
		$user1_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user1_id );

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
		$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();

		$this->plugin->__construct();

	}


	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);

	}

	public function w3cValidate(){

	}

	/**
	 * @group w3c
	 * @group admin
	 * @group settingsPage
	 * @covers Fergcorp_Countdown_Timer::settings_page
	 */
	public function test_settings_page(){

		ob_start();

			$this->plugin->settings_page();

		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);

	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @gropu meta_box
	 * @covers Fergcorp_Countdown_Timer::display_options_meta_box
	 */
	public function test_display_options_meta_box(){

		ob_start();
  		$this->plugin->display_options_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::events_meta_box
	 */
	public function test_events_meta_box(){
		ob_start();
  		$this->plugin->events_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::installation_meta_box
	 */
	public function test_installation_meta_box(){
		ob_start();
  		$this->plugin->installation_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::management_meta_box
	 */
	public function test_management_meta_box(){
		ob_start();
  		$this->plugin->management_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::onHover_time_format_meta_box
	 */
	public function test_onHover_time_format_meta_box(){
		ob_start();
  		$this->plugin->onHover_time_format_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::display_format_options_meta_Box
	 */
	public function test_display_format_options_meta_box(){
		ob_start();
  		$this->plugin->display_format_options_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
	/**
	 * @group w3c
	 * @group admin
	 * @group settings_page
	 * @group meta_box
	 * @covers Fergcorp_Countdown_Timer::example_display_meta_box
	 */
	public function test_example_display_meta_box(){
		ob_start();
  		$this->plugin->example_display_meta_box();
		$buffer = ob_get_clean();

		require_once 'Services/W3C/HTMLValidator.php';

		$v = new Services_W3C_HTMLValidator();
		$v->setOptions(array("doctype" => $docType));
		$r = $v->validateFragment($this->htmlStart.$buffer.$this->htmlEnd);
		//print $this->htmlStart.$buffer.$this->htmlEnd;
		//var_dump($r);

		$this->assertTrue($r->isValid(), $this->htmlStart.$buffer.$this->htmlEnd);
		sleep(1);
	}
}

class Test_Admin_Fergcorp_Countdown_Timer extends WP_UnitTestCase {
	public $plugin_slug = 'countdown_timer';
	public $timezone_string;
	private $plugin;

	public 	$docType = "XHTML 1.0 Transitional";
	public 	$htmlStart = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title>I AM YOUR DOCUMENT TITLE REPLACE ME</title><meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" /><meta http-equiv="Content-Style-Type" content="text/css" /></head><body><div>';
	public $htmlEnd = "</div></body></html>";

	public function setUp() {
        parent::setUp();
		
		update_option('timezone_string', $this->timezone_string);
		
		define('WP_ADMIN', true);
		$user1_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user1_id );
		
		

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        $this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();

		$this->plugin->__construct();

    }


	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);

	}

	public function w3cValidate(){

	}

	/**
	 * @group admin
	 * @group script
	 * @coverts Fergcorp_Countdown_Timer::print_admin_script
	 */
	public function test_print_admin_script(){
		$this->assertTrue(is_admin());
		$this->plugin->print_admin_script();
		$this->assertTrue(wp_script_is("postbox", "enqueued"));

	}

	//http://stackoverflow.com/questions/1019076/how-to-search-by-key-value-in-a-multidimensional-array-in-php
	function searchNestedArray(array $array, $search, $mode = 'value') {

		    foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $value) {
		        if ($search === ${${"mode"}})
		            return true;
		    }
		    return false;
		}


	/**
	 * @group admin
	 * @coverts Fergcorp_Countdown_Timer::register_settings_page
	 */
	public function test_register_settings_page(){
		$this->plugin->register_settings_page();
		$options = $GLOBALS["submenu"];

		$this->assertEquals($options["options-general.php"][0][0], "Countdown Timer"); //$menu_title
		$this->assertEquals($options["options-general.php"][0][1], "manage_options"); //$capability
		$this->assertEquals($options["options-general.php"][0][2], "fergcorp_countdownTimer.php"); //$menu_slug
		$this->assertEquals($options["options-general.php"][0][3], "Countdown Timer Settings"); //$page_title

		//has_filter($tag, $function_to_check = false)
		//var_dump($GLOBALS["wp_filter"]["admin_print_scripts-admin_page_fergcorp_countdownTimer"]);

		$haystack = $GLOBALS["wp_filter"]["admin_print_scripts-admin_page_fergcorp_countdownTimer"];

		//var_dump($haystack);

		$this->assertTrue($this->searchNestedArray($haystack, "print_admin_script"));
		$this->assertTrue($this->searchNestedArray($haystack, "print_countdown_scripts"));
	}

	/**
	 * @group admin
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::sanitize
	 * @covers Fergcorp_Countdown_Timer::compare
	 */
	public function test_sanitize(){
		//Setup
		$input = array(
						array(	"date" => "3 years",
								"text" => "Also Fourth",
								"link" => "http://google.com",
								),
						array(	"date" => "3 months ago",
								"text" => "First",
								"link" => "",
								"timeSince" => "1",
								),
						array(	"date" => "yesterday",
								"text" => "Second",
								"link" => "",
								),
						array(	"date" => "tomorrow",
								"text" => "Third",
								"link" => "",
								"timeSince" => "1",
								),
						array(	"date" => "3 years",
								"text" => "Fourth",
								"link" => "http://google.com",
								),
					);

		//Execute
		$is["notz"] = $this->plugin->sanitize($input);

		//Verify
		$should = 	array(
						array(	"title" => $input[1]["text"],
								"time" => strtotime($input[1]["date"]),
								"url" => $input[1]["link"],
								"timeSince" => $input[1]["timeSince"],
								),
						array(	"title" => $input[2]["text"],
								"time" => strtotime($input[2]["date"]),
								"url" => $input[2]["link"],
								"timeSince" => $input[2]["timeSince"],
								),
						array(	"title" => $input[3]["text"],
								"time" => strtotime($input[3]["date"]),
								"url" => $input[3]["link"],
								"timeSince" => $input[3]["timeSince"],
								),
						array(	"title" => $input[4]["text"],
								"time" => strtotime($input[4]["date"]),
								"url" => $input[4]["link"],
								"timeSince" => $input[4]["timeSince"],
								),
						array(	"title" => $input[0]["text"],
								"time" => strtotime($input[0]["date"]),
								"url" => $input[0]["link"],
								"timeSince" => $input[0]["timeSince"],
								),

					);

		for($i=0; $i<5; $i++){
			$this->assertEquals($should[$i]["title"], $is["notz"][$i]->getTitle());
			$this->assertEquals($should[$i]["time"], $is["notz"][$i]->getTime());
			$this->assertEquals($should[$i]["url"], $is["notz"][$i]->getURL());
			$this->assertEquals($should[$i]["timeSince"], $is["notz"][$i]->getTimeSince());
		}



	}

	/**
	 * @group admin
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::build_input
	 */
	public function test_build_input(){
		$this->expectOutputRegex("/^ <input(.*?)type=\"checkbox\" name=\"fergcorp_countdownTimer_oneTimeEvent\[1\]\[timeSince\]\" value=\"1\"(.*?)checked=\'checked\' \/>$/");
		echo $this->plugin->build_input(array(
												"type" => "checkbox",
												"name" => "fergcorp_countdownTimer_oneTimeEvent[1][timeSince]",
												"value" => 1,
												),
											checked("1", "1", false)
										);
	}

	/**
	 * @group admin
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::build_yes_no
	 */
	public function test_build_yes_no(){

		$this->assertRegExp("/^ <input(.*?)name=\"name\"(.*?)checked=\'checked\' \/>(.*?)<input(.*?)name=\"name\"(.*?) \/>No$/", $this->plugin->build_yes_no("name", "1"));
		$this->assertRegExp("/^ <input(.*?)name=\"name\"(.*?) \/>(.*?)<input(.*?)name=\"name\"(.*?)checked=\'checked\' \/>No$/", $this->plugin->build_yes_no("name", "0"));
	}

	/**
	 * @group admin
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::register_settings
	 */
	public function test_register_settings(){
		$this->plugin->register_settings();

		$settings = array(	"fergcorp_countdownTimer_deleteOneTimeEvents",
							"fergcorp_countdownTimer_deleteOneTimeEvents",
							"fergcorp_countdownTimer_timeFormat",
							"fergcorp_countdownTimer_showYear",
							"fergcorp_countdownTimer_showMonth",
							"fergcorp_countdownTimer_showWeek",
							"fergcorp_countdownTimer_showDay",
							"fergcorp_countdownTimer_showHour",
							"fergcorp_countdownTimer_showMinute",
							"fergcorp_countdownTimer_showSecond",
							"fergcorp_countdownTimer_stripZero",
							"fergcorp_countdownTimer_enableJS",
							"fergcorp_countdownTimer_timeSinceTime",
							"fergcorp_countdownTimer_titleSuffix",
							"fergcorp_countdownTimer_enableShortcodeExcerpt",
							"fergcorp_countdownTimer_oneTimeEvent",
						);

		foreach($settings as $setting){
			$this->assertContains($setting, $GLOBALS["new_whitelist_options"]["fergcorp_countdownTimer_options"]);
		}
	}
}

       /**
        * @group TZ
	    * @group admin
        */
class TZ_CountdownTimer_Test extends CountdownTimerTest{
	public $timezone_string;

    public function setUp() {
    	$this->timezone_string = 'America/Denver';
        parent::setUp();
    }

}

       /**
        * @group GMT
	    * @group admin
        */
class GMT_CountdownTimer_Test extends CountdownTimerTest{
	public $timezone_string;

    public function setUp() {
    	$this->timezone_string = 'UTC-2';
        parent::setUp();
    }

}


/**
 * Countdown Timer Tests
 */

class CountdownTimerTest extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
    public $timezone_string;
	private $plugin;

	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 *
	*/

    public function setUp() {
        parent::setUp();
        
        update_option('timezone_string', $this->timezone_string);

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        $this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();

		$this->plugin->__construct();
    }

	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);

	}
	/**
	 * @group guest
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::__construct
	 */
	public function test_construct(){
		$this->tearDown();

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        		$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();

		$pluginData = get_plugin_data("fergcorp_countdownTimer.php");
		//global $fergcorp_countdownTimer_init;

		$defaultInstallValues = array(
		'deleteOneTimeEvents' => '0',
		'timeFormat' => 'F jS, Y, g:i a',
		'showYear' => '1',
		'showMonth' => '1',
		'showWeek' => '0',
		'showDay' => '1',
		'showHour' => '1',
		'showMinute' => '1',
		'showSecond' => '0',
		'stripZero' => '1',
		'enableJS' => '1',
		'timeSinceTime' => '0',
		'titleSuffix' => ':<br />',
		'enableShortcodeExcerpt' => '0',
		'oneTimeEvent' => '0',
		'version' => $pluginData["Version"]
		);

		//First we need to create a ReflectionClass object
		//passing in the class name as a variable
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");

		$props = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);

		foreach ($props as $prop) {
			$prop->setAccessible(true);
			//echo $prop->getName() . ": " . $prop->getValue($this->plugin) ."\n";
			if(key_exists($prop->getName(), $defaultInstallValues)){
				$this->assertEquals($defaultInstallValues[$prop->getName()], $prop->getValue($this->plugin), "Failed to match: ".$prop->getName());
			}
		}

		$this->assertTrue( (bool) has_action("admin_init", array( &$this->plugin, 'install' )));
		$this->assertTrue( (bool) has_action("admin_init", array( &$this->plugin, 'loadSettings' )));

		$this->assertTrue(wp_script_is("webkit_sprintf", "registered"));
		$this->assertTrue(wp_script_is("fergcorp_countdowntimer", "registered"));

		$this->assertTrue( (bool) has_action('wp_footer', array ( &$this->plugin, 'json' ) ) );


		$this->assertFalse( (bool) has_filter('the_excerpt', 'do_shortcode') );


		$this->assertTrue( (bool) has_action('wp_head', array( &$this->plugin, 'print_countdown_scripts' ), 1) );

		$this->assertTrue( (bool) has_action('admin_init', array( &$this->plugin, 'register_settings' ) ) );			//Initialized the options
		$this->assertTrue( (bool) has_action('admin_menu', array( &$this->plugin, 'register_settings_page' ) ) );	//Add Action for adding the options page to admin panel

		$this->assertArrayHasKey("fergcorp_cdt_single", $GLOBALS["shortcode_tags"]);
		$this->assertArrayHasKey("fergcorp_cdt", $GLOBALS["shortcode_tags"]);

		//Update value
		update_option("fergcorp_countdownTimer_enableShortcodeExcerpt", 1);

		//Tear down the class
		$this->tearDown();

		//Partial reset
		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        		$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();


		$defaultInstallValues = array('enableShortcodeExcerpt' => '1',);

		//First we need to create a ReflectionClass object
		//passing in the class name as a variable
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");

		$props = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);

		foreach ($props as $prop) {
			$prop->setAccessible(true);
			//echo $prop->getName() . ": " . $prop->getValue($this->plugin) ."\n";
			if(key_exists($prop->getName(), $defaultInstallValues)){
				$this->assertEquals($defaultInstallValues[$prop->getName()], $prop->getValue($this->plugin), "Failed to match: ".$prop->getName());
			}
		}

		//@TODO: Need to test this
		//$plugin = plugin_basename("countdown-timer/fergcorp_countdownTimer.php");
		//var_dump($plugin);
		//var_dump($GLOBALS["wp_filter"]);
		//$this->assertTrue( (bool) has_filter("plugin_action_links_$plugin", array( &$this->plugin, 'settings_link' ) ) );

	}
	/**
	 * @group guest
	 * @group settings_page
	 * @covers Fergcorp_Countdown_Timer::loadSettings
	 */
	public function test_loadSettings(){

		$defaultInstallValues = array(
		'deleteOneTimeEvents',
		'timeFormat',
		'showYear',
		'showMonth',
		'showWeek',
		'showDay',
		'showHour',
		'showMinute',
		'showSecond',
		'stripZero',
		'enableJS',
		'timeSinceTime',
		'titleSuffix',
		'enableShortcodeExcerpt',
		'version');

		//First we need to create a ReflectionClass object
		//passing in the class name as a variable
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");

		//Clear the values, since they're initiated in the setup
		$props   = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			if(key_exists($prop->getName(), $defaultInstallValues)){
				$prop->setValue($this->plugin, NULL);
			}
		}

		//Reload the settings
		$this->plugin->loadSettings();

		$loaded_settings = array();

		//Test to make sure they were loaded correctly
		$props   = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			$loaded_settings[$prop->getName()] = $prop->getValue($this->plugin);
		}

		foreach($defaultInstallValues as $value){
			$this->assertEquals(get_option("fergcorp_countdownTimer_{$value}"), $loaded_settings[$value], "Failed to match $value: " . get_option("fergcorp_countdownTimer_{$value}") . " = " . $loaded_settings[$value]);
		}

		$this->assertEquals(get_option("fergcorp_countdownTimer_oneTimeEvent"), $loaded_settings["eventList"]);

	}

	/**
	 * @group guest
	 * @group script
	 * @covers Fergcorp_Countdown_Timer::print_countdown_scripts
	 */
	public function test_print_countdown_scripts_true(){
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");
		$prop = $reflection_class->getProperty("enableJS");
		$prop->setAccessible(true);
		$this->assertTrue((bool) $prop->getValue($this->plugin));

		$this->plugin->print_countdown_scripts();

		$this->assertTrue(wp_script_is("fergcorp_countdowntimer", "enqueued"));
		$this->assertTrue(wp_script_is("webkit_sprintf", "enqueued"));
	}
	/**
	 * @group guest
	 * @group script
	 * @covers Fergcorp_Countdown_Timer::print_countdown_scripts
	 */
	public function test_print_countdown_scripts_false(){
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");
		$prop = $reflection_class->getProperty("enableJS");
		$prop->setAccessible(true);
		$prop->setValue($this->plugin, FALSE);

		$this->assertFalse((bool) $prop->getValue($this->plugin));

		$this->plugin->print_countdown_scripts();

		$this->assertFalse(wp_script_is("fergcorp_countdowntimer", "enqueued"));
		$this->assertFalse(wp_script_is("webkit_sprintf", "enqueued"));
	}



	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::singleTimer
	 */
	public function test_singleTimer(){
		$date = "+31 days";
		$calcDate = gmdate("F jS, Y, g:i a", strtotime($date)+(3600*(get_option('gmt_offset'))));
		$this->assertRegExp("/<abbr title = \"$calcDate\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">1 month,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">0 days,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>/is", $this->plugin->singleTimer($date));
	}

	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_noEvents(){

		$this->assertRegExp("/^<li>No dates present<\/li>$/is", $this->plugin->showTimer(-1, FALSE));
	}

	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_all(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->assertRegExp("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>/is", $this->plugin->showTimer(-1, FALSE));
	}

	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_3(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>/is");
		print $this->plugin->showTimer(3, FALSE);
	}

	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_1(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>/is");
		print $this->plugin->showTimer(1, FALSE);
	}

	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::formatEvent
	 */
	public function test_formatEvent(){

		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Tomorrow<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1day" ), "Tomorrow", "http://www.example.com") , TRUE ));

		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Yesterday<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span> ago<\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "-1day" ), "Yesterday", "http://www.example.com" ) , TRUE ));

		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1 day" ) ) , FALSE ));

		$this->assertNull($this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "yesterday" ) ) , FALSE ));

		update_option("fergcorp_countdownTimer_timeFormat", "");
		update_option("fergcorp_countdownTimer_timeSinceTime", -99999);
		$this->plugin->loadSettings();
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1day" ), "", "", 1 ) , FALSE ));

		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Yesterday<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span> ago<\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "-1day" ), "Yesterday", "http://www.example.com", 1 ) , TRUE ));
	}

function check_this($time, $unitList, $s, $now){

				$nowObj = new DateTime;
				date_timestamp_set($nowObj, $now);

				$diff = date_diff($time, $nowObj);

				$secondsDiff = $time->getTimestamp() - $now;

				$Year =		$s[0].intval($secondsDiff/31536000)." ".$s[1]."s?,".$s[14]." ";
				$Month =	$s[2].intval($secondsDiff/2628000)." ".$s[3]."s?,".$s[14]." ";
				$Week =		$s[4].intval($secondsDiff/2628000)." ".$s[5]."s?,".$s[14]." ";
				$Day = 		$s[6].intval($secondsDiff/86400)." ".$s[7]."s?,".$s[14]." ";
				$Hour =		$s[8].intval($secondsDiff/3600)." ".$s[9]."s?,".$s[14]." ";
				$Minute =	$s[10].intval($secondsDiff/60)." ".	$s[11]."s?,".$s[14]." ";
				$Second = 	$s[12].$secondsDiff." ".		$s[13]."(s)?,".$s[14]." ";

				$secondFudge = $diff->s;	//"(".($diff->s-1)."|".$diff->s."|".($diff->s+1).")"; //Test takes too long!

				if(get_option("fergcorp_countdownTimer_showYear")){
					$Year =		$s[0].$diff->y." ".$s[1]."(s)?,".$s[14]." ";
					$Month =	$s[2].$diff->m." ".$s[3]."s?,".$s[14]." ";
					$Day = 		$s[6].$diff->d." ".$s[7]."s?,".$s[14]." ";
					$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
					$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
				}
				elseif(get_option("fergcorp_countdownTimer_showMonth")){
					$secondsDiff = $secondsDiff - ($diff->m*2628000);
					$Day = 		$s[6].$diff->d." ".$s[7]."s?,".$s[14]." ";
					$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
					$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
				}
				elseif(get_option("fergcorp_countdownTimer_showWeek")){
					$Week = 	$s[4].intval($diff->days/7)." ".$s[5]."s?,".$s[14]." ";
					$Day = 		$s[6].($diff->d%7)." ".$s[7]."s?,".$s[14]." ";
					$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
					$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
				}
				elseif(get_option("fergcorp_countdownTimer_showDay")){
					$secondsDiff = $secondsDiff - ($diff->d*86400);
					$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
					$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
				}
				elseif(get_option("fergcorp_countdownTimer_showHour")){
					$secondsDiff = $secondsDiff - ($diff->h*3600);
					$Minute =	$s[10].$diff->i." ".	$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondFudge." ".		$s[13]."s?,".$s[14]." ";
				}
				elseif(get_option("fergcorp_countdownTimer_showMinute")){
					$secondsDiff = $secondsDiff - ($diff->s*60);
					$Second = 	$s[12].$secondFudge." ".		$s[13]."s?,".$s[14]." ";
				}
				foreach($unitList as $unit){
					if(get_option("fergcorp_countdownTimer_show{$unit}")){
						$unitValue[$unit] .= ${$unit};
					}
				}

				$testOfUnits = "";
				foreach($unitList as $unit){
					if(get_option("fergcorp_countdownTimer_show{$unit}")){
						$testOfUnits .= ${$unit};
					}
				}
				//print $testOfUnits."\n\n";
				return $testOfUnits;
			}

	/**
	 * @group guest
	 * @group long
	 * @covers Fergcorp_Countdown_Timer::fuzzyDate
	 */
	public function test_fuzzyDate(){
		$now = time();

				$s = array(		"<span class=\"fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit\">",
								"year",
								"<span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">",
								"month",
								"<span class=\"fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit\">",
								"week",
								"<span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">",
								"day",
								"<span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">",
								"hour",
								"<span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">",
								"minute",
								"<span class=\"fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit\">",
								"second",
								"<\/span>",
		);

		//Setup massive database of events!!!!
		$unitList = array(	"Year",
							"Month",
							//"Week",
							"Day",
							"Hour",
							"Minute",
							"Second",
		);

		$unitList_reverse = array_reverse($unitList);

		foreach($unitList as $unit){
				update_option("fergcorp_countdownTimer_show{$unit}", 0);
			}

		for($u=count($unitList)-1; $u >= 0; $u--){
			update_option("fergcorp_countdownTimer_show{$unitList[$u]}", 1);
			$this->plugin->loadSettings();
			$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");

			//Years
			for($i = 10; $i > 2; $i--){
				$time->sub(new DateInterval("P1Y"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now), "Date attempted is: " . date("r", $time->getTimestamp()));
			}

			for($i = 1; $i < 12; $i++){
				$time->sub(new DateInterval("P1M"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now), "Time: ".var_export($time->getTimestamp(), true)."\n unitList: ".var_export($unitList, true)." \n s: ".var_export($s, true)." \n now: $now");
			}

			for($i = 1; $i < date("t", $time->getTimestamp()); $i++){
				$time->sub(new DateInterval("P1D"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			}

			for($i = 0; $i < 25; $i++){
				$time->sub(new DateInterval("PT1H"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			}

			for($i = 0; $i < 59; $i++){
				$time->sub(new DateInterval("PT1M"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			}

			for($i = 0; $i < 59; $i++){
				$time->sub(new DateInterval("PT1S"));
				$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			}

			}

		//some special cases
		$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");
		$now = time();
		//No month, with year, and every thing else
		update_option("fergcorp_countdownTimer_showMonth", 0);
		$this->plugin->loadSettings();
		//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
		$this->assertRegExp("/^<span class=\"fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit\">11 year(s)?,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">283 days?,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">5 hours?,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">3 minutes?,<\/span> <span class=\"fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit\">(1|2|3) seconds?<\/span>/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));


		//Weeks
				$unitList = array(	"Year",
							"Month",
							"Week",
							"Day",
							"Hour",
							"Minute",
							"Second",
		);
		update_option("fergcorp_countdownTimer_showYear", 0);
		update_option("fergcorp_countdownTimer_showMonth", 0);
		update_option("fergcorp_countdownTimer_showWeek", 1);
		$this->plugin->loadSettings();
		//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
		$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));


		//$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");
		//$now = time();
		//Nothing selected
		update_option("fergcorp_countdownTimer_showYear", 0);
		update_option("fergcorp_countdownTimer_showMonth", 0);
		update_option("fergcorp_countdownTimer_showWeek", 0);
		update_option("fergcorp_countdownTimer_showDay", 0);
		update_option("fergcorp_countdownTimer_showHour", 0);
		update_option("fergcorp_countdownTimer_showMinute", 0);
		update_option("fergcorp_countdownTimer_showSecond", 0);

		$this->plugin->loadSettings();
		//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
		$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));



	}
	/**
	 * @group guest
	 * @group shortcode
	 * @covers Fergcorp_Countdown_Timer::shortcode_showTimer
	 */
	public function test_shortcode_showTimer(){
		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", ($i%2) ) );
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();

		$atts = array("max" => "-1", "bar"=>"not");
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));

		$atts = array("max" => "1", "bar"=>"not");
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));

		$atts = array("max" => "3", "bar"=>"not");
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));

		$atts = NULL;
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));


	}
	/**
	 * @group guest
	 * @group shortcode
	 * @covers Fergcorp_Countdown_Timer::shortcode_singleTimer
	 */
	public function test_shortcode_singleTimer(){
		$date = "+2 months";
		$calcDate = gmdate("F jS, Y, g:i a", strtotime($date)+(3600*(get_option('gmt_offset'))));
		$atts = array("date" => $date);

		$this->assertRegExp("/^<abbr title = \"$calcDate\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">2 months,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">0 days,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>$/is", $this->plugin->shortcode_singleTimer($atts));
	}


	/**
	 * @group guest
	 * @group display
	 * @group script
	 * @covers Fergcorp_Countdown_Timer::json
	 */
	public function test_json(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}
		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->plugin->showtimer(-1, FALSE);
		$this->plugin->json();
		$this->assertRegExp("/^var fergcorp_countdown_timer_js_lang = {\"year\":\"%d year,\",\"years\":\"%d years,\",\"month\":\"%d month,\",\"months\":\"%d months,\",\"week\":\"%d week,\",\"weeks\":\"%d weeks,\",\"day\":\"%d day,\",\"days\":\"%d days,\",\"hour\":\"%d hour,\",\"hours\":\"%d hours,\",\"minute\":\"%d minute,\",\"minutes\":\"%d minutes,\",\"second\":\"%d second,\",\"seconds\":\"%d seconds,\",\"agotime\":\"%s ago\",\"intime\":\"in %s\"};\nvar fergcorp_countdown_timer_jsEvents = {\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\"};\nvar fergcorp_countdown_timer_options = {\"showYear\":\"1\",\"showMonth\":\"1\",\"showWeek\":\"0\",\"showDay\":\"1\",\"showHour\":\"1\",\"showMinute\":\"1\",\"showSecond\":\"0\",\"stripZero\":\"1\"};$/is", $GLOBALS["wp_scripts"]->print_scripts_l10n("fergcorp_countdowntimer", FALSE));
	}
}

class Countdown_Timer_Event_Test extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
	private $plugin;

	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 *
	*/

    public function setUp() {
        parent::setUp();

		$date = "31 January 2015"; //1422662400

		$GLOBALS['fergcorp_countdown_timer_event'] = new Fergcorp_Countdown_Timer_Event( strtotime( $date ), "myTitle", "http://google.com",  true);
        $this->plugin = $GLOBALS['fergcorp_countdown_timer_event'];

		//$this->plugin->__construct();
    }

	public function testTrue(){
		$this->assertTrue(true);
	}

	public function test_construct(){
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");

		$values = array("title" => "/^myTitle$/",
						"time" => "/^1422662400$/",
						"url" => "/^http\:\/\/google\.com$/",
						"timeSince" => "/^1$/",
						"UID" => "/^x[0-9a-z]{32}$/"
						);


		$props = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);

		foreach ($props as $prop) {
			$prop->setAccessible(true);
			//echo $prop->getName() . ": " . $prop->getValue($this->plugin) ."\n";
			if(key_exists($prop->getName(), $values)){
				$this->assertRegExp($values[$prop->getName()], (string) $prop->getValue($this->plugin), "Failed to match: ".$prop->getName());
			}
		}

	}

	public function test_getTimestamp() {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("time");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getTimestamp(), $prop->getValue($this->plugin));
    }

	public function test_setTitle () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("title");
		$prop->setAccessible(true);

		$current_value = $prop->getValue($this->plugin);
		$this->plugin->setTitle("new title");

		$this->assertNotEquals("new title", $current_value);
		$this->assertEquals("new title", $prop->getValue($this->plugin));

	}

	public function test_setTime () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("time");
		$prop->setAccessible(true);

		$current_value = $prop->getValue($this->plugin);
		$this->plugin->setTime(123456789);

		$this->assertNotEquals(123456789, $current_value);
		$this->assertEquals(123456789, $prop->getValue($this->plugin));
	}

	public function test_setURL () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("url");
		$prop->setAccessible(true);

		$current_value = $prop->getValue($this->plugin);
		$newValue = "http://example.com";
		$this->plugin->setURL($newValue);

		$this->assertNotEquals($newValue, $current_value);
		$this->assertEquals($newValue, $prop->getValue($this->plugin));
	}

	public function test_setTimeSince ( ) {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("timeSince");
		$prop->setAccessible(true);

		$current_value = $prop->getValue($this->plugin);
		$newValue = "0";
		$this->plugin->setTimeSince($newValue);

		$this->assertNotEquals($newValue, $current_value);
		$this->assertEquals($newValue, $prop->getValue($this->plugin));
	}

	public function test_getTitle () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("title");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getTitle(), $prop->getValue($this->plugin));
	}

	public function test_getTime () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("time");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getTime(), $prop->getValue($this->plugin));
	}

	public function test_getURL () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("url");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getURL(), $prop->getValue($this->plugin));
	}

	public function test_getTimeSince () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("timeSince");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getTimeSince(), $prop->getValue($this->plugin));
	}

	public function test_getUID () {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("UID");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->getUID(), $prop->getValue($this->plugin));
	}

	public function  test_date ( ) {
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		$prop = $reflection_class->getProperty("time");
		$prop->setAccessible(true);
		$this->assertEquals($this->plugin->date("U"), $prop->getValue($this->plugin));
	}
}

class Countdown_Timer_Widget_Test extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
	private $plugin;

	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 *
	*/

    public function setUp() {
        parent::setUp();

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        $this->fergcorp_countdownTimer_init = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->fergcorp_countdownTimer_init->install();
		$this->fergcorp_countdownTimer_init->__construct();

		$date = "31 January 2015"; //1422662400

		$GLOBALS['fergcorp_countdown_timer_widget'] = new Fergcorp_Countdown_Timer_Widget();
        $this->plugin = $GLOBALS['fergcorp_countdown_timer_widget'];

    }

	public function testTrue(){
		$this->assertTrue(true);
	}

	public function test_construct(){

		$this->assertEquals("fergcorp_countdown_timer_widget", $this->plugin->id_base);
		$this->assertEquals("Countdown Timer", $this->plugin->name);
		$this->assertEquals("widget_fergcorp_countdown_timer_widget", $this->plugin->widget_options["classname"]);
		$this->assertEquals("Adds the Countdown Timer", $this->plugin->widget_options["description"]);
		$this->assertEquals("fergcorp_countdown_timer_widget", $this->plugin->control_options["id_base"]);


	}

	public function test_form_1(){
		$this->expectOutputRegex("/<label(.*?)>(.*?)<\/label>(.*?)<input(.*?)\/>(.*?)<label(.*?)>(.*?)<\/label>(.*?)<input(.*?)\/>(.*?)<small><strong>(.*?)<\/strong>(.*?)<\/small>/is");
		echo $this->plugin->form( $instance );

	}

	public function test_form_2(){
		$instance = array(	"title" => "New Title",
								"countLimit" => "5",

		);
		$this->expectOutputRegex("/<label(.*?)>(.*?)<\/label>(.*?)<input(.*?)\/>(.*?)<label(.*?)>(.*?)<\/label>(.*?)<input(.*?)\/>(.*?)<small><strong>(.*?)<\/strong>(.*?)<\/small>/is");
		echo $this->plugin->form( $instance );

	}

	public function test_update(){

		$new_instance = array(	"title" => "New Title",
								"countLimit" => "5",

		);

		$old_instance = array();

		$update = $this->plugin->update($new_instance, $old_instance );

		$this->assertCount(2, $update);
		$this->assertEquals("New Title", $update["title"] );
		$this->assertEquals(5, $update["countLimit"] );

	}

	public function test_widget_1( ){
		$args = array(
					'name' => 'Main Sidebar',
					'id' => 'sidebar-1',
					'description' => '',
					'class' => '',
					'before_widget' => '<aside id="fergcorp_countdown_timer_widget-2" class="widget widget_fergcorp_countdown_timer_widget">',
					'after_widget' => '</aside>',
					'before_title' => '<h3 class="widget-title">',
					'after_title' => '</h3>',
					'widget_id' => 'fergcorp_countdown_timer_widget-2',
					'widget_name' => 'Countdown Timer',
					);
		$this->expectOutputRegex("/^<aside(.*?)><ul>(.*?)<\/ul><\/aside>$/is");
		print $this->plugin->widget($args, $instance );
		//<aside id="fergcorp_countdown_timer_widget-2" class="widget widget_fergcorp_countdown_timer_widget"><ul><li>No dates present</li></ul></aside>

	}
	public function test_widget_2( ){
		$args = array(
					'name' => 'Main Sidebar',
					'id' => 'sidebar-1',
					'description' => '',
					'class' => '',
					'before_widget' => '<aside id="fergcorp_countdown_timer_widget-2" class="widget widget_fergcorp_countdown_timer_widget">',
					'after_widget' => '</aside>',
					'before_title' => '<h3 class="widget-title">',
					'after_title' => '</h3>',
					'widget_id' => 'fergcorp_countdown_timer_widget-2',
					'widget_name' => 'Countdown Timer',
					);
		$instance = array(	"title" => "New Title",
							"countLimit" => "5",

		);
		$this->expectOutputRegex("/^<aside(.*?)><h3(.*?)>New Title<\/h3><ul>(.*?)<\/ul><\/aside>$/is");
		print $this->plugin->widget($args, $instance );
		//<aside id="fergcorp_countdown_timer_widget-2" class="widget widget_fergcorp_countdown_timer_widget"><h3 class="widget-title">New Title</h3><ul><li>No dates present</li></ul></aside>

	}

}


class DeltaTime_Test extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
	private $plugin;

	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 *
	*/

    public function setUp() {
        parent::setUp();

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        $this->fergcorp_countdownTimer_init = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->fergcorp_countdownTimer_init->install();
		$this->fergcorp_countdownTimer_init->__construct();

		$targetTime = mktime(01, 02, 03, 01, 31, 2015); //?1422662400
		$nowTime = mktime(12, 01, 02, 10, 19, 2012);

		$GLOBALS['fergcorp_deltatime'] = new Fergcorp_DeltaTime($targetTime, $nowTime);
        $this->plugin = $GLOBALS['fergcorp_deltatime'];

    }

	public function testTrue(){
		$this->assertTrue(true);
	}


	public function test_contruct(){


			$this->assertEquals(2012, $this->plugin->nowYear);
			$this->assertEquals(10, $this->plugin->nowMonth);
			$this->assertEquals(19, $this->plugin->nowDay);
			$this->assertEquals(12, $this->plugin->nowHour);
			$this->assertEquals(01, $this->plugin->nowMinute);
			$this->assertEquals(02, $this->plugin->nowSecond);

			$this->assertEquals(2015, $this->plugin->targetYear);
			$this->assertEquals(01, $this->plugin->targetMonth);
			$this->assertEquals(31, $this->plugin->targetDay);
			$this->assertEquals(01, $this->plugin->targetHour);
			$this->assertEquals(02, $this->plugin->targetMinute);
			$this->assertEquals(03, $this->plugin->targetSecond);

			$this->assertEquals(3, $this->plugin->y);
			$this->assertEquals(-9, $this->plugin->m);
			$this->assertEquals(12, $this->plugin->d);
			$this->assertEquals(-11, $this->plugin->h);
			$this->assertEquals(01, $this->plugin->i);
			$this->assertEquals(01, $this->plugin->s);

			$this->assertEquals(NULL, $this->plugin->w);

			$this->assertEquals(72018061, $this->plugin->delta);

	}

}
