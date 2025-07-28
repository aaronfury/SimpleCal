import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { FormTokenField, PanelBody, RadioControl, __experimentalNumberControl as NumberControl, TextControl, ToggleControl, SelectControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const {
		title = 'Calendar of Events',
		eventTags,
		hideOnNoEvents = false,
		noEventsText = "There are no upcoming events.",
		blockTheme = 'theme1',
		agendaLayout = 'layout1',
		agendaShowThumbnail = false,
		agendaShowDayOfWeek = true,
		agendaShowExcerpt = false,
		agendaExcerptLines = 0,
		agendaPostsPerPage = 10,
		agendaDisplayPagination = 'both',
		agendaShowAllEventsLink = true,
		agendaShowAllEventsLinkText = 'View All Events',
		agendaShowMonthYearHeaders = true,
		displayPastEvents = false,
		displayPastEventsDays = 7,
		displayFutureEventsDays = 30
	} = attributes;

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title="Settings">
					<TextControl
						label= "Title"
						value= {title}
						help= "Leave blank to hide the title"
						onChange= {(value) => setAttributes({title: value})}
					/>
					<ToggleControl
						label= "Hide calendar when there are no events"
						checked= {!!hideOnNoEvents}
						onChange= {() => {
							setAttributes({hideOnNoEvents: !hideOnNoEvents})
						}}
					/>
					{hideOnNoEvents ?
						null :
						<>
							<TextControl
								label= "Text to display when there are no events"
								value= {noEventsText}
								onChange= {(value) => setAttributes({noEventsText: value})}
							/>
						</>
					}
					<RadioControl
						label= "Theme"
						disabled= {true}
						selected= {blockTheme}
						options= {[
							{
								label: 'Theme 1',
								value: 'theme1'
							},
						]}
						onChange= {(value) => setAttributes({blockTheme: value})}
					/>
				</PanelBody>
				<PanelBody title="Agenda View Settings">
					<RadioControl
						label= "Agenda Item Layout"
						selected= {agendaLayout}
						options= {[
							{
								label: 'Layout 1',
								value: 'layout1'
							},
							{
								label: 'Layout 2',
								value: 'layout2'
							},
						]}
						onChange= {(value) => {
								if (value == 'layout2') {
									setAttributes({agendaShowMonthYearHeaders: false})
								}
								setAttributes({agendaLayout: value})
							}
						}
					/>
					<ToggleControl
						label= "Show thumbnails"
						checked= {!!agendaShowThumbnail}
						onChange= {() => {
							setAttributes({agendaShowThumbnail: !agendaShowThumbnail})
						}}
					/>
					<ToggleControl
						label= "Show day of week (Monday, Tuesday, etc.)"
						checked= {agendaShowDayOfWeek}
						onChange= {(newValue) => {
							setAttributes({agendaShowDayOfWeek: newValue})
						}}
					/>
					<ToggleControl
						label= "Show excerpt of event description"
						checked= {!!agendaShowExcerpt}
						onChange= {() => {
							setAttributes({agendaShowExcerpt: !agendaShowExcerpt})
						}}
					/>
					{ agendaShowExcerpt ?
						<>
						<NumberControl
							label= "Limit excerpt to X lines"
							help= "Set to '0' to display the full excerpt"
							min='0'
							spinControls='custom'
							__next40pxDefaultSize= {true}
							value= {agendaExcerptLines}
							onChange= {(value) => setAttributes({agendaExcerptLines: parseInt(value)})}
						/>
						</>
						: null
					}
					<NumberControl
						label= "Display this number of events at a time"
						help= "Set to '0' to display all events"
						min='0'
						spinControls='custom'
						__next40pxDefaultSize= {true}
						value= {agendaPostsPerPage}
						onChange= {(value) => setAttributes({agendaPostsPerPage: parseInt(value)})}
					/>
					<SelectControl
						label= "Display pagination at top or bottom of agenda view"
						value= {agendaDisplayPagination}
						options= {[
							{label: 'Top', value: 'top'},
							{label: 'Bottom', value: 'bottom'},
							{label: 'Both', value: 'both'},
							{label: 'None', value: 'none'}
						]}
						onChange= {(value) => setAttributes({agendaDisplayPagination: value})}
					/>
					<ToggleControl
						label= "Show 'View All Events' link"
						checked= {!!agendaShowAllEventsLink}
						onChange= {() => {
							setAttributes({agendaShowAllEventsLink: !agendaShowAllEventsLink})
						}}
					/>
					{ agendaShowAllEventsLink ?
						<>
						<TextControl
							label= "Link text"
							value= {agendaShowAllEventsLinkText}
							help= "Leave blank for default ('View All Events')"
							onChange= {(value) => setAttributes({agendaShowAllEventsLinkText: value})}
						/>
						</>
						: null
					}
					<ToggleControl
						label= "Group events under month and year headers"
						checked= {!!agendaShowMonthYearHeaders}
						disabled= {agendaLayout == 'layout2'}
						onChange= {() => {
							setAttributes({agendaShowMonthYearHeaders: !agendaShowMonthYearHeaders})
						}}
					/>
					<NumberControl
						label= "Display events for X days in the future"
						help= "Set to '0' to display all future events"
						spinControls='custom'
						min='0'
						__next40pxDefaultSize= {true}
						value= {displayFutureEventsDays}
						onChange= {(value) => setAttributes({displayFutureEventsDays: parseInt(value)})}
					/>
					<ToggleControl
						label= "Display past events"
						checked= {!!displayPastEvents}
						onChange= {() => {
							setAttributes({displayPastEvents: !displayPastEvents})
						}}
					/>
					{displayPastEvents ?
						<>
							<NumberControl
								label= "Display events for X days in the past"
								help= "Set to '0' to display all past events"
								spinControls='custom'
								min='0'
								__next40pxDefaultSize= {true}
								value= {displayPastEventsDays}
								onChange= {(value) => setAttributes({displayPastEventsDays: parseInt(value)})}
							/>
						</>
						: null
					}
				</PanelBody>
				<PanelBody title="Filters">
					<FormTokenField
						__next40pxDefaultSize
						label= "Filter on tags"
						help= "The query will only include upcoming events that have the following tags"
						value= {eventTags}
						onChange= {(value) => setAttributes({eventTags : value})}
						
					/>
				</PanelBody>
			</InspectorControls>
			<p>{(title && title != '') ? title : 'SimpleCal Agenda View'}</p>
		</div>
	);
}
