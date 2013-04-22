=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international, i18n, countdown timer, wedding, localization, i18n
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 3.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to setup a series of dates to count to or from in terms of years, months, weeks, days, hours, minutes, and/or seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar using the widget, or within posts and pages using shortcodes.

Currently supports 22 languages.

== Translations ==

= Using another language =

You'll need to modify your wp-config.php file. To do this...

1. Open your `wp-config.php` file in a text editor and search for: `define ('WPLANG', '');`
1. Edit this line according to the language you want to use (and is available). For example, for Deutsch spoken in Germany, you must add: `('WPLANG', 'de_DE');`
1. Once you've added your language code, save the file.
1. Upload the modified wp-config.php file into the WordPress root directory.

Of course, you'll replace de_DE with the language extension that you want to use, unless of course you actually did want the German language translation. For more information, consult [Installing WordPress in Your Language](http://codex.wordpress.org/Installing_WordPress_in_Your_Language) on the WordPress Codex

= Languages Available =

 * Bosnian translation: bs_BA
 * Catalan (Spain) translation: ca_CA
 * Czech translation: cs_CZ
 * Danish translation: da_DK
 * German translation: de_DE
 * Spanish translation: es_ES
 * French translation: fr_FR
 * Hungarian translation: hu_HU
 * Italian translation: it_IT
 * Lithuanian translation: lt_LT
 * Latvian translation: lv_LV
 * Dutch translation: nl_NL
 * Norwegian translation: nn_NO
 * Polish translation: pl_PL
 * Portuguese [Brazil] translation: pr_BR
 * Romanian translation: ro_RO
 * Russian translation: ru_RU
 * Serbian [Cyrilic] translation: sr_RS
 * Swedish translation: sv_SE
 * Turkish translation: tr_TR
 * Vietnamese translation: vi_VI
 * Chinese translation: zh_CN

== Installation ==

1. Install the plugin using your preferred method of choice. Using the built-in WordPress installer is the preferred choice, but you can also do things the hard/manual way.

1. Activate the timer.

1. Events can be added and other settings modified in Settings > Countdown Timer in The Dashboard.

= Inserting countdown timers into your blog =

There are three places you can insert a countdown timer:

1. Sidebar (using the widget)
1. A post or page (using shortcodes)
1. PHP (experts only)

= Adding to the sidebar =

Add the widget to the sidebar by going to Appearances > Widget in The Dashboard.

= Adding to a post or page =

If you want to insert the Countdown Timer into a page or post, you can use the following shortcodes to return all or a limited number of Countdown Timers, respectively:
`[fergcorp_cdt]`
`[fergcorp_cdt max=##]`

Where _##_ is maximum number of results to be displayed, ordered by date.

If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:

`[fergcorp_cdt_single date="ENTER_DATE_HERE"]`

Example:
`Time until our wedding:
[fergcorp_cdt_single date="08 December 2012"]`

