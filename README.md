<div align="center">

# MSP Nexus

**A clean-room WordPress product suite for managed service providers.**

A full-site-editing block theme, a companion functionality and visual-design plugin, a
customization-safe child starter, and an independent licensing and update service.

[![Version](https://img.shields.io/badge/version-0.5.1-b45309)](CHANGELOG.md)
[![WordPress](https://img.shields.io/badge/WordPress-6.7%2B-21759b)](#compatibility-target)
[![PHP](https://img.shields.io/badge/PHP-7.4.33%2B-777bb4)](#compatibility-target)
[![Theme licence](https://img.shields.io/badge/theme-GPL--2.0--or--later-blue)](#licensing)

[**Product site**](https://ithealthtech.github.io/msp-nexus/) ·
[Install](https://ithealthtech.github.io/msp-nexus/install.html) ·
[Licensing](https://ithealthtech.github.io/msp-nexus/licensing.html) ·
[Documentation](docs/)

![MSP Nexus managed services homepage preview](packages/msp-nexus/screenshot.png)

</div>

---

## What this is

Most MSP websites are built on a general-purpose theme bent into shape. MSP Nexus starts
from the assumption that a managed service provider's site has a known structure —
services, coverage, compliance posture, client onboarding — and ships that as a product
rather than as a pile of page builder settings.

It is WordPress-native throughout: block APIs, semantic HTML, progressive enhancement, and
server-side registration from `block.json`. There is no proprietary page-builder runtime to
get locked into.

## Packages

| Package | Role |
| --- | --- |
| `packages/msp-nexus` | Full-site-editing block theme |
| `packages/msp-nexus-core` | Nexus Studio, content model, conditional layouts, dynamic blocks, onboarding, diagnostics, integrations, license client |
| `packages/msp-nexus-child` | Customization-safe child theme starter |
| `services/licensing-service` | Entitlement, activation, signed updates, package delivery, customer portal |
| `demo` | Original demo data and deterministic import manifests |
| `docs` | Architecture, security, operations, editing, API, and traceability documentation |
| `scripts` | Validation and packaging automation |

The theme renders on its own, but the companion plugin owns Nexus Studio, the content
model, and the license client. Install both — see the
[install guide](https://ithealthtech.github.io/msp-nexus/install.html).

## Compatibility target

- Tested target: WordPress 7.0.3
- Minimum WordPress: 6.7
- Minimum PHP: 7.4.33
- Node.js: 22 or newer, for development tooling only

Node is not required to run MSP Nexus, only to build, validate, and package it from source.

## Development

```powershell
npm install
npm run check
npm run package
npm run qa:php74
```

`npm run check` runs structure, JSON, PHP, JS, block-markup, and security checks, then lint
(JS, CSS, HTML), the design check, and the service check.

The official WordPress Playground blueprints under `artifacts/` verify packaged single-site
and multisite installs on PHP 7.4.33 **without requiring native PHP or Docker**. The
licensing service test suite covers persistence-domain and HTTP contracts.

See [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) and
[docs/TESTING_AND_VALIDATION.md](docs/TESTING_AND_VALIDATION.md).

## Licensing

Distributed WordPress code is **GPL-2.0-or-later**. Commercial entitlement controls apply
only to separately provided services and delivery — the update channel, not the code you
already have.

Practically, that means **a site keeps rendering from installed code if the licensing
service is unreachable.** Signed entitlement caches provide a bounded administrative grace
state rather than a hard stop. An outage in licensing infrastructure is not permitted to
take a client's public website down.

Update manifests are signed with Ed25519 and verified by the WordPress client before they
are trusted. Package download grants expire after five minutes and are bound to product,
version, license, and site.

See [docs/LICENSING_OPERATIONS.md](docs/LICENSING_OPERATIONS.md) and the
[licensing overview](https://ithealthtech.github.io/msp-nexus/licensing.html).

## Clean-room provenance

This repository is a clean-room implementation. Reference products were studied only to
understand capability categories, packaging conventions, operational safeguards, and
commercial expectations. **No proprietary source, visual design, copy, trademarks, or
update endpoints are reused.** The architecture is a WordPress-native replacement, not a
source or UI clone.

This constraint is enforced in review — see [CONTRIBUTING.md](CONTRIBUTING.md).

## Product status

Version 0.5.1 puts every page design on the Nexus dark palette by default. 0.5.0 added a
complete, hand-designed 12-page MSP website (core, service, and healthcare/legal/manufacturing
industry pages) that installs in one click from Starter sites, a redesigned header and footer, a
single Company content menu, and accessibility and mobile-menu fixes. It builds on 0.4.0's expanded Nexus Studio authoring workspace, a 200+ composition
inserter, an 800-configuration MSP starter catalog, responsive geometry and conditions,
visual dynamic data and loops, advanced headers/menus/popups, WooCommerce template tooling,
Elementor widgets, consent controls, white labeling, portability, and performance tooling.

It is **pre-1.0**. Packaged installs are verified against the PHP floor and the licensing
service has contract tests, but pilot before standardizing a client base on it.

See [docs/REQUIREMENTS_TRACEABILITY.md](docs/REQUIREMENTS_TRACEABILITY.md) for
requirement-level evidence and intentional safety boundaries.

## Documentation

| Getting started | Building | Operating |
| --- | --- | --- |
| [Installation](docs/INSTALLATION_AND_CONFIGURATION.md) | [Architecture](docs/ARCHITECTURE.md) | [Licensing operations](docs/LICENSING_OPERATIONS.md) |
| [Editor guide](docs/EDITOR_GUIDE.md) | [Developer and hooks](docs/DEVELOPER_AND_HOOKS.md) | [Security](docs/SECURITY.md) |
| [Nexus Studio](docs/NEXUS_STUDIO.md) | [Content model](docs/CONTENT_MODEL.md) | [Privacy and data flow](docs/PRIVACY_AND_DATA_FLOW.md) |
| [Recommended plugins](docs/RECOMMENDED_PLUGINS.md) | [Design system](docs/DESIGN_SYSTEM.md) | [Upgrade and migration](docs/UPGRADE_AND_MIGRATION.md) |
| [Accessibility statement](docs/ACCESSIBILITY_STATEMENT.md) | [Development](docs/DEVELOPMENT.md) | [Testing and validation](docs/TESTING_AND_VALIDATION.md) |

## Security

Report vulnerabilities privately through [SECURITY.md](SECURITY.md) — never a public issue.

## Trademarks

WordPress is a trademark of the WordPress Foundation. WooCommerce and Elementor are
trademarks of their respective owners. MSP Nexus is not affiliated with or endorsed by any
of them.
