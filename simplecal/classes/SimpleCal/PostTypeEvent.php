<?php

namespace SimpleCal;

class PostTypeEvent {
	public function __construct() {
		global $pagenow;

		// register CPT on init
		add_action('init', [$this, 'cpt_register']);

		// REST exposure of meta is registered within cpt_register

		// Admin-specific hooks for columns and metaboxes
		if (is_admin()) {
			if ('edit.php' == $pagenow && isset($_GET['post_type']) && 'simplecal_event' == $_GET['post_type']) {
				add_filter("manage_simplecal_event_posts_columns", [$this, 'columns_set']);
				add_filter("manage_edit-simplecal_event_sortable_columns", [$this, 'columns_set_sortable']);
				add_action("manage_simplecal_event_posts_custom_column", [$this, 'columns_set_values'], 10, 2);
				add_action("pre_get_posts", [$this, 'columns_sort']);
			}
			if ( in_array($pagenow, ['post.php','post-new.php']) ) {
				add_action('add_meta_boxes', [$this, 'cpt_register_metaboxes']);
				add_action('save_post_simplecal_event', [$this, 'cpt_save_meta']);
			}
		} else {
			add_filter('single_template', [$this, 'cpt_register_templates']);
			add_filter('archive_template', [$this, 'cpt_register_templates']);
		}
	}

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
				'thumbnail',
				'custom-fields'
			],
			'public' => true,
			'description' => 'Posts that contain information pertaining to an event, meeting, or any other calendar item',
			'query_var' => true,
			'show_in_rest' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-calendar-alt', // TODO: Replace with custom SVG icon
			'rewrite' => ['slug' => get_option('simplecal_slug', 'events'), 'with_front' => false],
			'rest_base' => 'simplecal_event',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'taxonomies' => ['post_tag'],
			'has_archive' => get_option('simplecal_slug', 'events'),
			'show_in_rest' => true
		]);
		flush_rewrite_rules();

		$meta = [
			'simplecal_event_start_timestamp' => 'object',
			'simplecal_event_end_timestamp' => 'object',
			'simplecal_event_end_all_day' => 'boolean',
			'simplecal_event_venue_name' => 'string',
			'simplecal_event_street_adress' => 'string',
			'simplecal_event_city' => 'string',
			'simplecal_event_state' => 'string',
			'simplecal_event_country' => 'string',
			'simplecal_event_virtual_platform' => 'string',
			'simplecal_event_meeting_link' => 'string',
			'simplecal_event_website' => 'string',
			'simplecal_event_private_location' => 'boolean',
			'simplecal_event_recurring_link' => 'string'
		];

		foreach ($meta as $field => $type) {
			register_post_meta('simplecal_event', $field, [
				'type' => $type,
				'show_in_rest' => true
			]);
		}

		register_rest_field('simplecal_event', 'meta', [
			'get_callback' => function ($data) {
				return get_post_meta($data['id'], '', '');
			}
		]);
	}

	function cpt_register_templates($template) {
		if (wp_is_block_theme()) {
			return $template;
		}

		global $post;

		if (is_post_type_archive('simplecal_event')) {
			return Plugin::$path . '/templates/legacy-archive-simplecal_event.php';
		}
		if ($post && 'simplecal_event' === $post->post_type) {
			return Plugin::$path . '/templates/legacy-single-simplecal_event.php';
		}
		return $template;
	}

	/* Admin columns */
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
		$post_start = isset($meta['simplecal_event_start_timestamp'][0]) ? new \DateTime($meta['simplecal_event_start_timestamp'][0]) : null;
		$post_end = isset($meta['simplecal_event_end_timestamp'][0]) ? new \DateTime($meta['simplecal_event_end_timestamp'][0]) : null;

		$value = '';

		switch  ($column_name) {
			case 'startDate':
				$value = $post_start ? $post_start->format('m/d/Y') : '';
				break;
			case 'endDate':
				$value = $post_end ? $post_end->format('m/d/Y') : '';
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
				$value = (array_key_exists('simplecal_event_country', $meta) ? $meta['simplecal_event_country'][0] : null);
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

	//// METABOXES ////
	function cpt_register_metaboxes() {
		add_meta_box('event_date_time', 'Event Date and Time', [$this,'cpt_meta_box_datetime'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_location', 'Event Location', [$this,'cpt_meta_box_location'], 'simplecal_event', 'side', 'default');
		add_meta_box('event_moreinfo', 'Event More Info', [$this,'cpt_meta_box_moreinfo'], 'simplecal_event', 'side', 'default');
	}

	function cpt_meta_box_datetime($post, $args) {
		$alldayevent = $post->simplecal_event_all_day;

		echo '<div style="margin: 1em 0;"><label><input type="checkbox" name="event_all_day" id="simplecal_event_all_day" value="true" ' . ($alldayevent ? 'checked="checked"' : '') . ' /> All day event?</label></div>';

		$metabox_ids = ["start", "end"];
		foreach ($metabox_ids as $key) {
			if ($post->{"simplecal_event_{$key}_timestamp"}) {
				$timestamp = new \DateTime($post->{"simplecal_event_{$key}_timestamp"});
			} else {
				$timestamp = new \DateTime('now', Plugin::$tz);
				$timestamp->setTime($timestamp->format('H'),0,0,0);
			}

			$datetime_string = $alldayevent ? $timestamp->format('Y-m-d') : $timestamp->format('Y-m-d\TH:i');

			echo '<h3>' . ucfirst($key) . ' Date and Time</h3>';
			echo '<input type="' . ($alldayevent ? 'date' : 'datetime-local') . '" name="event_' . $key . '_datetime" id="simplecal_event_' . $key . '_datetime" value="' . $datetime_string . '" ' . ($key == 'start' ? 'required ' : '') . '/>';
		}

		$post_timezone = $timestamp->getTimezone();

		$timezone_list = Helper::timezone_list();

		echo '<h3>Timezone</h3>';
		echo '<input type="text" list="timezones" id="event_timezone" name="event_timezone" value="' . ($post->simplecal_event_timezone ?? wp_timezone_string()) . '"><br />';
		echo '<datalist id="timezones">';
		foreach ($timezone_list as $timezone => $display_name) {
			echo "<option value='$timezone'>$display_name</option>";
		}
		echo '</datalist>';

		echo "<small>The timezone defaults to <em>" . wp_timezone_string() . "</em> time zone based on your WordPress settings.</small>";

		echo '<div id="simplecal_event_datetime_error" style="display: none; color: red;"></div>';
	}

	function cpt_meta_box_location($post, $args) {
		echo '<h3>Physical</h3>';
		$metabox_ids = ["venue_name", "street_address", "city"];
		foreach ($metabox_ids as $metabox_id) {
			echo '<label for="simplecal_event_' . $metabox_id . '">' . mb_convert_case(str_replace('_', ' ', $metabox_id), MB_CASE_TITLE, 'UTF-8') . ':</label><br />';
			$event_meta = $post->{"simplecal_event_{$metabox_id}"};
			echo '<input type="text" class="fullwidth" name="event_' . $metabox_id . '" id="simplecal_event_' . $metabox_id . '" value="' . $event_meta . '" /><br />';
		}
		?>
		<label for="event_country">Country</label><br />
		<?php Helper::country_input($post->simplecal_event_country, 'event_country', 'simplecal_event_country'); ?>
		<br />

		<div id="simplecal_event_state_us_wrapper" style="display:none;">
			<label for="simplecal_event_state_us">State</label><br />
			<?php Helper::state_input($post->simplecal_event_state, "event_state_us", "simplecal_event_state_us"); ?>
		</div>
		<div id="simplecal_event_state_other_wrapper" style="display:none;">
			<label for="simplecal_event_state_other">State / County / Province</label><br />
			<input type="text" name="event_state" id="simplecal_event_state_other" value='<?php echo $post->simplecal_event_state; ?>'/>
		</div>
		<?php

		echo '<h3>Virtual</h3>';
		echo '<label for="event_virtual_platform">Virtual Platform</label><br />';
		echo '<input name="event_virtual_platform" type="text" list="virtual_platforms" placeholder="e.g. Zoom" value="'. $post->simplecal_event_virtual_platform .'" /><br />';
		echo '<datalist id="virtual_platforms">
			<option value="Google Meet">
			<option value="Jitsi">
			<option value="Microsoft Teams">
			<option value="Skype">
			<option value="Webex">
			<option value="Zoom">
		</datalist>';
		echo '<label for="event_meeting_link">Meeting Link</label><br />';
		echo '<input name="event_meeting_link" type="url" placeholder="e.g. https://zoom.us/j/8675309" value="'. $post->simplecal_event_meeting_link .'" />';

		echo '<h3>Privacy</h3>';
		echo '<label><input type="checkbox" name="event_private_location" value="1" ' . checked($post->simplecal_event_private_location, 1, false) . '/> Show location only to logged-in members?</label>';
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

		$metabox_ids = ['start', 'end'];
		foreach ($metabox_ids as $key) :
			$timestamp = new \DateTime($_POST["event_{$key}_datetime"], ($_POST['event_timezone'] ? new \DateTimeZone($_POST['event_timezone']) : Plugin::$tz));
			$events_meta["simplecal_event_{$key}_timestamp"] = $timestamp->format(DATE_ATOM);
		endforeach;

		$metas = ['timezone', 'all_day', 'private_location', 'venue_name', 'street_address', 'city', 'country', 'virtual_platform','meeting_link', 'website'];
		foreach ($metas as $meta) {
			if (isset($_POST['event_' . $meta])) {
				$events_meta['simplecal_event_' . $meta] = $_POST['event_' . $meta];
			} else {
				$events_meta['simplecal_event_' . $meta] = NULL;
			}
		}

		if (!empty($_POST['event_venue_name'].$_POST['event_street_address'].$_POST['event_city'])) {
			$events_meta['simplecal_event_state'] = ($events_meta['simplecal_event_country'] == 'United States' ? $_POST['event_state_us'] : $_POST['event_state']);
		}

		foreach ($events_meta as $key => $value) {
			$value = implode(',', (array)$value);
			update_post_meta($post_id, $key, $value);
			if (!$value) delete_post_meta($post_id, $key);
		}

		if ($events_meta['simplecal_event_state']) {
			update_option('simplecal_last_state', $events_meta['simplecal_event_state'], false);
		}

		if ($events_meta['simplecal_event_country']) {
			update_option('simplecal_last_country', $events_meta['simplecal_event_country'], false);
		}
	}
}
?>