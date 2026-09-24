const releaseKey = (release) => `${release.product}:${release.channel}:${release.version}`;

/**
 * Seed releases a persisted catalog does not have yet. Existing rows are never replaced, so operator
 * changes (pauses, withdrawals, rollout percentages) survive a service upgrade.
 */
export function missingSeedReleases(persisted, seeds) {
  const known = new Set(persisted.map(releaseKey));
  return seeds.filter((release) => !known.has(releaseKey(release)));
}

export class ReleaseCatalog {
  constructor({ items = [], repository, clock = () => new Date() }) {
    this.items = items;
    this.repository = repository;
    this.clock = clock;
  }

  find(product, channel) {
    const candidates = this.items.filter((item) => item.product === product && item.channel === channel && item.status === 'published');
    return candidates.sort((a, b) => b.version.localeCompare(a.version, undefined, { numeric: true }))[0] ?? null;
  }

  list() {
    return this.items.map((item) => ({ ...item }));
  }

  findVersion(product, version) {
    return this.items.find((item) => item.product === product && item.version === version && item.status !== 'withdrawn') ?? null;
  }

  async upsert(input, actor = 'operator') {
    const channel = String(input.channel ?? 'stable');
    if (!['stable', 'beta', 'development'].includes(channel)) throw new Error('invalid_release');
    if (!/^[a-z0-9-]+$/.test(String(input.product)) || !/^\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/.test(String(input.version))) throw new Error('invalid_release');
    if (!/^[a-f0-9]{64}$/i.test(String(input.sha256)) || !Number.isFinite(Number(input.size)) || Number(input.size) < 1) throw new Error('invalid_release');
    const item = {
      product: String(input.product), productType: String(input.product_type), slug: String(input.slug), channel,
      version: String(input.version), status: ['draft', 'published', 'paused', 'withdrawn'].includes(input.status) ? input.status : 'draft',
      rolloutPercent: Math.min(100, Math.max(0, Number(input.rollout_percent ?? 100))),
      requiresWordPress: String(input.requires_wordpress), testedWordPress: String(input.tested_wordpress), maximumWordPress: input.maximum_wordpress ? String(input.maximum_wordpress) : null,
      requiresPhp: String(input.requires_php), requiresDatabase: input.requires_database ? String(input.requires_database) : null,
      sha256: String(input.sha256).toLowerCase(), size: Number(input.size), file: String(input.file), changelogUrl: String(input.changelog_url),
      releaseNotes: String(input.release_notes ?? ''), migrationVersion: input.migration_version ? String(input.migration_version) : null,
      rollbackVersion: input.rollback_version ? String(input.rollback_version) : null, updatedAt: this.clock().toISOString()
    };
    const index = this.items.findIndex((candidate) => candidate.product === item.product && candidate.channel === item.channel && candidate.version === item.version);
    if (index >= 0) this.items[index] = item; else this.items.push(item);
    if (this.repository) {
      await this.repository.saveRelease(item);
      await this.repository.appendAudit({ action: 'release.upserted', product: item.product, version: item.version, channel: item.channel, status: item.status, actor, at: this.clock().toISOString() });
    }
    return { ...item };
  }

  async changeStatus({ product, version, channel, status, actor = 'operator' }) {
    if (!['draft', 'published', 'paused', 'withdrawn'].includes(status)) throw new Error('invalid_release_status');
    const item = this.items.find((candidate) => candidate.product === product && candidate.version === version && candidate.channel === channel);
    if (!item) throw new Error('release_not_found');
    item.status = status;
    item.updatedAt = this.clock().toISOString();
    if (this.repository) {
      await this.repository.saveRelease(item);
      await this.repository.appendAudit({ action: 'release.status_changed', product, version, channel, status, actor, at: this.clock().toISOString() });
    }
    return { ...item };
  }
}
