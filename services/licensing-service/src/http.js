import { randomUUID, timingSafeEqual } from 'node:crypto';
import { createReadStream } from 'node:fs';
import { pipeline } from 'node:stream/promises';
import { URLSearchParams } from 'node:url';
import { LoginThrottle, SessionStore, parseCookies, safeEquals } from './console/sessions.js';
import { appPage, loadAssets, signInPage } from './console/pages.js';

const errors = {
  invalid_json: 400,
  invalid_request: 400,
  license_not_found: 404,
  activation_not_found: 404,
  activation_limit_reached: 409,
  multisite_not_entitled: 403,
  license_expired: 403,
  license_suspended: 403,
  license_revoked: 403
  ,license_cancelled: 403
  ,product_not_entitled: 403
  ,release_not_found: 404
  ,invalid_download_grant: 403
  ,expired_download_grant: 403
  ,operator_access_denied: 401
  ,portal_access_denied: 401
  ,invalid_license_status: 400
  ,invalid_package_path: 403
  ,package_corrupt: 503
  ,invalid_webhook_event: 400
  ,invalid_webhook_signature: 401
  ,webhook_timestamp_outside_tolerance: 401
  ,webhook_not_configured: 404
  ,webhook_handler_failed: 503
  ,rate_limited: 429
  ,invalid_release: 400
  ,invalid_release_status: 400
  ,csrf_failed: 403
  ,sign_in_locked: 429
};

async function readBody(request) {
  const chunks = [];
  let size = 0;
  for await (const chunk of request) {
    size += chunk.length;
    if (size > 32768) throw new Error('invalid_request');
    chunks.push(chunk);
  }
  return Buffer.concat(chunks).toString('utf8');
}

function parseBody(raw) {
  try { return JSON.parse(raw || '{}'); }
  catch { throw new Error('invalid_json'); }
}

function send(response, status, payload, requestId, extraHeaders = {}) {
  const encoded = JSON.stringify(payload);
  response.writeHead(status, {
    'content-type': 'application/json; charset=utf-8',
    'content-length': Buffer.byteLength(encoded),
    'cache-control': 'no-store',
    'x-content-type-options': 'nosniff',
    'x-request-id': requestId,
    ...extraHeaders
  });
  response.end(encoded);
}

function bearer(request) {
  const header = String(request.headers.authorization ?? '');
  return header.startsWith('Bearer ') ? header.slice(7).trim() : '';
}

function tokenEquals(left, right) {
  const a = Buffer.from(String(left));
  const b = Buffer.from(String(right));
  return a.length > 0 && a.length === b.length && timingSafeEqual(a, b);
}



const PAGE_CSP = "default-src 'none'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'";

function sendHtml(response, status, html, requestId, extraHeaders = {}) {
  const encoded = Buffer.from(html);
  response.writeHead(status, {
    'content-type': 'text/html; charset=utf-8', 'content-length': encoded.length,
    'cache-control': 'no-store', 'content-security-policy': PAGE_CSP,
    'referrer-policy': 'no-referrer', 'x-content-type-options': 'nosniff', 'x-frame-options': 'DENY', 'x-request-id': requestId,
    ...extraHeaders
  });
  response.end(encoded);
}

function redirect(response, location, requestId, extraHeaders = {}) {
  response.writeHead(303, { location, 'cache-control': 'no-store', 'x-request-id': requestId, ...extraHeaders });
  response.end();
}

async function sendPackage(response, file, requestId) {
  response.writeHead(200, {
    'content-type': 'application/zip', 'content-length': file.size,
    'content-disposition': `attachment; filename="${file.filename.replaceAll('"', '')}"`,
    'cache-control': 'private, no-store', 'digest': `sha-256=${Buffer.from(file.sha256, 'hex').toString('base64')}`,
    'x-content-type-options': 'nosniff', 'x-request-id': requestId
  });
  await pipeline(createReadStream(file.path), response);
}

