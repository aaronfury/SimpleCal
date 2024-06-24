<?php
	wp_enqueue_script('simplecal_ajax', plugins_url('simplecal/js/ajax.js'), ['jquery','json2'],null,['defer',true]);
	wp_localize_script('simplecal_ajax', 'ajaxParams', ["url" => admin_url('admin-ajax.php')]);
?>
<div <?= get_block_wrapper_attributes(['data-hide-on-no-events' => $attributes['hideOnNoEvents']]); ?>>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" /> <!-- TODO: Move this into the JS and make it conditional load? -->
	<div class="simplecal simplecal_<?= $attributes['blockTheme']; ?>" data-display-style="<?= $attributes['displayStyle']; ?>" data-display-past-events=<?= $attributes['displayPastEvents']; ?> data-display-past-events-days=<?= $attributes['displayPastEventsDays']; ?> data-display-future-events-days=<?= $attributes['displayFutureEventsDays']; ?> data-agenda-show-month-year-headers=<?= $attributes['agendaShowMonthYearHeaders']; ?> data-agenda-posts-per-page=<?= $attributes['agendaPostsPerPage']; ?> data-agenda-show-thumbnail=<?= $attributes['agendaShowThumbnail']; ?> data-agenda-show-excerpt=<?= $attributes['agendaShowExcerpt']; ?>>
		<?php if ($attributes['title']) { ?><h2 class="simplecal_title"><?= $attributes['title']; ?></h2><?php } ?>
		<div class="simplecal_nav_prev">
			<div class="simplecal_nav_arrow">
				<span class="material-symbols-outlined">arrow_back</span>
			</div>
			<div>Previous Events</div>
		</div>
		<div class="simplecal_events_wrapper"></div>
		<div class="simplecal_nav_next">
			<div>Future Events</div>
			<div class="simplecal_nav_arrow">
				<span class="material-symbols-outlined">arrow_forward</span>
			</div>
		</div>
	</div>
</div>
