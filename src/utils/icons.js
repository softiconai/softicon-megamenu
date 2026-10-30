import { icon } from '../vendor/softicon-tools/components/icons';

// a menu bar with a panel of links under it
export const blockIcon = icon('M3 4h18v3H3zm0 5h18v11H3zm1.5 1.5v8h15v-8zM6 12h4v1.4H6zm0 2.8h4v1.4H6zm7-2.8h4v1.4h-4zm0 2.8h4v1.4h-4z', true);

// a frame (the screen) with the panel inside
const screen = inner => icon('M3 4h18v16H3zm1.5 1.5v13h15v-13z' + inner, true);

export const icons = {
	panel: icon('M4 4h16v3H4zm0 5h16v11H4zm1.5 1.5v8h13v-8z', true),

	hover: icon('M7 3.5l11 8.2-5 .9 2.9 6.2-2.2 1-2.9-6.2L7 17.3z'),
	click: icon('M12 4a8 8 0 1 1 0 16 8 8 0 0 1 0-16m0 1.6a6.4 6.4 0 1 0 0 12.8 6.4 6.4 0 0 0 0-12.8M12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6', true),

	accordion: icon('M4 5h16v1.6H4zm0 4h16v1.6H4zm4 4h12v1.6H8zm0 4h12v1.6H8z'),
	link: icon('M4 11.2h12.2l-4-4 1.1-1.1 5.9 5.9-5.9 5.9-1.1-1.1 4-4H4z'),

	content: screen('M9 7.5h6v9H9z'),
	wide: screen('M6.5 7.5h11v9h-11z'),
	full: screen('M4.5 7.5h15v9h-15z'),
	custom: icon('M4 6.2h9v1.6H4zm12 0h4v1.6h-4zM14 4.5h1.6v5H14zM4 16.2h4v1.6H4zm7 0h9v1.6h-9zM9 14.5h1.6v5H9z'),

	// the menu item on top, the panel under it
	center: icon('M9.5 3.5h5v3h-5zM4 8.5h16v12H4zm1.5 1.5v9h13v-9z', true),
	left: icon('M4 3.5h5v3H4zm0 5h12v12H4zm1.5 1.5v9h9v-9z', true),
	right: icon('M15 3.5h5v3h-5zM8 8.5h12v12H8zm1.5 1.5v9h9v-9z', true)
};
