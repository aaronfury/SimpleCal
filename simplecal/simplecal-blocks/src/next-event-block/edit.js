import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Button, Flex, FlexItem, FormTokenField, Icon, PanelBody, TextControl, ToggleControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { calendar, mapMarker, scheduled  } from '@wordpress/icons';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const {
		hideOnNoEvent = false,
		blockTitle = 'Next Event',
		showEventTitle = true,
		linkEventTitle = true,
		showIcons = true,
		showLabels = true,
		boldLabels = true,
		showLocation = true,
		showAllEventsLink = true,
		cornerRadius = 0,
		tags
	} = attributes;

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title="Basics">
					<ToggleControl
						label= "Hide block if no event"
						help= "If there is no upcoming event that matches the filters, don't render the block"
						checked= {hideOnNoEvent}
						onChange= {(value) => setAttributes({hideOnNoEvent : value})}
					/>
					<TextControl
						__next40pxDefaultSize
						label= "Block Title"
						value= {blockTitle}
						help= "Leave blank to hide the block title"
						onChange= {(value) => setAttributes({blockTitle : value})}
					/>
				</PanelBody>
				<PanelBody title="Styling">
					<ToggleControl
						label= "Show event name"
						help= "Whether to show the event name"
						checked= {showEventTitle}
						onChange= { (value) => setAttributes( {showEventTitle : value})}
					/>
					{showEventTitle ?
						<ToggleControl
							label= "Title links to event details"
							help= "Whether the event title links to the event details"
							checked= {linkEventTitle}
							onChange= { (value) => setAttributes( {linkEventTitle : value})}
						/>
						: null
					}
					<ToggleControl
						label= "Show icons"
						help= "Whether to show an icon (calendar, watch, etc.) before each meta value"
						checked= {showIcons}
						onChange= { (value) => setAttributes( {showIcons : value})}
					/>
					<ToggleControl
						label= "Show labels"
						help= "Whether to show a label before each meta value"
						checked= {showLabels}
						onChange= { (value) => setAttributes( {showLabels : value})}
					/>
					{ showLabels ?
						<ToggleControl
							label= "Show labels with bold font weight"
							help= "Wrap labels in '<strong>' tags"
							checked= {boldLabels}
							onChange= { (value) => setAttributes( {boldLabels : value})}
						/>
						: null
					}
					<ToggleControl
						label= "Show location"
						help= "Whether to include the event location (physical and virtual)"
						checked= {showLocation}
						onChange={ (value) => setAttributes( {showLocation : value})}
					/>
					<ToggleControl
						label= "Show link to all events"
						help= "Whether to include an 'All events' link at the bottom of the block"
						checked= {showAllEventsLink}
						onChange= { (value) => setAttributes( {showAllEventsLink : value})}
					/>
					<NumberControl
						__next40pxDefaultSize
						label= "Corner radius"
						help= "Only applies if a background color is set"
						step= {4}
						spinControls= 'native'
						onChange= { (value) => setAttributes( {cornerRadius : value})}
						value= {cornerRadius}
						max= {24}
						min= {0}
					/>
				</PanelBody>
				<PanelBody title="Filters">
					<FormTokenField
						__next40pxDefaultSize
						label= "Filter on tags"
						help= "The query will only include upcoming events that have the following tags"
						value= {tags}
						onChange= {(value) => setAttributes({tags : value})}
						
					/>
				</PanelBody>
			</InspectorControls>
			<div>
				{(blockTitle && blockTitle != '') ?
					<>
						<h2>{blockTitle}</h2>
						<hr />
					</>
				: null }
				{showEventTitle ?
					linkEventTitle ?
						<a href='javascript:;'>
							<h3>Event Title</h3>
						</a>
						:
						<h3>Event Title</h3>
					: null
				}
				<Flex align='normal' justify='flex-start'>
					{showIcons ?
						<FlexItem>
							<Icon icon={calendar} />
						</FlexItem>
						: null }
					<FlexItem>{ boldLabels ?
						<strong>Date:</strong>
						:
						<span>Date:</span>}</FlexItem>
					<FlexItem>October 18, 2025</FlexItem>
				</Flex>
				<Flex align='normal' justify='flex-start'>
					{showIcons ?
						<FlexItem>
							<Icon icon={scheduled} />
						</FlexItem>
						: null }
					<FlexItem>{ boldLabels ?
						<strong>Time:</strong>
						:
						<span>Time:</span>}</FlexItem>
					<FlexItem>3:00 PM</FlexItem>
				</Flex>
				{showLocation ?
					<Flex align='normal' justify='flex-start'>
						{showIcons ?
							<FlexItem>
								<Icon icon={mapMarker} />
							</FlexItem>
							: null }
						<FlexItem>{ boldLabels ?
							<strong>Location:</strong>
							:
							<span>Location:</span>}</FlexItem>
						<FlexItem><a href='javascript:;'>Online Meeting</a></FlexItem>
					</Flex>
					: null
				}

				{showAllEventsLink ?
					<Flex justify='center'>
						<FlexItem><Button variant='primary'>View all events</Button></FlexItem>
					</Flex>
					: null
				}
			</div>
		</div>
	);
}
