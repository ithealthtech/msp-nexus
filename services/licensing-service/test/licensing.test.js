import test from 'node:test';
import assert from 'node:assert/strict';
import { LicensingEngine, hashLicenseKey } from '../src/domain/licensing.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';

const pepper = '12345678901234567890123456789012';
const key = 'MSPN-ABCDEF-GHIJKL-MNOPQR-STUVWX';
const now = new Date('2026-08-08T12:00:00.000Z');

function fixture(overrides = {}) {
  const license = {
    id: 'license-1', keyHash: hashLicenseKey(key, pepper), status: 'active', plan: 'business',
    expiresAt: '2026-09-01T00:00:00.000Z', graceDays: 14, activationLimit: 1,
    allowFreeStaging: true, allowMultisite: false, entitlements: ['theme_updates'], activations: [], ...overrides
  };
  const repository = new MemoryRepository([license]);
  return { repository, engine: new LicensingEngine({ repository, pepper, clock: () => new Date(now) }) };
}

test('activation is normalized and idempotent', async () => {
  const { engine, repository } = fixture();
  const first = await engine.activate({ key, siteUrl: 'HTTPS://Example.COM/path', instanceId: 'one' });
  const second = await engine.activate({ key, siteUrl: 'https://example.com/', instanceId: 'one' });
  assert.equal(first.activation_id, second.activation_id);
  assert.equal((await repository.getLicense('license-1')).activations.length, 1);
});

test('staging activation does not consume production allowance', async () => {
  const { engine } = fixture();
  await engine.activate({ key, siteUrl: 'https://staging.example.com', instanceId: 'stage' });
  const production = await engine.activate({ key, siteUrl: 'https://example.com', instanceId: 'prod' });
  assert.equal(production.status, 'active');
});

test('production activation limit is enforced', async () => {
  const { engine } = fixture();
  await engine.activate({ key, siteUrl: 'https://one.example.com', instanceId: 'one' });
  await assert.rejects(() => engine.activate({ key, siteUrl: 'https://two.example.com', instanceId: 'two' }), /activation_limit_reached/);
});

test('expiry enters grace then expires', async () => {
  const graceFixture = fixture({ expiresAt: '2026-08-01T00:00:00.000Z' });
  const grace = await graceFixture.engine.activate({ key, siteUrl: 'https://example.com', instanceId: 'one' });
  assert.equal(grace.status, 'grace');
  const expiredFixture = fixture({ expiresAt: '2026-07-01T00:00:00.000Z' });
  await assert.rejects(() => expiredFixture.engine.activate({ key, siteUrl: 'https://example.com', instanceId: 'one' }), /license_expired/);
});

test('multisite requires an explicit entitlement', async () => {
  const { engine } = fixture();
  await assert.rejects(() => engine.activate({ key, siteUrl: 'https://network.example.com', instanceId: 'network', isMultisite: true }), /multisite_not_entitled/);
});

test('deactivation releases the allowance', async () => {
  const { engine } = fixture();
  await engine.activate({ key, siteUrl: 'https://one.example.com', instanceId: 'one' });
  await engine.deactivate({ licenseId: 'license-1', siteUrl: 'https://one.example.com', instanceId: 'one' });
  const next = await engine.activate({ key, siteUrl: 'https://two.example.com', instanceId: 'two' });
  assert.equal(next.status, 'active');
});

test('operator issuance returns secrets once and customer token resolves a redacted summary', async () => {
  const { engine } = fixture();
  const issued = await engine.issue({ plan: 'agency', expiresAt: '2027-08-08T00:00:00.000Z', activationLimit: 3, entitlements: ['theme_updates'] });
  assert.match(issued.license_key, /^MSPN-/);
  assert.ok(issued.portal_access_token.length > 20);
  const summary = await engine.customerSummary(issued.portal_access_token);
  assert.equal(summary[0].plan, 'agency');
  assert.equal('keyHash' in summary[0], false);
});

test('customer token authorizes export scope, domain transfer, and privacy requests', async () => {
  const { engine, repository } = fixture();
  const issued = await engine.issue({ plan: 'agency', expiresAt: '2027-08-08T00:00:00.000Z', activationLimit: 3 });
  const activation = await engine.activate({ key: issued.license_key, siteUrl: 'https://old.customer.example', instanceId: 'customer-one' });
  const moved = await engine.customerTransfer({ portalToken: issued.portal_access_token, licenseId: issued.license.id, activationId: activation.activation_id, siteUrl: 'https://new.customer.example/path' });
  assert.equal(moved.site_url, 'https://new.customer.example');
  const request = await engine.requestDataAction({ portalToken: issued.portal_access_token, action: 'export', note: 'Customer requested a portable copy.' });
  assert.equal(request.status, 'received');
  assert.ok((await repository.listAudit()).some((event) => event.action === 'privacy.requested'));
  await assert.rejects(() => engine.customerTransfer({ portalToken: 'wrong-token', licenseId: issued.license.id, activationId: activation.activation_id, siteUrl: 'https://denied.example' }), /portal_access_denied/);
});

test('operator can suspend a license and transfer an activation', async () => {
  const { engine } = fixture({ activationLimit: 2 });
  const active = await engine.activate({ key, siteUrl: 'https://old.example.com', instanceId: 'one' });
  const moved = await engine.transfer({ licenseId: 'license-1', activationId: active.activation_id, siteUrl: 'https://new.example.com/path' });
  assert.equal(moved.site_url, 'https://new.example.com');
  const suspended = await engine.setStatus({ licenseId: 'license-1', status: 'suspended' });
  assert.equal(suspended.status, 'suspended');
});

test('operator can renew, change plan, cancel, and restore a license', async () => {
  const { engine } = fixture();
  const renewed = await engine.renew({ licenseId: 'license-1', expiresAt: '2027-09-01T00:00:00.000Z' });
  assert.equal(renewed.expires_at, '2027-09-01T00:00:00.000Z');
  const upgraded = await engine.changePlan({ licenseId: 'license-1', plan: 'agency', activationLimit: 5, entitlements: ['theme_updates', 'priority_support'] });
  assert.equal(upgraded.plan, 'agency');
  assert.equal(upgraded.activation_limit, 5);
  assert.equal((await engine.setStatus({ licenseId: 'license-1', status: 'cancelled' })).status, 'cancelled');
  assert.equal((await engine.setStatus({ licenseId: 'license-1', status: 'active' })).status, 'active');
});
