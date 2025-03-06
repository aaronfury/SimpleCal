<?php
class SimpleCal {
	public static $tz;
	private $tz_string;
	
	public function __construct() {
		global $pagenow;

		self::$tz = wp_timezone();
		$this->tz_string = wp_timezone_string();

		add_action('init', [$this, 'cpt_register']);
		add_action('init', [$this, 'block_register']);
		add_action('init', [$this, 'block_template_register']);
		add_action('widgets_init', [$this, 'widget_register']);

		add_action('wp_ajax_simplecal_get_events', [$this, 'ajax_get_events']);
		add_action('wp_ajax_nopriv_simplecal_get_events', [$this, 'ajax_get_events']);
		
		if (is_admin()) {
			if ('edit.php' == $pagenow && 'simplecal_event' == $_GET['post_type']) {
				add_filter("manage_simplecal_event_posts_columns", [$this, 'columns_set']);
				add_filter("manage_edit-simplecal_event_sortable_columns", [$this, 'columns_set_sortable']);
				add_action("manage_simplecal_event_posts_custom_column", [$this, 'columns_set_values'], 10, 2);
				add_action("pre_get_posts", [$this, 'columns_sort']);
			}
			if ( in_array($pagenow,['post.php','post-new.php'])) {
				add_action('add_meta_boxes', [$this,'cpt_register_metaboxes']);
				add_action('save_post_simplecal_event', [$this,'cpt_save_meta']);
			}
			
			add_action('admin_enqueue_scripts', [$this,'enqueue_admin_scripts']);
			// TODO: Add CSS for admin panel
		} else {
			add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
			add_filter('single_template', [$this,'cpt_register_templates']);
			add_filter('archive_template', [$this,'cpt_register_templates']);
			add_action('pre_get_posts', [$this, 'cpt_prepare_archive_query']);
		}
	}

	//// CUSTOMIZE COLUMNS FOR THE CPT IN WP-ADMIN ////
	function columns_set($cols) {
		$cols = [
			'cb' => $cols['cb'],
			'title' => 'Title',
			'startDate' => 'Start Date',
			'endDate' => 'End Date',
			'location' => 'Location',
			'city' => 'City',
			'state' => 'State',
			'country' => 'Country'
		];
		
		return $cols;
	}
	
	function columns_set_sortable($cols) {
		$cols = [
			'title' => 'Title',
			'startDate' => 'Start Date',
			'endDate' => 'End Date',
			'location' => 'Location',
			'city' => 'City',
			'state' => 'State',
			'country' => 'Country'
		];
		
		return $cols;
	}

	function columns_set_values($column_name, $post_id) {
		$meta = get_post_meta($post_id);
		$post_start = new DateTime($meta['simplecal_event_start_timestamp'][0]);
		$post_end = new DateTime($meta['simplecal_event_end_timestamp'][0]);

		switch  ($column_name) {
			case 'startDate':
				$value = $post_start->format('m/d/Y');
				break;
			case 'endDate':
				$value = $post_end->format('m/d/Y');
				break;
			case 'location':
				$value = (array_key_exists('simplecal_event_venue_name',$meta) ? $meta['simplecal_event_venue_name'][0] : null);
				break;
			case 'city':
				$value = (array_key_exists('simplecal_event_city', $meta) ? $meta['simplecal_event_city'][0] : null);
				break;
			case 'state':
				$value = (array_key_exists('simplecal_event_state', $meta) ? $meta['simplecal_event_state'][0] : null);
				break;
			case 'country':
				$value = (array_key_exists('simplecaL_event_country', $meta) ? $meta['simplecal_event_country'][0] : null);
				break;
		}
		echo $value;
	}

	function columns_sort($query) {
		if ('simplecal_event' != $query->get('post_type')) {
			return;
		}
		
		$orderby = $query->get('orderby');
		switch ($orderby) {
			case 'Start Date':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_start_timestamp');
				$query->set('meta_type', 'DATETIME');
				break;
			case 'End Date':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_end_timestamp');
				$query->set('meta_type', 'DATETIME');
				break;
			case 'Location':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_venue_name');
				break;
			case 'City':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_city');
				break;
			case 'State':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_state');
				break;
			case 'Country':
				$query->set('orderby', 'meta_value');
				$query->set('meta_key', 'simplecal_event_country');
				break;
			default:
				$query->set('orderby', 'title');
		}
	}

