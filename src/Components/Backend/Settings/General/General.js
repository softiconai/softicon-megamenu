import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { resolveSelect, useDispatch, useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { store as noticesStore } from '@wordpress/notices';
import { URLInput } from '@wordpress/block-editor';
import { Button, ExternalLink, Notice, SelectControl, Spinner, TextControl } from '@wordpress/components';
import { addQueryArgs, cleanForSlug } from '@wordpress/url';
import { decodeEntities } from '@wordpress/html-entities';
import { FieldHead, HelpCards, Panel, Tiles } from '../../../../vendor/softicon-tools/components/Panels';
import { guideUrl, helpCards, mobileOptions, openOptions } from '../../../../utils/options';
import { uniqueSlug } from '../../../../utils/functions';

const PARTS = ['postType', 'wp_template_part', { per_page: -1 }];
// the ready-made designs are block patterns in this category (includes/patterns.php)
const CATEGORY = 'softicon-mega-menu';

// a ready-made design's blocks, waiting for the patterns when they haven't loaded yet
const designContent = async name => (await resolveSelect(coreStore).getBlockPatterns?.())?.find(pattern => pattern.name === name)?.content || '';

// choose, create and edit the template part shown in the panel
const PanelPicker = ({ menuSlug, setAttributes }) => {
	const [name, setName] = useState('');
	const [design, setDesign] = useState(`${CATEGORY}/links`);
	const [busy, setBusy] = useState(false);
	const { saveEntityRecord } = useDispatch(coreStore);
	const { createErrorNotice } = useDispatch(noticesStore);

	const { parts, resolved, theme, patterns } = useSelect(select => {
		const core = select(coreStore);
		return {
			parts: core.getEntityRecords(...PARTS),
			resolved: core.hasFinishedResolution('getEntityRecords', PARTS),
			theme: core.getCurrentTheme(),
			patterns: core.getBlockPatterns?.()
		};
	}, []);
	const designs = (patterns || []).filter(pattern => pattern.categories?.includes(CATEGORY)).map(pattern => ({ value: pattern.name, label: pattern.title }));

	if (theme && false === theme.is_block_theme) {
		return <Notice status='warning' isDismissible={false}>
			{__('Mega menu panels are template parts, which need a block theme such as Twenty Twenty-Five.', 'softicon-mega-menu')}
		</Notice>;
	}
	if (!resolved) {
		return <Spinner />;
	}

	const menus = (parts || []).filter(part => 'menu' === part.area);
	const options = [
		{ label: __('Choose a panel', 'softicon-mega-menu'), value: '' },
		...menus.map(part => ({ label: decodeEntities(part.title?.rendered || part.slug), value: part.slug }))
	];
	// a panel from another theme or area stays visible so it is not lost by mistake
	if (menuSlug && !menus.some(part => part.slug === menuSlug)) {
		options.push({ label: `${menuSlug} (${__('not found', 'softicon-mega-menu')})`, value: menuSlug });
	}

	const create = async () => {
		const title = name.trim() || __('Mega menu', 'softicon-mega-menu');
		const slug = uniqueSlug(cleanForSlug(title), (parts || []).map(part => part.slug));
		setBusy(true);
		try {
			const content = await designContent(design);
			const record = await saveEntityRecord('postType', 'wp_template_part', { slug, title, area: 'menu', content }, { throwOnError: true });
			setAttributes({ menuSlug: record?.slug || slug });
			setName('');
		} catch (e) {
			createErrorNotice(e?.message || __('The panel could not be created.', 'softicon-mega-menu'), { type: 'snackbar' });
		}
		setBusy(false);
	};

	const editUrl = menuSlug && theme ? addQueryArgs('site-editor.php', { postType: 'wp_template_part', postId: `${theme.stylesheet}//${menuSlug}`, canvas: 'edit' }) : '';

	return <>
		<div>
			<FieldHead label={__('Panel', 'softicon-mega-menu')} tip={__('The panel is a template part. Design it in the Site Editor with any blocks: columns, images, buttons, even another menu.', 'softicon-mega-menu')} />
			<SelectControl label={__('Panel', 'softicon-mega-menu')} hideLabelFromVision value={menuSlug} options={options} onChange={val => setAttributes({ menuSlug: val })} __nextHasNoMarginBottom __next40pxDefaultSize />
			{editUrl && <p className='almmHelp'><ExternalLink href={editUrl}>{__('Edit this panel in the Site Editor', 'softicon-mega-menu')}</ExternalLink></p>}
			{!menuSlug && <p className='almmHelp'>{__('New here?', 'softicon-mega-menu')} <ExternalLink href={guideUrl}>{__('Watch the 2-minute guide', 'softicon-mega-menu')}</ExternalLink></p>}
		</div>

		<div className='almmCreate'>
			<FieldHead label={__('New panel', 'softicon-mega-menu')} tip={__('Starts with a ready-made design. Change its text, links and images in the Site Editor.', 'softicon-mega-menu')} />
			<TextControl label={__('New panel name', 'softicon-mega-menu')} hideLabelFromVision placeholder={__('For example: Shop menu', 'softicon-mega-menu')} value={name} onChange={setName} __nextHasNoMarginBottom __next40pxDefaultSize />
			{designs.length > 1 && <SelectControl label={__('Design', 'softicon-mega-menu')} value={design} options={designs} onChange={setDesign} __nextHasNoMarginBottom __next40pxDefaultSize />}
			<Button variant='secondary' onClick={create} isBusy={busy} disabled={busy}>{__('Create panel', 'softicon-mega-menu')}</Button>
		</div>

		<p className='almmHelp'>{__('To rename or delete a panel, go to Appearance → Editor → Patterns → Mega Menu.', 'softicon-mega-menu')}</p>
	</>;
};

const General = ({ attributes, setAttributes }) => {
	const { url, menuSlug, openOn, mobile } = attributes;
	const set = key => val => setAttributes({ [key]: val });

	return <>
		<Panel id='help' title={__('Help', 'softicon-mega-menu')}>
			<ol className='almmSteps'>
				<li>{__('Under Panel, pick a design and click "Create panel".', 'softicon-mega-menu')}</li>
				<li>{__('Click "Edit this panel in the Site Editor". Change its text, links and images, or design it with any blocks.', 'softicon-mega-menu')}</li>
				<li>{__('Back here, type the menu item\'s text and save.', 'softicon-mega-menu')}</li>
			</ol>
			<HelpCards cards={helpCards} />
			<p className='almmHelp'>{__('Email:', 'softicon-mega-menu')} <a href='mailto:softiconai@gmail.com'>softiconai@gmail.com</a></p>
		</Panel>

		<Panel id='panel' title={__('Panel', 'softicon-mega-menu')}>
			<PanelPicker {...{ menuSlug, setAttributes }} />
		</Panel>

		<Panel id='link' title={__('Link', 'softicon-mega-menu')}>
			<div>
				<FieldHead label={__('Menu item link (optional)', 'softicon-mega-menu')} tip={__('With a link the text opens that page and a small arrow opens the panel. Without one the whole item opens the panel.', 'softicon-mega-menu')} />
				<URLInput label={__('Menu item link', 'softicon-mega-menu')} hideLabelFromVision className='almmUrl' value={url} onChange={set('url')} __nextHasNoMarginBottom />
			</div>
		</Panel>

		<Panel id='behavior' title={__('Behavior', 'softicon-mega-menu')}>
			<Tiles label={__('Open on', 'softicon-mega-menu')} tip={__('Hover opens the panel when the mouse is over the item. Touch screens and keyboards always open it with a tap or Enter.', 'softicon-mega-menu')} value={openOn} options={openOptions} onChange={set('openOn')} />

			{url ? <Tiles label={__('On phones', 'softicon-mega-menu')} tip={__('Inside the Navigation block\'s phone menu, show the panel under the item or just go to the link.', 'softicon-mega-menu')} value={mobile} options={mobileOptions} onChange={set('mobile')} />
				: <p className='almmHelp'>{__('On phones the panel opens under the item inside the menu.', 'softicon-mega-menu')}</p>}
		</Panel>
	</>;
};

export default General;
