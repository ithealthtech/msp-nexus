import { createHash, randomBytes, randomUUID, timingSafeEqual } from 'node:crypto';
import { isStagingSite, normalizeSite } from './domain.js';

export const LICENSE_STATES = Object.freeze({
  ACTIVE: 'active',
  GRACE: 'grace',
  EXPIRED: 'expired',
  SUSPENDED: 'suspended',
  REVOKED: 'revoked',
  CANCELLED: 'cancelled'
});

export function generateLicenseKey(prefix = 'MSPN') {
  const body = randomBytes(18).toString('base64url').toUpperCase();
  return `${prefix}-${body.slice(0, 6)}-${body.slice(6, 12)}-${body.slice(12, 18)}-${body.slice(18)}`;
}

export function hashLicenseKey(key, pepper) {
  if (!pepper || pepper.length < 32) throw new Error('license_pepper_too_short');
  return createHash('sha256').update(`${pepper}:${key.trim().toUpperCase()}`).digest('hex');
}

export function hashPortalToken(token, pepper) {
  return createHash('sha256').update(`${pepper}:portal:${token}`).digest('hex');
}

function constantEquals(a, b) {
  const left = Buffer.from(a);
  const right = Buffer.from(b);
  return left.length === right.length && timingSafeEqual(left, right);
}

export class LicensingEngine {
  constructor({ repository, pepper, clock = () => new Date() }) {
    this.repository = repository;
    this.pepper = pepper;
    this.clock = clock;
  }

  async activate({ key, siteUrl, instanceId, isMultisite = false }) {
    const license = await this.#licenseByKey(key);
    const site = normalizeSite(siteUrl);
    const status = this.#status(license);
    if (![LICENSE_STATES.ACTIVE, LICENSE_STATES.GRACE].includes(status)) throw new Error(`license_${status}`);
    if (isMultisite && !license.allowMultisite) throw new Error('multisite_not_entitled');

    const existing = license.activations.find((item) => item.site === site && item.instanceId === instanceId && !item.deactivatedAt);
    if (existing) return this.#response(license, existing);

    const countsTowardLimit = !isStagingSite(site) || !license.allowFreeStaging;
    const used = license.activations.filter((item) => !item.deactivatedAt && item.countsTowardLimit).length;
    if (countsTowardLimit && used >= license.activationLimit) throw new Error('activation_limit_reached');

    const activation = {
      id: randomUUID(),
      site,
      instanceId,
      isMultisite: Boolean(isMultisite),
      countsTowardLimit,
      activatedAt: this.clock().toISOString(),
      lastSeenAt: this.clock().toISOString(),
      deactivatedAt: null
    };
    license.activations.push(activation);
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.activated', licenseId: license.id, activationId: activation.id, site, at: this.clock().toISOString() });
    return this.#response(license, activation);
  }

