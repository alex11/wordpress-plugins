<?php
/*
Plugin Name: Tabbed Widgets
Plugin URI: http://konstruktors.com/projects/wordpress-plugins/tabbed-accordion-widgets/
Description: Place widgets into tabbed and accordion type interface.
Version: 1.3.1
Author: Kaspars Dambis
Author URI: http://konstruktors.com/blog/

Thanks for suggestions to Ronald Huereca.
*/

// Option row where we store widget copies, as other plugins (such as widget context) might take them over
define('ORIGINAL_WIDGETS', 'tabbed_widgets_originals');

new tabbedWidgets();

class tabbedWidgets {
	var $tabbed_widget_content = array();
	var $stored_widgets = array();
	var $plugin_path = '';
	
	function tabbedWidgets() {			
		$this->plugin_path = WP_PLUGIN_URL  . '/' . basename(dirname(__FILE__)) . '/';
		
		add_action('widgets_init', array($this, 'initSidebarAndWidget'), 1);
		add_action('sidebar_admin_setup', array($this, 'saveWidgets'), 1); // Save it in our own row, as other plugins might take it over when we need it. Like widget context plugin, for example.
		add_action('admin_menu', array($this, 'addOptionsPage'));
		add_action('wp_head', array($this, 'addHeader'), 1);
		add_action('wp_footer', array($this, 'printJsVars'));
		
		add_action('widgets_init', array($this, 'checkThemeCompat'));
	}
	
	function checkThemeCompat() {
		global $wp_registered_sidebars;
		
		$tmp = $wp_registered_sidebars;
		$sample_container = array_shift($tmp);
		if (!strstr($sample_container['before_widget'], '%1$s')) {
			add_action('admin_notices', array($this, 'theme_not_compatible'));
		}
	}
	
	function theme_not_compatible() {
		echo '<div class="updated fade"><p>', __("Tabbed Widgets plugin isn't compatible with your theme <em>". get_current_theme() ."</em>, because it doesn't use unique widgets identifiers. <a href='http://konstruktors.com/blog/wordpress/409-is-your-wordpress-theme-good-enough/'>Here is how you can fix it</a>. Please disable the plugin while you fix this issue."), '</p></div>';
	}
	
	function initSidebarAndWidget() {
		// Init tabbed widgets
		register_widget('tabbedWidgetWidget');
		
		// Since 3.0 we can use the inactive widgets area. Leave this for compatability.
		register_sidebar(array('name' => 'Invisible Widget Area'));
	}
	
	function saveWidgets() {
		global $wp_registered_widgets;
		
		$sidebars_widgets = wp_get_sidebars_widgets(false);
		
		// Stored widgets include all default widget settings and function calls
		if (is_array($sidebars_widgets) && !empty($sidebars_widgets)) {
			foreach ($sidebars_widgets as $sidebar_id => $widgets) {
				if (!empty($widgets) && $sidebar_id != 'wp_inactive_widgets') {
					foreach ($widgets as $no => $widget_id) {
						// Save original widgets, except the self	
						if (strpos($widget_id, 'tabbed-widget') === false) {
							$this->stored_widgets[$widget_id] = $wp_registered_widgets[$widget_id];
							$this->stored_widgets[$widget_id]['titles'] = $this->get_widget_titles($wp_registered_widgets[$widget_id]);
						}
					}
				}
			}
			update_option(ORIGINAL_WIDGETS, $this->stored_widgets);
		}
	}
	
	function get_widget_titles($widget_data) {
		$widget_name = $widget_data['name'];
		$widget_params = $widget_data['params'];
		$widget_callback = $widget_data['callback'];
		
		// if parameter is a string
		if (isset($widget_params[0]) && !is_array($widget_params[0]))
			$widget_params = $widget_params[0];
		
		$sidebar_params['before_title'] = '[%';
		$sidebar_params['after_title'] = '%]';
		$all_params = array_merge(array($sidebar_params), (array)$widget_params);					
			
		if (is_callable($widget_callback)) {
			// Call widget to see its title
			ob_start();
				call_user_func_array($widget_callback, $all_params);
				$widget_title = ob_get_contents();
			ob_end_clean();
			
			// Extract only title of the widget
			$find_fn_pattern = '/\[\%(.*?)\%\]/';
			preg_match_all($find_fn_pattern, $widget_title, $result);
			$given_title = strip_tags(trim((string)$result[1][0]));
		} else {
			$widget_title = $widget_name;
			$given_title = '';
		}
		
		return array('original_title' => $widget_name, 'given_title' => $given_title);
	}		

