=== Zycus Landing Page ===
Contributors: zycus
Tags: landing-page, lead-generation, procurement, form, b2b
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Custom landing-page plugin for Zycus B2B procurement demo requests. Provides the [zycus_demo_form] and [zycus_sticky_cta] shortcodes, a wp_zycus_leads custom table, AJAX submission with full validation, admin + user email notifications, an admin lead viewer with CSV export, and GA4 event tracking.

== Installation ==
1. Upload the `zycus-landing` folder to `/wp-content/plugins/`.
2. Activate the plugin via the WordPress 'Plugins' menu (this creates the `wp_zycus_leads` table).
3. Go to Demo Requests > Settings to add your GA4 Measurement ID.
4. Use the `[zycus_demo_form]` shortcode on any page (or use the included Elementor template / page template).

== Shortcodes ==
[zycus_demo_form]   Renders the demo-request form with full client + server validation.
[zycus_sticky_cta]  Renders the sticky "Book Your Demo" bar that appears after the hero scrolls out.

== Changelog ==
= 1.0.0 =
* Initial release.
