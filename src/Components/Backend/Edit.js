import { __ } from '@wordpress/i18n';
import { useEffect, useRef, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { BlockControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import Settings from './Settings/Settings';
import place from '../../utils/place';
import { cssVars } from '../../utils/functions';
import { icons } from '../../utils/icons';

const arrow = <span className='almm_arrow' aria-hidden='true'>
	<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none' focusable='false'><path d='M1.5 4L6 8l4.5-4' stroke='currentColor' strokeWidth='1.5' /></svg>
</span>;

const Edit = (props) => {
	const { attributes, setAttributes, isSelected } = props;
	const { label, menuSlug, shadow, width, panelAlign } = attributes;
	const [preview, setPreview] = useState(false);
	const item = useRef();
	const panel = useRef();
	const theme = useSelect(select => select(coreStore).getCurrentTheme()?.stylesheet, []);

	// the panel shows while the item is selected and Preview is on
	const showPanel = preview && isSelected && !!menuSlug && !!theme;

	const blockProps = useBlockProps({
		ref: item,
		className: ['wp-block-navigation-item', 'almm_item', 'almm_js', shadow && 'almm_shadow', showPanel && 'is-open'].filter(Boolean).join(' '),
		style: cssVars(attributes)
	});

	useEffect(() => {
		if (!showPanel || !item.current || !panel.current) {
			return;
		}
		const update = () => place(item.current, panel.current, { width, align: panelAlign });
		update();
		const win = item.current.ownerDocument.defaultView;
		win.addEventListener('resize', update);
		return () => win.removeEventListener('resize', update);
	});

	return <>
		<Settings {...{ attributes, setAttributes }} />

		<BlockControls group='block'>
			<ToolbarButton icon={icons.panel} label={menuSlug ? __('Preview the panel', 'softicon-mega-menu') : __('Choose a panel in the block settings first', 'softicon-mega-menu')}
				isPressed={showPanel} disabled={!menuSlug} onClick={() => setPreview(!preview)} />
		</BlockControls>

		<div {...blockProps}>
			<span className='almm_head'>
				<span className='wp-block-navigation-item__content almm_toggle'>
					<RichText identifier='label' tagName='span' className='wp-block-navigation-item__label' value={label} onChange={val => setAttributes({ label: val })}
						placeholder={__('Mega menu', 'softicon-mega-menu')} aria-label={__('Menu item text', 'softicon-mega-menu')} allowedFormats={['core/bold', 'core/italic']} withoutInteractiveFormatting />
					{arrow}
				</span>
			</span>

			{showPanel && <div ref={panel} className='almm_panel almm_preview'>
				<ServerSideRender block='core/template-part' attributes={{ slug: menuSlug, theme }} urlQueryArgs={{ almm_preview: 1 }} />
			</div>}
		</div>
	</>;
};

export default Edit;
