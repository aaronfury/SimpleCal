import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom/client';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { Button, ButtonGroup } from '@wordpress/components';
import Agenda from './Components/Agenda';
import AgendaItem from './Components/AgendaItem';

const scInstances = document.querySelectorAll('.simplecal-root');
scInstances.forEach(instance => {
	const root = ReactDOM.createRoot(instance);
	
	root.render(
		<SimpleCal
			displayStyle={instance.getAttribute('data-display-style')}
			displayPastEvents={instance.getAttribute('data-display-past-events')}
			displayPastEventsDays={instance.getAttribute('data-display-past-events-days')}
			displayFutureEventsDays={instance.getAttribute('data-display-future-events-days')}
			agendaShowMonthYearHeaders={instance.getAttribute('data-show-month-year-headers')}
			agendaPostsPerPage={instance.getAttribute('data-agenda-posts-per-page')}
			agendaShowThumbnail={instance.getAttribute('data-agenda-show-thumbnail')}
			agendaShowExcerpt={instance.getAttribute('data-agenda-show-excerpt')}
			title={instance.getAttribute('data-widget-title')}
	/>);
});

function SimpleCal({displayStyle,displayPastEvents,displayPastEventsDays,displayFutureEventsDays,agendaShowMonthYearHeaders, agendaPostsPerPage,agendaShowThumbnail,agendaShowExcerpt}) {
	let currentDate = new Date();

	const [calItems, setCalItems] = useState([]);
	const [agendaPage, setAgendaPage] = useState(1);
	const [calendarMonth, setCalendarMonth] = useState(currentDate.getMonth());

	var queryParams = {
		'pastEvents' : displayPastEvents,
		'pastEventsDays' : displayPastEventsDays,
		'futureEventsDays' : displayFutureEventsDays,
		'displayStyle' : displayStyle,
	};

	if (displayStyle == 'agenda') {
		queryParams.agendaPostsPerPage = agendaPostsPerPage;
		queryParams.agendaPage = agendaPage;
	} else {
		queryParams.calendarMonth = calendarMonth;
	}

	useEffect(() => {
		fetchCalItems();
	}, [agendaPage, calendarMonth]);

	const fetchCalItems = async () => {
		await apiFetch({
			path: addQueryArgs('simplecal/v1/events', queryParams),
		}).then(response => {
			console.log(response);
			
			var items = [];
			response.data.forEach( (post) => {
				items.push({
					title: post.title,
					startDate: post.start_date,
					endDate: post.end_date,
					description: post.description,
					thumbnail: post.thumbnail
				});
			});
			setCalItems(items);
		});
	}
	return (
		<>
		<ButtonGroup>
			<Button variant="primary" onClick={() => setAgendaPage(agendaPage -1)}>Previous Page</Button>
			<Button onClick={() => setAgendaPage(agendaPage +1)}>Next Page</Button>
		</ButtonGroup>
		{calItems.map(item => (
			<AgendaItem title={item.title} startDate={item.startDate} endDate={item.endDate} description={item.description}></AgendaItem>
		))}
		</>
	)
}