import { getContext, getElement, store } from '@wordpress/interactivity';
const apiFetch = window.wp.apiFetch;

const { state } = store( 'agendaBlock', {
	state: {
		output: '',
		currentPage: 0,
		morePastEvents: true,
		moreFutureEvents: true
	},
	actions: {
		getEvents: () => {
			const { ref } = getElement();
			const context = getContext();

			switch (ref.dataset.direction) {
				case "future":
					if (!state.moreFutureEvents) {return}
					++state.currentPage;
					break;
				case "previous":
					if (!state.morePastEvents) {return}
					--state.currentPage;
					break;
				default:
					break;
			}

			const queryArgs = [
				`page=${state.currentPage.toString()}`,
				`per_page=${context.postsPerPage}`,
				`agendaLayout=${context.agendaLayout}`,
				`monthYearHeadersShow=${context.monthYearHeadersShow}`,
				`dayOfWeekShow=${context.dayOfWeekShow}`,
				`thumbnailShow=${context.thumbnailShow}`,
				`excerptShow=${context.excerptShow}`,
				`excerptLines=${context.excerptLines}`,
				`pastEventsShow=${context.pastEventsShow}`,
				`pastEventsDays=${context.pastEventsDays}`,
				`futureEventsDays=${context.futureEventsDays}`,
				`eventTags=${context.eventTags}`,
				`tagsShow=${context.tagsShow}`
			];

			apiFetch({
				// TODO: It sure would be nice to use addQueryArgs to build the path, but WordPress doesn't support adding scripts to script modules yet; that's also why apiFetch is being called in a janky way
				path: `/simplecal/v1/events/agenda/?${queryArgs.join('&')}`
			}).then(
				(response) => {
					state.output = response.output;
					state.morePastEvents = response.morePrevious;
					state.moreFutureEvents = response.moreFuture;
				}
			);
			
		}
	},
	callbacks: {
		updateAgenda: () => {
			const { ref } = getElement();
			ref.innerHTML = state.output;
		}
	}
});