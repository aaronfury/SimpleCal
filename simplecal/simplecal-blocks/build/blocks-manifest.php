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
			'html' => false
		),
		'textdomain' => 'simplecal-agenda-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
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
				'default' => 'layout1'
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
	)
);
