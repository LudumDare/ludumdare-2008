=== Sabre ===
Contributors: dlo
Donate link:
Tags: spam, admin, registration, register, security, antispam, anti-spam, authentication
Requires at least: 3.0
Tested up to: 3.3.2
Stable tag: 1.2.2

Sabre is an acronym for Simple Anti Bot Registration Engine.
It's a set of counter measures against spam registration on your blog.

== Description ==

Sabre is an acronym for Simple Anti Bot Registration Engine.
It's a set of counter measures against spam registration on your blog.

Your visitors are granted permission to register freely on your blog and now you are plagued by fake users automatically created by spammers? Sabre is the solution to stop definitely these robotized visitors!

List of available features:

1. Inclusion of a captcha in the registration form 
1. Selection of the captcha's complexity
1. Selection of the background colour for the captcha image 
1. Inclusion of a math test in the registration form 
1. Selection of the math test's complexity
1. Inclusion of a text test in the registration form 
1. Random or fixed choice of the test to run 
1. Unobstrusive tests to detect if registration is done by humans or not 
1. Registration blocked if Javascript is unsupported by the browser 
1. Registration blocked if visitor's IP address is found on ban lists
1. The site administrator can validate the user registration (monosite only)
1. The user can validate his registration by clicking on a link sent by mail (monosite only)
1. Limited number of days for user confirmation. Without beeing confirmed within the period of time, the user account is disabled (monosite only)
1. Log on prohibited before user confirmation (monosite only)
1. User is allowed to choose his password when registering on the site (monosite only)
1. User must agree with a warning text, disclaimer or general policy note when registering
1. User must give an invitation code during registration  
1. Main statistics displayed on the site's dashboard
1. Custom logo on logon/registration screen (monosite only)
 
All these features are activated by parameters. Thus, Sabre is flexible enough and fits the protection policy you define for your blog.

NOTE 1:
For WordPress 3.0 or higher in mono or multisite modes, use Sabre 1.2.2
For WordPress 2.5 to 2.9.x, use Sabre 1.1.2
For WordPress prior to 2.5, use Sabre 0.6.3

NOTE 2:
If you are upgrading from a previous install, don't forget to deactivate Sabre before overwriting the older files with the new ones. Then activate it again so Sabre can apply the required database and options updates.

== Installation ==

1. download the archive
1. unzip and drop all the files, as is, in your plugins/sabre directory.
1. Enable the plugin in the WP Admin >> Plugins section.
1. In monosite mode, just click on "Activate".
1. In multisite mode, Sabre MUST be activated by clicking on "Network Activate" as it is designed to protect all the sites of a network with the same options. Additionaly, only the super admin of the network will be able to manage the plugin's options.
1. Change the parameters following the "Configure" link of the Plugins panel or go to the WP Admin >> Tool >> Sabre tab. 
1. Other languages
Sabre is delivered in English and French but can be easily used with other languages. You just have to create a file sabre-xx_YY.po (xx_YY being the language code of your WordPress settings. Eg: fr_FR for French) from the existing file sabre.pot using PoEdit. Then, just translate the text strings located after the "msgid" tag and put the translated string after the "msgstr" tag.
The resulting sabre-xx_YY.mo file has to be stored in the sabre/languages directory.
Alternatively, you can find the translation files (.mo et .po) of some languages in the FAQ page.


If you are upgrading from a previous install, don't forget to deactivate Sabre before overwriting the older files with the new ones. Then activate it again so Sabre can apply the required database and options updates.

== Frequently Asked Questions ==

= What are the required components for Sabre ? =

In order to run, Sabre needs the following:

- PHP 4.3.2 or higher
- GD 2.0.2 or higher
- and WordPress 2.5 or higher

For WordPress 3.0 or higher in mono or multisite mode, use Sabre 1.2.2
For WordPress 2.5 to 2.9.x, use Sabre 1.1.2
For WordPress prior to 2.5, use Sabre 0.6.3 

