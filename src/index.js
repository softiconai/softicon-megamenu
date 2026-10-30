import { createBlock, registerBlockType } from '@wordpress/blocks';
import { addFilter } from '@wordpress/hooks';
import metadata from './block.json';
import './style.scss';
import './editor.scss';
import Edit from './Components/Backend/Edit';
import { blockIcon } from './utils/icons';

// the Navigation block accepts only the blocks in its list, so the mega menu joins it
addFilter('blocks.registerBlockType', 'softicon-mega-menu/navigation', (settings, name) => {
	if ('core/navigation' !== name || !Array.isArray(settings.allowedBlocks) || settings.allowedBlocks.includes(metadata.name)) {
		return settings;
	}
	return { ...settings, allowedBlocks: [...settings.allowedBlocks, metadata.name] };
});

// a link's label is rich text (an object since WordPress 6.5), the mega menu keeps it as HTML
const html = value => ('string' === typeof value ? value : value?.toHTMLString?.() ?? String(value ?? ''));

registerBlockType(metadata, {
	icon: blockIcon,
	edit: Edit,
	// a menu link becomes a mega menu and back, keeping its text and link
	transforms: {
		from: [{
			type: 'block',
			blocks: ['core/navigation-link'],
			transform: ({ label, url }) => createBlock(metadata.name, { label: html(label), url: url || '' })
		}],
		to: [{
			type: 'block',
			blocks: ['core/navigation-link'],
			transform: ({ label, url }) => createBlock('core/navigation-link', { label, url, kind: 'custom' })
		}]
	},
	// Dynamic block, the markup comes from render.php.
	save: () => null
});
