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

// TODO: Debugging shortcut... make sure these are commented out or set to "0" (or false) in the production site
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
		add_action('init',[$this,'create_event_posts']);
		add_action('widgets_init', [$this,'register_widget']);
		add_action( 'init', [$this,'register_block']);
		
		if (is_admin()) {
			add_action('add_meta_boxes', [$this,'event_metaboxes']);
			add_action('save_post_simplecal_event', [$this,'event_save_meta']);
			add_action('admin_enqueue_scripts', [$this,'enqueue_admin_scripts']);
			// TODO: Add CSS for admin panel
		}
	}

	function enqueue_admin_scripts($hook) {
		if (!in_array($hook, ['post.php','post-new.php'])) {
			return;
		}
		wp_enqueue_script('simplecal_admin_script', plugin_dir_url(__FILE__). 'js/admin.js');
	}

	// Allow creation of event-style posts
	function create_event_posts() {
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

	function register_widget() {
		register_widget('SimpleCal_Widget');
	}

	function register_block() {
		register_block_type( __DIR__ . '/simplecal-calendar/build');
	}

	function event_metaboxes() {
		global $post;
		add_meta_box('event_date_time', 'Event Date and Time', [$this,'event_meta_datetime'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_location', 'Event Location', [$this,'event_meta_location'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_moreinfo', 'Event More Info', [$this,'event_meta_moreinfo'], 'simplecal_event', 'side', 'default');
	}

	function event_meta_datetime($post, $args) {
		echo "<small>All times displayed in the <em>$this->tz_string</em> time zone based on your WordPress settings.</small>";

		$alldayevent = $post->simplecal_event_all_day;

		echo '<div style="margin: 1em 0;"><label><input type="checkbox" name="event_all_day" id="simplecal_event_all_day" value="true" ' . checked($alldayevent) . ' /> All day event?</label></div>';

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
			
			$datetime_string = $timestamp->format('Y-m-d\TH:i');
		
			echo '<h3>' . ucfirst($key) . ' Date and Time</h3>';
			echo '<input type="datetime-local" name="event_' . $key . '_datetime" id="simplecal_event_' . $key . '_datetime" value="' . $datetime_string . '" ' . ($key == 'start' ? 'required ' : '') . '/>';

		endforeach;

		echo '<div id="simplecal_event_datetime_error" style="display: none; color: red;"><p>The event\'s end date/time must be after the start date/time.</p></div>';
	}

	function event_meta_location($post, $args) {
		echo '<h3>Physical</h3>';
		$metabox_ids = ["venue_name", "street_address", "city"];
		foreach ($metabox_ids as $metabox_id) :
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			$event_meta = $post->{"simplecal_event_{$metabox_id}"};
			echo '<input type="text" class="fullwidth" name="event_' . $metabox_id . '" id="simplecal_event_' . $metabox_id . '" value="' . $event_meta . '" /><br />';
		endforeach;
?>
		<label for="event_state">State</label><br />
		<?php $this->state_input("event_state", $post->{"simplecal_event_state"}); ?>
		<br />
		
		<label for="event_country">Country</label><br />
		<input type="text" class="fullwidth" name="event_country" id="simplecal_event_country" disabled value="<?php echo $post->{"simplecal_event_country"} ?? "United States of America"; ?>"/>
		<br />
<?php
		// TODO: Find a way to populate country and state lists for international. https://github.com/dr5hn/countries-states-cities-database seems like an option, combined with some AJAX. Places API is too expensive to offer, and having each person get their own API key seems cumbersome.
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
		
	function event_meta_moreinfo($post, $args) {
		$metabox_ids = ["website" => "url"];
		
		foreach ($metabox_ids as $metabox_id => $type) :
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			echo '<input type="' . $type . '" class="fullwidth" name="event_' . $metabox_id . '" value="' . $post->{"simplecal_event_{$metabox_id}"} . '" /><br />';
		endforeach;
	}
		
	function event_save_meta($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) return;

		if (!key_exists('event_start_datetime', $_POST)) return;

		if (!key_exists('event_end_datetime', $_POST)) :
			$_POST['event_end_datetime'] = $_POST['event_start_datetime'];
		endif;

		$metabox_ids = ['start', 'end']; // Cycle through the start and end timestamps for the event
		foreach ($metabox_ids as $key) : // Format the fields and parse them as a DateTime object, then output the Unix timestamp to be saved to the event's meta
			//$timestamp = DateTime::createFromFormat('Y-m-d H:i', $_POST["{$key}_date"] . ' ' . $_POST["{$key}_time"]);
			$time = str_replace('T',' ',$_POST["event_{$key}_datetime"]);
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

		// Save the selected state to prepopulate it on the next event.
		// TODO: Implement this for other fields? Or like a favorites list?
		if ($events_meta['simplecal_event_state']) :
			update_option('simplecal_last_state', $events_meta['simplecal_event_state']);
		endif;
	}

	// Retrieve the date from within the loop
	public static function event_get_the_date($format) {
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

		switch ($format) :
			case "datetime" :
				if (! $post->simplecal_event_all_day) : // If it's *not* an all-day event, include the start time
					$data = $starttimestamp->format('M d, Y \a\t g:i a');
				else :
					if ($starttimestamp->format('Y-m-d') == $endtimestamp->format('Y-m-d')) : // If the start and end dates are the same, just return the start date
						$data = $starttimestamp->format('M d, Y');
					elseif ($starttimestamp->format('Y-m') == $endtimestamp->format('Y-m')) : // If the start and end month/year are the same, just provide a dashed date
						$data = $starttimestamp->format('M d - ') . $endtimestamp->format('d, Y');
					else : // If months aren't the same, return the full start and end dates
						$data = $starttimestamp->format('M d, Y') . ' - ' . $endtimestamp->format('M d, Y');
					endif;
				endif;
				break;
			case "date" :
				if ($starttimestamp->format('Y-m-d') == $endtimestamp->format('Y-m-d')) : // If the start and end dates are the same, just return the start date
					$data = $starttimestamp->format('M d, Y');
				elseif ($starttimestamp->format('Y-m') == $endtimestamp->format('Y-m')) : // If the start and end month/year are the same, just provide a dashed date
					$data = $starttimestamp->format('M d - ') . $endtimestamp->format('d, Y');
				else : // If months aren't the same, return the full start and end dates
					$data = $starttimestamp->format('M d, Y') . ' - ' . $endtimestamp->format('M d, Y');
				endif;
				break;
			case "time" :
				if ($post->simplecal_event_all_day) : // If it's *not* an all-day event, include the start time
					$data = "All day";
				else :
					$data = $starttimestamp->format('g:i a');
					if ($endtime != $starttime) :
						$data .= ' - ' . $endtimestamp->format('g:i a');
					endif;
				endif;
				break;
		endswitch;

		return $data;
	}

	public static function get_formatted_website($url, $link_text = "More information") {
		if (preg_match('/^(?:https?:\/\/)?(?:www\.)?(.*\..*)\/?/', $url, $matches)) {
			$domain = explode('/',$matches[1])[0];
		} else {
			$domain = explode('/',$url)[0];
		}
		$formatted = "<a href=\"$url\" target=\"_blank\">$domain</a>";
		return $formatted;
	}

	function get_state_input($dom_name, $field_value) {
		if (empty($field_value)) {
			$field_value = get_option('simplecal_last_state','');
		}

		$states = [
			'AL'=>'Alabama',
			'AK'=>'Alaska',
			'AZ'=>'Arizona',
			'AR'=>'Arkansas',
			'CA'=>'California',
			'CO'=>'Colorado',
			'CT'=>'Connecticut',
			'DE'=>'Delaware',
			'DC'=>'District of Columbia',
			'FL'=>'Florida',
			'GA'=>'Georgia',
			'HI'=>'Hawaii',
			'ID'=>'Idaho',
			'IL'=>'Illinois',
			'IN'=>'Indiana',
			'IA'=>'Iowa',
			'KS'=>'Kansas',
			'KY'=>'Kentucky',
			'LA'=>'Louisiana',
			'ME'=>'Maine',
			'MD'=>'Maryland',
			'MA'=>'Massachusetts',
			'MI'=>'Michigan',
			'MN'=>'Minnesota',
			'MS'=>'Mississippi',
			'MO'=>'Missouri',
			'MT'=>'Montana',
			'NE'=>'Nebraska',
			'NV'=>'Nevada',
			'NH'=>'New Hampshire',
			'NJ'=>'New Jersey',
			'NM'=>'New Mexico',
			'NY'=>'New York',
			'NC'=>'North Carolina',
			'ND'=>'North Dakota',
			'OH'=>'Ohio',
			'OK'=>'Oklahoma',
			'OR'=>'Oregon',
			'PA'=>'Pennsylvania',
			'RI'=>'Rhode Island',
			'SC'=>'South Carolina',
			'SD'=>'South Dakota',
			'TN'=>'Tennessee',
			'TX'=>'Texas',
			'UT'=>'Utah',
			'VT'=>'Vermont',
			'VA'=>'Virginia',
			'WA'=>'Washington',
			'WV'=>'West Virginia',
			'WI'=>'Wisconsin',
			'WY'=>'Wyoming'
		];

		$output = "<select name=$dom_name>";
		foreach ($states as $code=>$name) {
			$output .= "<option value=\"$code\"" . ($field_value == $code ? ' selected ' : '') . ">$name</option>";
		}

		$output .= "</select>";
		return $output;
	}

	function state_input($dom_name, $field_value) {
		echo $this->get_state_input($dom_name, $field_value);
	}

	function get_country_input($dom_name) {
		// TODO: Add multinational support, obvs. Duh. But you know, America first.
		$output = "<select name=$dom_name><option value='US' selected='selected'>United States of America</option></select>";
		return $output;
	}

	function country_input($dom_name) {
		echo $this->get_country_input($dom_name);
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