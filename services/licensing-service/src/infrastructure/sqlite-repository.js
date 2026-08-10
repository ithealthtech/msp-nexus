import { DatabaseSync } from 'node:sqlite';
import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { randomUUID } from 'node:crypto';

export class SQLiteRepository {
  constructor(filename) {
    mkdirSync(dirname(filename), { recursive: true });
    this.database = new DatabaseSync(filename);
    this.database.exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON; PRAGMA busy_timeout = 5000;');
    this.migrate();
  }

  migrate() {
    this.database.exec(`
      CREATE TABLE IF NOT EXISTS schema_migrations (
        version INTEGER PRIMARY KEY,
        applied_at TEXT NOT NULL
      );
      CREATE TABLE IF NOT EXISTS licenses (
        id TEXT PRIMARY KEY,
        key_hash TEXT NOT NULL UNIQUE,
        document TEXT NOT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );
      CREATE TABLE IF NOT EXISTS audit_events (
        sequence INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id TEXT NOT NULL UNIQUE,
        action TEXT NOT NULL,
        license_id TEXT,
        document TEXT NOT NULL,
        occurred_at TEXT NOT NULL
      );
      CREATE INDEX IF NOT EXISTS audit_events_license_idx ON audit_events (license_id, occurred_at DESC);
      CREATE TABLE IF NOT EXISTS webhook_events (
        event_id TEXT PRIMARY KEY,
        event_type TEXT NOT NULL,
        status TEXT NOT NULL,
        document TEXT NOT NULL,
        received_at TEXT NOT NULL
      );
      CREATE TABLE IF NOT EXISTS webhook_dead_letters (
        sequence INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id TEXT NOT NULL,
        event_type TEXT NOT NULL,
        attempts INTEGER NOT NULL,
        next_attempt_at TEXT,
        document TEXT NOT NULL,
        created_at TEXT NOT NULL
      );
      CREATE INDEX IF NOT EXISTS webhook_dead_letters_retry_idx ON webhook_dead_letters (next_attempt_at, attempts);
      CREATE TABLE IF NOT EXISTS releases (
        product TEXT NOT NULL,
        channel TEXT NOT NULL,
        version TEXT NOT NULL,
        status TEXT NOT NULL,
        document TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        PRIMARY KEY (product, channel, version)
      );
      CREATE INDEX IF NOT EXISTS releases_lookup_idx ON releases (product, channel, status, version);
      INSERT OR IGNORE INTO schema_migrations (version, applied_at) VALUES (1, datetime('now'));
      INSERT OR IGNORE INTO schema_migrations (version, applied_at) VALUES (2, datetime('now'));
      INSERT OR IGNORE INTO schema_migrations (version, applied_at) VALUES (3, datetime('now'));
    `);
  }

  async getLicense(id) {
    const row = this.database.prepare('SELECT document FROM licenses WHERE id = ?').get(id);
    return row ? JSON.parse(row.document) : null;
  }

  async listLicenses() {
    return this.database.prepare('SELECT document FROM licenses ORDER BY created_at').all().map((row) => JSON.parse(row.document));
  }

  async saveLicense(license) {
    const now = new Date().toISOString();
    this.database.prepare(`
      INSERT INTO licenses (id, key_hash, document, created_at, updated_at)
      VALUES (?, ?, ?, ?, ?)
      ON CONFLICT(id) DO UPDATE SET key_hash = excluded.key_hash, document = excluded.document, updated_at = excluded.updated_at
    `).run(license.id, license.keyHash, JSON.stringify(license), now, now);
  }

  async appendAudit(event) {
    const id = event.id ?? randomUUID();
    const occurredAt = event.at ?? new Date().toISOString();
    this.database.prepare('INSERT INTO audit_events (event_id, action, license_id, document, occurred_at) VALUES (?, ?, ?, ?, ?)')
      .run(id, event.action, event.licenseId ?? null, JSON.stringify({ ...event, id }), occurredAt);
  }

  async listAudit(limit = 100) {
    const safeLimit = Math.min(1000, Math.max(1, Number.parseInt(limit, 10) || 100));
    return this.database.prepare('SELECT document FROM audit_events ORDER BY sequence DESC LIMIT ?').all(safeLimit).map((row) => JSON.parse(row.document));
  }

  async hasWebhookEvent(id) {
    return Boolean(this.database.prepare('SELECT 1 AS found FROM webhook_events WHERE event_id = ?').get(id));
  }

  async saveWebhookEvent(event) {
    this.database.prepare('INSERT OR REPLACE INTO webhook_events (event_id, event_type, status, document, received_at) VALUES (?, ?, ?, ?, ?)')
      .run(event.id, event.type, event.status, JSON.stringify(event), event.receivedAt);
  }

  async saveDeadLetter(record) {
    this.database.prepare('DELETE FROM webhook_dead_letters WHERE event_id = ?').run(record.eventId);
    this.database.prepare('INSERT INTO webhook_dead_letters (event_id, event_type, attempts, next_attempt_at, document, created_at) VALUES (?, ?, ?, ?, ?, ?)')
      .run(record.eventId, record.type, record.attempts, record.nextAttemptAt ?? null, JSON.stringify(record), record.createdAt);
  }

  async listDueDeadLetters(now, limit = 25) {
    const safeLimit = Math.min(100, Math.max(1, Number.parseInt(limit, 10) || 25));
    return this.database.prepare('SELECT document FROM webhook_dead_letters WHERE next_attempt_at IS NULL OR next_attempt_at <= ? ORDER BY sequence LIMIT ?').all(now, safeLimit).map((row) => JSON.parse(row.document));
  }

  async deleteDeadLetter(eventId) {
    this.database.prepare('DELETE FROM webhook_dead_letters WHERE event_id = ?').run(eventId);
  }

  async listReleases() {
    return this.database.prepare('SELECT document FROM releases ORDER BY product, channel, version').all().map((row) => JSON.parse(row.document));
  }

  async saveRelease(release) {
    this.database.prepare(`
      INSERT INTO releases (product, channel, version, status, document, updated_at)
      VALUES (?, ?, ?, ?, ?, ?)
      ON CONFLICT(product, channel, version) DO UPDATE SET status = excluded.status, document = excluded.document, updated_at = excluded.updated_at
    `).run(release.product, release.channel, release.version, release.status, JSON.stringify(release), release.updatedAt ?? new Date().toISOString());
  }
}
