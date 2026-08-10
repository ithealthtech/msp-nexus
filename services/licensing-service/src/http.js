import { randomUUID, timingSafeEqual } from 'node:crypto';
import { createReadStream } from 'node:fs';
import { pipeline } from 'node:stream/promises';

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

function requireOperator(request, expected) {
  if (!expected || !tokenEquals(bearer(request), expected)) throw new Error('operator_access_denied');
}

function escapeHtml(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function sendHtml(response, status, html, requestId) {
  const encoded = Buffer.from(html);
  response.writeHead(status, {
    'content-type': 'text/html; charset=utf-8', 'content-length': encoded.length,
    'cache-control': 'no-store', 'content-security-policy': "default-src 'none'; style-src 'unsafe-inline'; form-action 'none'; frame-ancestors 'none'; base-uri 'none'",
    'referrer-policy': 'no-referrer', 'x-content-type-options': 'nosniff', 'x-frame-options': 'DENY', 'x-request-id': requestId
  });
  response.end(encoded);
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

function portalDocument(title, intro, licenses) {
  const cards = licenses.map((license) => `<article><h2>${escapeHtml(license.plan)} plan</h2><dl><dt>Status</dt><dd>${escapeHtml(license.status)}</dd><dt>Expires</dt><dd>${escapeHtml(license.expires_at)}</dd><dt>Production activations</dt><dd>${license.active_production_activations} / ${license.activation_limit}</dd><dt>Staging activations</dt><dd>${license.active_staging_activations}</dd></dl><h3>Sites</h3><ul>${license.activations.map((item) => `<li>${escapeHtml(item.site)} — ${item.deactivated_at ? 'deactivated' : 'active'}</li>`).join('') || '<li>No active sites</li>'}</ul></article>`).join('');
  return `<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>${escapeHtml(title)}</title><style>body{font:16px/1.6 system-ui;margin:0;background:#f4f7fb;color:#081527}main{max-width:72rem;margin:auto;padding:3rem 1.25rem}article{background:#fff;border:1px solid #dbe5ef;border-radius:.6rem;padding:1.5rem;margin:1rem 0}dl{display:grid;grid-template-columns:minmax(10rem,1fr) 2fr;gap:.5rem}dt{font-weight:700}h1,h2,h3{line-height:1.15}</style><main><h1>${escapeHtml(title)}</h1><p>${escapeHtml(intro)}</p>${cards}</main></html>`;
}

export function createHandler({ engine, updates, packages = null, entitlements = null, webhook = null, rateLimiter = null, releaseCatalog = null, operatorToken = '' }) {
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
      if (request.method === 'GET' && request.url === '/v1/operator/licenses') { requireOperator(request, operatorToken); return send(response, 200, { licenses: await engine.list() }, requestId); }
      if (request.method === 'GET' && request.url === '/v1/operator/releases') { requireOperator(request, operatorToken); return send(response, 200, { releases: releaseCatalog ? releaseCatalog.list() : [] }, requestId); }
      if (request.method === 'GET' && request.url === '/operator') { requireOperator(request, operatorToken); return sendHtml(response, 200, portalDocument('MSP Nexus operator portal', 'License status and activation visibility. Administrative mutations use the authenticated API.', await engine.list()), requestId); }
      if (request.method === 'GET' && request.url === '/v1/customer/licenses') return send(response, 200, { licenses: await engine.customerSummary(bearer(request)) }, requestId);
      if (request.method === 'GET' && request.url === '/v1/customer/export') return send(response, 200, { schema: 1, generated_at: new Date().toISOString(), licenses: await engine.customerSummary(bearer(request)) }, requestId);
      if (request.method === 'GET' && request.url === '/customer') return sendHtml(response, 200, portalDocument('MSP Nexus customer portal', 'Review entitlements, dates, and activated sites. Contact support for billing changes.', await engine.customerSummary(bearer(request))), requestId);
      if (request.method !== 'POST') return send(response, 404, { error: 'not_found' }, requestId);
      const raw = await readBody(request);
      if (request.url === '/v1/webhooks/commerce') {
        if (!webhook) throw new Error('webhook_not_configured');
        return send(response, 200, await webhook.process({ rawBody: raw, signatureHeader: request.headers['x-msp-signature'] ?? '' }), requestId);
      }
      const payload = parseBody(raw);
      let result;
      if (request.url === '/v1/operator/licenses') { requireOperator(request, operatorToken); result = await engine.issue({ plan: payload.plan, expiresAt: payload.expires_at, activationLimit: payload.activation_limit, graceDays: payload.grace_days, allowFreeStaging: payload.allow_free_staging, allowMultisite: payload.allow_multisite, entitlements: Array.isArray(payload.entitlements) ? payload.entitlements : [] }); }
      else if (request.url === '/v1/operator/licenses/status') { requireOperator(request, operatorToken); result = await engine.setStatus({ licenseId: payload.license_id, status: payload.status }); }
      else if (request.url === '/v1/operator/licenses/renew') { requireOperator(request, operatorToken); result = await engine.renew({ licenseId: payload.license_id, expiresAt: payload.expires_at }); }
      else if (request.url === '/v1/operator/licenses/plan') { requireOperator(request, operatorToken); result = await engine.changePlan({ licenseId: payload.license_id, plan: payload.plan, activationLimit: payload.activation_limit, entitlements: payload.entitlements }); }
      else if (request.url === '/v1/operator/activations/transfer') { requireOperator(request, operatorToken); result = await engine.transfer({ licenseId: payload.license_id, activationId: payload.activation_id, siteUrl: payload.site_url }); }
      else if (request.url === '/v1/operator/releases') { requireOperator(request, operatorToken); if (!releaseCatalog) throw new Error('release_not_found'); result = await releaseCatalog.upsert(payload); }
      else if (request.url === '/v1/operator/releases/status') { requireOperator(request, operatorToken); if (!releaseCatalog) throw new Error('release_not_found'); result = await releaseCatalog.changeStatus({ product: payload.product, version: payload.version, channel: payload.channel, status: payload.status }); }
      else if (request.url === '/v1/operator/webhooks/retry') { requireOperator(request, operatorToken); if (!webhook) throw new Error('webhook_not_configured'); result = await webhook.retryDue(payload.limit); }
      else if (request.url === '/v1/customer/activations/transfer') result = await engine.customerTransfer({ portalToken: bearer(request), licenseId: payload.license_id, activationId: payload.activation_id, siteUrl: payload.site_url });
      else if (request.url === '/v1/customer/data-requests') result = await engine.requestDataAction({ portalToken: bearer(request), action: payload.action, note: payload.note });
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
