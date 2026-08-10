import test from 'node:test';
import assert from 'node:assert/strict';
import { ReleaseCatalog } from '../src/domain/releases.js';
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
