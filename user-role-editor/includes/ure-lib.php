<?php
/* 
 * * User Role Editor plugin Library for general staff
 * Author: Vladimir Garagulya vladimir@shinephp.com
 * 
 */


if (!function_exists("get_option")) {
  die;  // Silence is golden, direct call is prohibited
}

$ure_roles = false; $ure_capabilitiesToSave = false; 
$ure_currentRole = false; $ure_currentRoleName = false;
$ure_toldAboutBackup = false; $ure_apply_to_all = false; 
$ure_userToEdit = false; $ure_fullCapabilities = false;

// this array will be used to cash users checked for Administrator role
$ure_userToCheck = array();

function ure_logEvent($message, $showMessage = false) {
  include(ABSPATH .'wp-includes/version.php');

  $fileName = URE_PLUGIN_DIR.'/user-role-editor.log';
  $fh = fopen($fileName,'a');
  $cr = "\n";
  $s = $cr.date("d-m-Y H:i:s").$cr.
      'WordPress version: '.$wp_version.', PHP version: '.phpversion().', MySQL version: '.mysql_get_server_info().$cr;
  fwrite($fh, $s);
  fwrite($fh, $message.$cr);
  fclose($fh);

  if ($showMessage) {
    ure_showMessage('Error! '.__('Error is occur. Please check the log file.', 'ure'));
  }
}
// end of ure_logEvent()


// returns true is user has Role "Administrator"
function ure_has_administrator_role($user_id) {
  global $wpdb, $ure_userToCheck;

  if (empty($user_id) || !is_numeric($user_id)) {
    return false;
  }

  $tableName = (!is_multisite() && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
  $metaKey = $wpdb->prefix.'capabilities';
  $query = "SELECT count(*)
                FROM $tableName
                WHERE user_id=$user_id AND meta_key='$metaKey' AND meta_value like '%administrator%'";
  $hasAdminRole = $wpdb->get_var($query);
  if ($hasAdminRole>0) {
    $result = true;
  } else {
    $result = false;
  }
  $ure_userToCheck[$user_id] = $result;
  
  return $result;
}
// end of ure_has_administrator_role()


// true if user is superadmin under multi-site environment or has administrator role
function ure_is_admin( $user_id = false ) {
  global $current_user;

	if ( ! $user_id ) {
    if (empty($current_user) && function_exists('get_currentuserinfo')) {
      get_currentuserinfo();
    }
		$user_id = ! empty($current_user) ? $current_user->ID : 0;
	}

	if ( ! $user_id )
		return false;

	$user = new WP_User($user_id);

  $simpleAdmin = ure_has_administrator_role($user_id);

	if ( is_multisite() ) {
		$super_admins = get_super_admins();
		$superAdmin =  is_array( $super_admins ) && in_array( $user->user_login, $super_admins );
	} else {
    $superAdmin = false;
  }

	return $simpleAdmin || $superAdmin;
}
// end of ure_is_super_admin()


function ure_optionSelected($value, $etalon) {
  $selected = '';
  if (strcasecmp($value,$etalon)==0) {
    $selected = 'selected="selected"';
  }

  return $selected;
}
// end of ure_optionSelected()


function ure_showMessage($message) {

  if ($message) {
    if (strpos(strtolower($message), 'error')===false) {
      $class = 'updated fade';
    } else {
      $class = 'error';
    }
    echo '<div class="'.$class.'" style="margin:0;">'.$message.'</div><br style="clear: both;"/>';
  }

}
// end of ure_showMessage()


function ure_getUserRoles() {
  global $wp_roles, $wp_user_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	}

	$ure_roles = $wp_roles->roles;
  if (is_array($ure_roles)) {
    asort($ure_roles);
  }
  	
  return $ure_roles;
}
// end of ure_getUserRoles()


