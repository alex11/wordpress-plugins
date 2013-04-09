<?php
/**
 * Tabber Tabs Widget
 *
 * Create the actual tabbed widget
 *
 * @package Tabber Tabs
 * @subpackage Widget
 */


 // Let's build a widget
class Slipfire_Widget_Tabber extends WP_Widget {

	function Slipfire_Widget_Tabber() {
		$widget_ops = array( 'classname' => 'tabbertabs', 'description' => __('Place items in the TABBER TABS WIDGET AREA and have them show up here.', 'slipfire') );
		$control_ops = array( 'width' => 230, 'height' => 350, 'id_base' => 'slipfire-tabber' );
		$this->WP_Widget( 'slipfire-tabber', __('Tabber Tabs Widget', 'slipfire'), $widget_ops, $control_ops );
	}
	

	function widget( $args, $instance ) {
		extract( $args );
		
		$style = $instance['style']; // get the widget style from settings
		
		echo $before_widget;
?>		
		<div class="tabber <?php echo $style ?>">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('tabber_tabs') ); ?>
		</div>
		
<?php 	echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['style'] = $new_instance['style'];
		
		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array( 'title' => __('Tabber', 'slipfire'), 'style' => 'style1' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div style="float:left;width:98%;"></div>
		<p>
		<?php _e('Place items in the TABBER TABS WIDGET AREA and have them show up here.', 'slipfire')?>
		</p>
		<div style="float:left;width:48%;">
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e('Tab Style:', 'rolopress'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'style1' == $instance['style'] ) echo 'selected="selected"'; ?>>style1</option>
				<option <?php if ( 'style2' == $instance['style'] ) echo 'selected="selected"'; ?>>style2</option>
			</select>
		</p>
		
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>