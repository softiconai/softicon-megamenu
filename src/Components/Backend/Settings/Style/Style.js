import { __ } from '@wordpress/i18n';
import { RangeControl } from '@wordpress/components';
import { alignOptions, widthOptions } from '../../../../utils/options';
import { ColorRow, FieldHead, Panel, Reset, Tiles, ToggleRow } from '../../../../vendor/softicon-tools/components/Panels';

// a slider with its name, tip and Reset
const Range = ({ label, tip, value, reset, onChange, ...props }) => <div>
	<FieldHead label={label} tip={tip} action={<Reset onClick={() => onChange(reset)} disabled={reset === value} />} />
	<RangeControl label={label} hideLabelFromVision value={value} onChange={val => onChange(val ?? reset)} __nextHasNoMarginBottom {...props} />
</div>;

const Style = ({ attributes, setAttributes }) => {
	const { width, customWidth, panelAlign, gap, padding, radius, shadow } = attributes;
	const set = key => val => setAttributes({ [key]: val });
	const color = key => ({ value: attributes[key], onChange: set(key) });
	const full = 'full' === width;

	return <>
		<Panel id='size' title={__('Size and position', 'softicon-mega-menu')}>
			<Tiles label={__('Width', 'softicon-mega-menu')} tip={__('Content and Wide follow your theme\'s layout widths. Full spans the whole screen. The panel never gets wider than the screen.', 'softicon-mega-menu')} value={width} options={widthOptions} onChange={set('width')} />

			{'custom' === width && <Range label={__('Custom width (px)', 'softicon-mega-menu')} value={customWidth} reset={800} onChange={set('customWidth')} min={200} max={2000} step={10} />}

			{!full && <Tiles label={__('Position', 'softicon-mega-menu')} tip={__('Center puts the panel in the middle of the screen. Left and Right line it up with the menu item.', 'softicon-mega-menu')} value={panelAlign} options={alignOptions} onChange={set('panelAlign')} />}

			<Range label={__('Space above (px)', 'softicon-mega-menu')} tip={__('Moves the panel down, for example to the bottom of a tall header.', 'softicon-mega-menu')} value={gap} reset={0} onChange={set('gap')} min={0} max={100} />
		</Panel>

		<Panel id='look' title={__('Panel style', 'softicon-mega-menu')}>
			<Range label={__('Padding (px)', 'softicon-mega-menu')} value={padding} reset={24} onChange={set('padding')} min={0} max={100} />

			{!full && <Range label={__('Rounded corners (px)', 'softicon-mega-menu')} value={radius} reset={8} onChange={set('radius')} min={0} max={60} />}

			<ToggleRow label={__('Shadow', 'softicon-mega-menu')} checked={shadow} onChange={set('shadow')} />

			<div className='siColors'>
				<ColorRow label={__('Background', 'softicon-mega-menu')} {...color('panelBg')} />
				<ColorRow label={__('Text', 'softicon-mega-menu')} {...color('panelColor')} />
			</div>
		</Panel>
	</>;
};

export default Style;