// restores User Roles from the backup record
function ure_restore_user_roles() {

  global $wpdb, $wp_roles;

  $errorMessage = 'Error! '.__('Database operation error. Check log file.', 'ure');
  $option_name = $wpdb->prefix.'user_roles';
  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  $query = "select option_value
              from $wpdb->options
              where option_name='$backup_option_name'
              limit 0, 1";
  $option_value = $wpdb->get_var($query);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error, true);
    return $errorMessage;
  }
  if ($option_value) {
    $query = "update $wpdb->options
                    set option_value='$option_value'
                    where option_name='$option_name'
                    limit 1";
    $record = $wpdb->query($query);
    if ($wpdb->last_error) {
        ure_logEvent($wpdb->last_error, true);
        return $errorMessage;
    }
    $wp_roles = new WP_Roles();
    $reload_link = wp_get_referer();
    $reload_link = remove_query_arg('action', $reload_link);
    $reload_link = add_query_arg('action', 'roles_restore_note', $reload_link);
?>    
<script type="text/javascript" >
  document.location = '<?php echo $reload_link; ?>';
</script>  
<?php    
    $mess = '';
  } else {
    $mess = __('No backup data. It is created automatically before the first role data update.', 'ure');
  }
  if (isset($_REQUEST['user_role'])) {
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of ure_restore_user_roles()


function ure_makeRolesBackup() {
  global $wpdb, $mess, $ure_roles, $ure_toldAboutBackup;

  // check if backup user roles record exists already
  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  $query = "select option_id
              from $wpdb->options
              where option_name='$backup_option_name'
          limit 0, 1";
  $option_id = $wpdb->get_var($query);
  if ($wpdb->last_error) {
    ure_logEvent($wpdb->last_error, true);
    return false;
  }
  if (!$option_id) {
    // create user roles record backup
    $serialized_roles = mysql_real_escape_string(serialize($ure_roles));
    $query = "insert into $wpdb->options
                (option_name, option_value, autoload)
                values ('$backup_option_name', '$serialized_roles', 'yes')";
    $record = $wpdb->query($query);
    if ($wpdb->last_error) {
      ure_logEvent($wpdb->last_error, true);
      return false;
    }
    if (!$ure_toldAboutBackup) {
      $ure_toldAboutBackup = true;
      $mess .= __('Backup record is created for the current role capabilities', 'ure');
    }
  }

  return true;
}
// end of ure_makeRolesBackup()


// Save Roles to database
function ure_saveRolesToDb() {
  global $wpdb, $ure_roles, $ure_capabilitiesToSave, $ure_currentRole, $ure_currentRoleName;

  if (!isset($ure_roles[$ure_currentRole])) {
    $ure_roles[$ure_currentRole]['name'] = $ure_currentRoleName;
  }
  $ure_roles[$ure_currentRole]['capabilities'] = $ure_capabilitiesToSave;
  $option_name = $wpdb->prefix.'user_roles';
  
  $result = update_option($option_name, $ure_roles);
  
  return $result;
}
// end of saveRolesToDb()


function ure_direct_site_roles_update($blogIds) {
  global $wpdb, $table_prefix, $ure_roles, $ure_capabilitiesToSave, $ure_currentRole, $ure_currentRoleName;

  if (!isset($ure_roles[$ure_currentRole])) {
    $ure_roles[$ure_currentRole]['name'] = $ure_currentRoleName;
  }
  $ure_roles[$ure_currentRole]['capabilities'] = $ure_capabilitiesToSave;
  $serialized_roles = serialize($ure_roles);  
  foreach ($blogIds as $blog_id) {
    $prefix = $wpdb->get_blog_prefix($blog_id);
    $options_table_name = $prefix.'options';
    $option_name = $prefix.'user_roles';
    $query = "update $options_table_name
                set option_value='$serialized_roles'
                where option_name='$option_name'
                limit 1";
    $record = $wpdb->query($query);
    if ($wpdb->last_error) {
      ure_logEvent($wpdb->last_error, true);
      return false;
    }
    if ($record==0) {
     return false;
    }
  }
  
}
// end of ure_direct_site_roles_update()


function ure_updateRoles() {
  global $wpdb, $ure_apply_to_all, $ure_roles, $ure_toldAboutBackup;
  
  $ure_toldAboutBackup = false;
  if (is_multisite() && is_super_admin() && $ure_apply_to_all) {  // update Role for the all blogs/sites in the network (permitted to superadmin only)
    
    if (defined('URE_DEBUG') && URE_DEBUG) {
     $time_shot = microtime();
    }
    
    $old_blog = $wpdb->blogid;
    // Get all blog ids
    $blogIds = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    if (defined('URE_MULTISITE_DIRECT_UPDATE') && URE_MULTISITE_DIRECT_UPDATE == 1) {
      ure_direct_site_roles_update($blogIds);
    } else {
      foreach ($blogIds as $blog_id) {
        switch_to_blog($blog_id);
        $ure_roles = ure_getUserRoles();
        if (!$ure_roles) {
          echo '<div class="error fade below-h2">'.URE_ERROR.'</div>';
          return false;
        }
        if (!ure_saveRolesToDb()) {
          return false;
        }
      }
      switch_to_blog($old_blog);
      $ure_roles = ure_getUserRoles();            
    }
  
    if (defined('URE_DEBUG') && URE_DEBUG) {
      echo '<div class="updated fade below-h2">Roles updated for '.( microtime() - $time_shot ).' milliseconds</div>';
    }
    
  } else {
    if (!ure_saveRolesToDb()) {
      return false;
    }
  }
      
  return true;
}
// end of ure_updateRoles()


// process new role create request
function ure_newRoleCreate(&$ure_currentRole) {

  global $wp_roles;
  
  $mess = '';
  $ure_currentRole = '';
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $user_role = utf8_decode(urldecode($_GET['user_role']));
    // sanitize user input for security
    $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_role, $match);
    if (!$valid_name || ($valid_name && ($match[0]!=$user_role))) { // some non-alphanumeric charactes found!
      return __('Error: Role name must contain latin characters and digits only!', 'ure');
    }  
    if ($user_role) {
      if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
      }
      if (isset($wp_roles->roles[$user_role])) {      
        return sprintf('Error! '.__('Role %s exists already', 'ure'), $user_role);
      }
      // add new role to the roles array
      $ure_currentRole = strtolower($user_role);
      $user_role_copy_from = isset($_GET['user_role_copy_from']) ? $_GET['user_role_copy_from'] : false;
      if (!empty($user_role_copy_from) && $user_role_copy_from!='none' && $wp_roles->is_role($user_role_copy_from)) {
        $role = $wp_roles->get_role($user_role_copy_from);
        $capabilities = $role->capabilities;
      } else {
        $capabilities = array('read'=>1, 'level_0'=>1);
      }
      $result = add_role($ure_currentRole, $user_role, $capabilities);
      if (!isset($result) || !$result) {
        $mess = 'Error! '.__('Error is encountered during new role create operation', 'ure');
      } else {
        $mess = sprintf(__('Role %s is created successfully', 'ure'), $user_role);
      }
    }
  }
  return $mess;
}
// end of newRoleCreate()


