<?php
if ($events->have_posts()) {
	global $post;
	$prev_event_month = $prev_event_year = '';
	$current_time = time();
	
	if ($_POST['agendaShowExcerpt'] && $_POST['agendaExcerptLines'] != '0') {
?>
		<style>
			.simplecal_list_item_excerpt {
				overflow: hidden;
				display: -webkit-box;
				-webkit-line-clamp: <?= $_POST['agendaExcerptLines']; ?>;
				line-clamp: <?= $_POST['agendaExcerptLines']; ?>;
				-webkit-box-orient: vertical;
			}
		</style>
<?php
	}

	while ($events->have_posts() ) {
		$events->the_post();
		$post_id = get_the_ID();
		
		$post_timezone = $post->simplecal_event_timezone ? new DateTimeZone($post->simplecal_event_timezone) : SimpleCal::$tz;
		$start_datetime = new DateTime($post->simplecal_event_start_timestamp, $post_timezone);
		$end_datetime = new DateTime($post->simplecal_event_end_timestamp, $post_timezone);
?>
		<div class="simplecal_list_item <?= $post->simplecal_event_end_timestamp < $current_time ? 'simplecal_past_event':''?>">
			<div class="simplecal_list_item_content_wrapper">
				<div class="simplecal_list_item_container simplecal_list_item_datetime">
					<div class="simplecal_list_item_meta simplecal_list_item_date">
						<div class="simplecal_list_item_dates"><?= SimpleCal::event_get_the_date(date_or_time:'date',start_or_end:'both',date_format: 'n/d/y', nbsp_on_null: true); ?></div>
						<?php if ($_POST['agendaShowDayOfWeek'] == 'true') { ?>
						 <div class="simplecal_list_item_dayofweek"><?= SimpleCal::event_get_the_date(date_or_time:'date',start_or_end:'both',date_format:'l',nbsp_on_null:true); ?></div>
						<?php } ?>
					</div>
					<div class="simplecal_list_item_meta simplecal_list_item_time">
						<?= SimpleCal::event_get_the_date(date_or_time:'time',start_or_end:'both',nbsp_on_null:true) ?>
					</div>
				</div>
				<div class="simplecal_list_item_container">
					<div class="simplecal_list_item_title">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</div>
					<?php if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) { ?>
					<div class="simplecal_list_item_location">
						<?php if ($post->simplecal_event_venue_name || $post->simplecal_event_city) { ?>
						<div class="simplecal_list_item_meta simplecal_list_item_location_physical">
							<div class="simplecal_list_item_meta_icon">
								<span class="material-symbols-outlined">pin_drop</span>
								</div>
							<div class="simplecal_list_item_meta_data">
								<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list__item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
							</div>
						</div>
						<?php } ?>
						<?php if ($post->simplecal_event_meeting_link) { ?>
						<div class="simplecal_list_item_meta simplecal_list_item_location_virtual">
							<div class="simplecal_list_item_meta_icon">
								<span class="material-symbols-outlined">camera_video</span>
							</div>
							<div class="simplecal_list_item_meta_data">
								<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<?php if ($post->simplecal_event_website) { ?>
						<div class="simplecal_list_item_meta simplecal_list_item_website">
							<div class="simplecal_list_item_meta_icon">
								<span class="material-symbols-outlined">open_in_new</span>
								</div>
							<div class="simplecal_list_item_meta_data">
								<?= SimpleCal::get_formatted_website($post->simplecal_event_website); ?>
							</div>
						</div>
					<?php } ?>		
					<?php if ($_POST["agendaShowExcerpt"] == 'true') { ?>
					<div class="simplecal_list_item_excerpt">
						<?php
							echo wp_trim_words(get_the_excerpt(), 80);
						?>
					</div>
					<?php } ?>
				</div><!-- .simplecal_list_item_container -->
			</div><!-- .simplecal_list_item_content_wrapper -->
			<?php if ($_POST["agendaShowThumbnail"] == 'true') { ?>
			<div class="simplecal_list_item_thumbnail">
				<img src="<?php the_post_thumbnail_url();?>" />
			</div>
			<?php } ?>
		</div><!-- .simplecal_list_item -->
<?php
	}
?>
		</div><!-- .simplecal_block -->
<?php
} else {
?>
	<p class="simplecal_no_events">There are no events to display.</p>
<?php
}
?>