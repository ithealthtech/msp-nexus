import { signManifest } from './signing.js';

export class SignedEntitlements {
  constructor({ privateKey, clock = () => new Date(), cacheHours = 24 }) {
    this.privateKey = privateKey;
    this.clock = clock;
    this.cacheHours = Math.min(168, Math.max(1, cacheHours));
  }

  envelope(entitlement) {
    const signed = {
      schema: 1,
      license_id: entitlement.license_id,
      activation_id: entitlement.activation_id,
      site_url: entitlement.site_url,
      status: entitlement.status,
      plan: entitlement.plan,
      expires_at: entitlement.expires_at,
      grace_ends_at: entitlement.grace_ends_at,
      entitlements: [...entitlement.entitlements],
      activation_limit: entitlement.activation_limit,
      active_production_activations: entitlement.active_production_activations,
      active_staging_activations: entitlement.active_staging_activations,
      is_staging: entitlement.is_staging,
      allow_multisite: entitlement.allow_multisite,
      issued_at: this.clock().toISOString(),
      cache_expires_at: new Date(this.clock().getTime() + this.cacheHours * 3600000).toISOString()
    };
    return { ...entitlement, signed_entitlement: signed, entitlement_signature: signManifest(signed, this.privateKey), signature_algorithm: 'Ed25519' };
  }
}
