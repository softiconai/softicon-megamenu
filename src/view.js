import place, { alignText, clearPlace } from './utils/place';

const items = [];
// a mouse or trackpad; touch screens open the panel with a tap
const mouse = window.matchMedia('(hover: hover) and (pointer: fine)');

// the Navigation block's phone menu (overlay) shows panels inside the list instead
const inOverlay = el => !!el.closest('.wp-block-navigation__responsive-container.is-menu-open');

const setup = el => {
	const toggle = el.querySelector('.almm_toggle');
	const panel = el.querySelector('.almm_panel');
	if (!toggle || !panel) {
		return;
	}

	let config = {};
	try {
		config = JSON.parse(el.dataset.almm || '{}');
	} catch (e) {
		// keeps the defaults
	}

	let timer;
	const hover = () => 'click' !== config.openOn && mouse.matches && !inOverlay(el);

	const item = {
		el,
		open: false,
		position() {
			if (inOverlay(el)) {
				clearPlace(panel);
				alignText(el, panel);
			} else {
				place(el, panel, config);
			}
		},
		set(open) {
			clearTimeout(timer);
			if (open === item.open) {
				return;
			}
			if (open) {
				items.forEach(other => other !== item && other.set(false));
			}
			item.open = open;
			el.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			// measured once it is shown, before the browser paints it
			if (open) {
				item.position();
			}
		}
	};

	el.addEventListener('mouseenter', () => hover() && item.set(true));
	el.addEventListener('mouseleave', () => {
		if (hover()) {
			// time to cross the gap between the item and the panel
			clearTimeout(timer);
			timer = setTimeout(() => item.set(false), 250);
		}
	});

	toggle.addEventListener('click', e => {
		// a mouse click on an item that opened on hover keeps it open; Enter and Space (detail 0) toggle
		item.set(hover() && e.detail > 0 ? true : !item.open);
	});

	el.addEventListener('keydown', e => {
		if ('Escape' === e.key && item.open) {
			e.stopPropagation();
			item.set(false);
			toggle.focus();
		}
	});

	// Tab out of the item closes it
	el.addEventListener('focusout', e => {
		if (e.relatedTarget && !el.contains(e.relatedTarget)) {
			item.set(false);
		}
	});

	el.classList.add('almm_js');
	items.push(item);
};

const boot = () => {
	document.querySelectorAll('.almm_item[data-almm]').forEach(setup);
	if (!items.length) {
		return;
	}

	document.addEventListener('click', e => {
		items.forEach(item => item.open && !item.el.contains(e.target) && item.set(false));
	});

	let width = window.innerWidth;
	window.addEventListener('resize', () => {
		// phones fire resize while scrolling (the address bar), only a real width change matters
		if (width === window.innerWidth) {
			return;
		}
		width = window.innerWidth;
		items.forEach(item => item.open && item.position());
	});
};

if ('loading' === document.readyState) {
	document.addEventListener('DOMContentLoaded', boot);
} else {
	boot();
}
