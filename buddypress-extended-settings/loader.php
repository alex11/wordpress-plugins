<?php
/*
Plugin Name: BP Extended Settings
Plugin URI: http://buddypress.org
Description: Extra configuration settings for BuddyPress Admins.
Author: modemlooper
Version: 1.2.3
Author URI: http://twitter.com/modemlooper
*/


function bp_extended_settings_init() {
	require( dirname( __FILE__ ) . '/includes/bp-settings.php' );
	require( dirname( __FILE__ ) . '/includes/admin.php' );
	load_plugin_textdomain( 'bpes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'bp_include', 'bp_extended_settings_init' );

?>