<?php
namespace SimpleCal;

global $post;
$meta_field = $attributes['metaField'] ?? '';
$meta_date_format = $attributes['metaDateFormat'] ?? 'shortDateAndTime';
$meta_date_custom_format = $attributes['metaDateCustomFormat'] ?? '';
$meta_time_custom_format = $attributes['metaTimeCustomFormat'] ?? '';
$link_type = $attributes['linkType'] ?? 'none';
$block_type = $attributes['blockType'] ?? '';

$date_format_mapping = [
	'dayOfWeek' => ['date_or_time' => 'date', 'date_format' => 'l', 'time_format' => ''],
	'shortDate' => ['date_or_time' => 'date', 'date_format' => 'n/j/y', 'time_format' => ''],
	'longDate' => ['date_or_time' => 'date', 'date_format' => 'F j, Y', 'time_format' => ''],
	'time' => ['date_or_time' => 'time', 'date_format' => '', 'time_format' => 'g:i a'],
	'dayOfWeekAndTime' => ['date_or_time' => 'both', 'date_format' => 'l', 'time_format' => 'g:i a'],
	'shortDateAndTime' => ['date_or_time' => 'both',  'date_format' => 'n/j/y', 'time_format' => 'g:i a'],
	'longDateAndTime' => ['date_or_time' => 'both', 'date_format' => 'F j, Y', 'time_format' => 'g:i a'],
	'custom' => ['date_or_time' => ($meta_date_custom_format && $meta_time_custom_format ? 'both' : ($meta_date_custom_format ? 'date' : 'time')),'date_format' => $meta_date_custom_format, 'time_format' => $meta_time_custom_format]
];

$date_format = $date_format_mapping[$meta_date_format];

// If we're only pulling a single value, no sense in evaluating all of them... just get the one we need
if ($block_type === 'value') {
	switch ($meta_field) {
		case 'eventStartDateTime':
			$value = Helper::event_get_the_date($date_format['date_or_time'],'start',$date_format['date_format'], $date_format['time_format']);
			break;
		case 'eventEndDateTime':
			$value = Helper::event_get_the_date($date_format['date_or_time'],'end',$date_format['date_format'], $date_format['time_format']);
			break;
		case 'eventStartEndDateTime':
			$value = Helper::event_get_the_date($date_format['date_or_time'],'both',$date_format['date_format'], $date_format['time_format']);
			break;
		case 'eventVenueName':
			$value = $post->simplecal_event_venue_name;
			break;
		case 'eventStreetAddress':
			$value = $post->simplecal_event_street_address;
			break;
		case 'eventCity':
			$value = $post->simplecal_event_city;
			break;
		case 'eventState':
			$value = $post->simplecal_event_state;
			break;
		case 'eventCountry':
			$value = $post->simplecal_event_country;
			break;
		case 'eventFullAddressWithVenue':
			$value = Helper::event_get_the_location($link_type);
			break;
		case 'eventFullAddress':
			$value = Helper::event_get_the_location(link_type: $link_type, include_venue: false);
			break;
		case 'eventWebsite':
			$value = Helper::get_formatted_website($post->simplecal_event_website, $link_type);
			break;
		case 'eventVirtualPlatform':
			$value = $post->simplecal_event_virtual_platform;
			break;
		case 'eventMeetingLink':
			$value = Helper::get_formatted_website($post->simplecal_event_meeting_link, $link_type, $post->simplecal_event_virtual_platform);
			break;

	}
} else {
	$meta_field_mapping = [
		'eventStartDateTime' => Helper::event_get_the_date($date_format['date_or_time'],'start',$date_format['date_format'], $date_format['time_format']),
		'eventEndDateTime' => Helper::event_get_the_date($date_format['date_or_time'],'end',$date_format['date_format'], $date_format['time_format']),
		'eventStartEndDateTime' => Helper::event_get_the_date($date_format['date_or_time'],'both',$date_format['date_format'], $date_format['time_format']),
		'eventVenueName' => $post->simplecal_event_venue_name,
		'eventStreetAddress' => $post->simplecal_event_street_address,
		'eventCity' => $post->simplecal_event_city,
		'eventState' => $post->simplecal_event_state,
		'eventCountry' => $post->simplecal_event_country,
		'eventFullAddress' => Helper::event_get_the_location($link_type),
		'eventWebsite' => Helper::get_formatted_website($post->simplecal_event_website, $link_type),
		'eventVirtualPlatform' => $post->simplecal_event_virtual_platform,
		'eventMeetingLink' => Helper::get_formatted_website($post->simplecal_event_meeting_link, $link_type, $post->simplecal_event_virtual_platform)
	];
}

switch ($block_type) {
	case 'value':
?>
		<span <?php echo get_block_wrapper_attributes(); ?>>
			<?php echo $value; ?>
		</span>
<?php
		return;
	case 'summary':
?>
		<div <?php echo get_block_wrapper_attributes(['class' => 'simplecal_meta_block_summary']); ?>>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon" title="Event Date/Time">
					<span class="material-symbols-outlined">calendar_month</span>
				</div>
				<h3>
					<span><?php echo $meta_field_mapping['eventStartEndDateTime']; ?></span>
				</h3>
			</div>
<?php
		if (!$post->simplecal_event_private_location || is_user_logged_in()) {
			if (Helper::event_get_the_location(null, true)) {
?>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon">
					<span class="material-symbols-outlined">pin_drop</span>
				</div>
				<h3>
					<span><?php echo $meta_field_mapping['eventFullAddress']; ?></span>
				</h3>
			</div>
<?php
			}
		}
?>
		</div>
<?php
		return;
	case 'details':
?>
		<div <?php echo get_block_wrapper_attributes(['class' => 'simplecal_meta_block_details']); ?>>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon" title="Start Date/Time">
					<span class="material-symbols-outlined">play_circle</span>
				</div>
				<h2>
					<span><?php echo $meta_field_mapping['eventStartDateTime']; ?></span>
				</h2>
			</div>

<?php if ($post->simplecal_event_end_timestamp) { ?>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon" title="End Date/Time">
					<span class="material-symbols-outlined">stop_circle</span>
				</div>
				<h2>
					<span><?php echo $meta_field_mapping['eventEndDateTime']; ?></span>
				</h2>
			</div>
<?php }

		if (!$post->simplecal_event_private_location || is_user_logged_in()) {
			if (Helper::event_get_the_location(null, true)) {
?>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon">
					<span class="material-symbols-outlined">pin_drop</span>
				</div>
				<h3>
					<span><?php echo $meta_field_mapping['eventFullAddress']; ?></span>
				</h3>
			</div>
<?php
			}

			if ($post->simplecal_event_meeting_link) {
?>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon">
					<span class="material-symbols-outlined">camera_video</span>
				</div>
				<h3>
					<span><?php echo $meta_field_mapping['eventMeetingLink']; ?></span>
				</h3>
			</div>
<?php
			}
		}

		if ($post->simplecal_event_website) {
?>
			<div class="simplecal_meta_block_row">
				<div class="simplecal_meta_block_icon">
					<span class="material-symbols-outlined">link</span>
				</div>
				<h3>
					<span><?php echo $meta_field_mapping['eventWebsite']; ?></span>
				</h3>
			</div>
		</div><!-- block wrapper -->
<?php
		}
?>
		<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"/>
<?php
	}
?>