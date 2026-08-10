import { domainToASCII } from 'node:url';

const stagingLabels = new Set(['staging', 'stage', 'dev', 'development', 'test', 'testing', 'local']);

export function normalizeSite(input) {
  const url = new URL(input);
  if (!['http:', 'https:'].includes(url.protocol)) throw new Error('unsupported_protocol');
  const hostname = domainToASCII(url.hostname.toLowerCase().replace(/\.$/, ''));
  if (!hostname) throw new Error('invalid_hostname');
  const defaultPort = (url.protocol === 'https:' && url.port === '443') || (url.protocol === 'http:' && url.port === '80');
  return `${url.protocol}//${hostname}${url.port && !defaultPort ? `:${url.port}` : ''}`;
}

export function isStagingSite(input) {
  const { hostname } = new URL(normalizeSite(input));
  if (hostname === 'localhost' || hostname.endsWith('.local') || hostname.endsWith('.test')) return true;
  return hostname.split('.').some((label) => stagingLabels.has(label));
}
