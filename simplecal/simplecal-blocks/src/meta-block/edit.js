import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RadioControl, SelectControl, TextControl, __experimentalHStack as HStack } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const {
		blockType = 'value',
		metaDisplayAs = 'div',
		metaField = 'eventStartDateTime',
		metaDateFormat = 'shortDateAndTime',
		metaDateCustomFormat = '',
		metaTimeCustomFormat = '',
		linkType
	} = attributes;
	
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title="Settings">
					<RadioControl
						label= "Block type"
						selected= {blockType}
						options= {[
							{label: "Event Detail", value: "value"},
							{label: "Event Summary", value: "summary"},
							{label: "Full Event Details", value: "details"},
						]}
						onChange= {(value) => setAttributes({blockType : value})}
					/>
					{ blockType == 'value' ?
						<>
						<RadioControl
							label= "Display as"
							selected= {metaDisplayAs}
							options= {[
								{label: "Block (<div> element)", value: "div"},
								{label: "Inline (<span> element)", value: "span"},
							]}
							onChange= {(value) => setAttributes({metaDisplayAs : value})}
							help= "Inline allows you to place multiple meta blocks on the same line. Block will place each meta block on its own line."
						/>
						<SelectControl
							label= "Event meta to display"
							value= {metaField}
							options= {[
								{label: 'Start Date/Time', value: 'eventStartDateTime'},
								{label: 'End Date/Time', value: 'eventEndDateTime'},
								{label: 'Start & End Date/Time', value: 'eventStartEndDateTime'},
								{label: 'Venue Name', value: 'eventVenueName'},
								{label: 'Full Address with Venue', value: 'eventFullAddressWithVenue'},
								{label: 'Full Address', value: 'eventFullAddress'},
								{label: 'Street Address', value: 'eventStreetAddress'},
								{label: 'City', value: 'eventCity'},
								{label: 'State', value: 'eventState'},
								{label: 'Country', value: 'eventCountry'},
								{label: 'Virtual Platform', value: 'eventVirtualPlatform'},
								{label: 'Meeting Link', value: 'eventMeetingLink'},
								{label: 'Website', value: 'eventWebsite'},
							]}
							onChange= {(value) => setAttributes({metaField: value})}
							__next40pxDefaultSize
						/>
						</>
						: null
					}
					{ blockType == 'value' && ["eventStartDateTime","eventEndDateTime","eventStartEndDateTime"].includes(metaField) ?
						<>
						<SelectControl
							label= "Date format"
							value= {metaDateFormat}
							options= {[
								{label: 'Day of Week', value: 'dayOfWeek', disabled: metaField == 'eventStartEndDateTime'},
								{label: 'Short Date', value: 'shortDate'},
								{label: 'Long Date', value: 'longDate'},
								{label: 'Time', value: 'time'},
								{label: 'Day of Week and Time', value: 'dayOfWeekAndTime'},
								{label: 'Short Date & Time', value: 'shortDateAndTime'},
								{label: 'Long Date & Time', value: 'longDateAndTime'},
								{label: 'Custom Format', value: 'custom'}
							]}
							__next40pxDefaultSize
							onChange= {(value) => setAttributes({metaDateFormat: value})}
						/>
						</>
						: null
					}
					{ blockType == 'value' && metaDateFormat == 'custom' ?
						<>
						<HStack alignment='top'>
							<TextControl
								label= "Date format"
								value= {metaDateCustomFormat}
								onChange= {(value) => setAttributes({metaDateCustomFormat: value})}
								__next40pxDefaultSize
							/>
							<TextControl
								label= "Time format"
								value= {metaTimeCustomFormat}
								onChange= {(value) => setAttributes({metaTimeCustomFormat: value})}
								__next40pxDefaultSize
							/>
						</HStack>
						<small>Use <a href="https://www.php.net/manual/en/datetime.format.php" target="_blank" rel="noopener noreferrer">PHP DateTime format</a>. Leave blank to hide date or time.</small>
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
						: setAttributes({linkType: 'none'})
					}
				</PanelBody>
			</InspectorControls>
			<span>{
				(() => {
					let display = '';
					switch (metaField) {
						case 'eventStartDateTime':
						case 'eventEndDateTime':
							switch (metaDateFormat) {
								case 'dayOfWeek':
									display = 'Friday';
									break;
								case 'shortDate':
									display = '5/1/26';
									break;
								case 'longDate':
									display = 'May 1, 2026';
									break;
								case 'shortDateAndTime':
									display = '5/1/26 12:00 PM';
									break;
								case 'longDateAndTime':
									display = 'May 1, 2026 12:00 PM';
									break;
								case 'time':
									display = '12 PM';
									break;
								case 'custom':
									display = `${metaDateCustomFormat} ${metaTimeCustomFormat}` || 'Custom format';
									break;
							};
							break;
						case 'eventStartEndDateTime':
							switch (metaDateFormat) {
								case 'shortDate':
									display = '5/1/26 - 5/3/26';
									break;
								case 'longDate':
									display = 'May 1 - 3, 2026';
									break;
								case 'shortDateAndTime':
									display = '5/1/26 12:00 PM - 3:00 PM';
									break;
								case 'longDateAndTime':
									display = 'May 1 - 3, 2026 12:00 PM';
									break;
								case 'time':
									display = '12 PM - 3 PM';
									break;
								case 'custom':
									display = `${metaDateCustomFormat} ${metaTimeCustomFormat}` || 'Custom format';
									break;
							};
							break;
						case 'eventVenueName':
							display = 'Venue Name';
							break;
						case 'eventFullAddressWithVenue':
							display = 'Venue Name, Street Address, City, State, Country';
							break;
						case 'eventFullAddress':
							display = 'Street Address, City, State, Country';
							break;
						case 'eventStreetAddress':
							display = 'Street Address';
							break;
						case 'eventCity':
							display = 'City';
							break;
						case 'eventState':
							display = 'State';
							break;
						case 'eventCountry':
							display = 'Country';
							break;
						case 'eventVirtualPlatform':
							display = 'Virtual Platform';
							break;
						case 'eventMeetingLink':
							display = 'https://meeting.link/meeting-id';
							break;
						case 'eventWebsite':
							display = 'website.com';
							break;
					}

					switch (linkType) {
						case 'text':
							display = <a href="#">{display}</a>;
							break;
						case 'after':
							display = <>{display} <a href="#">(link)</a></>;
							break;
					}

					return display;
				})()
			}</span>
		</div>
	);
}
