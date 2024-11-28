$(document).ready(() => {	
	$('.simplecal').each((i, instance) => {
		$(instance).data('page',0);

		var formData = scGetFormData(instance);
		
		if (formData['displayPastEvents'] && formData['page'] == 0) {
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
			} else {
				$(instance).data().page++;
			}
			var formData = scGetFormData($(instance));

			scGetEvents(formData).done((response) => {
				$(instance).children('.simplecal_events_wrapper').fadeTo(0).html(response.data.output).css('opacity',1);
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
		'agendaShowMonthYearHeaders' : $(instance).data('agendaShowMonthYearHeaders'),
		'agendaPostsPerPage' : $(instance).data('agendaPostsPerPage'),
		'agendaShowThumbnail' : $(instance).data('agendaShowThumbnail'),
		'agendaShowExcerpt' : $(instance).data('agendaShowExcerpt'),
		'page' : $(instance).data('page')
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