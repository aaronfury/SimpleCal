<?php
	wp_enqueue_script('simplecal_ajax', plugins_url('simplecal/js/ajax.js'), ['jquery','json2'],null,['defer',true]);
	wp_localize_script('simplecal_ajax', 'ajaxParams', ["url" => admin_url('admin-ajax.php')]);
?>
<div <?= get_block_wrapper_attributes(['data-hide-on-no-events' => $attributes['hideOnNoEvents']]); ?>>
	<div class="simplecal simplecal_<?= $attributes['blockTheme']; ?> <?= 'simplecal_' . $attributes['agendaLayout']; ?>" data-display-past-events="<?= empty($attributes['displayPastEvents']) ? 'false' : 'true'; ?>" data-display-past-events-days="<?= $attributes['displayPastEventsDays']; ?>" data-display-future-events-days="<?= $attributes['displayFutureEventsDays']; ?>" data-agenda-layout="<?= $attributes['agendaLayout']; ?>" data-agenda-show-month-year-headers="<?= empty($attributes['agendaShowMonthYearHeaders']) ? 'false' : 'true'; ?>" data-agenda-show-day-of-week="<?= empty($attributes['agendaShowDayOfWeek']) ? 'false' : 'true'; ?>" data-agenda-posts-per-page="<?= $attributes['agendaPostsPerPage']; ?>" data-agenda-display-pagination="<?= $attributes['agendaDisplayPagination']; ?>" data-show-all-events-link="<?= empty($attributes['agendaShowAllEventsLink']) ? 'false' : 'true'; ?>" data-agenda-show-thumbnail="<?= $attributes['agendaShowThumbnail']; ?>" data-agenda-show-excerpt="<?= empty($attributes['agendaShowExcerpt']) ? 'false' : 'true'; ?>" data-agenda-excerpt-lines="<?= $attributes['agendaExcerptLines']; ?>"> 
		<?php if ($attributes['title']) { ?><h2 class="simplecal_title"><?= $attributes['title']; ?></h2><?php } ?>
		<?php if (in_array($attributes['agendaDisplayPagination'], ['top','both'])) { ?>
		<div class="simplecal_nav_pagination">
			<div class="simplecal_nav_prev">
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_back</span>
				</div>
				<div>Previous Events</div>
			</div>
			<div class="simplecal_nav_next">
				<div>Future Events</div>
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_forward</span>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="simplecal_events_wrapper simplecal_agenda_<?= $attributes['agendaLayout']; ?>"></div>
		<?php if (in_array($attributes['agendaDisplayPagination'], ['bottom','both'])) { ?>
		<div class="simplecal_nav_pagination">
			<div class="simplecal_nav_prev">
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_back</span>
				</div>
				<div>Previous Events</div>
			</div>
			<div class="simplecal_nav_next">
				<div>Future Events</div>
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_forward</span>
				</div>
			</div>
		</div>
		<?php } ?>	
		<?php if ($attributes['agendaShowAllEventsLink']) { ?>
			<a class="simplecal_all_events_link" href="<?= get_post_type_archive_link('simplecal_event'); ?>"><?= $attributes['agendaShowAllEventsLinkText']; ?> </a>
		<?php } ?>

	</div>
</div>
