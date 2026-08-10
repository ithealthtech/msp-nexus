import { readFile, readdir } from 'node:fs/promises';
import path from 'node:path';

const roots = ['package.json', 'packages', 'services', 'demo'];
const files = [];

async function walk(target) {
  let entries;
  try {
    entries = await readdir(target, { withFileTypes: true });
  } catch {
    if (target.endsWith('.json')) files.push(target);
    return;
  }
  for (const entry of entries) {
    if (['node_modules', 'vendor', 'dist', 'build'].includes(entry.name)) continue;
    const child = path.join(target, entry.name);
    if (entry.isDirectory()) await walk(child);
    else if (entry.name.endsWith('.json')) files.push(child);
  }
}

for (const root of roots) await walk(root);

let failed = false;
for (const file of files) {
  try {
    JSON.parse(await readFile(file, 'utf8'));
  } catch (error) {
    failed = true;
    console.error(`${file}: ${error.message}`);
  }
}

console.log(`Validated ${files.length} JSON files.`);
if (failed) process.exitCode = 1;