= Where can I find a translation of Sabre in my own language ? =

Ready-to-use translation files are available in :

1. [Danish](http://mads.eu/wp-plugins#sabre) (thanks to Mads Christian Jensen)
1. [Spanish](http://www.faltantornillos.net/proyectos/gnu/otros/sabre-es_ES.tar.gz) (thanks to Daniel)
1. [Finnish](http://systemshed.com/sabre/sabre_fi_FI.zip) (thanks to Kimmo Sinkko)
1. [German](http://www.inbegriff.de/wp-content/uploads/languages.rar) (thanks to Andreas Schulz)
1. [German - old version](http://matthiaskoch.net/?p=37) (thanks to Matthias Koch)
1. [Polish](http://dev.m1chu.eu/index.php?title=Polska_translacja_Sabre) (thanks to m1chu)
1. [Portuguese - Brazil](http://www.tudoparawordpress.com.br/download/plugins/sabre.0.8.1-pt-BR.zip) (thanks to Gustavo)
1. [Italian](http://gidibao.net/index.php/2009/07/07/sabre-in-italiano/) (thanks to Gianni)
1. [Norwegian](http://www.dolcevita.no/div/sabre-nb_NO.zip) (thanks to Kjetil Flekkøy)

If you can't find the files for your language, you can create them yourself with the sabre.pot file included in the package and PoEdit.

= I don't want to bother my users with a captcha or math test but still want to protect me against false registrations. What can Sabre do ? =

Sabre can protect your registration process without asking the user to pass a captcha or math test. Just untick the "Activate Captcha" and "Activate math test" checkboxes and select the "All" item of the "Test sequence" dropdown menu. Both tests are now inactive. Then tick off the "Activate stealth test" checkbox. Additionally, change the other parameters of the "Stealth tests" paragraph, if needed. Now Sabre will silently protect your registration process, keeping away spam registration from your site.

= I just installed Sabre and now the users already registered can't log in anymore. What's going wrong ? =

You decided to activate the registration confirmation and the users registered before Sabre's installation received the following message when they try to log in : "ERROR : Invalid registration status". This is because Sabre considers they have not confirmed their registration. You need to go to Manage >> Sabre >> Approved registrations and manually register each user giving his WordPress account name and clicking on "Add" under "Manual registration". You can also register all the existing WordPress users by ticking off the "Add all WordPress users" checkbox before hiting the "Add" button.

= I'm fond of numbers. How can I display some figures about Sabre on my blog ? =

You can activate the "Show on dashboard" parameter in Sabre's administration screen to display basic figures like number of registrations blocked, approved and pending confirmation on the dashboard of your blog.

Another possibility is to show up the number of blocked spam bots on the registration form by clicking on the "Show banner" parameter.

The third way is to use Bernhard Riedl's [GeneralStats](http://wordpress.org/extend/plugins/generalstats/) plugin that integrates Sabre's figures among other useful informations about your blog's activity.

= How can I fire some of my own functions based on Sabre's events ? =

If you're a plugin or theme designer, you can use the following Sabre's action hooks:

1. **sabre\_accepted\_registration** : Fired whenever the registration of a new user is fully accepted. You can simply add the following in your code: < ?php add\_action('sabre\_accepted\_registration', 'myownfunctiontolaunch'); ? > to have your 'myownfunctiontolaunch' function executed each time a new user has been registered by Sabre.
1. **sabre\_cancelled\_registration** : Fired each time you unregister a user. Just add the following in your code: < ?php add\_action('sabre\_cancelled\_registration', 'myownfunctiontolaunch'); ? > to have your 'myownfunctiontolaunch' function executed each time a new user has been unregistered in Sabre.

== Screenshots ==

1. Registration form with captcha added and custom password choice
2. Registration form with math test added and custom password choice
3. The dashboard widget

== Documentation ==

All the functions of Sabre are located in the Tools >> Sabre tab of the administration environment.

Sabre interface is divided into five tabs:

**1) "General options"**

First, some numbers related to valid, invalid and pending confirmation registrations are displayed.

Then, you can find the parameters of Sabre:

1a) Captcha Options

Click on the checkbox "Activate captcha" to display, in the registration form , a characters string that the user will have to copy back.

The other captcha's parameters will let you define the string's length, valid characters used to generate the random string  as well as the background colour of the image, number and type of graphic objects used to "blur" the string.

The captcha is based on QuickCaptcha 1.0 from Web 1 Marketing, Inc released under GNU GPL.

1b) Math Options

Click on the checkbox "Activate math test" to display, in the registration form , an arithmetic operation. The user will have to give the result.

The other parameter let you define the valid operations the plugin will choose from. Recognized operations are addition, substraction and multiplication. The plugin will choose randomly two numbers between 1 and 20 and one of the listed operations.

1c) Text Options

Click on the checkbox "Activate text test" to display, in the registration form , a random word. The user will have to give the n-th letter of this word.

This is an alternative to the graphic captcha for those who dislike it.

1d) Sequence of tests

Select "All" to add all the active tests in the registration form.

Select "Randomly" and the plugin will choose one of the tests, active or not.

1e) Stealth Options

Click on the checkbox "Enable stealth test" to activate a set of internal tests that try to detect if the current registration is done by a human being or not. These tests doesn't interact with the user and run undetected for a regular human registration.

These tests include the following:

* Control that the registration form is loaded before the answer is sent to the server.
* Control that the IP adress of the requester is the same when the form is sent back.
* Control that the browser used to register has Javascript capabilities as many spambots lack them. You can choose to reject the registration in such case, clicking on the checkbox "Block if Javascript unsupported".
* Control that the Javascript capability is not faked.
* Control that the registration is done within a maximum period of time. You can set this period (in seconds) under "Session time out". Try to maintain this number as low as possible for security reasons but high enough to let a human fill the registration form. Default value is 5 minutes (300 seconds).
* Control that the registration form is possibly filled by a human, in a minimum amount of time. A spambot will spend very little time to fill the form and send it to the server compared with human possibilities. You can set this minimum amount of time (in seconds) under "Speed limit". Default value is 5 seconds.
* Control that IP address is not banned by DNSBL servers. You can turn on/off this control, clicking on the checkbox "Check DNS Blacklists".   

1f) Confirmation Options

Confirmation Options are not available in multisite mode. The built-in registration features of WordPress will be used. 

Select the correct item on the dropdown list "Activate confirmation" to force the user or the blog administrator to confirm the registration on your blog.

* **None**: No confirmation is required. (default)
* **By user**: User must confirm his registration. When this option is activated, the registering user will receive his user id and password by mail as usual. He is also asked to  confirm his registration within x days following  a link added to the mail. During this period of time, the user account is waiting for the confirmation but usable to connect to the blog. If the confirmation is not done in the due time,  the account will become unusable.
* **By admin**: The registration needs the administrator's confirmation to be activated. When this option is activated, the registering user will receive his user id and password by mail as usual but the account is not usable to connect to the blog until administrator's validation. Upon confirmation, a mail is sent to the user.

The next three parameters are effective only if user confirmation is required.

Number of days lets you give a period of time (in days) for the user to confirm his registration before the account becomes unavailable.

By clicking on the "Deny early sign-in" checkbox, you can prevent the connection of the new user until confirmation of his registration.

If you want to receive a mail whenever a user confirms its registration, click on the "Send mail when confirmed" checkbox.

If you want to suppress automatically the user account created by WordPress when the registration is cancelled, just click on the "Suppress unregistered users" checkbox. This option will be taken into account either in a manual cancelation or in case of exceeded period of time. Keep in mind that all the posts and links owned by the suppressed user account will be deleted as well.

IMPORTANT : The users with "edit_users" capability will not be controled. Then, it is always possible to use the default admin account created by WordPress during the blog installation.

1g) Policy Options

Click on the checkbox "Enable policy agreement" to force the user to acknowledge the fact that he read the rules of use of your blog before registration.

Give a title to your text block filling the "Policy name" input box. You can choose names like Disclaimer, Licence agreement, General policy, etc...

If you have a dedicated policy page on your site, you can enter its URL in the "Policy link" input box. When displaying the registration form, the user will see a link to this URL. The link's text will be the Policy name entered above. The URL can be a WordPress page or an external html file.

If you don't have a dedicated policy page, just write down the text of your disclaimer in the "Policy text" input box. Just write plain text. HTML tags are not allowed. 

1h) Invitation Options

