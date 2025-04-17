import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RadioControl, SelectControl } from '@wordpress/components';

import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const {
		metaField = "Event Detail",
		linkType,
		blockType
	} = attributes;
	
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title="Settings">
					<RadioControl
						label= "Block Type"
						selectd= {blockType}
						options= {[
							{
								label: "Meta Value (<span> element)",
								value: "value"
							},
							{
								label: "Event Summary",
								value: "summary"
							},
							{
								label: "Full Event Details",
								value: "details"
							}
						]}
						onChange= {(value) => setAttributes({blockType : value})}
					/>
					{ blockType == 'value' ?
						<>
						<SelectControl
							label= "Event meta to display"
							value= {metaField}
							options= {[
								{label: 'Start Date', value: 'eventStartDate'},
								{label: 'Start Time', value: 'eventStartTime'},
								{label: 'Start Date & Time', value: 'eventStartDateTime'},
								{label: 'End Date', value: 'eventEndDate'},
								{label: 'End Time', value: 'eventEndTime'},
								{label: 'Start & End Date & Time', value: 'eventStartEndDateTime'},
								{label: 'Venue Name', value: 'eventVenueName'},
								{label: 'Street Address', value: 'eventStreetAddress'},
								{label: 'City', value: 'eventCity'},
								{label: 'State', value: 'eventState'},
								{label: 'Country', value: 'eventCountry'},
								{label: 'Full Address', value: 'eventFullAddress'},
								{label: 'Virtual Platform', value: 'eventVirtualPlatform'},
								{label: 'Meeting Link', value: 'eventMeetingLink'},
								{label: 'Website', value: 'eventWebsite'},
							]}
							onChange= {(value) => setAttributes({metaField: value})}
						/>
						</>
						: null
					}
					{ blockType == 'value' && ["eventVenueName","eventFullAddress","eventVirtualPlatform","eventMeetingLink","eventWebsite"].includes(metaField) ?
						<>
							<SelectControl
								label= "Link meta field to map/meeting/website?"
								value= {linkType}
								options= {[
									{label: 'None', value: 'none'} ,
									{label: 'Link text', value: 'text'} ,
									{label: 'Link after', value: 'after'} 
								]}
								onChange= {(value) => setAttributes({linkType: value})}
							/>
						</>
						: null
					}
				</PanelBody>
			</InspectorControls>
			<span>{metaField}</span>
		</div>
	);
}
