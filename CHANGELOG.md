# Changelog

## 0.9.3 — 2026-10-19

### Licensing console
- The licensing service gets a browser console at /operator: dashboard, searchable license list, license detail (renew, change plan, edit customer, suspend, cancel, revoke, reinstate, move or deactivate sites), new-license flow that shows the key and portal token once, releases, and activity.
- Customer portal at /portal: plan, sites, move a site, download data, privacy requests.
- Sign-in with HttpOnly SameSite=Strict session cookies, CSRF tokens, same-origin checks, a strict content security policy, and sign-in lockout.
- Licenses record customer name, email, and private notes; operators can deactivate a single site; new keys use five readable groups without look-alike characters.

## 0.9.2 — 2026-10-19

- New Client reviews pattern (msp-nexus/section-reviews, Trust & proof): rating summary with a link to the review profile, a featured client quote, and a grid of review cards with stars, reviewer, and source. Ships with clearly marked samples to replace with real reviews.

## 0.9.1 — 2026-10-19

- Settings screen reorganized into sections (Business, Contact, Social, Messages, Design, Updates & integrations) with a side menu, keyboard navigation, and search across every setting. One Save still keeps every field, the last section reopens after saving, and a Settings saved notice appears.
- Design colors start from the theme defaults, fixing a bug where saving the page on a fresh install stored black for every unset color.
- Admin light/dark mode now follows the computer until each user picks one.

## 0.9.0 — 2026-10-19

### Redesigned admin
- Every MSP Nexus admin screen now shares one app-style shell: a branded top bar with icon navigation (Dashboard, Settings, Starter sites, Studio, Layouts) and a Tools menu with short descriptions, breadcrumbs, and card-based settings, tables, and notices.
- New Dashboard landing screen: welcome banner, quick actions, a launch checklist with one-click fixes, system status, update status with a Check for updates now button, and help links.
- Light and dark modes, remembered per user.
- Settings moved to its own screen (admin.php?page=msp-nexus-settings); the MSP Nexus menu now opens the Dashboard.
- White-label client mode shows the partner product name and admin logo in the shell.
- Diagnostics: status badges, and Clear update-check cache now also clears the GitHub release lookup.

## 0.8.4 — 2026-10-18

- Footer gets a Resources column (all resources, insights, security booklet, self-assessment, FAQ). On tablet widths the brand block takes its own row so the four link columns stay readable.
- Footer text is easier to read: larger links and body copy, brighter colors, and clearer column headings, matching the header menu.

## 0.8.3 — 2026-10-18

- New Resources menu and a Resources hub page pattern (msp-nexus/page-resources-hub), also created by the Complete MSP website starter: the security booklet, self-assessment, articles, FAQ, and third-party audits in one place, plus the latest articles and a call to action.
- New tile icons: book, checklist, article, and question.

## 0.8.2 — 2026-10-18

- Header menu is easier to read: larger, lighter-weight links with more space between them, a soft highlight on hover and keyboard focus, an underline on the current page, and roomier dropdowns.

## 0.8.1 — 2026-10-18

- Blog articles: the summary under the headline lines up with the title.

## 0.8.0 — 2026-10-18

### Blog in the Nexus design
- New blog index, category archive, and article templates in the Nexus dark palette: page hero, category chips, article cards with category, excerpt, and date, styled pagination, and a closing call to action (free booklet and a call with an engineer).
- Articles get a hero with category and date, a readable long-form body (headings, lists, links, quotes), previous and next links, and a More insights row.

## 0.7.0 — 2026-10-18

### Security self-assessment
- New **MSP Security Self-Assessment** block and **Security self-assessment** page pattern: ten yes / partly / no questions mapped to the CIS Controls, an instant score out of 100 with a strong / gaps / at-risk rating, and a prioritized plain-English fix for every gap.
- Scoring runs on the server, so it works without JavaScript. Results come back through a signed link and cannot be tampered with.
- Visitors can optionally send their results to the sales inbox (rate-limited and nonce-protected); nothing is sent otherwise.

## 0.6.0 — 2026-10-16

