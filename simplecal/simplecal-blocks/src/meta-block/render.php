<?php
global $post;
global $scplugin;
$meta_field = $attributes['metaField'] ?? null;
$link_type = $attributes['linkType'] ?? null;
$block_type = $attributes['blockType'] ?? 'details';

$meta_field_mapping = [
	'eventStartDate' => $scplugin::event_get_the_date('date','start'),
	'eventStartTime' => $scplugin::event_get_the_date('time','start'),
	'eventStartDateTime' => $scplugin::event_get_the_date('both','start'),
	'eventEndDate' => $scplugin::event_get_the_date('date','end'),
	'eventEndTime' => $scplugin::event_get_the_date('time','end'),
	'eventEndDateTime' => $scplugin::event_get_the_date('both','end'),
	'eventStartEndDateTime' => $scplugin::event_get_the_date('both','both'),
	'eventVenueName' => $post->simplecal_event_venue_name,
	'eventStreetAddress' => $post->simplecal_event_street_address,
	'eventCity' => $post->simplecal_event_city,
	'eventState' => $post->simplecal_event_state,
	'eventCountry' => $post->simplecal_event_country,
	'eventFullAddress' => $scplugin::event_get_the_location($link_type),
	'eventWebsite' => $scplugin::get_formatted_website($post->simplecal_event_website, $link_type),
	'eventVirtualPlatform' => $post->simplecal_event_virtual_platform,
	'eventMeetingLink' => $scplugin::get_formatted_website($post->simplecal_event_meeting_link, $link_type, $post->simplecal_event_virtual_platform)
];

switch ($block_type) {
	case 'value':
?>
		<span <?php echo get_block_wrapper_attributes(); ?>><?= $meta_field_mapping[$meta_field]; ?></span>
<?php
		return;
	case 'summary':
?>
		<div <?php echo get_block_wrapper_attributes(['class' => 'simplecal_meta_block_summary']); ?>>
			<h2>Coming soon</h2>
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
			if ($scplugin::event_get_the_location(null, true)) {
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