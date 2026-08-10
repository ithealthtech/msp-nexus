# Privacy and data-flow inventory

| Flow | Data | Purpose | Retention/controls |
|---|---|---|---|
| License activation/heartbeat | License identifier, normalized site origin, random instance ID, multisite flag, product/version/channel | Entitlement and activation limits | Service policy; raw keys hashed, transport requires HTTPS |
| Update check/download | Above plus installed version; short-lived signed grant | Discover and authorize verified packages | No credentials in package URL; no-store responses |
| Commerce webhook | Provider event ID/type and entitlement fields | Issue, renew, suspend, cancel, or change a plan | Signature, clock-skew, replay, audit, retry/dead-letter controls |
| Consultation form | Contact details, organization, message, consent, request metadata | Respond to an explicit inquiry | Stored only by the selected handler/integration; site owner defines retention and rights handling |
| Diagnostics/support bundle | Environment versions, redacted license/import/update states | Troubleshooting | Administrator-initiated download; excludes raw license keys and secrets |
| Consent preferences | Essential/analytics/marketing/preferences booleans and timestamp in browser local storage and a first-party cookie | Remember visitor choices and activate category-marked scripts | Configurable up to 365 days; visitors can reopen choices with the preferences shortcode; no server-side identity log |
| Browser-local wishlist | WooCommerce product IDs in browser local storage | Remember products on the current browser without requiring an account | Visitor-controlled browser storage; no server synchronization by default |
| Starter catalog | Search/filter parameters sent to an authenticated first-party REST route | Browse the local 800-kit catalog | No remote transmission; administrator-only; no persistent search history |

The default theme has no analytics or advertising tracker. External fonts are not required. Optional scripts can be held as `type="text/plain"` with `data-msp-consent="analytics|marketing|preferences"` until the matching choice is granted.

Customer operators remain responsible for notices, legal bases, consent configuration, vendor agreements, rights handling, deletion schedules, and cross-border transfers. Production secrets belong in a secret manager or environment configuration, never theme files or source control.
