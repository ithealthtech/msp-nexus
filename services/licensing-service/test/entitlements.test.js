import test from 'node:test';
import assert from 'node:assert/strict';
import { generateKeyPairSync } from 'node:crypto';
import { SignedEntitlements } from '../src/domain/entitlements.js';
import { verifyManifest } from '../src/domain/signing.js';

test('cached entitlement envelope is signed with a bounded lifetime', () => {
  const keys = generateKeyPairSync('ed25519');
  const clock = () => new Date('2026-08-08T12:00:00.000Z');
  const service = new SignedEntitlements({ privateKey: keys.privateKey, clock, cacheHours: 24 });
  const result = service.envelope({ license_id: 'one', activation_id: 'activation', site_url: 'https://example.com', status: 'active', plan: 'business', expires_at: '2027-01-01T00:00:00.000Z', grace_ends_at: '2027-01-15T00:00:00.000Z', entitlements: ['theme_updates'] });
  assert.equal(verifyManifest(result.signed_entitlement, result.entitlement_signature, keys.publicKey), true);
  assert.equal(result.signed_entitlement.cache_expires_at, '2026-08-09T12:00:00.000Z');
});
