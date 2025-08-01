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
					<div class="simplecal_list_item_dates"><?= SimpleCal::event_get_the_date("date","start"); ?></div>
					<?php if ($dayOfWeekShow == 'true') { ?>
						 <div class="simplecal_list_item_dayofweek">(<?= SimpleCal::event_get_the_date(date_or_time:'date',start_or_end:'both',date_format:'l',nbsp_on_null:true); ?>)</div>
					<?php } ?>
					<?php if (!$post->simplecal_event_all_day) { ?>
						<div class="simplecal_list_item_time">
							at <?= SimpleCal::event_get_the_date("time","start"); ?>
						</div>
					<?php } ?>
				</div>
				<h4 class="simplecal_list_item_title">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h4>
<?php
		if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {

			if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_physical">
						<div class="simplecal_list_item_meta_data">
							<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list_item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
						</div>
					</div>
<?php
			}
			if ($post->simplecal_event_meeting_link) {
?>
					<div class="simplecal_list_item_meta simplecal_list_item_location_virtual">
						<div class="simplecal_list_item_meta_data">
							<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
						</div>
					</div>
<?php
			}
		}

		$postTags = get_the_tags();
		if ($tagsShow && $postTags == 'true') {
?>
			<div class="simplecal_list_item_meta simplecal_list_item_tags">
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