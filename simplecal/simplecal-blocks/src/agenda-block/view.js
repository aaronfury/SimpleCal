jQuery(document).ready(() => {	
	jQuery('.simplecal').each((i, instance) => {
		jQuery(instance).data('page',0);

		var formData = scGetFormData(instance);
		
		if (formData.displayPastEvents && formData.page == 0) {
			jQuery(instance).find('.simplecal_nav_prev').addClass('active');
		}

		scGetEvents(formData).done((response) => {
			jQuery(instance).children('.simplecal_events_wrapper').html(response.data.output);
			if (response.data.more_next_pages) {
				jQuery(instance).find('.simplecal_nav_next').addClass('active');
			} else {
				jQuery(instance).find('.simplecal_nav_next').removeClass('active');
			}
		});

		jQuery(instance).on('click', '.simplecal_nav_prev.active, .simplecal_nav_next.active', (event) => {
			var instance = jQuery(event.currentTarget).parents('.simplecal');
	
			if (jQuery(event.currentTarget).hasClass('simplecal_nav_prev')) {
				jQuery(instance).data().page--;
				jQuery(instance).find('.simplecal_nav_prev').removeClass('active');
			} else {
				jQuery(instance).data().page++;
				jQuery(instance).find('.simplecal_nav_next').removeClass('active');
			}
			
			var formData = scGetFormData(jQuery(instance));

			scGetEvents(formData).done((response) => {
				jQuery(instance).children('.simplecal_events_wrapper').fadeOut(function() {
					jQuery(this).html(response.data.output);
				}).fadeIn();

				if (response.data.more_next_pages) {
					jQuery(instance).find('.simplecal_nav_next').addClass('active');
				} else {
					jQuery(instance).find('.simplecal_nav_next').removeClass('active');
				}
				if (response.data.more_prev_pages || (formData['displayPastEvents'] && formData['page'] == 0)) {
					jQuery(instance).find('.simplecal_nav_prev').addClass('active');
				} else {
					jQuery(instance).find('.simplecal_nav_prev').removeClass('active');
				}
			});
		});
	});

});

function scGetFormData(instance) {
	var formData = {
		'action' : 'simplecal_get_agenda_events',
		'tags': jQuery(instance).data('tags'),
		'displayPastEvents' : jQuery(instance).data('displayPastEvents'),
		'displayPastEventsDays' : jQuery(instance).data('displayPastEventsDays'),
		'displayFutureEventsDays' : jQuery(instance).data('displayFutureEventsDays'),
		'agendaLayout' : jQuery(instance).data('agendaLayout'),
		'agendaShowMonthYearHeaders' : jQuery(instance).data('agendaShowMonthYearHeaders'),
		'agendaPostsPerPage' : jQuery(instance).data('agendaPostsPerPage'),
		'agendaShowThumbnail' : jQuery(instance).data('agendaShowThumbnail'),
		'agendaShowDayOfWeek' : jQuery(instance).data('agendaShowDayOfWeek'),
		'agendaShowExcerpt' : jQuery(instance).data('agendaShowExcerpt'),
		'agendaExcerptLines' : jQuery(instance).data('agendaExcerptLines'),
		'page' : jQuery(instance).data('page')
	}

	return formData;
}

function scGetEvents(formData) {
	return jQuery.ajax({
		type: "post",
		url: ajaxParams.url,
		data: formData,
		dataType: 'json',
		encode: true
	});
}