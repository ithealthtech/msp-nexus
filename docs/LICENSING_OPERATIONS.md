# Licensing service operations

## Deployment boundary

Deploy the licensing service independently from WordPress behind TLS, a reverse proxy, and managed network controls. Use a long-running Node 24 runtime, persistent SQLite only for controlled single-instance installations, or implement the documented repository interface with PostgreSQL before horizontally scaling. Package binaries belong in immutable object storage or a read-only mounted release directory. Never place a signing private key in WordPress or a public web root.

## Required production configuration

Production startup fails when `LICENSE_KEY_PEPPER`, `DOWNLOAD_GRANT_SECRET`, `MANIFEST_PRIVATE_KEY_PEM`, `OPERATOR_TOKEN`, `PUBLIC_ORIGIN`, or `PACKAGE_ROOT` is missing. Set `NODE_ENV=production`. Also configure `DATA_PATH`, package hashes/sizes, `COMMERCE_WEBHOOK_SECRET`, `ENTITLEMENT_CACHE_HOURS`, and `RATE_LIMIT_PER_MINUTE` as applicable. The complete example is `services/licensing-service/.env.example`.

Secrets must be at least 32 random bytes and stored in a managed secret store. Environment files are for local development only.

## Key rotation

1. Generate an Ed25519 signing key offline in the controlled release environment.
2. Add the new public key to WordPress packages as a trust-set entry before using the new private key. The current client exposes `msp_nexus_manifest_public_key`; a production release should embed the active raw public key in base64.
3. Deploy the trust-set release and verify adoption.
4. Switch the service signing private key.
5. Retain the prior verification key through the maximum entitlement-cache, update-manifest, and rollback window.
6. Revoke the prior key and record the rotation in the operator audit log.

Rotate license peppers through a versioned multi-pepper verifier; do not replace the only pepper without rehashing customer keys during successful validation. Rotate download-grant and webhook secrets with a short overlap window and explicit version identifier.

## Backup and restore

- Quiesce writes or use a SQLite online backup while WAL mode is enabled; copying only the main file during active writes is unsafe.
- Back up the database, release metadata, immutable package objects, audit retention store, public verification keys, encrypted private-key escrow, and configuration inventory.
- Encrypt backups, restrict restore rights, and test restoration quarterly.
- After restoration, validate schema migrations, license counts, activation counts, webhook replay records, dead-letter records, release hashes, and an activation/update/download contract flow.
- Restoring a WordPress site to a disaster-recovery URL uses activation transfer; never silently create an unintended production seat.

## Monitoring and alerts

Monitor availability, latency and status codes by route; activation/validation success; signature failures; package hash failures; grant denials; rate-limit volume; webhook lag/failures/dead letters; database size/locks; audit-write errors; active/grace/expired distribution; release rollout errors; and disk/storage health. Do not place license keys, portal tokens, message bodies, full IP addresses, or customer content in telemetry.

Alert immediately on manifest-signature errors, package corruption, signing-key access anomalies, webhook replay spikes, audit persistence failure, or a material increase in 5xx responses. Health checks establish liveness only; a separate authenticated readiness probe should validate persistence and package storage.

## Incident runbook

1. Pause affected releases using the operator release-status endpoint.
2. Preserve logs and audit records; do not expose secrets in the incident channel.
3. If a package is suspect, withdraw it, revoke its grants through secret rotation if necessary, publish an advisory, and prepare a signed emergency release.
4. If a signing key may be compromised, stop manifest issuance, begin the rotation plan, and do not weaken client verification.
5. If the service is unavailable, restore service while WordPress sites continue rendering from installed code. Signed entitlement caches provide a bounded administrative grace state.
6. Reconcile webhook dead letters and activation/audit events after recovery.
7. Document impact, timeline, root cause, corrective actions, and required customer communication.

## Rate and abuse controls

The built-in single-instance limiter defaults to 60 requests per client-route per minute and returns `429` with `Retry-After`. Production reverse proxies should add global/distributed limits, connection limits, request-body limits, bot/DDoS controls, and trusted-proxy address handling. Webhook signatures allow five minutes of clock skew; event IDs are persisted to prevent duplicate side effects. Package grants expire after five minutes and are bound to product, version, license, and site.

## Data retention and deletion

Define jurisdiction-appropriate retention before launch. A recommended baseline is active customer/license/order records for the contractual relationship, security/audit events for 12–24 months, failed webhook payloads for at most 30 days, and operational logs for 30–90 days. Customer deletion requests must verify identity, preserve records required for tax/security/legal obligations, delete or anonymize other personal fields, revoke portal tokens, and produce an auditable result without deleting aggregate non-identifying integrity records.
