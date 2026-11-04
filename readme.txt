=== SoftIcon Mega Menu ===
Contributors: softiconai
Tags: mega menu, menu, navigation, block, block theme
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.1
Stable tag: 1.1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add mega menus to the Navigation block in block themes. Design each dropdown panel in the Site Editor with any blocks.

== Description ==

**SoftIcon Mega Menu** adds a **Mega Menu** item to the Navigation block of block themes like Twenty Twenty-Five. Each item opens a large panel that you design in the Site Editor with the blocks you already know: columns of links, images, buttons, even a product grid.

Classic mega menu plugins work with the old Appearance → Menus screen, which block themes do not use. This plugin is made for the Navigation block.

= How it works =

1. Go to **Appearance → Mega Menus** and click **Create panel**. A new panel starts with three columns of links. Design it with any blocks.
2. Click **Open header editor**, select the Navigation block, click its **+** and add a **Mega Menu** item.
3. Type the item's text, choose the panel in the block settings and save.

You can also create a panel right from the block settings.

Already have a menu? Select a link in it, click the first icon in the toolbar and choose **Transform to → Mega Menu**. The link keeps its text and address.

Panels are template parts in their own **Mega Menu** area, so you can reuse one panel in several menus.

= Video guides =

How to create a mega menu, step by step (2:28):

https://www.youtube.com/watch?v=X_APEz4RrdM

A quick tour (0:32):

https://www.youtube.com/watch?v=dujCbVTPBlQ

You can also watch them from **Appearance → Mega Menus**.

= Features =

* Mega Menu item for the Navigation block
* Panels designed in the Site Editor with any blocks
* Appearance → Mega Menus: all panels in one list, create a panel in one click, a short how-to
* Turn any menu link into a mega menu (and back) with Transform, keeping its text and link
* Open on hover or on click
* Panel width: content, wide, full screen or a custom width
* Panel position: centered on the screen, or lined up with the menu item
* Panel always stays inside the screen
* Space above the panel, padding, rounded corners, shadow, background and text colors
* Optional link on the menu item text, with a small arrow that opens the panel
* Phones: the panel opens inside the Navigation block's phone menu, or the item just goes to its link
* Keyboard friendly: Enter or Space opens a panel, Escape closes it, Tab out closes it
* Preview the panel in the editor
* Works without JavaScript too: the panel opens on hover and keyboard focus
* Small script with no dependencies, about 2 KB
* Video guides on the Appearance → Mega Menus page

= Source code =

The JavaScript and CSS in the `build` folder are compiled from the human-readable source in the `src` folder, which is included in the plugin. To build it yourself, run `npm install` and then `npm run build` in the plugin folder. The build uses [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts).

= External services =

The Appearance → Mega Menus page has two video guides hosted on YouTube. Nothing is loaded from YouTube until you click a video. Then the video plays from youtube-nocookie.com, which receives your IP address and browser details like any website you visit. Visitors of your site never load anything from YouTube because of this plugin. YouTube [Terms of Service](https://www.youtube.com/t/terms) and Google [Privacy Policy](https://policies.google.com/privacy).

= Libraries =

The plugin does not bundle any third-party library. Its scripts use only the packages that come with WordPress (`@wordpress/blocks`, `@wordpress/block-editor`, `@wordpress/components` and others), loaded from WordPress itself. The settings panel helpers in `src/vendor/softicon-tools` are our own code, released under the same GPLv3 or later license as the plugin. All icons were drawn for this plugin.

== Installation ==

1. Upload the plugin and activate it, or install it from Plugins → Add New.
2. Use a block theme (Appearance → Editor).
3. Edit your header, select the Navigation block and add a **Mega Menu** item.

== Frequently Asked Questions ==

= Does it work with classic themes? =

No. Panels are template parts, which need a block theme. Classic themes can use a classic menu plugin.

= Where do I find my panels? =

**Appearance → Mega Menus** lists them all with an Edit button. They are also in Appearance → Editor → Patterns → **Mega Menu**, and the block settings link to the chosen panel.

= Where is Appearance → Menus? =

Block themes have no Menus screen; menus are made with the Navigation block in Appearance → Editor. Mega Menu items go inside that Navigation block.

= The panel opens too high, on top of my header =

The panel opens right under the menu item. If your header is taller, use **Space above** in the Style tab to move it down.

= What happens on phones? =

When the Navigation block turns into a phone menu, the panel opens under its item inside that menu. With a link on the item you can choose **Go to link** instead, then the item is a normal link on phones.

= I switched themes and my panels are gone =

Template parts belong to a theme. Switch back to see them, or copy the blocks into new panels in the new theme.

= Need help? =

Ask in the [support forum](https://wordpress.org/support/plugin/softicon-mega-menu/) or email softiconai@gmail.com. Feature ideas are welcome.

== Screenshots ==

1. A mega menu panel under the menu item
2. Panel settings in the block editor
3. Width, position and style settings
4. The panel inside the phone menu

== Changelog ==

= 1.1.1 =
* Changed: "Create panel" on the Appearance → Mega Menus page no longer redirects on the server. The page creates the panel and opens it in the Site Editor, or shows a link to it when JavaScript is off.
* Changed: the Mega Menus page script is now a file (includes/admin.js) instead of inline code.
* Readme: added the "block" tag.

= 1.1.0 =
* New: video guides on the Appearance → Mega Menus page, a step-by-step guide and a quick tour. A video loads from YouTube only when you click it.
* New: "Video guide" card in the Help panel of the block settings.
* Readme: Video guides and External services sections.

= 1.0.0 =
* First release.
