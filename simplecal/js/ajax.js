$(document).ready(() => {	
	$('.simplecal').each((i, instance) => {
		$(instance).data('page',0);

		var formData = scGetFormData(instance);
		
		scGetEvents(formData).done((response) => {
			$(instance).children('.simplecal_events_wrapper').html(response.data.output);
		});
	});

	$('.simplecal_nav_prev, .simplecal_nav_next').on('click', (event) => {
		var instance = $(event.target).parent();
	
		console.log($(instance).data('page'));
		if ($(event.target).hasClass('simplecal_nav_prev')) {
			$(instance).data().page--;
		} else {
			$(instance).data().page++;
		}
		console.log($(instance).data('page'));
		var formData = scGetFormData($(instance));

		scGetEvents(formData).done((response) => {
			$(instance).children('.simplecal_events_wrapper').fadeOut().html(response.data.output).fadeIn();
		});
	});
});

function scGetFormData(instance) {
	var formData = {
		'action' : 'simplecal_get_events',
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