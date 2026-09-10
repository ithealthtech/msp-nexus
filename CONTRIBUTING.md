# Contributing

MSP Nexus is a clean-room implementation. That constraint shapes how contributions are
reviewed, so please read this before opening a pull request.

## The clean-room rule

Reference products were studied **only** to understand capability categories, packaging
conventions, operational safeguards, and commercial expectations. No proprietary source,
visual design, copy, trademarks, or update endpoints are reused.

A contribution must not introduce:

- Code, markup, or CSS copied or adapted from another commercial theme or plugin.
- Visual designs or copy traced from a reference product.
- Third-party trademarks in names, slugs, class names, or user-facing text.
- Update, licensing, or telemetry endpoints belonging to another vendor.

If you looked at another product while solving a problem, say so in the pull request and
describe what you took from it — the answer should be a capability idea, not an
implementation.

## Before you start

- Security defects go through [SECURITY.md](SECURITY.md) — a private advisory, never a
  public issue.
- Open an issue first for anything beyond a doc fix or a contained bug fix.
- Read [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) and
  [docs/DEVELOPER_AND_HOOKS.md](docs/DEVELOPER_AND_HOOKS.md).

## Setup

Node.js 22 or newer is required for tooling. Node is not needed to run MSP Nexus, only to
build, validate, and package it.

```powershell
npm install
npm run check
npm run package
npm run qa:php74
```

## Before opening a pull request

```powershell
npm run check
```

`npm run check` runs, in order: structure, JSON, PHP, JS, block-markup, and security checks,
then lint (JS, CSS, HTML), the design check, and the service check. All of it must pass.

For changes touching packaged output or the PHP floor:

```powershell
npm run package
npm run qa:php74
```

`qa:php74` verifies packaged single-site and multisite installs on PHP 7.4.33 through
official WordPress Playground blueprints — no native PHP or Docker required.

## Platform rules

1. **WordPress-native first.** Use block APIs, semantic HTML, progressive enhancement, and
   server-side registration from `block.json`. Do not reach for a framework where a core
   API exists.
2. **Respect the compatibility floor.** WordPress 6.7 minimum, PHP 7.4.33 minimum. A change
   that requires newer syntax needs a deliberate decision, not an accident.
3. **Never break the child theme contract.** Customization-safe means an update to the
   parent must not destroy work done in `msp-nexus-child`.
4. **The plugin owns behavior, the theme owns presentation.** Nexus Studio, the content
   model, conditions, dynamic data, and the license client live in `msp-nexus-core`.
5. **Never weaken update verification.** Ed25519 manifest signatures and package hash checks
   are the boundary that stops arbitrary code reaching a client site. Do not add a bypass,
   a debug skip, or a "temporary" fallback.
6. **Keep secrets out of the tree.** The security check scans `packages`, `services`,
   `scripts`, and `docs` for private keys and credential-shaped strings.

## Licensing of contributions

Distributed WordPress code is GPL-2.0-or-later. By contributing to `packages/`, you agree
your contribution is licensed under GPL-2.0-or-later.

Commercial entitlement controls apply only to separately provided services and delivery —
see [docs/COMMERCIAL_ENTITLEMENT_TERMS_TEMPLATE.md](docs/COMMERCIAL_ENTITLEMENT_TERMS_TEMPLATE.md).

## Commits and pull requests

- Imperative subject line, under ~72 characters.
- Explain *why* in the body; the diff already shows what.
- One logical change per pull request.
- State whether the change touches update verification, the licensing service, or the
  compatibility floor.
- Update [CHANGELOG.md](CHANGELOG.md) for user-visible changes.
- Record requirement-level evidence in
  [docs/REQUIREMENTS_TRACEABILITY.md](docs/REQUIREMENTS_TRACEABILITY.md) where applicable.

## Documentation

`docs/` holds the detailed reference. `pages/` is the published product site at
<https://ithealthtech.github.io/msp-nexus/>. User-visible behavior changes should update
both.