	function addOptionsPage() {
		add_action('admin_enqueue_scripts', array($this, 'addAdminCSS'));
	}

	function addAdminCSS() {
		wp_enqueue_style('tabbed-widgets-admin', $this->plugin_path . 'css/admin-style.css');
	}
	
	function addHeader() {
		wp_enqueue_script('jquery-ui-accordion',  $this->plugin_path . 'js/jquery-ui-custom.min.js', array('jquery'), false, true);
		wp_enqueue_script('jquery-ui-cookie',  $this->plugin_path . 'js/jquery-cookie.min.js', array('jquery-ui-accordion'), false, true);
		
		// Add default widgets styles
		wp_enqueue_style('tabbed-widgets', $this->plugin_path . 'css/tabbed-widgets.css');
		
		if (get_current_theme() == 'Twenty Ten')
			wp_enqueue_style('tabbed-widgets-2010', $this->plugin_path . 'css/twenty-ten.css');
	}	
	
	function printJsVars() {
		// Read tabbed widget options
		$tw_options = get_option('widget_tabbed-widget');
		
		$optionsvar = '$rotateoptions';
		$jsvars = 'var ' . $optionsvar . ' = new Array();' . "\n";
		
		foreach ($tw_options as $tw_id => $tw_settings) {
			if (!is_numeric($tw_id))
				break;
				
			$style = $tw_settings['style'];
			$rotate = $tw_settings['rotate'];
			$rotate_time = $tw_settings['rotate_time'];
			$random_start = $tw_settings['random_start'];
			$start_tab = $tw_settings['start_tab'];
			
			if (!is_numeric($start_tab))
				$start_tab = 0;
			
			if (empty($rotate))
				$rotate = 0;
			else 
				$rotate = 1;
			
			if (empty($random_start))
				$random_start = 0;
			else 
				$random_start = 1;

			if (empty($rotate_time))
				$rotate_time = 10000; // Make default rotate time 10 seconds
			elseif ($rotate)
				$rotate_time = intval($rotate_time) * 1000; // Convert seconds to miliseconds
			
			if ($rotate_time < 1000)
				$rotate_time = 1000; // Don't allow rotation times slower than 1 second.

			$jsvars .= $optionsvar . '[' . $tw_id . '] = new Array();' . "\n";
			$jsvars .= $optionsvar . '[' . $tw_id . ']["style"] = "' . $style . "\";\n";
			$jsvars .= $optionsvar . '[' . $tw_id . ']["rotate"] = ' . $rotate . ";\n";
			$jsvars .= $optionsvar . '[' . $tw_id . ']["random_start"] = ' . $random_start . ";\n";
			$jsvars .= $optionsvar . '[' . $tw_id . ']["start_tab"] = ' . $start_tab . ";\n";			
			$jsvars .= $optionsvar . '[' . $tw_id . ']["interval"] = ' . $rotate_time . ";\n";
		}
		
		if (count($tw_options) > 0)
			echo '<script type="text/javascript">', $jsvars, '</script>', "\n",
				'<script type="text/javascript" src="', $this->plugin_path, 'js/init-plugin.js"></script>', "\n";
	}
}




class tabbedWidgetWidget extends WP_Widget {
	var $tw_options = array();
	var $active_widgets = array();
	
	function tabbedWidgetWidget() {
		$widget_ops = array('classname' => 'tabbed-widget', 'description' => 'Place widgets inside a tabbed or an accordion type interface');
		$control_ops = array('width' => 390, 'id_base' => 'tabbed-widget');
		$this->WP_Widget('tabbed-widget', 'Tabbed Widget', $widget_ops, $control_ops);
		
		if (empty($this->active_widgets))
			$this->active_widgets = get_option(ORIGINAL_WIDGETS);
	}
	
	function update($new_instance, $old_instance) {		
		return $new_instance;
		// return array();
	}
	