	//// REGISTER CUSTOM POST TYPE AND META BOXES ////

	function cpt_register() {
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
			'rewrite' => ['slug' => 'events', 'with_front' => false],
			'rest_base' => 'simplecal_event',
    		'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive' => 'events',
			'show_in_rest' => true
		]
		);

		register_rest_field( 'simplecal_event', 'meta', [
			'get_callback' => function ($data) {
				return get_post_meta( $data['id'], '', '' );
			}]
		);
	}

	function cpt_register_templates($template) {
		if (wp_is_block_theme()) {
			return $template;
		}
		
		global $post;
	
		if (is_post_type_archive('simplecal_event')) {
			return plugin_dir_path(__FILE__) . '../templates/legacy-archive-simplecal_event.php';
		}
		if ('simplecal_event' === $post->post_type) {
			return plugin_dir_path(__FILE__) . '../templates/legacy-single-simplecal_event.php';
		}
		return $template;
	}

	function cpt_prepare_archive_query($query) {
		if ($query->is_post_type_archive('simplecal_event')) {
			$query->set('posts_per_page',3);
		}
	}

	function cpt_register_metaboxes() {
		global $post;
		add_meta_box('event_date_time', 'Event Date and Time', [$this,'cpt_meta_box_datetime'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_location', 'Event Location', [$this,'cpt_meta_box_location'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_moreinfo', 'Event More Info', [$this,'cpt_meta_box_moreinfo'], 'simplecal_event', 'side', 'default');
	}

	function cpt_meta_box_datetime($post, $args) {
		$alldayevent = $post->simplecal_event_all_day;

		echo '<div style="margin: 1em 0;"><label><input type="checkbox" name="event_all_day" id="simplecal_event_all_day" value="true" ' . ($alldayevent ? 'checked="checked"' : '') . ' /> All day event?</label></div>';

		$metabox_ids = ["start", "end"]; // Cycle through the start and end time metaboxes for the event
		foreach ($metabox_ids as $key) {
			if ($post->{"simplecal_event_{$key}_timestamp"}) {
				$timestamp = new DateTime($post->{"simplecal_event_{$key}_timestamp"}); // Pull the meta in Unix timestamp format and set it as a DateTime object
			} else {
				$timestamp = new DateTime('now', self::$tz); // Pull the meta in Unix timestamp format and set it as a DateTime object
				$timestamp->setTime($timestamp->format('H'),0,0,0); // Reset the hours and seconds on the timestamp to 00
			}
			
			$datetime_string = $alldayevent ? $timestamp->format('Y-m-d') : $timestamp->format('Y-m-d\TH:i');
		
			echo '<h3>' . ucfirst($key) . ' Date and Time</h3>';
			echo '<input type="' . ($alldayevent ? 'date' : 'datetime-local') . '" name="event_' . $key . '_datetime" id="simplecal_event_' . $key . '_datetime" value="' . $datetime_string . '" ' . ($key == 'start' ? 'required ' : '') . '/>';
		}

		$post_timezone = $timestamp->getTimezone(); // Just use the last $timestamp object, presumably from the end_timestamp

		//$timezones = DateTimeZone::listIdentifiers();
		$timezone_list = $this->timezone_list();
		
		echo '<h3>Timezone</h3>';
		echo '<input type="text" list="timezones" id="event_timezone" name="event_timezone" value="' . ($post->simplecal_event_timezone ?? $this->tz_string) . '"><br />';
		echo '<datalist id="timezones">';
		foreach ($timezone_list as $timezone => $display_name) {
			echo "<option value='$timezone'>$display_name</option>";
		}
		echo '</datalist>';

		echo "<small>The timezone defaults to <em>$this->tz_string</em> time zone based on your WordPress settings.</small>";

		echo '<div id="simplecal_event_datetime_error" style="display: none; color: red;"></div>';
	}

	function cpt_meta_box_location($post, $args) {
		echo '<h3>Physical</h3>';
		$metabox_ids = ["venue_name", "street_address", "city"];
		foreach ($metabox_ids as $metabox_id) {
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			$event_meta = $post->{"simplecal_event_{$metabox_id}"};
			echo '<input type="text" class="fullwidth" name="event_' . $metabox_id . '" id="simplecal_event_' . $metabox_id . '" value="' . $event_meta . '" /><br />';
		}
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

	function cpt_meta_box_moreinfo($post, $args) {
		$metabox_ids = ["website" => "url"];
		
		foreach ($metabox_ids as $metabox_id => $type) {
			echo '<label for="event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			echo '<input type="' . $type . '" class="fullwidth" name="event_' . $metabox_id . '" value="' . $post->{"simplecal_event_{$metabox_id}"} . '" /><br />';
		}
	}

	function cpt_save_meta($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) return;

		if (!key_exists('event_start_datetime', $_POST)) return;

		if (!key_exists('event_end_datetime', $_POST)) {
			$_POST['event_end_datetime'] = $_POST['event_start_datetime'];
		}

		global $post;

		$metabox_ids = ['start', 'end']; // Cycle through the start and end timestamps for the event
		foreach ($metabox_ids as $key) : // Format the fields and parse them as a DateTime object, then output the Unix timestamp to be saved to the event's meta
			$timestamp = new DateTime($_POST["event_{$key}_datetime"], ($_POST['event_timezone'] ? new DateTimeZone($_POST['event_timezone']) : self::$tz));
			$events_meta["simplecal_event_{$key}_timestamp"] = $timestamp->format(DATE_ATOM);
		endforeach;

		$metas = ['timezone', 'all_day', 'private_location', 'venue_name', 'street_address', 'city', 'state', 'country', 'virtual_platform','meeting_link', 'website'];
		foreach ($metas as $meta) {
			if (isset($_POST['event_' . $meta])) {
				$events_meta['simplecal_event_' . $meta] = $_POST['event_' . $meta];
			} else {
				$events_meta['simplecal_event_' . $meta] = NULL;
			}
		}

		foreach ($events_meta as $key => $value) {
			$value = implode(',', (array)$value);
			update_post_meta($post_id, $key, $value);
			if (!$value) delete_post_meta($post_id, $key);
		}

		// Save the selected state to prepopulate it on the next event.
		// TODO: Implement this for other fields? Or like a favorites list?
		if ($events_meta['simplecal_event_state']) {
			update_option('simplecal_last_state', $events_meta['simplecal_event_state']);
		}
	}
	
	function enqueue_admin_scripts($hook) {
		if (in_array($hook, ['post.php','post-new.php'])) {
			$screen = get_current_screen();

			if (is_object($screen) && 'simplecal_event' == $screen->post_type ){
				wp_enqueue_script('simplecal_admin_script', plugin_dir_url(__FILE__). '../js/admin.js');
			}
		}
		return;
	}

	function enqueue_styles($hook) {
		//wp_enqueue_style('simplecal_css', plugin_dir_url(__FILE__) . '/templates/style.css');
	}

	//// REGISTER WP BLOCK AND WIDGET ////
	function widget_register() {
		register_widget('SimpleCal_Widget');
	}
	
	function block_register() {
		register_block_type(dirname(__FILE__) . '/../simplecal-calendar/build');
	}

	function block_template_register() {
		register_block_template(
			'simplecal//archive-simplecal_event',
			[
				'title' => 'SimpleCal Archive',
				'description' => 'An agenda-style view of SimpleCal events',
				'content' => self::get_template_content('archive-simplecal_event.php')
			]
		);
		register_block_template(
			'simplecal//single-simplecal_event',
			[
				'title' => 'SimpleCal Event',
				'description' => 'The layout for a single event',
				'content' => self::get_template_content('single-simplecal_event.php')
			]
		);
	}

	function get_template_content($template) {
		ob_start();
		include __DIR__ . "/../templates/{$template}";
		return ob_get_clean();
	}

	//// WP AJAX INTERFACE ////
	public function ajax_get_events() {
		// Determine the desired page
		$page = intval((array_key_exists('page', $_POST) ? $_POST['page'] : 0));
		$posts_per_page = intval((array_key_exists('agendaPostsPerPage', $_POST) ? ($_POST['agendaPostsPerPage'] == 0 ? -1 : $_POST['agendaPostsPerPage']) : 10));
		$page_param = ($page < 0 ? -$page : $page + 1);

		// Build the query parameters
		$args = [
			'post_type' => 'simplecal_event',
			'post_status' => 'publish',
			'orderby' => 'meta_value',
			'meta_key' => 'simplecal_event_start_timestamp',
			'meta_type' => 'DATETIME'
		];

		// Build the query for agenda views
		if ($_POST['displayStyle'] == 'agenda') {
			$args = $args + [
				'posts_per_page' => $posts_per_page,
				'paged' => $page_param,
			];
			
			if ($page >= 0) {
				$args = $args + [
					'meta_value' => date('Y-m-d H:i:s'),
					'meta_compare' => '>=',
					'order' => 'ASC'
				];
			} else {
				$args = $args + [
					'order' => 'DESC'
				];

				if (array_key_exists('displayPastEventsDays', $_POST)) {
					if ($_POST['displayPastEventsDays'] == 0) {
						$args = $args + [
							'meta_value' => date('Y-m-d H:i:s'),
							'meta_compare' => '<='
						];
					} else {
						$past_event_cutoff = new DateTime("-{$_POST['displayPastEventsDays']} days");
						$args = $args + [
							'meta_value' => [$past_event_cutoff->format('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
							'meta_compare' => 'BETWEEN'
						];
					}
				}
			}
		}

		// Build the query for calendar views
		$events = new WP_Query($args);

		if ($page < 0 && $events->have_posts()) { // Since "previous events" are searched in descending order from the current date, we flip the array of posts to have them show up in chronological order
			$events->posts = array_reverse($events->posts);
		}
		
		ob_start();

		if ($_POST['displayStyle'] == 'agenda') {
			include_once(plugin_dir_path(__FILE__) . '../templates/agenda-' . $_POST['agendaLayout'] . '.php');
		}

		$output = ob_get_clean();
		wp_reset_postdata();

		$more_prev = ((($page < 0) && ($events->max_num_pages > $page_param)) || $page >= 0);
		$more_next = ((($page >= 0) && ($events->max_num_pages > $page_param)) || $page < 0);
		wp_send_json_success([
			'output' => $output,
			'current_page' => $page,
			'current_page_param' => $page_param,
			'more_prev_pages' => $more_prev,
			'more_next_pages' => $more_next,
			'query' => $events->request,
			'query_vars' => $events->query_vars,
			'query_args' => $args,
			'filters' => $GLOBALS['wp_filter']
		]);		
	}

	//// UTILITIES ////
	
	// Filter events for a given month and year
	public function get_events_calendar($month, $year) {

	}

	// Retrieve the date from within the loop
	public static function event_get_the_date(string $date_or_time = 'both', string $start_or_end = "both", string $date_format = 'M d, Y', string $time_format = 'g:i a', string $span_link = ' - ', string $date_time_link = ' at ', bool $nbsp_on_null = false ) {
		// TODO: Add support for "doors" time. Maybe a separate function?
		global $post;
		$nbsp = '&nbsp;';

		if ($post) {
			$post_timezone = ($post->simplecal_event_timezone) ? new DateTimeZone($post->simplecal_event_timezone) : self::$tz;
			$starttimestamp = new DateTime($post->simplecal_event_start_timestamp, $post_timezone);
			
			if ($post->simplecal_event_end_timestamp && ($post->simplecal_event_start_timestamp != $post->simplecal_event_end_timestamp)) {
				$endtime = $post->simplecal_event_end_timestamp;
				$endtimestamp = new DateTime($endtime, $post_timezone);
			}
		} else { // This is for block themes and their API-based nonsense
			$post_timezone = self::$tz;
			$starttimestamp = new DateTime('now',$post_timezone);
		}

		$date_string = '';

		// Formatting is too different to mess with nested switches and whatnot
		switch ($start_or_end) {
			case 'start':
				switch ($date_or_time) {
					case 'date' :
						$date_string .= $starttimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $starttimestamp->format($date_format);
						if ($post && !$post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $date_time_link;
						}
					case 'time':
						if ($post && !$post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $starttimestamp->format($time_format);
						}
						break;
				}
				
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
			case 'end':
				if (!isset($endtime)) {
					return ($nbsp_on_null ? $nbsp : null);
				}

				switch ($date_or_time) {
					case 'date' :
						$date_string .= $endtimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $endtimestamp->format($date_format);
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
							$date_string .= $date_time_link;
						}
					case 'time':
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
							$date_string .= $endtimestamp->format($time_format);
						}
						break;
				}
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
			case 'both':
				switch ($date_or_time) {
					case 'date' :
						$date_string .= $starttimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $starttimestamp->format($date_format);
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, append date-time separation
							$date_string .= ' ';
						}
					case 'time':
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $starttimestamp->format($time_format);
						}
						break;
				}

				if (!isset($endtime)) {
					return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
				}

				if ($starttimestamp->format('ymd') == $endtimestamp->format('ymd')) { // If start and end date are the same
					if ('date' == $date_or_time) { // If it's only meant to return dates, then just return the start date
						return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
					}

					if (!$post->simplecal_event_all_day || $starttimestamp->format('Hi') != $endtimestamp->format('Hi')) { // If it's not an all-day event and start and end times are different, append the end time
						if ('both' == $date_or_time) $date_string .= ' ';
						$date_string .= $span_link . $endtimestamp->format($time_format);
					}

					return $date_string;
				} else { // If start and end date are different
					if ('time' != $date_or_time) { // Don't include the date if it's set to only return the time.
						$date_string .= $span_link . $endtimestamp->format($date_format);
					}
					
					switch ($date_or_time) {
						case 'date': // If it's only meant to return dates, return only the date portion
							return $date_string;
							case 'both':
								if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, append date-time separation
								$date_string .= ' ';
							}
						case 'time':
							if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
								$date_string .= $endtimestamp->format($time_format);
							}
							break;
					}
					
				}
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
		}
	}

	public static function event_has_valid_end_timestamp() {
		global $post;
		
		if ($post->simplecal_event_end_timestamp && ($post->simplecal_event_start_timestamp != $post->simplecal_event_end_timestamp)) {
			return true;
		} else {
			return false;
		}
	}

	public static function event_get_the_location($link_type = 'none') {
		global $post;
		
		if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
			$link = urlencode(implode(", ", array_filter([$post->simplecal_event_venue_name, $post->simplecal_event_street_address, $post->simplecal_event_city, $post->simplecal_event_state, $post->simplecal_event_country], 'strlen')));
			if ($link) $link = 'https://maps.google.com/maps?q=' . $link;
			
			$address = '';
			$address .= $post->simplecal_event_venue_name ? '<span class="simplecal_list_item_venue_name">' . $post->simplecal_event_venue_name . '<span>' : '';
			$address .= $post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state) ? '<span class="simplecal_list_item_venue_separator">, </span>' : '';
			$address .= $post->simplecal_event_city ? '<span class="simplecal_list_item_city">' . $post->simplecal_event_city . '</span>' : '';
			$address .= $post->simplecal_event_city && $post->simplecal_event_state ? '<span class="simplecal_list__item_city_separator">, </span>' : '';
			$address .= $post->simplecal_event_state ? '<span class="simplecal_list_item_state">' . $post->simplecal_event_state . '</span>' : '';

			switch ($link_type) {
				case 'address':
					$data = "<a href='$link' target='_blank'>$address</a>";
					break;
				case 'after':
					$data = "$address (<a href='$link' target='_blank'>Map</a>)";
					break;
				default:
					$data = $address;
			}

			return $data;
		} else {
			return null;
		}
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

	function timezone_list() {
		static $timezones = null;
		
		if ($timezones === null) {
			$timezones = [];
			$offsets = [];
			$now = new DateTime('now', new DateTimeZone('UTC'));
			
			foreach (DateTimeZone::listIdentifiers() as $timezone) {
				$now->setTimezone(new DateTimeZone($timezone));
				$offsets[] = $offset = $now->getOffset();
				$timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
			}
			
			array_multisort($offsets, $timezones);
		}
		
		return $timezones;
	}
	
	function format_GMT_offset($offset) {
		$hours = intval($offset / 3600);
		$minutes = abs(intval($offset % 3600 / 60));
		return 'GMT' . ($offset!==false ? sprintf('%+03d:%02d', $hours, $minutes) : '');
	}
	
	function format_timezone_name($name) {
		$name = str_replace('/', '- ', $name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('St ', 'St. ', $name);
		return $name;
	}
}
?>