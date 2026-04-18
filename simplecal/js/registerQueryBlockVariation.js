const MY_VARIATION_NAME = 'simplecal/event-query-loop';

wp.blocks.registerBlockVariation( 'core/query', {
    name: MY_VARIATION_NAME,
    title: 'SimpleCal Events Query',
    description: 'Displays a list of SimpleCal events',
    isActive: ( { namespace, query } ) => {
        return (
            namespace === MY_VARIATION_NAME
            && query.postType === 'simplecal_event'
        );
    },
    attributes: {
        namespace: MY_VARIATION_NAME,
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