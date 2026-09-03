# Security policy

## Supported versions

| Version | Supported |
| --- | --- |
| 0.4.x | Yes |
| Earlier 0.x | No |

MSP Nexus is pre-1.0. Only the latest minor release receives security fixes.

## Reporting a vulnerability

Report privately. Do **not** open a public issue for a security defect.

Use [GitHub private vulnerability reporting](https://github.com/ithealthtech/msp-nexus/security/advisories/new).

Please include:

- The MSP Nexus version, and whether the theme, companion plugin, child theme, or
  licensing service is affected.
- WordPress and PHP versions, and whether the site is single-site or multisite.
- What the safeguard was supposed to prevent, and what happened instead.
- Reproduction steps.

Never include license keys, portal tokens, signing keys, customer content, or full IP
addresses in a report.

We will acknowledge within 5 business days and aim to give a remediation timeline within
15 business days.

## In scope

**WordPress packages**

- Privilege escalation through Nexus Studio, dynamic blocks, conditions, or onboarding.
- Stored or reflected XSS through block attributes, dynamic data, or white-label fields.
- Unauthenticated access to editor-only or admin-only capability.
- Accepting an update manifest whose Ed25519 signature does not verify, or a package whose
  hash does not match.
- Any path that would let a site trust a signing key outside the published trust set.

**Licensing service**

- Bypassing entitlement or activation checks.
- Forging, replaying, or extending a download grant beyond its five-minute, product,
  version, license, and site binding.
- Webhook signature bypass, or replay causing duplicate side effects.
- Leaking license keys, peppers, operator tokens, or signing material through a response,
  log, or telemetry.
- Operator endpoint access without a valid operator token.

## Not vulnerabilities

These are documented design decisions.

- **Sites keep rendering when the licensing service is unreachable.** Signed entitlement
  caches provide a bounded administrative grace state. An outage in licensing
  infrastructure is not permitted to take a client's public site down.
- **Distributed WordPress code is GPL-2.0-or-later.** Commercial controls apply to the
  separately provided service and delivery, not to the code itself. Redistribution
  permitted by the GPL is not a security issue.
- **Persistent SQLite in the licensing service is single-instance only.** Scaling it
  horizontally without implementing the documented repository interface against PostgreSQL
  is a deployment error, not a product defect.
- **Environment files are development-only.** Secrets belong in a managed secret store and
  must be at least 32 random bytes.

## Operator responsibilities

- Never place a signing private key in WordPress or a public web root.
- Rotate signing keys through the trust-set procedure: publish the new public key to sites
  *before* signing with the new private key, and retain the prior verification key through
  the maximum entitlement-cache, manifest, and rollback window.
- Rotate license peppers through a versioned multi-pepper verifier. Replacing the only
  pepper without rehashing customer keys during successful validation will invalidate them.
- Deploy the licensing service behind TLS, a reverse proxy, and managed network controls,
  with distributed rate limiting above the built-in single-instance limiter.
- Keep license keys, portal tokens, message bodies, full IP addresses, and customer content
  out of telemetry.

Operational detail, monitoring signals, and the incident runbook are in
[docs/LICENSING_OPERATIONS.md](docs/LICENSING_OPERATIONS.md). Product security architecture
is in [docs/SECURITY.md](docs/SECURITY.md); data handling in
[docs/PRIVACY_AND_DATA_FLOW.md](docs/PRIVACY_AND_DATA_FLOW.md).

## Disclosure

We prefer coordinated disclosure and will credit you in the release notes unless you ask us
not to.
