<?php

	echo '<div class="wrap sabre_notopmargin">';
	echo '<h2>' . __("About", 'sabre') . '</h2>';

	echo '<p>';
	_e('Current version: ', 'sabre');
	echo '<b>' . $this->VERSION . '</b>';
	echo '</p>' ;

	echo '<p>';
	_e('SABRE (<b>S</b>imple <b>A</b>nti <b>B</b>ot <b>R</b>egistration <b>E</b>ngine) is dedicated to the protection of your site against automatic registration by spammers.', 'sabre');
	echo '</p>' ;

	_e('This modular protection can run a set of unobstrusive controls for a regular user and include a captcha or math test in the registration form. You can choose to include both or one test or let the plugin choose randomly the test.</br></br>Another possibility is to ask the user to confirm his registration by clicking on a link submitted by mail after he registered. This confirmation must take place within a maximum period of time or the account will become unusable.', 'sabre');

	echo '</div>';

?>