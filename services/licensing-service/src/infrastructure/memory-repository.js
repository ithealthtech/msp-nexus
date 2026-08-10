export class MemoryRepository {
  constructor(seed = []) {
    this.licenses = new Map(seed.map((license) => [license.id, structuredClone(license)]));
    this.audit = [];
    this.webhooks = new Map();
    this.deadLetters = [];
    this.releases = new Map();
  }

  async getLicense(id) { return this.licenses.has(id) ? structuredClone(this.licenses.get(id)) : null; }
  async listLicenses() { return [...this.licenses.values()].map((item) => structuredClone(item)); }
  async saveLicense(license) { this.licenses.set(license.id, structuredClone(license)); }
  async appendAudit(event) { this.audit.push(structuredClone(event)); }
  async listAudit() { return structuredClone(this.audit); }
  async hasWebhookEvent(id) { return this.webhooks.has(id); }
  async saveWebhookEvent(event) { this.webhooks.set(event.id, structuredClone(event)); }
  async saveDeadLetter(record) { this.deadLetters = this.deadLetters.filter((item) => item.eventId !== record.eventId); this.deadLetters.push(structuredClone(record)); }
  async listDueDeadLetters(now, limit = 25) { return this.deadLetters.filter((item) => !item.nextAttemptAt || item.nextAttemptAt <= now).slice(0, limit).map((item) => structuredClone(item)); }
  async deleteDeadLetter(eventId) { this.deadLetters = this.deadLetters.filter((item) => item.eventId !== eventId); }
  async listReleases() { return [...this.releases.values()].map((item) => structuredClone(item)); }
  async saveRelease(release) { this.releases.set(`${release.product}:${release.channel}:${release.version}`, structuredClone(release)); }
}
