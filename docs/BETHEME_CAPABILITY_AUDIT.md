# BeTheme capability audit and Nexus disposition

This audit compares public capability categories, not proprietary source or visual identity. The clean-room Nexus implementation uses WordPress-native documents, blocks, templates, revisions, metadata, and WooCommerce ownership. Evidence paths are repository-relative.

| Public capability category | Nexus 0.4.0 implementation | Disposition |
|---|---|---|
| Visual builder workspace | Layers, selection/actions, favorites, search, context menu, device canvas, dark UI, wireframes, style copy/paste, shortcuts, save snapshots and revision links in `assets/studio-workspace.js` | covered with native WordPress save surface |
| Large element library | 75 hand-curated MSP compositions + 140 service-family compositions + 36 Woo variations + native/core styles + 22 Nexus blocks | covered; specialized for MSP workflows |
| Responsive controls | Three-device spacing, sizing, typography, alignment, order, positioning, offsets, z-index, opacity, display, color, radius and transforms | covered |
| Dynamic data and custom fields | Visual schemas for site, post, REST meta, ACF, archive, author, visitor, term, date and WooCommerce | covered |
| Query/loop builder | Post type, two taxonomies with relation, meta comparison, include/exclude IDs, authors, related terms, sorting, offset, pagination, grid/list/masonry and visual Loop layouts | covered |
| Conditional templates/elements | Context, post type, IDs, terms, visitor state/roles, locale, date window, weekday and query conditions with cache protection | covered |
| Header/footer builder | Conditional block layouts plus sticky, overlay, compact and hide/reveal behavior | covered |
| Mega/off-canvas navigation | Hover intent, click, alignment, outside click, Escape, dialog focus behavior and navigation-close controls | covered |
| Popup builder | Delay, scroll, exit and selector triggers; position/motion, dismissal, consent gate and weighted persistent variants | covered |
| Prebuilt website scale | Searchable 800-kit matrix with 20 industries, ten service positions and four visual systems; selective non-destructive application | covered through composable kits rather than duplicate dumps |
| Theme options | Native Global Styles/appearance tools plus Nexus business, token, responsive, layout, privacy, commerce, performance, branding, update and integration settings | covered with structured/native controls rather than a single proprietary options array |
| WooCommerce templates | 36 context-aware elements spanning shop, product, cart, checkout, orders, account, free-shipping progress, dropdown login, view controls, off-canvas filters and mini/side cart plus presentation and interaction settings | covered; WooCommerce retains transactional ownership |
| Import/export | Full reviewed bundles and focused content export/import-as-new-draft with selectable settings/templates/pages | covered |
| White label | Login/admin/dashboard/footer/email/plugin identity controls without concealing critical status | covered with safety boundary |
| Elementor | Six bridge widgets for the primary Nexus dynamic capabilities | covered for interoperability; native blocks remain primary |
| Media and icon elements | Locally packaged MIT Lottie renderer, native video backgrounds, video dialog, content carousel, image masks, icon cards, counters, progress and comparison blocks with reduced-motion/privacy behavior | covered |
| Product attribute swatches | Progressive buttons synchronized to WooCommerce variation selects with accessible pressed state | covered |
| Purchased content | Nested Purchase Gate block checks signed-in WooCommerce purchase history and disables page caching | covered |
| Free-delivery progress | Configurable presentation threshold with explicit reminder that WooCommerce shipping zones own the actual rate | covered |
| Performance/media formats | WebP/AVIF output when supported, quality, lazy loading, priority preload, preconnect, heartbeat and scoped asset unload controls | covered |
| Consent/GDPR interface | First-party category preferences, script activation, recurrence and reopen control | covered; legal compliance remains operator responsibility |
| Licensing/updates | Independent entitlement service, signed manifests, scoped package grants, rollout channels, rollback and license-independent public content | exceeds a theme-only license screen |

## Intentional exclusions

- Arbitrary PHP execution from the visual editor.
- Fake-sale or fabricated social-proof popups.
- Destructive demo resets or silent customer-content replacement.
- Bundled paid plugins or copied proprietary libraries.
- Consent bypasses or optional tracking enabled without the configured choice.
- Hiding security, update, privacy, or license state from administrators.
- Proprietary shortcode serialization that locks content to the theme.

These exclusions are product and safety decisions, not unfinished parity work.

## Public reference sources

Reviewed 2026-08-08:

- BeTheme public feature catalog: <https://www.muffingroup.com/betheme/features/>
- BeTheme prebuilt website catalog: <https://muffingroup.com/betheme/prebuilt-websites/all/>
- BeBuilder overview: <https://support.muffingroup.com/documentation/bebuilder/an-overview/>
- BeTheme template builder documentation: <https://support.muffingroup.com/documentation/templates/>
- BeTheme WooCommerce template documentation: <https://support.muffingroup.com/documentation/woocommerce/templates/>
- BeTheme prebuilt website documentation: <https://support.muffingroup.com/documentation/pre-built-websites/>
