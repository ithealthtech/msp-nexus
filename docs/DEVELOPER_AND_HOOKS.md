# Developer and hooks guide

Use the child theme for presentation overrides and a site plugin for business logic. Do not edit production package files directly.

## WordPress extension points

- `msp_nexus_schema_enabled` filters whether the built-in conservative schema renderer runs.
- `msp_nexus_license_endpoint` changes the entitlement API origin.
- `msp_nexus_update_endpoint` changes the signed-update API origin.
- `msp_nexus_manifest_public_key` supplies the production Ed25519 public key.
- `msp_nexus_update_channel` selects `stable`, `beta`, or `dev` after administrator consent.
- `msp_nexus_consultation_submission` fires after a validated native form submission and is the integration seam for CRM, ticketing, calendaring, or a generic webhook adapter.

The block-binding source `msp-nexus/settings` exposes only an allowlist of non-secret organization fields. Bind with `metadata.bindings`, source `msp-nexus/settings`, and an `args.field` value such as `announcement`, `cta_url`, or `support_phone`.

Dynamic blocks are registered from `block.json`; their render files are authoritative for front-end output and no-JavaScript fallbacks. Custom REST-visible post metadata should follow the existing registration pattern with a narrow capability callback and type-appropriate sanitizer.