Click on the checkbox "Enable invitation" if you want to control who can register on your blog by asking the user to give his invitation code during registration.

Fill the "Code" input box with the valid invitation codes you want. Example : PROMO2008, SZ78PQR, etc... or click on the "Gen" button to let Sabre generate an invitation code for you. To delete a code, just click on the "Sup" button. CAUTION: Invitation codes must be typed in uppercase letters.

If you want to limit the number of use for a specific code, just type a number in the "Usage" zone. Sabre will rest one each time the code is used. When the counter reaches zero, the code is no longer valid.

If you want to limit the period of use for a code, just type an expiration date in the "Validity" zone. Format must be YYYY-MM-DD: Example : 2010-10-20 for a code valid until October 20th, 2010.

Then, you can communicate those codes to the persons who will be allowed to register on your blog.

1i) Miscellaneous Options

Click on the checkbox "User password" to let the user choose his own password during the registration process. Otherwise, WordPress will generate a random password. The password strength is displayed in real time to help the user to choose a strong and safe password. (Not available in multisite mode)

Enter the name you want in the "Sender's name" input box. This name will appear as the sender of the registration mail sent by Sabre to the user. Will default to the site's name if left blank.

Enter the Email address of your choice in the "Sender's Email" input box. It will appear as the sender's Email of the registration mail sent by Sabre. Will default to the administrator's Email set in the WordPress general parameters if left blank.

