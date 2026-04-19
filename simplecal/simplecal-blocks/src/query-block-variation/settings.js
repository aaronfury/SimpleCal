import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const VARIATION_NAMESPACE = 'simplecal/event-query-loop';

const withSimpleCalControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes } = props;

		// Only target the core/query block with our specific namespace.
		if (
			name !== 'core/query' ||
			attributes?.namespace !== VARIATION_NAMESPACE
		) {
			return <BlockEdit {...props} />;
		}

		const { query = {} } = attributes;
		const perPage = query.perPage ?? 5;
		const order = query.order ?? 'desc';
		const hidePastEvents = query.hidePastEvents ?? true;

		const updateQuery = (newValues) => {
			setAttributes({
				query: {
					...query,
					...newValues,
				},
			});
		};

		return (
			<>
				<BlockEdit {...props} />
				<InspectorControls>
					<PanelBody
						title={__('Event Query Settings', 'simplecal')}
						initialOpen={true}
					>
						<RangeControl
							label={__('Number of Events', 'simplecal')}
							value={perPage}
							onChange={(value) => updateQuery({ perPage: value })}
							min={1}
							max={50}
						/>
						<CheckboxControl
							label={__('Hide Past Events', 'simplecal')}
							checked={!! hidePastEvents}
							onChange={(value) => updateQuery({ hidePastEvents: value })}
							help={ __('Only show events whose END date is today or later.', 'simplecal' ) }
						/>
						<SelectControl
							label={__('Order', 'simplecal')}
							value={order}
							options={[
								{
									label: __('Ascending (upcoming first)', 'simplecal'),
									value: 'asc',
								},
								{
									label: __('Descending (latest first)', 'simplecal'),
									value: 'desc',
								},
							]}
							onChange={(value) => updateQuery({ order: value })}
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withSimpleCalControls');

addFilter(
	'editor.BlockEdit',
	'simplecal/query-inspector-controls',
	withSimpleCalControls
);