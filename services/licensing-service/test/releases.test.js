import test from 'node:test';
import assert from 'node:assert/strict';
import { ReleaseCatalog, missingSeedReleases } from '../src/domain/releases.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';

const release = { product: 'msp-nexus', product_type: 'theme', slug: 'msp-nexus', channel: 'stable', version: '1.2.3', status: 'published', rollout_percent: 25, requires_wordpress: '6.7', tested_wordpress: '7.0', requires_php: '7.4.33', sha256: 'a'.repeat(64), size: 42, file: 'theme.zip', changelog_url: 'https://example.test/changelog' };

test('operator release catalog supports publish, pause, and withdrawal with audit events', async () => {
  const repository = new MemoryRepository();
  const catalog = new ReleaseCatalog({ items: [], repository, clock: () => new Date('2026-08-08T12:00:00.000Z') });
  const published = await catalog.upsert(release);
  assert.equal(published.rolloutPercent, 25);
  assert.equal(catalog.find('msp-nexus', 'stable').version, '1.2.3');
  await catalog.changeStatus({ product: 'msp-nexus', version: '1.2.3', channel: 'stable', status: 'paused' });
  assert.equal(catalog.find('msp-nexus', 'stable'), null);
  await catalog.changeStatus({ product: 'msp-nexus', version: '1.2.3', channel: 'stable', status: 'withdrawn' });
  assert.equal((await repository.listAudit()).length, 3);
  assert.equal((await repository.listReleases())[0].status, 'withdrawn');
});

test('a service upgrade adds the new seed releases without replacing operator-edited rows', () => {
  const persisted = [
    { product: 'msp-nexus', channel: 'stable', version: '0.5.2', status: 'paused', rolloutPercent: 10 },
    { product: 'msp-nexus-core', channel: 'stable', version: '0.5.2', status: 'published', rolloutPercent: 100 }
  ];
  const pkg = { sha256: 'b'.repeat(64), size: 1024 };
  const seeds = [
    { product: 'msp-nexus', channel: 'stable', version: '0.5.2', status: 'published', rolloutPercent: 100, ...pkg },
    { product: 'msp-nexus', channel: 'stable', version: '0.5.3', status: 'published', rolloutPercent: 100, ...pkg },
    { product: 'msp-nexus-core', channel: 'stable', version: '0.5.3', status: 'published', rolloutPercent: 100, ...pkg }
  ];
  const added = missingSeedReleases(persisted, seeds);
  assert.deepEqual(added.map((item) => `${item.product}@${item.version}`), ['msp-nexus@0.5.3', 'msp-nexus-core@0.5.3']);
  assert.deepEqual(missingSeedReleases([], seeds), seeds);
  const catalog = new ReleaseCatalog({ items: [...persisted, ...added] });
  assert.equal(catalog.find('msp-nexus', 'stable').version, '0.5.3');
});

test('an existing catalog does not gain seeds without real package metadata', () => {
  const persisted = [{ product: 'msp-nexus', channel: 'stable', version: '0.5.2', status: 'published', rolloutPercent: 100, sha256: 'c'.repeat(64), size: 2048 }];
  const placeholder = { product: 'msp-nexus', channel: 'stable', version: '0.5.3', status: 'published', rolloutPercent: 100, sha256: 'development-package-not-published', size: 0 };
  assert.deepEqual(missingSeedReleases(persisted, [placeholder]), []);
  const catalog = new ReleaseCatalog({ items: [...persisted, ...missingSeedReleases(persisted, [placeholder])] });
  assert.equal(catalog.find('msp-nexus', 'stable').version, '0.5.2');
  assert.deepEqual(missingSeedReleases([], [placeholder]), [placeholder]);
});
