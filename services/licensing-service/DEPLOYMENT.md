# Deployment and operations

Run the service as an unprivileged Node.js 22+ process behind an HTTPS reverse proxy. Mount a private writable directory for SQLite and a read-only package directory. Supply a random license-key pepper, operator bearer token, webhook secret, Ed25519 signing private key, public package origin, database path, and package root through a managed secret/configuration system.

Never commit production keys. Restrict operator endpoints by authentication and network policy, rate-limit all non-health routes, back up the database and package catalog, monitor health/error/dead-letter/audit signals, rotate credentials with overlap, and rehearse database plus package restoration. Use PostgreSQL and a durable queue/object store adapter before horizontally scaling beyond a single SQLite writer.

Release procedure: build deterministic ZIPs, scan them, record hashes, publish a paused catalog record, verify compatibility on staging, sign with the offline/runtime key, enable a limited rollout, monitor failures, then expand. Withdrawal stops new downloads; it does not remove installed code. Rollback uses a previously signed compatible package and an explicit operator action.
