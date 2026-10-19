import { randomBytes, timingSafeEqual } from 'node:crypto';

/**
 * Browser sessions for the operator console and customer portal. Session ids live only in an
 * HttpOnly cookie; secrets (the operator check, the customer's portal token) stay server-side.
 * Sessions are in memory, so a restart signs everyone out, which is acceptable for a
 * single-instance control plane.
 */
export class SessionStore {
  constructor({ idleMs = 8 * 3600000, absoluteMs = 24 * 3600000, clock = () => Date.now() } = {}) {
    this.idleMs = idleMs;
    this.absoluteMs = absoluteMs;
    this.clock = clock;
    this.sessions = new Map();
  }

  create(kind, data = {}) {
    this.#sweep();
    const id = randomBytes(32).toString('base64url');
    const now = this.clock();
    this.sessions.set(id, { kind, data, csrf: randomBytes(24).toString('base64url'), createdAt: now, seenAt: now });
    return { id, csrf: this.sessions.get(id).csrf };
  }

  get(id, kind) {
    if (!id) return null;
    const session = this.sessions.get(id);
    if (!session || session.kind !== kind) return null;
    const now = this.clock();
    if (now - session.seenAt > this.idleMs || now - session.createdAt > this.absoluteMs) {
      this.sessions.delete(id);
      return null;
    }
    session.seenAt = now;
    return session;
  }

  destroy(id) {
    this.sessions.delete(id);
  }

  #sweep() {
    const now = this.clock();
    for (const [id, session] of this.sessions) {
      if (now - session.seenAt > this.idleMs || now - session.createdAt > this.absoluteMs) this.sessions.delete(id);
    }
  }
}

/** Failed sign-in throttle: a few attempts per window per client, then a lockout until the window passes. */
export class LoginThrottle {
  constructor({ limit = 5, windowMs = 15 * 60000, clock = () => Date.now() } = {}) {
    this.limit = limit;
    this.windowMs = windowMs;
    this.clock = clock;
    this.failures = new Map();
  }

  assertAllowed(client) {
    const entry = this.failures.get(client);
    if (entry && this.clock() - entry.first < this.windowMs && entry.count >= this.limit) throw new Error('sign_in_locked');
  }

  fail(client) {
    const now = this.clock();
    const entry = this.failures.get(client);
    if (!entry || now - entry.first >= this.windowMs) this.failures.set(client, { first: now, count: 1 });
    else entry.count += 1;
  }

  succeed(client) {
    this.failures.delete(client);
  }
}

export function safeEquals(left, right) {
  const a = Buffer.from(String(left));
  const b = Buffer.from(String(right));
  return a.length > 0 && a.length === b.length && timingSafeEqual(a, b);
}

export function parseCookies(header) {
  const cookies = {};
  for (const part of String(header ?? '').split(';')) {
    const index = part.indexOf('=');
    if (index < 1) continue;
    const name = part.slice(0, index).trim();
    const value = part.slice(index + 1).trim();
    try { cookies[name] = decodeURIComponent(value); } catch { cookies[name] = value; }
  }
  return cookies;
}
