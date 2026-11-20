const widths = {
	content: 'var(--wp--style--global--content-size, 800px)',
	wide: 'var(--wp--style--global--wide-size, 1200px)',
	full: '100vw'
};

// the same css variables render.php puts on the menu item (includes/block.php style())
export const cssVars = attributes => {
	const { width, customWidth, gap, padding, radius, panelBg, panelColor } = attributes;
	const vars = {
		'--almm-width': widths[width] || `${customWidth}px`,
		'--almm-gap': `${gap}px`,
		'--almm-pad': `${padding}px`,
		'--almm-radius': 'full' === width ? '0px' : `${radius}px`
	};
	if (panelBg) {
		vars['--almm-bg'] = panelBg;
	}
	if (panelColor) {
		vars['--almm-color'] = panelColor;
	}
	return vars;
};

// a slug for a new panel that no other template part uses
export const uniqueSlug = (base, taken) => {
	const slug = base || 'mega-menu';
	let next = slug;
	for (let i = 2; taken.includes(next); i++) {
		next = `${slug}-${i}`;
	}
	return next;
};
