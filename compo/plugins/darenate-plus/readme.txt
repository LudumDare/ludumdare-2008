=== Donate Plus ===
Contributors: devbit
Donate link: http://devbits.ca
Tags: donate, donation, recognition, paypal
Requires at least: 2.6
Tested up to: 3.2.1
Stable tag: 1.85

Donation form. Recognition wall.  Donation total tracker. PayPal integration. 

== Description ==

This plugin will allow you to place the shortcode `[donateplus]` on a WordPress page and accept donations.  The form includes the option to be recognized on your website after the donation is received.  The Recognition wall can be placed on any WordPress page using the shortcode `[donorwall]`.  You can display your running donation total using the shortcode `[donatetotal]`. The entire plugin is integrated with PayPal IPN so it will receive notification once a donation payment has been processed and put the donor information into your website (if they opted to be displayed).  The donor can promote his name and website, along with some comments on the Recognition Wall.  This should hopefully encourage donaters who like recognition to be more likely to contribute to you or your cause.

Includes Sidebar Widgets and Recurring Donations

*NEW: Manage your Donations*

== Installation ==

1. Upload the `donate_plus` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new top-level menu called 'Donate Plus' will appear in your administration menu.
1. Set up your options in the Donate Plus settings panel.
1. Activate the included widgets for display in your theme or use the following shortcodes:
1. Put the shortcode `[donateplus]` on your donation page to display the donate form.
1. Put the shortcode `[donatewall]` on your donation page or a seperate page to display the Donation Recognition Wall.
1. Use the shortcode `[donortotal]` to display your running total of donations to date.
1. Turn on Instant Payment Notification in PayPal and add your URL to your PayPal Profile IPN settings.

== Frequently Asked Questions ==

= Why don't the donations appear on my Recognition Wall =
You may need to add the Instant Payment Notification URL to your PayPal Profile IPN Settings.  This URL can be found on the Donate Plus Settings Panel at the bottom.  Login in to PayPal and go to your Profile Summary, the click on the Instant Payment Notification link under Selling Preferences and Turn on IPN and set the Notification URL from your Donate Plus Settings Panel.  You can also view your IPN History from this page to see if there are other issues.

= What are shortcodes? =

A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.  To use a shortcode, simple enter it on the page or post in your WordPress blog as described below and it will be replaced on the live page with the additional functionality.

= What shortcodes does Donate Plus use? =

`[donateplus]`
This shortcode will display the Donate Plus donation form

`[donorwall]`
This shortcode will display the Donor Recognition Wall. Optional attribute: title is wrapped within a `<h2>` tag. Usage is `[donorwall title='Donor Recognition Wall']`

`[donatetotal]`
This shortcode will display the total donations received. Optional attributes: prefix is the currency symbol (ie. $), suffix is the currency code (ie. USD), type is the english description (ie. U.S. Dollar). Usage is `[donatetotal prefix='true', suffix='true', type='false']`

= What kind of PayPal account will I need? = 
You will need a Premier or Business account.  Personal accounts are primarily for sending payments and may not include the PayPal IPN features this plugin requires.

= How do I adjust the text colors or other styles? =
Here are 2 simple rules you can add to the bottom of your style.css file to adjust the font color of the heading and main text:

`* Donate Plus Quick Styling */
#donate-plus-form . widgettitle, #donate-plus-total . widgettitle, ##donate-plus-wall . widgettitle{
	color: #000000;
}
#donate-plus-form p, #donate-plus-total p, ##donate-plus-wall p{
	color: #333333;
}`

If you need further styling you can use any of the following rules to adjust specific items:
 
`/* Donate Plus Form Widget Styling */
#donate-plus-form .widgettitle{
	color: #000000; 
}
#donate-plus-form p{
	color: #333333;
}
#donate-plus-form label{
}
#donate-plus-form small{
}
/* Donate Plus Total Widget Styling
#donate-plus-total .widgettitle{
}
#donate-plus-total p{
}
/* Donate Plus Wall Widget Styling */
#donate-plus-wall .widgettitle{
}
#donate-plus-wall p{
}
#donate-plus-wall .date{
}
#donate-plus-wall .name{
}
#donate-plus-wall .amount{
}
#donate-plus-wall .comment{
}`


== Screenshots ==

1. Settings Panel
2. Manage Donations
3. Example of Donation Form
4. Example of Recognition Wall

== Changelog ==
**Aug 30, 2011 - v1.8**

* PayPal IPN bugs fixed thanks to: Johan Rufus Lagerström

**Aug 16, 2011 - v1.7**

* Changed PayPal IPN connection to use SSL
* Moved Donor Wall Date above Name for better readability.
* Added prepare database insert to help prevent malicious HTML entries.

**Oct 25, 2009 - v1.6**

* Added Testing options
* Added Donation Management
* Added Menu Icon
* Added IPN URL information

**Jan 26, 2009 - v1.5.4/1.5.5**

* Added missed localisation tags
* Fixed Recognition Wall date/time to show

**Jan 25, 2009 - v1.5.3**

* Fixed MAJOR bug with option for displaying user info, was incorrectly set to always show wall info even when not checked.

**Jan 25, 2009 - v1.5.2**

* Fixed PayPal error when not using recurring donations

**Jan 25, 2009 - v1.5.1**

* Integrated Widgets into main plugin to fix version control issue.  No need to seperately activate widgets.

**Jan 24, 2009 - v1.5**

* Fixed bugs with recurring donations
* Added button image choices
* Allow donors to hide donation amount, but still appear on wall
* Donors can choose the period of donations rather than having it preset. Settings allow selective recurrance options.
* Limit the amount of Donors showing. Pagination coming soon.

**Jan. 23, 2009 - v1.4**

* Added Sidebar Widget Plugin as an alternative to shortcodes.

**Jan. 20, 2009 - v1.3**

* Fixed Donor Wall to allow disabling
* Added Recurring Donation support

**Dec. 7, 2008 - v1.2**

* Altered Paypal IPN script to use `mc_amount` variable instead of `payment_amount`
* Fixed {wall} url replacement - was putting link ID, not actual link in the Thank You email.

**Dec. 7, 2008 - v1.1**

* Replaced testing url in form back to PayPal url.

== Upgrade Notice ==
IPN re-fixed thanks to Johan Rufus Lagerström