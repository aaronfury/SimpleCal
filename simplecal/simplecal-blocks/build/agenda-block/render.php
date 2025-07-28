<div <?= get_block_wrapper_attributes(['data-hide-on-no-events' => $attributes['hideOnNoEvents']]); ?>>
	<div
		class="simplecal simplecal_<?= $attributes['blockTheme']; ?> <?= 'simplecal_' . $attributes['agendaLayout']; ?>"
		<?= wp_interactivity_data_wp_context([
			"pastEventsShow" => $attributes['displayPastEvents'],
			"pastEventsDays" => $attributes['displayPastEventsDays'],
			"futureEventsDays" => $attributes['displayFutureEventsDays'],
			"agendaLayout" => $attributes['agendaLayout'],
			"monthYearHeadersShow" => $attributes['agendaShowMonthYearHeaders'],
			"dayOfWeekShow" => $attributes['agendaShowDayOfWeek'],
			"postsPerPage" => $attributes['agendaPostsPerPage'],
			"thumbnailShow" => $attributes['agendaShowThumbnail'],
			"excerptShow" => $attributes['agendaShowExcerpt'],
			"excerptLines" => $attributes['agendaExcerptLines'],
			"eventTags" => $attributes['eventTags']
		]); ?>
		data-wp-interactive="agendaBlock"
	>
		<?php if ($attributes['title']) { ?><h2 class="simplecal_title"><?= $attributes['title']; ?></h2><?php } ?>
		<?php if (in_array($attributes['agendaDisplayPagination'], ['top','both'])) { ?>
		<div class="simplecal_nav_pagination">
			<div class="simplecal_nav_prev" data-wp-on--click="actions.getEvents" data-wp-class--active="state.morePastEvents" data-direction="previous">
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_back</span>
				</div>
				<div>Previous Events</div>
			</div>
			<div class="simplecal_nav_next" data-wp-on--click="actions.getEvents" data-wp-class--active="state.moreFutureEvents" data-direction="future">
				<div>Future Events</div>
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_forward</span>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="simplecal_events_wrapper simplecal_agenda_<?= $attributes['agendaLayout']; ?>" data-wp-init="actions.getEvents" data-wp-watch="callbacks.updateAgenda">
		</div>
		<?php if (in_array($attributes['agendaDisplayPagination'], ['bottom','both'])) { ?>
		<div class="simplecal_nav_pagination">
			<div class="simplecal_nav_prev" data-wp-on--click="actions.getEvents" data-wp-class--active="state.morePastEvents" data-direction="previous">
				<div class="simplecal_nav_arrow">
					<span class="material-symbols-outlined">arrow_back</span>
				</div>
				<div>Previous Events</div>
			</div>
			<div class="simplecal_nav_next" data-wp-on--click="actions.getEvents" data-wp-class--active="state.moreFutureEvents" data-direction="future">
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
