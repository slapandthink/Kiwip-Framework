<?php

/**
 * My Widget 
 *
 * @package Kiwip Framework
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */
class My_Widget extends WP_Widget{

	// construct
	public function __construct() {
		$widget_ops = array( 'classname' => 'example', 'description' => __('A widget that displays the authors name ', 'example') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );
		$this->WP_Widget( 'example-widget', __('Example Widget', 'example'), $widget_ops, $control_ops );
	}

	// widget fucntion
	public function widget($args, $instance){
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		$name = $instance['name'];
		$show_info = isset($instance['show_info']) ? $instance['show_info'] : FALSE;

		echo $before_widget;

		// display the widget title
		if($title) echo $before_title.$title.$after_title;

		// display the name
		if($name) printf('<p>'.__('Hey their Sailor! My name is %1$s.', 'example').'</p>', $name);

		if($show_info) printf($name);

		echo $after_widget;
	}

	// update function
	public function update($new_instance, $old_instance){
		$instance = $old_instance;

		// strip tags from title and naùe to remove HTML
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['name'] = strip_tags($new_instance['name']);
		$instance['show_info'] = strip_tags($new_instance['show_info']);

		return $instance;
	}

	// form function
	public function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Example', 'example'), 'name' => __('Ben', 'example'), 'show_info' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php //Widget Title: Text Input. ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<?php //Text Input. ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Your Name:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>

		
		<?php //Checkbox. ?>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_info'], true ); ?> id="<?php echo $this->get_field_id( 'show_info' ); ?>" name="<?php echo $this->get_field_name( 'show_info' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_info' ); ?>"><?php _e('Display info publicly?', 'example'); ?></label>
		</p>

	<?php
	}
}


