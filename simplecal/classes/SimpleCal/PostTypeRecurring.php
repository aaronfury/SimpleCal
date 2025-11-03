<?php

namespace SimpleCal;

/**
 * Recurring events post type.
 *  TODO:
 * - Support for variables to plug in date/month/year into title/description
 * - Implement occurrence generation logic (in Helper class?)
 * - Cron job to create occurences
 * - Option to edit existing occurrences when parent is updated
 * - Figure out exclusion logic
 */
class PostTypeRecurring {

	public function __construct() {
		global $pagenow;

		// register recurring CPT on init
		add_action('init', [$this, 'cpt_register']);

		// REST exposure of meta is registered within cpt_register_recurring

		// Admin-specific hooks for metaboxes and save handling
		if (is_admin()) {
			if ( in_array($pagenow, ['post.php','post-new.php']) ) {
				// reuse the parent metabox UI for date/location/moreinfo
				add_action('add_meta_boxes', [$this, 'cpt_register_metaboxes_recurring']);
				// save handler for recurring CPT
				add_action('save_post_simplecal_recurring_event', [$this, 'cpt_save_meta_recurring']);
			}
		} else {
			// templates can be handled by parent if desired; keep default behavior
			add_filter('single_template', [$this, 'cpt_register_templates_recurring']);
			add_filter('archive_template', [$this, 'cpt_register_templates_recurring']);
		}
	}

	/**
	 * Register the recurring event CPT and its meta fields.
	 */
	public function cpt_register() {
		register_post_type('simplecal_recurring_event', [
			'labels' => [
				'name' => 'Recurring Events',
				'singular_name' => 'Recurring Event',
				'add_new_item' => 'Add New Recurring Event',
				'edit_item' => 'Edit Recurring Event',
				'view_item' => 'View Recurring Event'
			],
			'supports' => [
				'title',
				'editor',
				'thumbnail',
				'custom-fields'
			],
			'public' => true,
			'description' => 'Define recurring events for your calendar.',
			'query_var' => true,
			'show_in_rest' => true,
			'menu_position' => 6,
			'menu_icon' => 'dashicons-repeat', // TODO: Replace with custom SVG icon
			'rewrite' => ['slug' => 'recurring-events', 'with_front' => false],
			'rest_base' => 'simplecal_recurring_event',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'taxonomies' => ['post_tag'],
			'show_in_rest' => true
		]);

		$meta = [
			'simplecal_start_timestamp' => 'object',
			'simplecal_end_timestamp' => 'object',
			'simplecal_all_day' => 'boolean',
			'simplecal_venue_name' => 'string',
			'simplecal_street_adress' => 'string',
			'simplecal_city' => 'string',
			'simplecal_state' => 'string',
			'simplecal_country' => 'string',
			'simplecal_virtual_platform' => 'string',
			'simplecal_meeting_link' => 'string',
			'simplecal_website' => 'string',
			'simplecal_private_location' => 'boolean',
	// recurrence-specific fields
			'simplecal_recurrence_enabled' => 'boolean',
			'simplecal_recurrence_frequency' => 'string', // daily, weekly, monthly, yearly
			'simplecal_recurrence_interval' => 'integer',
			'simplecal_recurrence_byday' => 'string', // e.g. "MO,TU"
			'simplecal_recurrence_hwm' => 'object', // high water mark, i.e. the latest generated occurrence timestamp
			'simplecal_recurrence_end_after' => 'object', // end date or number of instances
			'simplecal_recurrence_max_creation' => 'integer', // maximum number of occurences to create
			'simplecal_recurrence_exclude' => 'string', // dates to exclude
		];

		foreach ($meta as $field => $type) {
			register_post_meta('simplecal_recurring_event', $field, [
				'type' => $type,
				'show_in_rest' => true
			]);
		}

		register_rest_field('simplecal_recurring_event', 'meta', [
			'get_callback' => function ($data) {
				return get_post_meta($data['id'], '', '');
			}
		]);
	}

	/**
	 * Add metaboxes for recurring CPT: reuse parent boxes and add recurrence box.
	 */
	public function cpt_register_metaboxes_recurring() {
		// reuse parent metabox callbacks for date/location/moreinfo
		add_meta_box('event_date_time', 'Event Date and Time', [$this,'cpt_meta_box_datetime'], 'simplecal_recurring_event', 'side', 'default');
		add_meta_box('event_location', 'Event Location', [$this,'cpt_meta_box_location'], 'simplecal_recurring_event', 'side', 'default');
		add_meta_box('event_moreinfo', 'Event More Info', [$this,'cpt_meta_box_moreinfo'], 'simplecal_recurring_event', 'side', 'default');

		// recurrence-specific box
		add_meta_box('event_recurrence', 'Event Recurrence', [$this, 'cpt_meta_box_recurrence'], 'simplecal_recurring_event', 'side', 'default');
	}

