<?php
// This file is generated. Do not modify it manually.
return array(
	'agenda-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'simplecal/agenda-block',
		'version' => '0.1.0',
		'title' => 'SimpleCal Agenda',
		'category' => 'widgets',
		'icon' => 'calendar',
		'description' => 'Display SimpleCal calendar events in an agenda view.',
		'example' => array(
			
		),
		'supports' => array(
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'color' => array(
				'button' => true,
				'heading' => true
			),
			'html' => false,
			'interactivity' => true
		),
		'textdomain' => 'simplecal-agenda-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js',
		'attributes' => array(
			'title' => array(
				'type' => 'string',
				'default' => 'Calendar of Events'
			),
			'hideOnNoEvents' => array(
				'type' => 'boolean',
				'default' => false
			),
			'noEventsText' => array(
				'type' => 'string',
				'default' => 'There are no upcoming events.'
			),
			'agendaLayout' => array(
				'type' => 'string',
				'default' => 'list'
			),
			'agendaShowDayOfWeek' => array(
				'type' => 'boolean',
				'default' => true
			),
			'agendaShowThumbnail' => array(
				'type' => 'boolean',
				'default' => false
			),
			'agendaShowExcerpt' => array(
				'type' => 'boolean',
				'default' => false
			),
			'agendaExcerptLines' => array(
				'type' => 'integer',
				'default' => 0
			),
			'agendaShowTags' => array(
				'type' => 'boolean',
				'default' => false
			),
			'agendaPostsPerPage' => array(
				'type' => 'integer',
				'default' => 10
			),
			'agendaDisplayPagination' => array(
				'type' => 'string',
				'default' => 'both'
			),
			'agendaShowAllEventsLink' => array(
				'type' => 'boolean',
				'default' => 'true'
			),
			'agendaShowAllEventsLinkText' => array(
				'type' => 'string',
				'default' => 'View All Events'
			),
			'agendaShowMonthYearHeaders' => array(
				'type' => 'boolean',
				'default' => true
			),
			'blockTheme' => array(
				'type' => 'string',
				'default' => 'theme1'
			),
			'displayPastEvents' => array(
				'type' => 'boolean',
				'default' => false
			),
			'displayPastEventsDays' => array(
				'type' => 'integer',
				'default' => 7
			),
			'displayFutureEventsDays' => array(
				'type' => 'integer',
				'default' => 0
			),
			'eventTags' => array(
				'type' => 'array',
				'default' => array(
					
				)
			)
		)
	),
	'meta-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'simplecal/meta-block',
		'version' => '0.1.0',
		'title' => 'SimpleCal Meta Block',
		'category' => 'text',
		'icon' => 'index-card',
		'description' => 'Renders custom post meta for events, both in the single view and in a query loop',
		'example' => array(
			
		),
		'usesContext' => array(
			'postId',
			'postType'
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'simplecal-meta-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'attributes' => array(
			'metaField' => array(
				'type' => 'string'
			),
			'linkType' => array(
				'type' => 'string'
			),
			'blockType' => array(
				'type' => 'string'
			)
		)
	),
	'next-event-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'simplecal/next-event-block',
		'version' => '0.1.0',
		'title' => 'SimpleCal Next Event Block',
		'category' => 'widgets',
		'icon' => 'schedule',
		'description' => 'Displays information about the chronologically-next event.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'color' => array(
				'button' => true,
				'heading' => true
			)
		),
		'textdomain' => 'simplecal-next-event-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'attributes' => array(
			'hideOnNoEvent' => array(
				'type' => 'boolean',
				'default' => false
			),
			'blockTitle' => array(
				'type' => 'string',
				'default' => 'Next Event'
			),
			'showEventTitle' => array(
				'type' => 'boolean',
				'default' => true
			),
			'linkEventTitle' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showIcons' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLabels' => array(
				'type' => 'boolean',
				'default' => true
			),
			'boldLabels' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLocation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAllEventsLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'cornerRadius' => array(
				'type' => 'integer',
				'default' => 0
			),
			'tags' => array(
				'type' => 'array',
				'default' => array(
					
				)
			)
		)
	)
);