// define roles which we could delete, e.g self-created and not used with any blog user
function ure_getRolesCanDelete($ure_roles) {
  global $wpdb;
  
  $tableName = (!is_multisite() && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
  $metaKey = $wpdb->prefix.'capabilities';
  $defaultRole = get_option('default_role');
  $standardRoles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
  $ure_rolesCanDelete = array();
  foreach ($ure_roles as $key=>$role) {
    $canDelete = true;
    // check if it is default role for new users
    if ($key==$defaultRole) {
      $canDelete = false;
      continue;
    }
    // check if it is standard role
    foreach ($standardRoles as $standardRole) {
      if ($key==$standardRole) {
        $canDelete = false;
        break;
      }
    }
    if (!$canDelete) {
      continue;
    }
    // check if user with such role exists
    $query = "SELECT meta_value
                FROM $tableName
                WHERE meta_key='$metaKey' AND meta_value like '%$key%'";
    $ure_rolesUsed = $wpdb->get_results($query);
    if ($ure_rolesUsed && count($ure_rolesUsed>0)) {
      foreach ($ure_rolesUsed as $roleUsed) {
        $roleName = unserialize($roleUsed->meta_value);
        foreach ($roleName as $key1=>$value1) {
          if ($key==$key1) {
            $canDelete = false;
            break;
          }
        }
        if (!$canDelete) {
          break;
        }
      }
    }
    if ($canDelete) {
      $ure_rolesCanDelete[$key] = $role['name'];
    }
  }

  return $ure_rolesCanDelete;
}
// end of getRolesCanDelete()


function ure_deleteRole() {
  global $wp_roles;

  $mess = '';
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $role = $_GET['user_role'];
    //$result = remove_role($_GET['user_role']);
    // use this modified code from remove_role() directly as remove_role() returns nothing to check
    if (!isset($wp_roles)) {
      $wp_roles = new WP_Roles();
    }
    if (isset($wp_roles->roles[$role])) {
      unset($wp_roles->role_objects[$role]);
      unset($wp_roles->role_names[$role]);
      unset($wp_roles->roles[$role]);
      $result = update_option($wp_roles->role_key, $wp_roles->roles);
    } else {
      $result = false;
    }
    if (!isset($result) || !$result) {
      $mess = 'Error! '.__('Error encountered during role delete operation', 'ure');
    } else {
      $mess = sprintf(__('Role %s is deleted successfully', 'ure'), $role);
    }
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of ure_deleteRole()


function ure_changeDefaultRole() {
  global $wp_roles;

  $mess = '';
  if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
  }
  if (isset($_GET['user_role']) && $_GET['user_role']) {
    $errorMessage = 'Error! '.__('Error encountered during default role change operation', 'ure');
    if (isset($wp_roles->role_objects[$_GET['user_role']])) {
      $result = update_option('default_role', $_GET['user_role']);
      if (!isset($result) || !$result) {
        $mess = $errorMessage;
      } else {
        $mess = sprintf(__('Default role for new users is set to %s successfully', 'ure'), $wp_roles->role_names[$_GET['user_role']]);
      }
    } else {
      $mess = $errorMessage;
    }
    unset($_REQUEST['user_role']);
  }

  return $mess;
}
// end of ure_changeDefaultRole()


function ure_ConvertCapsToReadable($capsName) {

  $capsName = str_replace('_', ' ', $capsName);
  $capsName = ucfirst($capsName);

  return $capsName;
}
// ure_ConvertCapsToReadable


function ure_TranslationData() {

// for the translation purpose
  if (false) {
// Standard WordPress roles
    __('Editor', 'ure');
    __('Author', 'ure');
    __('Contributor', 'ure');
    __('Subscriber', 'ure');
// Standard WordPress capabilities
    __('Switch themes', 'ure');
    __('Edit themes', 'ure');
    __('Activate plugins', 'ure');
    __('Edit plugins', 'ure');
    __('Edit users', 'ure');
    __('Edit files', 'ure');
    __('Manage options', 'ure');
    __('Moderate comments', 'ure');
    __('Manage categories', 'ure');
    __('Manage links', 'ure');
    __('Upload files', 'ure');
    __('Import', 'ure');
    __('Unfiltered html', 'ure');
    __('Edit posts', 'ure');
    __('Edit others posts', 'ure');
    __('Edit published posts', 'ure');
    __('Publish posts', 'ure');
    __('Edit pages', 'ure');
    __('Read', 'ure');
    __('Level 10', 'ure');
    __('Level 9', 'ure');
    __('Level 8', 'ure');
    __('Level 7', 'ure');
    __('Level 6', 'ure');
    __('Level 5', 'ure');
    __('Level 4', 'ure');
    __('Level 3', 'ure');
    __('Level 2', 'ure');
    __('Level 1', 'ure');
    __('Level 0', 'ure');
    __('Edit others pages', 'ure');
    __('Edit published pages', 'ure');
    __('Publish pages', 'ure');
    __('Delete pages', 'ure');
    __('Delete others pages', 'ure');
    __('Delete published pages', 'ure');
    __('Delete posts', 'ure');
    __('Delete others posts', 'ure');
    __('Delete published posts', 'ure');
    __('Delete private posts', 'ure');
    __('Edit private posts', 'ure');
    __('Read private posts', 'ure');
    __('Delete private pages', 'ure');
    __('Edit private pages', 'ure');
    __('Read private pages', 'ure');
    __('Delete users', 'ure');
    __('Create users', 'ure');
    __('Unfiltered upload', 'ure');
    __('Edit dashboard', 'ure');
    __('Update plugins', 'ure');
    __('Delete plugins', 'ure');
    __('Install plugins', 'ure');
    __('Update themes', 'ure');
    __('Install themes', 'ure');
    __('Update core', 'ure');
    __('List users', 'ure');
    __('Remove users', 'ure');
    __('Add users', 'ure');
    __('Promote users', 'ure');
    __('Edit theme options', 'ure');
    __('Delete themes', 'ure');
    __('Export', 'ure');
  }
}
// end of ure_TranslationData()


function ure_ArrayUnique($myArray) {
    if (!is_array($myArray)) {
      return $myArray;
    }
    
    foreach ($myArray as $key=>$value) {
      $myArray[$key] = serialize($value);
    }

    $myArray = array_unique($myArray);

    foreach ($myArray as $key=>$value) {
      $myArray[$key] = unserialize($value);
    }

    return $myArray;

} 
// end of ure_ArrayUnique()


// sort 2 dimensional array by column of its sub-array
class ure_TableSorter {
  protected $column;
  
  function __construct($column) {
    $this->column = $column;
  }
  
  function sort($table) {
    usort($table, array($this, 'compare'));
    
    return $table;
  }
  
  function compare($a, $b) {
    if ($a[$this->column] == $b[$this->column]) {
      return 0;
    }
    
    return ($a[$this->column] < $b[$this->column]) ? -1 : 1;
  }
}
// enf of ure_CapsSorter()


function ure_updateUser($user) {
  global $wpdb, $ure_capabilitiesToSave, $ure_currentRole;

  $user->remove_all_caps();
  if (count($user->roles)>0) {
    $userRole = $user->roles[0];
  } else {
    $userRole = '';
  }
  $user->set_role($ure_currentRole);
    
  if (count($ure_capabilitiesToSave)>0) {
    foreach ($ure_capabilitiesToSave as $key=>$value) {
      $user->add_cap($key);
    }
  }
  $user->update_user_level_from_caps();

  return true;
}
// end of ure_updateUser()


function ure_AddNewCapability() {
  global $wp_roles;
  
  $mess = '';
  if (isset($_GET['new_user_capability']) && $_GET['new_user_capability']) {
    $user_capability = utf8_decode(urldecode($_GET['new_user_capability']));
    // sanitize user input for security
    $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_capability, $match);
    if (!$valid_name || ($valid_name && ($match[0]!=$user_capability))) { // some non-alphanumeric charactes found!    
      return 'Error! '.__('Error: Capability name must contain latin characters and digits only!', 'ure');;
    }
   
    if ($user_capability) {
      $user_capability = strtolower($user_capability);
      if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
      }
      $wp_roles->use_db = true;
      $administrator = $wp_roles->get_role('administrator');
      if (!$administrator->has_cap($user_capability)) {
        $wp_roles->add_cap('administrator', $user_capability);
        $mess = sprintf(__('Capability %s is added successfully', 'ure'), $user_capability);
      } else {
        $mess = sprintf('Error! '.__('Capability %s exists already', 'ure'), $user_capability);
      }
    }
  }
  
  return $mess;
  
}
// end of ure_AddNewCapability


