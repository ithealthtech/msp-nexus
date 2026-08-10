# MSP Nexus architecture

## Product boundary

The product is split so presentation, site-owned content, and vendor-operated commerce remain independently replaceable:

1. **MSP Nexus theme** owns templates, template parts, patterns, style variations, design tokens, and presentation assets.
2. **MSP Nexus Core plugin** owns services, industries, case studies, locations, testimonials, team members, resources, FAQs, settings, blocks, imports, diagnostics, and the WordPress-side entitlement/update client.
3. **Licensing service** owns products, versions, license keys, customer organizations, orders, entitlements, activations, audit events, update manifests, signed package metadata, download authorization, and customer/operator portals.
4. **Child theme** is the supported customization seam for customer-specific PHP, CSS, patterns, and templates.

Deactivating the companion plugin never deletes customer content. Losing entitlement never hides public content or disables the theme; it only gates premium downloads, cloud services, and support access after a documented grace period.

## WordPress architecture

The theme is a native block theme. `theme.json` is the source of design tokens and editor constraints. HTML templates and parts remain portable block markup. Patterns are PHP registrations so copy can be translated and categories can be curated. The theme avoids page-builder lock-in.

The plugin follows a small service-container architecture:

- `Plugin` controls boot order and lifecycle.
- domain modules register content types, taxonomies, settings, and capabilities.
- blocks register server-side from `block.json` and escape output at render time.
- onboarding runs versioned, idempotent jobs with explicit confirmation and resumable progress.
- licensing stores only the license identifier and cached entitlement state; raw keys are redacted from logs.
- update integration uses WordPress hooks but verifies manifest signatures, package hashes, product identity, version constraints, and authorized domains before installation.
- Nexus Studio extends registered block attributes with portable responsive controls and registers original block variations rather than serializing proprietary shortcodes.
- reusable `msp_nexus_layout` records hold block markup for conditional templates, header/footer replacements, mega-menu panels, popups, and loop regions; published rules resolve by context, post type, and priority.
- starter profiles select theme JSON variations and may replace only a homepage that still carries the Nexus starter-ownership marker.
- JSON portability excludes license credentials, validates bundle identity and size, rejects executable markup, and never overwrites an unmarked customer page.

All state-changing REST and admin actions require capabilities and nonces. Remote requests have bounded timeouts and fail closed for downloads while failing open for the already-rendered public site.

## Nexus Studio boundary

Nexus Studio is an editing and orchestration layer over WordPress blocks, Global Styles, templates, template parts, Navigation, revisions, and REST-enabled content. The front-end inspection mode maps visible regions back to the appropriate editor; saving remains in the authenticated WordPress editor. This preserves native undo, revisions, permissions, sanitization, and content portability.

Conditional layouts are opt-in published records. Draft starter layouts have no front-end effect. Popup content uses the native `dialog` element, local dismissal state, reduced-motion CSS, and explicit trigger settings. The WooCommerce module is inert until WooCommerce is active and does not replace checkout, account, payment, tax, stock, or order ownership.

## Licensing service architecture

The service uses modern dependency-light Node.js modules, a REST API, a relational persistence adapter, and Ed25519 signatures. Package binaries live behind a storage interface and short-lived download grants. The API is versioned under `/v1`. Domain normalization, activation limits, staging policy, grace periods, renewal dates, and subscription state are evaluated centrally.

The deployable single-writer implementation provides SQLite WAL persistence and an interchangeable in-memory test adapter. Customer and operator surfaces use constant-time bearer-token checks, no-store responses, a restrictive content security policy, and immutable audit events. A production deployment that requires horizontal write scaling must add a PostgreSQL/durable-queue adapter behind the documented repository contracts.

## Trust boundaries

- WordPress installations are untrusted API clients.
- License keys are bearer-like secrets and are hashed at rest.
- Update signing private keys exist only in service/runtime release tooling; WordPress ships only the public key.
- Package hashes are signed as part of a canonical manifest.
- Webhook payloads are signed and replay-protected.
- Portal sessions use secure, HTTP-only, same-site cookies and CSRF protection.

## Versioning and release

Theme, plugin, and service use semantic versions. A release manifest records compatible WordPress/PHP versions, database migrations, package SHA-256 hashes, signature, changelog, rollout channel, and rollback package. Production ZIPs never include tests, source maps, secrets, build caches, or reference-product artifacts.

## Clean-room provenance

The supplied commercial theme package was exhaustively inventoried to identify broad capability classes such as modular bootstrapping, admin UX, demo import, diagnostics, update delivery, builder interoperability, and operational recovery. MSP Nexus implements those outcomes with original names, structure, code, interfaces, copy, and visual design.
