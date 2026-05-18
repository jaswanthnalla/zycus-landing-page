# Zycus B2B Procurement Landing Page — Deliverables

**Submitted by:** Jessu Nalla &middot; **Stack:** WordPress + Elementor (Flexbox Containers) + Gravity-style custom plugin
**Build date:** 2026-05-15 &middot; **PRD:** v1.0 (May 2024)

---

## 1. Live URL

🟢 **https://zycus-landing-preview.vercel.app**
(static preview of the front-end — same CSS/JS/markup that ships in the WordPress plugin; form submit is stubbed until installed on a WP host)

**Deploy:** Vercel production · auto-promoted · security headers via `vercel.json`
**Inspector:** https://vercel.com/jassunalla-3342s-projects/zycus-landing-preview

> Screen recording: not produced from the toolchain. To record a 3–5 min walkthrough, open the live URL with Loom / OBS / Windows `Win+G` Game Bar and narrate: hero → benefits → social proof → form (show validation: try a `gmail.com` address, then a work email) → sticky CTA → mobile resize.

## 2. Screenshots

In `deliverables/screenshots/`:

| File | Viewport | Notes |
|---|---|---|
| `desktop-1440x900.png` | 1440 × 900 | Hero + benefits above the fold |
| `desktop-fullpage.png` | 1440 × full | Entire page top → bottom |
| `tablet-768x1024.png` | iPad | 2-col benefit grid, single-col form |
| `mobile-375x812.png` | iPhone 13 mini | Hero, single-column |
| `mobile-fullpage.png` | iPhone 13 mini × full | Whole responsive flow |

Captured via Puppeteer with device emulation (`isMobile`, `deviceScaleFactor`, mobile UA) against the live Vercel deployment.

## 3. WordPress export & source files

`deliverables/zycus-landing-plugin.zip` — drop-in WordPress plugin. Install via **Plugins → Add New → Upload Plugin → Activate**. Creates `wp_zycus_leads` table on activation, registers shortcodes `[zycus_demo_form]` and `[zycus_sticky_cta]`, exposes "Demo Requests" admin menu with CSV export.

