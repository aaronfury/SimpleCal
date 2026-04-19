import './settings';

const VARIATION_NAME = 'simplecal/event-query-loop';
wp.domReady( () => {
	wp.blocks.registerBlockVariation( 'core/query', {
		name: VARIATION_NAME,
		title: 'SimpleCal Custom Events Query',
		description: 'Query Loop for SimpleCal events, allowing full customization of the block template',
		isActive: ( { namespace, query } ) => {
			return (
				namespace === VARIATION_NAME
			);
		},
		attributes: {
			namespace: VARIATION_NAME,
			query: {
				perPage: 5,
				pages: 0,
				offset: 0,
				postType: 'simplecal_event',
				order: 'desc',
				orderBy: 'date',
				author: '',
				search: '',
				exclude: [],
				sticky: '',
				inherit: false,
				hidePastEvents: true
			},
		},
		allowedControls: [],
		scope: [ 'inserter' ],
		innerBlocks: [
			[ 'core/post-template', {}, [
				[ 'core/post-title', { placeholder: 'Event Title' } ],
				[ 'simplecal/meta-block', { metaField: 'eventStartEndDateTime' } ],
				[ 'simplecal/meta-block', { metaField: 'eventFullAddressWithVenue', linkType: 'text' } ],
				[ 'core/post-excerpt', { placeholder: 'Event Description' } ],
			] ],
			[ 'core/query-no-results', {}, [
				[ 'core/paragraph', { placeholder: 'No events found.' } ]
			] ]
		],
		}
	);
} );