  async issue({ plan = 'business', expiresAt, activationLimit = 1, graceDays = 14, allowFreeStaging = true, allowMultisite = false, entitlements = [] }) {
    const expiry = new Date(expiresAt);
    if (Number.isNaN(expiry.getTime())) throw new Error('invalid_request');
    const key = generateLicenseKey();
    const portalToken = randomBytes(24).toString('base64url');
    const license = {
      id: randomUUID(),
      keyHash: hashLicenseKey(key, this.pepper),
      portalTokenHash: hashPortalToken(portalToken, this.pepper),
      status: LICENSE_STATES.ACTIVE,
      plan: String(plan),
      expiresAt: expiry.toISOString(),
      graceDays: Math.min(90, Math.max(0, Number(graceDays))),
      activationLimit: Math.min(1000, Math.max(1, Number(activationLimit))),
      allowFreeStaging: Boolean(allowFreeStaging),
      allowMultisite: Boolean(allowMultisite),
      entitlements: [...new Set(entitlements.map(String))],
      activations: []
    };
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.issued', licenseId: license.id, at: this.clock().toISOString() });
    return { license: this.#publicLicense(license), license_key: key, portal_access_token: portalToken };
  }

  async setStatus({ licenseId, status }) {
    if (![LICENSE_STATES.ACTIVE, LICENSE_STATES.SUSPENDED, LICENSE_STATES.REVOKED, LICENSE_STATES.CANCELLED].includes(status)) throw new Error('invalid_license_status');
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    license.status = status;
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.status_changed', licenseId, status, at: this.clock().toISOString() });
    return this.#publicLicense(license);
  }

  async renew({ licenseId, expiresAt }) {
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    const expiry = new Date(expiresAt);
    if (Number.isNaN(expiry.getTime()) || expiry <= this.clock()) throw new Error('invalid_request');
    license.expiresAt = expiry.toISOString();
    license.status = LICENSE_STATES.ACTIVE;
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.renewed', licenseId, expiresAt: license.expiresAt, at: this.clock().toISOString() });
    return this.#publicLicense(license);
  }

  async changePlan({ licenseId, plan, activationLimit, entitlements }) {
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    if (!String(plan ?? '').trim()) throw new Error('invalid_request');
    const previousPlan = license.plan;
    license.plan = String(plan).trim();
    if (activationLimit !== undefined) license.activationLimit = Math.min(1000, Math.max(1, Number(activationLimit)));
    if (Array.isArray(entitlements)) license.entitlements = [...new Set(entitlements.map(String))];
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.plan_changed', licenseId, previousPlan, plan: license.plan, at: this.clock().toISOString() });
    return this.#publicLicense(license);
  }

  async heartbeat(input) {
    return this.validate(input);
  }

  async transfer({ licenseId, activationId, siteUrl }) {
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    const activation = license.activations.find((item) => item.id === activationId && !item.deactivatedAt);
    if (!activation) throw new Error('activation_not_found');
    const previous = activation.site;
    const nextSite = normalizeSite(siteUrl);
    const nextCounts = !isStagingSite(nextSite) || !license.allowFreeStaging;
    const usedByOthers = license.activations.filter((item) => item.id !== activationId && !item.deactivatedAt && item.countsTowardLimit).length;
    if (nextCounts && usedByOthers >= license.activationLimit) throw new Error('activation_limit_reached');
    activation.site = nextSite;
    activation.countsTowardLimit = nextCounts;
    activation.lastSeenAt = this.clock().toISOString();
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'activation.transferred', licenseId, activationId, previousSite: previous, site: activation.site, at: this.clock().toISOString() });
    return this.#response(license, activation);
  }

  async list() {
    const licenses = await this.repository.listLicenses();
    return licenses.map((license) => this.#publicLicense(license));
  }

  async customerSummary(portalToken) {
    const sought = hashPortalToken(portalToken, this.pepper);
    const licenses = await this.repository.listLicenses();
    const matches = licenses.filter((license) => license.portalTokenHash && constantEquals(license.portalTokenHash, sought));
    if (!matches.length) throw new Error('portal_access_denied');
    return matches.map((license) => this.#publicLicense(license));
  }

  async customerTransfer({ portalToken, licenseId, activationId, siteUrl }) {
    const allowed = await this.customerSummary(portalToken);
    if (!allowed.some((license) => license.id === licenseId)) throw new Error('portal_access_denied');
    return this.transfer({ licenseId, activationId, siteUrl });
  }

  async requestDataAction({ portalToken, action, note = '' }) {
    const allowedActions = new Set(['export', 'correction', 'deletion']);
    if (!allowedActions.has(action)) throw new Error('invalid_request');
    const licenses = await this.customerSummary(portalToken);
    const requestId = randomUUID();
    await this.repository.appendAudit({
      action: 'privacy.requested', requestId, requestType: action,
      licenseIds: licenses.map((license) => license.id), note: String(note).slice(0, 1000),
      at: this.clock().toISOString()
    });
    return { request_id: requestId, status: 'received', action, received_at: this.clock().toISOString() };
  }

  async validate({ licenseId, siteUrl, instanceId }) {
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    const site = normalizeSite(siteUrl);
    const activation = license.activations.find((item) => item.site === site && (!instanceId || item.instanceId === instanceId) && !item.deactivatedAt);
    if (!activation) throw new Error('activation_not_found');
    activation.lastSeenAt = this.clock().toISOString();
    await this.repository.saveLicense(license);
    return this.#response(license, activation);
  }

  async deactivate({ licenseId, siteUrl, instanceId }) {
    const license = await this.repository.getLicense(licenseId);
    if (!license) throw new Error('license_not_found');
    const site = normalizeSite(siteUrl);
    const activation = license.activations.find((item) => item.site === site && item.instanceId === instanceId && !item.deactivatedAt);
    if (!activation) throw new Error('activation_not_found');
    activation.deactivatedAt = this.clock().toISOString();
    await this.repository.saveLicense(license);
    await this.repository.appendAudit({ action: 'license.deactivated', licenseId: license.id, activationId: activation.id, site, at: this.clock().toISOString() });
    return { status: 'deactivated', activation_id: activation.id };
  }

  #status(license) {
    if ([LICENSE_STATES.SUSPENDED, LICENSE_STATES.REVOKED, LICENSE_STATES.CANCELLED].includes(license.status)) return license.status;
    const now = this.clock();
    const expiry = new Date(license.expiresAt);
    if (now <= expiry) return LICENSE_STATES.ACTIVE;
    const graceEnd = new Date(expiry.getTime() + license.graceDays * 86400000);
    return now <= graceEnd ? LICENSE_STATES.GRACE : LICENSE_STATES.EXPIRED;
  }

  #response(license, activation) {
    const expires = new Date(license.expiresAt);
    const graceEnd = new Date(expires.getTime() + license.graceDays * 86400000);
    return {
      license_id: license.id,
      status: this.#status(license),
      plan: license.plan,
      expires_at: expires.toISOString(),
      grace_ends_at: graceEnd.toISOString(),
      activation_id: activation.id,
      site_url: activation.site,
      activation_limit: license.activationLimit,
      active_production_activations: license.activations.filter((item) => !item.deactivatedAt && item.countsTowardLimit).length,
      active_staging_activations: license.activations.filter((item) => !item.deactivatedAt && !item.countsTowardLimit).length,
      is_staging: !activation.countsTowardLimit,
      allow_multisite: license.allowMultisite,
      entitlements: [...license.entitlements]
    };
  }

  #publicLicense(license) {
    const expires = new Date(license.expiresAt);
    return {
      id: license.id,
      status: this.#status(license),
      administrative_status: license.status,
      plan: license.plan,
      expires_at: expires.toISOString(),
      grace_ends_at: new Date(expires.getTime() + license.graceDays * 86400000).toISOString(),
      activation_limit: license.activationLimit,
      active_production_activations: license.activations.filter((item) => !item.deactivatedAt && item.countsTowardLimit).length,
      active_staging_activations: license.activations.filter((item) => !item.deactivatedAt && !item.countsTowardLimit).length,
      allow_free_staging: license.allowFreeStaging,
      allow_multisite: license.allowMultisite,
      entitlements: [...license.entitlements],
      activations: license.activations.map((item) => ({ id: item.id, site: item.site, is_multisite: item.isMultisite, counts_toward_limit: item.countsTowardLimit, activated_at: item.activatedAt, last_seen_at: item.lastSeenAt, deactivated_at: item.deactivatedAt }))
    };
  }

  async #licenseByKey(key) {
    const sought = hashLicenseKey(key, this.pepper);
    const candidates = await this.repository.listLicenses();
    const license = candidates.find((item) => constantEquals(item.keyHash, sought));
    if (!license) throw new Error('license_not_found');
    return license;
  }
}