Click on the checkbox "Show banner" to add a reference and a link to Sabre's site at the bottom of the registration form. It's up to you to decide if you want to advertise Sabre or not !

Click on the checkbox "Show on dashboard" to add a widget on the dashboard with the main statistics about Sabre.

Click on the checkbox "Show in profile" to add some informations about the registration status in the user profile.

Click on the checkbox "Suppress Sabre" if you want to delete all information created by Sabre (table and options) when deactivating the plugin. 
CAUTION : Use this option only if you decided to stop using Sabre or if you want to purge the table and reset all the options to their default values. 

**Don't forget to click on "Save options" to store your changes.**

**2) "Blocked Registrations" Tab**

List the invalid registrations with the cause of error. The number of new registrations blocked since your last visit is shown between parenthesis on the tab.

It's possible to suppress the log giving the number of days to retain (20 days by default) and clicking on "Delete". You can ask Sabre to do it automatically, from now on, with the same period by clicking on the checkbox. A null or negative number of days with the checkbox ticked will prevent Sabre from doing the automatic cleanup. To suppress the log for a specific period of time without modifying the parameter of the automatic cleanup, just type the number of days to retain, untick the checkbox and press "Delete".

**3) "Approuved Registrations" Tab**

List the registration definitively accepted (status = ok). The user id created is displayed and gives access to its data by just clicking on it. The number of new accepted registrations since your last visit is shown  between parenthesis on the tab.

It's also possible to register manually a user giving his WordPress account name or all existing WordPress users clicking on the checkbox then pressing the "Add" button.

To cancel the registration of users, just click on the corresponding checkbox in the list, then press the "Unregister" button.

**4) "Registrations to confirm" Tab**

List the registration waiting for confirmation (status = to confirm). The user id created is displayed and gives access to its data by just clicking on it. The number of new registrations to confirm since your last visit is shown  between parenthesis on the tab.

