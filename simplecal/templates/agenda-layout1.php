<?php
if ($events->have_posts()) {
	global $post;
	$prev_event_month = $prev_event_year = '';
	$current_time = time();

	if ($excerptShow && $excerptLines != '0') {
?>
		<style>
			.simplecal_list_item_excerpt {
				overflow: hidden;
				display: -webkit-box;
				-webkit-line-clamp: <?= $excerptLines; ?>;
				line-clamp: <?= $excerptLines; ?>;
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

		if (!$prev_event_year) {
			echo "<div class='simplecal_list_year_wrapper'>";
			if (!$prev_event_month) {
				echo "<div class='simplecal_list_month_wrapper'>";
				if ($monthYearHeadersShow == 'true') {
					echo "<div class='simplecal_list_month_header" . ($start_datetime->getTimestamp() < strtotime("first day of this month midnight") ? ' simplecal_past_event' : '') . "'>" . $start_datetime->format('F') . "</div>";
				}
			}
		} else {
			if ("$prev_event_month $prev_event_year" != $start_datetime->format('F Y')) { // If the previous event wasn't in the same month AND year
				echo "</div><!-- .simplecal_list_month_wrapper -->"; // We always want to close out the month

				if ($prev_event_year != $start_datetime->format('Y')) { // If the year doesn't match, let's close that and optionally display the header
					echo "</div><!-- .simplecal_list_year_wrapper -->\n<div class='simplecal_list_year_wrapper'>";
					if ($monthYearHeadersShow) {
						echo "<div class='simplecal_list_year_header'>" . $start_datetime->format('Y') . "</div>";
					}
				}
				echo "<div class='simplecal_list_month_wrapper'>"; // We always want to open up the month and optionally display the header
				if ($monthYearHeadersShow) {
					echo "<div class='simplecal_list_month_header" . ($start_datetime->getTimestamp() < strtotime("first day of this month midnight") ? ' simplecal_past_event' : '') . "'>" . $start_datetime->format('F') . "</div>";
				}
			}
		}
		$prev_event_year = $start_datetime->format('Y'); // Update the marker after it's been evaluated against the current post
		$prev_event_month = $start_datetime->format('F'); // Update the marker after it's been evaluated against the current post
?>
		<div class="simplecal_list_item <?= $post->simplecal_event_end_timestamp < $current_time ? 'simplecal_past_event':''?>">
<?php
		if ($thumbnailShow == 'true') {
?>
			<div class="simplecal_list_item_thumbnail">
				<img src="<?php the_post_thumbnail_url();?>" />
			</div>
<?php
		}
?>
			<div class="simplecal_list_item_content_wrapper">
				<div class="simplecal_list_item_meta simplecal_list_item_date">
					<div class="simplecal_list_item_dates"><?= SimpleCal::event_get_the_date("date","both"); ?></div>
					<?php if ($dayOfWeekShow == 'true') { ?>
						 <div class="simplecal_list_item_dayofweek">(<?= SimpleCal::event_get_the_date(date_or_time:'date',start_or_end:'both',date_format:'l',nbsp_on_null:true); ?>)</div>
					<?php } ?>
				</div>
				<h3 class="simplecal_list_item_title">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h3>
				<?php if (!$post->simplecal_event_all_day) { ?>
				<div class="simplecal_list_item_meta simplecal_list_item_time">
					<div class="simplecal_list_item_meta_icon">
						<span class="material-symbols-outlined">schedule</span>
					</div>
					<div class="simplecal_list_item_meta_data">
						<?= SimpleCal::event_get_the_date("time","both"); ?>
					</div>
				</div>
				<?php } ?>
				<div class="simplecal_list_item_meta simplecal_list_item_datetime">
					<div class="simplecal_event_meta">
						<span class="simplecal_event_meta_value">	
							<?= SimpleCal::event_get_the_date(); ?>
						</span>
					</div>
				</div>
<?php
		if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {

			if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_physical">
						<div class="simplecal_list_item_meta_icon">
							<span class="material-symbols-outlined">pin_drop</span>
						</div>
						<div class="simplecal_list_item_meta_data">
							<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list_item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
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
		}

		$postTags = get_the_tags();
		if ($tagsShow == 'true' && $postTags) {
?>
			<div class="simplecal_list_item_meta simplecal_list_item_tags">
				<div class="simplecal_list_item_meta_icon">
					<span class="material-symbols-outlined">tag</span>
				</div>
				<div class="simplecal_list_item_meta_data simplecal_tag_list">
					<?php
						foreach ($postTags as $tag) {
							echo "<div class='simplecal_tag'>{$tag->name}</div>";
						}
					?>
				</div>
			</div>
<?php
		}
		
		if ($excerptShow == 'true') {
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
?>
	<p class="simplecal_no_events">There are no events to display.</p>
<?php
}
?>