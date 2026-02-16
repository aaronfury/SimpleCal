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

		add_action('init', [$this, 'block_register']);
		add_action('init', [$this, 'block_template_register']);
		//add_action('widgets_init', [$this, 'widget_register']); // TODO: Add back in when the widget is ready
		
		if (is_admin()) {
			add_action('admin_enqueue_scripts', [$this,'enqueue_admin_scripts']);
			// TODO: Add CSS for admin panel
		} else {
			add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
		}

		add_action( 'rest_api_init', [$this, 'api_route_register']);

		if (!get_option('simplecal_slug')) {
			add_action('init', function() {
				update_option('simplecal_slug', Settings::$options_defaults['simplecal_slug']);;
			});
		}
	}

	function enqueue_admin_scripts($hook) {
		if (in_array($hook, ['post.php','post-new.php'])) {
			$screen = get_current_screen();

			if (is_object($screen) && 'simplecal_event' == $screen->post_type ){
				wp_enqueue_script('simplecal-admin-script', plugin_dir_url(__FILE__). '../../js/admin.js');
				wp_enqueue_style('simplecal-admin-script', plugin_dir_url(__FILE__). '../../css/admin.css');
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
		$agendaLayout = $request['agendaLayout'] ?? "list";
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