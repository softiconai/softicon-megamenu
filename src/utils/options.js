import { __ } from '@wordpress/i18n';
import { icons } from './icons';
import { icons as shared } from '../vendor/softicon-tools/components/icons';

export const tabs = [
	{ name: 'general', title: __('General', 'softicon-mega-menu'), className: 'general' },
	{ name: 'style', title: __('Style', 'softicon-mega-menu'), className: 'style' }
];

export const openOptions = [
	{ label: __('Hover', 'softicon-mega-menu'), value: 'hover', icon: icons.hover },
	{ label: __('Click', 'softicon-mega-menu'), value: 'click', icon: icons.click }
];

export const mobileOptions = [
	{ label: __('Show panel', 'softicon-mega-menu'), value: 'accordion', icon: icons.accordion },
	{ label: __('Go to link', 'softicon-mega-menu'), value: 'link', icon: icons.link }
];

export const widthOptions = [
	{ label: __('Content', 'softicon-mega-menu'), value: 'content', icon: icons.content },
	{ label: __('Wide', 'softicon-mega-menu'), value: 'wide', icon: icons.wide },
	{ label: __('Full', 'softicon-mega-menu'), value: 'full', icon: icons.full },
	{ label: __('Custom', 'softicon-mega-menu'), value: 'custom', icon: icons.custom }
];

export const alignOptions = [
	{ label: __('Center', 'softicon-mega-menu'), value: 'center', icon: icons.center },
	{ label: __('Left', 'softicon-mega-menu'), value: 'left', icon: icons.left },
	{ label: __('Right', 'softicon-mega-menu'), value: 'right', icon: icons.right }
];

// the step-by-step video guide on YouTube (2:16)
export const guideUrl = 'https://www.youtube.com/watch?v=XEJpAu02Gew';

// the cards in the Help panel
export const helpCards = [
	{ href: guideUrl, label: __('Video guide', 'softicon-mega-menu'), icon: shared.video },
	{ href: 'https://wordpress.org/support/plugin/softicon-mega-menu/', label: __('Get support', 'softicon-mega-menu'), icon: shared.support },
	{ href: 'https://wordpress.org/support/plugin/softicon-mega-menu/reviews/#new-post', label: __('Rate Mega Menu', 'softicon-mega-menu'), icon: shared.star },
	{ href: 'https://wordpress.org/plugins/author/softiconai/', label: __('More plugins', 'softicon-mega-menu'), icon: shared.plugins }
];
