var $j = jQuery.noConflict();
const msecsInDay = 86400000;

Date.prototype.isValid = function () {
    // If the date object is invalid it will return 'NaN' on getTime() and NaN is never equal to itself
    return this.getTime() === this.getTime();
};

function compareStartAndEnd() {
	[startDate, endDate] = [new Date(document.getElementById('simplecal_event_start_datetime').value), new Date(document.getElementById('simplecal_event_end_datetime').value)];
	if (endDate < startDate) {
		$j('#simplecal_event_datetime_error').html('<p>The event\'s end date/time must be after the start date/time.</p>').slideDown();
	} else if (!endDate.isValid() || !startDate.isValid()) {
		$j('#simplecal_event_datetime_error').html('<p>Please ensure that your specified dates/times are valid.</p>').slideDown();
	} else {
		$j('#simplecal_event_datetime_error').slideUp().html('');
	}
}

function swapStateInput() {
	if ($j('#simplecal_event_country').val() != 'United States') {
		$j('#simplecal_event_state_us_wrapper').hide();
		$j('#simplecal_event_state_other_wrapper').show();
	} else {
		$j('#simplecal_event_state_us_wrapper').show();
		$j('#simplecal_event_state_other_wrapper').hide();
	}
}

$j(document).ready( () => {
	if ($j('#simplecal_event_all_day').attr('checked')) {
		let currentTime = new Date();
		startEndTimes = [
			document.getElementById('simplecal_event_start_datetime').value.concat('T',("0" + currentTime.getHours()).slice(-2),':00'),
			document.getElementById('simplecal_event_end_datetime').value.concat('T', ("0" + currentTime.getHours()).slice(-2),':00')
		];
	} else {
		startEndTimes = [
			document.getElementById('simplecal_event_start_datetime').value,
			document.getElementById('simplecal_event_end_datetime').value
		];
	}

	compareStartAndEnd();
	swapStateInput();
	
	$j('#simplecal_event_all_day').on('change', (e) => {
		target = e.target;
		if (target.checked) {
			startEndTimes = [
				document.getElementById('simplecal_event_start_datetime').value,
				document.getElementById('simplecal_event_end_datetime').value
			];
			document.getElementById('simplecal_event_start_datetime').type = "date";
			document.getElementById('simplecal_event_end_datetime').type = "date";
			document.getElementById('simplecal_event_start_datetime').value = startEndTimes[0].split('T')[0];
			document.getElementById('simplecal_event_end_datetime').value = startEndTimes[1].split('T')[0];
		} else {
			[tempStart, tempEnd] = [document.getElementById('simplecal_event_start_datetime').value, document.getElementById('simplecal_event_end_datetime').value];

			document.getElementById('simplecal_event_start_datetime').type = "datetime-local";
			document.getElementById('simplecal_event_end_datetime').type = "datetime-local";
			document.getElementById('simplecal_event_start_datetime').value = tempStart.concat('T',startEndTimes[0].split('T')[1]);
			document.getElementById('simplecal_event_end_datetime').value = tempEnd.concat('T',startEndTimes[1].split('T')[1]);
		}
		compareStartAndEnd();
	});

	$j('#simplecal_event_start_datetime').on('change', (e) => {
		[startDate, endDate] = [new Date(document.getElementById('simplecal_event_start_datetime').value), new Date(document.getElementById('simplecal_event_end_datetime').value)];
		if (endDate < startDate) {
			document.getElementById('simplecal_event_end_datetime').value = document.getElementById('simplecal_event_start_datetime').value;
		}
	});

	$j('#simplecal_event_end_datetime').on('change', () => {
		compareStartAndEnd();
	});

	$j('#simplecal_event_country').on('change', () => {
		swapStateInput();
	});
});