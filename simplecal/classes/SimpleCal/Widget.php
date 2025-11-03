<?php
namespace SimpleCal;

class Widget extends \WP_Widget {
	// TODO: Make this a wrapper that calls the block render? Prepopulates $attributes from the widget settings and then just includes render.php?
	public function __construct() {
		parent::__construct(
			'simplecal_widget',
			'SimpleCal Widget', // esc_html__()?! We don't need no stinkin' esc_html__()!
			['description' => 'Displays SimpleCal Events in an agenda or calendar view']
		);
	}

	public function widget($args, $instance) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo 'This is the widget body. This is where calendar events should get displayed.';
		echo $args['after_widget'];
	}

	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : 'Calendar';
?>
		<p>
		<label for="<?= esc_attr($this->get_field_id('title')); ?>">Title:</label> 
		<input class="widefat" id="<?= esc_attr($this->get_field_id('title')); ?>" name="<?= esc_attr($this->get_field_name('title')); ?>" type="text" value="<?= esc_attr($title); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';

		return $instance;
	}
}
?>