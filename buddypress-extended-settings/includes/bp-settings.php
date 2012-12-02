<?php
$thumb_size = get_option('thumb-size');
$avi_size = get_option('avi-size');
$max_avi_size = get_option('max-avisize');
$default_profile_tab = get_option('profile-tab-default');



//ignore old code
if (get_option('old-code')==1){
	define ( 'BP_IGNORE_DEPRECATED', true );
}

// root profiles
if (get_option('root-profile')==1){
	add_filter( 'bp_core_enable_root_profiles', '__return_true' );
}

// turn off gravatar
if (get_option('no-gravatar')==1){
	add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
}

//thumbnail sizes
if (get_option('thumbsize')==1){
	define ( 'BP_AVATAR_THUMB_WIDTH', $thumb_size );
	define ( 'BP_AVATAR_THUMB_HEIGHT', $thumb_size );
}

// avatar sizes
if (get_option('avisize')==1){
	define ( 'BP_AVATAR_FULL_WIDTH', $avi_size );
	define ( 'BP_AVATAR_FULL_HEIGHT', $avi_size );
}

// maximum avatar size
if (get_option('max-avisize')==1){
	define ( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', $max_avi_size );
}

//allows special characters in usernames
if (get_option('username-compat')==1){
	define( 'BP_ENABLE_USERNAME_COMPATIBILITY_MODE', true );
}

// silences nagging theme notice
if (get_option('theme-notice')==1){
	define( 'BP_SILENCE_THEME_NOTICE', true );
}

// remove custom header option
if (get_option('custom-header')==1){
	define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
}

// uses profile as default profile tab
if (get_option('profile-default')==1 && get_option('profile-tab-default')== true){
	define( 'BP_DEFAULT_COMPONENT', $default_profile_tab );
//define ( 'BP_XPROFILE_SLUG', 'info' );

}

// disables user @ mentions
if (get_option('disable-mentions')== 1){
	function remove_user_mentions() {
	// removes @mention links in updates, forum posts, etc.
	remove_filter( 'bp_activity_after_save', 'bp_activity_at_name_filter_updates' );
	remove_filter( 'groups_activity_new_update_content', 'bp_activity_at_name_filter' );
	remove_filter( 'pre_comment_content', 'bp_activity_at_name_filter' );
	remove_filter( 'group_forum_topic_text_before_save', 'bp_activity_at_name_filter' );
	remove_filter( 'group_forum_post_text_before_save', 'bp_activity_at_name_filter' );
	remove_filter( 'bp_activity_comment_content', 'bp_activity_at_name_filter' );
	
	// remove @mention email notifications
	remove_action( 'bp_activity_posted_update', 'bp_activity_at_message_notification', 10, 3 );
	remove_action( 'bp_groups_posted_update', 'groups_at_message_notification', 10, 4 );
	}
	add_action( 'bp_init', 'remove_user_mentions' );

}

// remove profile field links
if (get_option('profile-links')==1){
	function remove_xprofile_links() {
		remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
	}
	add_action( 'bp_init', 'remove_xprofile_links' );
}

//remove admin bar
if (get_option('admin-bar')==1){
	add_filter( 'show_admin_bar', 'hide_admin_bar' );
}

//disable responsive css
if (get_option('responsive-css')==1){
	function bpes_enqueue_styles() {
		remove_theme_support( 'bp-default-responsive' );
	}
	add_action( 'wp_enqueue_scripts', 'bpes_enqueue_styles', 5 );
}

function bpse_change_profile_tab_order() {
	global $bp;
	
	//$bp->bp_nav['activity']['link'] = $bp->activity->slug . '/my-stream-activity';
	
	if (get_option('profile-tab-arrange') == true){
		$bp->bp_nav['profile']['position'] = get_option('profile-tab');
		
		if (get_option('profile-tab-text') == true){
		$bp->bp_nav['profile']['name'] = get_option('profile-tab-text');
		}
	}
	
	if (get_option('activity-tab-arrange') == true){
		$bp->bp_nav['activity']['position'] = get_option('activity-tab');
		
		if (get_option('activity-tab-text') == true){
		$bp->bp_nav['activity']['name'] = get_option('activity-tab-text');
		}
	}
	
	if (get_option('messages-tab-arrange') == true){
		$bp->bp_nav['messages']['position'] = get_option('messages-tab');
		
		if (get_option('messages-tab-text') == true){
		$bp->bp_nav['messages']['name'] = get_option('messages-tab-text');
		}
	}
	
	if (get_option('groups-tab-arrange') == true){
		$bp->bp_nav['groups']['position'] = get_option('groups-tab');
		
		if (get_option('groups-tab-text') == true){
		$bp->bp_nav['groups']['name'] = get_option('groups-tab-text');
		}
	}
	
	if (get_option('friends-tab-arrange') == true){
		$bp->bp_nav['friends']['position'] = get_option('friends-tab');
		
		if (get_option('friends-tab-text') == true){
		$bp->bp_nav['friends']['name'] = get_option('friends-tab-text');
		}
	}
	
	if (get_option('settings-tab-arrange') == true){
		$bp->bp_nav['settings']['position'] = get_option('settings-tab');
		
		if (get_option('settings-tab-text') == true){
		$bp->bp_nav['settings']['name'] = get_option('settings-tab-text');
		}
	}
	
	if (get_option('forums-tab-arrange') == true){
		$bp->bp_nav['forum']['position'] = get_option('forums-tab');
		
		if (get_option('forums-tab-text') == true){
		$bp->bp_nav['forum']['name'] = get_option('forums-tab-text');
		}
	}
}
add_action( 'bp_init', 'bpse_change_profile_tab_order', 999 );


function bpes_remove_xprofile_tabs(){
	global $bp;

	if (get_option('forums-tab-remove') == true){
		bp_core_remove_nav_item( 'forums' );
	}
	
	if (get_option('activity-tab-remove') == true){
		bp_core_remove_nav_item( 'activity' );
	}
	
	if (get_option('groups-tab-remove') == true){
		bp_core_remove_nav_item( 'groups' );
	}
	
	if (get_option('settings-tab-remove') == true){
		bp_core_remove_nav_item( 'settings' );
	}
	
	if (get_option('friends-tab-remove') == true){
		bp_core_remove_nav_item( 'friends' );
	}
	
	if (get_option('messages-tab-remove') == true){
		bp_core_remove_nav_item( 'messages' );
	}
	
	if (get_option('profile-tab-remove') == true){
		bp_core_remove_nav_item( 'profile' );
	}
	
	if (get_option('disable-mentions') == true){
		bp_core_remove_subnav_item( $bp->activity->slug, 'mentions' );
	}

	
}
add_action( 'bp_setup_nav', 'bpes_remove_xprofile_tabs', 15 );

function bpes_change_group_tab_order(){
	global $bp;
	
	if (get_option('group-home-tab') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['position'] = get_option('group-home-tab-arrange');
		
		if (get_option('group-home-tab-text') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = get_option('group-home-tab-text');
		}
	}

	if (get_option('group-forum-tab') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum']['position'] = get_option('group-forum-tab-arrange');
		
		if (get_option('group-forum-tab-text') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum']['name'] = get_option('group-forum-tab-text');
		}
	}

	if (get_option('group-invites-tab') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites']['position'] = get_option('group-invites-tab-arrange');
		
		if (get_option('group-invites-tab-text') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites']['name'] = get_option('group-invites-tab-text');
		}
	}

	if (get_option('group-members-tab') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members']['position'] = get_option('group-members-tab-arrange');
		
		if (get_option('group-members-tab-text') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members']['name'] = get_option('group-members-tab-text');
		}
	}

	if (get_option('group-admin-tab') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin']['position'] = get_option('group-admin-tab-arrange');
		
		if (get_option('group-admin-tab-text') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin']['name'] = get_option('group-admin-tab-text');
		}
	}

}
add_action( 'bp_init', 'bpes_change_group_tab_order' );


function bpes_remove_group_tabs(){
	global $bp;

	if (get_option('group-forum-tab-remove') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum'] = false;
	}
	
	if (get_option('group-home-tab-remove') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home'] = false;
	}
	
	if (get_option('group-invites-tab-remove') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites'] = false;
	}
	
	if (get_option('group-members-tab-remove') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members'] = false;
	}
	
	if (get_option('group-admin-tab-remove') == true){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin'] = false;
	}
	
}
add_action( 'bp_init', 'bpes_remove_group_tabs' );


// display username in member directory
if (get_option('direct-username')==1){
	function bpse_member_username() {
		global $members_template;
		return $members_template->member->user_login;
	}
	add_filter('bp_member_name','bpse_member_username');
}

//force wp-signup.php to bp register slug
if (get_option('redirect-signup')==1){
	function bpse_splog_signup_redirect() {
	global $bp;
	$regurl = BP_REGISTER_SLUG;
	        if (strpos($_SERVER['REQUEST_URI'], 'wp-signup.php') !== false ) {
	                $url = '/'. $regurl;
	                wp_redirect($url);
	                exit;
	        }
	}
	add_action('init', 'bpse_splog_signup_redirect');
}

//force wp-login.php to home page when username/password invalid
if (get_option('redirect-login')==1){



function redirect_login(){

if (empty($_POST['log']) && empty($_POST['pwd'])  ){

wp_redirect( home_url('/') );

} else {
	wp_redirect( home_url('/?login=false') );
}
}


add_action( 'login_form_login', 'redirect_login' );





/**
 * When a user logs out, send them back to the home page
 */
function wpse25628_catch_logout()
{
    wp_redirect(
        home_url(),
        302
    );
    exit();
}
add_action( 'wp_logout', 'wpse25628_catch_logout' );


}

function bpes_login_fail_notice() {
$referrer = $_GET['login'];
 if ($referrer == 'false'){
	 echo '<div id="message" class="error"><p>'; 
	 _e('Login Failed', 'bpes');
	 echo '</p></div>';
 } else {
 	
 }

}
add_action ( 'bp_before_sidebar_login_form', 'bpes_login_fail_notice'); 
?>