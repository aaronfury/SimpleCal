<div <?= get_block_wrapper_attributes(); ?>>
	<?php
		// START Events Loop
		$args = [
			'post_type' => 'simplecal_event',
			'posts_per_page' => 4, // TODO: Add pagination
			'meta_key' => 'simplecal_event_start_timestamp',
			'orderby' => 'meta_value_num',
			'order' => 'ASC'
		];

		if ($attributes['displayPastEvents'] && $attributes['displayPastEventsDays']) {
			$past_event_cutoff = new DateTime("-{$attributes['displayPastEventsDays']}");
			$args['meta_query'] = [
				'key' => 'simplecal_event_end_timestamp',
				'value' => $past_event_cutoff->format('U'),
				'compare' => '>='
			];
		} else {
			$args['meta_query'] = [
				'key' => 'simplecal_event_end_timestamp',
				'value' => date('U'),
				'compare' => '>='
			];
		}
		$events = new WP_Query( $args );
		if ($events->have_posts() || !$attributes["hideOnNoEvents"]) {
?>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
	<div class="simplecal_<?= $attributes['blockTheme'] ?>">
		<?php if ($attributes['title']) { ?><h2 class="simplecal_title"><?= $attributes['title']; ?></h2><?php } ?>
<?php
			if ($events->have_posts()) {
				global $post;
				$prev_event_month = $prev_event_year = '';

				while ($events->have_posts() ) {
					$events->the_post();
					$post_id = get_the_ID();

					if ($attributes['agendaShowMonthYearHeaders']) {
						if ($prev_event_year && $prev_event_year != date('Y', $post->simplecal_event_start_timestamp)) {
							echo "<div class='simplecal_list_year_header'>" . date('Y', $post->simplecal_event_start_timestamp) . "</div>";
						}
						$prev_event_year = date('Y', $post->simplecal_event_start_timestamp); // Since we want it to skip showing the year header for the first time, we set it regardless of whether it's changed, but we do it after the echo so that it doesn't factor into the first iteration

						if (!$prev_event_month || ("$prev_event_month $prev_event_year" != date('F Y', $post->simplecal_event_start_timestamp))) {
							$prev_event_month = date('F', $post->simplecal_event_start_timestamp); // Update the previous month marker
							echo "<div class='simplecal_list_month_header'>$prev_event_month</div>";
						}
					}
?>
		<div class="simplecal_list_item">
<?php
					if ($attributes["agendaShowThumbnail"]) {
?>
			<div class="simplecal_list_item_thumbnail">
				<img src="<?php the_post_thumbnail_url();?>" />
			</div>
<?php
					}
?>
			<div class="simplecal_list_item_content_wrapper">
				<div class="simplecal_list_item_title">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</div>
				<div class="simplecal_list_item_datetime">
					<?= SimpleCal::event_get_the_date("datetime"); ?>
				</div>
				<div class="simplecal_list_item_date">
					<?= SimpleCal::event_get_the_date("date"); ?>
				</div>
				<div class="simplecal_list_item_time">
					<?= SimpleCal::event_get_the_date("time"); ?>
				</div>
<?php
					if (!$post->simplecal_event_private_location || (($post->simpelcal_event_private_location) && is_user_logged_in())) {
?>
				<div class="simplecal_list_item_location">
<?php
						if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_physical">
						<span class="material-symbols-outlined">pin_drop</span>
						<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list__item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
					</div>
<?php
						}
						if ($post->simplecal_event_meeting_link) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_virtual">
						<span class="material-symbols-outlined">camera_video</span>
						<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
					</div>
<?php
						}
?>
				</div>
<?php
					}
					if ($post->simplecal_event_website) {
?>
				<div class="simplecal_list_item_meta simplecal_list_item_website">
					<span class="material-symbols-outlined">open_in_new</span>
					<?= SimpleCal::get_formatted_website($post->simplecal_event_website); ?>
				</div>
<?php
					}
					if ($attributes["agendaShowExcerpt"]) {
?>
				<div class="simplecal_list_item_excerpt">
					<?php the_excerpt(); ?>
				</div>
<?php
					}
?>
			</div><!-- .simplecal_list_item_content_wrapper -->
		</div><!-- .simplecal_list_item -->
<?php
				}
?>
	</div><!-- .simplecal_block -->
<?php
			} else {
				echo '<p class="simplecal_no_events">' . $attributes["noEventsText"] . '</p>';
			}
		}
		wp_reset_postdata();
?>
</div>
