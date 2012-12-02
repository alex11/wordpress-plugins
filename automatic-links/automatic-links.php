<?php
/*
Plugin Name: Automatic Links
Plugin URI: http://superann.com/wordpress-automatic-links-plugin/
Description: Automatically adds HTML anchor tags to plain text links and email addresses embedded in the content of posts and pages.
Version: 1.1
Author: Ann Oyama
Author URI: http://superann.com

== Version 1.1 ==
2009.09.12: Added option to set the link target attribute.

== Version 1.0 ==
2009.03.01: Initial version. Uses make_clickable function from bbPress 0.9.

Copyright 2009 Ann Oyama  (email : wordpress [at] superann.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'autolink_menu');

function autolink_menu() {
	$page = add_options_page('Automatic Links', 'Automatic Links', 'activate_plugins', __FILE__, 'autolink_options_page');
	add_action( "admin_print_scripts-$page", 'autolink_admin_scripts' );
}

function autolink_admin_scripts() {
	wp_enqueue_script('autolink', WP_PLUGIN_URL.'/automatic-links/automatic-links.js', array('jquery'), '1.1', true);
}

function autolink_options_page() { ?>
	<div class="wrap">
	<h2>Automatic Links</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>

	<p><label for="autolink_nofollow"><input id="autolink_nofollow" type="checkbox" name="autolink_nofollow" value="1" <?php checked('1',get_option('autolink_nofollow')); ?> /> rel="nofollow" all automatic hyperlinks</label></p>

	<p><label for="autolink_enable_target"><input type="checkbox" id="autolink_enable_target" name="autolink_enable_target" value="1" <?php checked('1',get_option('autolink_enable_target')); ?> onchange="toggleAutolinkTarget()" /> enable target attribute</label></p>
	
	<p id="autolink_target">target: <input type="text" name="autolink_target" value="<?php echo get_option('autolink_target'); ?>" /></p>
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="autolink_nofollow, autolink_enable_target, autolink_target" />

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	
	</form>
	</div>
	<?php
}

add_filter('the_content', 'autolink_make_clickable');

function autolink_make_clickable($ret) {
	$attribs = ''; if(get_option('autolink_nofollow')) $attribs=' rel="nofollow"';
	if(get_option('autolink_enable_target')) $attribs.=' target="'.get_option('autolink_target').'"';
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace(
		array(
			'#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
			'#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
			'#([\s>])([a-z0-9\-_.]+)@([^,< \n\r]+)#i'),
		array(
			'$1<a href="$2"' . $attribs . '>$2</a>',
			'$1<a href="http://$2"' . $attribs . '>$2</a>',
			'$1<a href="mailto:$2@$3">$2@$3</a>'),$ret);
	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}
?>