// returns array of built-in WP capabilities (WP 3.1 wp-admin/includes/schema.php) 
function ure_getBuiltInWPCaps() {
  $caps = array();
	$caps['switch_themes'] = 1;
	$caps['edit_themes'] = 1;
	$caps['activate_plugins'] = 1;
	$caps['edit_plugins'] = 1;
	$caps['edit_users'] = 1;
	$caps['edit_files'] = 1;
	$caps['manage_options'] = 1;
	$caps['moderate_comments'] = 1;
	$caps['manage_categories'] = 1;
	$caps['manage_links'] = 1;
	$caps['upload_files'] = 1;
	$caps['import'] = 1;
	$caps['unfiltered_html'] = 1;
	$caps['edit_posts'] = 1;
	$caps['edit_others_posts'] = 1;
	$caps['edit_published_posts'] = 1;
	$caps['publish_posts'] = 1;
	$caps['edit_pages'] = 1;
	$caps['read'] = 1;
	$caps['level_10'] = 1;
	$caps['level_9'] = 1;
	$caps['level_8'] = 1;
	$caps['level_7'] = 1;
	$caps['level_6'] = 1;
	$caps['level_5'] = 1;
	$caps['level_4'] = 1;
	$caps['level_3'] = 1;
	$caps['level_2'] = 1;
	$caps['level_1'] = 1;
	$caps['level_0'] = 1;
  $caps['edit_others_pages'] = 1;
  $caps['edit_published_pages'] = 1;
  $caps['publish_pages'] = 1;
  $caps['delete_pages'] = 1;
  $caps['delete_others_pages'] = 1;
  $caps['delete_published_pages'] = 1;
  $caps['delete_posts'] = 1;
  $caps['delete_others_posts'] = 1;
  $caps['delete_published_posts'] = 1;
  $caps['delete_private_posts'] = 1;
  $caps['edit_private_posts'] = 1;
  $caps['read_private_posts'] = 1;
  $caps['delete_private_pages'] = 1;
  $caps['edit_private_pages'] = 1;
  $caps['read_private_pages'] = 1;
  $caps['unfiltered_upload'] = 1; 
  $caps['edit_dashboard'] = 1;
  $caps['update_plugins'] = 1;
  $caps['delete_plugins'] = 1;
  $caps['install_plugins'] = 1;
  $caps['update_themes'] = 1;
  $caps['install_themes'] = 1;
  $caps['update_core'] = 1;
  $caps['list_users'] = 1;
  $caps['remove_users'] = 1;
  $caps['add_users'] = 1;
  $caps['promote_users'] = 1;
  $caps['edit_theme_options'] = 1;
  $caps['delete_themes'] = 1;
  $caps['export'] = 1;
  $caps['delete_users'] = 1;
  $caps['create_users'] = 1;

  return $caps;
}
//

