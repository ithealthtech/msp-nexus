import test from 'node:test';
import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { mkdtemp, rm, writeFile } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import path from 'node:path';
import { PackageDelivery } from '../src/infrastructure/package-delivery.js';
import { createDownloadGrant } from '../src/domain/signing.js';

const secret = '12345678901234567890123456789012';
const clock = () => new Date('2026-08-08T12:00:00.000Z');

test('package delivery binds grant, release record, path, and file hash', async (context) => {
  const root = await mkdtemp(path.join(tmpdir(), 'msp-nexus-package-'));
  context.after(() => rm(root, { recursive: true, force: true }));
  const bytes = Buffer.from('valid package fixture');
  await writeFile(path.join(root, 'theme.zip'), bytes);
  const sha256 = createHash('sha256').update(bytes).digest('hex');
  const delivery = new PackageDelivery({ root, grantSecret: secret, clock, releases: [{ product: 'msp-nexus', version: '1.0.0', file: 'theme.zip', sha256 }] });
  const grant = createDownloadGrant({ product: 'msp-nexus', version: '1.0.0', expires_at: clock().getTime() + 1000 }, secret);
  const result = await delivery.authorize({ product: 'msp-nexus', version: '1.0.0', grant });
  assert.equal(result.sha256, sha256);
  assert.equal(result.size, bytes.length);
});

test('package corruption fails closed', async (context) => {
  const root = await mkdtemp(path.join(tmpdir(), 'msp-nexus-package-'));
  context.after(() => rm(root, { recursive: true, force: true }));
  await writeFile(path.join(root, 'theme.zip'), 'tampered');
  const delivery = new PackageDelivery({ root, grantSecret: secret, clock, releases: [{ product: 'msp-nexus', version: '1.0.0', file: 'theme.zip', sha256: 'a'.repeat(64) }] });
  const grant = createDownloadGrant({ product: 'msp-nexus', version: '1.0.0', expires_at: clock().getTime() + 1000 }, secret);
  await assert.rejects(() => delivery.authorize({ product: 'msp-nexus', version: '1.0.0', grant }), /package_corrupt/);
});
