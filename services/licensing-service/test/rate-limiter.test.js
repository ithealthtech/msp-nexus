import test from 'node:test';
import assert from 'node:assert/strict';
import { RateLimiter } from '../src/infrastructure/rate-limiter.js';

test('rate limiter isolates keys and resets after its bounded window', () => {
  let now = 1000;
  const limiter = new RateLimiter({ limit: 2, windowMs: 1000, clock: () => now });
  limiter.consume('client-a');
  limiter.consume('client-a');
  assert.throws(() => limiter.consume('client-a'), /rate_limited/);
  assert.doesNotThrow(() => limiter.consume('client-b'));
  now = 2001;
  assert.doesNotThrow(() => limiter.consume('client-a'));
});
