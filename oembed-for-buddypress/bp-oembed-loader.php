<?php
/*
Plugin Name: oEmbed for BuddyPress
Description: The easiest way to share your favorite content from sites like YouTube, Flickr, Hulu and more on your BuddyPress network.
Author: r-a-y
Author URI: http://buddypress.org/developers/r-a-y
Plugin URI: http://buddypress.org/groups/oembed-for-buddypress
Version: 0.52

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KU38JAZ2DW8TW
*/
/* IMPORTANT: edit settings in bp-oembed-config.php - no need to edit this file */

/* Only load the plugin if BP is loaded and initialized. */
function bp_oembed_init() {
	require( dirname( __FILE__ ) . '/bp-oembed.php' );
}
add_action( 'bp_init', 'bp_oembed_init' );
?>