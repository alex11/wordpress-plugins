<?php
/*
oEmbed for BuddyPress settings

Help: http://buddypress.org/groups/oembed-for-buddypress
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KU38JAZ2DW8TW
*/

// enable / disable BP components for oEmbed parsing
$bp_oembed['activity_updates'] = true;
$bp_oembed['activity_comments'] = true;
$bp_oembed['forum_posts'] = true;

// whitelist - skip urls from oEmbed parsing + caching
// you can add domains to the whitelist array
// eg. uncomment the following line to whitelist links from google.com
//$bp_oembed['whitelist'][] = 'google.com';
?>