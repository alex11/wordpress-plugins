<?php

/*
 * 
 * User Role Editor plugin: user capabilities editor page
 * 
 */

if (!defined('URE_PLUGIN_URL')) {
  die;  // Silence is golden, direct call is prohibited
}

if (!isset($ure_currentRole) || !$ure_currentRole) {
  if (isset($_REQUEST['user_role']) && $_REQUEST['user_role']) {
    $ure_currentRole = $_REQUEST['user_role'];
  } else if (count($ure_userToEdit->roles)>0) {
    // just take the 1st element of array, as it could start from index not equal to 0
    foreach ($ure_userToEdit->roles as $role) {
      $ure_currentRole = $role;
      break;
    }
  } else {
   $ure_currentRole = '';
  }
}


$roleSelectHTML = '<select id="user_role" name="user_role" onchange="ure_Actions(\'role-change\', this.value);">';
foreach ($ure_roles as $key=>$value) {
  if ($key!='administrator') {
    $selected = ure_optionSelected($key, $ure_currentRole);
    $roleSelectHTML .= '<option value="'.$key.'" '.$selected.'>'.__($value['name'], 'ure').'</option>';
  }
}
if ($ure_currentRole==-1) {
  $selected = 'selected="selected"';
} else {
  $selected = '';
}
$roleSelectHTML .= '<option value="-1" '.$selected.' >&mdash; No role for this site &mdash;</option>';
$roleSelectHTML .= '</select>';


?>

<div class="has-sidebar-content">
<script language="javascript" type="text/javascript">
  function ure_Actions(action, value) {
    var url = '<?php echo URE_WP_ADMIN_URL.'/'.URE_PARENT; ?>?page=user-role-editor.php&object=user&user_id=<?php echo $ure_userToEdit->ID; ?>';
    if (action=='cancel') {
      document.location = url;
      return true;
    } if (action!='update') {
      url += '&action='+ action;
      if (value!='' && value!=undefined) {
        url = url +'&user_role='+ escape(value);
      }
      document.location = url;
    } else {
      document.getElementById('ure-form').submit();
    }
    
  }// end of ure_Actions()


  function ure_onSubmit() {
    if (!confirm('<?php echo sprintf(__('User "%s" update: please confirm to continue', 'ure'), $ure_userToEdit->display_name); ?>')) {
      return false;
    }
  }

</script>
<?php
  $userInfo = ' <span style="font-weight: bold;">'.$ure_userToEdit->user_login; 
  if ($ure_userToEdit->display_name!==$ure_userToEdit->user_login) {
    $userInfo .= ' ('.$ure_userToEdit->display_name.')';
  }
  $userInfo .= '</span>';
	ure_displayBoxStart(__('Change capabilities for user', 'ure').$userInfo);
 
?>
  <div style="float: left;"><?php echo __('Role:', 'ure').' '.$roleSelectHTML; ?></div>
  <?php
  if ($ure_caps_readable) {
    $checked = 'checked="checked"';
  } else {
    $checked = '';
  }
?>
  <div style="display:inline;float: right;"><input type="checkbox" name="ure_caps_readable" id="ure_caps_readable" value="1" <?php echo $checked; ?> onclick="ure_Actions('capsreadable');"/>
    <label for="ure_caps_readable"><?php _e('Show capabilities in human readable form', 'ure'); ?></label><br/>
<?php
    if ($ure_show_deprecated_caps) {
      $checked = 'checked="checked"';
    } else {
      $checked = '';
    }