// return the array of unused capabilities
function ure_getCapsToRemove() {
  global $wp_roles, $wpdb;

  // build full capabilities list from all roles except Administrator 
  $fullCapsList = array();  
  foreach($wp_roles->roles as $role) {    
    // validate if capabilities is an array
    if (isset($role['capabilities']) && is_array($role['capabilities'])) {
      foreach ($role['capabilities'] as $capability=>$value) {
        if (!isset($fullCapsList[$capability])) {
          $fullCapsList[$capability] = 1;
        }
      }
    }
  }

  $capsToExclude = ure_getBuiltInWPCaps();
  
  $capsToRemove = array();
  foreach ($fullCapsList as $capability=>$value) {
    if (!isset($capsToExclude[$capability])) {
      // check roles
      $capInUse = false;
      foreach ($wp_roles->role_objects as $wp_role) {
        if ($wp_role->name!='administrator') {
          if ($wp_role->has_cap($capability)) {
            $capInUse = true;
            break;
          }
        }
      }      
      if (!$capInUse) {
        $capsToRemove[$capability] = 1;
      }
    }
  } 
  
  return $capsToRemove;
}
// end of getCapsToRemove()


function ure_getCapsToRemoveHTML() {
  $capsToRemove = ure_getCapsToRemove();
  if (!empty($capsToRemove) && is_array($capsToRemove) && count($capsToRemove)>0) {
    $html = '<select id="remove_user_capability" name="remove_user_capability" width="200" style="width: 200px">';
  foreach ($capsToRemove as $key=>$value) {
    $html .= '<option value="'.$key.'">'.$key.'</option>';
  }
    $html .= '</select>';
  } else {
    $html = '';
  }
  
  return $html;
}
// end of getCapsToRemoveHTML()


