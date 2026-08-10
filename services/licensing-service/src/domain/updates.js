import { createHash } from 'node:crypto';
import { createDownloadGrant, signManifest } from './signing.js';

const entitlementFor = Object.freeze({
  'msp-nexus': 'theme_updates',
  'msp-nexus-core': 'plugin_updates'
});

export class UpdateService {
  constructor({ licensing, releases, privateKey, grantSecret, publicOrigin, clock = () => new Date() }) {
    this.licensing = licensing;
    this.releases = releases;
    this.privateKey = privateKey;
    this.grantSecret = grantSecret;
    this.publicOrigin = publicOrigin.replace(/\/$/, '');
    this.clock = clock;
  }

  async check({ licenseId, siteUrl, instanceId, product, channel = 'stable' }) {
    const entitlement = await this.licensing.validate({ licenseId, siteUrl, instanceId });
    const required = entitlementFor[product];
    if (!required || !entitlement.entitlements.includes(required)) throw new Error('product_not_entitled');
    const release = typeof this.releases.find === 'function' && !Array.isArray(this.releases)
      ? this.releases.find(product, channel)
      : this.releases.find((item) => item.product === product && item.channel === channel && (!item.status || item.status === 'published'));
    if (!release) throw new Error('release_not_found');
    const rollout = Number(release.rolloutPercent ?? 100);
    const bucket = Number.parseInt(createHash('sha256').update(`${licenseId}:${product}:${release.version}`).digest('hex').slice(0, 8), 16) / 0xffffffff * 100;
    if (bucket >= rollout) throw new Error('release_not_found');

    const expiresAt = this.clock().getTime() + 5 * 60 * 1000;
    const grant = createDownloadGrant({ product, version: release.version, license_id: licenseId, site: entitlement.site_url, expires_at: expiresAt }, this.grantSecret);
    const rollbackRelease = release.rollbackVersion
      ? (typeof this.releases.findVersion === 'function' ? this.releases.findVersion(product, release.rollbackVersion) : this.releases.find((item) => item.product === product && item.version === release.rollbackVersion))
      : null;
    const rollbackGrant = rollbackRelease ? createDownloadGrant({ product, version: rollbackRelease.version, license_id: licenseId, site: entitlement.site_url, expires_at: expiresAt }, this.grantSecret) : null;
    const manifest = {
      schema: 1,
      product,
      product_type: release.productType,
      slug: release.slug,
      version: release.version,
      channel: release.channel,
      requires_wordpress: release.requiresWordPress,
      tested_wordpress: release.testedWordPress,
      maximum_wordpress: release.maximumWordPress ?? null,
      requires_php: release.requiresPhp,
      requires_database: release.requiresDatabase ?? null,
      package_sha256: release.sha256,
      package_size: release.size,
      package_url: `${this.publicOrigin}/v1/packages/${encodeURIComponent(product)}/${encodeURIComponent(release.version)}?grant=${encodeURIComponent(grant)}`,
      changelog_url: release.changelogUrl,
      release_notes: release.releaseNotes ?? '',
      migration_version: release.migrationVersion ?? null,
      rollback_version: release.rollbackVersion ?? null,
      rollback_package_url: rollbackRelease ? `${this.publicOrigin}/v1/packages/${encodeURIComponent(product)}/${encodeURIComponent(rollbackRelease.version)}?grant=${encodeURIComponent(rollbackGrant)}` : null,
      rollback_package_sha256: rollbackRelease?.sha256 ?? null,
      issued_at: this.clock().toISOString(),
      expires_at: new Date(expiresAt).toISOString()
    };
    return { manifest, signature: signManifest(manifest, this.privateKey), algorithm: 'Ed25519' };
  }
}
