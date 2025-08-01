import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { FormTokenField, PanelBody, RadioControl, __experimentalNumberControl as NumberControl, TextControl, ToggleControl, SelectControl, Flex, FlexItem, Icon, __experimentalHStack as HStack, Button } from '@wordpress/components';
import { image, arrowLeft, arrowRight } from '@wordpress/icons';

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
		agendaShowTags = false,
		agendaPostsPerPage = 10,
		agendaDisplayPagination = 'both',
		agendaShowAllEventsLink = true,
		agendaShowAllEventsLinkText = 'View All Events',
		agendaShowMonthYearHeaders = true,
		displayPastEvents = false,
		displayPastEventsDays = 7,
		displayFutureEventsDays = 30
	} = attributes;

	function renderTags() {
		return <>
			<HStack direction='row' justify='flex-start'>
				{[...Array(3)].map((x,i) =>
					<div style={{padding:'2px 8px', borderRadius:'4px', backgroundColor:'#ddd', fontSize:'0.8em'}}>Tag {i+1}</div>
				)}
			</HStack>
		</>
	}

	function renderPreview() {
		return <>
			{ agendaDisplayPagination == 'top' || agendaDisplayPagination == 'both' ?
				<HStack style= {{margin: '0.5em 0'}}>
					<HStack justify='flex-start'>
						<Icon icon={arrowLeft} />
						<span>Previous Events</span>
					</HStack>
					<HStack justify='flex-end'>
						<span>Future Events</span>
						<Icon icon={arrowRight} />
					</HStack>
				</HStack>
			: null}
			{agendaLayout == 'layout1' && <>
				<Flex direction='column' gap='5'>
					{[...Array(3)].map((x,i) =>
					<FlexItem>
						<Flex gap='2' justify='flex-start'>
							{ agendaShowThumbnail &&
								<Icon icon={image} size='48' />
							}
							<FlexItem>
								<Flex direction='column' gap='1'>
									<FlexItem>
										May {i+1}, 2025{agendaShowDayOfWeek && <span> (Day of Week)</span>}
									</FlexItem>
									<FlexItem>
										<h3 style={{margin:'0'}}>Event {i+1} Title</h3>
									</FlexItem>
									<FlexItem>
										{8+i}:00 PM - {9+i}:00PM
									</FlexItem>
									<FlexItem>
										Event Location
									</FlexItem>
									<FlexItem>
										{agendaShowTags && renderTags()}
									</FlexItem>
									{ agendaShowExcerpt &&
										<FlexItem>
											Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam faucibus a augue ac suscipit. Vivamus blandit maximus ipsum, nec maximus metus blandit eget. Donec egestas, diam non faucibus blandit, tortor augue ultrices orci...
										</FlexItem>
									}
								</Flex>
							</FlexItem>
						</Flex>
					</FlexItem>
					)}
				</Flex>
			</>}
			{agendaLayout == 'layout3' && <>
				<Flex direction='column' gap='3'>
					{[...Array(3)].map((x,i) =>
						<FlexItem>
							<Flex gap='1' justify='flex-start'>
								{ agendaShowThumbnail &&
									<Icon icon={image} size='24' />
								}
								<FlexItem>
									<Flex direction='column' gap='0'>
										<FlexItem>
											May {i+1}, 2025{agendaShowDayOfWeek && <span> (Day of Week) at {8+i}PM</span>}
										</FlexItem>
										<FlexItem>
											<h4 style={{margin:'0'}}>Event {i+1} Title</h4>
										</FlexItem>
										<FlexItem>
											Event Location
										</FlexItem>
										<FlexItem>
											{agendaShowTags && renderTags()}
										</FlexItem>
									</Flex>
								</FlexItem>
							</Flex>
						</FlexItem>
					)}
				</Flex>
			</>}
			{ agendaDisplayPagination == 'bottom' || agendaDisplayPagination == 'both' ? 
				<HStack style= {{marginTop: '0.5em'}}>
					<HStack justify='flex-start'>
						<Icon icon={arrowLeft} />
						<span>Previous Events</span>
					</HStack>
					<HStack justify='flex-end'>
						<span>Future Events</span>
						<Icon icon={arrowRight} />
					</HStack>
				</HStack>
			: null }
			{ agendaShowAllEventsLink && <>
				<HStack justify= 'center' style= {{marginTop: '0.5em'}}>
					<Button variant= 'primary'>{agendaShowAllEventsLinkText}</Button>
				</HStack>
			</>}
		</>
	}

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title='Settings'>
					<TextControl
						label= 'Title'
						value= {title}
						help= 'Leave blank to hide the title'
						onChange= {(value) => setAttributes({title: value})}
					/>
					<ToggleControl
						label= 'Hide calendar when there are no events'
						checked= {!!hideOnNoEvents}
						onChange= {() => {
							setAttributes({hideOnNoEvents: !hideOnNoEvents})
						}}
					/>
					{!hideOnNoEvents &&
						<>
							<TextControl
								label= 'Text to display when there are no events'
								value= {noEventsText}
								onChange= {(value) => setAttributes({noEventsText: value})}
							/>
						</>
					}
					<RadioControl
						label= 'Theme'
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
				<PanelBody title='Agenda View Settings'>
					<RadioControl
						label= 'Agenda Item Layout'
						selected= {agendaLayout}
						options= {[
							{
								label: 'List',
								value: 'layout1'
							},
							{
								label: 'Compact List',
								value: 'layout3'
							},
							{
								label: 'Grid',
								value: 'layout2'
							}
						]}
						onChange= {(value) => {
								if (value == 'layout2') {
									setAttributes({agendaShowMonthYearHeaders: false})
								} else if (value == 'layout3') {
									setAttributes({agendaShowMonthYearHeaders: false})
									setAttributes({agendaShowExcerpt: false})
								}
								setAttributes({agendaLayout: value})
							}
						}
					/>
					<ToggleControl
						label= 'Show thumbnails'
						checked= {!!agendaShowThumbnail}
						onChange= {() => {
							setAttributes({agendaShowThumbnail: !agendaShowThumbnail})
						}}
					/>
					<ToggleControl
						label= 'Show day of week (Monday, Tuesday, etc.)'
						checked= {agendaShowDayOfWeek}
						onChange= {(newValue) => {
							setAttributes({agendaShowDayOfWeek: newValue})
						}}
					/>
					<ToggleControl
						label= 'Show tags assigned to each event'
						checked= {!!agendaShowTags}
						onChange= {() => {
							setAttributes({agendaShowTags: !agendaShowTags})
						}}
					/>
					<ToggleControl
						label= 'Show excerpt of event description'
						checked= {!!agendaShowExcerpt}
						onChange= {() => {
							setAttributes({agendaShowExcerpt: !agendaShowExcerpt})
						}}
						disabled= {agendaLayout == 'layout3'}
					/>
					{ agendaShowExcerpt &&
						<>
						<NumberControl
							label= 'Limit excerpt to X lines'
							help= 'Set to "0" to display the full excerpt'
							min= '0'
							spinControls='custom'
							__next40pxDefaultSize= {true}
							value= {agendaExcerptLines}
							onChange= {(value) => setAttributes({agendaExcerptLines: parseInt(value)})}
						/>
						</>
					}
					<NumberControl
						label= 'Display this number of events at a time'
						help= 'Set to "0" to display all events'
						min= '0'
						spinControls='custom'
						__next40pxDefaultSize= {true}
						value= {agendaPostsPerPage}
						onChange= {(value) => setAttributes({agendaPostsPerPage: parseInt(value)})}
					/>
					<SelectControl
						label= 'Display pagination at top or bottom of agenda view'
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
						label= 'Show "View All Events" link'
						checked= {!!agendaShowAllEventsLink}
						onChange= {() => {
							setAttributes({agendaShowAllEventsLink: !agendaShowAllEventsLink})
						}}
					/>
					{ agendaShowAllEventsLink &&
						<>
						<TextControl
							label= 'Link text'
							value= {agendaShowAllEventsLinkText}
							help= 'Leave blank for default ("View All Events")'
							onChange= {(value) => setAttributes({agendaShowAllEventsLinkText: value})}
						/>
						</>
					}
					<ToggleControl
						label= 'Group events under month and year headers'
						checked= {!!agendaShowMonthYearHeaders}
						disabled= {agendaLayout != 'layout1'}
						onChange= {() => {
							setAttributes({agendaShowMonthYearHeaders: !agendaShowMonthYearHeaders})
						}}
					/>
					<NumberControl
						label= 'Display events for X days in the future'
						help= 'Set to "0" to display all future events'
						spinControls= 'custom'
						min= '0'
						__next40pxDefaultSize= {true}
						value= {displayFutureEventsDays}
						onChange= {(value) => setAttributes({displayFutureEventsDays: parseInt(value)})}
					/>
					<ToggleControl
						label= 'Display past events'
						checked= {!!displayPastEvents}
						onChange= {() => {
							setAttributes({displayPastEvents: !displayPastEvents})
						}}
					/>
					{displayPastEvents &&
						<>
							<NumberControl
								label= 'Display events for X days in the past'
								help= 'Set to "0" to display all past events'
								spinControls= 'custom'
								min= '0'
								__next40pxDefaultSize= {true}
								value= {displayPastEventsDays}
								onChange= {(value) => setAttributes({displayPastEventsDays: parseInt(value)})}
							/>
						</>
					}
				</PanelBody>
				<PanelBody title= 'Filters'>
					<FormTokenField
						__next40pxDefaultSize
						label= 'Filter on tags'
						help= 'The query will only include upcoming events that have the following tags'
						value= {eventTags}
						onChange= {(value) => setAttributes({eventTags : value})}
						
					/>
				</PanelBody>
			</InspectorControls>
			<div>
				<h2>{(title && title != '') ? title : 'SimpleCal Agenda View'}</h2>
				{renderPreview()}
			</div>
		</div>
	);
}
