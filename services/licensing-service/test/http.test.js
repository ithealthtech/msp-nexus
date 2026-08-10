import test from 'node:test';
import assert from 'node:assert/strict';
import { createServer } from 'node:http';
import { LicensingEngine } from '../src/domain/licensing.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';
import { createHandler } from '../src/http.js';

const pepper = '12345678901234567890123456789012';

async function serverFixture() {
  const engine = new LicensingEngine({ repository: new MemoryRepository(), pepper });
  const server = createServer(createHandler({ engine, updates: { check: async () => ({}) }, operatorToken: 'operator-test-token' }));
  await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
  const address = server.address();
  return { server, origin: `http://127.0.0.1:${address.port}` };
}

test('health and authenticated operator issuance contract', async (context) => {
  const { server, origin } = await serverFixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const health = await fetch(`${origin}/health`);
  assert.equal(health.status, 200);
  assert.deepEqual(await health.json(), { status: 'ok' });

  const denied = await fetch(`${origin}/v1/operator/licenses`);
  assert.equal(denied.status, 401);

  const issued = await fetch(`${origin}/v1/operator/licenses`, {
    method: 'POST',
    headers: { authorization: 'Bearer operator-test-token', 'content-type': 'application/json' },
    body: JSON.stringify({ plan: 'business', expires_at: '2027-01-01T00:00:00.000Z', activation_limit: 2, entitlements: ['theme_updates'] })
  });
  assert.equal(issued.status, 200);
  const payload = await issued.json();
  assert.match(payload.license_key, /^MSPN-/);

  const listing = await fetch(`${origin}/v1/operator/licenses`, { headers: { authorization: 'Bearer operator-test-token' } });
  assert.equal(listing.status, 200);
  assert.equal((await listing.json()).licenses.length, 1);
});

test('malformed JSON is rejected without leaking internals', async (context) => {
  const { server, origin } = await serverFixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const response = await fetch(`${origin}/v1/licenses/activate`, { method: 'POST', headers: { 'content-type': 'application/json' }, body: '{not-json' });
  assert.equal(response.status, 400);
  assert.equal((await response.json()).error, 'invalid_json');
});
