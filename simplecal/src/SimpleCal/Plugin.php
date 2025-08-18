<?php
namespace SimpleCal;

class Plugin {
	public static $tz;
	public static $path = WP_PLUGIN_DIR . '/simplecal';
	public static $url = WP_PLUGIN_URL . '/simplecal';

	private $tz_string;
	protected $contents;
	
	public function __construct() {
		global $pagenow;

		self::$tz = wp_timezone();
		$this->tz_string = wp_timezone_string();

		add_action('init', [$this, 'cpt_register']);
		add_action('init', [$this, 'block_register']);
		add_action('init', [$this, 'block_template_register']);
		//add_action('widgets_init', [$this, 'widget_register']); // TODO: Add back in when the widget is ready
		
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
			add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
			add_filter('single_template', [$this,'cpt_register_templates']);
			add_filter('archive_template', [$this,'cpt_register_templates']);
		}

		add_action( 'rest_api_init', [$this, 'api_route_register']);
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
		$post_start = new \DateTime($meta['simplecal_event_start_timestamp'][0]);
		$post_end = new \DateTime($meta['simplecal_event_end_timestamp'][0]);

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
				'thumbnail',
				'custom-fields'
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
			'taxonomies' => ['post_tag'],
			'has_archive' => 'events',
			'show_in_rest' => true
		]
		);

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
		];

		foreach ($meta as $field => $type) {
			register_post_meta('simplecal_event', $field, [
				'type' => $type,
				'show_in_rest' => true
			]);
		}

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
			return $this->path . '/templates/legacy-archive-simplecal_event.php';
		}
		if ('simplecal_event' === $post->post_type) {
			return $this->path . '/templates/legacy-single-simplecal_event.php';
		}
		return $template;
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
				$timestamp = new \DateTime($post->{"simplecal_event_{$key}_timestamp"}); // Pull the meta in Unix timestamp format and set it as a DateTime object
			} else {
				$timestamp = new \DateTime('now', self::$tz); // Pull the meta in Unix timestamp format and set it as a DateTime object
				$timestamp->setTime($timestamp->format('H'),0,0,0); // Reset the hours and seconds on the timestamp to 00
			}
			
			$datetime_string = $alldayevent ? $timestamp->format('Y-m-d') : $timestamp->format('Y-m-d\TH:i');
		
			echo '<h3>' . ucfirst($key) . ' Date and Time</h3>';
			echo '<input type="' . ($alldayevent ? 'date' : 'datetime-local') . '" name="event_' . $key . '_datetime" id="simplecal_event_' . $key . '_datetime" value="' . $datetime_string . '" ' . ($key == 'start' ? 'required ' : '') . '/>';
		}

		$post_timezone = $timestamp->getTimezone(); // Just use the last $timestamp object, presumably from the end_timestamp

		//$timezones = DateTimeZone::listIdentifiers();
		$timezone_list = Helper::timezone_list();
		
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
			$timestamp = new \DateTime($_POST["event_{$key}_datetime"], ($_POST['event_timezone'] ? new \DateTimeZone($_POST['event_timezone']) : self::$tz));
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

		// TODO: Need a better way to determine whether to save the physical location meta if ONLY the state is set, since it is auto-populated from the simplecal_last_state WP Option, it always has a value, even for online-only events. For now, we will require that another physical location has a value before we save the state.
		if (!empty($_POST['event_venue_name'].$_POST['event_street_address'].$_POST['event_city'])) {
			$events_meta['simplecal_event_state'] = ($events_meta['simplecal_event_country'] == 'United States' ? $_POST['event_state_us'] : $_POST['event_state']);
		}

		foreach ($events_meta as $key => $value) {
			$value = implode(',', (array)$value);
			update_post_meta($post_id, $key, $value);
			if (!$value) delete_post_meta($post_id, $key);
		}

		// Save the selected state to prepopulate it on the next event.
		// TODO: Implement this for other fields? Or like a favorites list?
		if ($events_meta['simplecal_event_state']) {
			update_option('simplecal_last_state', $events_meta['simplecal_event_state'], false);
		}

		if ($events_meta['simplecal_event_country']) {
			update_option('simplecal_last_country', $events_meta['simplecal_event_country'], false);
		}
	}
	
	function enqueue_admin_scripts($hook) {
		if (in_array($hook, ['post.php','post-new.php'])) {
			$screen = get_current_screen();

			if (is_object($screen) && 'simplecal_event' == $screen->post_type ){
				wp_enqueue_script('simplecal-admin-script', self::$url . '/js/admin.js');
				wp_enqueue_style('simplecal-admin-script', self::$url . '/css/admin.css');
			}
		}
		return;
	}

	function enqueue_scripts($hook) {
		wp_enqueue_style('material-symbols', '//fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@36,400,1,0&display=block'); // TODO: Make this conditional based on page?

		wp_enqueue_script('wp-api-fetch'); // TODO: This is a workaround for the limitation that script modules cannot import scripts.
	}

	//// REGISTER WP BLOCK AND WIDGET ////
	function widget_register() {
		//register_widget('SimpleCal_Widget'); // TODO: Add this back when the widget's ready
	}
	
	function block_register() {
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			\wp_register_block_types_from_metadata_collection( self::$path . '/simplecal-blocks/build', self::$path . '/simplecal-blocks/build/blocks-manifest.php' );
			return;
		}
	
		if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
			\wp_register_block_metadata_collection( self::$path . '/simplecal-blocks/build', self::$path . '/simplecal-blocks/build/blocks-manifest.php' );
		}
	
		$manifest_data = require self::$path . '/simplecal-blocks/build/blocks-manifest.php';
		foreach ( array_keys( $manifest_data ) as $block_type ) {
			register_block_type( self::$path . "/simplecal-blocks/build/{$block_type}" );
		}
	}

	function block_template_register() {
		\register_block_template(
			'simplecal//archive-simplecal_event',
			[
				'title' => 'SimpleCal Archive',
				'description' => 'An agenda-style view of SimpleCal events',
				'content' => self::get_template_content('archive-simplecal_event.html')
			]
		);
		\register_block_template(
			'simplecal//single-simplecal_event',
			[
				'title' => 'SimpleCal Event',
				'description' => 'The layout for a single event',
				'content' => self::get_template_content('single-simplecal_event.html')
			]
		);
	}

	function get_template_content($template) {
		ob_start();
		include __DIR__ . "/../../templates/{$template}";
		return ob_get_clean();
	}

	// WordPress API Interface
	function api_route_register() {
		register_rest_route(
			'simplecal/v1',
			'/events/agenda',
			[
				'methods' => 'GET',
				'callback' => [$this,'api_route_agenda_get'],
				'permission_callback' => '__return_true'
			]
		);
	}

	function api_route_agenda_get(\WP_REST_Request $request) {
		// Determine the desired page
		$page = $request['page'] ?? 0;
		$posts_per_page = $request['per_page'] ?? 10;
		$page_param = ($page < 0 ? -$page : $page + 1);

		// Parse the additional query parameters... TODO: Is this necessary? Why not just parse request directly?
		$agendaLayout = $request['agendaLayout'] ?? "layout1";
		$pastEventsShow = $request['pastEventsShows'] ?? true;
		$pastEventsDays = $request['pastEventsDays'] ?? 0;
		$eventTags = array_filter(explode(',', $request['eventTags']));
		$tagsShow = $request['tagsShow'] ?? false;
		$excerptShow = $request['excerptShow'] ?? false;
		$excerptLines = $request['excerptLines'] ?? false;
		$monthYearHeadersShow = $request['monthYearHeadersShow'] ?? false;
		$dayOfWeekShow = $request['dayOfWeekShow'] ?? false;
		$thumbnailShow = $request['thumbnailShow'] ?? false;

		// Build the query parameters
		$args = [
			'post_type' => 'simplecal_event',
			'post_status' => 'publish',
			'orderby' => 'meta_value',
			'meta_key' => 'simplecal_event_start_timestamp',
			'meta_type' => 'DATETIME'
		];

		if ($eventTags) {
			$args['tag_slug__in'] = $eventTags;
		}

		// Build the query for agenda views
		$args += [
			'posts_per_page' => $posts_per_page,
			'paged' => $page_param,
		];
		
		if ($page >= 0) {
			$args += [
				'meta_value' => date('Y-m-d H:i:s'),
				'meta_compare' => '>=',
				'order' => 'ASC'
			];
		} else {
			$args += [
				'order' => 'DESC'
			];

			if ($pastEventsShow) {
				if ($pastEventsDays == 0) {
					$args += [
						'meta_value' => date('Y-m-d H:i:s'),
						'meta_compare' => '<='
					];
				} else {
					$past_event_cutoff = new \DateTime("-{$pastEventsDays} days");
					$args += [
						'meta_value' => [$past_event_cutoff->format('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
						'meta_compare' => 'BETWEEN'
					];
				}
			}
		}

		// Build the query for calendar views
		$events = new \WP_Query($args);

		if ($page < 0 && $events->have_posts()) { // Since "previous events" are searched in descending order from the current date, we flip the array of posts to have them show up in chronological order
			$events->posts = array_reverse($events->posts);
		}

		// TODO: Ideally, we'd want to return objects for each post (rather than rendered HTML), but WordPress' Interactivity API is still too immature to handle it well (lack of conditionals, unable to insert innerHTML, etc.)
		ob_start();
		include_once(self::$path . '/templates/agenda-' . $agendaLayout . '.php');
		$output = ob_get_clean();
		wp_reset_postdata();

		$more_prev = ((($page < 0) && ($events->max_num_pages > $page_param)) || $page >= 0);
		$more_next = ((($page >= 0) && ($events->max_num_pages > $page_param)) || $page < 0);

		return [
			"output" => $output,
			"morePrevious" => $more_prev,
			"moreFuture" => $more_next
		];
	}
}
?>