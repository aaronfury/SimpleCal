var $j = jQuery.noConflict();
const msecsInDay = 86400000;

function compareStartAndEnd() {
	[startDate, endDate] = [new Date(document.getElementById('simplecal_event_start_datetime').value), new Date(document.getElementById('simplecal_event_end_datetime').value)];
	if (endDate < startDate) {
		$j('#simplecal_event_datetime_error').slideDown();
	} else {
		$j('#simplecal_event_datetime_error').slideUp();
	}
}

$j(document).ready( () => {
	let startendtimes = [
		document.getElementById('simplecal_event_start_datetime').value,
		document.getElementById('simplecal_event_end_datetime').value
	];
	compareStartAndEnd();
	
	$j('#simplecal_event_all_day').on('change', (e) => {
		target = e.target;
		if (target.checked) {
			startendtimes = [
				document.getElementById('simplecal_event_start_datetime').value,
				document.getElementById('simplecal_event_end_datetime').value
			];
			document.getElementById('simplecal_event_start_datetime').type = "date";
			document.getElementById('simplecal_event_end_datetime').type = "date";
			document.getElementById('simplecal_event_start_datetime').value = startendtimes[0].split('T')[0];
			document.getElementById('simplecal_event_end_datetime').value = startendtimes[1].split('T')[0];
		} else {
			[tempStart, tempEnd] = [document.getElementById('simplecal_event_start_datetime').value, document.getElementById('simplecal_event_end_datetime').value];

			document.getElementById('simplecal_event_start_datetime').type = "datetime-local";
			document.getElementById('simplecal_event_end_datetime').type = "datetime-local";
			document.getElementById('simplecal_event_start_datetime').value = tempStart.concat('T',startendtimes[0].split('T')[1]);
			document.getElementById('simplecal_event_end_datetime').value = tempEnd.concat('T',startendtimes[1].split('T')[1]);
		}
		compareStartAndEnd();
	});

	$j('#simplecal_event_start_datetime').on('change', (e) => {
		target = e.target;
		[startDate, endDate] = [new Date(document.getElementById('simplecal_event_start_datetime').value), new Date(document.getElementById('simplecal_event_end_datetime').value)];
		if (endDate < startDate) {
			document.getElementById('simplecal_event_end_datetime').value = document.getElementById('simplecal_event_start_datetime').value;
		}
	});

	$j('#simplecal_event_end_datetime').on('change', () => {
		compareStartAndEnd();
	});
});