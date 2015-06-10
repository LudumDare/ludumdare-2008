# Project Logan #

Edit/rename login.php and mentions in wp-includes/general-template.php.

NOTE: confirm link is broken (sabre)

# Project Sober #

Edit classes/sabre\_class.php for internal logic

Edit sabre.php for e-mail response (above bug)

One small change in sabre\_class.php for the confirmation success email to send you to the right place.

# Phase 2 #

Edit login.php and wp-admin/include/user.php, changing $POST['user\_login'] only!

Find registerform in login.php, add bait, rename original.

Check for !empty.