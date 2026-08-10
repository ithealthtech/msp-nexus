import { readFile, readdir } from 'node:fs/promises';
import path from 'node:path';
import PhpParser from 'php-parser';

const engine = new PhpParser.Engine({
  parser: { version: '7.4', suppressErrors: false },
  ast: { withPositions: true }
});
const unavailableFunctions = new Set([
  'array_is_list',
  'fdiv',
  'get_debug_type',
  'get_resource_id',
  'preg_last_error_msg',
  'str_contains',
  'str_ends_with',
  'str_starts_with'
]);
const files = [];

function inspect(node) {
  if (!node || typeof node !== 'object') return;
  if (node.kind === 'call' && node.what?.kind === 'name' && unavailableFunctions.has(node.what.name.toLowerCase())) {
    throw new Error(`${node.what.name}() is unavailable on PHP 7.4.33`);
  }
  for (const value of Object.values(node)) inspect(value);
}

async function walk(target) {
  const entries = await readdir(target, { withFileTypes: true });
  for (const entry of entries) {
    if (['node_modules', 'vendor', 'dist', 'build'].includes(entry.name)) continue;
    const child = path.join(target, entry.name);
    if (entry.isDirectory()) await walk(child);
    else if (entry.name.endsWith('.php')) files.push(child);
  }
}

await walk('packages');
let failed = false;
for (const file of files) {
  try {
    inspect(engine.parseCode(await readFile(file, 'utf8'), file));
  } catch (error) {
    failed = true;
    console.error(`${file}: ${error.message}`);
  }
}
console.log(`Parsed ${files.length} PHP files against PHP 7.4 syntax and API policy.`);
if (failed) process.exitCode = 1;
