# Security model

## WordPress

- Admin actions require the narrowest relevant capability and a nonce.
- Settings, REST input, import manifests, URLs, metadata, and remote responses are validated before storage or use.
- HTML is escaped at its output context; imported block content is restricted with WordPress KSES.
- Remote requests use safe URL handling, JSON content types, HTTPS production defaults, and bounded timeouts.
- Raw license keys are never persisted. Logs and diagnostics expose only redacted identifiers.
- Public content does not depend on license-service availability.
- Update manifests use Ed25519. Downloads use short-lived grants and are checked against a signed SHA-256 hash and expected ZIP identity before extraction.
- Missing Sodium or ZipArchive support fails closed for commercial package installation.

## Licensing service

- License keys are random high-entropy values and only peppered SHA-256 hashes are stored.
- Domain origins are normalized before comparison; paths and default ports do not create additional activations.
- Staging and multisite behavior are explicit entitlement policies.
- Package grants expire after five minutes and are bound to product, version, license, and normalized site.
- Operator and customer authentication, CSRF protection, rate limiting, key rotation, PostgreSQL deployment, object storage, backup, monitoring, and incident runbooks must be completed and verified before public production deployment.

## Secret handling

Runtime secrets belong in a managed secret store. `.env` files, signing private keys, database files, customer exports, production packages, and raw license keys must never be committed. The release signing private key must not be present in WordPress packages or ordinary build workers.
