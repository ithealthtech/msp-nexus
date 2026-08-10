# Testing and validation

## Automated release gate

Run `npm run check` for the full local gate and `npm run package` to generate reproducible release archives. The gate validates repository structure, JSON, PHP 7.4 syntax and unavailable APIs with the bundled parser, JavaScript syntax, WordPress block markup, secret patterns, ESLint, Stylelint, HTML, color contrast, and the licensing service test suite.

The 2026-08-08 release candidate passed:

- 18 required structure checks
- 15 JSON documents
- 39 PHP files parsed against the PHP 7.4 compatibility policy
- 35 JavaScript files syntax checked
- 46 block-markup files checked
- 172 text files scanned for secrets
- ESLint, Stylelint, and HTML validation
- all configured foreground/background contrast pairs
- 26 licensing-service tests
- two consecutive package builds with identical SHA-256 hashes

## WordPress runtime

The packaged theme and core plugin were installed on clean WordPress through the official WordPress Playground CLI using PHP 7.4.33. Runtime assertions reject lower PHP versions and cover activation, content registration, REST visibility, block registration, settings, schema, breadcrumb output, demo import, import idempotency, diagnostics, and expected homepage structure. A multisite-enabled run exercises network-compatible activation and initialization. Both release-floor runs passed on 2026-08-08.

## Browser and performance

The real WordPress homepage was inspected at 1440, 768, and 390 CSS pixels. It had no horizontal overflow or console errors/warnings. The responsive Navigation overlay was opened and keyboard-dismissed. Lighthouse results and screenshots are recorded in `docs/VISUAL_QA.md` and `artifacts/`.

## Tooling boundaries

Native PHP, Composer, Docker, WP-CLI, PHPCS, PHPStan, PHPUnit, and the WordPress Theme Check plugin were unavailable on the authoring workstation. The repository therefore uses an independent PHP parser, static policy checks, JavaScript tests, and real WordPress Playground execution; these do not claim to be substitutes for every native CI job. Docker and Composer workflows remain supplied for a conventional CI or release host.

No production deployment, payment processing, transactional email, DNS validation, customer identity provider, or live update signing key was used. WooCommerce presentation compatibility is included, but checkout/payment behavior requires a separate WooCommerce-enabled integration run if that optional plugin is selected.
