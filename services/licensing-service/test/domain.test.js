import test from 'node:test';
import assert from 'node:assert/strict';
import { isStagingSite, normalizeSite } from '../src/domain/domain.js';

test('normalizes origin and strips paths', () => assert.equal(normalizeSite('https://EXAMPLE.com:443/a?b=1'), 'https://example.com'));
test('recognizes staging without treating arbitrary substrings as staging', () => {
  assert.equal(isStagingSite('https://staging.example.com'), true);
  assert.equal(isStagingSite('https://dev.example.com'), true);
  assert.equal(isStagingSite('https://device.example.com'), false);
});
