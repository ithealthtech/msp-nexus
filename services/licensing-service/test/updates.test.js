import test from 'node:test';
import assert from 'node:assert/strict';
import { generateKeyPairSync } from 'node:crypto';
import { LicensingEngine, hashLicenseKey } from '../src/domain/licensing.js';
import { UpdateService } from '../src/domain/updates.js';
import { createDownloadGrant, verifyDownloadGrant, verifyManifest } from '../src/domain/signing.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';

const pepper = '12345678901234567890123456789012';
const key = 'MSPN-ABCDEF-GHIJKL-MNOPQR-STUVWX';
const clock = () => new Date('2026-08-08T12:00:00.000Z');

async function setup() {
  const keys = generateKeyPairSync('ed25519');
  const repository = new MemoryRepository([{
    id: 'license-1', keyHash: hashLicenseKey(key, pepper), status: 'active', plan: 'business', expiresAt: '2027-01-01T00:00:00.000Z', graceDays: 14,
    activationLimit: 1, allowFreeStaging: true, allowMultisite: false, entitlements: ['theme_updates'], activations: []
  }]);
  const licensing = new LicensingEngine({ repository, pepper, clock });
  await licensing.activate({ key, siteUrl: 'https://example.com', instanceId: 'instance-1' });
  const updates = new UpdateService({ licensing, privateKey: keys.privateKey, grantSecret: pepper, publicOrigin: 'https://licenses.example.test', clock,
    releases: [
      { product: 'msp-nexus', productType: 'theme', slug: 'msp-nexus', channel: 'stable', version: '1.2.0', requiresWordPress: '6.7', testedWordPress: '7.0', requiresPhp: '7.4.33', sha256: 'abc123', size: 42, changelogUrl: 'https://example.test/changelog', rollbackVersion: '1.1.0' },
      { product: 'msp-nexus', productType: 'theme', slug: 'msp-nexus', channel: 'stable', version: '1.1.0', requiresWordPress: '6.7', testedWordPress: '7.0', requiresPhp: '7.4.33', sha256: 'def456', size: 40, changelogUrl: 'https://example.test/changelog/1.1.0' }
    ]
  });
  return { updates, keys };
}

test('update manifest is signed and bound to an entitled activation', async () => {
  const { updates, keys } = await setup();
  const result = await updates.check({ licenseId: 'license-1', siteUrl: 'https://example.com', instanceId: 'instance-1', product: 'msp-nexus' });
  assert.equal(result.manifest.version, '1.2.0');
  assert.equal(result.manifest.rollback_version, '1.1.0');
  assert.match(result.manifest.rollback_package_url, /\/v1\/packages\/msp-nexus\/1\.1\.0/);
  assert.equal(verifyManifest(result.manifest, result.signature, keys.publicKey), true);
});

test('manifest tampering invalidates its signature', async () => {
  const { updates, keys } = await setup();
  const result = await updates.check({ licenseId: 'license-1', siteUrl: 'https://example.com', instanceId: 'instance-1', product: 'msp-nexus' });
  result.manifest.version = '99.0.0';
  assert.equal(verifyManifest(result.manifest, result.signature, keys.publicKey), false);
});

test('download grants reject expiry and tampering', () => {
  const grant = createDownloadGrant({ product: 'msp-nexus', expires_at: 1000 }, pepper);
  assert.throws(() => verifyDownloadGrant(grant, pepper, 1001), /expired_download_grant/);
  assert.throws(() => verifyDownloadGrant(`${grant}x`, pepper, 1), /invalid_download_grant/);
});
