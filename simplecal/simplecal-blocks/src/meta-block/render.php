<?php
global $post;
$meta_field = $attributes['metaField'];
$link_type = $attributes['linkType'] ?? null;

$meta_field_mapping = [
	'eventStartDate' => SimpleCal::event_get_the_date('date','start'),
	'eventStartTime' => SimpleCal::event_get_the_date('time','start'),
	'eventStartDateTime' => SimpleCal::event_get_the_date('both','start'),
	'eventEndDate' => SimpleCal::event_get_the_date('date','end'),
	'eventEndTime' => SimpleCal::event_get_the_date('time','end'),
	'eventEndDateTime' => SimpleCal::event_get_the_date('both','end'),
	'eventStartEndDateTime' => SimpleCal::event_get_the_date('both','both'),
	'eventVenueName' => $post->simplecal_event_venue_name,
	'eventStreetAddress' => $post->simplecal_event_street_address,
	'eventCity' => $post->simplecal_event_city,
	'eventState' => $post->simplecal_event_state,
	'eventCountry' => $post->simplecal_event_country,
	'eventFullAddress' => SimpleCal::event_get_the_location($link_type),
	'eventVirtualPlatform' => $post->simplecal_event_virtual_platform,
	'eventMeetingLink' => $post->simplecal_event_meeting_link,
	'eventWebsite' => SimpleCal::get_formatted_website($post->simplecal_event_website, null)
]

?>
<span <?php echo get_block_wrapper_attributes(); ?>><?= $meta_field_mapping[$meta_field]; ?></span>