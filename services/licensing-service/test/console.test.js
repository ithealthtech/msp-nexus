import test from 'node:test';
import assert from 'node:assert/strict';
import { createServer } from 'node:http';
import { URLSearchParams } from 'node:url';
import { LicensingEngine } from '../src/domain/licensing.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';
import { createHandler } from '../src/http.js';
import { LoginThrottle, SessionStore } from '../src/console/sessions.js';

const pepper = '12345678901234567890123456789012';
const operatorToken = 'operator-console-test-token-0123456789';

async function fixture(options = {}) {
  const engine = new LicensingEngine({ repository: new MemoryRepository(), pepper });
  const server = createServer(createHandler({ engine, updates: { check: async () => ({}) }, operatorToken, ...options }));
  await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
  const origin = `http://127.0.0.1:${server.address().port}`;
  return { engine, server, origin };
}

const form = (fields) => new URLSearchParams(fields).toString();

async function signIn(origin, path, secret) {
  const response = await fetch(`${origin}${path}/session`, { method: 'POST', redirect: 'manual', headers: { 'content-type': 'application/x-www-form-urlencoded' }, body: form({ secret }) });
  const setCookie = response.headers.get('set-cookie') ?? '';
  return { response, cookie: setCookie.split(';')[0], setCookie };
}

async function csrfFrom(origin, path, cookie) {
  const html = await (await fetch(`${origin}${path}`, { headers: { cookie } })).text();
  return html.match(/name="csrf-token" content="([^"]+)"/)?.[1] ?? '';
}

test('operator console: sign-in page, wrong key, then a hardened session cookie', async (context) => {
  const { server, origin } = await fixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));

  const page = await fetch(`${origin}/operator`);
  assert.equal(page.status, 200);
  const csp = page.headers.get('content-security-policy');
  assert.match(csp, /script-src 'self'/);
  assert.doesNotMatch(csp, /unsafe-inline/);
  assert.equal(page.headers.get('x-frame-options'), 'DENY');
  assert.match(await page.text(), /Operator access key/);

  const wrong = await signIn(origin, '/operator', 'not-the-token');
  assert.equal(wrong.response.status, 401);
  assert.equal(wrong.setCookie, '');

  const right = await signIn(origin, '/operator', operatorToken);
  assert.equal(right.response.status, 303);
  assert.equal(right.response.headers.get('location'), '/operator');
  assert.match(right.setCookie, /HttpOnly/);
  assert.match(right.setCookie, /SameSite=Strict/);
  assert.match(right.setCookie, /^nx_op=/);

  const app = await fetch(`${origin}/operator`, { headers: { cookie: right.cookie } });
  const html = await app.text();
  assert.match(html, /name="csrf-token"/);
  assert.match(html, /operator\.js/);
});

test('cookie sessions read freely but every change needs the CSRF token and a same-host origin', async (context) => {
  const { server, origin } = await fixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const { cookie } = await signIn(origin, '/operator', operatorToken);
  const csrf = await csrfFrom(origin, '/operator', cookie);
  assert.ok(csrf);

  const list = await fetch(`${origin}/v1/operator/licenses`, { headers: { cookie } });
  assert.equal(list.status, 200);

  const body = JSON.stringify({ plan: 'business', expires_at: '2030-01-01T00:00:00.000Z', customer_name: 'Acme IT', customer_email: 'owner@acme.test' });
  const noToken = await fetch(`${origin}/v1/operator/licenses`, { method: 'POST', headers: { cookie, 'content-type': 'application/json' }, body });
  assert.equal(noToken.status, 403);
  assert.equal((await noToken.json()).error, 'csrf_failed');

  const foreign = await fetch(`${origin}/v1/operator/licenses`, { method: 'POST', headers: { cookie, 'content-type': 'application/json', 'x-csrf-token': csrf, origin: 'https://evil.example' }, body });
  assert.equal(foreign.status, 403);

  const issued = await fetch(`${origin}/v1/operator/licenses`, { method: 'POST', headers: { cookie, 'content-type': 'application/json', 'x-csrf-token': csrf, origin }, body });
  assert.equal(issued.status, 200);
  const payload = await issued.json();
  assert.equal(payload.license.customer_name, 'Acme IT');
  assert.equal(payload.license.customer_email, 'owner@acme.test');
  assert.match(payload.license_key, /^MSPN-/);
});

