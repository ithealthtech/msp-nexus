# Public-site clean-room reference audit

Audit date: 2026-08-08. This report records externally presented capability and user-experience classes only. It contains no copied source, assets, branded copy, screenshots, proprietary implementation details, or reverse-engineered endpoints.

## Scope reviewed

Primary routes reviewed in full-text form:

- `https://muffingroup.com/betheme/` (836 indexed lines)
- `https://muffingroup.com/betheme/prebuilt-websites/` (3,327 indexed lines; complete 749-entry catalog surface and category/filter structure)
- `https://muffingroup.com/betheme/bebuilder/` (550 lines)
- `https://muffingroup.com/betheme/bebuilder-blocks/` (826 lines)
- `https://muffingroup.com/betheme/woo-builder/` (646 lines)
- `https://muffingroup.com/betheme/header-builder/` (374 lines)
- `https://muffingroup.com/betheme/loop-builder/` (342 lines)
- `https://muffingroup.com/betheme/sidebar-menu-builder/` (289 lines)
- `https://muffingroup.com/betheme/theme-options/` (780 lines)
- `https://muffingroup.com/betheme/features/` (641 lines)
- `https://muffingroup.com/betheme/elements/` (713 lines)
- `https://muffingroup.com/betheme/speed-performance/` (429 lines)
- `https://muffingroup.com/betheme/licensing/` (124 lines)
- `https://www.dataprise.com/` (332 lines, including the complete header information architecture and homepage content sequence)

The prebuilt catalog advertises 749 entries and exposes search, category, WooCommerce, blog, portfolio, one-page, store, popularity, and recency discovery. Individual demo aesthetics are not copied. The product response is a focused MSP starter/page/pattern library with portable native blocks, searchable editor names, a safe importer, and an extensible signed library protocol.

## Capability translation

| Publicly presented class | Clean-room MSP Nexus equivalent | Product advantage sought |
|---|---|---|
| Proprietary live/blocks builder | Native WordPress Site Editor, List View, revisions, responsive previews, patterns, style variations | Portable content, smaller lock-in surface, standard WordPress recovery |
| Global styles and variables | `theme.json` v3 tokens plus four style variations and block styles | One standards-based design system across editor/front end |
| Prebuilt site/section library | 65+ original patterns, 12 page starters, 4+ header/footer choices, 3 mega-menu panels, signed demo manifest | MSP-specific depth instead of unrelated quantity |
| Header/footer/mega-menu builder | Editable template parts and compositional patterns using core Navigation | Keyboard/touch behavior remains owned by WordPress core |
| Loop and dynamic-data builder | Query Loop, post templates, server-rendered relationship blocks, REST-enabled metadata | Query output remains portable and inspectable |
| Custom post type/dynamic field support | Thirteen site-owned MSP content types, six relationship/filter taxonomies, registered REST metadata | Purpose-built administration for MSP buying journeys |
| WooCommerce builder | Native WooCommerce block and global-style compatibility with no paid dependency | Commerce stays optional and updateable independently |
| Conditional templates | WordPress hierarchy for types/archives/taxonomies/search plus editor-selected page/campaign templates and multilingual-plugin language routing | Transparent native resolution, revisions, and graceful fallback instead of serialized rule lock-in |
| Popup/sidebar builders | Accessible Navigation overlays, support-side-panel pattern, and announcement block with banner/modal, targeting, frequency, consent, focus return, Escape, reduced motion, and no-script behavior | No dark patterns and no global mandatory script |
| Theme options | Native visual controls plus concise operational settings | Lower admin complexity and fewer duplicate sources of truth |
| One-click demos | Explicit preview, integrity digest, batch import, stable origin keys, ledger, resumability, dry-run reset | Reruns reconcile rather than duplicate or erase customer data |
| Revisions/import/export | Core revisions plus portable block markup and manifest formats | Standard recovery and inter-site portability |
| Image/WebP/performance controls | Responsive core media, system font stacks, conditional block assets, and diagnostics for image processing/cache/rewrite state | Prefer platform/host facilities and avoid duplicating core media pipelines |
| Licensing and updates | Separate entitlement service, signed entitlements/manifests, SHA-256 ZIP verification, scoped grants, channels, rollback | Raw keys never enter package URLs; installed content never disappears |
| White labeling | Brand presentation hooks that cannot hide license/security/update information | Customer branding without obscuring operational risk |

## Dataprise flow observations translated, not copied

The reference homepage uses a broad service/industry/resource/company mega-navigation, separates buyers with and without internal IT staff, introduces an outcome-led hero, presents AI/proactive operations, expands service capability cards, then shifts from services to business outcomes and strong discovery/support calls to action.

MSP Nexus uses the same buyer-journey principles with an original visual grammar and copy: utility support access, sticky native navigation, dark operational hero, two-audience choice, integrated service grid, responsible-AI panel, business-outcome choices, proof, plans, industries, resource education, FAQ, consultation CTA, and detailed footer. The final design review will test hierarchy, density, rhythm, CTA clarity, proof context, mobile continuity, and visual fatigue across that entire sequence.

## “Better” acceptance criteria

“Better” is measured rather than asserted:

- essential content remains standard block/post data if the theme or plugin changes;
- no public content is disabled by license state;
- native keyboard/touch navigation and no horizontal overflow from 320px;
- fewer mandatory front-end scripts and no jQuery dependency;
- explicit fictional-claim labeling and conservative structured data;
- cryptographically verified update path with scoped downloads;
- importer reruns are idempotent and reset targets are enumerated;
- concise operational settings instead of hundreds of overlapping visual options;
- clean single-site and multisite install evidence;
- representative accessibility/performance evidence, with honest variances.

## Audit boundary

Marketing claims on reference pages are observations, not verified benchmarks. Third-party names and compatibility claims are not reused as endorsements. Feature classes that would be unsafe or counterproductive—fake-sale notices, bundled paid plugins, arbitrary HTML/PHP execution, consent bypasses, or dark-pattern popups—are deliberately excluded or replaced by safer native equivalents.
