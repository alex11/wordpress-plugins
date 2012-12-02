<?php
/**
 * Plugin Name: 2 Click Social Media Buttons
 * Plugin URI: http://ppfeufer.de/wordpress-plugin/2-click-social-media-buttons/
 * Description: Adding buttons for Facebook (Like/Recommend), Twitter, Google+, Flattr, Xing, Pinteres, t3n and LinkedIn to your WordPress-Website in respect with the german privacy law.
 * Version: 1.4.1
 * Author: H.-Peter Pfeufer
 * Author URI: http://ppfeufer.de
 * Text Domain: twoclick-socialmedia
 * Domain Path: /l10n
 */

/**
 * Avoid direct calls to this file
 *
 * @since 1.0
 * @author ppfeufer
 *
 * @package 2 Click Social Media Buttons
 */
if(!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');

	exit();
} // END if(!function_exists('add_action'))

/**
 * Konstanten
 */
define('TWOCLICK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TWOCLICK_PLUGIN_URI', plugin_dir_url(__FILE__));
define('TWOCLICK_BASENAME', plugin_basename(__FILE__));
define('TWOCLICK_TEXTDOMAIN', 'twoclick-socialmedia');
define('TWOCLICK_L10N_DIR', dirname(plugin_basename( __FILE__ )) . '/l10n/');

/**
 * Loading libs used in backend
 *
 * @since 1.0
 * @author ppfeufer
 *
 * @package 2 Click Social Media Buttons
 */
if(is_admin()) {
	require_once(TWOCLICK_PLUGIN_DIR . 'libs/class-twoclick-backend.php');
}

/**
 * Loading libs used in frontend
 *
 * @since 1.0
 * @author ppfeufer
 *
 * @package 2 Click Social Media Buttons
 */
if(!is_admin()) {
// 	require_once(TWOCLICK_PLUGIN_DIR . 'libs/class-twoclick-opengraph.php');
	require_once(TWOCLICK_PLUGIN_DIR . 'libs/class-twoclick-frontend.php');
}