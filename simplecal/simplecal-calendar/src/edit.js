import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RadioControl, __experimentalNumberControl as NumberControl, TextControl, ToggleControl } from '@wordpress/components';

import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes }) {
	const {title = 'Calendar of Events', eventTags = 'all', displayStyle = 'agenda', hideOnNoEvents = false, noEventsText = "There are no upcoming events.", blockTheme = 'theme1', agendaLayout = 'layout1', agendaShowThumbnail = false, agendaShowExcerpt = false, agendaShowMonthYearHeaders = true, displayPastEvents = false, displayPastEventsDays = 7, displayFutureEventsDays = 30} = attributes;
	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<TextControl
						label= "Title"
						value= {title}
						help= "Leave blank to hide the title"
						onChange= {(value) => setAttributes({title: value})}
					/>
					<RadioControl
						label= "Display Style"
						selected= {displayStyle}
						options= {[
							{label: 'Agenda view', value: 'agenda'},
							{label: 'Calendar view', value: 'calendar'},
						]}
						onChange= {(value) => setAttributes({displayStyle: value})}
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
						disabled= {true}
						options= {[
							{
								label: 'Layout 1',
								value: 'layout1'
							},
						]}
						onChange= {(value) => setAttributes({blockTheme: value})}
					/>
					<ToggleControl
						label= "Show thumbnails"
						checked= {!!agendaShowThumbnail}
						onChange= {() => {
							setAttributes({agendaShowThumbnail: !agendaShowThumbnail})
						}}
					/>
					<ToggleControl
						label= "Show excerpt of event description"
						checked= {!!agendaShowExcerpt}
						onChange= {() => {
							setAttributes({agendaShowExcerpt: !agendaShowExcerpt})
						}}
					/>
					<ToggleControl
						label= "Group events under month and year headers"
						checked= {!!agendaShowMonthYearHeaders}
						onChange= {() => {
							setAttributes({agendaShowMonthYearHeaders: !agendaShowMonthYearHeaders})
						}}
					/>
					<NumberControl
						label= "Display events for X days in the future"
						help= "Set to '0' to display all future events"
						value= {displayFutureEventsDays}
						onChange= {(value) => setAttributes({displayFutureEventsDays: value})}
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
								value= {displayPastEventsDays}
								onChange= {(value) => setAttributes({displayPastEventsDays: value})}
							/>
						</>
						: null
					}
				</PanelBody>
			</InspectorControls>
			<p { ...useBlockProps() }>{(title && title != '') ? title : 'SimpleCal Calendar'}</p>
		</>
	);
}