### Audits and lead magnets
- **3rd-party MSP audit** page pattern: why an audit, twelve review areas, a five-step process, deliverables, independence promises, who it is for, seven FAQs, and a call to action.
- **Request an audit** page pattern with the new **MSP Audit Request Form** block: contact details, business size and location, current provider and satisfaction, problems they are seeing, the reason for the audit, and industry-specific software, emailed to the sales inbox.
- **Cybersecurity ebook** landing-page pattern with the new **MSP Gated Download** block: visitors enter their details, the lead goes to the sales inbox, and a signed link (valid 24 hours) reveals the PDF chosen from the Media Library.
- Both forms use nonces, a honeypot, a per-visitor rate limit, and allow-listed choices, and they keep their pages out of page caches.

### Fixes
- Consultation form success and error messages are readable on dark sections.
- FAQ toggles (core/details) are styled in flagship sections, and five-step process lists lay out in five columns.

## 0.5.5 — 2026-09-24

### Performance and caching
- Pages that show the consultation form are marked uncacheable (`DONOTCACHEPAGE`, LiteSpeed no-cache, and no-cache headers). A page cache could otherwise serve a stale form nonce, and submissions would fail once it expired.
- The generated pattern library (91 patterns) is no longer built on public page views. It is registered on admin screens, editor REST requests, and WP-CLI, and on demand just before a template or post renders one of those patterns.

## 0.5.4 — 2026-09-24

### Automatic updates
- MSP Nexus Core now updates the theme and plugin from the published GitHub releases when no licensed update channel is active. It checks every six hours, installs a package only when its SHA-256 matches the digest published in that release's manifest and the archive contains the expected product, and shows release notes under View details.
- With **Automatic updates** enabled in MSP Nexus → Settings, WordPress installs new versions in the background. Otherwise they appear under Dashboard → Updates for one-click install.
- Sites can opt out with `define( 'MSP_NEXUS_DISABLE_GITHUB_UPDATES', true );` or point at a fork with the `msp_nexus_github_repository` filter.

### Fixes
- Licensing service: upgrading a running service now adds the new release to an existing catalog instead of keeping only the previous version. Operator edits to existing releases are preserved.
- Cybersecurity pages without a site logo: the fallback header mark is fully green (its offset shadow stayed blue).
- The small business IT pricing card is centered; it was pinned to the left of its section.

## 0.5.3 — 2026-09-24

- The cybersecurity (Bastion) design now carries its signal-green palette into the header, menus, mobile menu, and footer, and tints the site logo to match, so the page no longer sits inside a blue frame. Other pages are unchanged.
- The footer shows the site logo (when one is set) in place of the text site title.

## 0.5.2 — 2026-09-24

### Consistent spacing and styling
- Every page design uses one section rhythm (102px at desktop, 56px on phones) instead of four.
- One button shape across the site: 10px corners, and filled and outline buttons are the same height. The header button matches.
- The cybersecurity headline is scaled in line with the other page heroes, and the IT consulting hero photo is square instead of a tall portrait.
- Headings are followed by the same space everywhere; long assurance paragraphs are capped at a readable line length.
- New long-form styles (`.nx-fs-legal`) for privacy, accessibility, and terms pages.
- Footer links are 39px tap targets on phones (was 24px).

## 0.5.1 — 2026-09-24

### Dark by default
- The small business IT, IT consulting, and healthcare/legal/manufacturing industry designs now use the Nexus dark palette, matching the rest of the site. Each design keeps its own accent: the Nexus blue gradient for small business IT, gold for IT consulting, and blue, rose, or amber for the industry pages.
- The header and footer stay dark on every page. The automatic light header and footer are removed, so a white logo is always readable.
- Buttons and numbered markers that carry white text use a deeper fill of each accent. All text on the five redesigned pages meets WCAG AA contrast.

## 0.5.0 — 2026-09-23

### The complete MSP website
- One unified, hand-designed 12-page site: Home, Services, About, Contact, Cybersecurity, Co-managed IT, Cloud & Microsoft 365, IT Consulting & vCIO, Small Business IT, and Healthcare, Legal, and Manufacturing industry pages. Starter sites → **Complete MSP website** creates every page in one click (existing pages are never changed; optional publish-all).
- Industry pages share one template (`inc/industry-page.php`) with a per-industry accent, so every industry page stays structurally identical.
- Signature designs for cybersecurity (dark, signal green), IT consulting (serif editorial), and small business IT (warm, light).
- Redesigned header and footer with Services and Industries menus, contact details bound to Settings, and an automatic light variant on light pages.
- Nine CC0 stock photographs (WebP, 405 KB total), each recorded in the asset manifest.

