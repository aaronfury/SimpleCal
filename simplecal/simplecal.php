<?php
/*
Plugin Name:  SimpleCal
Plugin URI:   https://github.com/aaronfury/SimpleCal
Description:  This is a simple, free plugin for adding calendar events to WordPress using a custom post type and a widget. It is simple, and it is free.
Version:      0.1.20240417
Author:       Aaron Firouz
License:      Creative Commons Zero
License URI:  https://creativecommons.org/publicdomain/zero/1.0/
Text Domain:  simplecal
*/

// Debugging shortcut... make sure these are commented out or set to "0" (or false) in the production site
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors','Off');
ini_set('error_log','php-errors.log');
error_reporting(E_ALL);

class SimpleCal {
	public static $tz;
	private $tz_string;
	private $post_meta;
	
	public function __construct() {
		self::$tz = wp_timezone();
		$this->tz_string = wp_timezone_string();
		add_action('init',[$this,'simplecal_create_event_posts']);
		add_action('widgets_init', [$this,'register_simplecal_widget']);
		add_action( 'init', [$this,'register_simplecal_calendar_block']);
		
		if (is_admin()) {
			add_action('add_meta_boxes', [$this,'simplecal_event_metaboxes']);
			add_action('save_post_simplecal_event', [$this,'simplecal_event_save_meta']);
			// TODO: Enqueue admin JS to modify event end date on start date change, hide time on "all-day events, validate end time after start time, etc.
		}

	}

	// Allow creation of event-style posts
	function simplecal_create_event_posts() {
		register_post_type('simplecal_event', [
			'labels' => [
				'name' => 'Events',
				'singular_name' => 'Event',
				'add_new_item' => 'Add New Event',
				'edit_item' => 'Edit Event',
				'view_item' => 'View Event'
			],
			'supports' => [
				'title',
				'editor',
				'thumbnail'
			],
			'public' => true,
			'description' => 'Posts that contain information pertaining to an event, meeting, or any other calendar item',
			'query_var' => true,
			'show_in_rest' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-calendar-alt',
			'rewrite' => array('slug' => 'events', 'with_front' => false),
		]
		);
	}

	// TODO: Customize admin panel view to show start/end dates, venue, etc.

	function register_simplecal_widget() {
		register_widget('SimpleCal_Widget');
	}

	function register_simplecal_calendar_block() {
		register_block_type( __DIR__ . '/simplecal-calendar/build');
	}

	function simplecal_event_metaboxes() {
		global $post;
		add_meta_box('event_date_time', 'Event Date and Time', [$this,'simplecal_event_meta_datetime'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_location', 'Event Location', [$this,'simplecal_event_meta_location'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_moreinfo', 'Event More Info', [$this,'simplecal_event_meta_moreinfo'], 'simplecal_event', 'side', 'default');
	}

	function simplecal_event_meta_datetime($post, $args) {
		echo "<small>All times displayed in the <em>$this->tz_string</em> time zone based on your WordPress settings.</small>";

		$metabox_ids = array("start", "end"); // Cycle through the start and end time metaboxes for the event
		foreach ($metabox_ids as $key):
			if ($post->{"simplecal_event_{$key}_timestamp"}) :
				$time = "@" . $post->{"simplecal_event_{$key}_timestamp"};
				$timestamp = new DateTime($time); // Pull the meta in Unix timestamp format and set it as a DateTime object
			else :
				$time = 'now';
				$timestamp = new DateTime($time); // Pull the meta in Unix timestamp format and set it as a DateTime object
				$timestamp->setTime($timestamp->format('H'),0,0,0); // Reset the hours and seconds on the timestamp to 00
			endif;
			$timestamp->setTimeZone(self::$tz); // Specifying the timezone when creating a DateTime from a Unix timestamp does nothing, so we set it after
			
			$datestring = $timestamp->format('Y-m-d');
			$timestring = $timestamp->format('H:i');
		
			echo '<h3>' . ucfirst($key) . ' Date and Time</h3>';
			echo '<input type="date" name="event_' . $key . '_date" value="' . $datestring . '" ' . ($key == 'start' ? 'required ' : '') . '/>';
			echo '<input type="time" name="event_' . $key . '_time" value="' . $timestring . '" />';
		
			$alldayevent = $post->simplecal_event_all_day;

			echo ($key == 'start' ? '<br /><label><input type="checkbox" name="event_all_day" value="true" ' . checked($alldayevent) . ' /> All day event?</label>' : '');
		endforeach;
	}

	function simplecal_event_meta_location($post, $args) {
		echo '<h3>Physical</h3>';
		$metabox_ids = ["venue_name", "street_address", "city", "state"];
		foreach ($metabox_ids as $metabox_id) :
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			$event_meta = $post->{"simplecal_event_{$metabox_id}"};
			echo '<input type="text" class="fullwidth" name="event_' . $metabox_id . '" value="' . $event_meta . '"/><br />';
		endforeach;
		echo '<label for="event_country">Country</label><br />';
		$this->simplecal_country_input('event_country');
	?>
		<h3>Virtual</h3>
		<label for="event_virtual_platform">Virtual Platform</label><br />
		<input name="event_virtual_platform" type="text" list="virtual_platforms" placeholder="e.g. Zoom" value="<?= $post->simplecal_event_virtual_platform; ?>" />
		<datalist id="virtual_platforms">
			<option value="Google Meet">
			<option value="Jitsi">
			<option value="Microsoft Teams">
			<option value="Skype">
			<option value="Webex">
			<option value="Zoom">
		</datalist>
		<label for="event_meeting_link">Meeting Link</label><br />
		<input name="event_meeting_link" type="url" placeholder="e.g. https://zoom.us/j/8675309" value="<?= $post->simplecal_event_meeting_link; ?>" />
		
		<h3>Privacy</h3>
		<label>
			<input type="checkbox" name="event_private_location" value="1" <?php checked($post->simplecal_event_private_location, 1, true); ?>/>
			Show location only to logged-in members?
		</label>
	<?php
	}
		
	function simplecal_event_meta_moreinfo($post, $args) {
		$metabox_ids = ["website" => "url"];
		
		foreach ($metabox_ids as $metabox_id => $type) :
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			echo '<input type="' . $type . '" class="fullwidth" name="event_' . $metabox_id . '" value="' . $post->{"simplecal_event_{$metabox_id}"} . '" /><br />';
		endforeach;
	}
		
	function simplecal_event_save_meta($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) return;

		if (!key_exists('event_start_date', $_POST)) return;

		foreach (['event_start_time', 'event_end_time'] as $required) :
			if (! key_exists($required, $_POST)) :
				$_POST[$required] = '00:00';
			endif;
		endforeach;

		if (! key_exists('event_end_date', $_POST)) :
			$_POST['event_end_date'] = $_POST['event_start_date'];
		endif;

		$metabox_ids = ['start', 'end']; // Cycle through the start and end timestamps for the event
		foreach ($metabox_ids as $key) : // Format the fields and parse them as a DateTime object, then output the Unix timestamp to be saved to the event's meta
			//$timestamp = DateTime::createFromFormat('Y-m-d H:i', $_POST["{$key}_date"] . ' ' . $_POST["{$key}_time"]);
			$time = $_POST["event_{$key}_date"] . ' ' . $_POST["event_{$key}_time"];
			$timestamp = new DateTime($time, self::$tz);
			$events_meta["simplecal_event_{$key}_timestamp"] = $timestamp->format('U');
		endforeach;

		$metas = ['all_day', 'private_location', 'venue_name', 'street_address', 'city', 'state', 'country', 'virtual_platform','meeting_link', 'website'];
		foreach ($metas as $meta) :
			if (!empty($_POST['event_' . $meta])) :
				$events_meta['simplecal_event_' . $meta] = $_POST['event_' . $meta];
			else :
				$events_meta['simplecal_event_' . $meta] = NULL;
			endif;
		endforeach;
	
		foreach ($events_meta as $key => $value) :
			if ($post->post_type == 'revision') return;
			$value = implode(',', (array)$value);
			if ($post->{$key}):
				update_post_meta($post_id, $key, $value);
			else :
				add_post_meta($post_id, $key, $value);
			endif;
			if (!$value) delete_post_meta($post_id, '_' . $key);
		endforeach;
	}

