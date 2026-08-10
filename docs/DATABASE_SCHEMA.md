# Licensing database schema and migrations

The local adapter uses SQLite with foreign keys, WAL mode, a five-second busy timeout, and monotonic migrations recorded in `schema_migrations`.

## Current tables

- `licenses` — hashed key plus a versionable license/activation document. Raw license keys and portal tokens are never stored.
- `audit_events` — append-only operator and entitlement event documents ordered by an integer sequence.
- `webhook_events` — durable commerce event IDs, status, and receipt metadata for replay resistance.
- `webhook_dead_letters` — failed event payloads, bounded retry metadata, and failure reason.
- `schema_migrations` — applied migration versions and timestamps.

Migration 1 creates license and audit persistence. Migration 2 creates replay and dead-letter persistence. Migrations are idempotent and run before the service listens.

## Production relational model

Before a multi-instance commercial launch, normalize the repository into tables for organizations, users, products, plans, releases, packages, customers, orders, subscriptions, licenses, entitlements, sites, activations, portal sessions, webhook events, dead letters, invoices/receipts references, data requests, and audit events. Use immutable IDs, explicit foreign keys, optimistic concurrency/version columns, UTC timestamps, encrypted sensitive fields, unique event/idempotency keys, and partial indexes for active activations. Package and signing duties must remain separated from customer-support roles.

Every migration must supply forward verification, resumable batches for large rewrites, idempotency, a tested rollback or roll-forward plan, and a supported-version matrix. Never edit an already shipped migration.
