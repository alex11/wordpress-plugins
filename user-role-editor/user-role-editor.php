<?php
/*
Plugin Name: User Role Editor
Plugin URI: http://www.shinephp.com/user-role-editor-wordpress-plugin/
Description: It allows you to change/add/delete any WordPress user role (except administrator) capabilities list with a few clicks.
Version: 3.8.1
Author: Vladimir Garagulya
Author URI: http://www.shinephp.com
Text Domain: ure
Domain Path: /lang/
*/

/*
Copyright 2010-2012  Vladimir Garagulya  (email: vladimir@shinephp.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

if (!function_exists("get_option")) {
  die;  // Silence is golden, direct call is prohibited
}

global $wp_version, $current_user;

if (version_compare($wp_version,"3.0","<")) {
  $exit_msg = __('User Role Editor requires WordPress 3.0 or newer.', 'ure').'<a href="http://codex.wordpress.org/Upgrading_WordPress">'.__('Please update!', 'ure').'</a>';
	return ($exit_msg);
}

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
  $exit_msg = __('User Role Editor requires PHP 5.0 or newer.', 'ure').'<a href="http://codex.wordpress.org/Upgrading_WordPress">'.__('Please update!', 'ure').'</a>';
	return ($exit_msg);
}

$ure_siteURL = get_site_url();
$urePluginDirName = substr(strrchr(dirname(__FILE__), DIRECTORY_SEPARATOR), 1);

define('URE_PLUGIN_URL', WP_PLUGIN_URL.'/'.$urePluginDirName);
define('URE_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.$urePluginDirName);
define('URE_WP_ADMIN_URL', $ure_siteURL.'/wp-admin');
define('URE_ERROR', 'Error is encountered');
define('URE_SPACE_REPLACER', '_URE-SR_');
define('URE_PARENT', 'users.php');
define('URE_KEY_CAPABILITY', 'administrator');

require_once('includes/ure-lib.php');

load_plugin_textdomain('ure','', $urePluginDirName.'/lang');


function ure_optionsPage() {
  
  global $wpdb, $current_user, $ure_OptionsTable, $ure_roles, $ure_capabilitiesToSave, $ure_toldAboutBackup, 
         $ure_currentRole, $ure_currentRoleName, $ure_apply_to_all, $ure_fullCapabilities;

  if (!empty($current_user)) {
    $user_id = $current_user->ID;
  } else {
    $user_id = false;
  }
  if (!ure_is_admin($user_id)) {
    if (is_multisite()) {
      $admin = 'SuperAdministrator';
    } else {
      $admin = 'Administrator';
    }
    die(__('Only','ure').' '.$admin.' '.__('is allowed to use','ure').' '.'User Role Editor');
  }  
?>

<div class="wrap">
  <div class="icon32" id="icon-options-general"><br/></div>
    <h2><?php _e('User Role Editor', 'ure'); ?></h2>
		<?php require_once('includes/ure-options.php'); ?>
  </div>
<?php

}
// end of ure_optionsPage()


// Install plugin
function ure_install() {

  add_option('ure_caps_readable', 0);
  add_option('ure_show_deprecated_caps', 1);

}
// end of ure_install()


function ure_excludeAdminRole($roles) {

  if (isset($roles['administrator'])){
		unset( $roles['administrator'] );
	}

  return $roles;

}
// end of excludeAdminRole()


function ure_admin_jquery(){
	global $pagenow;
	if (URE_PARENT==$pagenow){
		wp_enqueue_script('jquery');
	}
}
// end of ure_admin_jquery()


// We have two vulnerable queries id users admin interface which should be processed
// 1st: http://blogdomain.com/wp-admin/user-edit.php?user_id=ID&wp_http_referer=%2Fwp-admin%2Fusers.php
// 2nd: http://blogdomain.com/wp-admin/users.php?action=delete&user=ID&_wpnonce=ab34225a78
// If put Administrator user ID into such request, user with lower capabilities (if he has 'edit_users')
// can edit, delete admin record
// This function removes 'edit_users' capability from current user capabilities
// if request has admin user ID in it
function ure_not_edit_admin($allcaps, $caps, $name) {

  global $ure_userToCheck;

  $userKeys = array('user_id', 'user');
  foreach ($userKeys as $userKey) {
    $accessDeny = false;
    if (isset($_GET[$userKey])) {
      $ure_UserId = $_GET[$userKey];
      if ($ure_UserId==1) {  // built-in WordPress Admin
        $accessDeny = true;
      } else {
        if (!isset($ure_userToCheck[$ure_UserId])) {
          // check if user_id has Administrator role
          $accessDeny = ure_has_administrator_role($ure_UserId);
        } else {
          // user_id was checked already, get result from cash
          $accessDeny = $ure_userToCheck[$ure_UserId];
        }
      }
      if ($accessDeny) {
        unset($allcaps['edit_users']);
      }
      break;
    }
  }

	return $allcaps;
}
// end of ure_not_edit_admin()


// add where criteria to exclude users with 'Administrator' role from users list
function ure_exclude_administrators($user_query) {
  
  global $wpdb;

  // get user_id of users with 'Administrator' role  
  $tableName = (!is_multisite() && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
  $meta_key = $wpdb->prefix.'capabilities';
  $admin_role_key = '%"administrator"%';
  $query = "select user_id
              from $tableName
              where meta_key='$meta_key' and meta_value like '$admin_role_key'";
  $ids_arr = $wpdb->get_col($query);
  if (is_array($ids_arr) && count($ids_arr)>0) {
    $ids = implode(',', $ids_arr);
    $user_query->query_where .= " AND ($wpdb->users.ID NOT IN ($ids))";
  }
  
}
// end of ure_exclude_administrators()


function exclude_admins_view($views) {
  
  unset($views['administrator']);

  return $views;
}
// end of exclude_admins_view()


function ure_init() {

  global $current_user, $wp_roles;
  
  if (!empty($current_user->ID)) {
    $user_id = $current_user->ID;
  } else {
    $user_id = 0;
  }
  
  // these filters and actions should prevent editing users with administrator role
  // by other users with URE_KEY_CAPABILITY capability
	if (!ure_is_admin($user_id)) {
    // Exclude administrator role from edit list.
    add_filter('editable_roles', 'ure_excludeAdminRole');
    // Enqueue jQuery
    add_action('admin_enqueue_scripts' , 'ure_admin_jquery' );
    // prohibit any actions with user who has Administrator role
    add_filter('user_has_cap', 'ure_not_edit_admin', 10, 3);
    // exclude users with 'Administrator' role from users list
    add_action('pre_user_query', 'ure_exclude_administrators');
    // do not show 'Administrator (n)' view above users list
    add_filter('views_users', 'exclude_admins_view');
  }
  
}
// end of ure_init()


function ure_plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__).'/user-role-editor.php')){
        $settings_link = "<a href='".URE_PARENT."?page=user-role-editor.php'>".__('Settings','ure')."</a>";
        array_unshift( $links, $settings_link );
    }
    return $links;
}
// end of ure_plugin_action_links


function ure_plugin_row_meta($links, $file) {
  if ($file == plugin_basename(dirname(__FILE__).'/user-role_editor.php')){
		$links[] = '<a target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#changelog">'.__('Changelog', 'ure').'</a>';
	}
	return $links;
} // end of ure_plugin_row_meta


function ure_settings_menu() {

  if (function_exists('add_submenu_page')) {
    if (!is_multisite()) {
      $keyCapability = URE_KEY_CAPABILITY;
    } else {
      if (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE==1) {
        $keyCapability = URE_KEY_CAPABILITY;
      } else {
        $keyCapability = 'manage_network_users';
      }
    }
    $ure_page = add_submenu_page('users.php', __('User Role Editor', 'ure'), __('User Role Editor', 'ure'), $keyCapability, basename(__FILE__), 'ure_optionsPage');
    add_action("admin_print_styles-$ure_page", 'ure_adminCssAction');
  }

}
// end of ure_settings_menu()

function ure_adminCssAction() {

  wp_enqueue_style('ure_admin_css', URE_PLUGIN_URL.'/css/ure-admin.css', array(), false, 'screen');

}
// end of ure_adminCssAction()


function ure_user_row($actions, $user) {
  
  global $pagenow, $current_user;

  if ($pagenow == 'users.php') {
    if (is_super_admin() || 
        (is_multisite() && defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE==1 && current_user_can('administrator'))) {
      if (isset($user->caps['administrator'])) { 
        if ($current_user->ID!=$user->ID) {
          unset($actions['edit']);
          unset($actions['delete']);
          unset($actions['remove']);
        }
      } else if ($current_user->has_cap(URE_KEY_CAPABILITY)) {
        $actions['capabilities'] = '<a href="' . wp_nonce_url("users.php?page=user-role-editor.php&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") . '">' . __('Capabilities', 'ure') . '</a>';
      }
    }
  }

  return $actions; 
}
// end of ure_user_row()


if (function_exists('is_multisite') && is_multisite()) {

// every time when new blog created - duplicate to it roles from the main blog (1) 
  function duplicate_roles_for_new_blog($blog_id, $user_id) {
    global $wpdb, $wp_roles;
    
    // get Id of 1st (main) blog
    $blogIds = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs order by blog_id asc"));
    if (!isset($blogIds[0])) {
      return;
    }
    $current_blog = $wpdb->blogid;
    switch_to_blog($blogIds[0]);
    $main_roles = new WP_Roles();  // get roles from primary blog
    $default_role = get_option('default_role');  // get default role from primary blog
    switch_to_blog($blog_id);  // switch to the new created blog
    $main_roles->use_db = false;  // do not touch DB
    $main_roles->add_cap('administrator', 'dummy_123456');   // just to save current roles into new blog
    $main_roles->role_key = $wp_roles->role_key;
    $main_roles->use_db = true;  // save roles into new blog DB
    $main_roles->remove_cap('administrator', 'dummy_123456');  // remove unneeded dummy capability
    update_option('default_role', $default_role); // set default role for new blog as it set for primary one
    switch_to_blog($current_blog);  // return to blog where we were at the begin
  }

  add_action( 'wpmu_new_blog', 'duplicate_roles_for_new_blog', 10, 2 );
  
  
  /** 
   * Filter out URE plugin from not superadmin users
   * @param type array $plugins plugins list
   * @return type array $plugins updated plugins list
   */
  function ure_exclude_from_plugins_list($plugins) {
    
    // if multi-site, then allow plugin activation for network superadmins and, if that's specially defined, - for single site administrators too    
    if (is_super_admin() || (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE==1)) {    
      return $plugins;
    }

    // exclude URE from plugins list
    foreach ($plugins as $key => $value) {
      if ($key == 'user-role-editor/user-role-editor.php') {
        unset($plugins[$key]);
      }
    }

    return $plugins;
  }
  // end of ure_exclude_from_plugins_list()
  
  add_filter( 'all_plugins', 'ure_exclude_from_plugins_list' ); 
  
}


if (is_admin()) {
  // activation action
  register_activation_hook(__FILE__, "ure_install");
  add_action('admin_init', 'ure_init');  
  // add a Settings link in the installed plugins page
  add_filter('plugin_action_links', 'ure_plugin_action_links', 10, 2);
  add_filter('plugin_row_meta', 'ure_plugin_row_meta', 10, 2);
  add_action('admin_menu', 'ure_settings_menu');
  add_action( 'user_row_actions', 'ure_user_row', 10, 2 );
}

?>