	function form($instance) {
		$options .= '<div class="widget-wrapper">';
		$options .= '<p class="tw-title">' . $this->makeTitleOption($instance, 'show_title', 'Show Title') . '<input type="text" name="' . $this->get_field_name('widget_title') . '" class="tw-widget-title" value="'. esc_attr($instance['widget_title']) .'" /></p>';
		
		$options .= '<p class="tw-style-type"><strong>'. __('Style as') .'</strong>: ';
		$options .= '<span>' . $this->makeSimpleRadio($instance, 'style', 'tabs', __('tabs')) . ' '. __('or') .'</span> ';
		$options .= '<span>' . $this->makeSimpleRadio($instance, 'style', 'accordion', __('accordion')) . '</span></p>';
		$options .= $this->makeDonate();
		$options .= '<p class="tw-widget-note">' . __('Place widgets inside the <em>Invisible Widget Area</em> to make them available here.') . '</p>';
			
		for ($count = 0; $count < 5; $count++) {
			$count_out = $count + 1;
			$tab_title = __('Tab') . ' ' . $count_out . ':';
			
			$options .= '<div class="tw-each-tab">' 
				. $this->makeSimpleRadio($instance, 'start_tab', $count, $tab_title) 
				. $this->makeSingleWidgetsList($instance, 'inside_' . $count . '_widget') . ' ' 
				. $this->makeSingleWidgetsTitleField($instance, 'inside_' . $count . '_title') 
				. '</div>';
		}
		
		$options .= '<div class="tw-each-tab">' . $this->makeSimpleRadio($instance, 'start_tab', 'default', __('Default start tab')) . '</div>';
		$options .= '<div class="tw-randomstart">' . $this->make_checkbox($instance, 'random_start', __('Choose a random start tab')) . '</div>';
		$options .= '<div class="tw-rotateoptions">' . $this->makeRotateOption($instance) . '</div>';
		
		$options .= '</div>';
				
		print $options;	
	}
	
	function widget($args, $instance) {	
		// Output tabbed interface
		if ($instance['style'] == 'tabs' || $instance['style'] == 'accordion')
			$this->print_tabbed_widget($args, $instance);
	}	
	
	function print_tabbed_widget($args, $instance) {
		global $wp_registered_sidebars, $wp_registered_widgets;
		
		$widgetdata = $this->get_widgetdata($instance);
		$widget_no = str_replace('tabbed-widget-', '', $args['widget_id']);
		
		if (!empty($instance['show_title']))
			$widget_title = $args['before_title'] . $instance['widget_title'] . $args['after_title'];
		
		$result = $args['before_widget'];
		$result .= $widget_title;
		
		if ($instance['style'] == 'tabs')
			$result .= '<div class="tw-tabs">';
		else
			$result .= '<div class="tw-accordion">';
		
		foreach ($widgetdata['inside'] as $id => $inside) {
			$callback = $wp_registered_widgets[$inside['widget']]['callback'];
			$params = array_merge(array($args), (array)$wp_registered_widgets[$inside['widget']]['params']);
			
			if (is_callable($callback)) {				
				$widget_title = trim($inside['title']);
				if (empty($widget_title))
					$widget_title = $this->active_widgets[$inside['widget']]['titles']['original_title'];
					
				$params[0]['before_widget'] = '<div id="tw-content-'. $widget_no .'-'. $id .'" class="tw-content">';
				$params[0]['before_title'] = '<span style="display:none;">';
				$params[0]['after_title'] =  '</span><h4 id="tw-title-'. $widget_no .'-'. $id .'" class="tw-title">' . $widget_title. '</h4>';
				$params[0]['after_widget'] = '</div>';
				
				$result .= $this->callMe($callback, $params);
			} else {
				$result .= '<!-- t-error: Callback not possible. -->';
			}
		}
		
		$result .= '</div>';
		$result .= $args['after_widget'];
		
		print $result;
	}	
	
	
	// ------------------------------------ Helpers
	
	function get_widgetdata($instance) {
		$widgetdata = array();
		
		// Turn the list of tab widgets into an array
		foreach ($instance as $id => $value) {
			list($kaka, $which, $what) = split('_', $id);
			if ($kaka == 'inside') {
				if ($what == 'title')
					$widgetdata['inside'][$which]['title'] = $value;
				if ($what == 'widget')
					$widgetdata['inside'][$which]['widget'] = $value;
			} else {
				$widgetdata[$id] = $value;
			}
		}
		
		// check if widget content is not empty
		foreach ($widgetdata['inside'] as $id => $data) {
			$widget_inside = trim($data['widget']);
			if (empty($widget_inside) || $widget_inside == '')
				unset($widgetdata['inside'][$id]);
		}
		
		return $widgetdata;
	}
	