### Easier to use
- One **Company content** menu with an overview page replaces thirteen top-level menus.
- The Settings screen is reachable from the menu, grouped into plain-language sections, with hints that describe exactly where each value is used.
- Phone, email, client portal, hours, and booking link are bound to Settings in the header, footer, and contact pages.

### Fixes
- A static homepage chosen under Settings → Reading is now respected (`front-page.html` no longer overrides it).
- A published page keeps its address when a content type would otherwise claim it (for example a Services page at `/services/`).
- Contrast: submenu text was invisible; secondary text failed WCAG AA in four color styles; two page labels used an unreadable color.
- The mobile menu opened clipped to the header height on every page (header backdrop filter); it now opens full-screen as a readable list.
- The site-wide button-radius setting no longer overrides each design's button shape unless changed from the default.
- The demo importer failed silently on Windows checkouts (line endings changed the manifest digest); `.gitattributes` now enforces LF.
- Check scripts no longer require ripgrep; the design check reads every style variation instead of a fixed list.

## 0.4.0 — 2026-08-08

- Expanded Nexus Studio with a hierarchical layer navigator, favorites, context actions, device canvas modes, wireframes, style copy/paste, shortcuts, explicit save snapshots, and revision links.
- Added full responsive geometry and per-element conditions, visual dynamic-data/ACF selection, advanced queries, and reusable visual loop-item layouts.
- Expanded the inserter beyond 200 MSP-specific compositions and added accessible tabs, carousel, video dialog, counters, comparison, progress, icon, off-canvas, mega-menu, and advanced conditional header/popup capabilities.
- Added a 32-element WooCommerce template suite with catalog, gallery, checkout, quick-view, wishlist, side-cart, filter, and mobile controls plus six Elementor bridge widgets.
- Added a searchable 800-configuration adaptive starter library, focused content portability, expanded white labeling, consent-category script activation, modern image/performance controls, and broader integration detection.
- Retained WordPress 6.7+ and PHP 7.4.33+ compatibility, site-owned content, safe import boundaries, and license-independent public rendering.

## 0.3.0 — 2026-08-08

- Added Nexus Studio with front-end region inspection, native visual-editor launch points, 30 portable MSP element variations, eight block styles, and per-device visibility, spacing, and motion controls.
- Added reusable block layouts for conditional full templates, headers, footers, mega menus, popups, and loop regions, including six safe draft starters.
- Added general dynamic-value, content-loop, layout-region, mega-menu-panel, and WooCommerce product-showcase blocks.
- Added four original starter-site profiles, reviewed JSON import/export, white-label client controls, an integrations screen, global design tokens, and a performance console.
- Retained the PHP 7.4.33 floor, native block portability, safe starter ownership rules, and license-independent public content.

## 0.2.0 — 2026-08-08

- Reworked the starter import so generic business content can publish safely while unverified proof, people, locations, resources, events, relationships, careers, and legal placeholders remain drafts.
- Added complete About, Support, and Contact starters; site-name-aware homepage titles; meaningful CTA destinations; archive headings; and SEO-description fallbacks.
- Removed fictional brand, telephone, testimonial, metric, and legal-link content from default public templates and patterns.
- Added launch-safety and privacy-policy diagnostics, refreshed release metadata, and retained the PHP 7.4.33 compatibility floor.

## 0.1.0 — 2026-08-08

- Initial clean-room MSP Nexus block theme, child theme, and companion plugin.
- Added 65+ patterns, 12 page starters, four styles, custom content types, dynamic blocks, consultation form, schema, breadcrumbs, onboarding, demo import, diagnostics, repair/reset, licensing UI, and WP-CLI.
- Added independent entitlement/update service with activation policy, signed offline caches and manifests, package grants, release rollout controls, portals, webhooks, persistence, rate limiting, audit events, and verified rollback.
- Added original Northstar demo content, generated artwork, lint/test/package automation, and responsive QA evidence.
- Set the production compatibility floor to PHP 7.4.33, removed PHP 8-only convenience APIs, and added PHP 7.4 syntax, API, and packaged-runtime gates.
