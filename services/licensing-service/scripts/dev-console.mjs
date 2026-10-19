// Local console preview: in-memory storage and a throwaway operator key printed to the log.
// Never use this for production; see DEPLOYMENT.md.
import { randomBytes } from 'node:crypto';

process.env.USE_MEMORY_STORAGE ??= '1';
process.env.PORT ??= '8787';
process.env.LICENSE_KEY_PEPPER ??= randomBytes(32).toString('hex');
if (!process.env.OPERATOR_TOKEN) {
  process.env.OPERATOR_TOKEN = randomBytes(24).toString('base64url');
  console.log(`Development operator access key: ${process.env.OPERATOR_TOKEN}`);
}
await import('../src/server.js');