Whether the confirmation is done by the blog administrator or the user, the following two buttons are available:

* Confirmation of the registrations is done by selecting the accounts in the list, then pressing the "Confirm" button.
* Refusal of the registrations is done by selecting the accounts in the list, then pressing the "Refuse" button.

When the confirmation must be done by the user himself, the blog administrator has no reason to intervene but, if needed, he can confirm or refuse manually a registration on behalf of a user.

**5) "About" Tab**

A wise text about the author and his work. A must-read you can't resist to !

**6) Custom image in logon/registration screen**

To change the default logo, copy your own image file in the /images directory of Sabre plugin. The file must be a 290pixels x 66pixels GIF file named sabre-login.gif. (Not available in multisite mode)

== Changelog ==

= v1.2.2 =

* IMPORTANT! This version is a security fix. 
* You are strongly requested to update to Sabre 1.2.2 in order to suppress a security flaw discovered in the previous releases of this plugin.
* Sabre v1.2.1 is missing some files, use v1.2.2 instead.

= v1.2.1 =

* IMPORTANT! This version is a security fix. 
* You are strongly requested to update to Sabre 1.2.1 in order to suppress a security flaw discovered in the previous releases of this plugin.

= v1.2.0 =

* CAUTION: This version won't run with WordPress versions prior to 3.0. See the NOTE 1 of the Description paragraph if you want to use Sabre with an older version of WordPress. 
* Compliant with WordPress 3.0 or higher.
* (NEW) Usable in multisite mode. See list of features for restrictions.
* (ENHANCED) Random security test now choose among active tests only.
* (ENHANCED) Password no longer included in the registration mail if the custom password option is activated.  
* (FIXED) Few bugs fixed like Password strength indicator and missing HTML tags.

= v1.1.2 =

* (FIXED) Fix a bug preventing to display the captcha image with WordPress 3.0.
* (FIXED) Fix the display of the policy rules in the registration screen.
* (ENHANCED) Link to an policy page and policy text can be both used at the same time.

= v1.1.1 =

* Compliant with WordPress 3.0 in mono-site configuration (formerly standalone WordPress).
* CAUTION: The use of Sabre in multi-site mode (formerly WordPress MU) is an untested feature and will probably not work or give unexpected results.
* (ENHANCED) One can choose now by parameter if registration's informations are to be included in the user profile screen.
* (FIXED) Sabre can again be used with WordPress version prior to 2.8.
* (FIXED) Fix some HTML stuff preventing the login screen to be XHTML 1.0 Transitional compliant.

= v1.1.0 =

* Compliant with WordPress 2.9.2
* (NEW) Registration's informations included in the user profile screen.
* (ENHANCED) Policy text can now be an external html or WordPress page shown as a link on the registration form.
* (FIXED) Added input fields have now the look and feel of the default WordPress registration form but can be customized in the sabre_login.css file.

= v1.0.0 =

* Compliant with WordPress 2.9.1
* (NEW) Captcha test added : The TEXT test. An alternative to the graphic captcha. The user will have to enter the n-th letter of a randomly generated word.
* (NEW) Custom sender's name and Email on the registration's mails sent by Sabre.
* (ENHANCED) Invitation codes management: Auto-generation of codes, limited use of the same code, period of validity of the codes.
* (FIXED) Timestamps didn't take into account the GMT offset from the WordPress general options.  

= v0.9.0 =

* Compliant with WordPress 2.8.2
* Administrator can force the confirmation of registration even when user's confirmation is required.
* New design of the options screen to reduce the need of scrolling. The various blocks of parameters can be hidden/shown for easier navigation in the form.
* Timestamps now take into account the GMT offset set in the WordPress general options. 
* No more reference to "blog" in the messages sent or displayed. This was done to stick with the fact that WordPress can be used in many other ways than a mere blog.
* Reinforced security against potential SQL injection using $wpdb->prepare function during database accesses.
* Action hooks added to allow third part plugins to run functions according to Sabre's events. See FAQ section for more details.  

