import { createServer } from 'node:http';
import { createPrivateKey, createPublicKey, generateKeyPairSync, randomUUID } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import { LicensingEngine, generateLicenseKey, hashLicenseKey, hashPortalToken } from './domain/licensing.js';
import { MemoryRepository } from './infrastructure/memory-repository.js';
import { SQLiteRepository } from './infrastructure/sqlite-repository.js';
import { createHandler } from './http.js';
import { UpdateService } from './domain/updates.js';
import { PackageDelivery } from './infrastructure/package-delivery.js';
import { SignedEntitlements } from './domain/entitlements.js';
import { WebhookProcessor } from './domain/webhooks.js';
import { RateLimiter } from './infrastructure/rate-limiter.js';
import { ReleaseCatalog } from './domain/releases.js';

const port = Number.parseInt(process.env.PORT ?? '8787', 10);
const pepper = process.env.LICENSE_KEY_PEPPER ?? 'local-development-pepper-change-me-now';
if (process.env.NODE_ENV === 'production') {
  for (const name of ['LICENSE_KEY_PEPPER', 'DOWNLOAD_GRANT_SECRET', 'MANIFEST_PRIVATE_KEY_PEM', 'OPERATOR_TOKEN', 'PUBLIC_ORIGIN', 'PACKAGE_ROOT']) {
    if (!process.env[name]) throw new Error(`missing_required_configuration:${name}`);
  }
}
const developmentKey = process.env.DEVELOPMENT_LICENSE_KEY ?? generateLicenseKey();
const seed = {
  id: randomUUID(),
  keyHash: hashLicenseKey(developmentKey, pepper),
  portalTokenHash: process.env.DEVELOPMENT_CUSTOMER_TOKEN ? hashPortalToken(process.env.DEVELOPMENT_CUSTOMER_TOKEN, pepper) : null,
  status: 'active',
  plan: 'agency',
  expiresAt: new Date(Date.now() + 365 * 86400000).toISOString(),
  graceDays: 14,
  activationLimit: 5,
  allowFreeStaging: true,
  allowMultisite: true,
  entitlements: ['theme_updates', 'plugin_updates', 'demo_library', 'priority_support'],
  activations: []
};
const repository = process.env.USE_MEMORY_STORAGE === '1'
  ? new MemoryRepository([seed])
  : new SQLiteRepository(process.env.DATA_PATH ?? fileURLToPath(new URL('../data/licensing.sqlite', import.meta.url)));
if ((await repository.listLicenses()).length === 0) await repository.saveLicense(seed);
const engine = new LicensingEngine({ repository, pepper });
const generatedKeys = process.env.MANIFEST_PRIVATE_KEY_PEM ? null : generateKeyPairSync('ed25519');
const privateKey = process.env.MANIFEST_PRIVATE_KEY_PEM
  ? createPrivateKey(process.env.MANIFEST_PRIVATE_KEY_PEM.replaceAll('\\n', '\n'))
  : generatedKeys.privateKey;
const publicKey = createPublicKey(privateKey);
const seededReleases = [
  { product: 'msp-nexus', productType: 'theme', slug: 'msp-nexus', channel: 'stable', version: '0.5.1', status: 'published', rolloutPercent: 100, requiresWordPress: '6.7', testedWordPress: '7.0', requiresPhp: '7.4.33', sha256: process.env.THEME_PACKAGE_SHA256 ?? 'development-package-not-published', size: Number(process.env.THEME_PACKAGE_SIZE ?? 0), file: 'msp-nexus-0.5.1.zip', changelogUrl: 'https://itdonerightnc.com/' },
  { product: 'msp-nexus-core', productType: 'plugin', slug: 'msp-nexus-core/msp-nexus-core.php', channel: 'stable', version: '0.5.1', status: 'published', rolloutPercent: 100, requiresWordPress: '6.7', testedWordPress: '7.0', requiresPhp: '7.4.33', sha256: process.env.PLUGIN_PACKAGE_SHA256 ?? 'development-package-not-published', size: Number(process.env.PLUGIN_PACKAGE_SIZE ?? 0), file: 'msp-nexus-core-0.5.1.zip', changelogUrl: 'https://itdonerightnc.com/' }
];
const persistedReleases = 'function' === typeof repository.listReleases ? await repository.listReleases() : [];
const releases = persistedReleases.length > 0 ? persistedReleases : seededReleases;
if (persistedReleases.length === 0 && 'function' === typeof repository.saveRelease) {
  for (const release of releases) await repository.saveRelease(release);
}
const grantSecret = process.env.DOWNLOAD_GRANT_SECRET ?? pepper;
const releaseCatalog = new ReleaseCatalog({ items: releases, repository });
const updates = new UpdateService({
  licensing: engine,
  privateKey,
  grantSecret,
  publicOrigin: process.env.PUBLIC_ORIGIN ?? `http://127.0.0.1:${port}`,
  releases: releaseCatalog
});
const packages = new PackageDelivery({ releases: releaseCatalog, root: process.env.PACKAGE_ROOT ?? fileURLToPath(new URL('../../../artifacts', import.meta.url)), grantSecret });
const entitlements = new SignedEntitlements({ privateKey, cacheHours: Number(process.env.ENTITLEMENT_CACHE_HOURS ?? 24) });
const webhook = process.env.COMMERCE_WEBHOOK_SECRET ? new WebhookProcessor({
  repository,
  secret: process.env.COMMERCE_WEBHOOK_SECRET,
  handlers: {
    'order.completed': async (data) => engine.issue({ plan: data.plan, expiresAt: data.expires_at, activationLimit: data.activation_limit, graceDays: data.grace_days, allowFreeStaging: data.allow_free_staging, allowMultisite: data.allow_multisite, entitlements: Array.isArray(data.entitlements) ? data.entitlements : [] }),
    'subscription.suspended': async (data) => engine.setStatus({ licenseId: data.license_id, status: 'suspended' }),
    'subscription.restored': async (data) => engine.setStatus({ licenseId: data.license_id, status: 'active' }),
    'subscription.cancelled': async (data) => engine.setStatus({ licenseId: data.license_id, status: 'cancelled' }),
    'subscription.renewed': async (data) => engine.renew({ licenseId: data.license_id, expiresAt: data.expires_at }),
    'subscription.plan_changed': async (data) => engine.changePlan({ licenseId: data.license_id, plan: data.plan, activationLimit: data.activation_limit, entitlements: data.entitlements })
  }
}) : null;
const rateLimiter = new RateLimiter({ limit: Number(process.env.RATE_LIMIT_PER_MINUTE ?? 60), windowMs: 60000 });
const server = createServer(createHandler({ engine, updates, packages, entitlements, webhook, rateLimiter, releaseCatalog, operatorToken: process.env.OPERATOR_TOKEN ?? '' }));

server.listen(port, '127.0.0.1', () => {
  console.log(`MSP Nexus licensing service listening on http://127.0.0.1:${port}`);
  if (!process.env.DEVELOPMENT_LICENSE_KEY) console.log(`Ephemeral development license: ${developmentKey}`);
  const publicKeyRaw = publicKey.export({ type: 'spki', format: 'der' }).subarray(-32).toString('base64');
  console.log(`${generatedKeys ? 'Ephemeral m' : 'M'}anifest public key (base64): ${publicKeyRaw}`);
});
