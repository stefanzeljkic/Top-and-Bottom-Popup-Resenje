# Top and Bottom Popup - Rešenje 🎯

A lightweight and flexible WordPress plugin for displaying **custom popups** at the **top** and **bottom** of your site. Each popup can have its own content, position, styles, and display timing.

> Perfect for announcements, promotional messages, newsletter signups, or custom HTML content.

---

## ✨ Features

✅ Show popups at **top** or **bottom** of the page  
✅ Choose popup alignment: **left**, **center**, or **right**  
✅ Custom **HTML** content in each popup (HTML allowed in posts, styled with inline `style` attributes)  
✅ "Show again after" interval in seconds (0 = on every page load)  
✅ Customize width, height, padding, background, text color, border radius  
✅ Responsive design — centered at 90% width on mobile  
✅ Supports two **independent popups** (top + bottom) with separate settings  
✅ Lightweight: CSS/JS are loaded only when a popup is enabled; no cookies, no external requests  
✅ Simple admin interface built on the WordPress Settings API  

---

## ⚙️ How to Use

1. Install the plugin (upload `top-and-bottom-popup-resenje.zip` under Plugins → Add New → Upload) and activate it.
2. Go to `Popup Resenje` in the left admin menu.
3. Configure the **Top Popup** and/or **Bottom Popup**:
   - Enable/Disable
   - Add your HTML content (`<script>` and `<style>` tags are removed for security; use inline `style` attributes)
   - Set width, height, padding, radius (units px, %, em, rem, vh, vw; height also `auto`)
   - Pick background and text / close button colors and the alignment
   - Define how long the popup stays hidden after a visitor closes it
4. Save settings. You're done!

Popups will appear automatically on the front-end based on your configuration.

---

## 🧪 Tested On

- WordPress 7.1 ✅ (requires 5.0+)  
- PHP 7.0+ (tested on 8.3) ✅  
- Plugin Check and PHPCS (WordPress Coding Standards) — no issues ✅  
- Desktop and Mobile browsers ✅  

---

## 🛠 Development

Contributions, bug reports, and feature suggestions are welcome via GitHub issues or pull requests.

Build the installable zip with `bash build-zip.sh` → `dist/top-and-bottom-popup-resenje.zip`.

---
## 📄 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).  
You are free to use, modify, and distribute it.

---

## ☕ Support Development

If you like this plugin, consider supporting future updates ❤️

- [Buy Me a Coffee ☕](https://buymeacoffee.com/stefanzeljkic)
- [Patreon 💸](https://www.patreon.com/user?u=76138587)
- [Ko-fi ❤️](https://ko-fi.com/stefanzeljkic)

---

## 🧑‍💻 Author

**Stefan Zeljkic**  
📧 stefanzeljkic@yahoo.com 


