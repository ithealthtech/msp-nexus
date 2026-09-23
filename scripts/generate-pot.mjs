import { mkdir, readFile, readdir, writeFile } from 'node:fs/promises';
import path from 'node:path';

async function walk(root) {
  const files = [];
  for (const entry of await readdir(root, { withFileTypes: true })) {
    if (['node_modules', 'vendor', 'languages'].includes(entry.name)) continue;
    const child = path.join(root, entry.name);
    if (entry.isDirectory()) files.push(...await walk(child));
    else if (/\.(php|js)$/.test(entry.name)) files.push(child);
  }
  return files;
}

function quote(value) { return `"${value.replaceAll('\\', '\\\\').replaceAll('"', '\\"').replaceAll('\n', '\\n')}"`; }

async function generate(root, domain, output) {
  const entries = new Map();
  const expression = /(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*(['"])([^'"]+)\3/g;
  for (const file of await walk(root)) {
    const source = await readFile(file, 'utf8');
    for (const match of source.matchAll(expression)) {
      if (match[4] !== domain) continue;
      const message = match[2].replace(/\\(['"\\])/g, '$1');
      if (!entries.has(message)) entries.set(message, []);
      entries.get(message).push(file.split(path.sep).join('/'));
    }
  }
  const lines = ['msgid ""', 'msgstr ""', '"Project-Id-Version: MSP Nexus 0.5.0\\n"', '"MIME-Version: 1.0\\n"', '"Content-Type: text/plain; charset=UTF-8\\n"', '"Content-Transfer-Encoding: 8bit\\n"', ''];
  for (const [message, references] of [...entries].sort(([a], [b]) => a.localeCompare(b))) {
    lines.push(`#: ${[...new Set(references)].join(' ')}`, `msgid ${quote(message)}`, 'msgstr ""', '');
  }
  await mkdir(path.dirname(output), { recursive: true });
  await writeFile(output, `${lines.join('\n')}\n`);
  console.log(`${output}: ${entries.size} messages`);
}

await generate('packages/msp-nexus', 'msp-nexus', 'packages/msp-nexus/languages/msp-nexus.pot');
await generate('packages/msp-nexus-core', 'msp-nexus-core', 'packages/msp-nexus-core/languages/msp-nexus-core.pot');