	// Retrieve the date from within the loop
	public static function simplecal_event_get_the_date() {
		global $post;

		$starttime = "@" . $post->simplecal_event_start_timestamp;
		$starttimestamp = new DateTime($starttime);
		$starttimestamp->setTimeZone(self::$tz);
		
		if ($post->_end_timestamp) :
			$endtime = "@" . $post->simplecal_event_end_timestamp;
		else :
			$endtime = $starttime;
		endif;

		$endtimestamp = new DateTime($endtime);
		$endtimestamp->setTimeZone(self::$tz);

		if (! $post->simplecal_event_all_day) : // If it's *not* an all-day event, include the start time
			$eventdate = $starttimestamp->format('M d, Y \a\t g:i a');
		else :
			if ($starttimestamp->format('Y-m-d') == $endtimestamp->format('Y-m-d')) : // If the start and end dates are the same, just return the start date
				$eventdate = $starttimestamp->format('M d, Y');
			elseif ($starttimestamp->format('Y-m') == $endtimestamp->format('Y-m')) : // If the start and end month/year are the same, just provide a dashed date
				$eventdate = $starttimestamp->format('M d - ') . $endtimestamp->format('d, Y');
			else : // If months aren't the same, return the full start and end dates
				$eventdate = $starttimestamp->format('M d, Y') . ' - ' . $endtimestamp->format('M d, Y');
			endif;
		endif;
		return $eventdate;
	}

	function simplecal_get_country_input($dom_name) {
		// TODO: Add multinational support, obvs. Duh. But you know, America first.
		$output = "<select name=$dom_name><option value='US' selected='selected'>United States of America</option></select>";
		return $output;
	}

	function simplecal_country_input($dom_name) {
		echo $this->simplecal_get_country_input($dom_name);
	}

}

class SimpleCal_Widget extends WP_Widget {
	// TODO: Make this a wrapper that calls the block render? Prepopulates $attributes from the widget settings and then just includes render.php?
	public function __construct() {
		parent::__construct(
			'simplecal_widget',
			'SimpleCal Widget', // esc_html__()?! We don't need no stinkin' esc_html__()!
			['description' => 'Displays SimpleCal Events in an agenda or calendar view']
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo 'This is the widget body. This is where calendar events should get displayed.';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
?>
		<p>
		<label for="<?= esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?= esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?= esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?= esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}
}

$scplugin = new SimpleCal();

// TODO: Does this need to be outside the class definition? I can't remember why I did it this way.
add_shortcode('simplecal', [$scplugin, 'render_shortcode']);

if (is_admin()) {
	// Call the code for the settings page
	include_once(plugin_dir_path(__FILE__) . 'options.php');
}

?>