Other source folders (in the repo root):
- `zycus-landing/` — the plugin sources (PHP / CSS / JS / email templates)
- `elementor/zycus-landing-template.json` — importable Elementor template using **Flexbox Containers** (the modern `e-con` schema zycus.com itself uses); 22-node nested tree, real `heading` / `text-editor` / `icon-list` / `button` / `divider` / `image` / `html` / `shortcode` widgets
- `theme-template/page-zycus-demo.php` — drop-in page template for any WP theme; canonical, pixel-accurate render
- `preview/` — the standalone static demo (what's on Vercel)
- `README.md` — full setup guide for both Elementor and template-only paths

---

# One-page note

## Page structure decisions

Single-page funnel, 5 sections aligned to PRD §4. **Hero** sells the value prop above the fold; **Benefits** addresses pain (3 numbered cards); **Social Proof** layers credibility (logos → metrics → testimonial — broad to specific); **Demo Form** is the conversion target with low friction (6 fields, 5 required); **Sticky CTA** re-engages scrollers who blew past the form.

The Elementor JSON was rebuilt from the legacy `section/column` model to **Flexbox Containers (`e-con`)** after inspecting zycus.com's real DOM — they use the same primitive (320 `e-con` instances on their homepage), Astra theme, Elementor Pro, Gravity Forms, Swiper carousels. Aligning our schema means the import lands cleanly into a current Elementor install and edits exactly like the real Zycus pages.

Brand alignment was discovered the same way: regex-scanning their inline `<style>` blocks surfaced `#003DA5 / #003DA5 / #002C76` as the primary blue family (not the PRD's `#003366` navy), `Inter` (not Segoe UI), H1 at `weight 300 UPPERCASE`, H2 at `weight 400`. The whole CSS-variable layer was swapped to match the real site rather than the PRD's outdated guess.

## Responsiveness approach

Mobile-first CSS with two breakpoints exactly where layout needs to break: **991px** (drops hero to single column, benefits to 2-col grid, logos to 3-up) and **640px** (everything single-column, form fields stack, H1 24px, CTAs full-width). Layout uses **CSS Grid** for the hero / cards / metrics and **Flexbox** for inline groups (CTAs, sticky bar) — chosen per use case, not as a global default.

The `<meta name="viewport" content="width=device-width, initial-scale=1">` ships in `<head>`, all metrics and font-sizes use rem-friendly absolute pixels for predictability, every interactive target meets the 48px touch-target spec, and `overflow-wrap: break-word` on the H1 protects against narrow viewports breaking the long uppercase headline.

## Form + validation logic

**Six fields** (5 required, 1 optional) per PRD §4.4. Two-layer validation by design:

- **Client-side** (`zycus-landing.js`): real-time on `blur`, with inline error messages in `#E53935` 12px. Each field has a per-rule message (`Please use your work email address` when a personal email domain is detected via regex against `gmail/yahoo/outlook/hotmail/aol`). The submit button is `disabled` until every required field has a value, then re-validates the whole form on submit and focuses the first invalid field if anything fails. Char counter on the textarea (`0/500`, live).

- **Server-side** (`Zycus_Form_Handler::handle()`): nonce check (CSRF), hidden honeypot field, IP-based rate limit (5/hour), full re-validation, then `sanitize_text_field` / `sanitize_email` / `sanitize_textarea_field` before `$wpdb->insert()` into `wp_zycus_leads` (indexed on email + created_at + job_title). On success, sends a branded admin notification and a user confirmation via `wp_mail` with HTML templates. Response is JSON; the client renders success/error/network states from the response code.

The job-title and country option arrays live as static class members so the validator on the server and the `<select>` on the client stay in sync from a single source.

## Tracking implementation

Google Analytics 4, configured via plugin settings (`Demo Requests → Settings → GA4 ID`). When the ID is set, `gtag.js` is injected sitewide from `Zycus_Analytics::inject_gtag()`. From the front-end JS we emit every event from PRD §8.1:

- `cta_click` (with `button_location`, `button_text`, `section`)
- `form_field_focus` (`field_name`)
- `scroll_to_form` (`scroll_depth: 50`) — fires once via IntersectionObserver
- `form_submit` (`form_type`, `job_title`, `country`)
- `form_error` (`error_type`: `validation_failed` / `submission_failed` / `network_error`)

`pageview` ships automatically from the `gtag('config')` call. All events are safe to register as GA4 conversions without further setup.

## AI tools used + specific impact

- **Claude Code (Sonnet 4.7)** — full build agent. Generated the WordPress plugin scaffold (`zycus-landing.php` + 6 `includes/*.php` classes), the CSS design system with variables and responsive breakpoints, the vanilla-JS validation/animation/sticky-CTA module, the Elementor Flexbox Container template JSON, the email templates, the static preview, and this note. Estimated time saved vs hand-coding from PRD: ~12 hours.
- **Claude WebFetch + Python regex** — used to *inspect zycus.com's real CSS* and discover the discrepancy between the PRD's color palette (`#003366` navy, `#00A8A8` teal, Segoe UI) and Zycus's actual brand (`#003DA5` corporate blue, no teal, Inter, weight-300 uppercase H1s). Drove the rebrand pass that swapped the entire CSS-variable layer to match the live site.
- **Vercel CLI** — single-command production deploy of the static preview (`vercel --prod --yes`).
- **Puppeteer-core (Node)** — programmatic, device-emulated screenshot capture against the live Vercel URL (mobile/tablet/desktop, full-page + viewport variants). Headless Chrome's `--window-size` alone doesn't trigger viewport emulation, so a real device-emulation harness was needed.
- **No code was copy-pasted from generic templates** — every file is purpose-built for this PRD and Zycus's actual brand.
