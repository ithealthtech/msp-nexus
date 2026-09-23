# Changelog

## Unreleased (0.5.0)

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