export function createHandler({ engine, updates, packages = null, entitlements = null, webhook = null, rateLimiter = null, releaseCatalog = null, operatorToken = '', sessions = new SessionStore(), loginThrottle = new LoginThrottle(), secureCookies = false, assets = loadAssets() }) {
  const cookieName = (kind) => `${secureCookies ? '__Host-' : ''}${'operator' === kind ? 'nx_op' : 'nx_cu'}`;
  const cookie = (kind, value, maxAge) => `${cookieName(kind)}=${value}; Path=/; HttpOnly; SameSite=Strict; Max-Age=${maxAge}${secureCookies ? '; Secure' : ''}`;
  const sessionFor = (request, kind) => {
    const id = parseCookies(request.headers.cookie)[cookieName(kind)];
    const session = sessions.get(id, kind);
    return session ? { id, session } : null;
  };
  // Cookie-authenticated changes need the session's CSRF token and, when the browser sends one, a same-host Origin.
  const assertCsrf = (request, session, supplied) => {
    const origin = request.headers.origin;
    if (origin) {
      let host = '';
      try { host = new URL(origin).host; } catch { host = ''; }
      if (host !== request.headers.host) throw new Error('csrf_failed');
    }
    if (!safeEquals(supplied ?? '', session.csrf)) throw new Error('csrf_failed');
  };
  const requireOperator = (request) => {
    if (operatorToken && tokenEquals(bearer(request), operatorToken)) return;
    const found = sessionFor(request, 'operator');
    if (!found) throw new Error('operator_access_denied');
    if ('GET' !== request.method) assertCsrf(request, found.session, request.headers['x-csrf-token']);
  };
  const customerToken = (request) => {
    const token = bearer(request);
    if (token) return token;
    const found = sessionFor(request, 'customer');
    if (!found) return '';
    if ('GET' !== request.method) assertCsrf(request, found.session, request.headers['x-csrf-token']);
    return found.session.data.portalToken;
  };
  const clientOf = (request) => request.socket.remoteAddress ?? 'unknown';

  const signIn = async (request, response, requestId, kind) => {
    const base = 'operator' === kind ? '/operator' : '/portal';
    const form = new URLSearchParams(await readBody(request));
    const secret = String(form.get('secret') ?? '').trim();
    const client = clientOf(request);
    try {
      loginThrottle.assertAllowed(client);
    } catch {
      return sendHtml(response, 429, signInPage(kind, { error: 'Too many attempts. Wait 15 minutes, then try again.' }), requestId, { 'retry-after': '900' });
    }
    let data = {};
    if ('operator' === kind) {
      if (!operatorToken) return sendHtml(response, 503, signInPage(kind, { error: 'Operator access is not configured on this server. Set OPERATOR_TOKEN.' }), requestId);
      if (!tokenEquals(secret, operatorToken)) {
        loginThrottle.fail(client);
        return sendHtml(response, 401, signInPage(kind, { error: 'That access key is not valid.' }), requestId);
      }
    } else {
      try {
        await engine.customerSummary(secret);
      } catch {
        loginThrottle.fail(client);
        return sendHtml(response, 401, signInPage(kind, { error: 'That access token is not valid.' }), requestId);
      }
      data = { portalToken: secret };
    }
    loginThrottle.succeed(client);
    const { id } = sessions.create(kind, data);
    return redirect(response, base, requestId, { 'set-cookie': cookie(kind, id, Math.floor(sessions.absoluteMs / 1000)) });
  };

  const signOut = async (request, response, requestId, kind) => {
    const base = 'operator' === kind ? '/operator' : '/portal';
    const form = new URLSearchParams(await readBody(request));
    const found = sessionFor(request, kind);
    if (found) {
      assertCsrf(request, found.session, form.get('csrf'));
      sessions.destroy(found.id);
    }
    return redirect(response, base, requestId, { 'set-cookie': cookie(kind, '', 0) });
  };

  return async (request, response) => {
    const requestId = randomUUID();
    try {
      const route = new URL(request.url, 'http://service.local').pathname;
      if (rateLimiter && route !== '/health') rateLimiter.consume(`${request.socket.remoteAddress ?? 'unknown'}:${route}`);
      if (request.method === 'GET' && request.url === '/health') return send(response, 200, { status: 'ok' }, requestId);
      if (request.method === 'GET' && request.url.startsWith('/v1/packages/')) {
        if (!packages) throw new Error('release_not_found');
        const url = new URL(request.url, 'http://service.local');
        const segments = url.pathname.split('/').filter(Boolean);
        if (segments.length !== 4) return send(response, 404, { error: 'not_found' }, requestId);
        const file = await packages.authorize({ product: decodeURIComponent(segments[2]), version: decodeURIComponent(segments[3]), grant: url.searchParams.get('grant') ?? '' });
        return sendPackage(response, file, requestId);
      }
      if (request.method === 'GET' && route.startsWith('/console/assets/')) {
        const asset = assets.get(route.slice('/console/assets/'.length));
        if (!asset) return send(response, 404, { error: 'not_found' }, requestId);
        response.writeHead(200, { 'content-type': asset.type, 'content-length': asset.body.length, 'cache-control': 'no-cache', 'x-content-type-options': 'nosniff', 'x-request-id': requestId });
        return response.end(asset.body);
      }
      if (request.method === 'GET' && (route === '/operator' || route === '/portal')) {
        const kind = route === '/operator' ? 'operator' : 'customer';
        const found = sessionFor(request, kind);
        return sendHtml(response, 200, found ? appPage(kind, found.session.csrf) : signInPage(kind), requestId);
      }
      if (request.method === 'GET' && route === '/customer') return redirect(response, '/portal', requestId);
      if (request.method === 'POST' && route === '/operator/session') return await signIn(request, response, requestId, 'operator');
      if (request.method === 'POST' && route === '/portal/session') return await signIn(request, response, requestId, 'customer');
      if (request.method === 'POST' && route === '/operator/session/end') return await signOut(request, response, requestId, 'operator');
      if (request.method === 'POST' && route === '/portal/session/end') return await signOut(request, response, requestId, 'customer');
      if (request.method === 'GET' && route === '/v1/operator/licenses') { requireOperator(request); return send(response, 200, { licenses: await engine.list() }, requestId); }
      if (request.method === 'GET' && route === '/v1/operator/releases') { requireOperator(request); return send(response, 200, { releases: releaseCatalog ? releaseCatalog.list() : [] }, requestId); }
      if (request.method === 'GET' && route === '/v1/operator/audit') {
        requireOperator(request);
        const limit = Number.parseInt(new URL(request.url, 'http://service.local').searchParams.get('limit') ?? '100', 10);
        return send(response, 200, { events: await engine.audit(limit) }, requestId);
      }
      if (request.method === 'GET' && route === '/v1/customer/licenses') return send(response, 200, { licenses: await engine.customerSummary(customerToken(request)) }, requestId);
      if (request.method === 'GET' && route === '/v1/customer/export') return send(response, 200, { schema: 1, generated_at: new Date().toISOString(), licenses: await engine.customerSummary(customerToken(request)) }, requestId);
      if (request.method !== 'POST') return send(response, 404, { error: 'not_found' }, requestId);
      const raw = await readBody(request);
      if (request.url === '/v1/webhooks/commerce') {
        if (!webhook) throw new Error('webhook_not_configured');
        return send(response, 200, await webhook.process({ rawBody: raw, signatureHeader: request.headers['x-msp-signature'] ?? '' }), requestId);
      }
      const payload = parseBody(raw);
      let result;
      if (request.url === '/v1/operator/licenses') { requireOperator(request); result = await engine.issue({ customerName: payload.customer_name, customerEmail: payload.customer_email, notes: payload.notes, plan: payload.plan, expiresAt: payload.expires_at, activationLimit: payload.activation_limit, graceDays: payload.grace_days, allowFreeStaging: payload.allow_free_staging, allowMultisite: payload.allow_multisite, entitlements: Array.isArray(payload.entitlements) ? payload.entitlements : [] }); }
      else if (request.url === '/v1/operator/licenses/status') { requireOperator(request); result = await engine.setStatus({ licenseId: payload.license_id, status: payload.status }); }
      else if (request.url === '/v1/operator/licenses/renew') { requireOperator(request); result = await engine.renew({ licenseId: payload.license_id, expiresAt: payload.expires_at }); }
      else if (request.url === '/v1/operator/licenses/plan') { requireOperator(request); result = await engine.changePlan({ licenseId: payload.license_id, plan: payload.plan, activationLimit: payload.activation_limit, entitlements: payload.entitlements }); }
      else if (request.url === '/v1/operator/licenses/details') { requireOperator(request); result = await engine.updateDetails({ licenseId: payload.license_id, customerName: payload.customer_name, customerEmail: payload.customer_email, notes: payload.notes }); }
      else if (request.url === '/v1/operator/activations/deactivate') { requireOperator(request); result = await engine.deactivateActivation({ licenseId: payload.license_id, activationId: payload.activation_id }); }
      else if (request.url === '/v1/operator/activations/transfer') { requireOperator(request); result = await engine.transfer({ licenseId: payload.license_id, activationId: payload.activation_id, siteUrl: payload.site_url }); }
      else if (request.url === '/v1/operator/releases') { requireOperator(request); if (!releaseCatalog) throw new Error('release_not_found'); result = await releaseCatalog.upsert(payload); }
      else if (request.url === '/v1/operator/releases/status') { requireOperator(request); if (!releaseCatalog) throw new Error('release_not_found'); result = await releaseCatalog.changeStatus({ product: payload.product, version: payload.version, channel: payload.channel, status: payload.status }); }
      else if (request.url === '/v1/operator/webhooks/retry') { requireOperator(request); if (!webhook) throw new Error('webhook_not_configured'); result = await webhook.retryDue(payload.limit); }
      else if (request.url === '/v1/customer/activations/transfer') result = await engine.customerTransfer({ portalToken: customerToken(request), licenseId: payload.license_id, activationId: payload.activation_id, siteUrl: payload.site_url });
      else if (request.url === '/v1/customer/data-requests') result = await engine.requestDataAction({ portalToken: customerToken(request), action: payload.action, note: payload.note });
      else if (request.url === '/v1/licenses/activate') { const value = await engine.activate({ key: payload.license_key, siteUrl: payload.site_url, instanceId: payload.instance_id, isMultisite: payload.is_multisite }); result = entitlements ? entitlements.envelope(value) : value; }
      else if (request.url === '/v1/licenses/validate') { const value = await engine.validate({ licenseId: payload.license_id, siteUrl: payload.site_url, instanceId: payload.instance_id }); result = entitlements ? entitlements.envelope(value) : value; }
      else if (request.url === '/v1/licenses/heartbeat' || request.url === '/v1/licenses/refresh') { const value = await engine.heartbeat({ licenseId: payload.license_id, siteUrl: payload.site_url, instanceId: payload.instance_id }); result = entitlements ? entitlements.envelope(value) : value; }
      else if (request.url === '/v1/licenses/deactivate') result = await engine.deactivate({ licenseId: payload.license_id, siteUrl: payload.site_url, instanceId: payload.instance_id });
      else if (request.url === '/v1/updates/check') result = await updates.check({ licenseId: payload.license_id, siteUrl: payload.site_url, instanceId: payload.instance_id, product: payload.product, channel: payload.channel });
      else return send(response, 404, { error: 'not_found' }, requestId);
      return send(response, 200, result, requestId);
    } catch (error) {
      const code = error instanceof Error ? error.message : 'internal_error';
      return send(response, errors[code] ?? 500, { error: errors[code] ? code : 'internal_error', request_id: requestId }, requestId, 'rate_limited' === code ? { 'retry-after': '60' } : {});
    }
  };
}
