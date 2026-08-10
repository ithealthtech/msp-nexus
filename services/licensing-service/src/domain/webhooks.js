import { createHmac, timingSafeEqual } from 'node:crypto';

export class WebhookProcessor {
  constructor({ repository, secret, handlers = {}, clock = () => new Date(), toleranceSeconds = 300 }) {
    this.repository = repository;
    this.secret = secret;
    this.handlers = handlers;
    this.clock = clock;
    this.toleranceSeconds = toleranceSeconds;
  }

  async process({ rawBody, signatureHeader }) {
    if (!this.secret || this.secret.length < 32) throw new Error('webhook_not_configured');
    const signature = this.#verify(rawBody, signatureHeader);
    let event;
    try { event = JSON.parse(rawBody); } catch { throw new Error('invalid_json'); }
    if (!event || typeof event.id !== 'string' || typeof event.type !== 'string' || !event.data || typeof event.data !== 'object') throw new Error('invalid_webhook_event');
    if (await this.repository.hasWebhookEvent(event.id)) return { status: 'duplicate', event_id: event.id };
    const handler = this.handlers[event.type];
    try {
      if (!handler) throw new Error('unsupported_webhook_event');
      const result = await handler(event.data);
      await this.repository.saveWebhookEvent({ id: event.id, type: event.type, signatureTimestamp: signature.timestamp, status: 'processed', receivedAt: this.clock().toISOString() });
      return { status: 'processed', event_id: event.id, result };
    } catch (error) {
      const code = error instanceof Error ? error.message : 'webhook_handler_failed';
      await this.repository.saveWebhookEvent({ id: event.id, type: event.type, signatureTimestamp: signature.timestamp, status: 'failed', receivedAt: this.clock().toISOString(), error: code });
      await this.repository.saveDeadLetter({ eventId: event.id, type: event.type, body: event, error: code, attempts: 1, nextAttemptAt: new Date(this.clock().getTime() + 60000).toISOString(), createdAt: this.clock().toISOString() });
      throw new Error('webhook_handler_failed');
    }
  }

  async retryDue(limit = 25) {
    const records = await this.repository.listDueDeadLetters(this.clock().toISOString(), limit);
    const results = [];
    for (const record of records) {
      const handler = this.handlers[record.type];
      try {
        if (!handler) throw new Error('unsupported_webhook_event');
        await handler(record.body.data);
        await this.repository.saveWebhookEvent({ id: record.eventId, type: record.type, status: 'processed', receivedAt: this.clock().toISOString(), retried: true });
        await this.repository.deleteDeadLetter(record.eventId);
        results.push({ event_id: record.eventId, status: 'processed' });
      } catch (error) {
        const attempts = Number(record.attempts ?? 1) + 1;
        const delay = Math.min(86400000, 60000 * (2 ** Math.min(10, attempts - 1)));
        await this.repository.saveDeadLetter({ ...record, error: error instanceof Error ? error.message : 'webhook_handler_failed', attempts, nextAttemptAt: new Date(this.clock().getTime() + delay).toISOString() });
        results.push({ event_id: record.eventId, status: 'failed', attempts });
      }
    }
    return { attempted: records.length, results };
  }

  #verify(rawBody, header) {
    const parts = Object.fromEntries(String(header).split(',').map((part) => part.trim().split('=', 2)));
    const timestamp = Number.parseInt(parts.t, 10);
    if (!Number.isFinite(timestamp) || !parts.v1) throw new Error('invalid_webhook_signature');
    const skew = Math.abs(Math.floor(this.clock().getTime() / 1000) - timestamp);
    if (skew > this.toleranceSeconds) throw new Error('webhook_timestamp_outside_tolerance');
    const expected = createHmac('sha256', this.secret).update(`${timestamp}.${rawBody}`).digest('hex');
    const provided = Buffer.from(parts.v1);
    const correct = Buffer.from(expected);
    if (provided.length !== correct.length || !timingSafeEqual(provided, correct)) throw new Error('invalid_webhook_signature');
    return { timestamp };
  }
}
