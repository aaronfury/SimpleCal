import React, { useState } from 'react';
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

function SimpleCal(props) {
	const queryParams = {};
	const [items, setItems] = useState([]);

	apiFetch({
		path: addQueryArgs('/wp/v2/simplecal_event', queryParams)
	}).then((posts) => {
		console.log(posts);
		posts.forEach( (post) => {
			const newItem = {title: post.title.rendered, startDate: post.meta.simplecal_event_start_timestamp, description: post.content.rendered};
			setItems((items) => [...items, newItem]);
		});
	});

	return (
		<>
		{items.map(item => (
			<AgendaItem title={item.title} startDate={item.startDate} description={item.description}></AgendaItem>
		))}
		</>
	)
}