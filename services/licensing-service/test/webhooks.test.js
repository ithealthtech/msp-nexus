import test from 'node:test';
import assert from 'node:assert/strict';
import { createHmac } from 'node:crypto';
import { WebhookProcessor } from '../src/domain/webhooks.js';
import { MemoryRepository } from '../src/infrastructure/memory-repository.js';

const secret = '12345678901234567890123456789012';
const now = new Date('2026-08-08T12:00:00.000Z');
const timestamp = Math.floor(now.getTime() / 1000);
function signature(body, at = timestamp) { return `t=${at},v1=${createHmac('sha256', secret).update(`${at}.${body}`).digest('hex')}`; }

test('commerce webhook verifies signature and rejects replay side effects', async () => {
  const repository = new MemoryRepository();
  let calls = 0;
  const processor = new WebhookProcessor({ repository, secret, clock: () => new Date(now), handlers: { 'order.completed': async () => ({ calls: ++calls }) } });
  const body = JSON.stringify({ id: 'event-1', type: 'order.completed', data: { order_id: 'order-1' } });
  assert.equal((await processor.process({ rawBody: body, signatureHeader: signature(body) })).status, 'processed');
  assert.equal((await processor.process({ rawBody: body, signatureHeader: signature(body) })).status, 'duplicate');
  assert.equal(calls, 1);
});

test('commerce webhook rejects bad signatures and clock skew', async () => {
  const processor = new WebhookProcessor({ repository: new MemoryRepository(), secret, clock: () => new Date(now), handlers: {} });
  const body = JSON.stringify({ id: 'event-2', type: 'unknown', data: {} });
  await assert.rejects(() => processor.process({ rawBody: body, signatureHeader: 't=1,v1=bad' }), /webhook_timestamp_outside_tolerance/);
  await assert.rejects(() => processor.process({ rawBody: body, signatureHeader: `t=${timestamp},v1=${'a'.repeat(64)}` }), /invalid_webhook_signature/);
});

test('failed webhook is persisted to the dead-letter queue', async () => {
  const repository = new MemoryRepository();
  const processor = new WebhookProcessor({ repository, secret, clock: () => new Date(now), handlers: { 'order.completed': async () => { throw new Error('commerce_unavailable'); } } });
  const body = JSON.stringify({ id: 'event-3', type: 'order.completed', data: {} });
  await assert.rejects(() => processor.process({ rawBody: body, signatureHeader: signature(body) }), /webhook_handler_failed/);
  assert.equal(repository.deadLetters.length, 1);
  assert.equal(repository.deadLetters[0].attempts, 1);
});

test('operator retry processes a due dead letter and clears it', async () => {
  const repository = new MemoryRepository();
  let available = false;
  let clockTime = new Date('2026-08-08T12:02:00.000Z');
  const clock = () => new Date(clockTime);
  const processor = new WebhookProcessor({ repository, secret, clock, handlers: { 'order.completed': async () => { if (!available) throw new Error('commerce_unavailable'); return { ok: true }; } } });
  const event = { id: 'evt_retry', type: 'order.completed', data: { order_id: 'o-2' } };
  const rawBody = JSON.stringify(event);
  await assert.rejects(() => processor.process({ rawBody, signatureHeader: signature(rawBody, Math.floor(clock().getTime() / 1000)) }), /webhook_handler_failed/);
  available = true;
  clockTime = new Date('2026-08-08T12:04:00.000Z');
  const result = await processor.retryDue();
  assert.equal(result.results[0].status, 'processed');
  assert.equal(repository.deadLetters.length, 0);
});
