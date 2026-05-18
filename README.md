# Zycus B2B Procurement Landing Page

WordPress + Elementor implementation of the Zycus landing-page PRD (v1.0).
Single-page, 5-section experience (Hero, Benefits, Social Proof, Demo Form, Sticky CTA) with a full back-end stack: custom leads table, AJAX form handler, admin + user email notifications, admin lead viewer with CSV export, and GA4 event tracking.

## What's in this repo

```
zycus 2/
├── zycus-landing/                  WordPress plugin (drop in wp-content/plugins/)
│   ├── zycus-landing.php           Bootstraps the plugin, enqueues assets
│   ├── includes/                   DB, AJAX handler, emails, admin, shortcodes, GA4
│   ├── templates/                  Branded HTML emails (admin + user)
│   └── assets/
│       ├── css/zycus-landing.css   Full design system per PRD §6
│       └── js/zycus-landing.js     Validation, counters, smooth-scroll, sticky CTA
├── theme-template/
│   └── page-zycus-demo.php         Single-page WP template (copy to your theme)
└── elementor/
    └── zycus-landing-template.json Importable Elementor page template
```

## Setup (recommended: WordPress + Elementor)

**Prerequisites:** WordPress 6.0+, PHP 7.4+, Elementor (free) installed and active.

1. **Install the plugin**
   - Zip the `zycus-landing/` folder and upload via *Plugins → Add New → Upload Plugin*, or copy the folder directly to `wp-content/plugins/`.
   - Activate **Zycus Landing Page**. The `wp_zycus_leads` table is created on activation.

2. **Configure GA4** (optional)
   - *Demo Requests → Settings* → paste your GA4 Measurement ID (`G-XXXXXXXXXX`).
   - Save. The `gtag.js` snippet is injected sitewide; all PRD §8 events will fire automatically.

3. **Build the page — pick one path**

   **Path A — Elementor template (visual editing)**
   - Create a new page: *Pages → Add New* → title "Procurement Demo" → set Permalink to `/procurement-demo`.
   - In the Page Attributes box, set Template to **Elementor Canvas** (no header/footer).
   - Click **Edit with Elementor**.
   - In the Elementor editor: hamburger menu (top-left) → *Templates → Import Templates* → upload `elementor/zycus-landing-template.json`.
   - Insert the imported template into the page.
   - Replace `{{REPLACE_WITH_PRODUCT_SCREENSHOT_URL}}` in the Hero image widget with a real product screenshot URL.
   - Publish.

   **Path B — Page template (code, no Elementor)**
   - Copy `theme-template/page-zycus-demo.php` into your active theme (or child theme) root.
   - In the WP admin: *Pages → Add New* → title "Procurement Demo" → set Page Attributes → Template = **Zycus Demo Landing** → Publish.

4. **Verify the form**
   - Submit a test request. Confirm:
     - Inline validation works (try a Gmail address → should be rejected).
     - Success message appears.
     - Admin email lands in the site admin's inbox.
     - User confirmation email lands in the submitted email's inbox.
     - Row appears under *Demo Requests* in the admin sidebar.

## Form fields & validation (PRD §4.4 / §7.2)

| Field      | Type      | Rules                                                                 |
|------------|-----------|-----------------------------------------------------------------------|
| Full Name  | text      | required, 2–50 chars, `/^[a-zA-Z\s\-']+$/`                            |
| Work Email | email     | required, RFC-shaped, rejects gmail/yahoo/outlook/hotmail/aol         |
| Company    | text      | required, 2–100 chars                                                 |
| Job Title  | select    | required, must match one of the 7 PRD options                         |
| Country    | select    | required, must match one of the 35 listed countries                   |
| Message    | textarea  | optional, ≤ 500 chars (live counter)                                  |

Server-side: nonce check (CSRF), honeypot field, IP-based rate limit (5/hour), full re-validation, sanitization via `sanitize_text_field` / `sanitize_email` / `sanitize_textarea_field`.

## Database schema (PRD §5.3)

Table `{prefix}_zycus_leads`:

| Column      | Type                  | Notes                                |
|-------------|-----------------------|--------------------------------------|
| id          | BIGINT UNSIGNED, PK   | Auto-increment                       |
| full_name   | VARCHAR(255), NOT NULL|                                      |
| email       | VARCHAR(255), NOT NULL| Indexed                              |
| company     | VARCHAR(255), NOT NULL|                                      |
| job_title   | VARCHAR(255), NOT NULL| Indexed                              |
| country     | VARCHAR(100), NOT NULL|                                      |
| message     | LONGTEXT, NULL        |                                      |
| source      | VARCHAR(50)           | Defaults to `landing_page`           |
| user_agent  | VARCHAR(500), NULL    |                                      |
| ip_address  | VARCHAR(45), NULL     | IPv4 or IPv6                         |
| created_at  | TIMESTAMP             | Indexed                              |
| updated_at  | TIMESTAMP             | Auto-updates on row change           |

## GA4 events emitted (PRD §8)

| Event              | Parameters                                                   |
|--------------------|--------------------------------------------------------------|
| `cta_click`        | `button_location`, `button_text`, `section`                  |
| `form_field_focus` | `field_name`, `section`                                      |
| `scroll_to_form`   | `section`, `scroll_depth`                                    |
| `form_submit`      | `form_type`, `job_title`, `country`                          |
| `form_error`       | `error_type` (validation_failed / submission_failed / network)|

`pageview` is sent automatically by `gtag('config', ...)`.

## Admin: viewing & exporting leads

*Demo Requests* (sidebar) — sortable table of every submission.
*Demo Requests → Export CSV* — downloads `zycus-leads-YYYY-MM-DD.csv` with every column.

## Browser & accessibility (PRD §5.1)

- Mobile-first responsive layout (breakpoints at 991px and 640px).
- WCAG 2.1 AA contrast on all text colors (Navy `#003366` on `#FFFFFF` = 10.4:1).
- Form labels associated to inputs, errors linked via `aria-describedby`, required fields marked.
- Smooth keyboard navigation; visible focus rings (teal outline + 3px shadow).
- Counter animation respects `IntersectionObserver` availability (falls back to static numbers).

## Customisation hooks

- **Job titles / countries** — edit `Zycus_Shortcode::job_titles()` and `Zycus_Shortcode::countries()` in `includes/class-zycus-shortcode.php`. The validator uses the same arrays, so client and server stay in sync.
- **Personal email blocklist** — edit `Zycus_Form_Handler::PERSONAL_DOMAINS`.
- **Email templates** — `templates/email-admin.php` and `templates/email-user.php` (branded HTML, inline styles for client compatibility).
- **Rate limit** — `Zycus_Form_Handler::RATE_LIMIT` (default 5) and `RATE_WINDOW` (default 3600 seconds).

## PRD coverage checklist

- [x] §4.1 Hero — headline, sub, 4 bullets, primary + secondary CTA, product screenshot slot
- [x] §4.2 Benefits — 3 numbered cards, hover lift, responsive grid
- [x] §4.3 Social Proof — 10 logos (grayscale → colour on hover), animated metrics, testimonial card
- [x] §4.4 Demo Form — 5 required + 1 optional, all PRD validation rules, AJAX submit, success/error states
- [x] §4.5 Sticky CTA — appears after hero scrolls past, dismiss persisted per session
- [x] §5 Technical — semantic HTML5, vanilla JS (no jQuery), CSS variables, mobile-first, accessibility
- [x] §5.2 Backend — nonce, sanitization, server-side re-validation, rate limit, honeypot, error logging
- [x] §5.3 Database — `wp_zycus_leads` with indexes on email / created_at / job_title
- [x] §6 Design — Navy #003366 / Teal #00A8A8 / 8px grid / Segoe UI stack
- [x] §7 Functional — all 4 user workflows supported, full validation rules implemented
- [x] §8 Analytics — every event from PRD §8.1 wired up
- [x] §9 Content — exact copy from PRD §9.1–§9.4

## Notes

- The Elementor JSON uses **Flexbox Containers** (`elType: "container"`, `e-con`) — the modern Elementor layout primitive used by zycus.com itself, not the legacy `section` / `column` model. Requires Elementor 3.6+ with Flexbox Containers enabled (Elementor → Settings → Features → Flexbox Container = Active).
- The Elementor JSON is a starter — import it, then tweak typography / spacing visually if needed. The page template (`page-zycus-demo.php`) is the canonical, pixel-accurate version.
- Production: configure SMTP (WP Mail SMTP, Postmark, SendGrid, etc.) before launch — the default PHP `mail()` is unreliable for transactional delivery.
- Privacy/Terms links in the form footer are placeholders (`href="#"`) — point them at real URLs before going live.
