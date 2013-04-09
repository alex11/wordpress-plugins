<?php
/**
Plugin Name: Tabber Tabs Widget
Plugin URI: http://slipfire.com
Description: Easily create a tabbed content area in your sidebar
Author: SlipFire LLC.
Version: 0.39
Author URI: http://slipfire.com/


// Copyright (c) 2010 SlipFire LLC., All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is a plugin for WordPress
// http://wordpress.org/
//
// Based on the JavaScript tabifier by Patrick Fitzgerald
// Copyright (c) 2006 Patrick Fitzgerald
// http://www.barelyfitz.com/projects/tabber/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************
**/

// Set constant path to the plugin directory.
define( TABBER_TABS_DIR, plugins_url('tabber-tabs-widget/'));

// Load Language
load_plugin_textdomain('tabber-tabs-widget', false, TABBER_TABS_DIR . 'language');



/**
 * Initializes the plugin and it's features.
 */
function tabber_tabs_plugin_init() {

	// Loads and registers the new widget.
	add_action( 'widgets_init', 'tabber_tabs_load_widget' );

	// Add Javascript if not admin area. No need to run in backend.
	if ( !is_admin() ) {
		wp_enqueue_script('tabbertabs', TABBER_TABS_DIR . 'js/tabber-minimized.js');
	};

	// Hide Tabber until page load 
	add_action( 'wp_head', 'tabber_tabs_temp_hide' );
		
	// Load css 
	add_action( 'wp_head', 'tabber_tabs_css' );
	
}
add_action( 'plugins_loaded', 'tabber_tabs_plugin_init' );

/**
 * Register the new widget area
 *
 * Load last so we don't effect other widget areas.
 */
function tabber_tabs_register_sidebar() {
	register_sidebar(
		array(
			'name' => __('Tabber Tabs Widget Area', 'slipfire'),
			'id' => 'tabber_tabs',
			'description' => __('Build your tabbed area by placing widgets here.  !! IMPORTANT: DO NOT PLACE THE TABBER TABS WIDGET IN THIS AREA.  BAD THINGS WILL HAPPEN !! Place the TABBER TABS widget in another widget area. ', 'slipfire'),
			'before_widget' => '<div id="%1$s" class="tabbertab">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title section-title">',
			'after_title' => '</h3>'
		)
	);
}
// Load the widget area last so we don't effect other widget areas.
add_action( 'wp_loaded', 'tabber_tabs_register_sidebar' );
	


/**
 * Register the widget. 
 *
 * @uses register_widget() Registers individual widgets.
 * @link http://codex.wordpress.org/WordPress_Widgets_Api
 */
function tabber_tabs_load_widget() {

	//Load widget file.
	require_once( 'tabber-widget.php' );

	// Register widget.
	register_widget( 'Slipfire_Widget_Tabber' );
}

/**
 * Tabber css
 */
function tabber_tabs_css(){
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . TABBER_TABS_DIR . 'tabber.css" />';
}

/**
 * Temporarily hide the "tabber" class so it does not "flash"
 * on the page as plain HTML. After tabber runs, the class is changed
 * to "tabberlive" and it will appear.
 */
function tabber_tabs_temp_hide(){
	echo '<script type="text/javascript">document.write(\'<style type="text/css">.tabber{display:none;}<\/style>\');</script>';
}

/**
 * Admin notice
 */

// Function to check if there are widgets in the Tabber Tabs widget area
// Thanks to Themeshaper: http://themeshaper.com/collapsing-wordpress-widget-ready-areas-sidebars/
function is_tabber_tabs_area_active( $index ){
  global $wp_registered_sidebars;

  $widgetcolums = wp_get_sidebars_widgets();
		 
  if ($widgetcolums[$index]) return true;
  
	return false;
}

// Show the admin notice if there are no widgets in Tabber Tabs widget area
if ( !is_tabber_tabs_area_active('tabber_tabs') ) { 
            add_action( 'admin_notices', 'tabber_tabs_admin_notice' );
	}

// Here's the admin notice
function tabber_tabs_admin_notice() {
	echo '<div class="error"><p><strong>' . sprintf( __('Tabber Tabs Widget is activated.  To start using, add some widgets to the <a href="%s">Tabber Tabs Widget Area</a>.', 'slipfire' ), admin_url( 'widgets.php' ) ) . '</strong></p></div>';
}


?>