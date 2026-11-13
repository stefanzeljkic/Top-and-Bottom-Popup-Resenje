=== Resenje Top and Bottom Popup ===
Contributors: stefanzeljkic
Donate link: https://buymeacoffee.com/stefanzeljkic
Tags: popup, top popup, bottom popup, announcement, notification bar
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Two independent popups, at the top and at the bottom of the page, with your own HTML content and separate style settings.

== Description ==

Resenje Top and Bottom Popup shows a small popup at the top and/or at the bottom of every front-end page. Use it for announcements, promotions, newsletter links or any short message.

Each popup has its own settings:

* Enable or disable
* Content (HTML allowed in posts, styled with inline `style` attributes)
* Alignment: left, center or right
* How long the popup stays hidden after a visitor closes it (in seconds, 0 = show on every page load)
* Width, height, padding and border radius
* Background color and text / close button color

On screens narrower than 768px the popups are centered and take 90% of the width.

The plugin loads its small CSS and JavaScript files only when at least one popup is enabled. It does not use cookies, does not contact external services and stores the "closed" time only in the visitor's browser (localStorage).

== Installation ==

1. Upload the `resenje-top-bottom-popup` folder to the `/wp-content/plugins/` directory, or install the plugin through the 'Plugins' screen in WordPress.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Open "Popup Resenje" in the admin menu, configure the top and/or bottom popup and save.

== Frequently Asked Questions ==

= Can I add my own HTML and CSS? =

Yes. The content box accepts the same HTML that is allowed in posts. Style it with inline `style` attributes, for example `<p style="font-size:18px;text-align:center">Free shipping this week!</p>`. `<script>` and `<style>` tags are removed for security.

= Which values can I use for width, height, padding and radius? =

Numbers with one of the units px, %, em, rem, vh or vw (for example `300px`, `80%`, `1.5rem`), or `0`. Height also accepts `auto`. Padding and radius accept up to four values separated by spaces (for example `10px 20px`). Invalid values are replaced by the default.

= The popup does not show again after I closed it =

That is intended: after closing, the popup stays hidden in that browser for the number of seconds set in "Show again after". Set it to 0 to show the popup on every page load.

== Screenshots ==

1. Admin panel for the top popup
2. Admin panel for the bottom popup
3. Popup displayed on the website

== Changelog ==

= 1.1.1 =
* Renamed to "Resenje Top and Bottom Popup" (slug resenje-top-bottom-popup). Settings are kept.

= 1.1.0 =
* CSS and JavaScript are loaded with wp_enqueue_style() / wp_enqueue_script(); per-popup styles use wp_add_inline_style().
* Settings use the Settings API (register_setting() with a sanitize callback); size values are validated.
* All output is escaped; direct file access is blocked.
* Main file renamed to match the plugin slug; added uninstall cleanup and a Settings link.
* Close button is a real button; works when localStorage is unavailable.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.1 =
New plugin name; no functional changes.

= 1.1.0 =
Security and standards update. Settings from 1.0 are read automatically; save them once to store them in the new format.
