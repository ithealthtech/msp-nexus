# Nexus Studio

Nexus Studio 0.4.0 expands the native WordPress editor rather than replacing it with a proprietary document format.

## Editing surfaces

- **Studio launchpad:** routes to Global Styles, templates, patterns, Navigation, layouts, starters, portability, privacy, commerce, and performance tools.
- **Front-end inspection:** identifies visible regions for authenticated editors and links to the responsible WordPress editor. Inspection is intentionally read-only.
- **Workspace:** hierarchical layers, block movement/duplication/removal, favorites, context actions, synced-pattern creation, light/dark UI, wireframes, style copy/paste, shortcuts, save snapshots, and revision links.
- **Element library:** more than 200 managed-services compositions, image masks, native video backgrounds, local Lottie rendering, eight presentation styles, 36 WooCommerce element variations, and 22 server-rendered Nexus blocks.
- **Responsive controls:** desktop, tablet, and mobile geometry, display, position, offsets, transforms, type, color, spacing, visibility, and reduced-motion-aware entrance treatments.
- **Dynamic content:** visual site/post/archive/author/term/visitor/date/ACF/Woo field selection, advanced queries, and reusable visual Loop layouts.

## Reusable layouts and conditions

Layouts are revisioned `msp_nexus_layout` records made from blocks. Areas are template, header, footer, mega menu, popup, and loop. Rules can target page context, post type, object IDs, taxonomies/terms, visitor state/roles, locale, dates, weekdays, and query values with include/exclude logic and priority. The highest-priority matching record wins for template, header, and footer areas. Matching popup records can all render.

Create draft layout starters from the Studio screen. Draft layouts do not alter the public site. Review block structure, navigation, links, accessibility, and responsive behavior before publishing a rule.

Headers support conditional sticky, overlay, compact-on-scroll, and hide/reveal behavior. Mega menus support hover intent, alignment, outside-click dismissal, Escape handling, and off-canvas navigation. Popups support delay, scroll percentage, exit intent, a CSS click selector, motion/position choices, backdrop behavior, recurrence, consent gates, and weighted persistent variants.

## Adaptive starter catalog

The catalog exposes 800 complete configurations assembled from 20 industries, ten service positions, and four reviewed visual systems. Search and facet controls narrow the library. Applying a kit is selective: design tokens, missing draft pages, front-page assignment, Nexus-owned homepage replacement, and reusable navigation are independent choices. Existing non-Nexus content is preserved.

## Commerce and alternate editors

The WooCommerce integration provides 36 context-aware shop, product, cart, checkout, order, account, filter, wishlist, quick-view, login, view-bar, shipping-progress, purchased-content, and side-cart elements. WooCommerce continues to own orders, payments, taxes, shipping rates, inventory, account authorization, and transactional security. Catalog, price, swatch, gallery, checkout, product-count, column, mobile-cart, AJAX-filter, quick-view, and wishlist controls are available under MSP Nexus → WooCommerce.

When Elementor is active, six optional Nexus widgets bridge the service grid, FAQ, consultation form, dynamic value, content loop, and WooCommerce template elements into Elementor. The underlying content and queries remain native Nexus/WordPress data.

## Portability and safety

Export bundles contain organization/design settings, branding, privacy, commerce and performance presets, Nexus layouts, current-theme templates and parts, and Nexus-owned starter pages. A focused export can carry one selected public content document; when its slug exists, focused import creates a new draft instead of overwriting it. Raw license keys and transient entitlement data are excluded.

Import requires `manage_options`, `unfiltered_html`, a nonce, a valid schema/product identifier, a JSON extension, and a file no larger than 2 MB. Content containing PHP tags, scripts, or inline event handlers is rejected. An unmarked starter page is never overwritten.

## Intentional boundary

Nexus Studio is a clean-room capability counterpart, not a one-to-one UI or source reproduction of BeBuilder. The authenticated WordPress editor remains the write surface so capabilities, revisions, sanitization, recovery, collaboration, and block portability remain native. Public inspection mode intentionally does not write directly into the rendered page. Arbitrary PHP execution, fake-sale urgency, consent bypasses, destructive demo resets, hidden update/security/license state, bundled paid plugins, and shortcode lock-in are excluded.
