<?php
	global $post;

	$filter_tags = $attributes['tags'] ?? null;

	$label_opening_tag = '<div class="simplecal_next_event_label' . ($attributes['boldLabels'] ? ' simplecal_strong' : null) . ($attributes['showLabels'] ? null : ' simplecal_hide') . '">';
	$icon_opening_tag = '<div class="simplecal_next_event_icon' . ($attributes['showIcons'] ? null : ' simplecal_hide') . '">';

	$now = date_create();
	$now->setTimezone(wp_timezone());

	// Build the query parameters
	$args = [
		'post_type' => 'simplecal_event',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'orderby' => 'meta_value',
		'meta_key' => 'simplecal_event_start_timestamp',
		'meta_value' => $now->format('Y-m-d H:i:s'),
		'meta_type' => 'DATETIME',
		'meta_compare' => '>=',
		'order' => 'ASC'
	];

	if ($filter_tags) {
		$args['tag_slug__in'] = $filter_tags;
	}
		
	// Build the query for calendar views
	$next_event = new WP_Query($args);

	if ($next_event->have_posts() || !$attributes['hideOnNoEvent']) {
	?>
	<div <?= get_block_wrapper_attributes([
		'style' => 'border-radius:' . $attributes['cornerRadius'] . 'px;'
	]); ?>>
<?php
		if ($attributes['blockTitle']) {
?>
	<h2><?= $attributes['blockTitle']; ?></h2>
	<hr />
<?php
		}

		if (!$next_event->have_posts()) {
			echo '<div class="simplecal_center_text"><em>There are no upcoming events scheduled.</em></div>';
		} else {
			$next_event->the_post();

			if ($attributes['showEventTitle']) {
?>
	<h3><?= $attributes['linkEventTitle'] ? '<a href="' . get_the_permalink() . '">' : null; ?><?php the_title(); ?><?= $attributes['linkEventTitle'] ? '</a>' : null; ?></h3>
<?php
			}
?>
	<div class="simplecal_next_event_meta">
		<?= $icon_opening_tag; ?>
			<span class="material-symbols-outlined">event</span>
		</div>
		<?= $label_opening_tag; ?>
			Date:
		</div>
		<div class="simplecal_next_event_data">
			<?= SimpleCal::event_get_the_date("date","start"); ?>
		</div>
	</div>
	<div class="simplecal_next_event_meta">
		<?= $icon_opening_tag; ?>
			<span class="material-symbols-outlined">schedule</span>
		</div>
		<?= $label_opening_tag; ?>
			Time:
		</div>
		<div class="simplecal_next_event_data">
			<?= SimpleCal::event_get_the_date("time","start"); ?>
		</div>
	</div>
<?php
			if ($attributes['showLocation']) {
				if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {

					if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
?>
	<div class="simplecal_next_event_meta">
		<?= $icon_opening_tag; ?>
			<span class="material-symbols-outlined">pin_drop</span>
		</div>
		<?= $label_opening_tag; ?>
			Location:
		</div>
		<div class="simplecal_next_event_meta_data">
			<span class="simplecal_next_event_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_next_event_venue_separator">, </span><?php } ?><span class="simplecal_next_event_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_next_event_city_separator">, </span><?php } ?><span class="simplecal_next_event_state"><?= $post->simplecal_event_state; ?></span>
		</div>
	</div>
<?php
					}
					if ($post->simplecal_event_meeting_link) {
?>
					<div class="simplecal_next_event_meta">
						<?= $icon_opening_tag; ?>
							<span class="material-symbols-outlined">camera_video</span>
						</div>
						<?= $label_opening_tag; ?>
							Virtual:
						</div>
						<div class="simplecal_next_event_meta_data">
							<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
						</div>
					</div>
<?php
					}
				}
			}

			if ($attributes["showAllEventsLink"]) {
	?>
	<a class="simplecal_all_events_link" href="<?= get_post_type_archive_link('simplecal_event'); ?>">View All Events</a>
<?php
		}
	}	
?>
</div>
<?php
	} // Top-level "if no event" conditional
?>