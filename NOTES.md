# NOTES — Top and Bottom Popup - Resenje (routine status)

## 2026-11-08
- WordPress.org review (ID R top-and-bottom-popup-resenje/stefanzeljkic/20Jun25, last mail June 2025) was still open.
  The repo held an OLDER version than the last upload (the reviewed one had register_setting + ABSPATH).
- Rewrote the plugin as 1.1.0 to fix every review point:
  - CSS/JS via wp_enqueue_style/wp_enqueue_script + wp_add_inline_style/wp_add_inline_script (assets/css/popup.css, assets/js/popup.js; defer, footer).
  - One option `popup_resenje_settings` via register_setting() with sanitize_callback (wp_kses_post, whitelist, absint, hex colors, regex for CSS sizes).
  - Settings API form (options.php) → no more slashed HTML; all output escaped late; ABSPATH guard; uninstall.php; 1.0 options read as fallback.
  - Headers "Requires at least: 5.0" / "Requires PHP: 7.0" in main file = readme.txt; Tested up to 7.1.
  - Main file renamed to top-and-bottom-popup-resenje.php; zip built by build-zip.sh as dist/top-and-bottom-popup-resenje.zip (zip no longer in git).
- Tests (WordPress 7.1.4 + PHP 8.3 in Docker on CT 270, /opt/tbp): Plugin Check 0 errors (incl. experimental/low severity),
  PHPCS WordPress standard clean (php/js/css), PHPCompatibilityWP 7.0+ OK, php -l on PHP 7.0 OK, Gitleaks 0,
  functional: CSS injection in width rejected, <script>/<style> stripped, popup show/close/interval works in browser.
- Donation links (Patreon, Buy Me a Coffee, Ko-fi) checked: all working.

## Open
- 2026-11-08: SUBMITTED as a NEW submission (old one was closed). Automated scan: Pass. Slug top-and-bottom-popup-resenje, version 1.1.0, status Awaiting Review (~655 in queue, 1–10 days). Review mail goes to stefan.zeljkic@resenje.rs ("Review in Progress: Top and Bottom Popup – Resenje"). Possible objection: Guideline 18 (many popup plugins).
- 2026-11-08: pushed to main + GitHub release 1.1.0 (zip asset) on Stefan's ok. Next: wait for the WordPress.org review mail, fix whatever they ask, re-upload on the same page.
- Test stack on CT 270: `cd /opt/tbp && docker compose down -v` when no longer needed.

## 2026-11-13 — WordPress.org review round 2: name
- Status page showed "waiting on action from you"; Outlook (classic, COM) mail of 2026-11-12 "Review in Progress: Top and Bottom Popup – Resenje"
  (Review ID AUTOPREREVIEW TRM ...12Nov26): only issue = name too generic ("Top and Bottom Popup" first, distinctive term last).
- Renamed to "Resenje Top and Bottom Popup", slug/text domain/main file resenje-top-bottom-popup (AI suggestion from the mail), version 1.1.1.
  Option name popup_resenje_settings unchanged → settings kept.
- Tests (CT 270, /opt/rsm site A): install + activate 1.1.1, existing settings shown on the front end, Plugin Check no errors, PHPCS (WPCS 3 +
  PHPCompatibilityWP 7.0+) clean. Local commit only.
- Next (needs Stefan's ok): upload dist/resenje-top-bottom-popup.zip on the "Add your plugin" page, reply to the 2026-11-12 mail requesting
  the new slug "resenje-top-bottom-popup", push + GitHub release 1.1.1 (repo name may stay).