= v0.8.1 =

* Made compliant with WordPress 2.7.1
* Corrected bug in dashboard statistics links
* Corrected bug in strengh meter when choosing custom password

= v0.8.0 =

* Added parameter to activate invitation codes
* Added custom logo in logon/registration screen
* Plugin is now compatible with WordPress 2.7
* Plugin totally rewritten in object oriented PHP
* Message management reviewed to support plural form  

= v0.7.4 =

* Corrected bug in automatic suppresion of the blocked registrations log. A value of zero or less days will now stop Sabre's automatic cleanup.
 
= v0.7.3 =

* Added parameter to choose between black or white background for the captcha image.
* Added parameter to ask the user to read and agree with the general policy rules of the blog before registration.
* Sabre has been made compliant with the possibility offered by WordPress 2.6 to move the wp-content directory to a custom location.

= v0.7.2 =

* Added parameter to let the blog admin validate the user registration.
* Revised admin interface for the management of registrations to confirm.
* Added tab "Registrations to confirm".

= v0.7.1 =

* Added parameter to display Sabre statistics on the dashboard of the blog.
* Added parameter to let the user choose his own password during registration.

= v0.7.0 =

* This version requires WordPress 2.5 and can't be used with previous versions of WordPress.
* Administration panel has been reworked to stick with the new WordPress admin interface.
* Manual registration of an existing user done by name and no longer by user ID as WordPress 2.5 no longer shows the user ID.
* Added parameter to receive a mail upon confirmation of registration by a user.

= v0.6.3 =

* Small adjustments done to comply with WordPress 2.3.3

= v0.6.2 =

* Added parameter to check if IP address is banned by DNSBL servers.
 
= v0.6.1 =

* Change the Sabre table definition as some MySql versions doesn't allow text columns to have default value. This was preventing the creation of the table during Sabre initialization in some occasion.

= v0.6.0 =

* Added parameter to delete WordPress account automatically when registration is canceled, either manually or because of exceeded period of time. 
* Added parameter to insert a reference to Sabre at the bottom of the registration form.
* Added parameter to select the number of days for automatic purge of history log.
* Added parameter to delete all information about Sabre when deactivating the plugin. This will clean your WordPress blog of tables and options created by Sabre, if you decide to stop using Sabre.  
* Performance improvement bypassing the tests if errors already detected before calling Sabre (eg. user name and/or mail missing)
* Performance improvement by code optimization
* Reinforced access security to the administration panels of Sabre using wp_nonce_field

= v0.4.2 =

* Corrected bug in the storage of the number of days for registration's confirmation
	
= v0.4.1 =

* Added parameter to deny/authorize newly registered users to sign in before registration is confirmed.
* Added the possibility to automatically register all existing WordPress accounts.
* Added several messages to give the status of operations done. 

= v0.4.0 =

* Added several new internal tests to make Sabre more efficient in spambot detection. Those tests run undetected for regular human registrars.
* Those new tests are: 
* Control that the registration form is loaded before the answer is sent to the server.
* Control that the IP adress of the requester is the same when the form is sent back.
* Control that the browser used to register has Javascript capabilities as many spambots lack them.
* Control that the Javascript capability is not faked.
* Control that the registration is done within a maximum period of time.
* Control that the registration form is possibly filled by a human, in a minimum amount of time.
* Changed the way to control who can bypass the registration confirmation : Sabre now tests the "edit_users" capability and no longer the user level. This will ensure that all accounts with a high level of rights will always be able to sign in.

= v0.2.2 =

* Code optimized and splitted in various files to reduce server loading.
* Automatic cleanup enhanced. 

= v0.2.1 =

* Added a possibility to include the test randomly in the registration form. Added manual suppression of logs.

= v0.1.1 =

* First public version