	function callMe($callback, $params) {		
		ob_start();
			call_user_func_array($callback, $params);	
			$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	
	// ------------------------------------ Design
	
	function make_checkbox($instance, $inst_name, $label = '', $tip = '') {
		if ($tip) 
			$tip = '<small>(' . $tip . ')</small>';
	
		if ($instance[$inst_name] == 1 || $instance[$inst_name] == 'on')
			$checked = 'checked="checked"';
		else
			$checked = '';
				
		$out = '<div><label><input value="1" type="checkbox" id="' . $this->get_field_id($inst_name) . '" name="' . $this->get_field_name($inst_name) . '" '. $checked .' /> '
			. $label . ' ' . $tip . '</label></div>';

		return $out;
	}

	function makeSingleWidgetsList($instance, $inst_name) {
		$list = ' <label class="tw-in-widget-list">'
			. '<select name="' . $this->get_field_name($inst_name) . '" id="'. $this->get_field_id($inst_name) .'">'
			. '<option></option>';
			
		if (!empty($this->active_widgets)) {
			foreach ($this->active_widgets as $widget_id => $widget_data) {
				$value = $widget_data['titles']['original_title'];
				if (!empty($widget_data['titles']['given_title']))
					$value .= ': ' . $widget_data['titles']['given_title'];
					
				if ($instance[$inst_name] == $widget_id) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				
				if (strpos($widget_id, 'tabbed-widget') === false) {
					$list .= '<option value="' . $widget_id . '" ' . $selected . '>' . esc_attr($value) . '</option>';
				}
			}
		} else {
			$list .= '<option value="error" selected="selected">Place widgets in the "Invisible Sidebar" to make them available here.</option>';
		}
		
		$list .= '</select></label>';
		
		return $list;
	}
	
	function makeSingleWidgetsTitleField($instance, $inst_name) {
		return '<label class="tw-in-widget-title">' . __('Title') . ': ' 
			. '<input type="text" name="'. $this->get_field_name($inst_name) .'" id="'. $this->get_field_id($inst_name) .'" value="'. esc_attr($instance[$inst_name]) .'" /></label>';
	}

	function makeDonate() {
		return '<p class="tw-donate"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=kaspars%40konstruktors%2ecom&item_name=Tabbed%20Widgets%20Plugin%20for%20WordPress&no_shipping=1&no_note=1&tax=0&currency_code=USD&lc=LV&bn=PP%2dDonationsBF&charset=UTF%2d8" title="Show your love and support -- make a donation. It is truly appreciated!" target="_blank">Donate</a></p>';
	}	
	
	function makeSimpleRadio($instance, $inst_name, $id, $label = null) {
		if ($instance[$inst_name] == $id) {
			$checked = 'checked="checked"';
			$classname = 'active';
		} else {
			$checked = '';
			$classname = '';
		}
		
		return '<label class="' . $classname . '">'
			. '<input type="radio" id="'. $this->get_field_id($inst_name) .'" name="'. $this->get_field_name($inst_name) . '" value="'. $id .'" '. $checked .' /> ' 
			. $label . '</label>';
	}	

	function makeTitleOption($instance, $inst_name, $label = '') {
		if ($instance[$inst_name] == 1 || $instance[$inst_name] == 'on')
			$checked = 'checked="checked"';
		else
			$checked = '';
		
		return '<input type="checkbox" value="1" id="' . $this->get_field_id($inst_name) . '" name="' . $this->get_field_name($inst_name) . '" '. $checked .' /> ' 
			. '<label for="' . $this->get_field_id($inst_name) . '">' . __($label) . '</label> ';
	}

	function makeRotateOption($instance) {		
		if ($instance['rotate'] == 1 || $instance['rotate'] == 'on')
			$checked = 'checked="checked"';
		else
			$checked = '';
		
		return '<label><input type="checkbox" value="1" id="'. $this->get_field_id('rotate') .'" name="'. $this->get_field_name('rotate') .'" '. $checked .'  /> ' 
			. __('Rotate tabs') . '</label> <label class="tw-rotate-time">' . __('with interval (in seconds)') . ': ' 
			. '<input type="text" id="'. $this->get_field_id('rotate_time') .'" name="'. $this->get_field_name('rotate_time') .'" value="'. $instance['rotate_time'] .'" size="3" /></label> <span class="info">' . __('(default is 10 seconds)') . '</span>';
	}
	
}



?>