Where "ENTER_DATE_HERE" uses PHP's strtotime function and will parse about any English textual date/time description (such as "08 December 2012"). A complete list of valid formats can be found on PHP's [Supported Date and Time Format](http://www.php.net/manual/en/datetime.formats.php) page.

= PHP = 

Countdown Timer also provides a PHP function designed to be accessed publicly so you can include it in elements of your site that may not be in The Loop.
 
`fergcorp_countdownTimer(##)`

Where _##_ is the maximum number of events you wish to be displayed. If _$maxEvents_ is not given, the function will return all timers.

Events are automatically sorted by date of occurrence.

Note: You should also encapsulate calls with "function_exists" to prevent unintentional fatal errors if the plugin is deactivated.

= Limiting the number of countdown timers displayed =

If you're using the widget, there is an option to set the maximum number of timers shown. If you are using the PHP code, _$maxEvents_ is the maximum number of events you wish to be displayed.

Events are automatically sorted by date of occurrence.

= Changing the font, size, and style using CSS =

You can makes changes to the appearance of Countdown Timer display using CSS.

The following CSS classes are available:

 * `fergcorp_countdownTimer_event_li` styles each List Item, each item encompasses one countdown event
 * `fergcorp_countdownTimer_event_title` styles the title of the event
 * `fergcorp_countdownTimer_event_linkTitle` styles the title of an event if it is linked
 * `fergcorp_countdownTimer_event_time` styles the actual countdown timer
 * `fergcorp_countdownTimer_timeUnit` styles the units (e.g. "25 days")
 * `fergcorp_countdownTimer_year` styles the year text
 * `fergcorp_countdownTimer_month` styles the month text
 * `fergcorp_countdownTimer_week` styles the week text
 * `fergcorp_countdownTimer_day` styles the day text
 * `fergcorp_countdownTimer_hour` styles the hour text
 * `fergcorp_countdownTimer_minute` styles the minute text
 * `fergcorp_countdownTimer_second` styles the second text

== Frequently Asked Questions ==

= How do I prevent the timer from wrapping my countdown? =

Add the following to your CSS file: .fergcorp_countdownTimer_timeUnit {white-space: nowrap;}

= I have JavaScript countdown enabled and it works on the administration page in the Example Display, but not on my main site! =

This, unfortunately, is a problem with your theme, and not with Countdown Timer. Themes _must_ call wp_footer(), which is a standard hook for WordPress. Without it, many other plugins may not work properly either.

Your best bet to fix the problem is to modify the `footer.php` file and put `<?php wp_footer(); ?>` right before `</html>`. Then contact the person you designed the theme and let them know of their coding oversight.

= Your program is broken! The count down is off by XX days! =

Well, not quite. As it turns out, determining the number of months between two dates is harder than one might think. As you know, all months don't have the same number of days. Thus, some months have 31 days, others have 30 days, and then there's February. It's pretty trivial to figure out the number of complete months between two days (if complete months exist).

However, how many months exist between January 15 and February 20? There are 36 days, which is obviously more than the number of days in any given month we have, so the timer should display 1 month and how many days? Six days (30 days/month)? Five days (31 days/month)? Eight days (28 days/month since the date does end in February)?

I happened to mention my problem to a friend who said that the US military decided that there were 30 days in every month and to prorate the addition day (or less day(s)) for all the months that have more (or less) than 30 days.

= Wait, so how /do/ you count months? =

Using the above example of January 15 to February 20, there would be one month and five days. February 15 to March 20 would also be one month and five days. Why? January 15 to February 15 is one month. February 15 to February 20 is 5 days. Put them together and you get one month and five days.

= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Expand the Settings menu, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.

= There's a foreign (non-English) word that's wrong, what do I do? =

There are two ways to fix this. First, you can always contact me via email, blog comment, support forum, etc and let me know about the error. I don't usually issue bug fix updates just for language errors, but it will make it into the next update cycle.

Second, if you're handy with poEdit or something of the like, you can make the changes yourself and email me the .po and .mo files (although I really only need the .po file).

= How come there are long periods of time when you don't respond or update the plugin? =

I'm an engineer and have to retreat to my cave from time to time. Also, I do this for fun. That means work must come first (unless you want to pay me, then we can talk). Since I work during the week, you may only hear from me during the weekend.


== Screenshots ==
1. Administration Interface
2. Example on Blog

== Upgrade Notice ==
= 3.0.5 =
A couple of major bug fixes that deal resolve issues with using PHP < 5.3 and scope of object during activation 

== Changelog ==

= 3.0.5 =
Relase date: 1/19/2013

 * Bug fix: Eliminates Fatal error: Using $this when not in object context in...that prevented plugin from activating in certain circumstances
 * Bug fix: Added getTimestamp function needed for PHP < 5.3
 * Bug fix: Properly enclose "No dates present" in <li> tags

= 3.0.4 =
Release date: 1/12/2013

 * Bug fix: Least Common Unit will now display correctly
 * Bug fix: Solve issues with admin page not loading
 * Removed "cal_days_in_month" as everyone should be at the PHP level that supports this now
 * Wrote unit test suite

= 3.0.3 =
Release date: 7/3/2012

 * Bug fix: Shortcode for events in the past will always display
 * Bug fix: Don't delete events that have their getTimeSince flag set
 * Bug fix: Fixed expired timers that didn't show counting against limit of timers displayed
 * Other minor code cleanup

= 3.0.2 =
Release date: 6/20/2012

 * Bug fix: Settings are updated and loaded properly during both upgrade and activations, thanks to [Beee] and [bonzo01] for playing canary in the coalmine
 * Bug fix: JS display now respects time unit settings, thanks to [Beee] and [bonzo01] for pointing this out

= 3.0.1 =
Release date: 6/18/2012

 * Bug fix: Will now check to see if the plugin has been updated and update settings as required to avoid problems such as "Call to a member function date() on a non-object...". Thanks to [pixwell] and [bonzo01] for pointing this out; special thanks to fiancée for letting me spend some time to fix it.
 * Bug fix: Widget settings now properly moved from 2.4.3 to new format
 * Cosmetic: Spaces after input labels 

= 3.0 =
Release date: 6/15/2012

 * Complete code rewrite. Should provide better code readability for users who wish to contribute (yes, that's you if you're reading this)
 * Future dates respect future saving time events!
 * Removed ability to use comments tag to insert countdown timer into post,  you should use shortcode
 * On-hover date localization is now completed and will show the correct time/zone. Thanks to [pwesolek] for the patch
 * date_default_timezone_set will no longer throw an error if the timezone string is not set
 * Remove the display of the GMT offset in the date box. All data should be displayed in blog time.
 * JS now passed as JSON, and JS is loaded with the footer
 * Use JQuery to update
 * Updated language files, reduced to 69 strings now
 * Fixed Czech language files with correct plurals with thanks to [Prause] for the patch
 * Updated readme instructions for installation and usage
 * Added banner graphic for WordPress directory
 

= 2.4.3 =
Release Date: 3/10/2012

 * Fixed bug with missing `?>` at end of file
 * Added additional element to each unit of time to allow for better CSS customization. New CSS hooks include: .fergcorp_countdownTimer_timeUnit .fergcorp_countdownTimer_year .fergcorp_countdownTimer_month .fergcorp_countdownTimer_week .fergcorp_countdownTimer_day .fergcorp_countdownTimer_hour .fergcorp_countdownTimer_minute .fergcorp_countdownTimer_second
 * Added ability to prevent wrapping by adding following to your CSS file: .fergcorp_countdownTimer_timeUnit {white-space: nowrap;}
 * Removed unused "vars" and "r" variable



= 2.4.2 =
Release Date: 4/12/2010

 * Fixes bug where using fergcorp_countdownTimer with event limits would limit events incorrectly.
 * Updating ISO 639-2 code for Norway to "nn" from "no"
 * Updated all the translations using the POT file generated by WordPress.org because it supports parsing _n correctly
 * Updated all PO files with plural and locale information, this also fixes bug where translations weren't working 100% correctly

= 2.4.1 =
Release Date: 4/4/2010

 * Standalone mode prevents li-element from being added when used in line. This also restores functionality that was in 2.3.5.
 * Fixed event insertion bug where events may not be saved if the total number of events is larger than a certain number (I never did figure out what that number was...but it was probably around 4 or 5).
 * Updated serialized output for the commented out file out serialized function.

= 2.4 =
Release Date: 4/2/2010:

 * Updated file structure, moving language files to the /lang directory and javascript files to the /js directory, also updated associated links
 * Added latches for CSS
 * Rewrote parts of fergcorp_countdownTimer_format to make it less repetitive. Also removed last three variables: displayFormatPrefix, displayFormatSuffix, and displayStyle
 * Completely moved namespace from afdn to fergcorp
 * Changed the way dates are removed. Made it based on the lack of a date, instead of the lack of a title. 
 * Reduced the user access level to Options Manager instead of Administrator
 * Moved the options page from tools to settings in the admin menu
 * Implemented the register_setting function, incidentally changing the way options are stored
 * Added option to parse shortcodes in the_excerpt
 * Updated all the languages files using Google Translate and http://pepipopum.dixo.net/index.php
 * Use the get_option directly instead of by variable
 * Removed checkUpdate variable because we don't use it anymore.
 * Updated to use _n i18n function
 * Updated depreciated get_settings to get_option

= 2.3.5 =
Release Date: 2/17/2009:

 * Updated calculation routine to ensure that dates are accurate when 'Months' are not displayed.
 * Updated languages and added Latvian, Romanian, Russian, Danish, Lithuanian, and Serbian.
 * Updated readme.txt file
 * Fixed small display issue in the administration menu

 = 2.3.1 =
Release Date: 11/20/2008:

 * Fixes a bug. Sorry to everyone who has to redownload.

 = 2.3 =
Release Date: 11/19/2008:

 * Made meta boxes into WP-based functions with AJAX support
 * Renamed $dates to $fergcorp_countdownTimer_dates and made it global
 * Reversed order of afdn_countdownTimer parameters. See documentation for usage
 * Updated meta boxes to work in WP 2.7
 * Removed the option to disable enableTheLoop (i.e. always enabled now)
 * Added shortcodes. See documentation for usage
 * Updated some of the text so that links are not part of the translation. Not that this has been an issue, but it assures that links aren't tampered with in language translations
 * Updated the widget to use the latest WP functions
 * Widget now has a description
 * Internal versioning is now done automatically
 * Fixed a bug where 'No Dates Present' would not display, even though there were no dates present
 * Fixed a bug where an empty array would cause plugin to crash
 * Fixed a problem that caused the timer to only display 'in ' if 'strip zeros' is enabled
 * Updated a couple function checks to check for the functions that we're actually using
 * Updated the plugins_dir function call to properly reference the countdown-timer directory (this fixes issues with IIS and Windows)
 * Added a helper function for afdn_countdownTimer so that users can use fergcorp_countdownTimer instead
 * Fixed a potential bug (aka The Furton Fix) for systems running Windows where PHP may barf and give a warning:
Warning: date() [function.date]: Windows does not support dates prior to midnight (00:00:00), January 1, 1970 in afdn_countdownTimer.php on line 612
 * Various bug and security fixes
 * Paypal link doesn't use a form anymore
 * Added a test to ensure cal_days_in_month function exists. If not, use a drop in replacement.

 = 2.2.5 =
Release Date: 9/23/2008:

 * Added Hungarian and Norwegian translations
 * Fixed small bug on line 426 regarding stripslashes

= 2.2.4 =
Release Date: 9/4/2008:

 * Added Bosnian language translation
 * Fixed mistranslations in German language
 * Output of displayFormatPrefix/displayFormatSuffix are now escaped
 * Fixed a fatal error that was sometimes caused when there were no dates to countdown to
 * Updated the FAQ

= 2.2.3 =
Release Date: 7/9/2008:

 * Fixes language issue with commas
 * Updated internal rev version number

= 2.2.2 =
Release Date: 6/30/2008:

 * Resolves #876 which had <? instead of <?php
 * Resolves #879 which was an incorrectly passed fergcorp_countdownTimer_fuzzyDate function
 * Globalized $fergcorp_countdownTimer_getOptions in the afdn_countdownTimer function
 * Added spaces to the end of all units of time

= 2.2.1 =
Release Date: 6/18/2008:

 * Fixed bug where Countdown Time Display didn't function properly
 * Added Italian and Polish translations, updated others

= 2.2 =
Release Date: 5/15/2008:

 * Updated some phrases that missed being i18n
 * Updated i18n to use sprintf's to assist in proper translation
 * Update the admin page to WordPress 2.5 Look and Feel
 * Users are now able to define the data (text/HTML) that comes after the title and before the actual countdown
 * Implemented a new function, fergcorp_countdownTimer_single($date), that allows users to create a one-off event outside of The Loop. $date should be PHP strtotime parseable string
 * Plugin output is now XHTML 1.0 Strict compliant
 * Scripts are now loaded using wp_enqueue_script
 * Added a new JS file, webtoolkit.sprintf.js, because JS doesn't have native sprintf support
 * Translators names (and URL, if available) are now displayed/credited within the plugin
 * Tweaked the table for the 'Resources' area
 * Tweaked the table for the 'One Time Events' area
 * The usual bug fixes

= 2.1.1 =
Release Date: 2/20/2008:

 * Fixed i18n translation issues where mo file would sometimes not be loaded
 * Updated UI (note: Based on UI code from Google XML Sitemaps)
 * Removed code dealing with recurring events (which has not been included for a few versions now)
 * Added js countdown ability to admin example (which doesn't have wp_footer hook?)
 * Updated the link to the JS file to make it dynamic in case a folder gets renamed
 * Fixed a bug in the JS file that caused dates to be calculated incorrectly
 * Append a letter to the beginning of the unique id (as per XHTML requirement)'who knew?
 * Added two language files: Swedish (thanks to Mattias Tengblad) and Spanish (thanks to Google Translator)
 * Released as 2.1.1 instead of 2.1 due to a technical glitch in the way WordPress recognizes version numbers.

= 2.0.1 =
Release Date: 12/29/2007:

 * Bug in the initial 2.0 version that required the plugin to have PHP5 or greater. This has now been fixed and it works with PHP4 and PHP5. Thanks to Jim Lynch for the bug report.

= 2.0 =
Release Date: 12/29/2007:

 * Updated plugin description line
 * Rearranged text in the installation notes to emphasize using the widget rather then the code
 * Fixed a bug that crashed the plugin if no dates were present (a PHP 5 problem??)
 * Implemented the 'register_activation_hook' function rather then the old way
 * Changed the way DB updates are handled. Instead of having a specific update regimen for each version, the plugin will only update an option field if it doesn't exist (if it exists, but is blank, it will NOT update'as it shouldn't).
 * Updated fergcorp_countdownTimer_fuzzyDate with another variable so that the real target date is always known.
 * Removed code dealing with updates since WP 2.3 now does this automatically
 * Bug fix. Widget title isn't saveable due to a programming error. Thanks to Michael Small for the catch.
 * Renamed some functions from afdn to fergcorp
 * Added JavaScript function for JS countdown
 * Tabbed $afdnOptions array to make it more readable
 * Fixed strtotime typo
 * Brought time display inline with current WordPress practice. This fixes the dreaded timezone glitch.
 * Strip non-sig zeros option added
 * Fixed bug where 'No dates present' would _not_ show if the data was returned instead of echo'd
 * Renamed to $getOptions to $fergcorp_countdownTimer_getOptions to avoid clashing

= 1.91 =
Release Date: 12/4/2007:

Fixed error where the widget wasn't updated correctly.

= 1.9 =
Release Date: 8/7/2007:

 * One-off countdown timers (i.e. standalone timers for posts, etc)
 * Ability to customize timer style
 * Added 'week' as a unit of time
 * Even more bug fixes including the one where if the plugin was in a directory other then the plugin directory, it really wouldn't work.

= 1.8 =
Release Date: 5/7/2007:

 * Note to Existing users: Version 1.8 sees the demise of recurring events.
 * Built in widget! No need to download and activate another plugin!
 * Ability to select any combination of years, days, hours, minutes, and seconds to display
 * Internationalization support
 * Default settings automatically set on activation
 * More accurate countdown mechanism (you wouldn't think counting could be so hard)
 * Numerous bug fixes
 * Reorganized management page

= 1.7.3 =
Release Date: 4/29/2007:

 * Fixed missing tag
 * Fixed form not submitting in IE, Safari, et all
 * Fixed 'pressing return key doesn't submit form' bug

= 1.7.1 =
Release Date: 3/9/2007:

 * 'No Dates Present' bug fix
 * Updated Plugin URI to be correct
 * If updates are checked for, a link to the latest tag is given instead of the trunk (guaranteed to be stable)
 * Added Widget plugin
 * foreach error fixed (Thanks to Johnathan)

= 1.7 =
Release Date: 3/9/2007:

 * Note: You may 'lose' your date when you upgrade, so you might want to write them down. If you forget to write them down, just downgrade to 1.6.1 and write them down, then upgrade again.
 * On update, color bar is displayed at top
 * Fixed a bug where HTML characters were not escaped
 * Prefixing and Suffixing of the event
 * Added more information on usage on the plugin page

= 1.6.1 =
 * Fixed the famed unserialze bug!

= 1.4 =
Release date: 03/16/2006

This is the first release using the WordPress SVN. It's kind of been cool using the SVN because it is easier to see what changes have been made from version to version. In any event, this release has a couple of updates. First, there is an option to include the timer from within the WP Loop, that is you can now display the timer from within a post or page (see instructions for the specifics). The idea came from Ronny a mere four days ago, just to give you an idea of the turn around time on suggestions to release. Because of the way the plugin is implemented, I also had to modify the main function that gets the ball rolling on displaying the timer. It's designed to be backwards compatible, so you shouldn't have any problems. I also had to rewrite parts of the version check system to work with the SVN. The format is actually much better and just calls one text file which lists the latest version (i.e. '1.4?). It compares that to the current version and let's you know if there's a new version. Simple! As always, you can turn the feature off.

= 1.3 =
Release date: 03/16/2006:

I've already received some preliminary feedback on 1.2 (thanks Dave), so I've made a couple of updates. First, there are actually instructions for configuring the onHover Time Format option. Second, if you leave onHover Time Format blank, it will remove the dashed underline on the timer so no one is confused. I also fixed a really stupid bug, I never but a stripslashes in. So if you tried using something that needed escaping (such as an apostrophe), you would get a slash. That doesn't happen anymore. Enjoy!

= 1.2 =
Release date: 03/13/2006: 

Development has been slow. Not really a lot to do. But version 1.2 offers some great things, including a bug fix! Deleting two or more events doesn't make the plugin freak out anymore (the bug fix). You can also customize how the onHover time is displayed, including not displaying it at all (just leave it blank). Because you can leave it blank, there is no default; but you can use and PHP 'date()' format. I recommend 'j M Y, G:i:s'. I also added a six month delay before the date on recurring events is reset'although as I'm writing this, I realize there's a slight bug there, nothing critical though. I welcome any new ideas, just leave a comment down below!

= 1.1 =
Release date: 11/28/2005:

NOTICE: Copyright format changed from MIT to GNU GPL as of version 1.1
Not a whole lot of major thing. Thanks to Benoit Kechid for catching a calculation and syntax error. There was a request for making things linkable, so you can now add a link an event. The time is also has a dashed underlined and if you roll over it with your mouse, the date and time of the event are displayed.
I don't have anymore ideas for this plugin, so unless I you email me with something that you would like, there probably won't be any more updates.

= 1.0 =
Release date: 10/05/2005:

You can now set recurring dates, sort of. The plugin can currently handle things that happen on a given day of a given month every year (such as a birthday). The plugin will also now check for updates whenever you are in the admin panel. Download and copy to your plugins directory, then rename it to .php

= 0.95 =
Release date: 09/26/2005:

Fixed a Warning message (thanks to cordney* for the heads up). Also changed a few things: If you have 'Automatically delete 'One Time Events' on, only events that that do NOT have 'Display 'Time since'' will be deleted. Also, the file is now a PHPS file, not zip file. Download and copy to your plugins directory, then rename it to .php

= 0.92 =
Release date: 09/23/2005:

This version adds the option to automatically delete One Time Events that have all ready occured. If you don't choose to automatically delete the events, they will be displayed as 'Time since' after the event occurs. Small issue I'm still working on: events are only erased when you update timer options.

= 0.8 =
Release date: 07/23/2005:

This is a backend change. The dates.txt file has been replaced with an entry in the WordPress Database. Unfortantly, you'll have to manually transfer your events into the new form. Once they are in the form, they are automatically sorted, with events ending soonest on the top.

= 0.7.1 =
Release date: 05/23/2005:

Changed an internal function name reference due to a potential conflict with another plugin (Thanks Robert)

= 0.7 =
Release date: 05/20/2005:

Added Admin menu. Allows user to update information via web-interface now.

= 0.6.1 =
Release date: 05/16/2005:

Fixed small math error in cdt_format() on line 47.

= 0.6 =
Release date: 05/15/2005:

Initial public release