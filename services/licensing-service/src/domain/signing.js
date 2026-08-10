import { createHmac, sign, verify, timingSafeEqual } from 'node:crypto';

export function canonicalJson(value) {
  if (value === null || typeof value !== 'object') return JSON.stringify(value);
  if (Array.isArray(value)) return `[${value.map(canonicalJson).join(',')}]`;
  return `{${Object.keys(value).sort().map((key) => `${JSON.stringify(key)}:${canonicalJson(value[key])}`).join(',')}}`;
}

export function signManifest(manifest, privateKey) {
  return sign(null, Buffer.from(canonicalJson(manifest)), privateKey).toString('base64');
}

export function verifyManifest(manifest, signature, publicKey) {
  return verify(null, Buffer.from(canonicalJson(manifest)), publicKey, Buffer.from(signature, 'base64'));
}

export function createDownloadGrant(payload, secret) {
  const encoded = Buffer.from(canonicalJson(payload)).toString('base64url');
  const signature = createHmac('sha256', secret).update(encoded).digest('base64url');
  return `${encoded}.${signature}`;
}

export function verifyDownloadGrant(grant, secret, now = Date.now()) {
  const [encoded, provided, extra] = String(grant).split('.');
  if (!encoded || !provided || extra) throw new Error('invalid_download_grant');
  const expected = createHmac('sha256', secret).update(encoded).digest('base64url');
  const left = Buffer.from(provided);
  const right = Buffer.from(expected);
  if (left.length !== right.length || !timingSafeEqual(left, right)) throw new Error('invalid_download_grant');
  const payload = JSON.parse(Buffer.from(encoded, 'base64url').toString('utf8'));
  if (!Number.isFinite(payload.expires_at) || payload.expires_at < now) throw new Error('expired_download_grant');
  return payload;
}