?>
                <input type="checkbox" name="ure_show_deprecated_caps" id="ure_show_deprecated_caps" value="1" <?php echo $checked; ?> onclick="ure_Actions('showdeprecatedcaps');"/>
                <label for="ure_show_deprecated_caps"><?php _e('Show deprecated capabilities', 'ure'); ?></label>    
  </div>

  <br/><br/><hr/>  
  <h3><?php _e('Add capabilities to this user:', 'ure'); ?></h3>
	<?php _e('Core capabilities:', 'ure'); ?>
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
<?php
  $deprecatedCaps = ure_get_deprecated_caps();
	$quant = count($built_in_wp_caps);
	$quantInColumn = 22;
	$printed_quant = 0;
	foreach ( $ure_fullCapabilities as $capability ) {
		if ( !$capability['wp_core'] ) { // show WP built-in capabilities 1st
			continue;
		}
		if (!$ure_show_deprecated_caps && isset($deprecatedCaps[$capability['inner']])) {
			$input_type = 'hidden';
		} else {
			$input_type = 'checkbox';
		}
		if (isset($deprecatedCaps[$capability['inner']])) {
			$labelStyle = 'style="color:#BBBBBB;"';
		} else {
			$labelStyle = '';
		}
		$checked = '';
		$disabled = '';
		if (isset($ure_roles[$ure_currentRole]['capabilities'][$capability['inner']])) {
			$checked = 'checked="checked"';
			$disabled = 'disabled="disabled"';
		} else if (isset($ure_userToEdit->caps[$capability['inner']])) {
			$checked = 'checked="checked"';
		}
		$cap_id = str_replace(' ', URE_SPACE_REPLACER, $capability['inner']);
		?>
		          <input type="<?php echo $input_type; ?>" name="<?php echo $cap_id; ?>" id="<?php echo $cap_id; ?>" value="<?php echo $capability['inner']; ?>" <?php echo $checked; ?> <?php echo $disabled; ?>/>
		<?php
		if ($input_type == 'checkbox') {
			if ($ure_caps_readable) {
				$capInd = 'human';
				$capIndAlt = 'inner';
			} else {
				$capInd = 'inner';
				$capIndAlt = 'human';
			}
			?>
				          <label for="<?php echo $cap_id; ?>" title="<?php echo $capability[$capIndAlt]; ?>" <?php echo $labelStyle; ?> ><?php echo $capability[$capInd]; ?></label> <?php echo ure_capability_help_link($capability['inner']); ?><br/>
			<?php
			$printed_quant++;
		}
		if ( $printed_quant >= $quantInColumn ) {
			$printed_quant = 0;
			echo '</td>
           <td style="vertical-align:top;">';
		}
	}
	?>
      </td>
    </tr>
  </table>
  <hr/>
<?php 
	$quant = count($ure_fullCapabilities) - $quant;
	if ($quant>0) {
		_e('Custom capabilities:', 'ure'); 
?>
  <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="vertical-align:top;">
<?php
        
        $quantInColumn = (int) $quant / 3;
        $printed_quant = 0;				
        foreach ($ure_fullCapabilities as $capability) {
					if ( $capability['wp_core'] ) {  // show plugins or user added capabilities
						continue;
					}
          $checked = ''; $disabled = '';
          if (isset($ure_roles[$ure_currentRole]['capabilities'][$capability['inner']])) {
            $checked = 'checked="checked"';
            $disabled = 'disabled="disabled"';
          } else if (isset($ure_userToEdit->caps[$capability['inner']])) {
            $checked = 'checked="checked"';
          }
          $cap_id = str_replace(' ', URE_SPACE_REPLACER, $capability['inner']);
?>
          <input type="checkbox" name="<?php echo $cap_id; ?>" id="<?php echo $cap_id; ?>" value="<?php echo $capability['inner']; ?>" <?php echo $checked; ?> <?php echo $disabled; ?>/>
<?php
        if ($input_type=='checkbox') {
          if ($ure_caps_readable) {
            $capInd = 'human';
            $capIndAlt = 'inner';
          } else {
            $capInd = 'inner';
            $capIndAlt = 'human';
          }
        ?>
          <label for="<?php echo $cap_id; ?>" title="<?php echo $capability[$capIndAlt]; ?>" ><?php echo $capability[$capInd]; ?></label> <?php echo ure_capability_help_link($capability['inner']); ?><br/>
<?php            
          $printed_quant++;
        }
          if ( $printed_quant >= $quantInColumn ) {
            $printed_quant = 0;
            echo '</td>
           <td style="vertical-align:top;">';
          }
        }
        ?>
      </td>
    </tr>
  </table>	
	<hr/>
<?php
	}  // if ($quant>0)
?>
  <input type="hidden" name="object" value="user" />
  <input type="hidden" name="user_id" value="<?php echo $ure_userToEdit->ID; ?>" />
  <div class="submit" style="padding-top: 0px;">
    <div style="float:left; padding-bottom: 10px;">
        <input type="submit" name="submit" value="<?php _e('Update', 'ure'); ?>" title="<?php _e('Save Changes', 'ure'); ?>" />
        <input type="button" name="cancel" value="<?php _e('Cancel', 'ure') ?>" title="<?php _e('Cancel not saved changes','ure');?>" onclick="ure_Actions('cancel');"/>
    </div>
  </div>

<?php
  ure_displayBoxEnd();
?>
  
</div>

