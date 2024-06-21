<?php
if ($events->have_posts()) {
	global $post;
	$prev_event_month = $prev_event_year = '';

	while ($events->have_posts() ) {
		$events->the_post();
		$post_id = get_the_ID();

		if ($_POST['agendaShowMonthYearHeaders']) {
			if ($prev_event_year && $prev_event_year != date('Y', $post->simplecal_event_start_timestamp)) {
				echo "<div class='simplecal_list_year_header'>" . date('Y', $post->simplecal_event_start_timestamp) . "</div>";
			}
			$prev_event_year = date('Y', $post->simplecal_event_start_timestamp); // Since we want it to skip showing the year header for the first time, we set it regardless of whether it's changed, but we do it after the echo so that it doesn't factor into the first iteration

			if (!$prev_event_month || ("$prev_event_month $prev_event_year" != date('F Y', $post->simplecal_event_start_timestamp))) {
				$prev_event_month = date('F', $post->simplecal_event_start_timestamp); // Update the previous month marker
				echo "<div class='simplecal_list_month_header" . (strtotime("$prev_event_month 1, $prev_event_year") < strtotime("first day of this month midnight") ? ' simplecal_past_event' : '') . "'>$prev_event_month</div>";
			}
		}
?>
		<div class="simplecal_list_item <?= $post->simplecal_event_end_timestamp < time() ? 'simplecal_past_event':''?>">
<?php
		if ($_POST["agendaShowThumbnail"]) {
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
				<div class="simplecal_list_item_meta simplecal_list_item_datetime">
					<?= SimpleCal::event_get_the_date("datetime"); ?>
				</div>
				<div class="simplecal_list_item_meta simplecal_list_item_date">
					<?= SimpleCal::event_get_the_date("date"); ?>
				</div>
				<div class="simplecal_list_item_meta simplecal_list_item_time">
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
						<div class="simplecal_list_item_meta_icon">
							<span class="material-symbols-outlined">pin_drop</span>
							</div>
						<div class="simplecal_list_item_meta_data">
							<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list__item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
						</div>
					</div>
<?php
			}
			if ($post->simplecal_event_meeting_link) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_virtual">
						<div class="simplecal_list_item_meta_icon">
							<span class="material-symbols-outlined">camera_video</span>
						</div>
						<div class="simplecal_list_item_meta_data">
							<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
						</div>
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
						<div class="simplecal_list_item_meta_icon">
							<span class="material-symbols-outlined">open_in_new</span>
							</div>
						<div class="simplecal_list_item_meta_data">
							<?= SimpleCal::get_formatted_website($post->simplecal_event_website); ?>
						</div>
					</div>
<?php
		}
		if ($_POST["agendaShowExcerpt"]) {
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
}
?>