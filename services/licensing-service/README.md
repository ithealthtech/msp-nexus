# MSP Nexus licensing service

The service is an independent entitlement and update-control plane. It implements key hashing, normalized and idempotent domain activations, production/staging limits, multisite policy, expiry/grace/cancellation/suspension/revocation, renewal and plan changes, customer-authorized domain transfer and data requests, signed update manifests, rollout channels, short-lived scoped download grants, a persisted release catalog, replay-safe commerce webhooks with dead-letter retry, and immutable audit events. Local state is persisted in SQLite with WAL mode and versioned migrations; tests use the interchangeable memory adapter.

Start locally:

```powershell
$env:LICENSE_KEY_PEPPER='replace-with-at-least-32-random-characters'
npm start --workspace services/licensing-service
```

The server may print development bootstrap credentials in local mode. Never use development defaults in production. The supplied portals are deliberately small authenticated operational surfaces; production commerce checkout, invoice rendering, tax, identity, and payment-card handling remain the responsibility of the selected commerce/identity providers rather than this service.

## Operator console and customer portal

Open `/operator` in a browser and sign in with the `OPERATOR_TOKEN`. The console covers day-to-day licensing without the API:

- **Dashboard**: active licenses, production sites in use, licenses expiring in 30 days or lapsed, and recent activity.
- **Licenses**: search by customer, email, plan, site, or ID; filter by status.
- **License detail**: renew, change plan and site limit, edit customer details and private notes, suspend, cancel, revoke, or reinstate; see every site with its last check-in, move a site to a new address, or deactivate it to free the slot.
- **New license**: issues a key (five readable groups, no look-alike characters) and a customer portal token, each shown once with a copy button.
- **Releases**: publish, pause, or withdraw releases, and add new ones from the release manifest's SHA-256 and size.
- **Activity**: the audit trail, newest first.

Customers sign in at `/portal` with their portal token to see their plan and sites, move a site after a domain change, download their data, and send privacy requests. `/customer` redirects there. Operator notes are never shown to customers.

Sessions use an HttpOnly, SameSite=Strict cookie (`__Host-` prefixed and Secure when `NODE_ENV=production` or `PUBLIC_ORIGIN` is https), expire after 8 idle hours or 24 hours in total, and live in memory, so a restart signs everyone out. Every change needs the session's CSRF token, cross-host origins are rejected, and five failed sign-ins in 15 minutes lock that client out. Pages use a strict content security policy with no inline script or style.

Preview the console locally with in-memory data and a throwaway operator key printed to the log:

```powershell
npm run dev:console --workspace services/licensing-service
```
