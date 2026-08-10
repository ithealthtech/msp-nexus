import { readdir } from 'node:fs/promises';
import { spawnSync } from 'node:child_process';
import path from 'node:path';

const roots = ['scripts', 'packages', 'services'];
const files = [];
async function walk(target) {
  let entries;
  try { entries = await readdir(target, { withFileTypes: true }); } catch { return; }
  for (const entry of entries) {
    if (['node_modules', 'vendor', 'artifacts', 'dist', 'build', 'coverage'].includes(entry.name)) continue;
    const child = path.join(target, entry.name);
    if (entry.isDirectory()) await walk(child);
    else if (entry.name.endsWith('.js') || entry.name.endsWith('.mjs')) files.push(child);
  }
}
for (const root of roots) await walk(root);
let failed = false;
for (const file of files) {
  const result = spawnSync(process.execPath, ['--check', file], { encoding: 'utf8' });
  if (result.status !== 0) {
    failed = true;
    console.error(result.stderr || result.stdout);
  }
}
console.log(`Syntax-checked ${files.length} JavaScript files.`);
if (failed) process.exitCode = 1;
