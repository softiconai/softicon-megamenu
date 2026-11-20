# SoftIcon Mega Menu

Mega menus for the Navigation block in block themes. Each menu item opens a panel you design in the Site Editor with any blocks.

Requires WordPress 6.5+ · PHP 7.1+ · a block theme · GPLv3

**Videos:** [step-by-step guide (2:16)](https://www.youtube.com/watch?v=XEJpAu02Gew) · [quick tour (0:32)](https://www.youtube.com/watch?v=wESHej61hmw) · [WordPress.org](https://wordpress.org/plugins/softicon-mega-menu/)

## Features

- Mega Menu item for the Navigation block; panels are template parts in a "Mega Menu" area
- Create a panel from the block settings, edit it in the Site Editor, preview it in the editor
- Open on hover or click; content, wide, full or custom width; centered or lined up with the item
- Phone menu: the panel opens inside the list, or the item goes to its link
- Keyboard: Enter/Space opens, Escape and Tab out close; works without JavaScript on hover and focus

## Development

```bash
npm install
npm run start   # watch
npm run build   # production build
npm run zip     # build + .pot + softicon-mega-menu.zip for WordPress.org
```

The settings panels come from `../softicon-tools` (copied into `src/vendor/softicon-tools` before every build).