function ure_removeCapability() {
  global $wpdb, $wp_roles;

  $mess = '';
  if (isset($_GET['removeusercapability']) && $_GET['removeusercapability']) {
    $capability = $_GET['removeusercapability'];
    $capsToRemove = ure_getCapsToRemove();    
    if (!is_array($capsToRemove) || count($capsToRemove)==0 || !isset($capsToRemove[$capability])) {
      return sprintf(__('Error! You do not have permission to delete this capability: %s!', 'ure'), $capability);
    }
        
    // process users
    $usersId = $wpdb->get_col($wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users"));
    foreach ($usersId as $user_id) {
      $user = get_user_to_edit($user_id);
      if (isset($user->roles[0]) && $user->roles[0] == 'administrator') {
        continue;
      }
      if ($user->has_cap($capability)) {
        $user->remove_cap($capability);
      }
    }

    // process roles
    foreach ($wp_roles->role_objects as $wp_role) {
      if ($wp_role->has_cap($capability)) {
        $wp_role->remove_cap($capability);
      }
    }
    
    $mess = sprintf(__('Capability %s is removed successfully', 'ure'), $capability);
  }

  return $mess;
}

// end of ure_removeCapability()


// returns link to the capability according its name in $capability parameter
function ure_capability_help_link($capability) {
  
  if (empty($capability)) {
    return '';
  }
  
  switch ($capability) {
    case 'activate_plugins':
      $url = 'http://www.shinephp.com/activate_plugins-wordpress-capability/';
      break;
    case 'add_users':
      $url = 'http://www.shinephp.com/add_users-wordpress-user-capability/';
      break;        
    case 'create_users':
      $url = 'http://www.shinephp.com/create_users-wordpress-user-capability/';
      break;
    case 'delete_others_pages':
    case 'delete_others_posts':
    case 'delete_pages':
    case 'delete_posts':
    case 'delete_private_pages':
    case 'delete_private_posts':
    case 'delete_published_pages':
    case 'delete_published_posts':  
      $url = 'http://www.shinephp.com/delete-posts-and-pages-wordpress-user-capabilities-set/';
      break;
    case 'delete_plugins':  
      $url = 'http://www.shinephp.com/delete_plugins-wordpress-user-capability/';
      break;
    case 'delete_themes':  
      $url = 'http://www.shinephp.com/delete_themes-wordpress-user-capability/';
      break;
    case 'delete_users':  
      $url = 'http://www.shinephp.com/delete_users-wordpress-user-capability/';
      break;    
    case 'edit_dashboard':
      $url = 'http://www.shinephp.com/edit_dashboard-wordpress-capability/';
      break;    
    case 'edit_files':
      $url = 'http://www.shinephp.com/edit_files-wordpress-user-capability/';
      break;            
    case 'edit_plugins':
      $url = 'http://www.shinephp.com/edit_plugins-wordpress-user-capability';
      break;                
    case 'moderate_comments':
      $url = 'http://www.shinephp.com/moderate_comments-wordpress-user-capability/';
      break;    
    case 'update_core':
      $url = 'http://www.shinephp.com/update_core-capability-for-wordpress-user/';
      break;    
    default:
      $url = '';
  }
  // end of switch
  if (!empty($url)) {
    $link = '<a href="'.$url.'" title="read about '.$capability.' user capability" target="new"><img src="'.URE_PLUGIN_URL.'/images/help.png" alt="'.__('Help','ure').'" /></a>';
  } else {
    $link = '';
  }
  
  return $link;
}
// end of ure_capability_help_link()


// returns array of deprecated capabilities
function ure_get_deprecated_caps() {
  
  $dep_caps = array('level_0'=>0, 
                    'level_1'=>0, 
                    'level_2'=>0, 
                    'level_3'=>0, 
                    'level_4'=>0, 
                    'level_5'=>0, 
                    'level_6'=>0, 
                    'level_7'=>0, 
                    'level_8'=>0, 
                    'level_9'=>0, 
                    'level_10'=>0,
                    'edit_files'=>0);
  
  return $dep_caps;
  
}
// end of get_deprecated_caps()

?>