test('operator can edit customer details, deactivate one site, and read the audit trail', async (context) => {
  const { engine, server, origin } = await fixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const issued = await engine.issue({ plan: 'business', expiresAt: '2030-01-01T00:00:00.000Z', activationLimit: 2 });
  await engine.activate({ key: issued.license_key, siteUrl: 'https://client.example.com', instanceId: 'site-1' });
  const auth = { authorization: `Bearer ${operatorToken}`, 'content-type': 'application/json' };

  const details = await fetch(`${origin}/v1/operator/licenses/details`, { method: 'POST', headers: auth, body: JSON.stringify({ license_id: issued.license.id, customer_name: 'Client Co', customer_email: 'it@client.example.com', notes: 'Order 1001' }) });
  assert.equal(details.status, 200);
  assert.equal((await details.json()).notes, 'Order 1001');

  const badEmail = await fetch(`${origin}/v1/operator/licenses/details`, { method: 'POST', headers: auth, body: JSON.stringify({ license_id: issued.license.id, customer_email: 'not-an-email' }) });
  assert.equal(badEmail.status, 400);

  const [license] = (await (await fetch(`${origin}/v1/operator/licenses`, { headers: auth })).json()).licenses;
  const deactivated = await fetch(`${origin}/v1/operator/activations/deactivate`, { method: 'POST', headers: auth, body: JSON.stringify({ license_id: license.id, activation_id: license.activations[0].id }) });
  assert.equal(deactivated.status, 200);
  assert.equal((await deactivated.json()).active_production_activations, 0);

  const audit = await (await fetch(`${origin}/v1/operator/audit?limit=10`, { headers: auth })).json();
  const actions = audit.events.map((event) => event.action);
  assert.ok(actions.includes('license.details_changed'));
  assert.ok(actions.includes('license.deactivated'));
});

test('operator notes never reach the customer portal', async (context) => {
  const { engine, server, origin } = await fixture();
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const issued = await engine.issue({ plan: 'starter', expiresAt: '2030-01-01T00:00:00.000Z', customerName: 'Client Co', notes: 'internal: slow payer' });

  const wrong = await signIn(origin, '/portal', 'wrong-token');
  assert.equal(wrong.response.status, 401);

  const { response, cookie } = await signIn(origin, '/portal', issued.portal_access_token);
  assert.equal(response.status, 303);
  assert.match(cookie, /^nx_cu=/);
  const summary = await (await fetch(`${origin}/v1/customer/licenses`, { headers: { cookie } })).json();
  assert.equal(summary.licenses[0].customer_name, 'Client Co');
  assert.equal('notes' in summary.licenses[0], false);

  const csrf = await csrfFrom(origin, '/portal', cookie);
  const request = await fetch(`${origin}/v1/customer/data-requests`, { method: 'POST', headers: { cookie, 'content-type': 'application/json', 'x-csrf-token': csrf }, body: JSON.stringify({ action: 'export' }) });
  assert.equal(request.status, 200);

  // An operator session is not a customer session, and vice versa.
  const crossover = await fetch(`${origin}/v1/operator/licenses`, { headers: { cookie } });
  assert.equal(crossover.status, 401);
});

test('signing out ends the session; repeated bad sign-ins lock out', async (context) => {
  const { server, origin } = await fixture({ loginThrottle: new LoginThrottle({ limit: 2 }) });
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const { cookie } = await signIn(origin, '/operator', operatorToken);
  const csrf = await csrfFrom(origin, '/operator', cookie);
  const out = await fetch(`${origin}/operator/session/end`, { method: 'POST', redirect: 'manual', headers: { cookie, 'content-type': 'application/x-www-form-urlencoded' }, body: form({ csrf }) });
  assert.equal(out.status, 303);
  assert.match(out.headers.get('set-cookie'), /Max-Age=0/);
  assert.equal((await fetch(`${origin}/v1/operator/licenses`, { headers: { cookie } })).status, 401);

  await signIn(origin, '/operator', 'bad-1');
  await signIn(origin, '/operator', 'bad-2');
  const locked = await signIn(origin, '/operator', operatorToken);
  assert.equal(locked.response.status, 429);
});

test('secure deployments use __Host- cookies; only allowlisted assets are served', async (context) => {
  const { server, origin } = await fixture({ secureCookies: true });
  context.after(() => new Promise((resolve) => server.close(resolve)));
  const { setCookie } = await signIn(origin, '/operator', operatorToken);
  assert.match(setCookie, /^__Host-nx_op=/);
  assert.match(setCookie, /; Secure/);

  const css = await fetch(`${origin}/console/assets/console.css`);
  assert.equal(css.status, 200);
  assert.match(css.headers.get('content-type'), /text\/css/);
  assert.equal((await fetch(`${origin}/console/assets/..%2Fsessions.js`)).status, 404);
  assert.equal((await fetch(`${origin}/console/assets/http.js`)).status, 404);
});

test('session store expires idle sessions', () => {
  let now = 0;
  const store = new SessionStore({ idleMs: 1000, absoluteMs: 5000, clock: () => now });
  const { id } = store.create('operator');
  now = 500;
  assert.ok(store.get(id, 'operator'));
  assert.equal(store.get(id, 'customer'), null);
  now = 1600;
  assert.equal(store.get(id, 'operator'), null);
});

test('license keys use five readable groups without look-alike characters', async () => {
  const { generateLicenseKey } = await import('../src/domain/licensing.js');
  for (let i = 0; i < 200; i++) {
    const key = generateLicenseKey();
    assert.match(key, /^MSPN-([2-9A-HJ-NP-Z]{5}-){4}[2-9A-HJ-NP-Z]{5}$/);
  }
});
