$(document).ready(() => {	
	$('.simplecal').each((i, instance) => {
		$(instance).data('page',0);

		var formData = scGetFormData(instance);
		
		if (formData.displayPastEvents && formData.page == 0) {
			$(instance).find('.simplecal_nav_prev').addClass('active');
		}

		scGetEvents(formData).done((response) => {
			$(instance).children('.simplecal_events_wrapper').html(response.data.output);
			if (response.data.more_next_pages) {
				$(instance).find('.simplecal_nav_next').addClass('active');
			} else {
				$(instance).find('.simplecal_nav_next').removeClass('active');
			}
		});

		$(instance).on('click', '.simplecal_nav_prev.active, .simplecal_nav_next.active', (event) => {
			var instance = $(event.currentTarget).parents('.simplecal');
	
			if ($(event.currentTarget).hasClass('simplecal_nav_prev')) {
				$(instance).data().page--;
				$(instance).find('.simplecal_nav_prev').removeClass('active');
			} else {
				$(instance).data().page++;
				$(instance).find('.simplecal_nav_next').removeClass('active');
			}
			
			localStorage.setItem('simplecal_page', $(instance).data().page);
			var formData = scGetFormData($(instance));

			scGetEvents(formData).done((response) => {
				$(instance).children('.simplecal_events_wrapper').fadeOut(function() {
					$(this).html(response.data.output);
				}).fadeIn();

				if (response.data.more_next_pages) {
					$(instance).find('.simplecal_nav_next').addClass('active');
				} else {
					$(instance).find('.simplecal_nav_next').removeClass('active');
				}
				if (response.data.more_prev_pages || (formData['displayPastEvents'] && formData['page'] == 0)) {
					$(instance).find('.simplecal_nav_prev').addClass('active');
				} else {
					$(instance).find('.simplecal_nav_prev').removeClass('active');
				}
			});
		});
	});

});

function scGetFormData(instance) {
	var formData = {
		'action' : 'simplecal_get_events',
		'tags': $(instance).data('tags'),
		'displayStyle' : $(instance).data('displayStyle'),
		'displayPastEvents' : $(instance).data('displayPastEvents'),
		'displayPastEventsDays' : $(instance).data('displayPastEventsDays'),
		'displayFutureEventsDays' : $(instance).data('displayFutureEventsDays'),
		'agendaLayout' : $(instance).data('agendaLayout'),
		'agendaShowMonthYearHeaders' : $(instance).data('agendaShowMonthYearHeaders'),
		'agendaPostsPerPage' : $(instance).data('agendaPostsPerPage'),
		'agendaShowThumbnail' : $(instance).data('agendaShowThumbnail'),
		'agendaShowDayOfWeek' : $(instance).data('agendaShowDayOfWeek'),
		'agendaShowExcerpt' : $(instance).data('agendaShowExcerpt'),
		'page' : localStorage.hasOwnProperty('simplecal_page') ? localStorage.getItem('simplecal_page') : $(instance).data('page') // Whenever the form is enumerated, read the local storage for the page number if it exists. This won't work well if multiple instances are present on a single page, we'll need to assign a unique ID to each instance but not sure how to make that persistent. Later.
	}

	return formData;
}

function scGetEvents(formData) {
	return $.ajax({
		type: "post",
		url: ajaxParams.url,
		data: formData,
		dataType: 'json',
		encode: true
	});
}