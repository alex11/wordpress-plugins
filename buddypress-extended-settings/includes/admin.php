<?php
if (function_exists('is_multisite') && is_multisite()) {
	add_action( 'network_admin_menu', 'bp_extended_settings_plugin_menu' );
} else {
	add_action('admin_menu', 'bp_extended_settings_plugin_menu');
}

function bp_extended_settings_plugin_menu() {
	add_submenu_page( 'bp-general-settings', 'Extended Settings', 'Extended Settings', 'manage_options', 'bp-extended-settings', 'bpes_plugin_options');
	
	//call register settings function
	add_action( 'admin_init', 'bpes_register_settings' );
}

function bpes_register_settings() {
	//register our settings
	register_setting( 'bpes_plugin_options', 'theme-notice' );
	register_setting( 'bpes_plugin_options', 'thumb-size' );
	register_setting( 'bpes_plugin_options', 'avi-size' );
	register_setting( 'bpes_plugin_options', 'avisize' );
	register_setting( 'bpes_plugin_options', 'thumbsize' );
	register_setting( 'bpes_plugin_options', 'max-avisize' );
	register_setting( 'bpes_plugin_options', 'profile-default' );
	register_setting( 'bpes_plugin_options', 'profile-links' );
	register_setting( 'bpes_plugin_options', 'root-profile' );
	register_setting( 'bpes_plugin_options', 'admin-bar' );
	register_setting( 'bpes_plugin_options', 'username-compat' );
	register_setting( 'bpes_plugin_options', 'old-code' );
	register_setting( 'bpes_plugin_options', 'custom-header' );
	register_setting( 'bpes_plugin_options', 'no-gravatar' );
	register_setting( 'bpes_plugin_options', 'switch-tabs' );
	register_setting( 'bpes_plugin_options', 'direct-username' );
	register_setting( 'bpes_plugin_options', 'profile-tab-default' );
	register_setting( 'bpes_plugin_options', 'responsive-css' );
	register_setting( 'bpes_plugin_options', 'redirect-signup' );
	register_setting( 'bpes_plugin_options', 'redirect-login' );
	register_setting( 'bpes_plugin_options', 'disable-mentions' );
	
	register_setting( 'bpes_plugin_options', 'profile-tab' );
	register_setting( 'bpes_plugin_options', 'profile-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'profile-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'activity-tab' );
	register_setting( 'bpes_plugin_options', 'activity-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'activity-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'messages-tab' );
	register_setting( 'bpes_plugin_options', 'messages-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'messages-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'groups-tab' );
	register_setting( 'bpes_plugin_options', 'groups-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'groups-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'settings-tab' );
	register_setting( 'bpes_plugin_options', 'settings-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'settings-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'forums-tab' );
	register_setting( 'bpes_plugin_options', 'forums-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'forums-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'friends-tab' );
	register_setting( 'bpes_plugin_options', 'friends-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'friends-tab-text' );
	
	register_setting( 'bpes_plugin_options', 'forums-tab-remove' );
	register_setting( 'bpes_plugin_options', 'groups-tab-remove' );
	register_setting( 'bpes_plugin_options', 'friends-tab-remove' );
	register_setting( 'bpes_plugin_options', 'messages-tab-remove' );
	register_setting( 'bpes_plugin_options', 'profile-tab-remove' );
	register_setting( 'bpes_plugin_options', 'settings-tab-remove' );
	register_setting( 'bpes_plugin_options', 'activity-tab-remove' );

	register_setting( 'bpes_plugin_options', 'group-home-tab' );
	register_setting( 'bpes_plugin_options', 'group-home-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'group-home-tab-text' );
	register_setting( 'bpes_plugin_options', 'group-home-tab-remove' );
	
	register_setting( 'bpes_plugin_options', 'group-forum-tab' );
	register_setting( 'bpes_plugin_options', 'group-forum-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'group-forum-tab-text' );
	register_setting( 'bpes_plugin_options', 'group-forum-tab-remove' );

	register_setting( 'bpes_plugin_options', 'group-members-tab' );
	register_setting( 'bpes_plugin_options', 'group-members-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'group-members-tab-text' );
	register_setting( 'bpes_plugin_options', 'group-members-tab-remove' );	

	register_setting( 'bpes_plugin_options', 'group-invites-tab' );
	register_setting( 'bpes_plugin_options', 'group-invites-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'group-invites-tab-text' );
	register_setting( 'bpes_plugin_options', 'group-invites-tab-remove' );

	register_setting( 'bpes_plugin_options', 'group-admin-tab' );
	register_setting( 'bpes_plugin_options', 'group-admin-tab-arrange' );
	register_setting( 'bpes_plugin_options', 'group-admin-tab-text' );
	register_setting( 'bpes_plugin_options', 'group-admin-tab-remove' );
}

function bpes_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.', 'bpes') );
				
	}
	
$pluginpath = plugins_url();
$admin_url = site_url('/wp-admin/', 'http');

?>


			<?php if ( !empty( $_GET['updated'] ) ) : ?>
				<div id="message" class="updated">
					<p><strong><?php _e('settings saved.', 'bpes' ); ?></strong></p>
				</div>
			<?php endif; ?>


<div class="wrap">
	<h2><?php _e('BuddyPress Extended Settings', 'bpes') ?></h2>
	
	<div id="icon-buddypress" class="icon32"><br /></div>
			<h2 class="nav-tab-wrapper" style="margin-bottom: 20px;">
			<a href="<?php $admin_url ?>admin.php?page=bp-components" class="nav-tab"><?php _e('Components', 'bpes') ?></a>
			<a href="<?php $admin_url ?>admin.php?page=bp-page-settings" class="nav-tab"><?php _e('Pages', 'bpes') ?></a>
			<a href="<?php $admin_url ?>admin.php?page=bp-settings" class="nav-tab"><?php _e('Settings', 'bpes') ?></a>
			<a href="<?php $admin_url ?>admin.php?page=bb-forums-setup" class="nav-tab"><?php _e('Forums', 'bpes') ?></a>
			<a href="<?php $admin_url ?>admin.php?page=bp-extended-settings" class="nav-tab nav-tab-active"><?php _e('Extended Settings', 'bpes') ?></a>
			</h2>
	
	<form method="post" action="<?php echo admin_url('options.php');?>">
	<?php wp_nonce_field('update-options'); ?>
	<h2><?php _e('General Settings', 'bpes') ?></h2>
	<table class="widefat fixed plugins" cellspacing="0"">
	<thead>
		<tr>
			<th width="30px"></th>
			<th width="190px"><?php _e('Setting', 'bpes') ?></th>
			<th><?php _e('Description', 'bpes') ?></th>
		</tr>
	</thead>
	<tbody id="the-list">
	
	<tr>
	<th scope="row"><input type="checkbox" name="theme-notice" value="1" <?php if (get_option('theme-notice')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Theme Notice', 'bpes') ?></strong></td>
	<td><?php _e('Stop theme notice when using a none BuddyPress Theme', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="responsive-css" value="1" <?php if (get_option('responsive-css')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Responsive CSS', 'bpes') ?></strong></td>
	<td><?php _e('Disable responsive CSS on BP-Default', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="old-code" value="1" <?php if (get_option('old-code')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Deprecated Code', 'bpes') ?></strong></td>
	<td><?php _e('Don\'t load deprecated code', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="disable-mentions" value="1" <?php if (get_option('disable-mentions')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Disable @ Mentions', 'bpes') ?></strong></td>
	<td><?php _e('Turn off @mentions in activity streams and forum topics, removes mentions tab from profile', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="username-compat" value="1" <?php if (get_option('username-compat')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Username Compatability', 'bpes') ?></strong></td>
	<td><?php _e('Allow special characters and uppercase letters in usernames', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="custom-header" value="1" <?php if (get_option('custom-header')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Custom Header', 'bpes') ?></strong></td>
	<td><?php _e('Disable custom header', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="admin-bar" value="1" <?php if (get_option('admin-bar')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Admin Bar', 'bpes') ?></strong></td>
	<td><?php _e('Disable WordPress admin bar', 'bpes') ?></td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="no-gravatar" value="1" <?php if (get_option('no-gravatar')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Gravatars', 'bpes') ?></strong></td>
	<td><?php _e('Turn off Gravatars', 'bpes') ?></td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="direct-username" value="1" <?php if (get_option('direct-username')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Directory Username') ?></strong></td>
	<td><?php _e('Show usernames in member directory instead of profile field name', 'bpes') ?></td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="redirect-signup" value="1" <?php if (get_option('redirect-signup')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Spam Signup') ?></strong></td>
	<td><?php _e('Prevent spam sign ups by redirecting wp-signup.php to BuddyPress register slug', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="redirect-login" value="1" <?php if (get_option('redirect-login')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('WordPress login') ?></strong></td>
	<td><?php _e('Prevent access to the WordPress login on signin fail. Redirects to home.', 'bpes') ?></td>
	</tr>

	
	<tr>
	<th scope="row"><input type="checkbox" name="thumbsize" value="1" <?php if (get_option('thumbsize')==1) echo 'checked="checked"'; ?></th>
	<td><strong><?php _e('Thumbnail Avatar Size', 'bpes') ?></strong></td>
	<td>
		<select name="thumb-size" style="width:100px;">
			<option value="50" <?php if (get_option('avi-size')==50) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="25" <?php if (get_option('thumb-size')==25) echo 'selected="selected"'; ?> >25px</option>
			<option value="30" <?php if (get_option('thumb-size')==30) echo 'selected="selected"'; ?> >30px</option>
			<option value="33" <?php if (get_option('thumb-size')==35) echo 'selected="selected"'; ?> >35px</option>
			<option value="40" <?php if (get_option('thumb-size')==40) echo 'selected="selected"'; ?> >40px</option>
			<option value="45" <?php if (get_option('thumb-size')==45) echo 'selected="selected"'; ?> >45px</option>
			<option value="50" <?php if (get_option('thumb-size')==50) echo 'selected="selected"'; ?> >50px</option>
			<option value="55" <?php if (get_option('thumb-size')==55) echo 'selected="selected"'; ?> >55px</option>
			<option value="60" <?php if (get_option('thumb-size')==60) echo 'selected="selected"'; ?> >60px</option>
			<option value="65" <?php if (get_option('thumb-size')==65) echo 'selected="selected"'; ?> >65px</option>
		</select>
	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="avisize" value="1" <?php if (get_option('avisize')==1) echo 'checked="checked"'; ?></th>
	<td><strong><?php _e('Avatar Size', 'bpes') ?></strong></td>
	<td>
		<select name="avi-size" style="width:100px;">
			<option value="150" <?php if (get_option('avi-size')==150) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="25" <?php if (get_option('avi-size')==25) echo 'selected="selected"'; ?> >25px</option>
			<option value="50" <?php if (get_option('avi-size')==50) echo 'selected="selected"'; ?> >50px</option>
			<option value="75" <?php if (get_option('avi-size')==75) echo 'selected="selected"'; ?> >75px</option>
			<option value="100" <?php if (get_option('avi-size')==100) echo 'selected="selected"'; ?> >100px</option>
			<option value="125" <?php if (get_option('avi-size')==125) echo 'selected="selected"'; ?> >125px</option>
			<option value="150" <?php if (get_option('avi-size')==150) echo 'selected="selected"'; ?> >150px</option>
			<option value="175" <?php if (get_option('avi-size')==175) echo 'selected="selected"'; ?> >175px</option>
			<option value="200" <?php if (get_option('avi-size')==200) echo 'selected="selected"'; ?> >200px</option>
			<option value="225" <?php if (get_option('avi-size')==225) echo 'selected="selected"'; ?> >225px</option>	
		</select>
	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="max-avisize" value="1" <?php if (get_option('max-avisize')==1) echo 'checked="checked"'; ?></th>
	<td><strong><?php _e('Max Avatar Size', 'bpes') ?></strong></td>
	<td>
		<select name="avi-size" style="width:100px;">
			<option value="640" <?php if (get_option('max-avisize')==640) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="150" <?php if (get_option('max-avisize')==150) echo 'selected="selected"'; ?> >150px</option>
			<option value="300" <?php if (get_option('max-avisize')==300) echo 'selected="selected"'; ?> >300px</option>
			<option value="640" <?php if (get_option('max-avisize')==640) echo 'selected="selected"'; ?> >640px</option>	
		</select>
	</td>
	</tr>
	
	</tbody>
	
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	</table>
	<h2><?php _e('Profile Settings', 'bpes') ?></h2>
		<table class="widefat fixed plugins" cellspacing="0"">
	<thead>
		<tr>
			<th width="30px"></th>
			<th width="190px"><?php _e('Setting', 'bpes') ?></th>
			<th><?php _e('Description', 'bpes') ?></th>
		</tr>
	</thead>
	<tbody id="the-list">

	<tr>
	<th scope="row"><input type="checkbox" name="profile-links" value="1" <?php if (get_option('profile-links')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Profile Field Links', 'bpes') ?></strong></td>
	<td><?php _e('Disable auto linking of user profile fields', 'bpes') ?></td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="root-profile" value="1" <?php if (get_option('root-profile')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Root Profiles', 'bpes') ?></strong></td>
	<td><?php _e('Show user profiles at site root url/membername', 'bpes') ?></td>
	</tr>
		
	<tr>
	<th scope="row"><input type="checkbox" name="profile-default" value="1" <?php if (get_option('profile-default')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Default Profile Tab', 'bpes') ?></strong></td>
	<td>
		<select name="profile-tab-default" style="width:100px;">
			<option value="activity" <?php if (get_option('profile-tab-default')=='activity') echo 'selected="selected"'; ?> ><?php _e('Activity', 'bpes') ?></option>
			<option value="profile" <?php if (get_option('profile-tab-default')=='profile') echo 'selected="selected"'; ?> ><?php _e('Profile', 'bpes') ?></option>
			<option value="friends" <?php if (get_option('profile-tab-default')=='friends') echo 'selected="selected"'; ?> ><?php _e('Friends', 'bpes') ?></option>
			<option value="messages" <?php if (get_option('profile-tab-default')=='messages') echo 'selected="selected"'; ?> ><?php _e('Messages', 'bpes') ?></option>
			<option value="groups" <?php if (get_option('profile-tab-default')=='groups') echo 'selected="selected"'; ?> ><?php _e('Groups', 'bpes') ?></option>
			<option value="forums" <?php if (get_option('profile-tab-default')=='forums') echo 'selected="selected"'; ?> ><?php _e('Forums', 'bpes') ?></option>
			<option value="settings" <?php if (get_option('profile-tab-default')=='settings') echo 'selected="selected"'; ?> ><?php _e('Settings', 'bpes') ?></option>
		</select>		
	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="profile-tab-arrange" value="1" <?php if (get_option('profile-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Profile Tab', 'bpes') ?></strong></td>
	<td>
		<select name="profile-tab" style="width:100px;">
			<option value="20" <?php if (get_option('profile-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Default') ?></option>
			<option value="10" <?php if (get_option('profile-tab')==10) echo 'selected="selected"'; ?> >Position 1</option>
			<option value="20" <?php if (get_option('profile-tab')==20) echo 'selected="selected"'; ?> >Position 2</option>
			<option value="30" <?php if (get_option('profile-tab')==30) echo 'selected="selected"'; ?> >Position 3</option>
			<option value="40" <?php if (get_option('profile-tab')==40) echo 'selected="selected"'; ?> >Position 4</option>
			<option value="50" <?php if (get_option('profile-tab')==50) echo 'selected="selected"'; ?> >Position 5</option>
			<option value="60" <?php if (get_option('profile-tab')==60) echo 'selected="selected"'; ?> >Position 6</option>
			<option value="70" <?php if (get_option('profile-tab')==70) echo 'selected="selected"'; ?> >Position 7</option>
		</select>	
		
		<input type="text" name="profile-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('profile-tab-text')==true) echo get_option('profile-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="profile-tab-remove" value="1" <?php if (get_option('profile-tab-remove')==1) echo 'checked="checked"'; ?>/>
	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="activity-tab-arrange" value="1" <?php if (get_option('activity-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Activity Tab', 'bpes') ?></strong></td>
	<td>
		<select name="activity-tab" style="width:100px;">
			<option value="10" <?php if (get_option('activity-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('activity-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('activity-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('activity-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('activity-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('activity-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('activity-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('activity-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="activity-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('activity-tab-text')==true) echo get_option('activity-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="activity-tab-remove" value="1" <?php if (get_option('activity-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="messages-tab-arrange" value="1" <?php if (get_option('messages-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Messages Tab', 'bpes') ?></strong></td>
	<td>
		<select name="messages-tab" style="width:100px;">
			<option value="40" <?php if (get_option('messages-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('messages-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('messages-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('messages-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('messages-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('messages-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('messages-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('messages-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="messages-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('messages-tab-text')==true) echo get_option('messages-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="messages-tab-remove" value="1" <?php if (get_option('messages-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="friends-tab-arrange" value="1" <?php if (get_option('friends-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Friends Tab', 'bpes') ?></strong></td>
	<td>
		<select name="friends-tab" style="width:100px;">
			<option value="30" <?php if (get_option('friends-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('friends-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('friends-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('friends-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('friends-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('friends-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('friends-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('friends-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="friends-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('friends-tab-text')==true) echo get_option('friends-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="friends-tab-remove" value="1" <?php if (get_option('friends-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>


	<tr>
	<th scope="row"><input type="checkbox" name="groups-tab-arrange" value="1" <?php if (get_option('groups-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Groups Tab', 'bpes') ?></strong></td>
	<td>
		<select name="groups-tab" style="width:100px;">
			<option value="50" <?php if (get_option('groups-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Default 1', 'bpes') ?></option>
			<option value="10" <?php if (get_option('groups-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('groups-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('groups-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('groups-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('groups-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('groups-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('groups-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="groups-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('groups-tab-text')==true) echo get_option('groups-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="groups-tab-remove" value="1" <?php if (get_option('groups-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>
	
	<tr>
	<th scope="row"><input type="checkbox" name="forums-tab-arrange" value="1" <?php if (get_option('forums-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Forums Tab', 'bpes') ?></strong></td>
	<td>
		<select name="forums-tab" style="width:100px;">
			<option value="60" <?php if (get_option('forums-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('forums-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('forums-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('forums-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('forums-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('forums-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('forums-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('forums-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="forums-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('forums-tab-text')==true) echo get_option('forums-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="forums-tab-remove" value="1" <?php if (get_option('forums-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="settings-tab-arrange" value="1" <?php if (get_option('settings-tab-arrange')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Settings Tab', 'bpes') ?></strong></td>
	<td>
		<select name="settings-tab" style="width:100px;">
			<option value="70" <?php if (get_option('settings-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('settings-tab')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('settings-tab')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('settings-tab')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('settings-tab')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('settings-tab')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('settings-tab')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('settings-tab')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="settings-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('settings-tab-text')==true) echo get_option('settings-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="settings-tab-remove" value="1" <?php if (get_option('settings-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

	
	</tbody>
	
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	</table>

	</table>
	<h2><?php _e('Group Settings', 'bpes') ?></h2>
		<table class="widefat fixed plugins" cellspacing="0"">
	<thead>
		<tr>
			<th width="30px"></th>
			<th width="190px"><?php _e('Setting', 'bpes') ?></th>
			<th><?php _e('Description', 'bpes') ?></th>
		</tr>
	</thead>
	<tbody id="the-list">

	<tr>
	<th scope="row"><input type="checkbox" name="group-home-tab" value="1" <?php if (get_option('group-home-tab')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Home Tab', 'bpes') ?></strong></td>
	<td>
		<select name="group-home-tab-arrange" style="width:100px;">
			<option value="70" <?php if (get_option('group-home-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('group-home-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('group-home-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('group-home-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('group-home-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('group-home-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('group-home-tab-arrange')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('group-home-tab-arrange')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="group-home-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('group-home-tab-text')==true) echo get_option('group-home-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="group-home-tab-remove" value="1" <?php if (get_option('group-home-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="group-forum-tab" value="1" <?php if (get_option('group-forum-tab')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Forum Tab', 'bpes') ?></strong></td>
	<td>
		<select name="group-forum-tab-arrange" style="width:100px;">
			<option value="70" <?php if (get_option('group-forum-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('group-forum-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('group-forum-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('group-forum-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('group-forum-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('group-forum-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('group-forum-tab-arrange')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('group-forum-tab-arrange')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="group-forum-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('group-forum-tab-text')==true) echo get_option('group-forum-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="group-forum-tab-remove" value="1" <?php if (get_option('group-forum-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

		<tr>
	<th scope="row"><input type="checkbox" name="group-members-tab" value="1" <?php if (get_option('group-members-tab')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Members Tab', 'bpes') ?></strong></td>
	<td>
		<select name="group-members-tab-arrange" style="width:100px;">
			<option value="70" <?php if (get_option('group-members-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('group-members-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('group-members-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('group-members-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('group-members-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('group-members-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('group-members-tab-arrange')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('group-members-tab-arrange')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="group-members-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('group-members-tab-text')==true) echo get_option('group-members-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="group-members-tab-remove" value="1" <?php if (get_option('group-members-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

	<tr>
	<th scope="row"><input type="checkbox" name="group-invites-tab" value="1" <?php if (get_option('group-invites-tab')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Invites Tab', 'bpes') ?></strong></td>
	<td>
		<select name="group-invites-tab-arrange" style="width:100px;">
			<option value="70" <?php if (get_option('group-invites-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('group-invites-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('group-invites-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('group-invites-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('group-invites-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('group-invites-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('group-invites-tab-arrange')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('group-invites-tab-arrange')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="group-invites-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('group-invites-tab-text')==true) echo get_option('group-invites-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="group-invites-tab-remove" value="1" <?php if (get_option('group-invites-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>

		<tr>
	<th scope="row"><input type="checkbox" name="group-admin-tab" value="1" <?php if (get_option('group-admin-tab')==1) echo 'checked="checked"'; ?>/></th>
	<td><strong><?php _e('Admin Tab', 'bpes') ?></strong></td>
	<td>
		<select name="group-admin-tab-arrange" style="width:100px;">
			<option value="70" <?php if (get_option('group-admin-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Default', 'bpes') ?></option>
			<option value="10" <?php if (get_option('group-admin-tab-arrange')==10) echo 'selected="selected"'; ?> ><?php _e('Position 1', 'bpes') ?></option>
			<option value="20" <?php if (get_option('group-admin-tab-arrange')==20) echo 'selected="selected"'; ?> ><?php _e('Position 2', 'bpes') ?></option>
			<option value="30" <?php if (get_option('group-admin-tab-arrange')==30) echo 'selected="selected"'; ?> ><?php _e('Position 3', 'bpes') ?></option>
			<option value="40" <?php if (get_option('group-admin-tab-arrange')==40) echo 'selected="selected"'; ?> ><?php _e('Position 4', 'bpes') ?></option>
			<option value="50" <?php if (get_option('group-admin-tab-arrange')==50) echo 'selected="selected"'; ?> ><?php _e('Position 5', 'bpes') ?></option>
			<option value="60" <?php if (get_option('group-admin-tab-arrange')==60) echo 'selected="selected"'; ?> ><?php _e('Position 6', 'bpes') ?></option>
			<option value="70" <?php if (get_option('group-admin-tab-arrange')==70) echo 'selected="selected"'; ?> ><?php _e('Position 7', 'bpes') ?></option>
		</select>	
		
		<input type="text" name="group-admin-tab-text" placeholder="<?php _e('Tab Text', 'bpes') ?>" value="<?php if (get_option('group-admin-tab-text')==true) echo get_option('group-admin-tab-text'); ?>">
		
		<strong><?php _e('Remove Tab', 'bpes') ?></strong>
		<input type="checkbox" name="group-admin-tab-remove" value="1" <?php if (get_option('group-admin-tab-remove')==1) echo 'checked="checked"'; ?>/>

	</td>
	</tr>
	
	</tbody>
	
	<tfoot>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</tfoot>
	</table>


	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="theme-notice,thumb-size,avi-size,avisize,thumbsize,profile-default,profile-links,root-profile,username-compat,old-code,admin-bar,custom-header,max-avisize,no-gravatar,switch-tabs,direct-username,profile-tab,profile-tab-default,profile-tab-arrange,profile-tab-text,activity-tab-arrange,activity-tab-text,activity-tab,messages-tab-arrange,messages-tab-text,messages-tab,groups-tab,groups-tab-arrange,groups-tab-text,settings-tab,settings-tab-arrange,settings-tab-text,forums-tab,forums-tab-arrange,forums-tab-text,friends-tab,friends-tab-arrange,friends-tab-text,activity-tab-remove,groups-tab-remove,forums-tab-remove,messages-tab-remove,settings-tab-remove,profile-tab-remove,friends-tab-remove,responsive-css,redirect-signup,redirect-login,group-home-tab,group-forum-tab,group-members-tab,group-invites-tab,group-admin-tab,group-home-tab-arrange,group-home-tab-text,group-home-tab-remove,group-forum-tab-arrange,group-forum-tab-text,group-forum-tab-remove,group-invites-tab-arrange,group-invites-tab-text,group-invites-tab-remove,group-admin-tab-arrange,group-admin-tab-text,group-admin-tab-remove,group-members-tab-arrange,group-members-tab-text,group-members-tab-remove,disable-mentions" />
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'bpes') ?>" />
	</p>
	
	</form>
</div>
<?php
}
?>