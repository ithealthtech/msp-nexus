# MSP Nexus licensing service

The service is an independent entitlement and update-control plane. It implements key hashing, normalized and idempotent domain activations, production/staging limits, multisite policy, expiry/grace/cancellation/suspension/revocation, renewal and plan changes, customer-authorized domain transfer and data requests, signed update manifests, rollout channels, short-lived scoped download grants, a persisted release catalog, replay-safe commerce webhooks with dead-letter retry, and immutable audit events. Local state is persisted in SQLite with WAL mode and versioned migrations; tests use the interchangeable memory adapter.

Start locally:

```powershell
$env:LICENSE_KEY_PEPPER='replace-with-at-least-32-random-characters'
npm start --workspace services/licensing-service
```

The server may print development bootstrap credentials in local mode. Never use development defaults in production. The supplied portals are deliberately small authenticated operational surfaces; production commerce checkout, invoice rendering, tax, identity, and payment-card handling remain the responsibility of the selected commerce/identity providers rather than this service.
