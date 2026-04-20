<?php

namespace SimpleCal;

class Block {
	public function __construct() {

		add_action('init', [$this, 'block_register']);
		add_action('init', [$this, 'block_template_register']);

		add_action('enqueue_block_editor_assets', [$this, 'enqueue_event_query_variation_script']);
		add_filter('pre_render_block', [$this, 'set_query_for_events_query_block'], 10, 2);
		add_filter( 'wp_theme_json_data_default', [$this, 'add_block_theme_json_defaults'], 10, 1 );
	}

	function enqueue_event_query_variation_script() {
		$asset_file = Plugin::$path . '/simplecal-blocks/build/query-block-variation/index.asset.php';
		
		if (!file_exists($asset_file)) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'simplecal-query-variation-settings',
			Plugin::$url . '/simplecal-blocks/build/query-block-variation/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
		//wp_enqueue_script('simplecal-register-query-block-variation', plugin_dir_url(__FILE__). '../../js/registerQueryBlockVariation.js', ['wp-blocks']);
	}

	function set_query_for_events_query_block($pre_render, $parsed_block) {
		if ($parsed_block['blockName'] !== 'core/query' || $parsed_block['attrs']['namespace'] !== 'simplecal/event-query-loop') {
			return $pre_render;
		}

		$query_attrs = $parsed_block['attrs']['query'] ?? [];
		$per_page = isset( $query_attrs['perPage'] ) ? (int) $query_attrs['perPage'] : 5;
		$order = ( isset( $query_attrs['order'] ) && 'DESC' === strtoupper( $query_attrs['order'] ) ) ? 'DESC' : 'ASC';
		$upcoming_only = ! empty( $query_attrs['hidePastEvents'] );

		add_filter('query_loop_block_query_vars', function($query_vars) use ($per_page, $order, $upcoming_only) {
			$query_vars['post_type'] = 'simplecal_event';
			$query_vars['posts_per_page'] = $per_page;
			$query_vars['orderby'] = 'meta_value';
			$query_vars['meta_key'] = 'simplecal_event_start_timestamp';
			$query_vars['meta_type'] = 'DATETIME';
			$query_vars['order'] = $order;

			if ($upcoming_only) {
				$query_vars['meta_value'] = date('Y-m-d H:i:s');
				$query_vars['meta_compare'] = '>=';
			}

			return $query_vars;
		});
		
		return $pre_render;
	}

	function add_block_theme_json_defaults($json) {
		$new_data = [
			'version'  => 2,
			'settings' => [
				'blocks' => [
					'simplecal/event-meta-text' => [
						'typography' => [
							'fontStyle'      => true,
							'fontWeight'     => true,
							'letterSpacing'  => true,
							'lineHeight'     => true,
							'textDecoration' => true,
							'textTransform'  => true,
							'writingMode'    => true,
							'customFontSize' => true,
						],
					],
				],
			],
		];

		return $json->update_with( $new_data );
	}

	function get_template_content($template) {
		ob_start();
		include __DIR__ . "/../../templates/{$template}";
		return ob_get_clean();
	}

	//// REGISTER WP BLOCK ////
	function block_register() {
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			\wp_register_block_types_from_metadata_collection( Plugin::$path . '/simplecal-blocks/build', Plugin::$path . '/simplecal-blocks/build/blocks-manifest.php' );
			return;
		}
	
		if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
			\wp_register_block_metadata_collection( Plugin::$path . '/simplecal-blocks/build', Plugin::$path . '/simplecal-blocks/build/blocks-manifest.php' );
		}
	
		$manifest_data = require Plugin::$path . '/simplecal-blocks/build/blocks-manifest.php';
		foreach ( array_keys( $manifest_data ) as $block_type ) {
			register_block_type( Plugin::$path . "/simplecal-blocks/build/{$block_type}" );
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

}
?>