	/**
	 * Recurrence metabox UI.
	 */
	public function cpt_meta_box_recurrence($post, $args) {
		// load existing values (post meta accessible as properties on $post in WP)
		$enabled = $post->simplecal_recurring_recurrence_enabled ?? 0;
		$freq = $post->simplecal_recurring_recurrence_frequency ?? 'weekly';
		$interval = $post->simplecal_recurring_recurrence_interval ?? 1;
		$byday = $post->simplecal_recurring_recurrence_byday ?? '';
		$until = $post->simplecal_recurring_recurrence_until ?? '';

		echo '<label><input type="checkbox" name="recurrence_enabled" value="1" ' . checked(1, $enabled, false) . ' /> Enable recurrence?</label><br/><br/>';

		echo '<label for="recurrence_frequency">Frequency</label><br/>';
		echo '<select name="recurrence_frequency" id="recurrence_frequency">';
		$freqs = ['daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly','yearly'=>'Yearly'];
		foreach ($freqs as $key=>$label) {
			echo "<option value=\"$key\"" . ($freq == $key ? ' selected' : '') . ">$label</option>";
		}
		echo '</select><br/><br/>';

		echo '<label for="recurrence_interval">Interval</label><br/>';
		echo '<input type="number" name="recurrence_interval" id="recurrence_interval" min="1" value="' . esc_attr($interval) . '" style="width:60px;" /><br/><small>Every N occurrences (e.g. 2 = every 2 weeks)</small><br/><br/>';

		echo '<label for="recurrence_byday">By day (weekdays)</label><br/>';
		echo '<input type="text" name="recurrence_byday" id="recurrence_byday" placeholder="MO,TU,WE" value="' . esc_attr($byday) . '" /><br/><small>Comma separated day codes (MO,TU,...). Leave blank for default.</small><br/><br/>';

		echo '<label for="recurrence_until">Until</label><br/>';
		$until_value = $until ? (new \DateTime($until))->format('Y-m-d') : '';
		echo '<input type="date" name="recurrence_until" id="recurrence_until" value="' . esc_attr($until_value) . '" /><br/><small>Optional end date for the recurrence series.</small>';
	}

	/**
	 * Save handler for recurring CPT. Mirrors parent save logic but writes recurring-prefixed meta
	 */
	public function cpt_save_meta_recurring($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;
		if (!key_exists('event_start_datetime', $_POST)) return;

		if (!key_exists('event_end_datetime', $_POST)) {
			$_POST['event_end_datetime'] = $_POST['event_start_datetime'];
		}

		$metabox_ids = ['start', 'end'];
		foreach ($metabox_ids as $key) {
			$timestamp = new \DateTime($_POST["event_{$key}_datetime"], ($_POST['event_timezone'] ? new \DateTimeZone($_POST['event_timezone']) : Plugin::$tz));
			$events_meta["simplecal_recurring_{$key}_timestamp"] = $timestamp->format(DATE_ATOM);
		}

		$metas = ['timezone', 'all_day', 'private_location', 'venue_name', 'street_address', 'city', 'country', 'virtual_platform','meeting_link', 'website'];
		foreach ($metas as $meta) {
			if (isset($_POST['event_' . $meta])) {
				$events_meta['simplecal_recurring_' . $meta] = $_POST['event_' . $meta];
			} else {
				$events_meta['simplecal_recurring_' . $meta] = NULL;
			}
		}

		// Recurrence-specific fields
		$events_meta['simplecal_recurring_recurrence_enabled'] = isset($_POST['recurrence_enabled']) ? 1 : 0;
		$events_meta['simplecal_recurring_recurrence_frequency'] = $_POST['recurrence_frequency'] ?? null;
		$events_meta['simplecal_recurring_recurrence_interval'] = isset($_POST['recurrence_interval']) ? intval($_POST['recurrence_interval']) : null;
		$events_meta['simplecal_recurring_recurrence_byday'] = $_POST['recurrence_byday'] ?? null;
		if (!empty($_POST['recurrence_until'])) {
			$events_meta['simplecal_recurring_recurrence_until'] = (new \DateTime($_POST['recurrence_until']))->format(DATE_ATOM);
		} else {
			$events_meta['simplecal_recurring_recurrence_until'] = null;
		}

		// Save physical state logic (same as parent)
		if (!empty($_POST['event_venue_name'] . $_POST['event_street_address'] . $_POST['event_city'])) {
			$events_meta['simplecal_recurring_state'] = ($events_meta['simplecal_recurring_country'] == 'United States' ? $_POST['event_state_us'] : $_POST['event_state']);
		}

		foreach ($events_meta as $key => $value) {
			$value = implode(',', (array)$value);
			update_post_meta($post_id, $key, $value);
			if (!$value) delete_post_meta($post_id, $key);
		}

		// persist last state/country like parent
		if (!empty($events_meta['simplecal_recurring_state'])) {
			update_option('simplecal_last_state', $events_meta['simplecal_recurring_state'], false);
		}
		if (!empty($events_meta['simplecal_recurring_country'])) {
			update_option('simplecal_last_country', $events_meta['simplecal_recurring_country'], false);
		}
	}
}

?>