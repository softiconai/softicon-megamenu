// Puts an open panel under its menu item: centered on the screen or lined up with the item,
// always inside the screen. Used on the page (view.js) and for the editor preview.
const GUTTER = 16;

export const clearPlace = panel => ['left', 'width', 'max-width', 'text-align'].forEach(prop => panel.style.removeProperty(prop));

// the phone menu lines its items up with align-items, the panel's text follows it
export const alignText = (item, panel) => {
	const align = { 'flex-end': 'end', end: 'end', right: 'end', center: 'center' }[getComputedStyle(item.parentElement).alignItems];
	if (align) {
		panel.style.textAlign = align;
	}
};

const place = (item, panel, { width, align }) => {
	const screen = item.ownerDocument.documentElement.clientWidth;
	const full = 'full' === width;
	const gutter = full ? 0 : GUTTER;

	panel.style.removeProperty('text-align');
	// 100vw counts the scrollbar, the page width does not
	panel.style.width = full ? `${screen}px` : '';
	panel.style.maxWidth = full ? 'none' : `${screen - gutter * 2}px`;
	panel.style.left = '0px';

	const box = item.getBoundingClientRect();
	const size = panel.offsetWidth;
	let left = box.left;
	if (full || 'center' === align) {
		left = (screen - size) / 2;
	} else if ('right' === align) {
		left = box.right - size;
	}
	left = Math.max(gutter, Math.min(left, screen - gutter - size));

	// the panel's left is measured from the menu item
	panel.style.left = `${Math.round(left - box.left)}px`;
};

export default place;
